<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Transaction;
use Faker\Generator as Faker;

$factory->define(Transaction::class, function (Faker $faker){
    $randomCustomer = App\Customer::inRandomOrder()->first();

    return [
        'amount' => "33",
        'braintree_id' => '33',
        'customer_id' => $randomCustomer->id
    ];
});
