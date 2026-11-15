<?php
/*
 | Aquí preparo las rutas registradas y recupero
 | los datos anteriores cuando ocurre una validación.
 */
$routeTitles = old(
    'ruta_titulo',
    array_map(
        static fn(array $route): string =>
            (string) ($route['title'] ?? ''),
        $game['routes'] ?? []
    )
);

$routeIcons = old(
    'ruta_icono',
    array_map(
        static fn(array $route): string =>
            (string) ($route['icon'] ?? ''),
        $game['routes'] ?? []
    )
);

$routeColors = old(
    'ruta_color',
    array_map(
        static fn(array $route): string =>
            (string) ($route['color'] ?? ''),
        $game['routes'] ?? []
    )
);

$routeDescriptions = old(
    'ruta_descripcion',
    array_map(
        static fn(array $route): string =>
            (string) ($route['description'] ?? ''),
        $game['routes'] ?? []
    )
);

$stageTitles = [];
$stageDescriptions = [];
$stageEmojis = [];

for ($stageNumber = 1; $stageNumber <= 5; $stageNumber++) {
    $stageIndex = $stageNumber - 1;

    $stageTitles[$stageNumber] = old(
        'etapa_' . $stageNumber . '_titulo',
        array_map(
            static fn(array $route): string =>
                (string) (
                    $route['stages'][$stageIndex]['title']
                    ?? ''
                ),
            $game['routes'] ?? []
        )
    );

    $stageDescriptions[$stageNumber] = old(
        'etapa_' . $stageNumber . '_descripcion',
        array_map(
            static fn(array $route): string =>
                (string) (
                    $route['stages'][$stageIndex]['description']
                    ?? ''
                ),
            $game['routes'] ?? []
        )
    );

    $stageEmojis[$stageNumber] = old(
        'etapa_' . $stageNumber . '_emoji',
        array_map(
            static fn(array $route): string =>
                (string) (
                    $route['stages'][$stageIndex]['emoji']
                    ?? ''
                ),
            $game['routes'] ?? []
        )
    );
}

$routeIconOptions = [
    'bi-recycle' => 'Reciclaje',
    'bi-cup-straw' => 'Envase',
    'bi-cup' => 'Vidrio',
    'bi-file-earmark-text-fill' => 'Papel',
    'bi-box-seam-fill' => 'Caja',
    'bi-phone-fill' => 'Electrónicos',
    'bi-battery-charging' => 'Baterías',
];

$routeColorOptions = [
    'blue' => 'Azul',
    'cyan' => 'Cian',
    'amber' => 'Ámbar',
];
?>

<section class="eco-card mb-4">
    <div class="eco-card-head">
        <div>
            <h2 class="eco-card-title">
                Rutas del reciclaje
            </h2>

            <span class="small text-muted-eco">
                Cada ruta contiene cinco etapas en su orden correcto.
            </span>
        </div>

        <button
            class="btn btn-eco"
            type="button"
            data-add-editor-row="recycling-route"
        >
            <i class="bi bi-plus-lg me-2"></i>
            Agregar ruta
        </button>
    </div>

    <div
        class="eco-card-body game-editor-list"
        data-editor-list="recycling-route"
    >
        <?php foreach (
            $routeTitles as $index => $routeTitle
        ): ?>
        <article class="game-editor-row game-editor-route">
            <div class="game-editor-row-number">
                <?= (int) ($index + 1) ?>
            </div>

            <div class="flex-grow-1">
                <div class="row g-3">
                    <div class="col-lg-5">
                        <label class="form-label">
                            Nombre de la ruta *
                        </label>

                        <input
                            class="form-control"
                            type="text"
                            name="ruta_titulo[]"
                            maxlength="100"
                            value="<?= e($routeTitle) ?>"
                            required
                        >
                    </div>

                    <div class="col-md-6 col-lg-4">
                        <label class="form-label">
                            Icono *
                        </label>

                        <select
                            class="form-select"
                            name="ruta_icono[]"
                            required
                        >
                            <?php foreach (
                                $routeIconOptions
                                as $value => $label
                            ): ?>
                            <option
                                value="<?= e($value) ?>"
                                <?= selected(
                                    $routeIcons[$index] ?? '',
                                    $value
                                ) ?>
                            >
                                <?= e($label) ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-6 col-lg-3">
                        <label class="form-label">
                            Color *
                        </label>

                        <select
                            class="form-select"
                            name="ruta_color[]"
                            required
                        >
                            <?php foreach (
                                $routeColorOptions
                                as $value => $label
                            ): ?>
                            <option
                                value="<?= e($value) ?>"
                                <?= selected(
                                    $routeColors[$index] ?? '',
                                    $value
                                ) ?>
                            >
                                <?= e($label) ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-12">
                        <label class="form-label">
                            Descripción de la ruta *
                        </label>

                        <textarea
                            class="form-control"
                            name="ruta_descripcion[]"
                            rows="2"
                            maxlength="250"
                            required
                        ><?= e(
                            $routeDescriptions[$index]
                            ?? ''
                        ) ?></textarea>
                    </div>
                </div>

                <div class="game-editor-stages mt-4">
                    <?php for (
                        $stageNumber = 1;
                        $stageNumber <= 5;
                        $stageNumber++
                    ): ?>
                    <div class="game-editor-stage">
                        <span class="game-editor-stage-number">
                            <?= (int) $stageNumber ?>
                        </span>

                        <div class="row g-3 flex-grow-1">
                            <div class="col-md-2">
                                <label class="form-label">
                                    Emoji *
                                </label>

                                <input
                                    class="form-control text-center"
                                    type="text"
                                    name="etapa_<?= (int) $stageNumber ?>_emoji[]"
                                    maxlength="12"
                                    value="<?= e(
                                        $stageEmojis[
                                            $stageNumber
                                        ][$index] ?? ''
                                    ) ?>"
                                    required
                                >
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">
                                    Etapa <?= (int) $stageNumber ?> *
                                </label>

                                <input
                                    class="form-control"
                                    type="text"
                                    name="etapa_<?= (int) $stageNumber ?>_titulo[]"
                                    maxlength="100"
                                    value="<?= e(
                                        $stageTitles[
                                            $stageNumber
                                        ][$index] ?? ''
                                    ) ?>"
                                    required
                                >
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">
                                    Descripción *
                                </label>

                                <input
                                    class="form-control"
                                    type="text"
                                    name="etapa_<?= (int) $stageNumber ?>_descripcion[]"
                                    maxlength="250"
                                    value="<?= e(
                                        $stageDescriptions[
                                            $stageNumber
                                        ][$index] ?? ''
                                    ) ?>"
                                    required
                                >
                            </div>
                        </div>
                    </div>
                    <?php endfor; ?>
                </div>
            </div>

            <button
                type="button"
                class="btn btn-outline-danger
                       game-editor-remove"
                data-remove-editor-row
                aria-label="Eliminar ruta"
            >
                <i class="bi bi-trash"></i>
            </button>
        </article>
        <?php endforeach; ?>
    </div>
