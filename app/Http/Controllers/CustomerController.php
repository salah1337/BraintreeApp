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
        /** check if user is a customer */
        if (Gate::denies('is-customer', Auth::user())) {
            return view('customer.create');
        }
        /** create gateway */
        $gateway = app()->make('Gateway');
        /** get our customer */
        $data['myCustomer'] = Auth::user()->customer;
        /** get braintree customer */
        $data['braintreeCustomer'] = $gateway->customer()->find($data['myCustomer']->braintree_id);
        /** get subscriptions */
        $data['activeSubscription'] = $data['myCustomer']->subscriptions->where('status', 'Active')->first();
        $data['pendingSubscription'] = $data['myCustomer']->subscriptions->where('status', 'Pending')->first();
        /** return */
        return view('customer.show', $data);
    }
    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit()
    {
        /** check if user is a customer */
        if (Gate::denies('is-customer', Auth::user())) {
            return view('customer.create');
        }
        $data = Auth::user()->customer;

        return view('customer.edit', $data);
        
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
        if (Gate::denies('is-customer', Auth::user())) {
            return view('customer.create');
        }
        /** create gateway */
        $gateway = app()->make('Gateway');
        /** get our customer */
        $myCustomer = Auth::user()->customer;
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
            return Redirect::to('customer.show');
        } else{
            return "oopsie woopsie";
        }
        return "oopsie woopsie";
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy()
    {
        if (Gate::denies('is-customer', Auth::user())) {
            return view('customer.create');
        }
        /** create gateway */
        $gateway = app()->make('Gateway');
        /** get our customer */
        $myCustomer = Auth::user()->customer;
        /** delete braintree customer */
        $result = $gateway->customer()->delete($myCustomer->braintree_id);
        /** delete our customer */
        Customer::where(['id', $myCustomer->id])->delete();
        if ($result->success) {
            Session::flash('message', 'Customer deleted.'); 
            return Redirect::to('home');
        } else{
            return "oopsie woopsie";
        }
        return "oopsie woopsie";
    }
}
