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

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        /** check if user is a customer */
        $customer = Customer::where(['user_id' => Auth::user()->id])->exists();
        if ($customer) {
            /** check if user is already subscribed */
            $subscription = Subscription::where(['customer_id' => $customer['id']])->exists();
            if ($subscription) {
                Session::flash('message', 'You are already subbcribed.'); 
                return Redirect::to('home');
            }else{
                return view('/subscription/create');
            }
        }else{
            return Redirect::to('customer/create');
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        /** create gateway */
        $gateway = app()->make('Gateway');
        /** check if user is a customer */
        $mycustomer = Customer::where(['user_id' => Auth::user()->id]);
        if( !$myCustomer->exists() ){
            return Redirect::view('customer.create');
        }
        /** get our customer */
        $mycustomer = $myCustomer->first();
        /** get braintree customer */
        $braintreeCustomer = $gateway->customer()->find($myCustomer['braintree_id']);
        /** create subscription */
        $res = $gateway->subscription()->create([
            'paymentMethodToken' => $braintreeCustomer->paymentMethods[0]->token,
            'planId' => $request->get('planId')
        ]);
        /** return */
        if ($res->success) {
            $braintreeSubscription = $res->subscription;
            Subscription::create([
                'paymentMethodToken' => $braintreeSubscription->paymentMethodToken,
                'planId' => $braintreeSubscription->planId,
                'braintree_id' => $braintreeSubscription->id,
                'customer_id' => $myCustomer->id,
            ]);
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
        /** check if subscription exists */
        $mySubscription = Subscription::find($id);
        if( !$mySubscription ){
            return view('errors.404');
        }
        /** check if user is a customer */
        $myCustomer = Customer::where(['user_id' => Auth::user()->id]);
        if( !$myCustomer->exists() ){
            return Redirect::view('customer.create');
        }
        /** get our customer */
        $myCustomer = $myCustomer->first();
        /** create gateway */
        $gateway = app()->make('Gateway');
        /** check if subscription belongs to logged in customer */
        if ( !$mySubscription->customer_id == $myCustomer->id ){
            /** this is returning not found if the logged in customer isn't the subscription owner,
             *  i'm not sure if this would make a security concern but better be safe than sorry. */
            return view('errors.404');
        }
        /** get braintree subscription */
        $braintreeSubscription = $gateway->subscription()->find($mySubscription['braintree_id']);
        /** this is the date format i chose, you can chose what ever format you want,
         *  but you have to format the date or it'll error out.
         */
        $format = 'd/m/Y'; 

        $data['Subscription'] = $braintreeSubscription;
        /** formating the dates  */
        $data['Subscription']->createdAt = $braintreeSubscription->createdAt->format($format);
        $data['Subscription']->updatedAt = $braintreeSubscription->updatedAt->format($format);
        $data['Subscription']->firstBillingDate = $braintreeSubscription->firstBillingDate->format($format);
        $data['Subscription']->nextBillingDate = $braintreeSubscription->nextBillingDate->format($format);
        
        return view('subscription.show', $data);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Subscription  $subscription
     * @return \Illuminate\Http\Response
     */
    public function edit(Subscription $subscription)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Subscription  $subscription
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Subscription $subscription)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Subscription  $subscription
     * @return \Illuminate\Http\Response
     */
    public function cancel($id)
    {
        /** create gateway */
        $gateway = app()->make('Gateway');
        /** cancel subscription */
        $result = $gateway->subscription()->cancel($id);

        if ( $result->success ){
            Session::flash('message', 'Your subscription has been cancled, sad to see you go :(.'); 
            return Redirect::to('home');
        }else{
            return "Something went wrong...";
        }
    }
}