</section>

<template data-editor-template="recycling-route">
    <article class="game-editor-row game-editor-route">
        <div class="game-editor-row-number"></div>

        <div class="flex-grow-1">
            <div class="row g-3">
                <div class="col-lg-5">
                    <label class="form-label">
                        Nombre de la ruta *
                    </label>

                    <input
                        class="form-control"
                        type="text"
                        name="ruta_titulo[]"
                        maxlength="100"
                        placeholder="Ruta del aluminio"
                        required
                    >
                </div>

                <div class="col-md-6 col-lg-4">
                    <label class="form-label">
                        Icono *
                    </label>

                    <select
                        class="form-select"
                        name="ruta_icono[]"
                        required
                    >
                        <?php foreach (
                            $routeIconOptions
                            as $value => $label
                        ): ?>
                        <option value="<?= e($value) ?>">
                            <?= e($label) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-6 col-lg-3">
                    <label class="form-label">
                        Color *
                    </label>

                    <select
                        class="form-select"
                        name="ruta_color[]"
                        required
                    >
                        <option value="blue">Azul</option>
                        <option value="cyan">Cian</option>
                        <option value="amber">Ámbar</option>
                    </select>
                </div>

                <div class="col-12">
                    <label class="form-label">
                        Descripción de la ruta *
                    </label>

                    <textarea
                        class="form-control"
                        name="ruta_descripcion[]"
                        rows="2"
                        maxlength="250"
                        required
                    ></textarea>
                </div>
            </div>

            <div class="game-editor-stages mt-4">
                <?php for (
                    $stageNumber = 1;
                    $stageNumber <= 5;
                    $stageNumber++
                ): ?>
                <div class="game-editor-stage">
                    <span class="game-editor-stage-number">
                        <?= (int) $stageNumber ?>
                    </span>

                    <div class="row g-3 flex-grow-1">
                        <div class="col-md-2">
                            <label class="form-label">
                                Emoji *
                            </label>

                            <input
                                class="form-control text-center"
                                type="text"
                                name="etapa_<?= (int) $stageNumber ?>_emoji[]"
                                maxlength="12"
                                placeholder="♻️"
                                required
                            >
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">
                                Etapa <?= (int) $stageNumber ?> *
                            </label>

                            <input
                                class="form-control"
                                type="text"
                                name="etapa_<?= (int) $stageNumber ?>_titulo[]"
                                maxlength="100"
                                required
                            >
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">
                                Descripción *
                            </label>

                            <input
                                class="form-control"
                                type="text"
                                name="etapa_<?= (int) $stageNumber ?>_descripcion[]"
                                maxlength="250"
                                required
                            >
                        </div>
                    </div>
                </div>
                <?php endfor; ?>
            </div>
        </div>

        <button
            type="button"
            class="btn btn-outline-danger
                   game-editor-remove"
            data-remove-editor-row
            aria-label="Eliminar ruta"
        >
            <i class="bi bi-trash"></i>
        </button>
    </article>
</template>