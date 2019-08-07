@extends('layouts.adminpages')

@section('content')

    <main role="main" class="col-md-9 ml-sm-auto col-lg-10 pt-3 px-4">

        <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom">
            <h1 class="h2">oAuth 2</h1>
        </div>
        <h2>Dashboard</h2>
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <passport-clients></passport-clients>
                </div>
            </div>
            <div class="row justify-content-center pt-4">
                <div class="col-md-8">
                    <passport-authorized-clients></passport-authorized-clients>
                </div>
            </div>
        </div>
    </main>
@endsection

@section('js-app')
    <script src="{{ asset('js/apps/admin.js') }}" defer></script>
@endsection
