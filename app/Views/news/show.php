<div class="page-header">
    <div>
        <span class="section-label">
            <?= e(
                $typeLabels[
                    $publication['tipo_publicacion']
                ] ?? 'Publicación'
            ) ?>
        </span>

        <h1 class="page-title mt-1">
            <?= e($publication['titulo']) ?>
        </h1>

        <?php if (
            !empty($publication['resumen'])
        ): ?>

            <p class="page-subtitle">
                <?= e($publication['resumen']) ?>
            </p>

        <?php endif; ?>
    </div>

    <div class="d-flex flex-wrap gap-2">
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
                class="btn btn-eco"
            >
                <i class="bi bi-pencil me-2"></i>
                Editar
            </a>

        <?php endif; ?>

        <a
            href="<?= e(url('noticias')) ?>"
            class="btn btn-eco-outline"
        >
            <i class="bi bi-arrow-left me-2"></i>
            Regresar
        </a>
    </div>
</div>

<article class="eco-card">
    <?php if (
        !empty($publication['imagen_portada'])
    ): ?>

        <div class="publication-detail-cover">
            <img
                src="<?= e(asset(
                    $publication['imagen_portada']
                )) ?>"
                alt="Portada de <?= e(
                    $publication['titulo']
                ) ?>"
            >
        </div>

    <?php endif; ?>

    <div class="eco-card-body">
        <div class="d-flex flex-wrap gap-2 mb-4">
            <span class="chip">
                <i class="bi bi-megaphone"></i>

                <?= e(
                    $typeLabels[
                        $publication['tipo_publicacion']
                    ] ?? 'Publicación'
                ) ?>
            </span>

            <?php if ($canManage): ?>

                <span class="status-pill
                    status-<?= e(
                        $publication['estado']
                    ) ?>">
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

        <div class="d-flex flex-wrap
                    gap-3 text-muted-eco mb-4">
            <span>
                <i class="bi bi-person me-1"></i>
                <?= e(
                    $publication['nombre_autor']
                ) ?>
            </span>

            <span>
                <i class="bi bi-calendar3 me-1"></i>

                <?= e(date_mx(
                    $publication['fecha_publicacion']
                    ?: $publication['fecha_creacion']
                )) ?>
            </span>
        </div>

        <div class="publication-content">
            <?= nl2br(
                e($publication['contenido'])
            ) ?>
        </div>

        <?php if ($categories !== []): ?>

            <hr class="my-4">

            <h2 class="h5 fw-bold mb-3">
                Categorías relacionadas
            </h2>

            <div class="d-flex flex-wrap gap-2">
                <?php foreach (
                    $categories as $category
                ): ?>

                    <span class="chip">
                        <i class="bi bi-recycle"></i>
                        <?= e($category['nombre']) ?>
                    </span>

                <?php endforeach; ?>
            </div>

        <?php endif; ?>
    </div>
</article>