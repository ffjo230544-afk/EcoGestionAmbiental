<div class="page-header">
    <div>
        <span class="section-label">
            Cuestionario
        </span>

        <h1 class="page-title mt-1">
            <?= $editing
                ? 'Editar pregunta'
                : 'Agregar pregunta' ?>
        </h1>

        <p class="page-subtitle">
            Encuesta:
            <?= e($survey['titulo']) ?>
        </p>
    </div>

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
        <i class="bi bi-arrow-left me-2"></i>
        Regresar
    </a>
</div>

<form
    action="<?= e(
        url(
            $editing
                ? 'encuestas/preguntas/actualizar'
                : 'encuestas/preguntas/guardar'
        )
    ) ?>"
    method="post"
    class="eco-card"
>
    <?= csrf_field() ?>

    <input
        type="hidden"
        name="id_encuesta"
        value="<?= (int) $survey[
            'id_encuesta'
        ] ?>"
    >

    <?php if ($editing): ?>
        <input
            type="hidden"
            name="id_pregunta"
            value="<?= (int) $question[
                'id_pregunta'
            ] ?>"
        >
    <?php endif; ?>

    <div class="eco-card-body">
        <div class="row g-4">
            <div class="col-12">
                <label
                    for="texto_pregunta"
                    class="form-label"
                >
                    Texto de la pregunta *
                </label>

                <input
                    id="texto_pregunta"
                    name="texto_pregunta"
                    class="form-control"
                    minlength="5"
                    maxlength="255"
                    value="<?= e(
                        old(
                            'texto_pregunta',
                            $question[
                                'texto_pregunta'
                            ] ?? ''
                        )
                    ) ?>"
                    required
                    autofocus
                >
            </div>

            <div class="col-md-5">
                <label
                    for="tipo_pregunta"
                    class="form-label"
                >
                    Tipo de pregunta *
                </label>

                <?php
                $currentType = old(
                    'tipo_pregunta',
                    $question['tipo_pregunta']
                        ?? 'opcion_multiple'
                );
                ?>

                <select
                    id="tipo_pregunta"
                    name="tipo_pregunta"
                    class="form-select"
                    data-question-type
                    required
                >
                    <?php foreach (
                        $questionTypes as $value => $label
                    ): ?>
                        <option
                            value="<?= e($value) ?>"
                            <?= selected(
                                $currentType,
                                $value
                            ) ?>
                        >
                            <?= e($label) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-md-3">
                <label
                    for="orden"
                    class="form-label"
                >
                    Orden *
                </label>

                <input
                    id="orden"
                    type="number"
                    name="orden"
                    class="form-control"
                    min="1"
                    max="999"
                    value="<?= (int) old(
                        'orden',
                        $question['orden']
                            ?? $defaultOrder
                    ) ?>"
                    required
                >
            </div>

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
                    $question['estado']
                        ?? 'activa'
                );
                ?>

                <select
                    id="estado"
                    name="estado"
                    class="form-select"
                >
                    <option
                        value="activa"
                        <?= selected(
                            $currentStatus,
                            'activa'
                        ) ?>
                    >
                        Activa
                    </option>

                    <option
                        value="inactiva"
                        <?= selected(
                            $currentStatus,
                            'inactiva'
                        ) ?>
                    >
                        Inactiva
                    </option>
                </select>
            </div>

            <div
                class="col-12"
                data-question-options
            >
                <label
                    for="opciones"
                    class="form-label"
                >
                    Opciones de respuesta *
                </label>

                <textarea
                    id="opciones"
                    name="opciones"
                    class="form-control"
                    rows="6"
                    placeholder="Una opción por línea&#10;Siempre&#10;A veces&#10;Nunca"
                ><?= e(
                    old(
                        'opciones',
                        $optionsText
                    )
                ) ?></textarea>

                <div class="form-text">
                    Escribe entre 2 y 10 opciones,
                    una en cada línea. Este campo
                    solamente se utiliza para selección.
                </div>
            </div>

            <div class="col-12">
                <label class="eco-choice">
                    <input
                        type="hidden"
                        name="obligatoria"
                        value="0"
                    >

                    <input
                        type="checkbox"
                        name="obligatoria"
                        value="1"
                        <?= checked(
                            old(
                                'obligatoria',
                                $question[
                                    'obligatoria'
                                ] ?? 1
                            ),
                            1
                        ) ?>
                    >

                    <strong>
                        Respuesta obligatoria
                    </strong>

                    <span
                        class="d-block small
                               text-muted-eco mt-1"
                    >
                        El formulario no podrá enviarse
                        si esta pregunta queda vacía.
                    </span>
                </label>
            </div>
        </div>
    </div>

    <div
        class="eco-card-head
               justify-content-end"
    >
        <button
            class="btn btn-eco"
            type="submit"
        >
            <i class="bi bi-check-lg me-2"></i>

            <?= $editing
                ? 'Guardar cambios'
                : 'Agregar pregunta' ?>
        </button>
    </div>
</form>