<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Providers\AppServiceProvider;
use App\Customer;
use App\Subscription;
Use Braintree;
use Auth;
use Session;
use Redirect;
use Gate;
class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $count = Customer::count();
        return "There are"." ".$count." "."users.";
    }
    public function all()
    {
        return Customer::all();
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        /** check if user is already a customer */
        if (Gate::allows('is-customer', Auth::user())) {
            return view('customer.show', Auth::user()->customer);
        }
        return view('customer.create');
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        /** check if user is already a customer */
        if (Gate::allows('is-customer', Auth::user())) {
            return view('customer.show', Auth::user()->customer);
        }
        /** create gateway */
        $gateway = app()->make('Gateway');
        /** create braintree customer */
        $braintreeCustomer = $gateway->customer()->create([
            'firstName' => $request->get('firstName'),
            'lastName' => $request->get('lastName'),
            'email' => Auth::user()->email,
            'paymentMethodNonce' => $request->get('paymentMethodNonce'),
        ]);
        /** create our customer */
        $myCustomer = Customer::create([
            "firstName" => $braintreeCustomer->customer->firstName,
            "lastName" => $braintreeCustomer->customer->lastName,
            "braintree_id" => $braintreeCustomer->customer->id,
            "user_id"=>Auth::user()->id,
        ]);

        if ($braintreeCustomer->success) {
            Session::flash('message', 'Customer created successfully, check your dashboard for more info!'); 
            return Redirect::to('home');
        } else{
            return "Something went wrong.";
        }
        return "Something went wrong.";
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show()
    {
        /** create gateway */
        $gateway = app()->make('Gateway');
        /** check if user is a customer */
        $myCustomer = Customer::where(['user_id'=>Auth::user()->id]);
        if ( !$myCustomer->exists() ){ 
            return Redirect::to('customer/create');
        }
        /** get our customer */
        $myCustomer = $myCustomer->first();
        /** get braintree customer */
        $braintreeCustomer = $gateway->customer()->find($myCustomer->braintree_id);
        /** get active subscription */
        $activeSubscription = Subscription::where(['customer_id' => $myCustomer->id, 'status' => 'Active'])->first();
        $pendingSubscription = Subscription::where(['customer_id' => $myCustomer->id, 'status' => 'Pending'])->first();
     
        $data['myCustomer'] = $myCustomer;
        $data['braintreeCustomer'] = $braintreeCustomer;
        $data['activeSubscription'] = $activeSubscription;
        $data['pendingSubscription'] = $pendingSubscription;

        return $data;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit()
    {
        $myCustomer = Customer::where(['user_id'=>Auth::user()->id]);
        /** check if user is not a customer */
        if ( !$myCustomer->exists() ) {
            return view('customer.create');
        }
        $data = $myCustomer->first();

        return $data;
        
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        /** create gateway */
        $gateway = app()->make('Gateway');
        $myCustomer = Customer::where(['user_id'=>Auth::user()->id]);
        /** check if user is not a customer */
        if ( !$myCustomer->exists() ) {
            return view('customer.create');
        }
        /** get our customer */
        $myCustomer = $myCustomer->first();
        /** get braintree customer */
        $braintreeCustomer = $gateway->customer()->find($myCustomer->braintree_id);
        /** update braintree customer */
        $updatedBraintreeCustomer = $gateway->customer()->update(
            $braintreeCustomer->id,
            [
             'firstName' => $request->get('firstName'),
             'lastName' => $request->get('lastName'),
             'email' => Auth::user()->email,
             'paymentMethodNonce' => $request->get('paymentMethodNonce'),
            ]
        );
        /** update our customer */
        $myCustomer->update([
            "firstName" => $updatedBraintreeCustomer->customer->firstName,
            "lastName" => $updatedBraintreeCustomer->customer->lastName,
            "braintree_id" => $updatedBraintreeCustomer->customer->id,
            "user_id"=>Auth::user()->id,
        ]);
 
        if ($updatedBraintreeCustomer->success) {
            return True;
        } else{
            return False;
        }
        return False;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy()
    {
        /** create gateway */
        $gateway = app()->make('Gateway');
        $myCustomer = Customer::where(['user_id'=>Auth::user()->id]);
        /** check if user is not a customer */
        if ( !$myCustomer->exists() ) {
            return view('customer.create');
        }
        /** get our customer */
        $myCustomer = $myCustomer->first();
        /** delete braintree customer */
        $result = $gateway->customer()->delete($myCustomer->braintree_id);
        /** delete our customer */
        $myCustomer->delete();
        if ($result->success) {
            return True;
        } else{
            return False;
        }
        return False;
    }
}
