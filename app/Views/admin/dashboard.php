<!-- Aquí presento el resumen visual principal del administrador. -->
<section
    id="inicio-panel"
    class="welcome-panel mb-4"
>
    <div class="position-relative z-1">

        <span class="small text-uppercase opacity-75 fw-semibold">
            Administración general
        </span>

        <h1 class="h3 fw-bold mt-2 mb-2">
            Bienvenido al panel de administración.
        </h1>

        <p class="mb-0 opacity-75">
            Desde esta interfaz controlaré los usuarios, centros,
            residuos, contenido, operaciones, reportes y seguridad
            del sistema.
        </p>
    </div>
</section>





<?php
/*
 | Aquí preparo los indicadores utilizando
 | la información consultada en MySQL.
 */
$metrics = [
    [
        'Usuarios activos',
        $summary['usuarios_activos'],
        'bi-people-fill',
        'Cuentas habilitadas',
    ],
    [
        'Centros registrados',
        $summary['centros_registrados'],
        'bi-geo-alt-fill',
        'Registrados en el sistema',
    ],
    [
        'Cotizaciones pendientes',
        $summary['cotizaciones_pendientes'],
        'bi-cash-coin',
        'Requieren revisión',
    ],
    [
        'Citas programadas',
        $summary['citas_programadas'],
        'bi-calendar2-check-fill',
        'Pendientes o confirmadas',
    ],
];
?>

<div class="row g-3 mb-4">

    <?php foreach (
        $metrics as [$label, $value, $icon, $meta]
    ): ?>

        <div class="col-6 col-xl-3">
            <article class="eco-card metric-card">

                <div class="metric-icon">
                    <i class="bi <?= e($icon) ?>"></i>
                </div>

                <div class="metric-value">
                    <?= e($value) ?>
                </div>

                <div class="metric-label">
                    <?= e($label) ?>
                </div>

                <div class="metric-meta">
                    <?= e($meta) ?>
                </div>

            </article>
        </div>

    <?php endforeach; ?>

</div>




<!--
    Aquí permito consultar la actividad
    de un centro durante un periodo específico.
-->
<section class="eco-card mb-4">
    <div class="eco-card-head">
        <div>
            <h2 class="eco-card-title">
                Consultar actividad por periodo
            </h2>

            <p class="text-muted-eco small mb-0 mt-1">
                Selecciona las fechas y el centro
                que deseas analizar.
            </p>
        </div>

        <i class="bi bi-funnel text-eco fs-4"></i>
    </div>

    <div class="eco-card-body">
        <form
            action="<?= e(url('administrador')) ?>"
            method="get"
            class="row g-3 align-items-end"
        >
            <!--
                En los formularios GET vuelvo a enviar
                la ruta porque la consulta anterior se reemplaza.
            -->
            <input
                type="hidden"
                name="ruta"
                value="administrador"
            >

            <div class="col-xl-3 col-md-6">
                <label
                    for="desde"
                    class="form-label"
                >
                    Fecha inicial
                </label>

                <input
                    id="desde"
                    type="date"
                    name="desde"
                    class="form-control"
                    value="<?= e(
                        $filters['date_from']
                    ) ?>"
                >
            </div>

            <div class="col-xl-3 col-md-6">
                <label
                    for="hasta"
                    class="form-label"
                >
                    Fecha final
                </label>

                <input
                    id="hasta"
                    type="date"
                    name="hasta"
                    class="form-control"
                    value="<?= e(
                        $filters['date_to']
                    ) ?>"
                >
            </div>

            <div class="col-xl-3 col-md-6">
                <label
                    for="centro"
                    class="form-label"
                >
                    Centro de atención
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
                            value="<?= (int)
                                $center['id_punto'] ?>"
                            <?= selected(
                                $filters['point_id'],
                                $center['id_punto']
                            ) ?>
                        >
                            <?= e($center['nombre']) ?>

                            <?php if (
                                !empty($center['municipio'])
                            ): ?>
                                — <?= e(
                                    $center['municipio']
                                ) ?>
                            <?php endif; ?>
                        </option>

                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-xl-3 col-md-6">
                <div class="d-flex gap-2">

                    <button
                        type="submit"
                        class="btn btn-eco flex-grow-1"
                    >
                        <i class="bi bi-search me-2"></i>
                        Consultar
                    </button>

                    <a
                        href="<?= e(
                            url('administrador')
                        ) ?>"
                        class="btn btn-eco-outline"
                        title="Limpiar filtros"
                        aria-label="Limpiar filtros"
                    >
                        <i
                            class="bi
                                   bi-arrow-counterclockwise"
                        ></i>
                    </a>

                </div>
            </div>
        </form>
    </div>
