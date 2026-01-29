@extends('layouts.guest')

@section('content')
    @include('guest.sections.portada')
    @include('guest.sections.prologo')
    @include('guest.sections._razas_grid')
    @include('guest.sections.reglas_del_juramento')
        @include('guest.sections.cronica_mensual', [
            'previousSeason' => $previousSeason ?? null,
            'seasonRankings' => $seasonRankings ?? collect(),
            'seasonWinner' => $seasonWinner ?? null,
            'fallbackUsed' => $fallbackUsed ?? null,
            'fallbackMessage' => $fallbackMessage ?? null,
        ])
    @include('guest.sections.entrar_en_la_historia')
    @include('guest.sections.footer')
@endsection
