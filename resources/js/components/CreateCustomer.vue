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
        mounted() {
            console.log('Component mounted.')
        },
        methods:{

        }
    }
</script>