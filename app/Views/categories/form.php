<div class="page-header">
    <div>
        <h1 class="page-title">
            <?= $editing
                ? 'Editar categoría'
                : 'Registrar categoría' ?>
        </h1>

        <p class="page-subtitle">
            Configura su clasificación y disponibilidad.
        </p>
    </div>

    <a
        class="btn btn-eco-outline"
        href="<?= e(url('categorias')) ?>"
    >
        <i class="bi bi-arrow-left me-2"></i>
        Regresar
    </a>
</div>

<form
    action="<?= e(
        url(
            $editing
                ? 'categorias/actualizar'
                : 'categorias/guardar'
        )
    ) ?>"
    method="post"
    class="eco-card"
>
    <?= csrf_field() ?>

    <?php if ($editing): ?>
        <input
            type="hidden"
            name="id_categoria"
            value="<?= (int) $record['id_categoria'] ?>"
        >
    <?php endif; ?>

    <div class="eco-card-body">
        <div class="row g-4">
            <div class="col-md-6">
                <label
                    for="nombre"
                    class="form-label"
                >
                    Nombre de la categoría
                </label>

                <input
                    id="nombre"
                    name="nombre"
                    class="form-control"
                    minlength="2"
                    maxlength="80"
                    value="<?= e(
                        old(
                            'nombre',
                            $record['nombre'] ?? ''
                        )
                    ) ?>"
                    required
                >
            </div>

            <div class="col-md-6">
                <label
                    for="tipo_residuo"
                    class="form-label"
                >
                    Tipo de residuo
                </label>

                <select
                    id="tipo_residuo"
                    name="tipo_residuo"
                    class="form-select"
                    required
                >
                    <?php foreach (
                        $types as $value => $label
                    ): ?>
                        <option
                            value="<?= e($value) ?>"
                            <?= old(
                                'tipo_residuo',
                                $record['tipo_residuo']
                                    ?? 'reciclable'
                            ) === $value
                                ? 'selected'
                                : '' ?>
                        >
                            <?= e($label) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-12">
                <label
                    for="descripcion"
                    class="form-label"
                >
                    Descripción
                </label>

                <textarea
                    id="descripcion"
                    name="descripcion"
                    class="form-control"
                    maxlength="255"
                    rows="3"
                ><?= e(
                    old(
                        'descripcion',
                        $record['descripcion'] ?? ''
                    )
                ) ?></textarea>
            </div>

            <div class="col-md-4">
                <label
                    for="icono"
                    class="form-label"
                >
                    Ícono
                </label>

                <select
                    id="icono"
                    name="icono"
                    class="form-select"
                    required
                >
                    <?php foreach (
                        $icons as $value => $label
                    ): ?>
                        <option
                            value="<?= e($value) ?>"
                            <?= old(
                                'icono',
                                $record['icono']
                                    ?? 'bi-recycle'
                            ) === $value
                                ? 'selected'
                                : '' ?>
                        >
                            <?= e($label) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-md-4">
                <label
                    for="color_hex"
                    class="form-label"
                >
                    Color identificador
                </label>

                <input
                    id="color_hex"
                    type="color"
                    name="color_hex"
                    class="form-control form-control-color w-100"
                    value="<?= e(
                        old(
                            'color_hex',
                            $record['color_hex']
                                ?? '#16803C'
                        )
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
                $selectedStatus = old(
                    'estado',
                    $record['estado'] ?? 'activo'
                );
                ?>

                <select
                    id="estado"
                    name="estado"
                    class="form-select"
                >
                    <option
                        value="activo"
                        <?= $selectedStatus === 'activo'
                            ? 'selected'
                            : '' ?>
                    >
                        Activo
                    </option>

                    <option
                        value="inactivo"
                        <?= $selectedStatus === 'inactivo'
                            ? 'selected'
                            : '' ?>
                    >
                        Inactivo
                    </option>
                </select>
            </div>

            <div class="col-md-6">
                <label class="eco-choice">
                    <input
                        type="hidden"
                        name="disponible_cotizacion"
                        value="0"
                    >

                    <input
                        type="checkbox"
                        name="disponible_cotizacion"
                        value="1"
                        <?= (int) old(
                            'disponible_cotizacion',
                            $record[
                                'disponible_cotizacion'
                            ] ?? 1
                        ) === 1
                            ? 'checked'
                            : '' ?>
                    >

                    <strong>
                        Disponible para cotización
                    </strong>

                    <span
                        class="d-block small text-muted-eco mt-1"
                    >
                        Permitirá calcular precios para esta
                        categoría.
                    </span>
                </label>
            </div>

            <div class="col-md-6">
                <label class="eco-choice">
                    <input
                        type="hidden"
                        name="visible_base_conocimiento"
                        value="0"
                    >

                    <input
                        type="checkbox"
                        name="visible_base_conocimiento"
                        value="1"
                        <?= (int) old(
                            'visible_base_conocimiento',
                            $record[
                                'visible_base_conocimiento'
                            ] ?? 1
                        ) === 1
                            ? 'checked'
                            : '' ?>
                    >

                    <strong>
                        Visible en base de conocimiento
                    </strong>

                    <span
                        class="d-block small text-muted-eco mt-1"
                    >
                        Podrá relacionarse con contenido
                        educativo.
                    </span>
                </label>
            </div>
        </div>
    </div>

    <div class="eco-card-head justify-content-end">
        <a
            class="btn btn-eco-outline"
            href="<?= e(url('categorias')) ?>"
        >
            Cancelar
        </a>

        <button
            class="btn btn-eco"
            type="submit"
        >
            <i class="bi bi-check2-circle me-2"></i>

            <?= $editing
                ? 'Guardar cambios'
                : 'Registrar categoría' ?>
        </button>
    </div>
</form>