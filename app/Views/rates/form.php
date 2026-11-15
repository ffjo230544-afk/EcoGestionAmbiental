<div class="page-header">
    <div>
        <span class="section-label">
            Operación
        </span>

        <h1 class="page-title mt-1">
            <?= $editing
                ? 'Editar tarifa'
                : 'Registrar tarifa' ?>
        </h1>

        <p class="page-subtitle">
            Define los precios y las condiciones
            que utilizará el cotizador.
        </p>
    </div>

    <a
        class="btn btn-eco-outline"
        href="<?= e(url('tarifas')) ?>"
    >
        <i class="bi bi-arrow-left me-2"></i>
        Regresar
    </a>
</div>

<?php if ($categories === []): ?>

    <div class="alert alert-warning">
        <i class="bi bi-exclamation-triangle me-2"></i>

        No existen categorías activas disponibles
        para cotización. Primero debes habilitar
        una categoría desde el módulo de categorías.
    </div>

<?php endif; ?>

<form
    action="<?= e(url(
        $editing
            ? 'tarifas/actualizar'
            : 'tarifas/guardar'
    )) ?>"
    method="post"
    class="eco-card"
>
    <?= csrf_field() ?>

    <?php if ($editing): ?>
        <input
            type="hidden"
            name="id_regla"
            value="<?= (int) $record['id_regla'] ?>"
        >
    <?php endif; ?>

    <div class="eco-card-body">
        <div class="alert alert-light border mb-4">
            <i
                class="bi bi-info-circle
                       text-eco me-2"
            ></i>

            Puedes registrar un precio por kilogramo,
            por pieza o ambos. Un precio en cero significa
            que esa modalidad no estará disponible.
        </div>

        <div class="row g-4">
            <div class="col-md-6">
                <label
                    for="id_categoria"
                    class="form-label"
                >
                    Categoría *
                </label>

                <select
                    id="id_categoria"
                    name="id_categoria"
                    class="form-select"
                    required
                >
                    <option value="">
                        Selecciona una categoría
                    </option>

                    <?php foreach (
                        $categories as $category
                    ): ?>
                        <option
                            value="<?= (int) $category[
                                'id_categoria'
                            ] ?>"
                            <?= selected(
                                old(
                                    'id_categoria',
                                    $record['id_categoria']
                                        ?? ''
                                ),
                                $category['id_categoria']
                            ) ?>
                        >
                            <?= e($category['nombre']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-md-3">
                <label
                    for="precio_por_kg"
                    class="form-label"
                >
                    Precio por kilogramo *
                </label>

                <div class="input-group">
                    <span class="input-group-text">
                        $
                    </span>

                    <input
                        id="precio_por_kg"
                        type="number"
                        name="precio_por_kg"
                        class="form-control"
                        min="0"
                        max="99999999.99"
                        step="0.01"
                        value="<?= e(old(
                            'precio_por_kg',
                            $record['precio_por_kg']
                                ?? '0.00'
                        )) ?>"
                        required
                    >

                    <span class="input-group-text">
                        kg
                    </span>
                </div>
            </div>

            <div class="col-md-3">
                <label
                    for="precio_por_pieza"
                    class="form-label"
                >
                    Precio por pieza *
                </label>

                <div class="input-group">
                    <span class="input-group-text">
                        $
                    </span>

                    <input
                        id="precio_por_pieza"
                        type="number"
                        name="precio_por_pieza"
                        class="form-control"
                        min="0"
                        max="99999999.99"
                        step="0.01"
                        value="<?= e(old(
                            'precio_por_pieza',
                            $record['precio_por_pieza']
                                ?? '0.00'
                        )) ?>"
                        required
                    >

                    <span class="input-group-text">
                        pieza
                    </span>
                </div>
            </div>

            <div class="col-md-3">
                <label
                    for="minimo_kg"
                    class="form-label"
                >
                    Peso mínimo
                </label>

                <div class="input-group">
                    <input
                        id="minimo_kg"
                        type="number"
                        name="minimo_kg"
                        class="form-control"
                        min="0"
                        step="0.01"
                        value="<?= e(old(
                            'minimo_kg',
                            $record['minimo_kg']
                                ?? ''
                        )) ?>"
                        placeholder="Sin mínimo"
                    >

                    <span class="input-group-text">
                        kg
                    </span>
                </div>
            </div>

            <div class="col-md-3">
                <label
                    for="maximo_kg"
                    class="form-label"
                >
                    Peso máximo
                </label>

                <div class="input-group">
                    <input
                        id="maximo_kg"
                        type="number"
                        name="maximo_kg"
                        class="form-control"
                        min="0"
                        step="0.01"
                        value="<?= e(old(
                            'maximo_kg',
                            $record['maximo_kg']
                                ?? ''
                        )) ?>"
                        placeholder="Sin máximo"
                    >

                    <span class="input-group-text">
                        kg
                    </span>
                </div>
            </div>

            <div class="col-md-3">
                <label
                    for="vigencia_inicio"
                    class="form-label"
                >
                    Inicio de vigencia *
                </label>

                <input
                    id="vigencia_inicio"
                    type="date"
                    name="vigencia_inicio"
                    class="form-control"
                    value="<?= e(old(
                        'vigencia_inicio',
                        $record['vigencia_inicio']
                            ?? date('Y-m-d')
                    )) ?>"
                    required
                >
            </div>

            <div class="col-md-3">
                <label
                    for="vigencia_fin"
                    class="form-label"
                >
                    Final de vigencia
                </label>

                <input
                    id="vigencia_fin"
                    type="date"
                    name="vigencia_fin"
                    class="form-control"
                    value="<?= e(old(
                        'vigencia_fin',
                        $record['vigencia_fin']
                            ?? ''
                    )) ?>"
                >

                <div class="form-text">
                    Déjala vacía si no tendrá fecha final.
                </div>
            </div>

            <div class="col-md-4">
                <label
                    for="estado"
                    class="form-label"
                >
                    Estado
                </label>

                <select
                    id="estado"
                    name="estado"
                    class="form-select"
                >
                    <option
                        value="activa"
                        <?= selected(
                            old(
                                'estado',
                                $record['estado']
                                    ?? 'activa'
                            ),
                            'activa'
                        ) ?>
                    >
                        Activa
                    </option>

                    <option
                        value="inactiva"
                        <?= selected(
                            old(
                                'estado',
                                $record['estado']
                                    ?? 'activa'
                            ),
                            'inactiva'
                        ) ?>
                    >
                        Inactiva
                    </option>
                </select>
            </div>
        </div>
    </div>

    <div class="eco-card-head justify-content-end">
        <a
            class="btn btn-eco-outline"
            href="<?= e(url('tarifas')) ?>"
        >
            Cancelar
        </a>

        <button
            type="submit"
            class="btn btn-eco"
            <?= $categories === []
                ? 'disabled'
                : '' ?>
        >
            <i class="bi bi-check2-circle me-2"></i>

            <?= $editing
                ? 'Guardar cambios'
                : 'Registrar tarifa' ?>
        </button>
    </div>
</form>