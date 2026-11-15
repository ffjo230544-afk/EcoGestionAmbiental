<div class="page-header">
    <div>
        <h1 class="page-title">
            Categorías de residuos
        </h1>

        <p class="page-subtitle">
            Clasificación de materiales y su disponibilidad
            en los servicios ambientales.
        </p>
    </div>

    <?php if ($canManage): ?>
        <a
            class="btn btn-eco"
            href="<?= e(url('categorias/crear')) ?>"
        >
            <i class="bi bi-plus-lg me-2"></i>
            Nueva categoría
        </a>
    <?php endif; ?>
</div>

<section class="eco-card mb-4">
    <div class="eco-card-body">
        <form
            action="<?= e(url('categorias')) ?>"
            method="get"
            class="row g-3 align-items-end"
        >
            <input
                type="hidden"
                name="ruta"
                value="categorias"
            >

            <div class="col-lg-5">
                <label
                    for="buscar"
                    class="form-label"
                >
                    Buscar
                </label>

                <input
                    id="buscar"
                    type="search"
                    name="buscar"
                    value="<?= e($search) ?>"
                    class="form-control"
                    placeholder="Nombre o descripción"
                >
            </div>

            <div class="col-md-4 col-lg-3">
                <label
                    for="tipo"
                    class="form-label"
                >
                    Tipo de residuo
                </label>

                <select
                    id="tipo"
                    name="tipo"
                    class="form-select"
                >
                    <option value="">
                        Todos los tipos
                    </option>

                    <?php foreach (
                        $types as $value => $label
                    ): ?>
                        <option
                            value="<?= e($value) ?>"
                            <?= $type === $value
                                ? 'selected'
                                : '' ?>
                        >
                            <?= e($label) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <?php if ($canManage): ?>
                <div class="col-md-4 col-lg-2">
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
                        <option value="">Todos</option>

                        <option
                            value="activo"
                            <?= $status === 'activo'
                                ? 'selected'
                                : '' ?>
                        >
                            Activo
                        </option>

                        <option
                            value="inactivo"
                            <?= $status === 'inactivo'
                                ? 'selected'
                                : '' ?>
                        >
                            Inactivo
                        </option>
                    </select>
                </div>
            <?php endif; ?>

            <div class="col-md-4 col-lg-2 d-grid">
                <button
                    class="btn btn-eco-outline"
                    type="submit"
                >
                    <i class="bi bi-search me-1"></i>
                    Buscar
                </button>
            </div>
        </form>
    </div>
</section>

<?php if ($categories !== []): ?>

    <div class="row g-4">
        <?php foreach ($categories as $item): ?>

            <?php
            $color = preg_match(
                '/^#[0-9A-Fa-f]{6}$/',
                (string) $item['color_hex']
            ) === 1
                ? $item['color_hex']
                : '#16803C';

            $icon = preg_match(
                '/^bi-[a-z0-9-]+$/',
                (string) $item['icono']
            ) === 1
                ? $item['icono']
                : 'bi-recycle';
            ?>

            <div class="col-md-6 col-xl-4">
                <article class="eco-card h-100">
                    <div class="eco-card-body h-100 d-flex flex-column">
                        <div
                            class="d-flex justify-content-between
                                   gap-3"
                        >
                            <div
                                class="category-icon"
                                style="color: <?= e($color) ?>;
                                       background: <?= e($color) ?>18;"
                            >
                                <i class="bi <?= e($icon) ?>"></i>
                            </div>

                            <span
                                class="status-pill
                                       status-<?= e($item['estado']) ?>
                                       align-self-start"
                            >
                                <?= $item['estado'] === 'activo'
                                    ? 'Activo'
                                    : 'Inactivo' ?>
                            </span>
                        </div>

                        <h2 class="h5 fw-bold mt-4">
                            <?= e($item['nombre']) ?>
                        </h2>

                        <p class="text-muted-eco">
                            <?= e(
                                $item['descripcion']
                                ?: 'Sin descripción registrada.'
                            ) ?>
                        </p>

                        <div class="d-flex flex-wrap gap-2 mb-3">
                            <span class="chip">
                                <?= e(
                                    $types[$item['tipo_residuo']]
                                    ?? 'Otro'
                                ) ?>
                            </span>

                            <span class="chip">
                                <i class="bi bi-geo-alt"></i>
                                <?= (int) $item['total_centros'] ?>
                                centro(s)
                            </span>
                        </div>

                        <div class="small text-muted-eco mb-3">
                            <div>
                                <i class="bi <?= (int) $item[
                                    'disponible_cotizacion'
                                ] === 1
                                    ? 'bi-check-circle text-success'
                                    : 'bi-x-circle text-danger' ?>"></i>

                                Disponible para cotización
                            </div>

                            <div class="mt-1">
                                <i class="bi <?= (int) $item[
                                    'visible_base_conocimiento'
                                ] === 1
                                    ? 'bi-check-circle text-success'
                                    : 'bi-x-circle text-danger' ?>"></i>

                                Visible en base de conocimiento
                            </div>
                        </div>

                        <?php if ($canManage): ?>
                            <div
                                class="d-flex flex-wrap gap-2 mt-auto"
                            >
                                <a
                                    href="<?= e(
                                        url(
                                            'categorias/editar',
                                            [
                                                'id' =>
                                                    $item['id_categoria'],
                                            ]
                                        )
                                    ) ?>"
                                    class="btn btn-eco-outline"
                                >
                                    <i class="bi bi-pencil me-1"></i>
                                    Editar
                                </a>

                                <?php
                                $activate =
                                    $item['estado'] !== 'activo';

                                $newStatus = $activate
                                    ? 'activo'
                                    : 'inactivo';
                                ?>

                                <form
                                    action="<?= e(
                                        url(
                                            'categorias/cambiar-estado'
                                        )
                                    ) ?>"
                                    method="post"
                                    class="m-0"
                                    data-confirm="<?= e(
                                        $activate
                                            ? '¿Deseas activar esta categoría?'
                                            : '¿Deseas desactivar esta categoría?'
                                    ) ?>"
                                >
                                    <?= csrf_field() ?>

                                    <input
                                        type="hidden"
                                        name="id_categoria"
                                        value="<?= (int) $item[
                                            'id_categoria'
                                        ] ?>"
                                    >

                                    <input
                                        type="hidden"
                                        name="estado"
                                        value="<?= e($newStatus) ?>"
                                    >

                                    <button
                                        type="submit"
                                        class="btn <?= $activate
                                            ? 'btn-outline-success'
                                            : 'btn-outline-danger' ?>"
                                    >
                                        <i class="bi <?= $activate
                                            ? 'bi-check-circle'
                                            : 'bi-x-circle' ?>"></i>

                                        <?= $activate
                                            ? 'Activar'
                                            : 'Desactivar' ?>
                                    </button>
                                </form>
                            </div>
                        <?php endif; ?>
                    </div>
                </article>
            </div>

        <?php endforeach; ?>
    </div>

<?php else: ?>

    <section class="eco-card">
        <div class="p-5 text-center">
            <i class="bi bi-recycle fs-1 text-eco"></i>

            <h2 class="h5 mt-3">
                No se encontraron categorías
            </h2>

            <p class="text-muted-eco mb-0">
                Modifica los filtros o registra una categoría.
            </p>
        </div>
    </section>

<?php endif; ?>