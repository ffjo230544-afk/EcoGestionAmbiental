<?php
$isCoordinator = has_role('coordinador');

$assignedCenterName = (string) (
    auth_user()['nombre_punto_asignado']
    ?? 'Centro asignado'
);
?>

<div class="page-header">
    <div>
        <span class="section-label">
            <?= $canManage
                ? 'Operación'
                : 'Mis servicios' ?>
        </span>

        <h1 class="page-title mt-1">
            <?= $canManage
                ? 'Gestión de citas'
                : 'Mis citas' ?>
        </h1>

        <p class="page-subtitle">
            <?= $canManage
                ? 'Consulta y actualiza las visitas programadas.'
                : 'Consulta el estado de tus visitas programadas.' ?>
        </p>
    </div>

    <?php if (!$canManage): ?>

        <a
            class="btn btn-eco"
            href="<?= e(url('citas/crear')) ?>"
        >
            <i class="bi bi-calendar-plus me-2"></i>
            Solicitar cita
        </a>

    <?php endif; ?>
</div>

<?php if ($isCoordinator): ?>

    <div class="alert alert-light border mb-4">
        <i class="bi bi-building text-eco me-2"></i>

        Estás consultando únicamente las citas de:

        <strong>
            <?= e($assignedCenterName) ?>
        </strong>
    </div>

<?php endif; ?>

<section class="eco-card mb-4">
    <div class="eco-card-body">
        <form
            action="<?= e(url('citas')) ?>"
            method="get"
            class="row g-3 align-items-end"
        >
            <input
                type="hidden"
                name="ruta"
                value="citas"
            >

            <div class="col-lg-4">
                <label
                    for="buscar"
                    class="form-label"
                >
                    Buscar
                </label>

                <input
                    id="buscar"
                    type="search"
                    name="buscar"
                    class="form-control"
                    value="<?= e($search) ?>"
                    placeholder="<?= e(
                        $canManage
                            ? 'Folio, ciudadano o centro'
                            : 'Folio, centro o motivo'
                    ) ?>"
                >
            </div>

            <?php if (has_role('admin')): ?>

                <div class="col-lg-3">
                    <label
                        for="centro"
                        class="form-label"
                    >
                        Centro
                    </label>

                    <select
                        id="centro"
                        name="centro"
                        class="form-select"
                    >
                        <option value="0">
                            Todos los centros
                        </option>

                        <?php foreach (
                            $centers as $center
                        ): ?>

                            <option
                                value="<?= (int) $center[
                                    'id_punto'
                                ] ?>"
                                <?= selected(
                                    $selectedPointId,
                                    $center['id_punto']
                                ) ?>
                            >
                                <?= e($center['nombre']) ?>
                            </option>

                        <?php endforeach; ?>
                    </select>
                </div>

            <?php elseif ($isCoordinator): ?>

                <div class="col-lg-3">
                    <label class="form-label">
                        Centro
                    </label>

                    <div class="form-control bg-light">
                        <?= e($assignedCenterName) ?>
                    </div>
                </div>

            <?php endif; ?>

            <div class="col-lg-2">
                <label
                    for="fecha"
                    class="form-label"
                >
                    Fecha
                </label>

                <input
                    id="fecha"
                    type="date"
                    name="fecha"
                    class="form-control"
                    value="<?= e($appointmentDate) ?>"
                >
            </div>

            <div class="col-lg-2">
                <label
                    for="estado"
                    class="form-label"
                >
                    Estado
                </label>

                <select
                    id="estado"
                    name="estado"
                    class="form-select"
                >
                    <option value="">
                        Todos
                    </option>

                    <?php foreach (
                        $statusLabels
                        as $value => $label
                    ): ?>

                        <option
                            value="<?= e($value) ?>"
                            <?= selected(
                                $status,
                                $value
                            ) ?>
                        >
                            <?= e($label) ?>
                        </option>

                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-lg d-grid">
                <button
                    type="submit"
                    class="btn btn-eco-outline"
                >
                    <i class="bi bi-search"></i>
                    Buscar
                </button>
            </div>
        </form>
    </div>
</section>

