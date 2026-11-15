<div class="page-header">
    <div>
        <span class="section-label">
            Operación
        </span>

        <h1 class="page-title mt-1">
            Tarifas de cotización
        </h1>

        <p class="page-subtitle">
            Administra precios, límites de peso
            y periodos de vigencia.
        </p>
    </div>

    <a
        class="btn btn-eco"
        href="<?= e(url('tarifas/crear')) ?>"
    >
        <i class="bi bi-plus-lg me-2"></i>
        Nueva tarifa
    </a>
</div>

<section class="eco-card mb-4">
    <div class="eco-card-body">
        <form
            action="<?= e(url('tarifas')) ?>"
            method="get"
            class="row g-3 align-items-end"
        >
            <input
                type="hidden"
                name="ruta"
                value="tarifas"
            >

            <div class="col-md-5">
                <label
                    for="categoria"
                    class="form-label"
                >
                    Categoría
                </label>

                <select
                    id="categoria"
                    name="categoria"
                    class="form-select"
                >
                    <option value="0">
                        Todas las categorías
                    </option>

                    <?php foreach (
                        $categories as $category
                    ): ?>
                        <option
                            value="<?= (int) $category[
                                'id_categoria'
                            ] ?>"
                            <?= selected(
                                $categoryId,
                                $category['id_categoria']
                            ) ?>
                        >
                            <?= e($category['nombre']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-md-4">
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
                        Todos los estados
                    </option>

                    <option
                        value="activa"
                        <?= selected($status, 'activa') ?>
                    >
                        Activa
                    </option>

                    <option
                        value="inactiva"
                        <?= selected($status, 'inactiva') ?>
                    >
                        Inactiva
                    </option>
                </select>
            </div>

            <div class="col-md-3 d-grid">
                <button
                    type="submit"
                    class="btn btn-eco-outline"
                >
                    <i class="bi bi-search me-2"></i>
                    Aplicar filtros
                </button>
            </div>
        </form>
    </div>
</section>

<?php if ($rates !== []): ?>

    <div class="row g-4">
        <?php foreach ($rates as $rate): ?>

            <?php
            $today = date('Y-m-d');

            if ($rate['estado'] === 'inactiva') {
                $periodLabel = 'Desactivada';
                $periodClass = 'status-inactiva';
            } elseif (
                $rate['vigencia_inicio'] > $today
            ) {
                $periodLabel = 'Programada';
                $periodClass = 'status-pendiente';
            } elseif (
                $rate['vigencia_fin'] !== null
                && $rate['vigencia_fin'] < $today
            ) {
                $periodLabel = 'Vencida';
                $periodClass = 'status-inactiva';
            } else {
                $periodLabel = 'Vigente';
                $periodClass = 'status-activa';
            }

            $activate =
                $rate['estado'] !== 'activa';

            $newStatus = $activate
                ? 'activa'
                : 'inactiva';
            ?>

            <div class="col-md-6 col-xxl-4">
                <article class="eco-card h-100">
                    <div
                        class="eco-card-body
                               h-100 d-flex flex-column"
                    >
                        <div
                            class="d-flex
                                   justify-content-between
                                   align-items-start gap-3"
                        >
                            <div>
                                <span class="section-label">
                                    Categoría
                                </span>

                                <h2 class="h5 fw-bold mt-1 mb-0">
                                    <?= e($rate['categoria']) ?>
                                </h2>
                            </div>

                            <span
                                class="status-pill
                                       <?= e($periodClass) ?>"
                            >
                                <?= e($periodLabel) ?>
                            </span>
                        </div>

                        <div class="row g-3 mt-2">
                            <div class="col-6">
                                <div
                                    class="small text-muted-eco"
                                >
                                    Precio por kilogramo
                                </div>

                                <strong class="fs-5 text-eco">
                                    <?= e(money(
                                        $rate['precio_por_kg']
                                    )) ?>
                                </strong>
                            </div>

                            <div class="col-6">
                                <div
                                    class="small text-muted-eco"
                                >
                                    Precio por pieza
                                </div>

                                <strong class="fs-5 text-eco">
                                    <?= e(money(
                                        $rate['precio_por_pieza']
                                    )) ?>
                                </strong>
                            </div>
                        </div>

                        <hr>

                        <div class="small mb-2">
                            <i
                                class="bi bi-box-seam
                                       text-eco me-2"
                            ></i>

                            <strong>Límites:</strong>

                            <?= $rate['minimo_kg'] !== null
                                ? e($rate['minimo_kg'])
                                : 'Sin mínimo' ?>

                            –

                            <?= $rate['maximo_kg'] !== null
                                ? e($rate['maximo_kg']) . ' kg'
                                : 'Sin máximo' ?>
                        </div>

                        <div class="small mb-2">
                            <i
                                class="bi bi-calendar-event
                                       text-eco me-2"
                            ></i>

                            <strong>Inicia:</strong>

                            <?= e(date_mx(
                                $rate['vigencia_inicio']
                            )) ?>
                        </div>

                        <div class="small mb-3">
                            <i
                                class="bi bi-calendar-check
                                       text-eco me-2"
                            ></i>

                            <strong>Finaliza:</strong>

                            <?= $rate['vigencia_fin'] !== null
                                ? e(date_mx(
                                    $rate['vigencia_fin']
                                ))
                                : 'Sin fecha final' ?>
                        </div>

                        <div
                            class="d-flex flex-wrap
                                   gap-2 mt-auto"
                        >
                            <a
                                class="btn btn-eco-outline"
                                href="<?= e(url(
                                    'tarifas/editar',
                                    [
                                        'id' =>
                                            $rate['id_regla'],
                                    ]
                                )) ?>"
                            >
                                <i class="bi bi-pencil me-1"></i>
                                Editar
                            </a>

                            <form
                                action="<?= e(url(
                                    'tarifas/cambiar-estado'
                                )) ?>"
                                method="post"
                                class="m-0"
                                data-confirm="<?= e(
                                    $activate
                                        ? '¿Deseas activar esta tarifa?'
                                        : '¿Deseas desactivar esta tarifa?'
                                ) ?>"
                            >
                                <?= csrf_field() ?>

                                <input
                                    type="hidden"
                                    name="id_regla"
                                    value="<?= (int) $rate[
                                        'id_regla'
                                    ] ?>"
                                >

                                <input
                                    type="hidden"
                                    name="estado"
                                    value="<?= e($newStatus) ?>"
                                >

                                <button
                                    type="submit"
                                    class="btn <?= $activate
                                        ? 'btn-outline-success'
                                        : 'btn-outline-danger' ?>"
                                >
                                    <i class="bi <?= $activate
                                        ? 'bi-check-circle'
                                        : 'bi-x-circle' ?>"></i>

                                    <?= $activate
                                        ? 'Activar'
                                        : 'Desactivar' ?>
                                </button>
                            </form>
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
                class="bi bi-tags fs-1 text-eco"
            ></i>

            <h2 class="h5 mt-3">
                No se encontraron tarifas
            </h2>

            <p class="text-muted-eco mb-3">
                Modifica los filtros o registra
                la primera tarifa.
            </p>

            <a
                class="btn btn-eco"
                href="<?= e(url('tarifas/crear')) ?>"
            >
                Registrar tarifa
            </a>
        </div>
    </section>

<?php endif; ?>