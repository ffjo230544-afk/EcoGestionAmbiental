<<div class="page-header">
    <div>
        <span class="section-label">
            <?= e(
                $pointTypes[$center['tipo_punto']]
                ?? 'Punto de atención'
            ) ?>
        </span>

        <h1 class="page-title mt-1">
            <?= e($center['nombre']) ?>
        </h1>

        <p class="page-subtitle">
            <?= e(
                $center['descripcion']
                ?: 'Punto de atención ambiental.'
            ) ?>
        </p>
    </div>

    <div class="d-flex flex-wrap gap-2">
        <?php if ($canManage): ?>
            <a
                class="btn btn-eco-outline"
                href="<?= e(url(
                    'centros/materiales',
                    ['id' => $center['id_punto']]
                )) ?>"
            >
                <i class="bi bi-recycle me-2"></i>
                Configurar materiales
            </a>
        <?php endif; ?>

        <a
            class="btn btn-eco-outline"
            href="<?= e(url('centros')) ?>"
        >
            <i class="bi bi-arrow-left me-2"></i>
            Regresar
        </a>
    </div>
</div>

<div class="row g-4">
    <div class="col-xl-7">
        <section class="eco-card">
            <div class="eco-card-head">
                <h2 class="eco-card-title">
                    Información del centro
                </h2>

                <span class="status-pill
                           status-<?= e($center['estado']) ?>">
                    <?= $center['estado'] === 'activo'
                        ? 'Activo'
                        : 'Inactivo' ?>
                </span>
            </div>

            <div class="eco-card-body">
                <div class="row g-4">
                    <div class="col-md-6">
                        <div class="text-muted-eco small">
                            Dirección
                        </div>

                        <strong>
                            <?= e($center['direccion']) ?>
                        </strong>
                    </div>

                    <div class="col-md-6">
                        <div class="text-muted-eco small">
                            Municipio y estado
                        </div>

                        <strong>
                            <?= e(
                                $center['municipio']
                                ?: 'No especificado'
                            ) ?>,
                            <?= e(
                                $center['estado_region']
                                ?: 'Morelos'
                            ) ?>
                        </strong>
                    </div>

                    <div class="col-md-6">
                        <div class="text-muted-eco small">
                            Código postal
                        </div>

                        <strong>
                            <?= e(
                                $center['codigo_postal']
                                ?: 'No disponible'
                            ) ?>
                        </strong>
                    </div>

                    <div class="col-md-6">
                        <div class="text-muted-eco small">
                            Tipo de servicio
                        </div>

                        <strong>
                            <?= e(
                                $serviceTypes[
                                    $center['tipo_servicio']
                                ] ?? 'Servicio ambiental'
                            ) ?>
                        </strong>
                    </div>

                    <div class="col-md-6">
                        <div class="text-muted-eco small">
                            Teléfono
                        </div>

                        <strong>
                            <?= e(
                                $center['telefono']
                                ?: 'No disponible'
                            ) ?>
                        </strong>
                    </div>

                    <div class="col-md-6">
                        <div class="text-muted-eco small">
                            Correo
                        </div>

                        <strong>
                            <?= e(
                                $center['correo_contacto']
                                ?: 'No disponible'
                            ) ?>
                        </strong>
                    </div>

                    <?php if (
                        $center['latitud'] !== null
                        && $center['longitud'] !== null
                    ): ?>
                    <div class="col-12">
                        <div class="text-muted-eco small">
                            Coordenadas
                        </div>

                        <strong>
                            <?= e($center['latitud']) ?>,
                            <?= e($center['longitud']) ?>
                        </strong>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </section>


    </div>

    <div class="col-xl-5">
        <section class="eco-card">
            <div class="eco-card-head">
                <h2 class="eco-card-title">
                    Horarios de atención
                </h2>
            </div>

            <div class="eco-card-body">
                <?php if ($schedules !== []): ?>

                <?php foreach ($schedules as $schedule): ?>
                <div class="d-flex justify-content-between
                                   gap-3 py-2 border-bottom">
                    <strong>
                        <?= e(
                                    $days[$schedule['dia_semana']]
                                    ?? ucfirst(
                                        $schedule['dia_semana']
                                    )
                                ) ?>
                    </strong>

                    <span class="text-muted-eco">
                        <?php if (
                                    (int) $schedule['cerrado'] === 1
                                ): ?>
                        Cerrado
                        <?php else: ?>
                        <?= e(
                                        substr(
                                            $schedule['hora_apertura'],
                                            0,
                                            5
                                        )
                                    ) ?>
                        –
                        <?= e(
                                        substr(
                                            $schedule['hora_cierre'],
                                            0,
                                            5
                                        )
                                    ) ?>
                        <?php endif; ?>
                    </span>
                </div>
                <?php endforeach; ?>

                <?php else: ?>

                <p class="text-muted-eco mb-0">
                    Todavía no se han registrado horarios.
                </p>

                <?php endif; ?>
            </div>
        </section>
    </div>

    <div class="col-12">
        <section class="eco-card mt-4">
            <div class="eco-card-head">
                <h2 class="eco-card-title">
                    Materiales aceptados
                </h2>
            </div>

            <div class="eco-card-body">
                <?php if ($categories !== []): ?>

                <div class="d-flex flex-wrap gap-2">
                    <?php foreach (
                            $categories as $category
                        ): ?>
                    <span class="chip">
                        <i class="bi bi-recycle"></i>

                        <?= e($category['nombre']) ?>

                        <?php if (
        (int) $category['acepta_compra'] === 1
    ): ?>
                        · Compra
                        <?php endif; ?>

                        <?php if (
        (int) $category['acepta_recepcion'] === 1
    ): ?>
                        · Recepción
                        <?php endif; ?>
                    </span>
                    <?php endforeach; ?>
                </div>

                <?php foreach ($categories as $category): ?>
                <?php if (
        !empty($category['observaciones'])
    ): ?>
                <div class="small text-muted-eco mt-2">
                    <strong>
                        <?= e($category['nombre']) ?>:
                    </strong>

                    <?= e($category['observaciones']) ?>
                </div>
                <?php endif; ?>
                <?php endforeach; ?>

                <?php else: ?>

                <p class="text-muted-eco mb-0">
                    Todavía no hay categorías relacionadas.
                </p>

                <?php endif; ?>
            </div>
        </section>
    </div>




</div>