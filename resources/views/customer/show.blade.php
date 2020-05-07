@extends('../layouts.app')

@section('content')
    
    <h1>
        First name: {{ $braintreeCustomer->firstName }} <br/>
        Last name: {{ $braintreeCustomer->lastName }}   <br/>
        Email: {{ $braintreeCustomer->email }}          <br/>
    </h1>
    
    Member since {{ substr($myCustomer->created_at, 0, 10) }}
    <br/>
    Credit cards: <br/>
    @foreach ($braintreeCustomer->creditCards as $card)
        {{ $card->cardType }}<br/>
        Expiration date: {{ $card->expirationDate }}<br/>
        ****{{ $card->last4 }}<br/>
    @endforeach
@endsection