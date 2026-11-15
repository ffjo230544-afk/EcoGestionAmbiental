<div class="page-header">
    <div>
        <span class="section-label">
            Encuesta ambiental
        </span>

        <h1 class="page-title mt-1">
            <?= e($survey['titulo']) ?>
        </h1>

        <?php if (
            !empty($survey['objetivo'])
        ): ?>
            <p class="page-subtitle">
                <?= e($survey['objetivo']) ?>
            </p>
        <?php endif; ?>
    </div>

    <a
        class="btn btn-eco-outline"
        href="<?= e(url('encuestas')) ?>"
    >
        <i class="bi bi-arrow-left me-2"></i>
        Regresar
    </a>
</div>

<form
    action="<?= e(url('encuestas/enviar')) ?>"
    method="post"
    class="eco-card"
>
    <?= csrf_field() ?>

    <input
        type="hidden"
        name="id_encuesta"
        value="<?= (int) $survey[
            'id_encuesta'
        ] ?>"
    >

    <div class="eco-card-body">
        <?php if (
            !empty($survey['descripcion'])
        ): ?>
            <div class="survey-introduction mb-4">
                <?= nl2br(
                    e($survey['descripcion'])
                ) ?>
            </div>
        <?php endif; ?>

        <div
            class="d-flex flex-wrap gap-3
                   small text-muted-eco mb-4"
        >
            <span>
                <i
                    class="bi
                           bi-ui-checks-grid me-1"
                ></i>

                <?= count($questions) ?>
                preguntas
            </span>

            <span>
                <i
                    class="bi
                           bi-shield-check me-1"
                ></i>

                Una participación por usuario
            </span>

            <?php if (
                !empty($survey['fecha_cierre'])
            ): ?>
                <span>
                    <i
                        class="bi
                               bi-calendar-x me-1"
                    ></i>

                    Disponible hasta

                    <?= e(
                        date_mx(
                            $survey['fecha_cierre']
                        )
                    ) ?>
                </span>
            <?php endif; ?>
        </div>

        <div class="d-grid gap-4">
            <?php foreach (
                $questions as $index => $question
            ): ?>

                <?php
                $questionId = (int) (
                    $question['id_pregunta']
                );

                $previous = old(
                    'respuestas.' . $questionId,
                    ''
                );
                ?>

                <section class="survey-question-card">
                    <div
                        class="d-flex gap-2
                               align-items-start mb-3"
                    >
                        <span class="survey-number">
                            <?= $index + 1 ?>
                        </span>

                        <h2 class="h5 fw-bold mb-0">
                            <?= e(
                                $question[
                                    'texto_pregunta'
                                ]
                            ) ?>

                            <?php if (
                                (int) $question[
                                    'obligatoria'
                                ] === 1
                            ): ?>
                                <span
                                    class="text-danger"
                                    title="Obligatoria"
                                >
                                    *
                                </span>
                            <?php endif; ?>
                        </h2>
                    </div>

                    <?php if (
                        $question['tipo_pregunta']
                        === 'respuesta_corta'
                    ): ?>

                        <textarea
                            name="respuestas[<?= $questionId ?>]"
                            class="form-control"
                            maxlength="500"
                            rows="3"
                            placeholder="Escribe tu respuesta"
                            <?= (int) $question[
                                'obligatoria'
                            ] === 1
                                ? 'required'
                                : '' ?>
                        ><?= e($previous) ?></textarea>

                    <?php elseif (
                        $question['tipo_pregunta']
                        === 'escala'
                    ): ?>

                        <div
                            class="d-flex
                                   flex-wrap gap-2"
                        >
                            <?php for (
                                $value = 1;
                                $value <= 5;
                                $value++
                            ): ?>

                                <label
                                    class="eco-scale-choice"
                                >
                                    <input
                                        type="radio"
                                        name="respuestas[<?= $questionId ?>]"
                                        value="<?= $value ?>"
                                        <?= checked(
                                            $previous,
                                            $value
                                        ) ?>
                                        <?= (
                                            (int) $question[
                                                'obligatoria'
                                            ] === 1
                                            && $value === 1
                                        )
                                            ? 'required'
                                            : '' ?>
                                    >

                                    <?= $value ?>
                                </label>

                            <?php endfor; ?>
                        </div>

                        <div
                            class="d-flex
                                   justify-content-between
                                   small text-muted-eco
                                   mt-2 survey-scale-labels"
                        >
                            <span>
                                1 · Valor más bajo
                            </span>

                            <span>
                                5 · Valor más alto
                            </span>
                        </div>

                    <?php else: ?>

                        <div class="d-grid gap-2">
                            <?php foreach (
                                $question['opciones']
                                as $optionIndex => $option
                            ): ?>

                                <label
                                    class="eco-answer-choice"
                                >
                                    <input
                                        type="radio"
                                        name="respuestas[<?= $questionId ?>]"
                                        value="<?= (int) $option[
                                            'id_opcion'
                                        ] ?>"
                                        <?= checked(
                                            $previous,
                                            $option[
                                                'id_opcion'
                                            ]
                                        ) ?>
                                        <?= (
                                            (int) $question[
                                                'obligatoria'
                                            ] === 1
                                            && $optionIndex === 0
                                        )
                                            ? 'required'
                                            : '' ?>
                                    >

                                    <span>
                                        <?= e(
                                            $option[
                                                'texto_opcion'
                                            ]
                                        ) ?>
                                    </span>
                                </label>

                            <?php endforeach; ?>
                        </div>

                    <?php endif; ?>
                </section>

            <?php endforeach; ?>
        </div>
    </div>

    <div class="eco-card-head survey-submit-bar">
        <span class="small text-muted-eco">
            Los campos marcados con * son obligatorios.
        </span>

        <button
            class="btn btn-eco"
            type="submit"
        >
            <i class="bi bi-send-check me-2"></i>
            Enviar respuestas
        </button>
    </div>
</form>