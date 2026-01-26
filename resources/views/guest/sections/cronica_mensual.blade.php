<section class="mb-5">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-12 col-lg-10 col-xl-9">

        <div class="card bg-dark text-light border-secondary rounded-4 shadow-sm overflow-hidden">
          <div class="card-body p-4 p-md-5">

            {{-- Header --}}
            <div class="d-flex flex-wrap align-items-start justify-content-between gap-3">
              <div class="text-start">
                <h3 class="mb-2 section-title-chronicle" style="font-family: serif;">
                  Crónica Mensual
                </h3>
                <p class="text-secondary mb-0" style="max-width: 80ch;">
                  Cada mes, las razas compiten por el dominio del Viejo Mundo. La facción ganadora recibe un
                  <span class="fw-semibold chronicle-accent">cofre con 3 objetos de poder</span>.
                </p>
              </div>

              <span class="badge rounded-pill chronicle-badge px-3 py-2">
                Recompensa mensual
              </span>
            </div>

            <div class="row g-4 mt-3 align-items-stretch">
              {{-- Explicación + cómo sumar puntos --}}
              <div class="col-12 col-lg-6 text-start">
                <div class="p-3 p-md-4 rounded-4 bg-black bg-opacity-25 border border-secondary-subtle h-100">
                  <p class="mb-3 text-light" style="line-height: 1.8;">
                    La clasificación se alimenta de <span class="fw-semibold">Puntos de Raza</span>. Cada acción importante suma
                    presencia a tu facción. El ranking se reinicia al inicio de cada mes, pero la historia de la victoria queda marcada.
                  </p>

                  <h4 class="h6 mb-3 section-subtitle-chronicle" style="font-family: serif;">
                    Cómo conseguir Puntos de Raza
                  </h4>

                  <div class="row g-3">
                    <div class="col-12">
                      <div class="d-flex gap-3 align-items-start p-3 rounded-4 border border-secondary-subtle bg-dark bg-opacity-25">
                        <span class="badge rounded-pill chronicle-step">+P</span>
                        <div>
                          <p class="mb-1 text-white fw-semibold">Victorias en combates</p>
                          <small class="text-secondary">Duelos, batallas y enfrentamientos suman puntos según dificultad.</small>
                        </div>
                      </div>
                    </div>

                    <div class="col-12">
                      <div class="d-flex gap-3 align-items-start p-3 rounded-4 border border-secondary-subtle bg-dark bg-opacity-25">
                        <span class="badge rounded-pill chronicle-step">+P</span>
                        <div>
                          <p class="mb-1 text-white fw-semibold">Misiones completadas</p>
                          <small class="text-secondary">Cada misión aporta puntos. Las de mayor rango aportan más.</small>
                        </div>
                      </div>
                    </div>

                    <div class="col-12">
                      <div class="d-flex gap-3 align-items-start p-3 rounded-4 border border-secondary-subtle bg-dark bg-opacity-25">
                        <span class="badge rounded-pill chronicle-step">+P</span>
                        <div>
                          <p class="mb-1 text-white fw-semibold">Logros y hitos</p>
                          <small class="text-secondary">Subidas de nivel, objetivos especiales y eventos del mes.</small>
                        </div>
                      </div>
                    </div>
                  </div>

                  <div class="mt-3 pt-3 border-top border-secondary-subtle">
                    <small class="text-secondary">
                      Nota: la facción <span class="fw-semibold">Premium</span> siempre aparece la primera en la lista.
                      “Guerreros del Caos” no se muestra en esta clasificación.
                    </small>
                  </div>
                </div>
              </div>

              {{-- Imagen ejemplo clasificación --}}
              <div class="col-12 col-lg-6">
                <div class="p-3 p-md-4 rounded-4 bg-black bg-opacity-25 border border-secondary-subtle h-100">
                  <div class="d-flex align-items-center justify-content-between gap-3 mb-3">
                    <h4 class="h6 mb-0 section-subtitle-chronicle" style="font-family: serif;">
                      Ejemplo de Clasificación
                    </h4>
                    <span class="badge rounded-pill chronicle-badge px-3 py-2">
                      Vista previa
                    </span>
                  </div>

                  <div class="ratio ratio-16x9 rounded-4 overflow-hidden border border-secondary-subtle bg-dark">
                    {{-- Sustituye esta imagen por tu captura real cuando la tengas --}}
                    <img
                      src="{{ asset('assets/images/ejemplo-clasificacion.png') }}"
                      alt="Ejemplo de clasificación mensual de razas"
                      class="w-100 h-100"
                      style="object-fit: cover;"
                    >
                  </div>

                  <small class="text-secondary d-block mt-3">
                    La clasificación muestra todas las razas ordenadas por puntos. La premium aparece la primera.
                  </small>
                </div>
              </div>
            </div>

            {{-- Footer --}}
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mt-4 pt-3 border-top border-secondary-subtle">
              <small class="text-secondary">
                Recompensa: cofre con <span class="fw-semibold chronicle-accent">3 objetos de poder</span> para la facción ganadora del mes.
              </small>

              <a href="#" class="btn btn-outline-light btn-sm">
                Ver reglas
              </a>
            </div>

          </div>
        </div>

      </div>
    </div>
  </div>
</section>