<?php if ($appointments !== []): ?>

    <div class="row g-4">
        <?php foreach (
            $appointments as $appointment
        ): ?>

            <?php
            $allowedTransitions =
                $transitions[
                    $appointment['estado']
                ] ?? [];

            $canCancel = !$canManage
                && in_array(
                    $appointment['estado'],
                    [
                        'pendiente',
                        'confirmada',
                    ],
                    true
                );

            $appointmentDateTime =
                $appointment['fecha_cita']
                . ' '
                . $appointment['hora_cita'];

            $isPast =
                $appointmentDateTime
                < date('Y-m-d H:i:s');
            ?>

            <div class="col-md-6 col-xxl-4">
                <article class="eco-card h-100">
                    <div
                        class="eco-card-body
                               d-flex flex-column h-100"
                    >
                        <div
                            class="d-flex
                                   justify-content-between
                                   align-items-start gap-3"
                        >
                            <div>
                                <div
                                    class="small
                                           text-muted-eco"
                                >
                                    Folio de cita
                                </div>

                                <strong class="fs-5">
                                    #<?= (int) $appointment[
                                        'id_cita'
                                    ] ?>
                                </strong>
                            </div>

                            <span
                                class="status-pill
                                       status-<?= e(
                                           $appointment[
                                               'estado'
                                           ]
                                       ) ?>"
                            >
                                <?= e(
                                    $statusLabels[
                                        $appointment['estado']
                                    ] ?? 'Sin estado'
                                ) ?>
                            </span>
                        </div>

                        <hr>

                        <h2 class="h5 fw-bold">
                            <?= e(
                                $motiveLabels[
                                    $appointment['motivo']
                                ] ?? 'Otro motivo'
                            ) ?>
                        </h2>

                        <?php if ($canManage): ?>

                            <div class="small mb-2">
                                <i
                                    class="bi bi-person
                                           text-eco me-2"
                                ></i>

                                <strong>
                                    <?= e(
                                        $appointment[
                                            'ciudadano'
                                        ]
                                    ) ?>
                                </strong>

                                <div
                                    class="text-muted-eco
                                           ms-4"
                                >
                                    <?= e(
                                        $appointment[
                                            'correo_ciudadano'
                                        ]
                                    ) ?>
                                </div>

                                <?php if (
                                    !empty(
                                        $appointment[
                                            'telefono_ciudadano'
                                        ]
                                    )
                                ): ?>

                                    <div
                                        class="text-muted-eco
                                               ms-4"
                                    >
                                        <?= e(
                                            $appointment[
                                                'telefono_ciudadano'
                                            ]
                                        ) ?>
                                    </div>

                                <?php endif; ?>
                            </div>

                        <?php endif; ?>

                        <div class="small mb-2">
                            <i
                                class="bi bi-building
                                       text-eco me-2"
                            ></i>

                            <strong>Centro:</strong>

                            <?= e(
                                $appointment['centro']
                            ) ?>
                        </div>

                        <div class="small mb-2">
                            <i
                                class="bi bi-calendar3
                                       text-eco me-2"
                            ></i>

                            <strong>Fecha:</strong>

                            <?= e(date_mx(
                                $appointment['fecha_cita']
                            )) ?>
                        </div>

                        <div class="small mb-3">
                            <i
                                class="bi bi-clock
                                       text-eco me-2"
                            ></i>

                            <strong>Hora:</strong>

                            <?= e(substr(
                                $appointment['hora_cita'],
                                0,
                                5
                            )) ?>
                        </div>

                        <?php if ($isPast): ?>

                            <div
                                class="small text-muted-eco
                                       mb-3"
                            >
                                <i class="bi bi-clock-history me-2"></i>
                                La fecha programada ya pasó.
                            </div>

                        <?php endif; ?>

                        <?php if (
                            !empty(
                                $appointment['observaciones']
                            )
                        ): ?>

                            <div
                                class="p-3 rounded-3
                                       bg-light small mb-3"
                            >
                                <strong>Observaciones:</strong>

                                <div
                                    class="text-muted-eco
                                           mt-1"
                                >
                                    <?= e(
                                        $appointment[
                                            'observaciones'
                                        ]
                                    ) ?>
                                </div>
                            </div>

                        <?php endif; ?>

                        <div class="mt-auto">

                            <?php if (
                                $canManage
                                && $allowedTransitions !== []
                            ): ?>

                                <form
                                    action="<?= e(url(
                                        'citas/cambiar-estado'
                                    )) ?>"
                                    method="post"
                                    class="d-flex
                                           flex-column gap-2"
                                >
                                    <?= csrf_field() ?>

                                    <input
                                        type="hidden"
                                        name="id_cita"
                                        value="<?= (int) $appointment[
                                            'id_cita'
                                        ] ?>"
                                    >

                                    <select
                                        name="estado"
                                        class="form-select"
                                        required
                                    >
                                        <option value="">
                                            Selecciona una acción
                                        </option>

                                        <?php foreach (
                                            $allowedTransitions
                                            as $newStatus
                                        ): ?>

                                            <option
                                                value="<?= e(
                                                    $newStatus
                                                ) ?>"
                                            >
                                                Marcar como
                                                <?= e(strtolower(
                                                    $statusLabels[
                                                        $newStatus
                                                    ]
                                                )) ?>
                                            </option>

                                        <?php endforeach; ?>
                                    </select>

                                    <button
                                        type="submit"
                                        class="btn btn-eco"
                                    >
                                        <i
                                            class="bi
                                                   bi-check2-circle
                                                   me-2"
                                        ></i>

                                        Actualizar estado
                                    </button>
                                </form>

                            <?php endif; ?>

                            <?php if ($canCancel): ?>

                                <form
                                    action="<?= e(url(
                                        'citas/cancelar'
                                    )) ?>"
                                    method="post"
                                    data-confirm="¿Deseas cancelar esta cita?"
                                >
                                    <?= csrf_field() ?>

                                    <input
                                        type="hidden"
                                        name="id_cita"
                                        value="<?= (int) $appointment[
                                            'id_cita'
                                        ] ?>"
                                    >

                                    <button
                                        type="submit"
                                        class="btn
                                               btn-outline-danger
                                               w-100"
                                    >
                                        <i
                                            class="bi
                                                   bi-x-circle
                                                   me-2"
                                        ></i>

                                        Cancelar cita
                                    </button>
                                </form>

                            <?php endif; ?>
                        </div>
                    </div>
                </article>
            </div>

        <?php endforeach; ?>
    </div>

<?php else: ?>

    <section class="eco-card">
        <div class="p-5 text-center">
            <i
                class="bi bi-calendar2-x
                       fs-1 text-eco"
            ></i>

            <h2 class="h5 mt-3">
                No se encontraron citas
            </h2>

            <p class="text-muted-eco mb-3">
                <?= $canManage
                    ? 'No existen citas con los filtros seleccionados.'
                    : 'Todavía no has solicitado una cita.' ?>
            </p>

            <?php if (!$canManage): ?>

                <a
                    class="btn btn-eco"
                    href="<?= e(url('citas/crear')) ?>"
                >
                    Solicitar una cita
                </a>

            <?php endif; ?>
        </div>
    </section>

<?php endif; ?>