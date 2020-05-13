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
    public function __construct() {
        $this->middleware('auth');
        $this->middleware('customer');
      }
    //
    public function startNow($id){
        $user = Auth::user();
        /** check is user is customer */
        
        /** get sub */
        $mySubscription = Subscription::find($id);

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
    /** upgrade/downgrade subs */
    public function switch($id, $planId)
    {
        /** get user & subscription */
        $mySubscription = Subscription::find($id);
        $user = Auth::user();
        /** check if user is allowed to edit */
        $this->authorize('edit-subscription', $user, $mySubscription);
        /** make new subscription */
        $gateway = app()->make('Gateway');
        /** get old subscription from braintree */
        $oldSubscription = $gateway->subscription()->find($mySubscription->braintree_id);
        /** get customer */
        $myCustomer = $user->customer;
        $braintreeCustomer = $gateway->customer()->find($myCustomer->braintree_id);
        /** make subscription that starts when old one ends */
        $res = $gateway->subscription()->create([
            'paymentMethodToken' => $braintreeCustomer->paymentMethods[0]->token,
            'planId' => $planId,
            'firstBillingDate' => $oldSubscription->nextBillingDate
        ]);
        $newSubscription = $res->subscription;
        /** make last one end this month */
        $gateway->subscription()->update($oldSubscription->id, [
            'numberOfBillingCycles' => 1,
        ]);
        /** save new subscription details in our db */
        Subscription::create([
            'paymentMethodToken' => $newSubscription->paymentMethodToken,
            'planId' => $newSubscription->planId,
            'braintree_id' => $newSubscription->id,
            'status' => $newSubscription->status,
            'customer_id' => $myCustomer->id,
        ]);
        /** victory! */
        Session::flash('message', 'Donezo'); 
        return Redirect::to('home');
    }
}
