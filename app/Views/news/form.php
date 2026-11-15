<?php
$publicationId = (int) (
    $publication['id_publicacion'] ?? 0
);

$selected = old(
    'categorias',
    $selectedCategories
);

if (!is_array($selected)) {
    $selected = [];
}

$selected = array_map(
    'intval',
    $selected
);

$formAction = $editing
    ? url('noticias/actualizar')
    : url('noticias/guardar');
?>

<div class="page-header">
    <div>
        <span class="section-label">
            Administración de contenido
        </span>

        <h1 class="page-title mt-1">
            <?= $editing
                ? 'Editar publicación'
                : 'Nueva publicación' ?>
        </h1>

        <p class="page-subtitle">
            Registra noticias, avisos, campañas
            y eventos ambientales.
        </p>
    </div>

    <a
        href="<?= e(url('noticias')) ?>"
        class="btn btn-eco-outline"
    >
        <i class="bi bi-arrow-left me-2"></i>
        Regresar
    </a>
</div>

<section class="eco-card">
    <div class="eco-card-body">
        <form
            action="<?= e($formAction) ?>"
            method="post"
            enctype="multipart/form-data"
            novalidate
        >
            <?= csrf_field() ?>

            <?php if ($editing): ?>

                <input
                    type="hidden"
                    name="id_publicacion"
                    value="<?= e($publicationId) ?>"
                >

            <?php endif; ?>

            <div class="row g-4">
                <div class="col-lg-8">
                    <label
                        for="titulo"
                        class="form-label"
                    >
                        Título *
                    </label>

                    <input
                        id="titulo"
                        type="text"
                        name="titulo"
                        class="form-control"
                        maxlength="150"
                        value="<?= e(old(
                            'titulo',
                            $publication['titulo'] ?? ''
                        )) ?>"
                        required
                        autofocus
                    >
                </div>

                <div class="col-lg-4">
                    <label
                        for="tipo_publicacion"
                        class="form-label"
                    >
                        Tipo de publicación *
                    </label>

                    <?php
                    $currentType = old(
                        'tipo_publicacion',
                        $publication[
                            'tipo_publicacion'
                        ] ?? 'noticia'
                    );
                    ?>

                    <select
                        id="tipo_publicacion"
                        name="tipo_publicacion"
                        class="form-select"
                        required
                    >
                        <?php foreach (
                            $typeLabels as $typeValue => $typeLabel
                        ): ?>

                            <option
                                value="<?= e($typeValue) ?>"
                                <?= selected(
                                    $currentType,
                                    $typeValue
                                ) ?>
                            >
                                <?= e($typeLabel) ?>
                            </option>

                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-12">
                    <label
                        for="resumen"
                        class="form-label"
                    >
                        Resumen
                    </label>

                    <textarea
                        id="resumen"
                        name="resumen"
                        class="form-control"
                        rows="3"
                        maxlength="255"
                        placeholder="Descripción corta para la tarjeta de la publicación"
                    ><?= e(old(
                        'resumen',
                        $publication['resumen'] ?? ''
                    )) ?></textarea>

                    <div class="form-text">
                        Máximo 255 caracteres.
                    </div>
                </div>

                <div class="col-12">
                    <label
                        for="imagen_portada"
                        class="form-label"
                    >
                        Imagen de portada
                    </label>

                    <input
                        id="imagen_portada"
                        type="file"
                        name="imagen_portada"
                        class="form-control"
                        accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp"
                    >

                    <div class="form-text">
                        JPG, PNG o WebP. Máximo 2 MB.
                        La imagen es opcional.
                    </div>
                </div>

                <?php if (
                    $editing
                    && !empty(
                        $publication['imagen_portada']
                    )
                ): ?>

                    <div class="col-12">
                        <div class="current-cover">
                            <div class="small
                                        text-muted-eco mb-2">
                                Portada actual
                            </div>

                            <img
                                src="<?= e(asset(
                                    $publication[
                                        'imagen_portada'
                                    ]
                                )) ?>"
                                alt="Portada actual"
                            >
                        </div>
                    </div>

                <?php endif; ?>

                <div class="col-12">
                    <label
                        for="contenido"
                        class="form-label"
                    >
                        Contenido *
                    </label>

                    <textarea
                        id="contenido"
                        name="contenido"
                        class="form-control"
                        rows="12"
                        required
                    ><?= e(old(
                        'contenido',
                        $publication['contenido'] ?? ''
                    )) ?></textarea>
                </div>

                <div class="col-lg-8">
                    <div class="form-label">
                        Categorías relacionadas
                    </div>

                    <?php if ($categories !== []): ?>

                        <div class="category-selector">
                            <?php foreach (
                                $categories as $category
                            ): ?>

                                <?php
                                $categoryId = (int) (
                                    $category['id_categoria']
                                );
                                ?>

                                <label class="category-option">
                                    <input
                                        type="checkbox"
                                        name="categorias[]"
                                        value="<?= e($categoryId) ?>"
                                        <?= in_array(
                                            $categoryId,
                                            $selected,
                                            true
                                        )
                                            ? 'checked'
                                            : '' ?>
                                    >

                                    <span>
                                        <i class="bi bi-recycle me-1"></i>
                                        <?= e(
                                            $category['nombre']
                                        ) ?>
                                    </span>
                                </label>

                            <?php endforeach; ?>
                        </div>

                    <?php else: ?>

                        <p class="text-muted-eco mb-0">
                            No existen categorías activas.
                        </p>

                    <?php endif; ?>
                </div>

                <div class="col-lg-4">
                    <label
                        for="estado"
                        class="form-label"
                    >
                        Estado *
                    </label>

                    <?php
                    $currentStatus = old(
                        'estado',
                        $publication['estado']
                        ?? 'borrador'
                    );
                    ?>

                    <select
                        id="estado"
                        name="estado"
                        class="form-select"
                        required
                    >
                        <option
                            value="borrador"
                            <?= selected(
                                $currentStatus,
                                'borrador'
                            ) ?>
                        >
                            Borrador
                        </option>

                        <option
                            value="publicado"
                            <?= selected(
                                $currentStatus,
                                'publicado'
                            ) ?>
                        >
                            Publicado
                        </option>

                        <option
                            value="inactivo"
                            <?= selected(
                                $currentStatus,
                                'inactivo'
                            ) ?>
                        >
                            Inactivo
                        </option>
                    </select>

                    <div class="form-text">
                        Los ciudadanos solamente pueden
                        consultar publicaciones con estado
                        Publicado.
                    </div>
                </div>
            </div>

            <div class="d-flex flex-wrap gap-2 mt-4">
                <button
                    type="submit"
                    class="btn btn-eco"
                >
                    <i class="bi bi-floppy me-2"></i>

                    <?= $editing
                        ? 'Guardar cambios'
                        : 'Registrar publicación' ?>
                </button>

                <a
                    href="<?= e(url('noticias')) ?>"
                    class="btn btn-eco-outline"
                >
                    Cancelar
                </a>
            </div>
        </form>
    </div>
</section>