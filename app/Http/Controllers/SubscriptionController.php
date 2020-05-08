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
        //
        $customer = Customer::where(['user_id' => Auth::user()->id])->exists();
        if ($customer) {
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
        $gateway = app()->make('Gateway');
        $myCustomer = Customer::where([ 'user_id' => Auth::user()->id])->first();
        $braintreeCustomer = $gateway->customer()->find($myCustomer['braintree_id']);
        $res = $gateway->subscription()->create([
            'paymentMethodToken' => $braintreeCustomer->paymentMethods[0]->token,
            'planId' => $request->get('planId')
        ]);
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
        
        $mySubscription = Subscription::find($id);
        $gateway = app()->make('Gateway');
        $braintreeSubscription = $gateway->subscription()->find($mySubscription['braintree_id']);
        $format = 'd/m/Y';


        $data['Subscription'] = $braintreeSubscription;
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
    public function destroy(Subscription $subscription)
    {
        //
    }
}
