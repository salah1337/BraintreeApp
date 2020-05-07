<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Providers\AppServiceProvider;
use App\Customer;
Use Braintree;
use Auth;
use Session;
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
        if ( Customer::where(['user_id'=>Auth::user()->id])->exists() ){
            Session::flash('message', 'You are already a customer!'); 
            return view('home');
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
        //
        $gateway = app()->make('Gateway');

        $result = $gateway->customer()->create([
            'firstName' => $request->get('firstName'),
            'lastName' => $request->get('lastName'),
            'email' => Auth::user()->email,
            'paymentMethodNonce' => $request->get('paymentMethodNonce'),
        ]);
        if ( Customer::where(['user_id'=>Auth::user()->id])->exists() ){
            Session::flash('message', 'You are already a customer!'); 
            return view('home');
        }
        $mycustomer = Customer::create([
            "firstName" => $result->customer->firstName,
            "lastName" => $result->customer->lastName,
            "braintree_id" => $result->customer->id,
            "user_id"=>Auth::user()->id,
        ]);
        Session::flash('message', 'Customer created succesfully.');
        return view('home');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
