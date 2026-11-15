<?php
/*
 | Estos juegos ya tienen un formulario
 | para modificar su contenido educativo.
 */
$editableGames = [
    'clasifica',
    'trivia',
    'memorama',
    'limpia-parque',
    'ruta-reciclaje',
    'decisiones',
];

$totalGames = count($games);
?>

<div class="game-catalog-page">
    <section class="game-hero mb-4">
        <div class="game-hero-content">
            <span class="game-eyebrow">
                <i class="bi bi-stars"></i>
                Aprende jugando
            </span>

            <h1>EcoAventuras</h1>

            <p>
                Completa retos, mejora tu puntuación
                y descubre acciones sencillas para
                cuidar el planeta.
            </p>
        </div>

        <div
            class="game-hero-planet"
            aria-hidden="true"
        >
            <span>🌎</span>

            <i class="bi bi-leaf-fill leaf-one"></i>

            <i class="bi bi-recycle leaf-two"></i>
        </div>
    </section>

    <section class="game-summary-grid mb-4">
        <article class="game-stat">
            <i class="bi bi-controller"></i>

            <div>
                <strong>
                    <?= (int) (
                        $summary['total_partidas']
                        ?? 0
                    ) ?>
                </strong>

                <span>Partidas realizadas</span>
            </div>
        </article>

        <article class="game-stat">
            <i class="bi bi-trophy-fill"></i>

            <div>
                <strong>
                    <?= (int) (
                        $summary['mejor_puntaje']
                        ?? 0
                    ) ?>
                </strong>

                <span>Mejor puntuación</span>
            </div>
        </article>

        <article class="game-stat">
            <i class="bi bi-graph-up-arrow"></i>

            <div>
                <strong>
                    <?= number_format(
                        (float) (
                            $summary['promedio']
                            ?? 0
                        ),
                        0
                    ) ?>%
                </strong>

                <span>Promedio general</span>
            </div>
        </article>
    </section>

    <div class="page-header game-catalog-header">
        <div>
            <span class="section-label">
                Zona de juegos
            </span>

            <h2 class="page-title mt-1">
                Elige una aventura
            </h2>

            <p class="page-subtitle">
                Selecciona una actividad para aprender
                y poner a prueba tus conocimientos ambientales.
            </p>
        </div>

        <span class="game-catalog-count">
            <i class="bi bi-lightning-charge-fill"></i>

            <?= (int) $totalGames ?>

            <?= $totalGames === 1
                ? 'experiencia disponible'
                : 'experiencias disponibles' ?>
        </span>
    </div>

    <?php if ($games !== []): ?>

    <div class="row g-4 game-catalog-grid">
     <?php foreach ($games as $gameIndex => $game): ?>

        <?php
        $canEditGame = $canManage
            && in_array(
                $game['slug'],
                $editableGames,
                true
            );

        $activityExists = $game['activity'] !== null;
        ?>

        <div class="col-12 col-md-6 col-xxl-4 d-flex">
            <article
                class="game-card
                       game-theme-<?= e($game['theme']) ?>
                       <?= !$game['available']
                            ? 'game-card-disabled'
                            : '' ?>"
            >
                <div class="game-card-visual">
                    <div class="game-card-pattern">
                        <span></span>
                        <span></span>
                        <span></span>
                    </div>

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
                        <i class="bi bi-pause-circle-fill"></i>
                        No disponible
                    </span>

                    <?php endif; ?>
                </div>

                <div class="game-card-body">
                    <div class="game-card-meta">
                        <span class="game-difficulty">
                            <i class="bi bi-bar-chart-fill"></i>

                            <?= e($game['difficulty']) ?>
                        </span>

                      <span class="game-card-number">
                        Juego <?= (int) ($gameIndex + 1) ?>
                    </span>
                    </div>

                    <h3>
                        <?= e($game['title']) ?>
                    </h3>

                    <p class="game-card-description">
                        <?= e(
                            $game['short_description']
                        ) ?>
                    </p>

                    <div class="game-learning-note">
                        <i class="bi bi-lightbulb-fill"></i>

                        <div>
                            <strong>¿Qué aprenderás?</strong>

                            <span>
                                <?= e($game['learning']) ?>
                            </span>
                        </div>
                    </div>

                    <?php if ($activityExists): ?>

                    <div class="game-card-record">
                        <span>
                            <i class="bi bi-trophy-fill"></i>

                            Récord:

                            <strong>
                                <?= (int) $game['best_score'] ?>/100
                            </strong>
                        </span>

                        <span>
                            <i class="bi bi-controller"></i>

                            <?= (int) $game['plays'] ?>

                            <?= (int) $game['plays'] === 1
                                ? 'partida'
                                : 'partidas' ?>
                        </span>
                    </div>

                    <?php endif; ?>

                    <div class="game-card-actions">
                        <?php if ($game['available']): ?>

                        <a
                            class="btn btn-eco
                                   game-play-button"
                            href="<?= e(
                                url($game['route'])
                            ) ?>"
                        >
                            <i class="bi bi-play-fill me-1"></i>
                            Jugar ahora
                        </a>

                        <?php elseif (!$canEditGame): ?>

                        <button
                            class="btn btn-light
                                   game-play-button"
                            type="button"
                            disabled
                        >
                            <i class="bi bi-lock-fill me-1"></i>
                            No disponible
                        </button>

                        <?php endif; ?>

                        <?php if ($canEditGame): ?>

                        <a
                            class="btn btn-eco-outline"
                            href="<?= e(
                                url(
                                    'juegos/editar',
                                    [
                                        'juego' =>
                                            $game['slug'],
                                    ]
                                )
                            ) ?>"
                        >
                            <i class="bi bi-pencil-square me-2"></i>

                            <?= $game['available']
                                ? 'Editar contenido'
                                : 'Editar o reactivar' ?>
                        </a>

                        <?php endif; ?>
                    </div>
                </div>
            </article>
        </div>

        <?php endforeach; ?>
    </div>

    <?php else: ?>

    <section class="eco-card">
        <div class="eco-card-body text-center py-5">
            <i class="bi bi-controller fs-1 text-eco"></i>

            <h2 class="h4 mt-3">
                No hay juegos disponibles
            </h2>

            <p class="text-muted-eco mb-0">
                Todavía no se han registrado actividades educativas.
            </p>
        </div>
    </section>

    <?php endif; ?>
</div>