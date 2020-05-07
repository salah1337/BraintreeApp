@extends('../layouts.app')

@section('content')
@guest
    <button>
      <a href="/login">Login</a>
    </button>
    <button>
      <a href="/login">Register</a>
    </button>
@endguest
@auth
<form id="form" method="POST" action="/customer/update" class="container">
  {{ csrf_field() }}
  <input type="hidden" name="_method" value="patch" />
  <div class="form-group">
    <label for="firstName">First Name</label>
    <input value="{{ $firstName }}" required type="text" class="form-control" name="firstName" id="firstName"placeholder="first name">
  </div>
  <div class="form-group">
    <label for="lastName">Last Name</label>
    <input value="{{ $lastName }}" required type="text" class="form-control" name="lastName" id="lastName"placeholder="last name">
  </div>
  <button type="submit" class="btn btn-primary">Submit</button>
</form>  
@endauth
@endsection
