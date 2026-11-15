<div class="page-header">
    <div>
        <span class="section-label">
            Horario semanal
        </span>

        <h1 class="page-title mt-1">
            <?= e($center['nombre']) ?>
        </h1>

        <p class="page-subtitle">
            Configura apertura, cierre y días sin servicio.
        </p>
    </div>

    <a class="btn btn-eco-outline" href="<?= e(url('horarios')) ?>">
        <i class="bi bi-arrow-left me-2"></i>
        Regresar
    </a>
</div>

<form action="<?= e(url('horarios/actualizar')) ?>" method="post" class="eco-card">
    <?= csrf_field() ?>

    <input type="hidden" name="id_punto" value="<?= (int) $center['id_punto'] ?>">

    <div class="eco-card-head">
        <div>
            <h2 class="eco-card-title">
                Días de atención
            </h2>

            <div class="small text-muted-eco mt-1">
                Marca “Cerrado” cuando el centro no brinde
                servicio ese día.
            </div>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table eco-table">
            <thead>
                <tr>
                    <th>Día</th>
                    <th>Apertura</th>
                    <th>Cierre</th>
                    <th>Cerrado</th>
                    <th>Observaciones</th>
                </tr>
            </thead>

            <tbody>
                <?php foreach ($days as $day => $label): ?>

                <?php
                    $schedule = $week[$day];

                    $closed = (int) old(
                        'horarios.' . $day . '.cerrado',
                        $schedule['cerrado']
                    ) === 1;

                    /*
                     | old() no interpreta rutas con puntos,
                     | así que tomo los valores actuales directamente.
                     | Si hay error, el controlador regresa al formulario,
                     | pero las horas conservadas siguen en la base.
                     */
                    ?>

                <tr data-schedule-row>
                    <td>
                        <strong><?= e($label) ?></strong>
                    </td>

                    <td>
                        <input type="time" name="horarios[<?= e($day) ?>][hora_apertura]" value="<?= e(
    old(
        'horarios.'
        . $day
        . '.hora_apertura',
        $schedule['hora_apertura']
            ? substr(
                $schedule['hora_apertura'],
                0,
                5
            )
            : ''
    )
) ?>" class="form-control" data-time-field <?= $closed ? 'disabled' : '' ?>>
                    </td>

                    <td>
                        <input type="time" name="horarios[<?= e($day) ?>][hora_cierre]" value="<?= e(
    old(
        'horarios.'
        . $day
        . '.hora_cierre',
        $schedule['hora_cierre']
            ? substr(
                $schedule['hora_cierre'],
                0,
                5
            )
            : ''
    )
) ?>" class="form-control" data-time-field <?= $closed ? 'disabled' : '' ?>>
                    </td>

                    <td>
                        <input type="hidden" name="horarios[<?= e($day) ?>][cerrado]" value="0">

                        <div class="form-check form-switch">
                            <input type="checkbox" name="horarios[<?= e($day) ?>][cerrado]" value="1"
                                class="form-check-input" data-closed-toggle <?= $closed ? 'checked' : '' ?>>
                        </div>
                    </td>

                    <td style="min-width: 220px;">
                        <input type="text" name="horarios[<?= e($day) ?>][observaciones]" value="<?= e(
    old(
        'horarios.'
        . $day
        . '.observaciones',
        $schedule['observaciones'] ?? ''
    )
) ?>" class="form-control" maxlength="150" placeholder="Opcional">
                    </td>
                </tr>

                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div class="eco-card-head justify-content-end">
        <a class="btn btn-eco-outline" href="<?= e(url('horarios')) ?>">
            Cancelar
        </a>

        <button type="submit" class="btn btn-eco">
            <i class="bi bi-check2-circle me-2"></i>
            Guardar horario semanal
        </button>
    </div>
</form>