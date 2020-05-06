@extends('../layouts.app')

@section('content')

<form method="POST" action="/customer/create" class="container">
    {{ csrf_field() }}
    @auth
        {{Auth::user()->email}}
    @endauth
    <div class="form-group">
      <label for="firstName">First Name</label>
      <input required type="text" class="form-control" name="firstName" id="firstName"placeholder="first name">
    </div>
    <div class="form-group">
      <label for="lastName">Last Name</label>
      <input required type="text" class="form-control" name="lastName" id="lastName"placeholder="last name">
    </div>
    <button type="submit" class="btn btn-primary">Submit</button>
</form>

@endsection
