<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Subscription;
use Faker\Generator as Faker;

$factory->define(Subscription::class, function (Faker $faker) {
    
    $firstName = $faker->name;
    $companyName = Str::random(5);
    $randomCustomer = App\Customer::inRandomOrder()->first();

    return [
        'planId' => $faker->randomElement(['monthly_plan','bi_yearly_plan','yearly_plan']),
        'paymentMethodToken' => Str::random(8),
        'braintree_id' =>'22',
        'customer_id' => $randomCustomer->id,
    ];
});
