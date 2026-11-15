<div class="page-header">
    <div>
        <span class="section-label">
            Análisis de participación
        </span>

        <h1 class="page-title mt-1">
            Resultados de la encuesta
        </h1>

        <p class="page-subtitle">
            <?= e($survey['titulo']) ?>
        </p>
    </div>

    <div class="d-flex flex-wrap gap-2">
        <a
            class="btn btn-eco-outline"
            href="<?= e(
                url(
                    'encuestas/preguntas',
                    [
                        'id' => $survey[
                            'id_encuesta'
                        ],
                    ]
                )
            ) ?>"
        >
            <i class="bi bi-list-check me-2"></i>
            Preguntas
        </a>

        <a
            class="btn btn-eco-outline"
            href="<?= e(url('encuestas')) ?>"
        >
            <i class="bi bi-arrow-left me-2"></i>
            Regresar
        </a>
    </div>
</div>

<section class="row g-4 mb-4">
    <div class="col-md-4">
        <div class="eco-card h-100">
            <div
                class="eco-card-body text-center"
            >
                <i
                    class="bi bi-people-fill
                           survey-summary-icon"
                ></i>

                <div class="survey-summary-number">
                    <?= (int) $survey[
                        'total_respuestas'
                    ] ?>
                </div>

                <div class="text-muted-eco">
                    Participaciones
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="eco-card h-100">
            <div
                class="eco-card-body text-center"
            >
                <i
                    class="bi bi-ui-checks-grid
                           survey-summary-icon"
                ></i>

                <div class="survey-summary-number">
                    <?= count($questions) ?>
                </div>

                <div class="text-muted-eco">
                    Preguntas
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="eco-card h-100">
            <div
                class="eco-card-body text-center"
            >
                <i
                    class="bi bi-activity
                           survey-summary-icon"
                ></i>

                <div class="mt-2">
                    <span
                        class="status-pill
                               status-<?= e(
                                   $survey['estado']
                               ) ?>"
                    >
                        <?= e(
                            ucfirst(
                                $survey['estado']
                            )
                        ) ?>
                    </span>
                </div>

                <div class="text-muted-eco mt-3">
                    Estado actual
                </div>
            </div>
        </div>
    </div>
</section>

<?php if (
    (int) $survey['total_respuestas'] === 0
): ?>

    <section class="eco-card">
        <div class="empty-state">
            <i class="bi bi-bar-chart"></i>

            <h2 class="h5">
                Todavía no hay resultados
            </h2>

            <p>
                Los datos aparecerán cuando los usuarios
                respondan la encuesta.
            </p>
        </div>
    </section>

<?php else: ?>

    <div class="d-grid gap-4">
        <?php foreach (
            $questions as $index => $question
        ): ?>

            <?php
            $questionId = (int) (
                $question['id_pregunta']
            );

            $result = $results[
                $questionId
            ] ?? [
                'total_contestado' => 0,
            ];

            $answered = (int) (
                $result['total_contestado'] ?? 0
            );
            ?>

            <section class="eco-card">
                <div class="eco-card-head">
                    <div>
                        <span
                            class="small text-muted-eco"
                        >
                            Pregunta <?= $index + 1 ?>
                            ·
                            <?= e(
                                $questionTypes[
                                    $question[
                                        'tipo_pregunta'
                                    ]
                                ]
                                ?? $question[
                                    'tipo_pregunta'
                                ]
                            ) ?>
                        </span>

                        <h2
                            class="eco-card-title mt-1"
                        >
                            <?= e(
                                $question[
                                    'texto_pregunta'
                                ]
                            ) ?>
                        </h2>
                    </div>

                    <span class="chip">
                        <?= $answered ?>
                        respuestas
                    </span>
                </div>

                <div class="eco-card-body">
                    <?php if (
                        $question['tipo_pregunta']
                        === 'opcion_multiple'
                    ): ?>

                        <?php foreach (
                            $result['opciones'] ?? []
                            as $option
                        ): ?>

                            <?php
                            $total = (int) (
                                $option['total']
                            );

                            $percentage = $answered > 0
                                ? round(
                                    (
                                        $total
                                        / $answered
                                    ) * 100,
                                    1
                                )
                                : 0;
                            ?>

                            <div class="survey-result-row">
                                <div
                                    class="d-flex
                                           justify-content-between
                                           gap-3 mb-2"
                                >
                                    <strong>
                                        <?= e(
                                            $option[
                                                'texto_opcion'
                                            ]
                                        ) ?>
                                    </strong>

                                    <span
                                        class="text-muted-eco"
                                    >
                                        <?= $total ?>
                                        ·
                                        <?= e($percentage) ?>%
                                    </span>
                                </div>

                                <div class="eco-progress">
                                    <span
                                        style="width: <?= e(
                                            $percentage
                                        ) ?>%"
                                    ></span>
                                </div>
                            </div>

                        <?php endforeach; ?>

                    <?php elseif (
                        $question['tipo_pregunta']
                        === 'escala'
                    ): ?>

                        <div class="survey-average mb-4">
                            <span>Promedio</span>

                            <strong>
                                <?= $result['promedio'] !== null
                                    ? number_format(
                                        (float) $result[
                                            'promedio'
                                        ],
                                        2
                                    )
                                    : '0.00' ?>
                                / 5
                            </strong>
                        </div>

                        <?php foreach (
                            $result['distribucion'] ?? []
                            as $value => $total
                        ): ?>

                            <?php
                            $percentage = $answered > 0
                                ? round(
                                    (
                                        (int) $total
                                        / $answered
                                    ) * 100,
                                    1
                                )
                                : 0;
                            ?>

                            <div
                                class="dashboard-distribution"
                            >
                                <strong>
                                    Valor <?= (int) $value ?>
                                </strong>

                                <div class="eco-progress">
                                    <span
                                        style="width: <?= e(
                                            $percentage
                                        ) ?>%"
                                    ></span>
                                </div>

                                <span>
                                    <?= (int) $total ?>
                                </span>
                            </div>

                        <?php endforeach; ?>

                    <?php else: ?>

                        <?php if (
                            ($result['textos'] ?? [])
                            !== []
                        ): ?>

                            <div class="d-grid gap-2">
                                <?php foreach (
                                    $result['textos']
                                    as $text
                                ): ?>

                                    <div
                                        class="survey-text-answer"
                                    >
                                        <p class="mb-1">
                                            <?= nl2br(
                                                e(
                                                    $text[
                                                        'respuesta_texto'
                                                    ]
                                                )
                                            ) ?>
                                        </p>

                                        <small
                                            class="text-muted-eco"
                                        >
                                            <?= e(
                                                date_mx(
                                                    $text[
                                                        'fecha_respuesta'
                                                    ],
                                                    'd/m/Y H:i'
                                                )
                                            ) ?>
                                        </small>
                                    </div>

                                <?php endforeach; ?>
                            </div>

                        <?php else: ?>

                            <p
                                class="text-muted-eco mb-0"
                            >
                                Esta pregunta no recibió
                                respuestas de texto.
                            </p>

                        <?php endif; ?>

                    <?php endif; ?>
                </div>
            </section>

        <?php endforeach; ?>
    </div>

<?php endif; ?>