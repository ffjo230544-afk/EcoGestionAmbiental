<?php
/*
 | Aquí recupero el centro seleccionado en los filtros.
 | Para el coordinador siempre será el centro
 | que tiene asignado en su sesión.
 */
$selectedPointId = (int) (
    $pointId
    ?? auth_center_id()
    ?? 0
);

$isCoordinator = has_role('coordinador');

$assignedCenterName = (string) (
    auth_user()['nombre_punto_asignado']
    ?? 'Centro asignado'
);
?>

<div class="page-header">
    <div>
        <span class="section-label">
            Operación
        </span>

        <h1 class="page-title mt-1">
            Tarifas de cotización
        </h1>

        <p class="page-subtitle">
            Administra los precios, límites de peso
            y periodos de vigencia de cada centro.
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

<?php if ($isCoordinator): ?>

    <div class="alert alert-light border mb-4">
        <i class="bi bi-building text-eco me-2"></i>

        Estás administrando únicamente las tarifas de:

        <strong>
            <?= e($assignedCenterName) ?>
        </strong>
    </div>

<?php endif; ?>

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

            <!-- Filtro por centro -->
            <div class="col-md-4">
                <label
                    for="centro"
                    class="form-label"
                >
                    Centro
                </label>

                <?php if ($isCoordinator): ?>

                    <!--
                     | El coordinador no puede consultar
                     | las tarifas de otros centros.
                     -->
                    <input
                        type="hidden"
                        name="centro"
                        value="<?= $selectedPointId ?>"
                    >

                    <div class="form-control bg-light">
                        <i
                            class="bi bi-building
                                   text-eco me-2"
                        ></i>

                        <?= e($assignedCenterName) ?>
                    </div>

                <?php else: ?>

                    <!--
                     | El administrador puede filtrar
                     | las tarifas de cualquier centro.
                     -->
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

                                <?php if (
                                    !empty($center['municipio'])
                                ): ?>
                                    —
                                    <?= e($center['municipio']) ?>
                                <?php endif; ?>
                            </option>

                        <?php endforeach; ?>
                    </select>

                <?php endif; ?>
            </div>

            <!-- Filtro por categoría -->
            <div class="col-md-3">
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

            <!-- Filtro por estado -->
            <div class="col-md-3">
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
                        <?= selected(
                            $status,
                            'activa'
                        ) ?>
                    >
                        Activa
                    </option>

                    <option
                        value="inactiva"
                        <?= selected(
                            $status,
                            'inactiva'
                        ) ?>
                    >
                        Inactiva
                    </option>
                </select>
            </div>

            <!-- Botón de búsqueda -->
            <div class="col-md-2 d-grid">
                <button
                    type="submit"
                    class="btn btn-eco-outline"
                >
                    <i class="bi bi-search me-2"></i>
                    Filtrar
                </button>
            </div>
        </form>
    </div>
</section>

<?php if ($rates !== []): ?>

    <div class="row g-4">
        <?php foreach ($rates as $rate): ?>

            <?php
            /*
             | Aquí determino el estado visual
             | de la tarifa según su vigencia.
             */
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

            /*
             | Si la tarifa está inactiva,
             | preparo el botón para activarla.
             | De lo contrario, permito desactivarla.
             */
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

                        <!-- Centro relacionado con la tarifa -->
                        <div class="mt-3">
                            <div class="small text-muted-eco">
                                Centro de atención
                            </div>

                            <div class="fw-semibold">
                                <i
                                    class="bi bi-building
                                           text-eco me-2"
                                ></i>

                                <?= e(
                                    $rate['centro']
                                    ?? 'Centro no disponible'
                                ) ?>
                            </div>

                            <?php if (
                                !empty($rate['municipio'])
                            ): ?>

                                <div
                                    class="small
                                           text-muted-eco
                                           ms-4"
                                >
                                    <?= e($rate['municipio']) ?>
                                </div>

                            <?php endif; ?>
                        </div>

                        <!-- Precios -->
                        <div class="row g-3 mt-2">
                            <div class="col-6">
                                <div
                                    class="small
                                           text-muted-eco"
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
                                    class="small
                                           text-muted-eco"
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

                        <!-- Límites de peso -->
                        <div class="small mb-2">
                            <i
                                class="bi bi-box-seam
                                       text-eco me-2"
                            ></i>

                            <strong>Límites:</strong>

                            <?= $rate['minimo_kg'] !== null
                                ? e($rate['minimo_kg'])
                                    . ' kg'
                                : 'Sin mínimo' ?>

                            –

                            <?= $rate['maximo_kg'] !== null
                                ? e($rate['maximo_kg'])
                                    . ' kg'
                                : 'Sin máximo' ?>
                        </div>

                        <!-- Inicio de vigencia -->
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

                        <!-- Final de vigencia -->
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

                        <!-- Acciones -->
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
            <i class="bi bi-tags fs-1 text-eco"></i>

            <h2 class="h5 mt-3">
                No se encontraron tarifas
            </h2>

            <p class="text-muted-eco mb-3">
                No existen tarifas con los filtros
                seleccionados o el centro todavía
                no ha registrado ninguna.
            </p>

            <a
                class="btn btn-eco"
                href="<?= e(url('tarifas/crear')) ?>"
            >
                <i class="bi bi-plus-lg me-2"></i>
                Registrar tarifa
            </a>
        </div>
    </section>

<?php endif; ?>