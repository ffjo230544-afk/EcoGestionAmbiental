<div
    class="game-play-page memory-game-page"
    data-game-root
    data-game-type="memorama"
    data-game-endpoint="<?= e(url('juegos/resultado')) ?>"
>
    <input
        type="hidden"
        value="<?= e(csrf_token()) ?>"
        data-game-csrf
    >

    <div class="page-header game-page-header">
        <div>
            <span class="section-label">EcoAventuras</span>
            <h1 class="page-title mt-1"><?= e($game['title']) ?></h1>
            <p class="page-subtitle">
                Encuentra cada residuo y relaciónalo con su destino correcto.
            </p>
        </div>

        <a class="btn btn-eco-outline" href="<?= e(url('juegos')) ?>">
            <i class="bi bi-arrow-left me-2"></i>
            Volver a los juegos
        </a>
    </div>

    <section class="game-toolbar mb-4">
        <div class="game-toolbar-item">
            <i class="bi bi-stopwatch-fill"></i>
            <span>Tiempo</span>
            <strong data-game-timer>00:00</strong>
        </div>

        <div class="game-toolbar-item">
            <i class="bi bi-grid-3x3-gap-fill"></i>
            <span>Parejas</span>
            <strong>
                <span data-memory-matches>0</span>/<?= count($game['pairs']) ?>
            </strong>
        </div>

        <div class="game-toolbar-item">
            <i class="bi bi-arrow-repeat"></i>
            <span>Intentos</span>
            <strong data-memory-attempts>0</strong>
        </div>

        <div class="game-toolbar-item">
            <i class="bi bi-trophy-fill"></i>
            <span>Tu récord</span>
            <strong><?= (int) $bestScore ?>/100</strong>
        </div>
    </section>

    <section class="memory-stage">
        <div class="memory-stage-copy">
            <span class="game-eyebrow">
                <i class="bi bi-eye-fill"></i>
                Observa, recuerda y relaciona
            </span>

            <h2>Descubre las seis parejas ambientales</h2>
            <p>
                Voltea dos tarjetas por turno. Una pareja se forma al unir
                un residuo con la manera adecuada de aprovecharlo o desecharlo.
            </p>

            <div class="memory-tip">
                <span>💡</span>
                <p>
                    Menos intentos significa un mejor puntaje.
                    Cada error descuenta 8 puntos.
                </p>
            </div>
        </div>

        <div class="memory-board" data-memory-board>
            <?php foreach ($cards as $card): ?>
                <button
                    class="memory-card"
                    type="button"
                    data-memory-card
                    data-card-id="<?= e($card['id']) ?>"
                    data-match-token="<?= e($card['match_token']) ?>"
                    aria-label="Voltear tarjeta"
                    aria-pressed="false"
                >
                    <span class="memory-card-inner">
                        <span class="memory-card-face memory-card-back">
                            <i class="bi bi-leaf-fill"></i>
                            <small>EcoGestión</small>
                        </span>

                        <span class="memory-card-face memory-card-front">
                            <span class="memory-card-emoji" aria-hidden="true">
                                <?= e($card['emoji']) ?>
                            </span>
                            <strong><?= e($card['label']) ?></strong>
                        </span>
                    </span>
                </button>
            <?php endforeach; ?>
        </div>
    </section>

    <div class="game-inline-message d-none mt-3" data-game-message></div>

    <section class="game-result-panel d-none mt-4" data-game-result>
        <div class="game-result-medal" data-result-medal>🌱</div>
        <span class="section-label">Resultado del memorama</span>
        <h2 data-result-title>¡Sigue aprendiendo!</h2>

        <div class="game-result-score">
            <strong data-result-score>0</strong>
            <span>/100 puntos</span>
        </div>

        <p data-result-message></p>

        <div class="game-result-stats">
            <span>
                <i class="bi bi-check-circle-fill"></i>
                <strong data-result-correct>0</strong> parejas
            </span>
            <span>
                <i class="bi bi-x-circle-fill"></i>
                <strong data-result-errors>0</strong> intentos fallidos
            </span>
        </div>

        <div class="game-feedback-list" data-result-feedback></div>

        <div class="d-flex flex-wrap justify-content-center gap-2 mt-4">
            <button class="btn btn-eco" type="button" data-game-restart>
                <i class="bi bi-arrow-repeat me-2"></i>
                Jugar nuevamente
            </button>
            <a class="btn btn-eco-outline" href="<?= e(url('juegos')) ?>">
                Elegir otro juego
            </a>
        </div>
    </section>
</div>