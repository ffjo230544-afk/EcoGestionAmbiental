<div
    class="game-play-page decisions-game-page"
    data-game-root
    data-game-type="decisiones"
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
                <?= e($game['short_description']) ?>
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
            <i class="bi bi-house-heart-fill"></i>
            <span>Situación</span>
            <strong>
                <span data-decision-current>1</span>/<?= count($scenarios) ?>
            </strong>
        </div>

        <div class="game-toolbar-item">
            <i class="bi bi-check2-square"></i>
            <span>Respondidas</span>
            <strong>
                <span data-decision-answered>0</span>/<?= count($scenarios) ?>
            </strong>
        </div>

        <div class="game-toolbar-item">
            <i class="bi bi-trophy-fill"></i>
            <span>Tu récord</span>
            <strong><?= (int) $bestScore ?>/100</strong>
        </div>
    </section>

    <div class="decisions-layout" data-decisions-layout>
        <aside class="eco-house-panel">
            <div class="eco-house-sky" aria-hidden="true">
                <span>☀️</span>
                <i class="bi bi-cloud-fill"></i>
            </div>

            <div class="eco-house" aria-hidden="true">
                <div class="eco-house-roof"></div>
                <div class="eco-house-body">
                    <span class="eco-house-window window-one"></span>
                    <span class="eco-house-window window-two"></span>
                    <span class="eco-house-door"></span>
                    <span class="eco-house-plant">🌿</span>
                </div>
            </div>

            <div class="eco-house-content">
                <span class="game-eyebrow">
                    <i class="bi bi-house-check-fill"></i>
                    Tu EcoCasa
                </span>

                <h2 data-decision-room><?= e($scenarios[0]['room']) ?></h2>
                <p>
                    Cada elección tiene un impacto. No buscamos perfección:
                    buscamos reconocer opciones más responsables.
                </p>

                <div class="decision-progress-label">
                    <span>Recorrido completado</span>
                    <strong data-decision-progress-text>
                        <?= (int) round(100 / count($scenarios)) ?>%
                    </strong>
                </div>

                <div class="decision-progress">
                    <span
                        data-decision-progress
                        style="width: <?= (int) round(100 / count($scenarios)) ?>%"
                    ></span>
                </div>
            </div>
        </aside>

        <section class="eco-card decision-question-card">
            <div class="eco-card-body">
                <?php foreach ($scenarios as $index => $scenario): ?>
                    <fieldset
                        class="decision-question<?= $index === 0 ? '' : ' d-none' ?>"
                        data-decision-question
                        data-scenario-id="<?= e($scenario['id']) ?>"
                        data-room="<?= e($scenario['room']) ?>"
                    >
                        <legend>
                            <span class="decision-room-icon">
                                <i class="bi <?= e($scenario['icon']) ?>"></i>
                            </span>

                            <span>
                                <small>
                                    Situación <?= (int) ($index + 1) ?> ·
                                    <?= e($scenario['room']) ?>
                                </small>
                                <?= e($scenario['question']) ?>
                            </span>
                        </legend>

                        <div class="decision-options">
                            <?php foreach ($scenario['options'] as $optionIndex => $option): ?>
                                <label class="decision-option">
                                    <input
                                        type="radio"
                                        name="scenario_<?= e($scenario['id']) ?>"
                                        value="<?= e($option['id']) ?>"
                                    >

                                    <span class="decision-option-letter">
                                        <?= e(chr(65 + $optionIndex)) ?>
                                    </span>

                                    <span class="decision-option-copy">
                                        <?= e($option['label']) ?>
                                    </span>

                                    <i class="bi bi-circle"></i>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </fieldset>
                <?php endforeach; ?>

                <div class="game-inline-message d-none" data-game-message></div>

                <div class="decision-navigation">
                    <button
                        class="btn btn-eco-outline"
                        type="button"
                        data-decision-previous
                        disabled
                    >
                        <i class="bi bi-arrow-left me-2"></i>
                        Anterior
                    </button>

                    <button
                        class="btn btn-eco"
                        type="button"
                        data-decision-next
                    >
                        Siguiente habitación
                        <i class="bi bi-arrow-right ms-2"></i>
                    </button>

                    <button
                        class="btn btn-eco d-none"
                        type="button"
                        data-decision-submit
                    >
                        <i class="bi bi-house-check-fill me-2"></i>
                        Evaluar mi EcoCasa
                    </button>
                </div>
            </div>
        </section>
    </div>

    <section class="game-result-panel d-none mt-4" data-game-result>
        <div class="game-result-medal" data-result-medal>🌱</div>
        <span class="section-label">Resultado de tu EcoCasa</span>
        <h2 data-result-title>¡Sigue aprendiendo!</h2>

        <div class="game-result-score">
            <strong data-result-score>0</strong>
            <span>/100 puntos de impacto positivo</span>
        </div>

        <p data-result-message></p>

        <div class="game-result-stats">
            <span>
                <i class="bi bi-check-circle-fill"></i>
                <strong data-result-correct>0</strong> mejores decisiones
            </span>
            <span>
                <i class="bi bi-lightbulb-fill"></i>
                <strong data-result-errors>0</strong> oportunidades de mejora
            </span>
        </div>

        <div class="game-feedback-list decision-feedback-list" data-result-feedback></div>

        <div class="d-flex flex-wrap justify-content-center gap-2 mt-4">
            <button class="btn btn-eco" type="button" data-game-restart>
                <i class="bi bi-arrow-repeat me-2"></i>
                Recorrer otra vez
            </button>
            <a class="btn btn-eco-outline" href="<?= e(url('juegos')) ?>">
                Elegir otro juego
            </a>
        </div>
    </section>
</div>