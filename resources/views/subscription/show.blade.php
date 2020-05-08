@extends('../layouts.app')

@section('content')
    Subscription: {{ $Subscription->planId }}
    <br/>
    Subscription status: {{ $Subscription->status }}
    <br/>
    Subscription price: {{ $Subscription->price }}
    <br/>
    Subscription created on: {{ $Subscription->createdAt }}
    <br/>
    @if ($Subscription->trialPeriod)
        Subscription is on {{ $Subscription->trialDuration }} {{ $Subscription->trialDurationUnit }} trial period.
        The free trial will end on {{ $Subscription->firstBillingDate }}, if you do not wish to be billed, cancel before then.
    @else
        Your Subscription is active, you will be billed on the {{ $Subscription->billingDayOfMonth }}th of each month
    @endif
    <br/>
@endsection