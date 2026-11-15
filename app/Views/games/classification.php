<div
    class="game-play-page"
    data-game-root
    data-game-type="clasifica"
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
            <h1 class="page-title mt-1">
                <?= e($game['title']) ?>
            </h1>
            <p class="page-subtitle">
                Arrastra cada objeto o selecciónalo y después toca un contenedor.
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
            <i class="bi bi-box-seam-fill"></i>
            <span>Clasificados</span>
            <strong>
                <span data-classified-count>0</span>/<?= count($items) ?>
            </strong>
        </div>

        <div class="game-toolbar-item">
            <i class="bi bi-trophy-fill"></i>
            <span>Tu récord</span>
            <strong><?= (int) $bestScore ?>/100</strong>
        </div>
    </section>

    <div class="game-instruction-banner mb-4">
        <span class="game-instruction-number">1</span>
        <div>
            <strong>Selecciona un residuo</strong>
            <p>En computadora también puedes arrastrarlo.</p>
        </div>

        <i class="bi bi-arrow-right instruction-arrow"></i>

        <span class="game-instruction-number">2</span>
        <div>
            <strong>Colócalo en un contenedor</strong>
            <p>Puedes cambiarlo antes de finalizar.</p>
        </div>
    </div>

    <section class="eco-card mb-4">
        <div class="eco-card-head">
            <div>
                <h2 class="eco-card-title">Residuos por clasificar</h2>
                <span class="small text-muted-eco">
                    Completa los <?= count($items) ?> objetos.
                </span>
            </div>

            <span class="game-selected-hint" data-selected-hint>
                Ningún objeto seleccionado
            </span>
        </div>

        <div class="eco-card-body">
            <div class="game-item-bank" data-item-bank>
                <?php foreach ($items as $item): ?>
                    <button
                        type="button"
                        class="game-waste-item"
                        draggable="true"
                        data-item-id="<?= e($item['id']) ?>"
                    >
                        <span class="game-item-emoji" aria-hidden="true">
                            <?= e($item['emoji']) ?>
                        </span>
                        <span><?= e($item['name']) ?></span>
                    </button>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <div class="game-bins-grid">
        <?php foreach ($categories as $key => $category): ?>
            <section
                class="game-bin game-bin-<?= e($category['color']) ?>"
                data-category="<?= e($key) ?>"
            >
                <div class="game-bin-head">
                    <span class="game-bin-icon">
                        <i class="bi <?= e($category['icon']) ?>"></i>
                    </span>

                    <div>
                        <strong><?= e($category['label']) ?></strong>
                        <small>Suelta o toca aquí</small>
                    </div>
                </div>

                <div class="game-bin-items" data-bin-items>
                    <span class="game-bin-empty" data-bin-empty>
                        <i class="bi bi-plus-circle"></i>
                        Agregar residuos
                    </span>
                </div>
            </section>
        <?php endforeach; ?>
    </div>

    <div class="game-actions-bar mt-4">
        <div>
            <strong>¿Terminaste de clasificar?</strong>
            <span>El resultado será validado por EcoGestión.</span>
        </div>

        <button
            type="button"
            class="btn btn-eco btn-lg"
            data-classification-submit
        >
            <i class="bi bi-check2-circle me-2"></i>
            Revisar resultado
        </button>
    </div>

    <div class="game-inline-message d-none mt-3" data-game-message></div>

    <section class="game-result-panel d-none mt-4" data-game-result>
        <div class="game-result-medal" data-result-medal>🌱</div>
        <span class="section-label">Resultado de la aventura</span>
        <h2 data-result-title>¡Sigue aprendiendo!</h2>

        <div class="game-result-score">
            <strong data-result-score>0</strong>
            <span>/100 puntos</span>
        </div>

        <p data-result-message></p>

        <div class="game-result-stats">
            <span>
                <i class="bi bi-check-circle-fill"></i>
                <strong data-result-correct>0</strong> aciertos
            </span>
            <span>
                <i class="bi bi-x-circle-fill"></i>
                <strong data-result-errors>0</strong> por mejorar
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