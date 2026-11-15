<div class="page-header">
    <div>
        <span class="section-label">
            Administración de encuestas
        </span>

        <h1 class="page-title mt-1">
            <?= $editing
                ? 'Editar encuesta'
                : 'Crear encuesta' ?>
        </h1>

        <p class="page-subtitle">
            Define el propósito, la vigencia
            y el estado de participación.
        </p>
    </div>

    <a
        class="btn btn-eco-outline"
        href="<?= e(url('encuestas')) ?>"
    >
        <i class="bi bi-arrow-left me-2"></i>
        Regresar
    </a>
</div>

<form
    action="<?= e(
        url(
            $editing
                ? 'encuestas/actualizar'
                : 'encuestas/guardar'
        )
    ) ?>"
    method="post"
    class="eco-card"
>
    <?= csrf_field() ?>

    <?php if ($editing): ?>
        <input
            type="hidden"
            name="id_encuesta"
            value="<?= (int) $survey[
                'id_encuesta'
            ] ?>"
        >
    <?php endif; ?>

    <div class="eco-card-body">
        <?php if (!$editing): ?>

            <div class="stage-note mb-4">
                <i class="bi bi-info-circle-fill"></i>

                <div>
                    <strong>
                        Primero se creará como borrador.
                    </strong>

                    Después podrás agregar preguntas
                    y publicarla.
                </div>
            </div>

        <?php elseif (
            (int) (
                $survey['total_respuestas'] ?? 0
            ) > 0
        ): ?>

            <div class="stage-note mb-4">
                <i class="bi bi-lock-fill"></i>

                <div>
                    Esta encuesta ya tiene respuestas.
                    Puedes cerrarla o inactivarla, pero
                    sus preguntas no podrán modificarse.
                </div>
            </div>

        <?php endif; ?>

        <div class="row g-4">
            <div class="col-12">
                <label
                    for="titulo"
                    class="form-label"
                >
                    Título de la encuesta *
                </label>

                <input
                    id="titulo"
                    name="titulo"
                    class="form-control"
                    minlength="5"
                    maxlength="150"
                    value="<?= e(
                        old(
                            'titulo',
                            $survey['titulo'] ?? ''
                        )
                    ) ?>"
                    required
                    autofocus
                >
            </div>

            <div class="col-12">
                <label
                    for="objetivo"
                    class="form-label"
                >
                    Objetivo
                </label>

                <input
                    id="objetivo"
                    name="objetivo"
                    class="form-control"
                    maxlength="255"
                    value="<?= e(
                        old(
                            'objetivo',
                            $survey['objetivo'] ?? ''
                        )
                    ) ?>"
                    placeholder="Ejemplo: conocer los hábitos de reciclaje"
                >
            </div>

            <div class="col-12">
                <label
                    for="descripcion"
                    class="form-label"
                >
                    Descripción para los participantes
                </label>

                <textarea
                    id="descripcion"
                    name="descripcion"
                    class="form-control"
                    maxlength="3000"
                    rows="5"
                    placeholder="Explica para qué se utilizarán las respuestas."
                ><?= e(
                    old(
                        'descripcion',
                        $survey['descripcion'] ?? ''
                    )
                ) ?></textarea>
            </div>

            <div class="col-md-4">
                <label
                    for="fecha_inicio"
                    class="form-label"
                >
                    Fecha de inicio
                </label>

                <input
                    id="fecha_inicio"
                    type="date"
                    name="fecha_inicio"
                    class="form-control"
                    value="<?= e(
                        old(
                            'fecha_inicio',
                            $survey[
                                'fecha_inicio'
                            ] ?? ''
                        )
                    ) ?>"
                >

                <div class="form-text">
                    Vacía significa sin límite inicial.
                </div>
            </div>

            <div class="col-md-4">
                <label
                    for="fecha_cierre"
                    class="form-label"
                >
                    Fecha de cierre
                </label>

                <input
                    id="fecha_cierre"
                    type="date"
                    name="fecha_cierre"
                    class="form-control"
                    value="<?= e(
                        old(
                            'fecha_cierre',
                            $survey[
                                'fecha_cierre'
                            ] ?? ''
                        )
                    ) ?>"
                >

                <div class="form-text">
                    Vacía significa sin fecha de cierre.
                </div>
            </div>

            <?php if ($editing): ?>
                <div class="col-md-4">
                    <label
                        for="estado"
                        class="form-label"
                    >
                        Estado
                    </label>

                    <?php
                    $currentStatus = old(
                        'estado',
                        $survey['estado']
                            ?? 'borrador'
                    );
                    ?>

                    <select
                        id="estado"
                        name="estado"
                        class="form-select"
                        required
                    >
                        <?php foreach (
                            $statuses as $value => $label
                        ): ?>
                            <option
                                value="<?= e($value) ?>"
                                <?= selected(
                                    $currentStatus,
                                    $value
                                ) ?>
                            >
                                <?= e($label) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div
        class="eco-card-head
               justify-content-end"
    >
        <?php if ($editing): ?>
            <a
                class="btn btn-eco-outline"
                href="<?= e(
                    url(
                        'encuestas/preguntas',
                        [
                            'id' => $survey[
                                'id_encuesta'
                            ],
                        ]
                    )
                ) ?>"
            >
                <i class="bi bi-list-check me-2"></i>
                Administrar preguntas
            </a>
        <?php endif; ?>

        <button
            class="btn btn-eco"
            type="submit"
        >
            <i class="bi bi-check-lg me-2"></i>

            <?= $editing
                ? 'Guardar cambios'
                : 'Crear encuesta' ?>
        </button>
    </div>
</form>