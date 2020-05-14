<?php

namespace App\Http\Controllers;

use App\Transaction;
use Illuminate\Http\Request;
use Auth;
class TransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        /** check if user is a customer */
        $user = Auth::user();
        $this->authorize('is-customer', $user);
        /** get braintree customer */
        $gateway = app()->make('Gateway');
        $myCustomer = $user->customer;
        $braintreeCustoemr = $gateway->customer()->find($myCustomer->braintree_id);
        /** make transaction */
        $res = $gateway->transaction()->sale([
            'customerId' => $braintreeCustoemr->id,
            'amount' => '33',
            'options' => [
                'submitForSettlement' => True
            ]
        ]);
        $transaction = $res->transaction;
        /** save details in our db */
        $myTransaction = Transaction::create([
            'braintree_id' => $transaction->id,
            'amount' => '33',
            'customer_id' => $myCustomer->id
        ]);
        return $myTransaction;

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
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Transaction  $transaction
     * @return \Illuminate\Http\Response
     */
    public function show(Transaction $transaction)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Transaction  $transaction
     * @return \Illuminate\Http\Response
     */
    public function edit(Transaction $transaction)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Transaction  $transaction
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Transaction $transaction)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Transaction  $transaction
     * @return \Illuminate\Http\Response
     */
    public function destroy(Transaction $transaction)
    {
        //
    }
}
