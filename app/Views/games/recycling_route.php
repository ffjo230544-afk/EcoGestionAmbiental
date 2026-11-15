<div
    class="game-play-page recycling-route-page"
    data-game-root
    data-game-type="ruta-reciclaje"
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
                Ordena cada proceso desde la separación hasta el nuevo producto.
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
            <i class="bi bi-signpost-split-fill"></i>
            <span>Rutas</span>
            <strong><?= count($routes) ?></strong>
        </div>

        <div class="game-toolbar-item">
            <i class="bi bi-list-ol"></i>
            <span>Etapas</span>
            <strong>
                <?= array_sum(
                    array_map(
                        static fn(array $route): int =>
                            count($route['stages']),
                        $routes
                    )
                ) ?>
            </strong>
        </div>

        <div class="game-toolbar-item">
            <i class="bi bi-trophy-fill"></i>
            <span>Tu récord</span>
            <strong><?= (int) $bestScore ?>/100</strong>
        </div>
    </section>

    <section class="route-mission-banner mb-4">
        <div class="route-mission-icon" aria-hidden="true">
            <i class="bi bi-arrow-repeat"></i>
        </div>

        <div>
            <span class="game-eyebrow">
                <i class="bi bi-stars"></i>
                Completa el ciclo
            </span>
            <h2>Todo residuo aprovechable sigue una ruta</h2>
            <p>
                Arrastra las tarjetas o usa las flechas para acomodarlas.
                La etapa número 1 debe ser el inicio del proceso.
            </p>
        </div>
    </section>

    <div class="route-lanes">
        <?php foreach ($routes as $route): ?>
            <section
                class="route-lane route-lane-<?= e($route['color']) ?>"
                data-route-lane
                data-route-id="<?= e($route['id']) ?>"
            >
                <header class="route-lane-head">
                    <span class="route-lane-icon">
                        <i class="bi <?= e($route['icon']) ?>"></i>
                    </span>

                    <div>
                        <h2><?= e($route['title']) ?></h2>
                        <p><?= e($route['description']) ?></p>
                    </div>

                    <span class="route-lane-count">
                        <?= count($route['stages']) ?> etapas
                    </span>
                </header>

                <div class="route-stage-list" data-route-list>
                    <?php foreach ($route['stages'] as $stage): ?>
                        <article
                            class="route-stage-card"
                            draggable="true"
                            data-route-stage
                            data-stage-id="<?= e($stage['id']) ?>"
                        >
                            <span class="route-stage-position" data-stage-position>
                                1
                            </span>

                            <span class="route-stage-emoji" aria-hidden="true">
                                <?= e($stage['emoji']) ?>
                            </span>

                            <div class="route-stage-copy">
                                <strong><?= e($stage['title']) ?></strong>
                                <p><?= e($stage['description']) ?></p>
                            </div>

                            <div class="route-stage-controls">
                                <button
                                    type="button"
                                    data-stage-up
                                    aria-label="Subir <?= e($stage['title']) ?>"
                                    title="Subir"
                                >
                                    <i class="bi bi-chevron-up"></i>
                                </button>

                                <button
                                    type="button"
                                    data-stage-down
                                    aria-label="Bajar <?= e($stage['title']) ?>"
                                    title="Bajar"
                                >
                                    <i class="bi bi-chevron-down"></i>
                                </button>
                            </div>

                            <i class="bi bi-grip-vertical route-drag-handle"></i>
                        </article>
                    <?php endforeach; ?>
                </div>
            </section>
        <?php endforeach; ?>
    </div>

    <div class="game-actions-bar mt-4">
        <div>
            <strong>¿Las tres rutas están ordenadas?</strong>
            <span>PHP comprobará la posición de las quince etapas.</span>
        </div>

        <button
            type="button"
            class="btn btn-eco btn-lg"
            data-route-submit
        >
            <i class="bi bi-check2-circle me-2"></i>
            Revisar las rutas
        </button>
    </div>

    <div class="game-inline-message d-none mt-3" data-game-message></div>

    <section class="game-result-panel d-none mt-4" data-game-result>
        <div class="game-result-medal" data-result-medal>🌱</div>
        <span class="section-label">Resultado de las rutas</span>
        <h2 data-result-title>¡Sigue aprendiendo!</h2>

        <div class="game-result-score">
            <strong data-result-score>0</strong>
            <span>/100 puntos</span>
        </div>

        <p data-result-message></p>

        <div class="game-result-stats">
            <span>
                <i class="bi bi-check-circle-fill"></i>
                <strong data-result-correct>0</strong> etapas correctas
            </span>
            <span>
                <i class="bi bi-x-circle-fill"></i>
                <strong data-result-errors>0</strong> fuera de posición
            </span>
        </div>

        <div class="game-feedback-list route-feedback-list" data-result-feedback></div>

        <div class="d-flex flex-wrap justify-content-center gap-2 mt-4">
            <button class="btn btn-eco" type="button" data-game-restart>
                <i class="bi bi-arrow-repeat me-2"></i>
                Intentar nuevamente
            </button>
            <a class="btn btn-eco-outline" href="<?= e(url('juegos')) ?>">
                Elegir otro juego
            </a>
        </div>
    </section>
</div>