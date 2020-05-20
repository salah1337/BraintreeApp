@extends('../layouts.app')

@section('content')
    
    @if (!$subscriptions)
        <h1>Something went wrong here. <a href='/contact'> Contact us </a> </h1>
    @endif
    @foreach ($subscriptions as $subscription)
        <div class="card">
            <div class="card-body">
            <h5 class="card-title">{{ $subscription->planId }}</h5>
            <p class="card-text">Subscription is {{ $subscription->status }}</p>
            <a href="/subscription/show/{{ $subscription->id }}" class="btn btn-primary">More</a>
            </div>
        </div>
    @endforeach
@endsection