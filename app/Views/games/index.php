<div class="game-catalog-page">
    <section class="game-hero mb-4">
        <div class="game-hero-content">
            <span class="game-eyebrow">
                <i class="bi bi-stars"></i>
                Aprende jugando
            </span>

            <h1>EcoAventuras</h1>

            <p>
                Completa retos, mejora tu puntuación y descubre
                acciones sencillas para cuidar el planeta.
            </p>
        </div>

        <div class="game-hero-planet" aria-hidden="true">
            <span>🌎</span>
            <i class="bi bi-leaf-fill leaf-one"></i>
            <i class="bi bi-recycle leaf-two"></i>
        </div>
    </section>

    <div class="row g-3 mb-4">
        <div class="col-4">
            <article class="game-stat">
                <i class="bi bi-controller"></i>
                <strong><?= (int) $summary['total_partidas'] ?></strong>
                <span>Partidas</span>
            </article>
        </div>

        <div class="col-4">
            <article class="game-stat">
                <i class="bi bi-trophy-fill"></i>
                <strong><?= (int) $summary['mejor_puntaje'] ?></strong>
                <span>Mejor puntaje</span>
            </article>
        </div>

        <div class="col-4">
            <article class="game-stat">
                <i class="bi bi-graph-up-arrow"></i>
                <strong>
                    <?= number_format(
                        (float) $summary['promedio'],
                        0
                    ) ?>%
                </strong>
                <span>Promedio</span>
            </article>
        </div>
    </div>

    <div class="page-header">
        <div>
            <span class="section-label">Zona de juegos</span>
            <h2 class="page-title mt-1">Elige una aventura</h2>
            <p class="page-subtitle">
            Las seis EcoAventuras ya están disponibles
            </p>
        </div>

        <span class="chip">
            <i class="bi bi-lightning-charge-fill"></i>
            6 experiencias educativas
        </span>
    </div>

    <div class="row g-4">
        <?php foreach ($games as $game): ?>
        <div class="col-md-6 col-xxl-4">
            <article class="game-card game-theme-<?= e($game['theme']) ?>">
                <div class="game-card-visual">
                    <div class="game-card-icon">
                        <i class="bi <?= e($game['icon']) ?>"></i>
                    </div>

                    <?php if ($game['available']): ?>
                    <span class="game-availability available">
                        <i class="bi bi-play-circle-fill"></i>
                        Disponible
                    </span>
                    <?php else: ?>
                    <span class="game-availability soon">
                        <i class="bi bi-lock-fill"></i>
                        Próximamente
                    </span>
                    <?php endif; ?>
                </div>

                <div class="game-card-body">
                    <span class="game-difficulty">
                        <i class="bi bi-bar-chart-fill"></i>
                        <?= e($game['difficulty']) ?>
                    </span>

                    <h3><?= e($game['title']) ?></h3>

                    <p><?= e($game['short_description']) ?></p>

                    <div class="game-learning-note">
                        <i class="bi bi-lightbulb-fill"></i>
                        <span><?= e($game['learning']) ?></span>
                    </div>

                    <?php if ($game['available']): ?>
                    <div class="game-card-record">
                        <span>
                            <i class="bi bi-trophy"></i>
                            Récord: <?= (int) $game['best_score'] ?>/100
                        </span>

                        <span>
                            <?= (int) $game['plays'] ?> partidas
                        </span>
                    </div>

                    <div class="d-grid gap-2">
    <a
        class="btn btn-eco game-play-button"
        href="<?= e(url($game['route'])) ?>"
    >
        <i class="bi bi-play-fill"></i>
        Jugar ahora
    </a>

    <?php if (
        $canManage
        && in_array(
            $game['slug'],
           [
                'clasifica',
                'trivia',
                'memorama',
                'limpia-parque',
                'ruta-reciclaje',
                'decisiones',
            ],
            true
        )
    ): ?>
    <a
        class="btn btn-eco-outline"
        href="<?= e(
            url(
                'juegos/editar',
                ['juego' => $game['slug']]
            )
        ) ?>"
    >
        <i class="bi bi-pencil-square me-2"></i>
        Editar contenido
    </a>
    <?php endif; ?>
</div>
                    <?php else: ?>
                    <button class="btn btn-light game-play-button" type="button" disabled>
                        <i class="bi bi-hourglass-split"></i>
                        En desarrollo
                    </button>
                    <?php endif; ?>





<?php if (
    $canManage
    && !$game['available']
    && $game['activity'] !== null
    && in_array(
        $game['slug'],
        [
        'clasifica',
            'trivia',
            'memorama',
            'limpia-parque',
            'ruta-reciclaje',
            'decisiones',
        ],
        true
    )
): ?>
<a
    class="btn btn-eco-outline game-play-button mt-2"
    href="<?= e(
        url(
            'juegos/editar',
            ['juego' => $game['slug']]
        )
    ) ?>"
>
    <i class="bi bi-pencil-square me-2"></i>
    Editar o reactivar
</a>
<?php endif; ?>






                </div>
            </article>
        </div>
        <?php endforeach; ?>
    </div>
</div>