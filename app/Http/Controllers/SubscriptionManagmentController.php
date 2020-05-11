<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Subscription;
use App\Customer;
use Auth;
use Session;
use Braintree;
use Redirect;
use DateTime;

class SubscriptionManagmentController extends Controller
{
    //
    public function startNow($id){



        $gateway->subscription()->update($oldSubscription->id, [
            'numberOfBillingCycles' => 1,
        ]);
    }
}
