<div class="page-header">
    <div>
        <h1 class="page-title">
            Centros de acopio
        </h1>

        <p class="page-subtitle">
            Consulta empresas, centros y puntos de servicio
            ambiental.
        </p>
    </div>

    <?php if ($canCreate): ?>
    <a class="btn btn-eco" href="<?= e(url('centros/crear')) ?>">
        <i class="bi bi-plus-lg me-2"></i>
        Nuevo centro
    </a>
    <?php endif; ?>
</div>

<section class="eco-card mb-4">
    <div class="eco-card-body p-4">
        <form action="<?= e(url('centros')) ?>" method="get" class="row g-3 align-items-end">
            <input type="hidden" name="ruta" value="centros">

            <div class="col-lg-5">
                <label for="buscar" class="form-label">
                    Buscar
                </label>

                <input id="buscar" type="search" name="buscar" value="<?= e($search) ?>" class="form-control"
                    placeholder="Nombre, dirección, municipio o C.P.">
            </div>

            <div class="col-md-4 col-lg-3">
                <label for="tipo" class="form-label">
                    Tipo de punto
                </label>

                <select id="tipo" name="tipo" class="form-select">
                    <option value="">
                        Todos los tipos
                    </option>

                    <?php foreach (
                        $pointTypes as $value => $label
                    ): ?>
                    <option value="<?= e($value) ?>" <?= $type === $value
                                ? 'selected'
                                : '' ?>>
                        <?= e($label) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <?php if ($canChangeStatus): ?>
            <div class="col-md-4 col-lg-2">
                <label for="estado" class="form-label">
                    Estado
                </label>

                <select id="estado" name="estado" class="form-select">
                    <option value="">
                        Todos
                    </option>

                    <option value="activo" <?= $status === 'activo'
                                ? 'selected'
                                : '' ?>>
                        Activo
                    </option>

                    <option value="inactivo" <?= $status === 'inactivo'
                                ? 'selected'
                                : '' ?>>
                        Inactivo
                    </option>
                </select>
            </div>
            <?php endif; ?>

            <div class="col-md-4 col-lg-2 d-grid">
                <button class="btn btn-eco-outline" type="submit">
                    <i class="bi bi-search me-1"></i>
                    Buscar
                </button>
            </div>
        </form>
    </div>
</section>

<?php if ($centers !== []): ?>

<div class="row g-4">
    <?php foreach ($centers as $center): ?>

    <div class="col-md-6 col-xl-4">
        <article class="eco-card center-card h-100">
            <div class="center-cover">
                <i class="bi bi-building-fill"></i>
            </div>

            <div class="eco-card-body">
                <div class="d-flex justify-content-between
                                   gap-3">
                    <div>
                        <span class="small text-eco fw-semibold">
                            <?= e(
                                        $pointTypes[
                                            $center['tipo_punto']
                                        ] ?? 'Punto de atención'
                                    ) ?>
                        </span>

                        <h2 class="h5 fw-bold mt-1">
                            <?= e($center['nombre']) ?>
                        </h2>
                    </div>

                    <span class="status-pill
                                       status-<?= e($center['estado']) ?>
                                       align-self-start">
                        <?= $center['estado'] === 'activo'
                                    ? 'Activo'
                                    : 'Inactivo' ?>
                    </span>
                </div>

                <p class="text-muted-eco small">
                    <i class="bi bi-geo-alt me-1"></i>

                    <?= e($center['direccion']) ?>

                    <?php if (
                                !empty($center['municipio'])
                            ): ?>
                    , <?= e($center['municipio']) ?>
                    <?php endif; ?>
                </p>

                <div class="d-flex flex-wrap gap-2">
                    <a href="<?= e(
            url(
                'centros/detalle',
                ['id' => $center['id_punto']]
            )
        ) ?>" class="btn btn-light-eco">
                        Ver detalles
                    </a>

                   <?php if ($canManage): ?>
    <a
        href="<?= e(
            url(
                'centros/editar',
                ['id' => $center['id_punto']]
            )
        ) ?>"
        title="Editar"
        class="btn btn-eco-outline"
    >
        <i class="bi bi-pencil"></i>
    </a>
<?php endif; ?>

<?php if ($canChangeStatus): ?>
    <?php
    $activate = $center['estado'] !== 'activo';

    $newStatus = $activate
        ? 'activo'
        : 'inactivo';
    ?>

    <form
        action="<?= e(url('centros/cambiar-estado')) ?>"
        method="post"
        class="m-0"
        data-confirm="<?= e(
            $activate
                ? '¿Deseas activar este centro?'
                : '¿Deseas desactivar este centro?'
        ) ?>"
    >
        <?= csrf_field() ?>

        <input
            type="hidden"
            name="id_punto"
            value="<?= (int) $center['id_punto'] ?>"
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
            title="<?= $activate
                ? 'Activar'
                : 'Desactivar' ?>"
        >
            <i class="bi <?= $activate
                ? 'bi-check-circle'
                : 'bi-x-circle' ?>"></i>
        </button>
    </form>
<?php endif; ?>
                </div>
            </div>
        </article>
    </div>

    <?php endforeach; ?>
</div>

<?php else: ?>

<section class="eco-card">
    <div class="p-5 text-center">
        <i class="bi bi-geo-alt fs-1 text-eco"></i>

        <h2 class="h5 mt-3">
            No se encontraron centros
        </h2>

        <p class="text-muted-eco mb-0">
            Cambia los filtros o registra un centro.
        </p>
    </div>
</section>

<?php endif; ?>