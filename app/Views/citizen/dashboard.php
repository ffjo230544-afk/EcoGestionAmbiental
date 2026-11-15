<!-- Aquí presento el resumen de servicios personales del ciudadano. -->
<section id="inicio-ciudadano" class="welcome-panel mb-4">
    <div class="position-relative z-1">

        <span class="small text-uppercase opacity-75 fw-semibold">
            Participación ciudadana
        </span>

        <h1 class="h3 fw-bold mt-2 mb-2">
            Bienvenido a EcoGestión Ambiental.
        </h1>

        <p class="mb-0 opacity-75">
            Desde este espacio podré consultar centros,
            aprender sobre reciclaje, cotizar materiales,
            solicitar citas y participar en actividades ambientales.
        </p>
    </div>
</section>





<?php
/*
 | Aquí presento los servicios disponibles
 | y la actividad del ciudadano autenticado.
 */
$metrics = [
    [
        'label' => 'Centros disponibles',
        'value' =>
            $summary['centros_disponibles'],
        'icon' => 'bi-geo-alt-fill',
        'meta' => 'Centros activos',
    ],
    [
        'label' => 'Mis cotizaciones',
        'value' =>
            $summary['cotizaciones_usuario'],
        'icon' => 'bi-cash-stack',
        'meta' => 'Registros realizados',
    ],
    [
        'label' => 'Mis citas próximas',
        'value' =>
            $summary['citas_usuario'],
        'icon' => 'bi-calendar2-check-fill',
        'meta' => 'Pendientes o confirmadas',
    ],
    [
        'label' => 'Puntos ambientales',
        'value' =>
            $summary['puntos_ambientales'],
        'icon' => 'bi-stars',
        'meta' => 'Acumulados en actividades',
    ],
];
?>

<div class="row g-3 mb-4">

    <?php foreach ($metrics as $metric): ?>

        <div class="col-6 col-xl-3">
            <article class="eco-card metric-card">

                <div class="metric-icon">
                    <i class="bi <?= e(
                        $metric['icon']
                    ) ?>"></i>
                </div>

                <div class="metric-value">
                    <?= e($metric['value']) ?>
                </div>

                <div class="metric-label">
                    <?= e($metric['label']) ?>
                </div>

                <div class="metric-meta">
                    <?= e($metric['meta']) ?>
                </div>

            </article>
        </div>

    <?php endforeach; ?>

</div>












<!-- Aquí destaco las acciones principales del ciudadano. -->
<div class="row g-3 mb-4">

    <div class="col-md-4">
        <article class="eco-card eco-card-body h-100">

            <div class="feature-icon">
                <i class="bi bi-calculator-fill"></i>
            </div>

            <h2 class="h5 fw-bold mt-3">
                Cotizar materiales
            </h2>

            <p class="text-muted-eco">
                Conoceré el valor estimado de mis residuos
                por kilogramo o pieza.
            </p>

            <a
                class="btn btn-eco"
                href="<?= e(url('cotizador')) ?>"
            >
                <i class="bi bi-calculator me-2"></i>
                Realizar cotización
            </a>

        </article>
    </div>

    <div class="col-md-4">
        <article class="eco-card eco-card-body h-100">

            <div class="feature-icon">
                <i class="bi bi-calendar-plus-fill"></i>
            </div>

            <h2 class="h5 fw-bold mt-3">
                Solicitar una cita
            </h2>

            <p class="text-muted-eco">
                Seleccionaré un centro, una fecha
                y un horario disponible.
            </p>

            <a
                class="btn btn-eco"
                href="<?= e(url('citas/crear')) ?>"
            >
                <i class="bi bi-calendar-plus me-2"></i>
                Solicitar cita
            </a>

        </article>
    </div>

    <div class="col-md-4">
        <article class="eco-card eco-card-body h-100">

            <div class="feature-icon">
                <i class="bi bi-map-fill"></i>
            </div>

            <h2 class="h5 fw-bold mt-3">
                Encontrar centros
            </h2>

            <p class="text-muted-eco">
                Consultaré ubicaciones, horarios
                y materiales aceptados.
            </p>

            <a
                class="btn btn-eco"
                href="<?= e(url('mapa')) ?>"
            >
                <i class="bi bi-map me-2"></i>
                Abrir mapa
            </a>

        </article>
    </div>

</div>

<div class="page-header">

    <div>
        <h2 class="page-title">
            Servicios y consultas
        </h2>

        <p class="page-subtitle">
            Estas son las funciones que estarán disponibles
            para el ciudadano.
        </p>
    </div>

 
</div>

<!-- Aquí genero las tarjetas utilizando la configuración del menú ciudadano. -->
<div class="row g-3">

    <?php foreach ($modules as $module): ?>

    <div class="col-md-6 col-xl-4" id="<?= e($module['target'] ?? '') ?>">
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
                    Este servicio ya se encuentra disponible
                    dentro del portal ciudadano.
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
                    Este servicio se conectará con su módulo
                    funcional en una etapa posterior.
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
