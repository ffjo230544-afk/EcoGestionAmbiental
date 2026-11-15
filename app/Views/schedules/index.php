<div class="page-header">
    <div>
        <h1 class="page-title">
            Horarios de atención
        </h1>

        <p class="page-subtitle">
            Consulta los días y horas de servicio
            de cada centro.
        </p>
    </div>

    <a
        class="btn btn-eco-outline"
        href="<?= e(url('centros')) ?>"
    >
        <i class="bi bi-geo-alt me-2"></i>
        Ver centros
    </a>
</div>

<section class="eco-card mb-4">
    <div class="eco-card-body">
        <form
            action="<?= e(url('horarios')) ?>"
            method="get"
            class="row g-3"
        >
            <input
                type="hidden"
                name="ruta"
                value="horarios"
            >

            <div class="col-md-9">
                <input
                    type="search"
                    name="buscar"
                    value="<?= e($search) ?>"
                    class="form-control"
                    placeholder="Buscar centro, dirección o municipio"
                >
            </div>

            <div class="col-md-3 d-grid">
                <button
                    type="submit"
                    class="btn btn-eco-outline"
                >
                    <i class="bi bi-search me-1"></i>
                    Buscar
                </button>
            </div>
        </form>
    </div>
</section>

<?php if ($centers !== []): ?>

    <div class="row g-4">
        <?php foreach ($centers as $center): ?>

            <?php
            $pointId = (int) $center['id_punto'];

            $centerSchedules = $grouped[$pointId] ?? [];
            ?>

            <div class="col-lg-6 col-xl-4">
                <section class="eco-card h-100">
                    <div class="eco-card-head">
                        <div>
                            <h2 class="eco-card-title">
                                <?= e($center['nombre']) ?>
                            </h2>

                            <div class="text-muted-eco small mt-1">
                                <?= e(
                                    $center['municipio']
                                    ?: 'Municipio no indicado'
                                ) ?>
                            </div>
                        </div>

                        <i class="bi bi-clock text-eco fs-4"></i>
                    </div>

                    <div class="eco-card-body">
                        <?php if (
                            $canManage
                            && $center['estado'] !== 'activo'
                        ): ?>
                            <div class="mb-3">
                                <span
                                    class="status-pill status-inactivo"
                                >
                                    Centro inactivo
                                </span>
                            </div>
                        <?php endif; ?>

                        <?php if ($centerSchedules !== []): ?>

                            <?php foreach (
                                $centerSchedules as $schedule
                            ): ?>
                                <div
                                    class="d-flex justify-content-between
                                           gap-3 py-2 border-bottom"
                                >
                                    <span>
                                        <?= e(
                                            $days[
                                                $schedule['dia_semana']
                                            ] ?? $schedule['dia_semana']
                                        ) ?>
                                    </span>

                                    <strong
                                        class="<?= (int) $schedule[
                                            'cerrado'
                                        ] === 1
                                            ? 'text-danger'
                                            : 'text-eco' ?>"
                                    >
                                        <?php if (
                                            (int) $schedule['cerrado']
                                            === 1
                                        ): ?>
                                            Cerrado
                                        <?php else: ?>
                                            <?= e(
                                                substr(
                                                    $schedule[
                                                        'hora_apertura'
                                                    ],
                                                    0,
                                                    5
                                                )
                                            ) ?>
                                            –
                                            <?= e(
                                                substr(
                                                    $schedule[
                                                        'hora_cierre'
                                                    ],
                                                    0,
                                                    5
                                                )
                                            ) ?>
                                        <?php endif; ?>
                                    </strong>
                                </div>
                            <?php endforeach; ?>

                        <?php else: ?>

                            <p class="text-muted-eco">
                                Este centro todavía no tiene
                                horarios registrados.
                            </p>

                        <?php endif; ?>

                        <div class="d-flex flex-wrap gap-2 mt-4">
                            <a
                                href="<?= e(
                                    url(
                                        'centros/detalle',
                                        ['id' => $pointId]
                                    )
                                ) ?>"
                                class="btn btn-light-eco"
                            >
                                Ver centro
                            </a>

                            <?php if ($canManage): ?>
                                <a
                                    href="<?= e(
                                        url(
                                            'horarios/editar',
                                            ['id' => $pointId]
                                        )
                                    ) ?>"
                                    class="btn btn-eco"
                                >
                                    <i class="bi bi-pencil me-1"></i>
                                    Configurar
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </section>
            </div>

        <?php endforeach; ?>
    </div>

<?php else: ?>

    <section class="eco-card">
        <div class="p-5 text-center">
            <i class="bi bi-clock fs-1 text-eco"></i>

            <h2 class="h5 mt-3">
                No se encontraron centros
            </h2>

            <p class="text-muted-eco mb-0">
                Cambia el criterio de búsqueda.
            </p>
        </div>
    </section>

<?php endif; ?>