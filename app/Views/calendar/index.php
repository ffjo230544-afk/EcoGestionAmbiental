<?php
/*
 | Creo el nombre del mes en español
 | sin depender de la configuración
 | regional del servidor.
 */
$monthNames = [
    1 => 'Enero',
    2 => 'Febrero',
    3 => 'Marzo',
    4 => 'Abril',
    5 => 'Mayo',
    6 => 'Junio',
    7 => 'Julio',
    8 => 'Agosto',
    9 => 'Septiembre',
    10 => 'Octubre',
    11 => 'Noviembre',
    12 => 'Diciembre',
];

$monthNumber = (int) $monthDate->format(
    'n'
);

$monthTitle =
    ($monthNames[$monthNumber] ?? '')
    . ' '
    . $monthDate->format('Y');

/*
 | Conservo los filtros cuando el usuario
 | cambia al mes anterior o siguiente.
 */
$navigationParameters = [
    'centro' => $selectedPointId,
    'estado' => $status,
];
?>

<div class="page-header">
    <div>
        <span class="section-label">
            <?= $isCitizen
                ? 'Mis servicios'
                : 'Operación' ?>
        </span>

        <h1 class="page-title mt-1">
            <?= $isCitizen
                ? 'Mi calendario de citas'
                : 'Calendario de citas' ?>
        </h1>

        <p class="page-subtitle">
            <?php if ($isAdmin): ?>

                Consulta el resumen general o selecciona
                un centro para revisar su agenda.

            <?php elseif ($isCoordinator): ?>

                Consulta todas las citas programadas
                para tu centro asignado.

            <?php else: ?>

                Consulta tus visitas programadas
                y el estado de cada cita.

            <?php endif; ?>
        </p>
    </div>

    <a
        class="btn btn-eco-outline"
        href="<?= e(url('citas')) ?>"
    >
        <i class="bi bi-list-ul me-2"></i>

        <?= $isCitizen
            ? 'Mis citas'
            : 'Gestión de citas' ?>
    </a>
</div>

<?php if ($isCoordinator): ?>

    <div class="alert alert-light border mb-4">
        <i class="bi bi-building text-eco me-2"></i>

        Calendario del centro:

        <strong>
            <?= e($selectedCenterName) ?>
        </strong>
    </div>

<?php elseif (
    $isAdmin
    && $summaryMode
): ?>

    <div class="alert alert-light border mb-4">
        <i
            class="bi bi-calendar3
                   text-eco me-2"
        ></i>

        Estás consultando el resumen de todos los centros.
        Selecciona un centro para mostrar cada cita
        directamente dentro del calendario.
    </div>

<?php elseif (
    $isAdmin
    && $selectedCenterName !== ''
): ?>

    <div class="alert alert-light border mb-4">
        <i class="bi bi-building text-eco me-2"></i>

        Calendario del centro:

        <strong>
            <?= e($selectedCenterName) ?>
        </strong>
    </div>

<?php endif; ?>

<!-- Filtros -->
<section class="eco-card mb-4">
    <div class="eco-card-body">
        <form
            action="<?= e(url('calendario')) ?>"
            method="get"
            class="row g-3 align-items-end"
        >
            <input
                type="hidden"
                name="ruta"
                value="calendario"
            >

            <input
                type="hidden"
                name="mes"
                value="<?= e($month) ?>"
            >

            <?php if ($isAdmin): ?>

                <div class="col-md-5">
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
                                    !empty(
                                        $center['municipio']
                                    )
                                ): ?>
                                    —
                                    <?= e(
                                        $center['municipio']
                                    ) ?>
                                <?php endif; ?>
                            </option>

                        <?php endforeach; ?>
                    </select>
                </div>

            <?php endif; ?>

            <div class="<?= $isAdmin
                ? 'col-md-4'
                : 'col-md-8' ?>"
            >
                <label
                    for="estado"
                    class="form-label"
                >
                    Estado de la cita
                </label>

                <select
                    id="estado"
                    name="estado"
                    class="form-select"
                >
                    <option value="">
                        Todos los estados
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

            <div class="<?= $isAdmin
                ? 'col-md-3'
                : 'col-md-4' ?> d-grid"
            >
                <button
                    type="submit"
                    class="btn btn-eco-outline"
                >
                    <i class="bi bi-funnel me-2"></i>
                    Aplicar filtros
                </button>
            </div>
        </form>
    </div>
