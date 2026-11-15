<?php
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

<div class="page-header">
    <div>
        <span class="section-label">
            <?= e(
                $typeLabels[
                    $publication['tipo_publicacion']
                ] ?? 'Educación ambiental'
            ) ?>
        </span>

        <h1 class="page-title mt-1">
            <?= e($publication['titulo']) ?>
        </h1>

        <?php if (!empty($publication['resumen'])): ?>
            <p class="page-subtitle">
                <?= e($publication['resumen']) ?>
            </p>
        <?php endif; ?>
    </div>

    <div class="d-flex flex-wrap gap-2">
        <?php if ($canManage): ?>
            <a
                class="btn btn-eco"
                href="<?= e(url(
                    'educacion/editar',
                    [
                        'id' =>
                            $publication['id_publicacion'],
                    ]
                )) ?>"
            >
                <i class="bi bi-pencil me-2"></i>
                Editar
            </a>
        <?php endif; ?>

        <a
            class="btn btn-eco-outline"
            href="<?= e(url('educacion')) ?>"
        >
            <i class="bi bi-arrow-left me-2"></i>
            Regresar
        </a>
    </div>
</div>

<article class="eco-card overflow-hidden">
    <?php if (!empty($publication['imagen_portada'])): ?>
        <img
            src="<?= e(asset(
                $publication['imagen_portada']
            )) ?>"
            class="knowledge-detail-cover"
            alt="Portada de <?= e(
                $publication['titulo']
            ) ?>"
        >
    <?php endif; ?>

    <div class="eco-card-body">
        <div class="d-flex flex-wrap gap-2 mb-4">
            <span class="chip">
                <i class="bi bi-book"></i>

                <?= e(
                    $typeLabels[
                        $publication['tipo_publicacion']
                    ] ?? 'Publicación'
                ) ?>
            </span>

            <?php foreach ($categories as $category): ?>
                <?php
                $color = preg_match(
                    '/^#[0-9a-fA-F]{6}$/',
                    (string) $category['color_hex']
                )
                    ? $category['color_hex']
                    : '#198754';
                ?>

                <span
                    class="chip"
                    style="border-left: 4px solid <?= e(
                        $color
                    ) ?>;"
                >
                    <i class="bi bi-tag"></i>
                    <?= e($category['nombre']) ?>
                </span>
            <?php endforeach; ?>

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

        <div class="small text-muted-eco mb-4">
            <i class="bi bi-person me-1"></i>
            <?= e($publication['autor']) ?>

            <span class="mx-2">·</span>

            <i class="bi bi-calendar3 me-1"></i>
            <?= e($formattedDate) ?>
        </div>

        <div class="knowledge-content">
            <?= nl2br(e($publication['contenido'])) ?>
        </div>
    </div>
</article>