<section class="mb-5">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-12 col-lg-10 col-xl-9">

        <div class="card bg-dark text-light border-secondary rounded-4 shadow-sm overflow-hidden">
          <div class="card-body p-4 p-md-5">

            {{-- Header --}}
            <div class="d-flex flex-wrap align-items-start justify-content-between gap-3">
              <div class="text-start">
                <h2 class="mb-2 section-title-chronicle" style="font-family: serif; font-size: 2.5rem;">
                  Crónica Mensual
                </h2>
                <p class="text-secondary mb-3 text-center" style="max-width: 80ch;">
                  Cada mes, las razas compiten por el dominio del Viejo Mundo. La facción ganadora recibe un
                  <span class="fw-semibold chronicle-accent">cofre con 3 objetos de poder</span>.
                </p>
                <div class="d-flex justify-content-center gap-3 mb-3">
                  <i class="fas fa-gift text-warning fa-2x" title="Cofre de premio"></i>
                  <i class="fas fa-gift text-warning fa-2x" title="Cofre de premio"></i>
                  <i class="fas fa-gift text-warning fa-2x" title="Cofre de premio"></i>
                </div>
              </div>


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
                          <p class="mb-1 fw-semibold" style="color: #654321;">Victorias en combates</p>
                          <small class="text-secondary">Duelos, batallas y enfrentamientos suman puntos según dificultad.</small>
                        </div>
                      </div>
                    </div>

                    <div class="col-12">
                      <div class="d-flex gap-3 align-items-start p-3 rounded-4 border border-secondary-subtle bg-dark bg-opacity-25">
                        <span class="badge rounded-pill chronicle-step">+P</span>
                        <div>
                          <p class="mb-1 fw-semibold" style="color: #654321;">Misiones completadas</p>
                          <small class="text-secondary">Cada misión aporta puntos. Las de mayor rango aportan más.</small>
                        </div>
                      </div>
                    </div>

                    <div class="col-12">
                      <div class="d-flex gap-3 align-items-start p-3 rounded-4 border border-secondary-subtle bg-dark bg-opacity-25">
                        <span class="badge rounded-pill chronicle-step">+P</span>
                        <div>
                          <p class="mb-1 fw-semibold" style="color: #654321;">Logros y hitos</p>
                          <small class="text-secondary">Subidas de nivel, objetivos especiales y eventos del mes.</small>
                        </div>
                      </div>
                    </div>
                  </div>


                </div>
              </div>

              {{-- Imagen ejemplo clasificación --}}
              <div class="col-12 col-lg-6">
                <div class="p-3 p-md-4 rounded-4 bg-black bg-opacity-25 border border-secondary-subtle h-100">

                  <div class="d-flex align-items-center justify-content-between gap-3 mb-3">
                    <h4 class="h6 mb-0 section-subtitle-chronicle" style="font-family: serif;">
                      Clasificación mensual
                    </h4>
                  </div>

                  <div class="rounded-4 overflow-hidden border border-secondary-subtle bg-dark p-3">
                    @if (!empty($fallbackMessage))
                      <div class="alert alert-warning bg-dark bg-opacity-75 border-0 rounded-4 shadow-sm mb-3" role="alert">
                        <strong>{{ $fallbackMessage }}</strong>
                      </div>
                    @endif

                    @if (isset($seasonWinner) && $seasonWinner)
                      <p class="mb-2 text-warning text-center fs-5">
                        👑 Raza ganadora del mes anterior: <strong>{{ $seasonWinner->race_name ?? '—' }}</strong> 👑
                      </p>
                    @endif

                    @if (isset($seasonRankings) && $seasonRankings->count() > 0)
                      <div class="table-responsive">
                        <table class="table table-sm table-striped table-dark mb-0 align-middle">
                          <thead>
                            <tr>
                              <th>Posición</th>
                              <th>Raza</th>
                              <th class="text-end">Puntos</th>
                            </tr>
                          </thead>
                          <tbody>
                            @foreach($seasonRankings as $i => $r)
                              <tr>
                                <td>
                                  @if($i === 0)
                                    <span class="badge bg-warning text-dark fs-6">🥇</span>
                                  @elseif($i === 1)
                                    <span class="badge bg-secondary fs-6">🥈</span>
                                  @elseif($i === 2)
                                    <span class="badge bg-brown text-light fs-6" style="background-color:#8B5C2A!important;">🥉</span>
                                  @else
                                    <span class="badge bg-dark border border-secondary">{{ $i+1 }}</span>
                                  @endif
                                </td>
                                <td>{{ $r->race_name }}</td>
                                <td class="text-end">{{ $r->points }}</td>
                              </tr>
                            @endforeach
                          </tbody>
                        </table>
                      </div>
                      @if(isset($fallbackUsed) && $fallbackUsed === 'A')
                        <div class="mt-2 text-center">
                          <small class="text-secondary">Clasificación provisional (sin puntos aún)</small>
                        </div>
                      @endif
                    @endif
                  </div>

                </div>
              </div>
            </div>



          </div>
        </div>

      </div>
    </div>
  </div>
</section>

