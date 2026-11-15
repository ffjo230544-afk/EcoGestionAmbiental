<?php
$oldPointId = (int) old(
    'id_punto',
    0
);

$oldDate = (string) old(
    'fecha_cita',
    ''
);

$oldTime = (string) old(
    'hora_cita',
    ''
);

$maximumDate = date(
    'Y-m-d',
    strtotime('+90 days')
);
?>

<div class="page-header">
    <div>
        <span class="section-label">
            Mis servicios
        </span>

        <h1 class="page-title mt-1">
            Solicitar una cita
        </h1>

        <p class="page-subtitle">
            Selecciona un centro, una fecha
            y uno de sus horarios disponibles.
        </p>
    </div>

    <a
        class="btn btn-eco-outline"
        href="<?= e(url('citas')) ?>"
    >
        <i class="bi bi-clock-history me-2"></i>
        Mis citas
    </a>
</div>

<?php if ($centers === []): ?>

    <section class="eco-card">
        <div class="p-5 text-center">
            <i
                class="bi bi-calendar-x
                       fs-1 text-eco"
            ></i>

            <h2 class="h5 mt-3">
                No hay centros disponibles
            </h2>

            <p class="text-muted-eco mb-0">
                Actualmente no existen centros activos
                con horarios configurados para recibir citas.
            </p>
        </div>
    </section>

<?php else: ?>

    <form
        action="<?= e(url('citas/guardar')) ?>"
        method="post"
        class="row g-4"
        data-appointment-form
    >
        <?= csrf_field() ?>

        <div class="col-xl-7">
            <section class="eco-card h-100">
                <div class="eco-card-head">
                    <h2 class="eco-card-title">
                        Datos de la visita
                    </h2>
                </div>

                <div class="eco-card-body">
                    <div class="row g-4">

                        <div class="col-12">
                            <label
                                for="id_punto"
                                class="form-label"
                            >
                                Centro de atención *
                            </label>

                            <select
                                id="id_punto"
                                name="id_punto"
                                class="form-select"
                                data-appointment-center
                                required
                            >
                                <option value="">
                                    Selecciona un centro
                                </option>

                                <?php foreach (
                                    $centers as $center
                                ): ?>

                                    <option
                                        value="<?= (int) $center[
                                            'id_punto'
                                        ] ?>"
                                        <?= selected(
                                            $oldPointId,
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
                        </div>

                        <div class="col-md-6">
                            <label
                                for="fecha_cita"
                                class="form-label"
                            >
                                Fecha *
                            </label>

                            <input
                                id="fecha_cita"
                                type="date"
                                name="fecha_cita"
                                class="form-control"
                                min="<?= e(date('Y-m-d')) ?>"
                                max="<?= e($maximumDate) ?>"
                                value="<?= e($oldDate) ?>"
                                data-appointment-date
                                required
                            >

                            <div class="form-text">
                                Puedes programar con hasta
                                90 días de anticipación.
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label
                                for="hora_cita"
                                class="form-label"
                            >
                                Horario disponible *
                            </label>

                            <select
                                id="hora_cita"
                                name="hora_cita"
                                class="form-select"
                                data-appointment-time
                                data-old-time="<?= e($oldTime) ?>"
                                disabled
                                required
                            >
                                <option value="">
                                    Selecciona centro y fecha
                                </option>
                            </select>
                        </div>

                        <div class="col-12">
                            <label
                                for="motivo"
                                class="form-label"
                            >
                                Motivo de la visita *
                            </label>

                            <select
                                id="motivo"
                                name="motivo"
                                class="form-select"
                                required
                            >
                                <option value="">
                                    Selecciona un motivo
                                </option>

                                <?php foreach (
                                    $motiveLabels
                                    as $value => $label
                                ): ?>

                                    <option
                                        value="<?= e($value) ?>"
                                        <?= selected(
                                            old('motivo'),
                                            $value
                                        ) ?>
                                    >
                                        <?= e($label) ?>
                                    </option>

                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-12">
                            <label
                                for="observaciones"
                                class="form-label"
                            >
                                Observaciones
                            </label>

                            <textarea
                                id="observaciones"
                                name="observaciones"
                                class="form-control"
                                rows="4"
                                maxlength="300"
                                placeholder="Describe los materiales o la atención que necesitas."
                            ><?= e(old(
                                'observaciones'
                            )) ?></textarea>

                            <div class="form-text">
                                Máximo 300 caracteres.
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>

        <div class="col-xl-5">
            <section class="eco-card">
                <div class="eco-card-head">
                    <h2 class="eco-card-title">
                        Disponibilidad
                    </h2>
                </div>

                <div class="eco-card-body">
                    <div
                        class="p-4 rounded-4
                               bg-light border"
                    >
                        <div
                            class="feature-icon mb-3"
                        >
                            <i
                                class="bi
                                       bi-calendar2-check"
                            ></i>
                        </div>

                        <h3
                            class="h5 fw-bold"
                            data-appointment-summary-title
                        >
                            Selecciona un centro
                        </h3>

                        <p
                            class="text-muted-eco mb-0"
                            data-appointment-summary
                        >
                            Aquí aparecerá el horario de
                            atención y los espacios disponibles.
                        </p>
                    </div>

                    <div
                        class="alert alert-light
                               border mt-4 mb-0"
                    >
                        <i
                            class="bi bi-info-circle
                                   text-eco me-2"
                        ></i>

                        Cada cita tiene una duración estimada
                        de 30 minutos y necesita confirmación
                        del centro.
                    </div>
                </div>

                <div
                    class="eco-card-head
                           justify-content-end"
                >
                    <button
                        type="submit"
                        class="btn btn-eco"
                    >
                        <i
                            class="bi
                                   bi-calendar-plus me-2"
                        ></i>

                        Solicitar cita
                    </button>
                </div>
            </section>
        </div>
    </form>

    <script
        id="appointment-schedules"
        type="application/json"
    >
    <?= json_encode(
        $scheduleData,
        JSON_UNESCAPED_UNICODE
        | JSON_UNESCAPED_SLASHES
        | JSON_HEX_TAG
        | JSON_HEX_AMP
        | JSON_HEX_APOS
        | JSON_HEX_QUOT
    ) ?>
    </script>

    <script
        id="appointment-occupied"
        type="application/json"
    >
    <?= json_encode(
        $occupiedData,
        JSON_UNESCAPED_UNICODE
        | JSON_UNESCAPED_SLASHES
        | JSON_HEX_TAG
        | JSON_HEX_AMP
        | JSON_HEX_APOS
        | JSON_HEX_QUOT
    ) ?>
    </script>

<?php endif; ?>