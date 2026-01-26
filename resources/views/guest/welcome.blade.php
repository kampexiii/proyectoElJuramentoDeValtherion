@extends('layouts.guest')

@section('content')
    @include('guest.sections.portada')
    @include('guest.sections.prologo')
    @include('guest.sections._razas_grid')
    @include('guest.sections.reglas_del_juramento')
    @include('guest.sections.cronica_mensual')
    @include('guest.sections.entrar_en_la_historia')
    @include('guest.sections.colofon')
    @include('partials.guest_footer')
@endsection
