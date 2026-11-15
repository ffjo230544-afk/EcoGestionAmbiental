<?php
/*
 | Aquí determino el centro que debe aparecer
 | seleccionado dentro del formulario.
 |
 | Si estoy editando, utilizo el centro de la tarifa.
 | Si soy coordinador, utilizo el centro de mi sesión.
 */
$selectedPointId = (int) old(
    'id_punto',
    $record['id_punto']
        ?? auth_center_id()
        ?? 0
);

/*
 | Aquí compruebo si el usuario actual
 | es un coordinador.
 */
$isCoordinator = has_role('coordinador');

/*
 | Busco el nombre del centro seleccionado
 | para poder mostrarlo al coordinador.
 */
$selectedCenterName = '';

foreach ($centers as $center) {
    if (
        (int) $center['id_punto']
        === $selectedPointId
    ) {
        $selectedCenterName =
            (string) $center['nombre'];

        break;
    }
}

/*
 | Si el centro no apareció en el arreglo,
 | utilizo el nombre almacenado en la sesión.
 */
if (
    $selectedCenterName === ''
    && $isCoordinator
) {
    $selectedCenterName = (string) (
        auth_user()['nombre_punto_asignado']
        ?? 'Centro asignado'
    );
}

/*
 | El formulario solamente podrá guardarse
 | cuando existan centros y categorías disponibles.
 */
$formAvailable =
    $centers !== []
    && $categories !== [];
?>

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
            Define los precios que utilizará cada
            centro para realizar sus cotizaciones.
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

<?php if ($centers === []): ?>

    <div class="alert alert-warning">
        <i class="bi bi-exclamation-triangle me-2"></i>

        No existen centros activos que compren
        materiales. Primero debes configurar los
        materiales aceptados por un centro.
    </div>

<?php elseif ($categories === []): ?>

    <div class="alert alert-warning">
        <i class="bi bi-exclamation-triangle me-2"></i>

        No existen categorías disponibles para
        registrar tarifas. El centro debe tener por
        lo menos una categoría marcada como Compra.
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

            Cada centro puede manejar precios diferentes.
            Puedes registrar un precio por kilogramo,
            por pieza o ambos. Un precio en cero significa
            que esa modalidad no estará disponible.
        </div>

        <div class="row g-4">

            <!-- Centro de atención -->
            <div class="col-md-6">
                <label
                    for="id_punto"
                    class="form-label"
                >
                    Centro de atención *
                </label>

                <?php if ($isCoordinator): ?>

                    <!--
                     | El coordinador no puede cambiar
                     | el centro que tiene asignado.
                     -->
                    <input
                        type="hidden"
                        id="id_punto"
                        name="id_punto"
                        value="<?= $selectedPointId ?>"
                        data-rate-center
                    >

                    <div
                        class="form-control
                               bg-light"
                    >
                        <i
                            class="bi bi-building
                                   text-eco me-2"
                        ></i>

                        <?= e($selectedCenterName) ?>
                    </div>

                    <div class="form-text">
                        Este es el centro asignado
                        a tu cuenta.
                    </div>

                <?php else: ?>

                    <!--
                     | El administrador puede seleccionar
                     | cualquiera de los centros disponibles.
                     -->
                    <select
                        id="id_punto"
                        name="id_punto"
                        class="form-select"
                        data-rate-center
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
                                    $selectedPointId,
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

                    <div class="form-text">
                        Selecciona el centro que utilizará
                        esta tarifa.
                    </div>

                <?php endif; ?>
            </div>

            <!-- Categoría del residuo -->
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
                    data-rate-category
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
                            data-rate-centers="<?= e(
                                $category['centros'] ?? ''
                            ) ?>"
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

                <div class="form-text">
                    Solamente puede utilizarse una categoría
                    que el centro tenga marcada como Compra.
                </div>
            </div>

            <!-- Precio por kilogramo -->
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

            <!-- Precio por pieza -->
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

            <!-- Peso mínimo -->
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

            <!-- Peso máximo -->
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

            <!-- Inicio de vigencia -->
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

            <!-- Final de vigencia -->
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
                    Déjala vacía si la tarifa
                    no tendrá una fecha final.
                </div>
            </div>

            <!-- Estado de la tarifa -->
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
            <?= !$formAvailable
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