@extends('layouts.guest')

@section('content')
<section class="py-4">
    <h1 class="h3 mb-3">Legales</h1>
    <p class="text-muted">Consulta nuestras politicas y condiciones.</p>

    <ul class="list-unstyled">
        <li class="mb-2"><a href="{{ route('legal.cookies') }}">Politica de cookies</a></li>
        <li class="mb-2"><a href="{{ route('legal.terms') }}">Terminos y condiciones</a></li>
        <li class="mb-2"><a href="{{ route('legal.privacy') }}">Politica de privacidad</a></li>
    </ul>
</section>
@endsection
