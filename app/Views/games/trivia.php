<div
    class="game-play-page"
    data-game-root
    data-game-type="trivia"
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
                Responde diez preguntas y revisa las explicaciones al finalizar.
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
            <i class="bi bi-patch-question-fill"></i>
            <span>Pregunta</span>
            <strong>
                <span data-trivia-current>1</span>/<?= count($questions) ?>
            </strong>
        </div>

        <div class="game-toolbar-item">
            <i class="bi bi-trophy-fill"></i>
            <span>Tu récord</span>
            <strong><?= (int) $bestScore ?>/100</strong>
        </div>
    </section>

    <div class="trivia-layout">
        <aside class="trivia-side-card">
            <div class="trivia-mascot" aria-hidden="true">🌿</div>
            <span class="section-label">Reto ambiental</span>
            <h2>¿Cuánto sabes del planeta?</h2>
            <p>
                Lee con calma. Al terminar conocerás la respuesta
                correcta y una explicación de cada tema.
            </p>

            <div class="trivia-progress-label">
                <span>Progreso</span>
                <strong data-trivia-progress-text>10%</strong>
            </div>

            <div class="trivia-progress">
                <span data-trivia-progress style="width: 10%"></span>
            </div>
        </aside>

        <section class="eco-card trivia-question-card">
            <div class="eco-card-body">
                <?php foreach ($questions as $index => $question): ?>
                    <fieldset
                        class="trivia-question<?= $index === 0 ? '' : ' d-none' ?>"
                        data-trivia-question
                        data-question-id="<?= e($question['id']) ?>"
                        data-question-index="<?= (int) $index ?>"
                    >
                        <legend>
                            <span>Pregunta <?= (int) ($index + 1) ?></span>
                            <?= e($question['question']) ?>
                        </legend>

                        <div class="trivia-options">
                            <?php foreach (
                                $question['options'] as $value => $label
                            ): ?>
                                <label class="trivia-option">
                                    <input
                                        type="radio"
                                        name="question_<?= e($question['id']) ?>"
                                        value="<?= e($value) ?>"
                                    >

                                    <span class="trivia-option-letter">
                                        <?= e(strtoupper($value)) ?>
                                    </span>

                                    <span><?= e($label) ?></span>

                                    <i class="bi bi-circle"></i>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </fieldset>
                <?php endforeach; ?>

                <div class="game-inline-message d-none" data-game-message></div>

                <div class="trivia-navigation">
                    <button
                        class="btn btn-eco-outline"
                        type="button"
                        data-trivia-previous
                        disabled
                    >
                        <i class="bi bi-arrow-left me-2"></i>
                        Anterior
                    </button>

                    <button
                        class="btn btn-eco"
                        type="button"
                        data-trivia-next
                    >
                        Siguiente
                        <i class="bi bi-arrow-right ms-2"></i>
                    </button>

                    <button
                        class="btn btn-eco d-none"
                        type="button"
                        data-trivia-submit
                    >
                        <i class="bi bi-check2-circle me-2"></i>
                        Finalizar trivia
                    </button>
                </div>
            </div>
        </section>
    </div>

    <section class="game-result-panel d-none mt-4" data-game-result>
        <div class="game-result-medal" data-result-medal>🌱</div>
        <span class="section-label">Resultado de la EcoTrivia</span>
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