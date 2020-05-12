@extends('../layouts.app')

@section('content')
    <div class="text-center container">
        Subscription: {{ $Subscription->planId }}
        <br/>
        Subscription status: {{ $Subscription->status }}
        <br/>
        Subscription price: {{ $Subscription->price }}
        <br/>
        @switch($Subscription->status)
            @case("Active")
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
                    <a class="btn btn-danger" href="/subscription/cancel/{{ $mySubscription->id }}">Cancel</a>
                    <br/>
                    @cannot('has-pending-sub', Auth::user())
                        @if (!$Subscription->trialPeriod)
                            <a class="btn btn-warning" href="/subscription/edit/{{ $mySubscription->id }}">Upgrade / Downgrade</a>
                        @endif
                    @endcannot
                @break
            @case("Canceled")
                    <a class="btn btn-primary" href="{{ URL::previous() }}">Back</a>
                    <a class="btn btn-success" href="/subscription/create">Subscribe Again</a>
                @break
            @case("Pending")
                    Subscription is pending untill {{ $Subscription->firstBillingDate }}.
                    <br/>
                    @cannot('is-subbed', Auth::user())
                        <a class="btn btn-success" href="/subscription/startnow/{{ $mySubscription->id }}">Start Now</a>
                    @endcannot
                    <a class="btn btn-danger" href="/subscription/cancel/{{ $mySubscription->id }}">Cancel</a>
                    <a class="btn btn-primary" href="{{ URL::previous() }}">Back</a>
                @break
            @default
                
        @endswitch
    </div>
@endsection