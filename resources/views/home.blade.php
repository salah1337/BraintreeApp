@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Dashboard</div>
                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif
                    @if(Session::has('message'))
                        <p class="alert {{ Session::get('alert-class', 'alert-info') }}">{{ Session::get('message') }}</p>
                    @endif
                    <div id="">
                        <div class="d-flex flex-column flex-md-row align-items-center p-3 px-md-4 mb-3 bg-white border-bottom box-shadow">
                          <h5 class="my-0 mr-md-auto font-weight-normal">Company name</h5>
                          <nav class="my-2 my-md-0 mr-md-3">
                            <a class="p-2 text-dark" href="/customer/show">Me</a>
                            <a class="p-2 text-dark" href="/subscription/">Subs</a>
                            <a class="p-2 text-dark" href="/contact">Support</a>
                            <a class="p-2 text-dark" href="/subscription/create">Pricing</a>
                          </nav>
                        </div>
                </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