</section>

<!-- Calendario -->
<section class="eco-card calendar-card">
    <div class="calendar-toolbar">
        <a
            class="calendar-navigation-button"
            href="<?= e(url(
                'calendario',
                array_merge(
                    $navigationParameters,
                    ['mes' => $previousMonth]
                )
            )) ?>"
            aria-label="Mes anterior"
        >
            <i class="bi bi-chevron-left"></i>
        </a>

        <div class="calendar-current-month">
            <span class="section-label">
                Agenda mensual
            </span>

            <h2 class="h4 fw-bold mb-0">
                <?= e($monthTitle) ?>
            </h2>
        </div>

        <div class="d-flex gap-2">
            <a
                class="btn btn-light-eco
                       calendar-today-button"
                href="<?= e(url(
                    'calendario',
                    array_merge(
                        $navigationParameters,
                        ['mes' => date('Y-m')]
                    )
                )) ?>"
            >
                Hoy
            </a>

            <a
                class="calendar-navigation-button"
                href="<?= e(url(
                    'calendario',
                    array_merge(
                        $navigationParameters,
                        ['mes' => $nextMonth]
                    )
                )) ?>"
                aria-label="Mes siguiente"
            >
                <i class="bi bi-chevron-right"></i>
            </a>
        </div>
    </div>

    <!-- Leyenda -->
    <div class="calendar-legend">
        <span>
            <i class="calendar-dot status-pendiente"></i>
            Pendiente
        </span>

        <span>
            <i class="calendar-dot status-confirmada"></i>
            Confirmada
        </span>

        <span>
            <i class="calendar-dot status-atendida"></i>
            Atendida
        </span>

        <span>
            <i class="calendar-dot status-cancelada"></i>
            Cancelada
        </span>
    </div>

    <!-- Vista mensual para computadora -->
    <div class="calendar-desktop">
        <div class="calendar-weekdays">
            <div>Lunes</div>
            <div>Martes</div>
            <div>Miércoles</div>
            <div>Jueves</div>
            <div>Viernes</div>
            <div>Sábado</div>
            <div>Domingo</div>
        </div>

        <div
            class="calendar-grid"
            data-calendar-grid
        ></div>
    </div>

    <!-- Vista de agenda para teléfono -->
    <div
        class="calendar-mobile-agenda"
        data-calendar-mobile
    ></div>
</section>

<!-- Agenda del día seleccionado -->
<section
    class="eco-card mt-4 calendar-day-agenda"
    data-calendar-agenda
>
    <div class="eco-card-head">
        <div>
            <span class="section-label">
                Agenda del día
            </span>

            <h2
                class="eco-card-title mt-1"
                data-calendar-agenda-title
            >
                Selecciona un día
            </h2>
        </div>

        <span
            class="chip"
            data-calendar-agenda-count
        >
            0 citas
        </span>
    </div>

    <div
        class="eco-card-body"
        data-calendar-agenda-content
    >
        <div class="text-center py-4">
            <i
                class="bi bi-calendar3
                       fs-1 text-eco"
            ></i>

            <p class="text-muted-eco mt-3 mb-0">
                Selecciona un día del calendario
                para consultar todas sus citas.
            </p>
        </div>
    </div>
</section>

<!--
 | Envío los eventos y la configuración
 | del calendario a JavaScript.
-->
<script
    id="calendar-data"
    type="application/json"
>
<?= json_encode(
    [
        'month' => $month,
        'events' => $events,

        'summaryMode' =>
            $summaryMode,

        'canManage' =>
            $canManage,

        'statusLabels' =>
            $statusLabels,

        'motiveLabels' =>
            $motiveLabels,
    ],
    JSON_UNESCAPED_UNICODE
    | JSON_UNESCAPED_SLASHES
    | JSON_HEX_TAG
    | JSON_HEX_AMP
    | JSON_HEX_APOS
    | JSON_HEX_QUOT
) ?>
</script>