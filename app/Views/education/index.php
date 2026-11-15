<div class="knowledge-page knowledge-list-page">
<div class="page-header">
    <div>
        <span class="section-label">
            Educación ambiental
        </span>

        <h1 class="page-title mt-1">
            Base de conocimiento ambiental
        </h1>

        <p class="page-subtitle">
            Aprende a separar, reutilizar y manejar
            correctamente los residuos.
        </p>
    </div>

    <?php if ($canManage): ?>
        <a
            class="btn btn-eco"
            href="<?= e(url('educacion/crear')) ?>"
        >
            <i class="bi bi-plus-lg me-2"></i>
            Nueva publicación
        </a>
    <?php endif; ?>
</div>

<form
    action="<?= e(url('educacion')) ?>"
    method="get"
class="eco-card knowledge-filter mb-4"
>
    <!--
        En formularios GET se conserva la ruta como
        campo oculto para no perderla al aplicar filtros.
    -->
    <input
        type="hidden"
        name="ruta"
        value="educacion"
    >

    <div class="eco-card-body">
        <div class="row g-3">
            <div class="col-lg-4">
                <label
                    for="buscar"
                    class="form-label"
                >
                    Buscar contenido
                </label>

                <input
                    id="buscar"
                    type="search"
                    name="buscar"
                    class="form-control"
                    value="<?= e($search) ?>"
                    placeholder="Título, tema o recomendación"
                >
            </div>

            <div class="col-md-4 col-lg-3">
                <label
                    for="categoria"
                    class="form-label"
                >
                    Categoría
                </label>

                <select
                    id="categoria"
                    name="categoria"
                    class="form-select"
                >
                    <option value="">
                        Todas las categorías
                    </option>

                    <?php foreach ($categories as $category): ?>
                        <option
                            value="<?= (int) $category[
                                'id_categoria'
                            ] ?>"
                            <?= $categoryId ===
                                (int) $category['id_categoria']
                                ? 'selected'
                                : '' ?>
                        >
                            <?= e($category['nombre']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-md-4 col-lg-2">
                <label
                    for="tipo"
                    class="form-label"
                >
                    Tipo
                </label>

                <select
                    id="tipo"
                    name="tipo"
                    class="form-select"
                >
                    <option value="">
                        Todos
                    </option>

                    <?php foreach (
                        $typeLabels as $value => $label
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
                        <option value="">
                            Todos
                        </option>

                        <?php foreach (
                            $statusLabels as $value => $label
                        ): ?>
                            <option
                                value="<?= e($value) ?>"
                                <?= $status === $value
                                    ? 'selected'
                                    : '' ?>
                            >
                                <?= e($label) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            <?php endif; ?>

            <div class="col-lg-1 d-flex align-items-end">
                <button
                    type="submit"
                    class="btn btn-eco w-100"
                    title="Aplicar filtros"
                >
                    <i class="bi bi-search"></i>
                </button>
            </div>
        </div>

        <?php if (
            $search !== ''
            || $categoryId > 0
            || $type !== ''
            || $status !== ''
        ): ?>
            <div class="mt-3">
                <a
                    href="<?= e(url('educacion')) ?>"
                    class="small"
                >
                    <i class="bi bi-x-circle me-1"></i>
                    Limpiar filtros
                </a>
            </div>
        <?php endif; ?>
    </div>
</form>

<div class="d-flex justify-content-between mb-3">
    <strong>
        <?= (int) $total ?>
        <?= $total === 1
            ? 'publicación encontrada'
            : 'publicaciones encontradas' ?>
    </strong>
</div>

<?php if ($publications !== []): ?>

    <div class="row g-4">
        <?php foreach ($publications as $publication): ?>
            <?php
            $preview = (string) (
                $publication['resumen']
                ?: $publication['contenido']
            );

            $previewLength = function_exists('mb_strlen')
                ? mb_strlen($preview)
                : strlen($preview);

            if ($previewLength > 180) {
                $preview = function_exists('mb_substr')
                    ? mb_substr($preview, 0, 180)
                    : substr($preview, 0, 180);

                $preview .= '…';
            }

            $publicationDate =
                $publication['fecha_publicacion']
                ?: $publication['fecha_creacion'];

            $formattedDate = $publicationDate
                ? date(
                    'd/m/Y',
                    strtotime($publicationDate)
                )
                : 'Sin fecha';
            ?>

            <div class="col-md-6 col-xl-4">
                <article class="eco-card h-100 overflow-hidden">
                    <?php if (
                        !empty($publication['imagen_portada'])
                    ): ?>
                        <img
                            src="<?= e(asset(
                                $publication['imagen_portada']
                            )) ?>"
                            class="knowledge-cover"
                            alt="Portada de <?= e(
                                $publication['titulo']
                            ) ?>"
                        >
                    <?php else: ?>
                        <div class="knowledge-cover-placeholder">
                            <i class="bi bi-journal-richtext"></i>
                        </div>
                    <?php endif; ?>

                    <div class="eco-card-body">
                        <div
                            class="d-flex flex-wrap
                                   justify-content-between
                                   align-items-center gap-2"
                        >
                            <span class="chip">
                                <i class="bi bi-book"></i>

                                <?= e(
                                    $typeLabels[
                                        $publication[
                                            'tipo_publicacion'
                                        ]
                                    ] ?? 'Publicación'
                                ) ?>
                            </span>

                            <?php if ($canManage): ?>
                                <span
                                    class="status-pill
                                           status-<?= e(
                                               $publication['estado']
                                           ) ?>"
                                >
                                    <?= e(
                                        $statusLabels[
                                            $publication['estado']
                                        ] ?? $publication['estado']
                                    ) ?>
                                </span>
                            <?php endif; ?>
                        </div>

                        <h2 class="h5 fw-bold mt-3">
                            <?= e($publication['titulo']) ?>
                        </h2>

                        <p class="text-muted-eco">
                            <?= e($preview) ?>
                        </p>

                        <?php if (
                            !empty($publication['categorias'])
                        ): ?>
                            <div class="d-flex flex-wrap gap-1 mb-3">
                                <?php foreach (
                                    explode(
                                        ', ',
                                        $publication['categorias']
                                    ) as $categoryName
                                ): ?>
                                    <span class="chip small">
                                        <i class="bi bi-tag"></i>
                                        <?= e($categoryName) ?>
                                    </span>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>

                        <div class="small text-muted-eco mb-3">
                            <i class="bi bi-person me-1"></i>
                            <?= e($publication['autor']) ?>

                            <span class="mx-1">·</span>

                            <i class="bi bi-calendar3 me-1"></i>
                            <?= e($formattedDate) ?>
                        </div>

                        <div class="d-flex flex-wrap gap-2">
                            <a
                                class="btn btn-light-eco"
                                href="<?= e(url(
                                    'educacion/detalle',
                                    [
                                        'id' =>
                                            $publication[
                                                'id_publicacion'
                                            ],
                                    ]
                                )) ?>"
                            >
                                <i class="bi bi-book-half me-1"></i>
                                Leer
                            </a>

                            <?php if ($canManage): ?>
                                <a
                                    class="btn btn-eco-outline"
                                    href="<?= e(url(
                                        'educacion/editar',
                                        [
                                            'id' =>
                                                $publication[
                                                    'id_publicacion'
                                                ],
                                        ]
                                    )) ?>"
                                >
                                    <i class="bi bi-pencil"></i>
                                </a>

                                <?php if (
                                    $publication['estado']
                                    !== 'inactivo'
                                ): ?>
                                    <form
                                        action="<?= e(url(
                                            'educacion/desactivar'
                                        )) ?>"
                                        method="post"
                                        data-confirm="¿Deseas desactivar esta publicación?"
                                    >
                                        <?= csrf_field() ?>

                                        <input
                                            type="hidden"
                                            name="id_publicacion"
                                            value="<?= (int)
                                                $publication[
                                                    'id_publicacion'
                                                ] ?>"
                                        >

                                        <button
                                            class="btn btn-outline-danger"
                                            type="submit"
                                            title="Desactivar"
                                        >
                                            <i class="bi bi-x-circle"></i>
                                        </button>
                                    </form>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </article>
            </div>
        <?php endforeach; ?>
    </div>

    <?php if ($pages > 1): ?>
        <nav
            class="mt-4"
            aria-label="Páginas de publicaciones"
        >
            <ul class="pagination justify-content-center">
                <?php for (
                    $pageNumber = 1;
                    $pageNumber <= $pages;
                    $pageNumber++
                ): ?>
                    <li
                        class="page-item
                               <?= $pageNumber === $page
                                   ? 'active'
                                   : '' ?>"
                    >
                        <a
                            class="page-link"
                            href="<?= e(url(
                                'educacion',
                                [
                                    'buscar' => $search,
                                    'categoria' => $categoryId,
                                    'tipo' => $type,
                                    'estado' => $status,
                                    'pagina' => $pageNumber,
                                ]
                            )) ?>"
                        >
                            <?= $pageNumber ?>
                        </a>
                    </li>
                <?php endfor; ?>
            </ul>
        </nav>
    <?php endif; ?>

<?php else: ?>

    <div class="eco-card">
        <div class="eco-card-body text-center py-5">
            <i class="bi bi-journal-x display-5 text-muted-eco"></i>

            <h2 class="h5 fw-bold mt-3">
                No se encontraron publicaciones
            </h2>

            <p class="text-muted-eco mb-0">
                Cambia los filtros o registra un nuevo
                material educativo.
            </p>
        </div>
    </div>

<?php endif; ?>

</div>