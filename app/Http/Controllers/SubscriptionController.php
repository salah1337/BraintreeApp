<?php

namespace App\Http\Controllers;

use App\Subscription;
use Illuminate\Http\Request;
use App\Customer;
use Auth;
use Session;
use Braintree;
use Redirect;
use DateTime;
use Gate;
class SubscriptionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        /** check if user is a customer */
        $customer = Customer::where(['user_id' => Auth::user()->id])->exists();
        if ($customer) {
            $subscriptions = Subscription::where(['customer_id' => $customer['id']])->exists();
            if (!$subscriptions) {
                return Redirect::to('subscription/create');
            }else{
                return view('/subscription');
            }
        }else{
            return view('/customer/create');
        }
    }
    public function all(){
        return Auth::user()->customer->subscriptions;
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $user = Auth::user();
        /** check if user is customer */
        if (Gate::denies('is-customer', $user)) {
            return Redirect::to('customer/create');
        }
        /** check if user already has an active subscription */
        if (Gate::allows('is-subbed', $user)) {
            Session::flash('message', 'You are already subbcribed.'); 
            return Redirect::to('home');
        }
        return view('/subscription/create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store($planId)
    {
        $user = Auth::user();
        /** check if user is a customer */
        $this->authorize('is-customer', $user);
        /** check if user has active subscription */
        if (Gate::allows('is-subbed', $user)) {
            return 403;
        }
        /** get customer */
        $myCustomer = $user->customer;
        /** create gateway */
        $gateway = app()->make('Gateway');
        /** get braintree customer */
        $braintreeCustomer = $gateway->customer()->find($myCustomer->braintree_id);
        /** create subscription */
        $res = $gateway->subscription()->create([
            'paymentMethodToken' => $braintreeCustomer->paymentMethods[0]->token,
            'planId' => $planId
        ]);
        /** check if if was successful */
        if ($res->success) {
            $braintreeSubscription = $res->subscription;
            /** create subscription to save on our own database */
            Subscription::create([
                'paymentMethodToken' => $braintreeSubscription->paymentMethodToken,
                'planId' => $braintreeSubscription->planId,
                'braintree_id' => $braintreeSubscription->id,
                'status' => $braintreeSubscription->status,
                'customer_id' => $myCustomer->id,
            ]);
            /** return */
            Session::flash('message', 'Subscription created Successfully, check your dashboard for more info.'); 
            return Redirect::to('home');
        }else{
            return "Something went wrong.";
        }
        return "Something went wrong.";
    }
    /**
     * Display the specified resource.
     *
     * @param  \App\Subscription  $subscription
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = Auth::user();
        /** check if user is a customer */
        $this->authorize('is-customer', $user);
        /** check if subscription exists */
        $mySubscription = Subscription::find($id);
        if( !$mySubscription ){
            return 404;
        }
        /** check if subscription belongs to logged in user */
        $this->authorize('edit-subscription', $user, $mySubscription);
        /** get our customer */
        $myCustomer = $user->customer;
        /** create gateway */
        $gateway = app()->make('Gateway');
        /** get braintree subscription */
        $braintreeSubscription = $gateway->subscription()->find($mySubscription['braintree_id']);
        /** chose date format */
        $format = 'd/m/Y'; 
        /**  */
        $data['Subscription'] = $braintreeSubscription;
        /** formating the dates  */
        $data['Subscription']->createdAt = $braintreeSubscription->createdAt->format($format);
        $data['Subscription']->updatedAt = $braintreeSubscription->updatedAt->format($format);
        $data['Subscription']->firstBillingDate = $braintreeSubscription->firstBillingDate->format($format);
        $data['Subscription']->nextBillingDate = $braintreeSubscription->nextBillingDate->format($format);
        $data['mySubscription'] = $mySubscription;
        /**  */
        return view('subscription.show', $data);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Subscription  $subscription
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $user = Auth::user();
        /** check if user is a customer */
        $this->authorize('is-customer', $user);
        /** check if subscription exists */
        $subscription = Subscription::find($id);
        if( !$subscription ){
            return 404;
        }
        /** check if subscription belongs to user */
        $this->authorize('edit-subscription', Auth::user(), $subscription);
        
        return view('subscription.edit', $subscription);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Subscription  $subscription
     * @return \Illuminate\Http\Response
     */
    public function switch($id, $planId)
    {
        /** get user & subscription */
        $mySubscription = Subscription::find($id);
        $user = Auth::user();
        /** check if user is allowed to edit */
        $this->authorize('edit-subscription', $user, $mySubscription);
        /** make new subscription */
        $gateway = app()->make('Gateway');
        /** get old subscription from braintree */
        $oldSubscription = $gateway->subscription()->find($mySubscription->braintree_id);
        /** get customer */
        $myCustomer = $user->customer;
        $braintreeCustomer = $gateway->customer()->find($myCustomer->braintree_id);
        /** make subscription that starts when old one ends */
        $res = $gateway->subscription()->create([
            'paymentMethodToken' => $braintreeCustomer->paymentMethods[0]->token,
            'planId' => $planId,
            'firstBillingDate' => $oldSubscription->nextBillingDate
        ]);
        $newSubscription = $res->subscription;
        /** make last one end this month */
        $gateway->subscription()->update($oldSubscription->id, [
            'numberOfBillingCycles' => 1,
        ]);
        /** save new subscription details in our db */
        Subscription::create([
            'paymentMethodToken' => $newSubscription->paymentMethodToken,
            'planId' => $newSubscription->planId,
            'braintree_id' => $newSubscription->id,
            'status' => $newSubscription->status,
            'customer_id' => $myCustomer->id,
        ]);
        /** victory! */
        Session::flash('message', 'Donezo'); 
        return Redirect::to('home');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Subscription  $subscription
     * @return \Illuminate\Http\Response
     */
    public function cancel($id)
    {
        $user = Auth::user();
        /** check if user is a customer */
        $this->authorize('is-customer', $user);
        /** check if subscription exists */
        $mySubscription = Subscription::find($id);
        if( !$mySubscription ){
            return view('errors.404');
        }
        /** check if subscription belongs to logged in user */
        $this->authorize('edit-subscription', $user, $mySubscription);
        /** get our customer */
        $myCustomer = $user->customer;
        /** create gateway */
        $gateway = app()->make('Gateway');
        /** cancel subscription */
        $result = $gateway->subscription()->cancel($mySubscription->braintree_id);

        Subscription::where(['id' => $id])->update([
            'status' => $result->subscription->status
        ]);
        if ( $result->success ){
            Session::flash('message', 'Your subscription has been cancled, sad to see you go :(.'); 
            return Redirect::to('home');
        }else{
            return "Something went wrong...";
        }
        return "Something went wrong...";
    }
}
