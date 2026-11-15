<div class="page-header">
    <div>
        <span class="section-label">
            Diseño del cuestionario
        </span>

        <h1 class="page-title mt-1">
            <?= e($survey['titulo']) ?>
        </h1>

        <p class="page-subtitle">
            Organiza las preguntas que responderán
            los participantes.
        </p>
    </div>

    <div class="d-flex flex-wrap gap-2">
        <?php if (!$locked): ?>
            <a
                class="btn btn-eco"
                href="<?= e(
                    url(
                        'encuestas/preguntas/crear',
                        [
                            'id' => $survey[
                                'id_encuesta'
                            ],
                        ]
                    )
                ) ?>"
            >
                <i class="bi bi-plus-lg me-2"></i>
                Agregar pregunta
            </a>
        <?php endif; ?>

        <a
            class="btn btn-eco-outline"
            href="<?= e(url('encuestas')) ?>"
        >
            <i class="bi bi-arrow-left me-2"></i>
            Regresar
        </a>
    </div>
</div>

<section class="eco-card mb-4">
    <div class="eco-card-body">
        <div class="row g-3 align-items-center">
            <div class="col-md-8">
                <div
                    class="d-flex flex-wrap
                           gap-2 mb-2"
                >
                    <span
                        class="status-pill
                               status-<?= e(
                                   $survey['estado']
                               ) ?>"
                    >
                        <?= e(
                            ucfirst($survey['estado'])
                        ) ?>
                    </span>

                    <span class="chip">
                        <i class="bi bi-list-ol"></i>
                        <?= count($questions) ?>
                        preguntas registradas
                    </span>

                    <span class="chip">
                        <i class="bi bi-people"></i>

                        <?= (int) $survey[
                            'total_respuestas'
                        ] ?>

                        respuestas
                    </span>
                </div>

                <?php if ($locked): ?>
                    <div class="stage-note mt-3">
                        <i class="bi bi-lock-fill"></i>

                        <div>
                            Las preguntas están bloqueadas
                            porque ya existen respuestas.
                            Esto protege los resultados.
                        </div>
                    </div>

                <?php elseif ($questions === []): ?>

                    <div class="stage-note mt-3">
                        <i
                            class="bi
                                   bi-info-circle-fill"
                        ></i>

                        <div>
                            Agrega al menos una pregunta
                            activa antes de publicar.
                        </div>
                    </div>

                <?php endif; ?>
            </div>

            <div class="col-md-4 text-md-end">
                <a
                    class="btn
                           btn-eco-outline
                           btn-sm"
                    href="<?= e(
                        url(
                            'encuestas/editar',
                            [
                                'id' => $survey[
                                    'id_encuesta'
                                ],
                            ]
                        )
                    ) ?>"
                >
                    <i class="bi bi-sliders me-1"></i>
                    Configurar encuesta
                </a>
            </div>
        </div>
    </div>
</section>

<?php if ($questions !== []): ?>

    <div class="d-grid gap-3">
        <?php foreach ($questions as $question): ?>

            <article class="survey-question-card">
                <div
                    class="d-flex flex-column
                           flex-lg-row gap-3
                           justify-content-between"
                >
                    <div class="flex-grow-1">
                        <div
                            class="d-flex flex-wrap
                                   gap-2 mb-2"
                        >
                            <span class="chip">
                                Orden
                                <?= (int) $question[
                                    'orden'
                                ] ?>
                            </span>

                            <span class="chip">
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

                            <?php if (
                                (int) $question[
                                    'obligatoria'
                                ] === 1
                            ): ?>
                                <span class="chip">
                                    Obligatoria
                                </span>
                            <?php endif; ?>

                            <span
                                class="status-pill
                                       status-<?= e(
                                           $question[
                                               'estado'
                                           ]
                                       ) ?>"
                            >
                                <?= $question['estado']
                                    === 'activa'
                                        ? 'Activa'
                                        : 'Inactiva' ?>
                            </span>
                        </div>

                        <h2 class="h5 fw-bold mb-3">
                            <?= e(
                                $question[
                                    'texto_pregunta'
                                ]
                            ) ?>
                        </h2>

                        <?php if (
                            $question['opciones'] !== []
                        ): ?>

                            <div
                                class="d-flex
                                       flex-wrap gap-2"
                            >
                                <?php foreach (
                                    $question['opciones']
                                    as $option
                                ): ?>
                                    <span class="chip">
                                        <?= e(
                                            $option[
                                                'texto_opcion'
                                            ]
                                        ) ?>
                                    </span>
                                <?php endforeach; ?>
                            </div>

                        <?php elseif (
                            $question['tipo_pregunta']
                            === 'escala'
                        ): ?>

                            <span
                                class="small
                                       text-muted-eco"
                            >
                                Valores permitidos:
                                1, 2, 3, 4 y 5.
                            </span>

                        <?php endif; ?>
                    </div>

                    <?php if (!$locked): ?>
                        <div
                            class="d-flex flex-wrap
                                   gap-2 align-self-lg-start"
                        >
                            <a
                                class="btn
                                       btn-eco-outline
                                       btn-sm"
                                href="<?= e(
                                    url(
                                        'encuestas/preguntas/editar',
                                        [
                                            'encuesta' =>
                                                $survey[
                                                    'id_encuesta'
                                                ],

                                            'id' =>
                                                $question[
                                                    'id_pregunta'
                                                ],
                                        ]
                                    )
                                ) ?>"
                            >
                                <i class="bi bi-pencil"></i>
                                Editar
                            </a>

                            <form
                                action="<?= e(
                                    url(
                                        'encuestas/preguntas/cambiar-estado'
                                    )
                                ) ?>"
                                method="post"
                                data-confirm="¿Deseas cambiar el estado de esta pregunta?"
                            >
                                <?= csrf_field() ?>

                                <input
                                    type="hidden"
                                    name="id_encuesta"
                                    value="<?= (int) $survey[
                                        'id_encuesta'
                                    ] ?>"
                                >

                                <input
                                    type="hidden"
                                    name="id_pregunta"
                                    value="<?= (int) $question[
                                        'id_pregunta'
                                    ] ?>"
                                >

                                <input
                                    type="hidden"
                                    name="estado"
                                    value="<?= $question[
                                        'estado'
                                    ] === 'activa'
                                        ? 'inactiva'
                                        : 'activa' ?>"
                                >

                                <button
                                    class="btn
                                           btn-light
                                           btn-sm"
                                    type="submit"
                                >
                                    <i class="bi bi-power"></i>

                                    <?= $question['estado']
                                        === 'activa'
                                            ? 'Desactivar'
                                            : 'Activar' ?>
                                </button>
                            </form>
                        </div>
                    <?php endif; ?>
                </div>
            </article>

        <?php endforeach; ?>
    </div>

<?php else: ?>

    <section class="eco-card">
        <div class="empty-state">
            <i class="bi bi-list-check"></i>

            <h2 class="h5">
                La encuesta todavía no tiene preguntas
            </h2>

            <p>
                Agrega preguntas de selección,
                respuesta corta o escala.
            </p>
        </div>
    </section>

<?php endif; ?>