<div class="page-header">
    <div>
        <h1 class="page-title">
            <?= $editing
                ? 'Editar centro'
                : 'Registrar centro' ?>
        </h1>

        <p class="page-subtitle">
            Registra la ubicación, contacto y servicio
            proporcionado.
        </p>
    </div>

    <a
        class="btn btn-eco-outline"
        href="<?= e(url('centros')) ?>"
    >
        <i class="bi bi-arrow-left me-2"></i>
        Regresar
    </a>
</div>

<form
    action="<?= e(
        url(
            $editing
                ? 'centros/actualizar'
                : 'centros/guardar'
        )
    ) ?>"
    method="post"
    class="eco-card"
>
    <?= csrf_field() ?>

    <?php if ($editing): ?>
        <input
            type="hidden"
            name="id_punto"
            value="<?= (int) $record['id_punto'] ?>"
        >
    <?php endif; ?>

    <div class="eco-card-head">
        <h2 class="eco-card-title">
            Información general
        </h2>
    </div>

    <div class="eco-card-body">
        <div class="row g-4">
            <div class="col-md-8">
                <label for="nombre" class="form-label">
                    Nombre del centro
                </label>

                <input
                    id="nombre"
                    name="nombre"
                    class="form-control"
                    maxlength="120"
                    value="<?= e(
                        old(
                            'nombre',
                            $record['nombre'] ?? ''
                        )
                    ) ?>"
                    required
                >
            </div>

            <div class="col-md-4">
                <label
                    for="tipo_punto"
                    class="form-label"
                >
                    Tipo de punto
                </label>

                <select
                    id="tipo_punto"
                    name="tipo_punto"
                    class="form-select"
                    required
                >
                    <?php foreach (
                        $pointTypes as $value => $label
                    ): ?>
                        <option
                            value="<?= e($value) ?>"
                            <?= old(
                                'tipo_punto',
                                $record['tipo_punto']
                                    ?? 'centro_acopio'
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
                    for="direccion"
                    class="form-label"
                >
                    Dirección
                </label>

                <input
                    id="direccion"
                    name="direccion"
                    class="form-control"
                    maxlength="200"
                    value="<?= e(
                        old(
                            'direccion',
                            $record['direccion'] ?? ''
                        )
                    ) ?>"
                    placeholder="Calle, número y colonia"
                    required
                >
            </div>

            <div class="col-md-4">
                <label
                    for="municipio"
                    class="form-label"
                >
                    Municipio
                </label>

                <input
                    id="municipio"
                    name="municipio"
                    class="form-control"
                    maxlength="100"
                    value="<?= e(
                        old(
                            'municipio',
                            $record['municipio']
                                ?? 'Jiutepec'
                        )
                    ) ?>"
                >
            </div>

            <div class="col-md-4">
                <label
                    for="estado_region"
                    class="form-label"
                >
                    Estado
                </label>

                <input
                    id="estado_region"
                    name="estado_region"
                    class="form-control"
                    maxlength="100"
                    value="<?= e(
                        old(
                            'estado_region',
                            $record['estado_region']
                                ?? 'Morelos'
                        )
                    ) ?>"
                >
            </div>

            <div class="col-md-4">
                <label
                    for="codigo_postal"
                    class="form-label"
                >
                    Código postal
                </label>

                <input
                    id="codigo_postal"
                    name="codigo_postal"
                    class="form-control"
                    inputmode="numeric"
                    maxlength="5"
                    pattern="[0-9]{5}"
                    value="<?= e(
                        old(
                            'codigo_postal',
                            $record['codigo_postal'] ?? ''
                        )
                    ) ?>"
                >
            </div>
        </div>
    </div>

    <div class="eco-card-head border-top">
        <h2 class="eco-card-title">
            Contacto y servicio
        </h2>
    </div>

    <div class="eco-card-body">
        <div class="row g-4">
            <div class="col-md-4">
                <label
                    for="telefono"
                    class="form-label"
                >
                    Teléfono
                </label>

                <input
                    id="telefono"
                    name="telefono"
                    type="tel"
                    class="form-control"
                    maxlength="20"
                    value="<?= e(
                        old(
                            'telefono',
                            $record['telefono'] ?? ''
                        )
                    ) ?>"
                >
            </div>

            <div class="col-md-4">
                <label
                    for="correo_contacto"
                    class="form-label"
                >
                    Correo de contacto
                </label>

                <input
                    id="correo_contacto"
                    name="correo_contacto"
                    type="email"
                    class="form-control"
                    maxlength="120"
                    value="<?= e(
                        old(
                            'correo_contacto',
                            $record['correo_contacto'] ?? ''
                        )
                    ) ?>"
                >
            </div>

            <div class="col-md-4">
                <label
                    for="tipo_servicio"
                    class="form-label"
                >
                    Tipo de servicio
                </label>

                <select
                    id="tipo_servicio"
                    name="tipo_servicio"
                    class="form-select"
                    required
                >
                    <?php foreach (
                        $serviceTypes as $value => $label
                    ): ?>
                        <option
                            value="<?= e($value) ?>"
                            <?= old(
                                'tipo_servicio',
                                $record['tipo_servicio']
                                    ?? 'servicio_general'
                            ) === $value
                                ? 'selected'
                                : '' ?>
                        >
                            <?= e($label) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-md-6">
                <label
                    for="latitud"
                    class="form-label"
                >
                    Latitud
                </label>

                <input
                    id="latitud"
                    name="latitud"
                    type="number"
                    step="0.0000001"
                    min="-90"
                    max="90"
                    class="form-control"
                    value="<?= e(
                        old(
                            'latitud',
                            $record['latitud'] ?? ''
                        )
                    ) ?>"
                    placeholder="18.8837600"
                >
            </div>

            <div class="col-md-6">
                <label
                    for="longitud"
                    class="form-label"
                >
                    Longitud
                </label>

                <input
                    id="longitud"
                    name="longitud"
                    type="number"
                    step="0.0000001"
                    min="-180"
                    max="180"
                    class="form-control"
                    value="<?= e(
                        old(
                            'longitud',
                            $record['longitud'] ?? ''
                        )
                    ) ?>"
                    placeholder="-99.1769100"
                >
            </div>

            <div class="col-md-8">
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
    <label class="form-label">
        Estado del registro
    </label>

    <?php
    $currentStatus = (string) (
        $record['estado'] ?? 'activo'
    );
    ?>

    <?php if ($canChangeStatus): ?>

        <?php
        $selectedStatus = old(
            'estado',
            $currentStatus
        );
        ?>

        <select
            id="estado"
            name="estado"
            class="form-select"
        >
            <option
                value="activo"
                <?= selected(
                    $selectedStatus,
                    'activo'
                ) ?>
            >
                Activo
            </option>

            <option
                value="inactivo"
                <?= selected(
                    $selectedStatus,
                    'inactivo'
                ) ?>
            >
                Inactivo
            </option>
        </select>

    <?php else: ?>

        <input
            type="hidden"
            name="estado"
            value="<?= e($currentStatus) ?>"
        >

        <div>
            <span
                class="status-pill
                       status-<?= e($currentStatus) ?>"
            >
                <?= $currentStatus === 'activo'
                    ? 'Activo'
                    : 'Inactivo' ?>
            </span>
        </div>

        <div class="form-text">
            Solamente el administrador puede cambiar
            el estado del centro.
        </div>

    <?php endif; ?>
</div>
        </div>
    </div>

    <div class="eco-card-head justify-content-end">
        <a
            class="btn btn-eco-outline"
            href="<?= e(url('centros')) ?>"
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
                : 'Registrar centro' ?>
        </button>
    </div>
</form>