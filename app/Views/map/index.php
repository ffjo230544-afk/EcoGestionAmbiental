<div class="page-header">
    <div>
        <span class="section-label">
            Ubicación de servicios ambientales
        </span>

        <h1 class="page-title mt-1">
            Mapa interactivo
        </h1>

        <p class="page-subtitle">
            Localiza centros de acopio,
            empresas y puntos de servicio.
        </p>
    </div>

    <a class="btn btn-eco-outline" href="<?= e(url('centros')) ?>">
        <i class="bi bi-list-ul me-2"></i>
        Ver listado
    </a>
</div>

<section class="eco-card mb-4">
    <div class="eco-card-body">
        <form action="<?= e(url('mapa')) ?>" method="get" class="row g-3 align-items-end">
            <!--
                Los formularios GET reemplazan
                los parámetros de la dirección.
            -->
            <input type="hidden" name="ruta" value="mapa">

            <div class="col-xl-4 col-md-6">
                <label for="buscar" class="form-label">
                    Buscar ubicación
                </label>

                <input id="buscar" type="search" name="buscar" class="form-control" value="<?= e($search) ?>"
                    placeholder="Nombre, dirección o municipio">
            </div>

            <div class="col-xl-2 col-md-6">
                <label for="tipo" class="form-label">
                    Tipo de punto
                </label>

                <select id="tipo" name="tipo" class="form-select">
                    <option value="">
                        Todos
                    </option>

                    <?php foreach (
                        $pointTypes as $value => $label
                    ): ?>
                    <option value="<?= e($value) ?>" <?= selected(
                                $type,
                                $value
                            ) ?>>
                        <?= e($label) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-xl-2 col-md-6">
                <label for="servicio" class="form-label">
                    Servicio
                </label>

                <select id="servicio" name="servicio" class="form-select">
                    <option value="">
                        Todos
                    </option>

                    <?php foreach (
                        $serviceTypes
                        as $value => $label
                    ): ?>
                    <option value="<?= e($value) ?>" <?= selected(
                                $service,
                                $value
                            ) ?>>
                        <?= e($label) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-xl-2 col-md-6">
                <label for="categoria" class="form-label">
                    Material
                </label>

                <select id="categoria" name="categoria" class="form-select">
                    <option value="0">
                        Todos
                    </option>

                    <?php foreach (
                        $categories as $category
                    ): ?>
                    <option value="<?= (int) $category[
                                'id_categoria'
                            ] ?>" <?= selected(
                                $categoryId,
                                $category[
                                    'id_categoria'
                                ]
                            ) ?>>
                        <?= e(
                                $category['nombre']
                            ) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <?php if ($canManage): ?>
            <div class="col-xl-2 col-md-6">
                <label for="estado" class="form-label">
                    Estado
                </label>

                <select id="estado" name="estado" class="form-select">
                    <option value="">
                        Todos
                    </option>

                    <option value="activo" <?= selected(
                                $status,
                                'activo'
                            ) ?>>
                        Activos
                    </option>

                    <option value="inactivo" <?= selected(
                                $status,
                                'inactivo'
                            ) ?>>
                        Inactivos
                    </option>
                </select>
            </div>
            <?php endif; ?>

            <div class="col-12 d-flex flex-wrap
                       gap-2 justify-content-end">
                <a class="btn btn-light" href="<?= e(url('mapa')) ?>">
                    Limpiar filtros
                </a>

                <button class="btn btn-eco" type="submit">
                    <i class="bi bi-funnel me-2"></i>
                    Aplicar filtros
                </button>
            </div>
        </form>
    </div>
</section>

