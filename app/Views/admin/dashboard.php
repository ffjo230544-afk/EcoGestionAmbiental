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

    <span class="chip">
        <i class="bi bi-tools me-1"></i>
        <?= count($modules) ?> módulos preparados
    </span>
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
