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
    Credit cards: <br/>
    @foreach ($braintreeCustomer->creditCards as $card)
        {{ $card->cardType }}
        <br/>
        Expiration date: {{ $card->expirationDate }}
        <br/>
        ****{{ $card->last4 }}
        <br/>
    @endforeach
    <br/>
    <br/>
    @if ($subscriptions)
        @foreach ($subscriptions as $subscription)
            Plan: {{ $subscription->planId }}
            <br/>
            Created at: {{ substr($subscription->created_at, 0, 10) }}
            <br/>
            <a href="/subscription/show/{{$subscription->id}}">More</a>
            <br/>
            <br/>
        @endforeach
    @endif
</div>

@endsection