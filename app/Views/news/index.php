<div class="page-header">
    <div>
        <span class="section-label">
            Comunicación ambiental
        </span>

        <h1 class="page-title mt-1">
            Noticias y campañas
        </h1>

        <p class="page-subtitle">
            Consulta avisos, eventos y actividades ambientales.
        </p>
    </div>

    <?php if ($canManage): ?>

        <a
            href="<?= e(url('noticias/crear')) ?>"
            class="btn btn-eco"
        >
            <i class="bi bi-plus-lg me-2"></i>
            Nueva publicación
        </a>

    <?php endif; ?>
</div>

<section class="eco-card mb-4">
    <div class="eco-card-body">
        <form
            action="<?= e(url('noticias')) ?>"
            method="get"
            class="row g-3 align-items-end"
        >
            <!--
                Los formularios GET reemplazan la consulta actual.
                Por eso vuelvo a enviar el nombre de la ruta.
            -->
            <input
                type="hidden"
                name="ruta"
                value="noticias"
            >

            <div class="col-xl-4 col-md-6">
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
                    placeholder="Título, tema o palabra clave"
                >
            </div>

            <div class="col-xl-3 col-md-6">
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
                        Todos los tipos
                    </option>

                    <?php foreach (
                        $typeLabels as $typeValue => $typeLabel
                    ): ?>

                        <option
                            value="<?= e($typeValue) ?>"
                            <?= selected($type, $typeValue) ?>
                        >
                            <?= e($typeLabel) ?>
                        </option>

                    <?php endforeach; ?>
                </select>
            </div>

            <?php if ($canManage): ?>

                <div class="col-xl-3 col-md-6">
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
                            Todos los estados
                        </option>

                        <option
                            value="borrador"
                            <?= selected(
                                $status,
                                'borrador'
                            ) ?>
                        >
                            Borrador
                        </option>

                        <option
                            value="publicado"
                            <?= selected(
                                $status,
                                'publicado'
                            ) ?>
                        >
                            Publicado
                        </option>

                        <option
                            value="inactivo"
                            <?= selected(
                                $status,
                                'inactivo'
                            ) ?>
                        >
                            Inactivo
                        </option>
                    </select>
                </div>

            <?php endif; ?>

            <div class="col-xl-2 col-md-6">
                <div class="d-flex gap-2">
                    <button
                        type="submit"
                        class="btn btn-eco flex-grow-1"
                        aria-label="Buscar"
                    >
                        <i class="bi bi-search"></i>
                    </button>

                    <a
                        href="<?= e(url('noticias')) ?>"
                        class="btn btn-eco-outline"
                        aria-label="Limpiar filtros"
                    >
                        <i class="bi bi-arrow-counterclockwise"></i>
                    </a>
                </div>
            </div>
        </form>
    </div>
</section>

<div class="d-flex justify-content-between
            align-items-center gap-3 mb-3">
    <strong>
        <?= e($total) ?>
        <?= $total === 1
            ? 'publicación encontrada'
            : 'publicaciones encontradas' ?>
    </strong>
</div>

