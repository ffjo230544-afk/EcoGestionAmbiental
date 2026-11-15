<div class="page-header">
    <div>
        <span class="section-label">
            Materiales aceptados
        </span>

        <h1 class="page-title mt-1">
            <?= e($center['nombre']) ?>
        </h1>

        <p class="page-subtitle">
            Selecciona los residuos que recibe o compra
            este centro.
        </p>
    </div>

    <a
        class="btn btn-eco-outline"
        href="<?= e(
            url(
                'centros/detalle',
                ['id' => $center['id_punto']]
            )
        ) ?>"
    >
        <i class="bi bi-arrow-left me-2"></i>
        Regresar
    </a>
</div>

<form
    action="<?= e(
        url('centros/materiales/actualizar')
    ) ?>"
    method="post"
>
    <?= csrf_field() ?>

    <input
        type="hidden"
        name="id_punto"
        value="<?= (int) $center['id_punto'] ?>"
    >

    <?php if ($categories !== []): ?>

        <div class="row g-4">
            <?php foreach ($categories as $category): ?>

                <?php
                $categoryId = (int) $category[
                    'id_categoria'
                ];

                $baseKey = 'categorias.'
                    . $categoryId
                    . '.';

                $selected = (int) old(
                    $baseKey . 'seleccionada',
                    $category['relacionada']
                ) === 1;

                $acceptsPurchase = (int) old(
                    $baseKey . 'acepta_compra',
                    $category['acepta_compra']
                ) === 1;

                $acceptsReception = (int) old(
                    $baseKey . 'acepta_recepcion',
                    $category['acepta_recepcion']
                ) === 1;

                $color = preg_match(
                    '/^#[0-9A-Fa-f]{6}$/',
                    (string) $category['color_hex']
                ) === 1
                    ? $category['color_hex']
                    : '#16803C';

                $icon = preg_match(
                    '/^bi-[a-z0-9-]+$/',
                    (string) $category['icono']
                ) === 1
                    ? $category['icono']
                    : 'bi-recycle';
                ?>

                <div class="col-md-6 col-xl-4">
                    <section
                        class="eco-card h-100"
                        data-category-row
                    >
                        <div class="eco-card-head">
                            <div class="d-flex gap-3 align-items-center">
                                <div
                                    class="category-icon"
                                    style="color: <?= e($color) ?>;
                                           background: <?= e($color) ?>18;"
                                >
                                    <i
                                        class="bi <?= e($icon) ?>"
                                    ></i>
                                </div>

                                <div>
                                    <h2 class="eco-card-title">
                                        <?= e($category['nombre']) ?>
                                    </h2>

                                    <?php if (
                                        $category['estado']
                                        !== 'activo'
                                    ): ?>
                                        <span
                                            class="status-pill
                                                   status-inactivo mt-1"
                                        >
                                            Inactiva
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <div class="form-check form-switch">
                                <input
                                    type="hidden"
                                    name="categorias[<?= $categoryId ?>][seleccionada]"
                                    value="0"
                                >

                                <input
                                    type="checkbox"
                                    name="categorias[<?= $categoryId ?>][seleccionada]"
                                    value="1"
                                    class="form-check-input"
                                    data-category-toggle
                                    <?= $selected
                                        ? 'checked'
                                        : '' ?>
                                >
                            </div>
                        </div>

                        <div class="eco-card-body">
                            <p class="small text-muted-eco">
                                <?= e(
                                    $category['descripcion']
                                    ?: 'Sin descripción registrada.'
                                ) ?>
                            </p>

                            <div class="row g-3">
                                <div class="col-6">
                                    <label class="eco-choice">
                                        <input
                                            type="hidden"
                                            name="categorias[<?= $categoryId ?>][acepta_compra]"
                                            value="0"
                                        >

                                        <input
                                            type="checkbox"
                                            name="categorias[<?= $categoryId ?>][acepta_compra]"
                                            value="1"
                                            data-category-option
                                            <?= $acceptsPurchase
                                                ? 'checked'
                                                : '' ?>
                                        >

                                        Compra
                                    </label>
                                </div>

                                <div class="col-6">
                                    <label class="eco-choice">
                                        <input
                                            type="hidden"
                                            name="categorias[<?= $categoryId ?>][acepta_recepcion]"
                                            value="0"
                                        >

                                        <input
                                            type="checkbox"
                                            name="categorias[<?= $categoryId ?>][acepta_recepcion]"
                                            value="1"
                                            data-category-option
                                            <?= $acceptsReception
                                                ? 'checked'
                                                : '' ?>
                                        >

                                        Recepción
                                    </label>
                                </div>

                                <div class="col-12">
                                    <label
                                        class="form-label"
                                        for="observacion-<?= $categoryId ?>"
                                    >
                                        Observaciones
                                    </label>

                                    <input
                                        id="observacion-<?= $categoryId ?>"
                                        type="text"
                                        name="categorias[<?= $categoryId ?>][observaciones]"
                                        value="<?= e(
                                            old(
                                                $baseKey
                                                . 'observaciones',
                                                $category[
                                                    'observaciones'
                                                ] ?? ''
                                            )
                                        ) ?>"
                                        class="form-control"
                                        maxlength="150"
                                        placeholder="Condiciones de recepción"
                                        data-category-option
                                    >
                                </div>
                            </div>
                        </div>
                    </section>
                </div>

            <?php endforeach; ?>
        </div>

        <div
            class="eco-card mt-4"
            style="height: auto;"
        >
            <div class="eco-card-head justify-content-end">
                <a
                    class="btn btn-eco-outline"
                    href="<?= e(
                        url(
                            'centros/detalle',
                            ['id' => $center['id_punto']]
                        )
                    ) ?>"
                >
                    Cancelar
                </a>

                <button
                    type="submit"
                    class="btn btn-eco"
                >
                    <i class="bi bi-check2-circle me-2"></i>
                    Guardar materiales
                </button>
            </div>
        </div>

    <?php else: ?>

        <section class="eco-card">
            <div class="p-5 text-center">
                <i class="bi bi-recycle fs-1 text-eco"></i>

                <h2 class="h5 mt-3">
                    No existen categorías disponibles
                </h2>

                <p class="text-muted-eco">
                    Primero registra una categoría de residuo.
                </p>

                <a
                    href="<?= e(url('categorias/crear')) ?>"
                    class="btn btn-eco"
                >
                    Registrar categoría
                </a>
            </div>
        </section>

    <?php endif; ?>
</form>