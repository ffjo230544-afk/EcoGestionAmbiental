<div class="page-header">
    <div>
        <span class="section-label">
            Participación ambiental
        </span>

        <h1 class="page-title mt-1">
            Encuestas ambientales
        </h1>

        <p class="page-subtitle">
            Comparte tu opinión y ayuda a mejorar
            los servicios ambientales.
        </p>
    </div>

    <?php if ($canManage): ?>
    <a class="btn btn-eco" href="<?= e(url('encuestas/crear')) ?>">
        <i class="bi bi-plus-lg me-2"></i>
        Nueva encuesta
    </a>
    <?php endif; ?>
</div>

<section class="eco-card mb-4">
    <div class="eco-card-body">
        <form action="<?= e(url('encuestas')) ?>" method="get" class="row g-3 align-items-end">
            <input type="hidden" name="ruta" value="encuestas">

            <div class="<?= $canManage
                ? 'col-lg-7'
                : 'col-lg-10' ?>">
                <label for="buscar" class="form-label">
                    Buscar encuesta
                </label>

                <input id="buscar" type="search" name="buscar" class="form-control" value="<?= e($search) ?>"
                    placeholder="Título, objetivo o tema">
            </div>

            <?php if ($canManage): ?>
            <div class="col-lg-3">
                <label for="estado" class="form-label">
                    Estado
                </label>

                <select id="estado" name="estado" class="form-select">
                    <option value="">
                        Todos los estados
                    </option>

                    <?php foreach (
                            $statuses as $value => $label
                        ): ?>
                    <option value="<?= e($value) ?>" <?= selected(
                                    $status,
                                    $value
                                ) ?>>
                        <?= e($label) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <?php endif; ?>

            <div class="col-lg-2">
                <button class="btn btn-eco w-100" type="submit">
                    <i class="bi bi-search me-2"></i>
                    Buscar
                </button>
            </div>
        </form>
    </div>
</section>

<?php if ($surveys !== []): ?>

<div class="row g-4">
    <?php foreach ($surveys as $survey): ?>

    <?php
            $available = (
                (int) $survey['disponible'] === 1
            );

            $answered = (
                (int) $survey[
                    'respondida_usuario'
                ] === 1
            );
            ?>

    <div class="col-12 col-xl-6">
        <article class="eco-card survey-card h-100">
            <div class="eco-card-body
                               d-flex flex-column h-100">
                <div class="d-flex flex-wrap gap-2
                                   justify-content-between mb-3">
                    <span class="status-pill
                                       status-<?= e(
                                           $survey['estado']
                                       ) ?>">
                        <?= e(
                                    $statuses[
                                        $survey['estado']
                                    ]
                                    ?? ucfirst(
                                        $survey['estado']
                                    )
                                ) ?>
                    </span>

                    <?php if ($answered): ?>
                    <span class="survey-answered-pill">
                        <i class="bi
                                               bi-check-circle-fill"></i>
                        Respondida
                    </span>
                    <?php endif; ?>
                </div>

                <h2 class="h4 fw-bold">
                    <?= e($survey['titulo']) ?>
                </h2>

                <?php if (
                            !empty($survey['objetivo'])
                        ): ?>
                <p class="text-muted-eco mb-3">
                    <?= e(
                                    $survey['objetivo']
                                ) ?>
                </p>
                <?php endif; ?>

                <div class="d-flex flex-wrap gap-3
                                   small text-muted-eco mb-3">
                    <span>
                        <i class="bi
                                           bi-ui-checks-grid me-1"></i>

                        <?= (int) $survey[
                                    'total_preguntas'
                                ] ?>
                        preguntas
                    </span>

                    <?php if ($canManage): ?>
                    <span>
                        <i class="bi
                                               bi-people me-1"></i>

                        <?= (int) $survey[
                                        'total_respuestas'
                                    ] ?>
                        respuestas
                    </span>
                    <?php endif; ?>
                </div>

                <div class="survey-dates mb-4">
                    <span>
                        <i class="bi
                                           bi-calendar-event me-1"></i>

                        Inicio:

                        <strong>
                            <?= $survey['fecha_inicio']
                                        ? e(
                                            date_mx(
                                                $survey[
                                                    'fecha_inicio'
                                                ]
                                            )
                                        )
                                        : 'Sin límite' ?>
                        </strong>
                    </span>

                    <span>
                        <i class="bi
                                           bi-calendar-x me-1"></i>

                        Cierre:

                        <strong>
                            <?= $survey['fecha_cierre']
                                        ? e(
                                            date_mx(
                                                $survey[
                                                    'fecha_cierre'
                                                ]
                                            )
                                        )
                                        : 'Sin límite' ?>
                        </strong>
                    </span>
                </div>

                <div class="d-flex flex-wrap
                                   gap-2 mt-auto">
                    <?php if ($canManage): ?>
                    <a class="btn
                                           btn-eco-outline
                                           btn-sm" href="<?= e(
                                        url(
                                            'encuestas/preguntas',
                                            [
                                                'id' => $survey[
                                                    'id_encuesta'
                                                ],
                                            ]
                                        )
                                    ) ?>">
                        <i class="bi
                                               bi-list-check me-1"></i>
                        Preguntas
                    </a>

                    <a class="btn
                                           btn-eco-outline
                                           btn-sm" href="<?= e(
                                        url(
                                            'encuestas/editar',
                                            [
                                                'id' => $survey[
                                                    'id_encuesta'
                                                ],
                                            ]
                                        )
                                    ) ?>">
                        <i class="bi
                                               bi-pencil me-1"></i>
                        Editar
                    </a>

                    <a class="btn
                                           btn-eco-outline
                                           btn-sm" href="<?= e(
                                        url(
                                            'encuestas/resultados',
                                            [
                                                'id' => $survey[
                                                    'id_encuesta'
                                                ],
                                            ]
                                        )
                                    ) ?>">
                        <i class="bi
                                               bi-bar-chart-fill me-1"></i>
                        Resultados
                    </a>
                    <?php endif; ?>

                    <?php if (
                                $available
                                && !$answered
                            ): ?>
                    <a class="btn btn-eco btn-sm" href="<?= e(
                                        url(
                                            'encuestas/responder',
                                            [
                                                'id' => $survey[
                                                    'id_encuesta'
                                                ],
                                            ]
                                        )
                                    ) ?>">
                        <i class="bi
                                               bi-send-check me-1"></i>
                        Responder
                    </a>

                    <?php elseif (
                                !$canManage
                                && $answered
                            ): ?>

                    <span class="btn btn-light
                                           btn-sm disabled">
                        Gracias por participar
                    </span>

                    <?php endif; ?>
                </div>
            </div>
        </article>
    </div>

    <?php endforeach; ?>
</div>

<?php else: ?>

<section class="eco-card">
    <div class="empty-state">
        <i class="bi bi-ui-checks-grid"></i>

        <h2 class="h5">
            No se encontraron encuestas
        </h2>

        <p>
            <?php if ($canManage): ?>
            Registra una encuesta o modifica
            los filtros de búsqueda.
            <?php else: ?>
            En este momento no hay encuestas
            abiertas para responder.
            <?php endif; ?>
        </p>
    </div>
</section>

<?php endif; ?>