<?php if ($publications !== []): ?>

    
<div class="row g-4">

    <?php foreach ($publications as $publication): ?>

        <?php
        /*
         | Convierto las categorías separadas por comas
         | en una lista para mostrarlas como etiquetas.
         */
        $publicationCategories = [];

        if (!empty($publication['nombres_categorias'])) {
            $publicationCategories = preg_split(
                '/\s*,\s*/',
                (string) $publication['nombres_categorias'],
                -1,
                PREG_SPLIT_NO_EMPTY
            );
        }
        ?>

        <div class="col-md-6 col-xl-4">
            <article
                class="eco-card publication-card h-100
                       d-flex flex-column overflow-hidden"
            >
                <div class="publication-cover">
                    <?php if (
                        !empty($publication['imagen_portada'])
                    ): ?>

                        <img
                            src="<?= e(asset(
                                $publication['imagen_portada']
                            )) ?>"
                            alt="Portada de <?= e(
                                $publication['titulo']
                            ) ?>"
                            loading="lazy"
                        >

                    <?php else: ?>

                        <div class="publication-placeholder">
                            <i class="bi bi-megaphone"></i>
                        </div>

                    <?php endif; ?>
                </div>

                <div
                    class="eco-card-body d-flex
                           flex-column flex-grow-1"
                >
                    <div
                        class="d-flex flex-wrap
                               justify-content-between
                               align-items-center gap-2"
                    >
                        <span class="chip">
                            <i class="bi bi-megaphone"></i>

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
                                <?= e(match (
                                    $publication['estado']
                                ) {
                                    'publicado' => 'Publicado',
                                    'inactivo' => 'Inactivo',
                                    default => 'Borrador',
                                }) ?>
                            </span>

                        <?php endif; ?>
                    </div>

                    <h2 class="h5 fw-bold mt-3">
                        <?= e($publication['titulo']) ?>
                    </h2>

                    <p
                        class="text-muted-eco
                               publication-summary"
                    >
                        <?= e(
                            $publication['resumen']
                            ?: 'Consulta el contenido completo de esta publicación.'
                        ) ?>
                    </p>

                    <?php if (
                        $publicationCategories !== []
                    ): ?>

                        <div
                            class="d-flex flex-wrap
                                   gap-1 mb-3"
                        >
                            <?php foreach (
                                $publicationCategories
                                as $categoryName
                            ): ?>

                                <span class="chip small">
                                    <i class="bi bi-tag"></i>
                                    <?= e($categoryName) ?>
                                </span>

                            <?php endforeach; ?>
                        </div>

                    <?php endif; ?>

                    <div
                        class="small text-muted-eco
                               mb-3"
                    >
                        <i class="bi bi-person me-1"></i>

                        <?= e(
                            $publication['nombre_autor']
                        ) ?>

                        <span class="mx-1">·</span>

                        <i class="bi bi-calendar3 me-1"></i>

                        <?= e(date_mx(
                            $publication['fecha_publicacion']
                            ?: $publication['fecha_creacion']
                        )) ?>
                    </div>

                    <!--
                        Mantengo las acciones al final para que
                        las tarjetas conserven la misma altura.
                    -->
                    <div
                        class="d-flex flex-wrap
                               gap-2 mt-auto"
                    >
                        <a
                            href="<?= e(url(
                                'noticias/detalle',
                                [
                                    'id' => $publication[
                                        'id_publicacion'
                                    ],
                                ]
                            )) ?>"
                            class="btn btn-light-eco"
                        >
                            <i class="bi bi-eye me-1"></i>
                            Ver detalle
                        </a>

                        <?php if ($canManage): ?>

                            <a
                                href="<?= e(url(
                                    'noticias/editar',
                                    [
                                        'id' => $publication[
                                            'id_publicacion'
                                        ],
                                    ]
                                )) ?>"
                                class="btn btn-eco-outline"
                                title="Editar publicación"
                                aria-label="Editar publicación"
                            >
                                <i class="bi bi-pencil"></i>
                            </a>

                            <?php if (
                                $publication['estado']
                                !== 'inactivo'
                            ): ?>

                                <form
                                    action="<?= e(url(
                                        'noticias/desactivar'
                                    )) ?>"
                                    method="post"
                                    class="m-0"
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
                                        type="submit"
                                        class="btn
                                               btn-outline-danger"
                                        title="Desactivar publicación"
                                        aria-label="Desactivar publicación"
                                    >
                                        <i
                                            class="bi
                                                   bi-x-circle"
                                        ></i>
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



<?php else: ?>

    <section class="eco-card">
        <div class="eco-card-body text-center py-5">
            <i class="bi bi-newspaper
                      display-5 text-eco"></i>

            <h2 class="h4 mt-3">
                No se encontraron publicaciones
            </h2>

            <p class="text-muted-eco mb-0">
                Prueba con otros filtros o registra
                una nueva publicación.
            </p>
        </div>
    </section>

<?php endif; ?>

<?php if ($pages > 1): ?>

    <?php
    $filterParameters = [];

    if ($search !== '') {
        $filterParameters['buscar'] = $search;
    }

    if ($type !== '') {
        $filterParameters['tipo'] = $type;
    }

    if ($status !== '') {
        $filterParameters['estado'] = $status;
    }
    ?>

    <nav
        class="mt-4"
        aria-label="Paginación de publicaciones"
    >
        <ul class="pagination justify-content-center">

            <li class="page-item
                <?= $page <= 1 ? 'disabled' : '' ?>">
                <a
                    class="page-link"
                    href="<?= e(url(
                        'noticias',
                        array_merge(
                            $filterParameters,
                            [
                                'pagina' => max(
                                    1,
                                    $page - 1
                                ),
                            ]
                        )
                    )) ?>"
                >
                    Anterior
                </a>
            </li>

            <?php for (
                $pageNumber = 1;
                $pageNumber <= $pages;
                $pageNumber++
            ): ?>

                <li class="page-item
                    <?= $pageNumber === $page
                        ? 'active'
                        : '' ?>">
                    <a
                        class="page-link"
                        href="<?= e(url(
                            'noticias',
                            array_merge(
                                $filterParameters,
                                [
                                    'pagina' => $pageNumber,
                                ]
                            )
                        )) ?>"
                    >
                        <?= e($pageNumber) ?>
                    </a>
                </li>

            <?php endfor; ?>

            <li class="page-item
                <?= $page >= $pages ? 'disabled' : '' ?>">
                <a
                    class="page-link"
                    href="<?= e(url(
                        'noticias',
                        array_merge(
                            $filterParameters,
                            [
                                'pagina' => min(
                                    $pages,
                                    $page + 1
                                ),
                            ]
                        )
                    )) ?>"
                >
                    Siguiente
                </a>
            </li>
        </ul>
    </nav>

<?php endif; ?>