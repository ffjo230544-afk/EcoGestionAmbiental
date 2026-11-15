<!-- Aquí presento el resumen de las actividades del coordinador. -->
<section
    id="inicio-coordinador"
    class="welcome-panel mb-4"
>
    <div class="position-relative z-1">

        <span class="small text-uppercase opacity-75 fw-semibold">
            Operación ambiental
        </span>

        <h1 class="h3 fw-bold mt-2 mb-2">
            Bienvenido al panel del coordinador.
        </h1>

        <p class="mb-0 opacity-75">
            Desde esta interfaz gestionaré centros, residuos,
            contenido ambiental, cotizaciones, citas,
            encuestas y reportes.
        </p>
    </div>
</section>



<?php
/*
 | Estos indicadores respetan el centro
 | asignado al coordinador autenticado.
 */
$metrics = [
    [
        'Centro activo',
        $summary['centros_activos'],
        'bi-geo-alt-fill',
        $assignedCenterName,
    ],
    [
        'Cotizaciones pendientes',
        $summary['cotizaciones_pendientes'],
        'bi-cash-coin',
        'Únicamente de mi centro',
    ],
    [
        'Citas del día',
        $summary['citas_del_dia'],
        'bi-calendar2-check-fill',
        'Pendientes o confirmadas',
    ],
    [
        'Encuestas publicadas',
        $summary['encuestas_publicadas'],
        'bi-ui-checks-grid',
        'Disponibles para responder',
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





<div class="page-header">

    <div>
        <h2 class="page-title">
            Funciones del coordinador
        </h2>

        <p class="page-subtitle">
            Estos son los módulos operativos disponibles
            para este perfil.
        </p>
    </div>

    <span class="chip">
        <i class="bi bi-tools me-1"></i>
        <?= count($modules) ?> módulos preparados
    </span>
</div>

<!--
    Aquí genero las tarjetas utilizando
    la configuración del menú del coordinador.
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
                        para el perfil coordinador.
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
                        se agregarán posteriormente.
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
