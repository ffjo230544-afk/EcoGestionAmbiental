<div class="page-header">
    <div>
        <span class="section-label">
            <?= $canManage
                ? 'Operación'
                : 'Mis servicios' ?>
        </span>

        <h1 class="page-title mt-1">
            <?= $canManage
                ? 'Gestión de cotizaciones'
                : 'Mis cotizaciones' ?>
        </h1>

        <p class="page-subtitle">
            <?= $canManage
                ? 'Revisa y actualiza las solicitudes realizadas por los ciudadanos.'
                : 'Consulta el importe y estado de tus estimaciones.' ?>
        </p>
    </div>

    <?php if (!$canManage): ?>
    <a class="btn btn-eco" href="<?= e(url('cotizador')) ?>">
        <i class="bi bi-calculator me-2"></i>
        Nueva cotización
    </a>
    <?php endif; ?>
</div>

<section class="eco-card mb-4">
    <div class="eco-card-body">
        <form action="<?= e(url('cotizaciones')) ?>" method="get" class="row g-3 align-items-end">
            <input type="hidden" name="ruta" value="cotizaciones">

            <div class="col-md-7">
                <label for="buscar" class="form-label">
                    Buscar
                </label>

                <input id="buscar" type="search" name="buscar" class="form-control" value="<?= e($search) ?>"
                    placeholder="<?= e(
                        $canManage
                            ? 'Folio, ciudadano, material o centro'
                            : 'Folio, material o centro'
                    ) ?>">
            </div>

            <div class="col-md-3">
                <label for="estado" class="form-label">
                    Estado
                </label>

                <select id="estado" name="estado" class="form-select">
                    <option value="">
                        Todos los estados
                    </option>

                    <?php foreach (
                        $statusLabels as $value => $label
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

            <div class="col-md-2 d-grid">
                <button type="submit" class="btn btn-eco-outline">
                    <i class="bi bi-search me-1"></i>
                    Buscar
                </button>
            </div>
        </form>
    </div>
</section>

<?php if ($quotes !== []): ?>

<div class="row g-4">
    <?php foreach ($quotes as $quote): ?>

    <?php
            $isWeight = $quote['peso_kg'] !== null;

            $quantity = $isWeight
                ? $quote['peso_kg'] . ' kg'
                : $quote['cantidad_piezas']
                    . ' pieza(s)';

            $allowedTransitions =
                $transitions[$quote['estado']] ?? [];

            $canCancel = !$canManage
                && in_array(
                    $quote['estado'],
                    ['pendiente', 'revisada'],
                    true
                );
            ?>

    <div class="col-md-6 col-xxl-4">
        <article class="eco-card h-100">
            <div class="eco-card-body
                               d-flex flex-column h-100">
                <div class="d-flex
                                   justify-content-between
                                   align-items-start gap-3">
                    <div>
                        <div class="small text-muted-eco">
                            Folio
                        </div>

                        <strong class="fs-5">
                            #<?= (int) $quote[
                                        'id_cotizacion'
                                    ] ?>
                        </strong>
                    </div>

                    <span class="status-pill
                                       status-<?= e(
                                           $quote['estado']
                                       ) ?>">
                        <?= e(
                                    $statusLabels[
                                        $quote['estado']
                                    ] ?? 'Sin estado'
                                ) ?>
                    </span>
                </div>

                <hr>

                <h2 class="h5 fw-bold">
                    <?= e($quote['categoria']) ?>
                </h2>

                <?php if ($canManage): ?>
                <div class="small mb-2">
                    <i class="bi bi-person
                                           text-eco me-2"></i>

                    <strong>
                        <?= e(
                                        $quote['ciudadano']
                                    ) ?>
                    </strong>

                    <div class="text-muted-eco ms-4">
                        <?= e(
                                        $quote[
                                            'correo_ciudadano'
                                        ]
                                    ) ?>
                    </div>
                </div>
                <?php endif; ?>

                <div class="small mb-2">
                    <i class="bi bi-box-seam
                                       text-eco me-2"></i>

                    <strong>Cantidad:</strong>
                    <?= e($quantity) ?>
                </div>

                <div class="small mb-2">
                    <i class="bi bi-tag
                                       text-eco me-2"></i>

                    <strong>Precio unitario:</strong>
                    <?= e(money(
                                $quote['precio_unitario']
                            )) ?>
                </div>

                <div class="small mb-2">
                    <i class="bi bi-building
                                       text-eco me-2"></i>

                    <strong>Centro:</strong>

                    <?= e(
                                $quote['centro']
                                ?: 'Cotización general'
                            ) ?>
                </div>

                <div class="small mb-3">
                    <i class="bi bi-calendar3
                                       text-eco me-2"></i>

                    <?= e(date_mx(
                                $quote['fecha_cotizacion'],
                                'd/m/Y H:i'
                            )) ?>
                </div>

                <?php if (
                            !empty($quote['observaciones'])
                        ): ?>
                <div class="p-3 rounded-3
                                       bg-light small mb-3">
                    <strong>Observaciones:</strong>

                    <div class="text-muted-eco mt-1">
                        <?= e(
                                        $quote['observaciones']
                                    ) ?>
                    </div>
                </div>
                <?php endif; ?>

                <div class="mt-auto">
                    <div class="d-flex
                                       justify-content-between
                                       align-items-end
                                       gap-3 mb-3">
                        <span class="text-muted-eco">
                            Importe estimado
                        </span>

                        <strong class="h4 text-eco mb-0">
                            <?= e(money(
                                        $quote[
                                            'monto_estimado'
                                        ]
                                    )) ?>
                        </strong>
                    </div>

                    <?php if (
                                $canManage
                                && $allowedTransitions !== []
                            ): ?>
                    <form action="<?= e(url(
                                        'cotizaciones/cambiar-estado'
                                    )) ?>" method="post" class="d-flex
                                           flex-column gap-2">
                        <?= csrf_field() ?>

                        <input type="hidden" name="id_cotizacion" value="<?= (int) $quote[
                                            'id_cotizacion'
                                        ] ?>">

                        <select name="estado" class="form-select" required>
                            <option value="">
                                Selecciona una acción
                            </option>

                            <?php foreach (
                                            $allowedTransitions
                                            as $newStatus
                                        ): ?>
                            <option value="<?= e(
                                                    $newStatus
                                                ) ?>">
                                Marcar como
                                <?= e(
                                                    strtolower(
                                                        $statusLabels[
                                                            $newStatus
                                                        ]
                                                    )
                                                ) ?>
                            </option>
                            <?php endforeach; ?>
                        </select>

                        <button type="submit" class="btn btn-eco">
                            <i class="bi bi-check2-circle
                                                   me-2"></i>

                            Actualizar estado
                        </button>
                    </form>
                    <?php endif; ?>

                    <?php if ($canCancel): ?>
                    <form action="<?= e(url(
                                        'cotizaciones/cancelar'
                                    )) ?>" method="post" data-confirm="¿Deseas cancelar esta cotización?">
                        <?= csrf_field() ?>

                        <input type="hidden" name="id_cotizacion" value="<?= (int) $quote[
                                            'id_cotizacion'
                                        ] ?>">

                        <button type="submit" class="btn
                                               btn-outline-danger
                                               w-100">
                            <i class="bi bi-x-circle
                                                   me-2"></i>

                            Cancelar cotización
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
        <i class="bi bi-cash-stack
                       fs-1 text-eco"></i>

        <h2 class="h5 mt-3">
            No se encontraron cotizaciones
        </h2>

        <p class="text-muted-eco mb-3">
            <?= $canManage
                    ? 'Todavía no existen solicitudes con esos filtros.'
                    : 'Todavía no has guardado una cotización.' ?>
        </p>

        <?php if (!$canManage): ?>
        <a class="btn btn-eco" href="<?= e(url('cotizador')) ?>">
            Crear una cotización
        </a>
        <?php endif; ?>
    </div>
</section>

<?php endif; ?>