@extends('layouts.home')

@section('content')
    <div class="row">
        <div class="col-md-8">
            @include('home.sections.quick_actions')
            @include('home.sections.status')
        </div>
        <div class="col-md-4">
            @include('home.sections.ranking')
        </div>
    </div>
@endsection
