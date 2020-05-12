@extends('../layouts.app')

@section('content')
    
<div class="container text-center">
    <h1>
        First name: {{ $braintreeCustomer->firstName }} <br/>
        Last name: {{ $braintreeCustomer->lastName }}   <br/>
        Email: {{ $braintreeCustomer->email }}          <br/>
    </h1>
    <br/>
    <br/>
    
    Member since {{ substr($myCustomer->created_at, 0, 10) }}
    <br/>
    <br/>
    @if ($braintreeCustomer->creditCards)
    Credit cards: <br/>
        @foreach ($braintreeCustomer->creditCards as $card)
        {{ $card->cardType }}
        <br/>
        Expiration date: {{ $card->expirationDate }}
        <br/>
        ****{{ $card->last4 }}
        <br/>
        @endforeach
    @endif
    @if ($braintreeCustomer->paypalAccounts)
    Paypal Accounts: <br/>
        @foreach ($braintreeCustomer->paypalAccounts as $account)
        {{ $account->email }}
        <br/>
        <img src="{{ $account->imageUrl }}" alt="">
        <br/>
        <br/>
        @endforeach
    @endif

    <br/>
    <br/>
    @if ($activeSubscription)
        <h4>
            Active Subscription:
        </h4>
        <h5>
            Plan: {{ $activeSubscription->planId }}
            <br/>
            Created at: {{ substr($activeSubscription->created_at, 0, 10) }}
            <br/>
            <a href="/subscription/show/{{$activeSubscription->id}}">More</a>
            <br/>
        </h5>
    @else
        You have no active subscriptions.
        <br/>
        <a href="/subscription/create" class="btn btn-success">Subscribe</a>
    @endif
    @if ($pendingSubscription)
            <h5>
                Pending Subscription:
            </h5>
            <h6>
                Plan: {{ $pendingSubscription->planId }}
                <br/>
                Created at: {{ substr($pendingSubscription->created_at, 0, 10) }}
                <br/>
                <a href="/subscription/show/{{$pendingSubscription->id}}">More</a>
                <br/>
            </h6>
    @endif
        <br/>
        <a href="/subscription/all">See subscription history</a>
        <br/>
        <br/>
        <a class="btn btn-danger" href="/customer/delete">Delete Customer</a>
        <br/>
</div>

@endsection