@php
    $razas = [
        [
            'key' => 'humanos',
            'nombre' => 'Humanos',
            'descripcion' => 'Voluntad, ambición y supervivencia. Resistir también es una forma de guerra.',
            'imagen' => '/assets/common/razas/humanos.webp',
            'is_premium' => false,
        ],
        [
            'key' => 'enanos',
            'nombre' => 'Enanos',
            'descripcion' => 'Honor y agravios tallados en piedra. El acero rúnico no olvida.',
            'imagen' => '/assets/common/razas/enanos.webp',
            'is_premium' => false,
        ],
        [
            'key' => 'altos-elfos',
            'nombre' => 'Altos Elfos',
            'descripcion' => 'Legado antiguo y disciplina implacable. Magia refinada, orgullo intacto.',
            'imagen' => '/assets/common/razas/altos-elfos.webp',
            'is_premium' => false,
        ],
        [
            'key' => 'elfos-silvanos',
            'nombre' => 'Elfos Silvanos',
            'descripcion' => 'El bosque observa y castiga. Flechas antes que palabras.',
            'imagen' => '/assets/common/razas/elfos-silvanos.webp',
            'is_premium' => false,
        ],
        [
            'key' => 'elfos-oscuros',
            'nombre' => 'Elfos Oscuros',
            'descripcion' => 'Crueldad y dominio. Donde pisan, la compasión desaparece.',
            'imagen' => '/assets/common/razas/elfos-oscuros.webp',
            'is_premium' => false,
        ],
        [
            'key' => 'orcos',
            'nombre' => 'Orcos',
            'descripcion' => 'Brutalidad pura. Si algo no se rompe… lo rompen.',
            'imagen' => '/assets/common/razas/orcos.webp',
            'is_premium' => false,
        ],
        [
            'key' => 'skaven',
            'nombre' => 'Skaven',
            'descripcion' => 'Plagas y traición bajo la tierra. Siempre hay más, siempre más cerca.',
            'imagen' => '/assets/common/razas/skaven.webp',
            'is_premium' => false,
        ],
        [
            'key' => 'hombres-bestia',
            'nombre' => 'Hombres Bestia',
            'descripcion' => 'Furia salvaje. Emboscadas, cuernos y sangre en el barro.',
            'imagen' => '/assets/common/razas/hombres-bestia.webp',
            'is_premium' => false,
        ],
        [
            'key' => 'condes-vampiro',
            'nombre' => 'Condes Vampiro',
            'descripcion' => 'Noche eterna. Legiones que no descansan y señores que no perdonan.',
            'imagen' => '/assets/common/razas/condes-vampiro.webp',
            'is_premium' => false,
        ],
        [
            'key' => 'reyes-funerarios',
            'nombre' => 'Reyes Funerarios',
            'descripcion' => 'Imperios enterrados. El pasado marcha y exige obediencia.',
            'imagen' => '/assets/common/razas/reyes-funerarios.webp',
            'is_premium' => false,
        ],
        [
            'key' => 'hombres-lagarto',
            'nombre' => 'Hombres Lagarto',
            'descripcion' => 'Junglas y templos. El Gran Plan no admite errores.',
            'imagen' => '/assets/common/razas/hombres-lagarto.webp',
            'is_premium' => false,
        ],
        [
            'key' => 'enanos-del-caos',
            'nombre' => 'Enanos del Caos',
            'descripcion' => 'Forjas blasfemas y cadenas. El humo oculta juramentos rotos.',
            'imagen' => '/assets/common/razas/enanos-del-caos.webp',
            'is_premium' => false,
        ],
        [
            'key' => 'demonios-del-caos',
            'nombre' => 'Demonios del Caos',
            'descripcion' => 'Entidad del Reino Disforme. Pactos que se pagan con el alma.',
            'imagen' => '/assets/common/razas/demonios-del-caos.webp',
            'is_premium' => true,
        ],
    ];
@endphp

<section class="mb-5">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-12 col-lg-10 col-xl-9">

        <div class="card bg-dark text-light border-secondary rounded-4 shadow-sm overflow-hidden">
          <div class="card-body p-4 p-md-5">

            {{-- Header --}}
            <div class="d-flex flex-wrap align-items-start justify-content-center gap-3">
              <div class="text-center">
                <h2 class="mb-3 section-title-races" style="font-family: serif; font-size: 2.5rem;">
                  Razas del Viejo Mundo
                </h2>
                <p class="text-center mb-4 section-description-races" style="color: #654321;">
                    Cada raza carga un legado… y una deuda. Al jurar, heredas su gloria y su condena:</p>
                <p class="text-center mb-4 section-description-races" style="color: #654321;">El Juramento te marcará para siempre.</p>
              </div>
            </div>

            {{-- Grid de Razas --}}
            <div class="row g-3 g-md-4 justify-content-center">
                @foreach($razas as $raza)
                    <div class="col-12 col-md-6 col-lg-4 col-xl-3">
                        <article
                            class="race-card card h-100 border-0 shadow-sm {{ $raza['is_premium'] ? 'is-premium' : '' }}"
                            tabindex="0"
                            role="group"
                            aria-label="{{ $raza['nombre'] }}"
                        >
                            <div class="race-card-body">
                                <div class="race-aside">
                                    <img
                                        src="{{ $raza['imagen'] }}"
                                        alt="Avatar de {{ $raza['nombre'] }}"
                                        class="race-avatar"
                                        width="52"
                                        height="52"
                                        loading="lazy"
                                    >
                                </div>
                                <div class="race-content">
                                    <div class="race-header">
                                        <h4 class="race-title">{{ $raza['nombre'] }}</h4>
                                    </div>
                                    <div class="race-main">
                                        <p class="race-desc">{{ $raza['descripcion'] }}</p>
                                    </div>
                                    <div class="race-footer">
                                        @if(!$raza['is_premium'])
                                            <span class="race-status">Disponible</span>
                                        @else
                                            <div class="race-pill-premium">Premium</div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </article>
                    </div>
                @endforeach
            </div>

          </div>
        </div>

      </div>
    </div>
  </div>
</section>