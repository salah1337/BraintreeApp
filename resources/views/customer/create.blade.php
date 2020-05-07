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
<form id="form" method="POST" action="/customer/create" class="container">
  {{ csrf_field() }}

  <div class="form-group">
    <label for="firstName">First Name</label>
    <input required type="text" class="form-control" name="firstName" id="firstName"placeholder="first name">
  </div>
  <div class="form-group">
    <label for="lastName">Last Name</label>
    <input required type="text" class="form-control" name="lastName" id="lastName"placeholder="last name">
  </div>
  <input type="text" id="nonce" hidden required name="paymentMethodNonce">
  <div id="dropin-container"></div>
  <div class="form-group">
    <button type="button" id="submit-button">Add Payment Method</button>
  </div>
  <button type="submit" class="btn btn-primary">Submit</button>
</form>  
@endauth
@endsection
@section('js')
<script src="https://js.braintreegateway.com/web/dropin/1.22.1/js/dropin.min.js"></script>
<script>

  window.addEventListener('DOMContentLoaded', ()=>{

  var button = document.querySelector('#submit-button');
  
  braintree.dropin.create({
  authorization: 'sandbox_q77mfd28_bmsnxb8gpbywh53h',
  container: '#dropin-container'
  }, function (createErr, instance) {    
    button.addEventListener('click', function () {
        instance.requestPaymentMethod(function (requestPaymentMethodErr, payload) {
        // Submit payload.nonce to your server
        console.log(payload.nonce)
        document.querySelector('#nonce').value = payload.nonce;
        button.style.display = "none";
        });
  });
  });
  });


</script>

@endsection