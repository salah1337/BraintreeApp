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
        /** get sub */
        $mySubscription = Subscription::find($id);

        $user = Auth::user();
        /** check for authorization */
        $this->authorize('edit-subscription', $user, $mySubscription);
        /** make gateway */
        $gateway = app()->make('Gateway');
        /** get pending subscription */
        $pendingSubscription = $gateway->subscription()->find($mySubscription->braintree_id);
        $status = $pendingSubscription->status;
        if ( $status !== "Pending" ){
            return 'nope';
        }
        /** get our customer */
        $myCustomer = $user->customer;
        /** get braintree customer */
        $braintreeCustomer = $gateway->customer()->find($myCustomer->braintree_id);
        /** create new subscription from last one's details but that starts today */
        $newSubscription = $gateway->subscription()->create([
            'paymentMethodToken' => $braintreeCustomer->paymentMethods[0]->token,
            'planId' => $pendingSubscription->planId,
            'options' => ['startImmediately' => true]
        ]);
        /** cancel old one and delete it from our db */
        $gateway->subscription()->cancel($mySubscription->braintree_id);
        $mySubscription->delete();

        return $newSubscription;
    }
}
