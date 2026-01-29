@extends('layouts.home')

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-10">
            @include('home.sections.sala_de_guerra')
            
            <div class="row">
                <div class="col-md-8">
                    @include('home.sections.estado_del_personaje')
                    @include('home.sections.accesos_rapidos')
                </div>
                <div class="col-md-4">
                    @include('home.sections.tablon')
                </div>
            </div>
        </div>
    </div>
@endsection
