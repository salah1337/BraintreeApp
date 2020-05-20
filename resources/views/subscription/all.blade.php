@extends('../layouts.app')

@section('content')
    
    @if (!$subscriptions)
        <h1>Something went wrong here. <a href='/contact'> Contact us </a> </h1>
    @endif
    <div class="container grid-box">
        @foreach ($subscriptions as $subscription)
        <div class="card">
            <div class="card-body">
            <h5 class="card-title">{{ $subscription->planId }}</h5>
            <p class="card-text">Subscription is {{ $subscription->status }}</p>
            <a href="/subscription/show/{{ $subscription->id }}" class="btn btn-primary">More</a>
            </div>
        </div>
        @endforeach
    </div>
@endsection


@section('css')
    <style>

      .grid-box{
          display: grid;
          grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
          grid-auto-rows: repeat(auto-fill, minmax(250px, 1fr));
          grid-column-gap: 10px;
          grid-row-gap: 10px;
      }

    </style>
@endsection