@extends('../layouts.app')

@section('content')
    <div class="text-center container">
        Subscription: {{ $Subscription->planId }}
        <br/>
        Subscription status: {{ $Subscription->status }}
        <br/>
        Subscription price: {{ $Subscription->price }}
        <br/>
        @if ( $Subscription->status != "Canceled" )
            Subscription created on: {{ $Subscription->createdAt }}
            <br/>
            @if ($Subscription->trialPeriod)
                Subscription is on {{ $Subscription->trialDuration }} {{ $Subscription->trialDurationUnit }} trial period.
                The free trial will end on {{ $Subscription->firstBillingDate }}, if you do not wish to be billed, cancel before then.
            @else
                Your Subscription is active, you will be billed on the {{ $Subscription->billingDayOfMonth }}th of each month.
            @endif
            <br/>
            <br/>
            <a class="btn btn-primary" href="{{ URL::previous() }}">Back</a>
            <a class="btn btn-warning" href="/subscription/edit/{{ $mySubscription->id }}">Change Subscription</a>
            <a class="btn btn-danger" href="/subscription/cancel/{{ $mySubscription->id }}">Cancel</a>
        @else
            <a class="btn btn-primary" href="{{ URL::previous() }}">Back</a>
            <a class="btn btn-success" href="/subscription/create">Subscribe Again</a>
        @endif
    </div>
@endsection