</section>

<div
    class="d-flex justify-content-between
           align-items-center flex-wrap
           gap-2 mb-3"
>
    <div>
        <h2 class="h5 fw-bold mb-1">
            Resultados del periodo
        </h2>

        <p class="text-muted-eco small mb-0">
            <?= e($periodLabel) ?>
            ·
            <?= e($selectedCenterName) ?>
        </p>
    </div>

    <span class="chip">
        <i class="bi bi-bar-chart me-1"></i>
        Resultados filtrados
    </span>
</div>

<?php
/*
 | Aquí preparo las cifras obtenidas
 | mediante los filtros seleccionados.
 */
$periodMetrics = [
    [
        'Cotizaciones del periodo',
        $periodSummary[
            'cotizaciones_periodo'
        ],
        'bi-receipt',
        'Registros encontrados',
    ],
    [
        'Cotizaciones pendientes',
        $periodSummary[
            'cotizaciones_pendientes_periodo'
        ],
        'bi-hourglass-split',
        'Requieren revisión',
    ],
    [
        'Citas del periodo',
        $periodSummary[
            'citas_periodo'
        ],
        'bi-calendar3',
        'Todos los estados',
    ],
    [
        'Citas activas',
        $periodSummary[
            'citas_activas_periodo'
        ],
        'bi-calendar2-check-fill',
        'Pendientes o confirmadas',
    ],
];
?>

<div class="row g-3 mb-4">

    <?php foreach (
        $periodMetrics
        as [$label, $value, $icon, $meta]
    ): ?>

        <div class="col-6 col-xl-3">
            <article
                class="eco-card metric-card h-100"
            >
                <div class="metric-icon">
                    <i
                        class="bi <?= e($icon) ?>"
                    ></i>
                </div>

                <div class="metric-value">
                    <?= e($value) ?>
                </div>

                <div class="metric-label">
                    <?= e($label) ?>
                </div>

                <div class="metric-meta">
                    <?= e($meta) ?>
                </div>
            </article>
        </div>

    <?php endforeach; ?>

</div>





<div class="page-header">

    <div>
        <h2 class="page-title">
            Operaciones disponibles
        </h2>

        <p class="page-subtitle">
            Estas tarjetas representan todos los módulos
            asignados al administrador.
        </p>
    </div>

   
</div>

<!--
    Aquí creo una tarjeta para cada módulo
    y conservo el mismo identificador del menú.
-->
<div class="row g-3">

    <?php foreach ($modules as $module): ?>

        <div
            class="col-md-6 col-xl-4"
            id="<?= e($module['target'] ?? '') ?>"
        >
            <article class="eco-card feature-card">

                <div class="feature-icon">
                    <i class="bi <?= e($module['icon']) ?>"></i>
                </div>

                <span class="section-label d-block mt-4">
                    <?= e($module['section']) ?>
                </span>

                <h3 class="h5 fw-bold mt-2">
                    <?= e($module['label']) ?>
                </h3>

                <?php if (!empty($module['route'])): ?>

                    <p class="text-muted-eco">
                        Este módulo ya se encuentra disponible
                        para el perfil administrador.
                    </p>

                    <a
                        class="btn btn-eco"
                        href="<?= e(menu_url($module)) ?>"
                    >
                        <i class="bi bi-arrow-right-circle me-2"></i>
                        Abrir módulo
                    </a>

                <?php else: ?>

                    <p class="text-muted-eco">
                        La interfaz y las operaciones de este módulo
                        se implementarán en una etapa posterior.
                    </p>

                    <button
                        class="btn btn-light-eco"
                        type="button"
                        disabled
                    >
                        <i class="bi bi-hourglass-split me-2"></i>
                        Próximamente
                    </button>

                <?php endif; ?>

            </article>
        </div>

    <?php endforeach; ?>

</div>
