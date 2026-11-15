<?php
$oldCategories = old(
    'categorias',
    $selectedCategories
);

if (!is_array($oldCategories)) {
    $oldCategories = [];
}

$oldCategories = array_map(
    'intval',
    $oldCategories
);
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
            Registra materiales educativos y fichas
            informativas para la ciudadanía.
        </p>
    </div>

    <a
        class="btn btn-eco-outline"
        href="<?= e(
            $editing
                ? url(
                    'educacion/detalle',
                    [
                        'id' =>
                            $publication[
                                'id_publicacion'
                            ],
                    ]
                )
                : url('educacion')
        ) ?>"
    >
        <i class="bi bi-arrow-left me-2"></i>
        Regresar
    </a>
</div>

<form
    action="<?= e(url(
        $editing
            ? 'educacion/actualizar'
            : 'educacion/guardar'
    )) ?>"
    method="post"
    enctype="multipart/form-data"
    class="eco-card"
>
    <?= csrf_field() ?>

    <?php if ($editing): ?>
        <input
            type="hidden"
            name="id_publicacion"
            value="<?= (int)
                $publication['id_publicacion'] ?>"
        >
    <?php endif; ?>

    <div class="eco-card-body">
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

                <select
                    id="tipo_publicacion"
                    name="tipo_publicacion"
                    class="form-select"
                    required
                >
                    <?php foreach (
                        $typeLabels as $value => $label
                    ): ?>
                        <?php
                        $selectedType = (string) old(
                            'tipo_publicacion',
                            $publication[
                                'tipo_publicacion'
                            ] ?? 'material_educativo'
                        );
                        ?>

                        <option
                            value="<?= e($value) ?>"
                            <?= $selectedType === $value
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
                    for="resumen"
                    class="form-label"
                >
                    Resumen
                </label>

                <input
                    id="resumen"
                    type="text"
                    name="resumen"
                    class="form-control"
                    maxlength="255"
                    value="<?= e(old(
                        'resumen',
                        $publication['resumen'] ?? ''
                    )) ?>"
                    placeholder="Descripción corta que aparecerá en la tarjeta"
                >
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
                    accept="image/jpeg,image/png,image/webp"
                >

                <div class="form-text">
                    JPG, PNG o WebP. Máximo 2 MB.
                    La imagen es opcional.
                </div>

                <?php if (
                    $editing
                    && !empty(
                        $publication['imagen_portada']
                    )
                ): ?>
                    <div class="mt-3">
                        <img
                            src="<?= e(asset(
                                $publication[
                                    'imagen_portada'
                                ]
                            )) ?>"
                            class="knowledge-current-cover"
                            alt="Portada actual"
                        >

                        <div class="small text-muted-eco mt-2">
                            Si no seleccionas otra imagen,
                            se conservará la actual.
                        </div>
                    </div>
                <?php endif; ?>
            </div>

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
                    maxlength="50000"
                    required
                ><?= e(old(
                    'contenido',
                    $publication['contenido'] ?? ''
                )) ?></textarea>

                <div class="form-text">
                    El contenido se mostrará como texto
                    seguro. Los saltos de línea se conservarán.
                </div>
            </div>

            <div class="col-lg-6">
                <label
                    for="estado"
                    class="form-label"
                >
                    Estado *
                </label>

                <select
                    id="estado"
                    name="estado"
                    class="form-select"
                    required
                >
                    <?php foreach (
                        $statusLabels as $value => $label
                    ): ?>
                        <?php
                        $selectedStatus = (string) old(
                            'estado',
                            $publication['estado']
                                ?? 'borrador'
                        );
                        ?>

                        <option
                            value="<?= e($value) ?>"
                            <?= $selectedStatus === $value
                                ? 'selected'
                                : '' ?>
                        >
                            <?= e($label) ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <div class="form-text">
                    Borrador e inactivo no se mostrarán
                    al ciudadano.
                </div>
            </div>

            <div class="col-12">
                <label class="form-label">
                    Categorías relacionadas *
                </label>

                <?php if ($categories !== []): ?>
                    <div class="row g-2">
                        <?php foreach (
                            $categories as $category
                        ): ?>
                            <?php
                            $categoryId = (int)
                                $category['id_categoria'];

                            $isAvailable =
                                $category['estado']
                                    === 'activo'
                                && (int) $category[
                                    'visible_base_conocimiento'
                                ] === 1;
                            ?>

                            <div class="col-md-6 col-xl-4">
                                <label class="eco-choice w-100">
                                    <input
                                        type="checkbox"
                                        name="categorias[]"
                                        value="<?= $categoryId ?>"
                                        <?= in_array(
                                            $categoryId,
                                            $oldCategories,
                                            true
                                        )
                                            ? 'checked'
                                            : '' ?>
                                    >

                                    <span>
                                        <?= e(
                                            $category['nombre']
                                        ) ?>

                                        <?php if (
                                            !$isAvailable
                                        ): ?>
                                            <small class="text-warning">
                                                (actualmente inactiva)
                                            </small>
                                        <?php endif; ?>
                                    </span>
                                </label>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="alert alert-warning mb-0">
                        Primero debes activar al menos una
                        categoría y marcarla como visible en
                        la base de conocimiento.
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="eco-card-head justify-content-end">
        <button
            type="submit"
            class="btn btn-eco"
            <?= $categories === []
                ? 'disabled'
                : '' ?>
        >
            <i class="bi bi-check-circle me-2"></i>

            <?= $editing
                ? 'Guardar cambios'
                : 'Crear publicación' ?>
        </button>
    </div>
</form>