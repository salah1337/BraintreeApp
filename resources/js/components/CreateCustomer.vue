<template>
    <div class="container">
        <div class="form-group">
        <label for="firstName">First Name</label>
        <input required type="text" class="form-control" name="firstName" id="firstName"placeholder="first name">
        </div>
        <div class="form-group">
        <label for="lastName">Last Name</label>
        <input required type="text" class="form-control" name="lastName" id="lastName"placeholder="last name">
        </div>
        <div id="dropin-container" class="form-group">
        <div id="submit-button">Add Payment Method</div>
        </div>
        <button type="submit" class="btn btn-primary">Submit</button>
    </div>
</template>

<script>
    export default {
        data(){
            return{
                firstName: '',
                lastName: '',
                nonce: '',
            }
        },
        mounted() {
            // let instance = this.loadDropIn();

        },
        methods:{
            loadDropIn() {
                var button = document.querySelector('#submit-button');
  
                braintree.dropin.create({
                authorization: 'sandbox_q77mfd28_bmsnxb8gpbywh53h',
                container: '#dropin-container'
                }, function (createErr, instance) {   
                        return instance;
                });
            },
            requestPayment(instance) {
                button.addEventListener('click', function () {
                    instance.requestPaymentMethod(function (requestPaymentMethodErr, payload) {
                    // Submit payload.nonce to your server
                    console.log(payload.nonce)
                    document.querySelector('#nonce').value = payload.nonce;
                    button.style.display = "none";
                    });
                });
            }
        }
    }
</script>