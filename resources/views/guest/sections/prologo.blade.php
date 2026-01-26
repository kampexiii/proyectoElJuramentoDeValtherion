@php
    $title = 'Prólogo';
    $teaser = [
        'Dicen los viejos cronistas que el mundo no se rompe de golpe, sino a mordiscos: un juramento mal dicho, una corona mal puesta, una verdad guardada demasiado tiempo.',
        'En el Norte de Valtherion, donde la escarcha oscurece los caminos, la Marca de Ceniza vuelve a latir. No con gritos… con un pulso profundo, como si el mundo recordara algo que quiso enterrar.',
        'Aquí no nacen héroes. Se forjan. Y cada decisión pesa como hierro: alianza, traición o silencio. El Juramento está despierto… y nadie saldrá intacto.'
    ];
@endphp

<section class="prologo py-5">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-12 col-lg-10">
        <div class="card bg-transparent border-0">
          <div class="card-body p-4 p-md-5">
            <h2 class="h3 mb-3 text-light" style="font-family: serif;">{{ $title }}</h2>

            @foreach($teaser as $p)
              <p class="text-muted mb-3" style="max-width: 72ch; line-height: 1.8;">{{ $p }}</p>
            @endforeach

          </div>
        </div>
      </div>
    </div>
  </div>
</section>
