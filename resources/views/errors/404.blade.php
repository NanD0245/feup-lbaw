@extends('layouts.errors')

@section('errors')
<div class="error-body">
    <div class="error-container row m-0" style="padding-top: 15%;">
        <div class="error-number col-md-6">
            <div class="error-neon">404</div> 
        </div>
        <div class="error-start-flex col-md-6">
            <div class="error-meaning">
                <div class="error-message">Page not found.</div>
                <div class="error-message2">You tried to access a page that doesn't exist.</div>
            </div>
        </div>
    </div>
</div>
@endsection