<div
    class="game-play-page park-game-page"
    data-game-root
    data-game-type="limpia-parque"
    data-game-endpoint="<?= e(url('juegos/resultado')) ?>"
    data-waste-total="<?= (int) $wasteCount ?>"
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
                Encuentra los residuos ocultos sin molestar a la naturaleza.
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
            <i class="bi bi-trash3-fill"></i>
            <span>Recogidos</span>
            <strong>
                <span data-park-collected>0</span>/<?= (int) $wasteCount ?>
            </strong>
        </div>

        <div class="game-toolbar-item">
            <i class="bi bi-shield-heart-fill"></i>
            <span>Cuidado</span>
            <strong data-park-care>100%</strong>
        </div>

        <div class="game-toolbar-item">
            <i class="bi bi-trophy-fill"></i>
            <span>Tu récord</span>
            <strong><?= (int) $bestScore ?>/100</strong>
        </div>
    </section>

    <section class="park-mission-card mb-4">
        <div>
            <span class="game-eyebrow">
                <i class="bi bi-stars"></i>
                Misión ambiental
            </span>
            <h2>Devuelve la vida y el color al parque</h2>
            <p>
                Toca solamente los residuos. Las flores, aves, mariposas
                y hongos forman parte del ecosistema y deben permanecer ahí.
            </p>
        </div>

        <div class="park-progress-box">
            <div class="park-progress-label">
                <span>Parque recuperado</span>
                <strong data-park-progress-text>0%</strong>
            </div>
            <div class="park-progress">
                <span data-park-progress style="width: 0%"></span>
            </div>
        </div>
    </section>

    <section class="park-scene" data-park-scene>
        <div class="park-sky" aria-hidden="true">
            <span class="park-sun">☀️</span>
            <span class="park-cloud cloud-one">☁️</span>
            <span class="park-cloud cloud-two">☁️</span>
        </div>

        <div class="park-background" aria-hidden="true">
            <span class="park-tree tree-one">🌳</span>
            <span class="park-tree tree-two">🌲</span>
            <span class="park-tree tree-three">🌳</span>
            <span class="park-bench">🪑</span>
            <span class="park-path"></span>
        </div>

        <?php foreach ($objects as $object): ?>
            <button
                class="park-object park-object-<?= e($object['type']) ?>"
                type="button"
                data-park-object
                data-object-id="<?= e($object['id']) ?>"
                data-object-type="<?= e($object['type']) ?>"
                style="--object-x: <?= (int) $object['x'] ?>%; --object-y: <?= (int) $object['y'] ?>%;"
                aria-label="<?= e($object['label']) ?>"
                title="<?= e($object['label']) ?>"
            >
                <span aria-hidden="true"><?= e($object['emoji']) ?></span>
                <small><?= e($object['label']) ?></small>
            </button>
        <?php endforeach; ?>

        <div class="park-ranger" aria-hidden="true">
            <span>🧑‍🌾</span>
            <p>¡Ayúdame a encontrar todos los residuos!</p>
        </div>

        <div class="park-bag" aria-hidden="true">
            <i class="bi bi-trash3-fill"></i>
            <span data-park-bag-count>0</span>
        </div>
    </section>

    <div class="park-live-message" data-park-live aria-live="polite">
        <i class="bi bi-search"></i>
        <span>Explora el parque y selecciona el primer residuo.</span>
    </div>

    <div class="game-inline-message d-none mt-3" data-game-message></div>

    <section class="game-result-panel d-none mt-4" data-game-result>
        <div class="game-result-medal" data-result-medal>🌱</div>
        <span class="section-label">Misión completada</span>
        <h2 data-result-title>¡Sigue aprendiendo!</h2>

        <div class="game-result-score">
            <strong data-result-score>0</strong>
            <span>/100 puntos</span>
        </div>

        <p data-result-message></p>

        <div class="game-result-stats">
            <span>
                <i class="bi bi-check-circle-fill"></i>
                <strong data-result-correct>0</strong> residuos recogidos
            </span>
            <span>
                <i class="bi bi-x-circle-fill"></i>
                <strong data-result-errors>0</strong> elementos naturales tocados
            </span>
        </div>

        <div class="game-feedback-list" data-result-feedback></div>

        <div class="d-flex flex-wrap justify-content-center gap-2 mt-4">
            <button class="btn btn-eco" type="button" data-game-restart>
                <i class="bi bi-arrow-repeat me-2"></i>
                Repetir misión
            </button>
            <a class="btn btn-eco-outline" href="<?= e(url('juegos')) ?>">
                Elegir otro juego
            </a>
        </div>
    </section>
</div>