<div class="row g-4 map-layout">
    <!--
        En teléfono la lista aparece después del mapa.
        En laptop queda a la izquierda.
    -->
    <div class="col-xl-4
               order-2 order-xl-1">
        <section class="eco-card h-100">
            <div class="eco-card-head">
                <div>
                    <h2 class="eco-card-title">
                        Puntos encontrados
                    </h2>

                    <span class="small text-muted-eco">
                        <?= count($records) ?>
                        ubicaciones en el mapa
                    </span>
                </div>
            </div>

            <div class="map-results-list">
                <?php if ($records !== []): ?>

                <?php foreach (
                        $records as $record
                    ): ?>

                <button type="button" class="map-result-item" data-map-point-id="<?= (int) $record[
                                'id_punto'
                            ] ?>">
                    <span class="map-result-icon
                                       map-result-icon-<?= e(
                                           $record['estado']
                                       ) ?>">
                        <i class="bi
                                           bi-geo-alt-fill"></i>
                    </span>

                    <span class="map-result-content">
                        <strong>
                            <?= e(
                                        $record['nombre']
                                    ) ?>
                        </strong>

                        <small>
                            <?= e(
                                        $pointTypes[
                                            $record[
                                                'tipo_punto'
                                            ]
                                        ]
                                        ?? 'Punto de atención'
                                    ) ?>
                        </small>

                        <small>
                            <?= e(
                                        $record[
                                            'direccion'
                                        ]
                                    ) ?>
                        </small>

                        <?php if (
                                    !empty(
                                        $record[
                                            'materiales'
                                        ]
                                    )
                                ): ?>
                        <small class="map-result-materials">
                            <i class="bi
                                                   bi-recycle"></i>

                            <?= e(
                                            $record[
                                                'materiales'
                                            ]
                                        ) ?>
                        </small>
                        <?php endif; ?>
                    </span>

                    <i class="bi bi-chevron-right
                                       map-result-arrow"></i>
                </button>

                <?php endforeach; ?>

                <?php else: ?>

                <div class="empty-state py-5">
                    <i class="bi bi-geo-alt"></i>

                    <h3 class="h6">
                        No hay ubicaciones
                    </h3>

                    <p>
                        Modifica los filtros o registra
                        las coordenadas de los centros.
                    </p>
                </div>

                <?php endif; ?>
            </div>
        </section>
    </div>

    <div class="col-xl-8
               order-1 order-xl-2">
        <section class="eco-card map-card">
            <div class="eco-card-head">
                <div>
                    <h2 class="eco-card-title">
                        Ubicaciones
                    </h2>

                    <span class="small text-muted-eco">
                        Selecciona un marcador para
                        consultar sus datos.
                    </span>
                </div>

                <button type="button" class="btn
                           btn-eco-outline
                           btn-sm" data-map-locate>
                    <i class="bi bi-crosshair me-1"></i>
                    Mi ubicación
                </button>
            </div>

            <div class="eco-card-body
                       p-0 position-relative">
                <div id="centers-map" class="eco-map" aria-label="Mapa de centros de acopio"></div>

                <div class="map-message d-none" data-map-message></div>
            </div>
        </section>
    </div>
</div>

<?php if ($withoutCoordinates > 0): ?>
<div class="stage-note mt-4">
    <i class="bi bi-info-circle-fill"></i>

    <div>
        Hay
        <?= (int) $withoutCoordinates ?>

        <?= $withoutCoordinates === 1
                ? 'punto'
                : 'puntos' ?>

        sin coordenadas completas.

        No aparecerán en el mapa hasta que
        se registre su latitud y longitud
        desde Centros de acopio.
    </div>
</div>
<?php endif; ?>

<!--
    Envío la información a JavaScript como JSON.
    JSON_HEX evita que los textos puedan cerrar
    o alterar esta etiqueta.
-->
<script id="map-points-data" type="application/json">
<?=
    json_encode(
        $points,
        JSON_UNESCAPED_UNICODE
        | JSON_UNESCAPED_SLASHES
        | JSON_HEX_TAG
        | JSON_HEX_AMP
        | JSON_HEX_APOS
        | JSON_HEX_QUOT
    )
?>
</script>