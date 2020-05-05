<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Customer;
use Faker\Generator as Faker;
use Illuminate\Support\Str;

$factory->define(Customer::class, function (Faker $faker) {

    $firstName = $faker->name;
    $companyName = Str::random(5);
    $randomUser = App\User::inRandomOrder()->first();

    return [
        'firstName' => $firstName,
        'lastName' => $faker->name,
        'company' => $companyName,
        'email' => $randomUser->email,
        'website' => $firstName.'@'.$companyName.'.com',
        'phone' => $faker->phoneNumber,
        'fax' => $faker->phoneNumber,
        'braintree_id' => $faker->unique()->randomDigit,
        'user_id' => $randomUser->id
    ];
});
