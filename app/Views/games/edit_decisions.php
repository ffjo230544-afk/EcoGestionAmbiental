<?php
$scenarioRooms = old(
    'escenario_habitacion',
    array_map(
        static fn(array $scenario): string =>
            (string) ($scenario['room'] ?? ''),
        $game['scenarios'] ?? []
    )
);

$scenarioIcons = old(
    'escenario_icono',
    array_map(
        static fn(array $scenario): string =>
            (string) ($scenario['icon'] ?? ''),
        $game['scenarios'] ?? []
    )
);

$scenarioQuestions = old(
    'escenario_pregunta',
    array_map(
        static fn(array $scenario): string =>
            (string) ($scenario['question'] ?? ''),
        $game['scenarios'] ?? []
    )
);

$decisionData = [];

foreach (['a', 'b', 'c'] as $optionIndex => $letter) {
    $decisionData[$letter] = [
        'labels' => old(
            'decision_' . $letter . '_etiqueta',
            array_map(
                static fn(array $scenario): string =>
                    (string) (
                        $scenario['options'][$optionIndex]['label']
                        ?? ''
                    ),
                $game['scenarios'] ?? []
            )
        ),

        'impacts' => old(
            'decision_' . $letter . '_impacto',
            array_map(
                static fn(array $scenario): int =>
                    (int) (
                        $scenario['options'][$optionIndex]['impact']
                        ?? 0
                    ),
                $game['scenarios'] ?? []
            )
        ),

        'feedback' => old(
            'decision_' . $letter . '_retroalimentacion',
            array_map(
                static fn(array $scenario): string =>
                    (string) (
                        $scenario['options'][$optionIndex]['feedback']
                        ?? ''
                    ),
                $game['scenarios'] ?? []
            )
        ),
    ];
}

$scenarioIconOptions = [
    'bi-droplet-fill' => 'Agua',
    'bi-lightbulb-fill' => 'Energía',
    'bi-basket2-fill' => 'Lavandería',
    'bi-bag-heart-fill' => 'Compras',
    'bi-egg-fried' => 'Cocina',
    'bi-plug-fill' => 'Electricidad',
    'bi-wrench-adjustable-circle-fill' => 'Reparación',
    'bi-thermometer-sun' => 'Temperatura',
    'bi-tree-fill' => 'Naturaleza',
    'bi-recycle' => 'Reciclaje',
];
?>

<section class="eco-card mb-4">
    <div class="eco-card-head">
        <div>
            <h2 class="eco-card-title">
                Situaciones de EcoDecisiones
            </h2>

            <span class="small text-muted-eco">
                Cada situación debe tener una decisión negativa,
                una intermedia y una positiva.
            </span>
        </div>

        <button
            class="btn btn-eco"
            type="button"
            data-add-editor-row="decisions"
        >
            <i class="bi bi-plus-lg me-2"></i>
            Agregar situación
        </button>
    </div>

    <div
        class="eco-card-body game-editor-list"
        data-editor-list="decisions"
    >
        <?php foreach (
            $scenarioQuestions as $index => $question
        ): ?>
        <article class="game-editor-row game-editor-question">
            <div class="game-editor-row-number">
                <?= (int) ($index + 1) ?>
            </div>

            <div class="flex-grow-1">
                <div class="row g-3">
                    <div class="col-md-5">
                        <label class="form-label">
                            Habitación o ubicación *
                        </label>

                        <input
                            class="form-control"
                            type="text"
                            name="escenario_habitacion[]"
                            maxlength="60"
                            value="<?= e(
                                $scenarioRooms[$index]
                                ?? ''
                            ) ?>"
                            required
                        >
                    </div>

                    <div class="col-md-7">
                        <label class="form-label">
                            Icono *
                        </label>

                        <select
                            class="form-select"
                            name="escenario_icono[]"
                            required
                        >
                            <?php foreach (
                                $scenarioIconOptions
                                as $value => $label
                            ): ?>
                            <option
                                value="<?= e($value) ?>"
                                <?= selected(
                                    $scenarioIcons[$index]
                                        ?? '',
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
                            Situación o pregunta *
                        </label>

                        <textarea
                            class="form-control"
                            name="escenario_pregunta[]"
                            rows="2"
                            maxlength="220"
                            required
                        ><?= e($question) ?></textarea>
                    </div>
                </div>

                <div class="game-editor-decisions mt-4">
                    <?php foreach (
                        ['a', 'b', 'c'] as $letter
                    ): ?>
                    <section class="game-editor-decision">
                        <span class="game-editor-option-letter">
                            <?= e(strtoupper($letter)) ?>
                        </span>

                        <div class="row g-3 flex-grow-1">
                            <div class="col-lg-7">
                                <label class="form-label">
                                    Decisión *
                                </label>

                                <input
                                    class="form-control"
                                    type="text"
                                    name="decision_<?= e($letter) ?>_etiqueta[]"
                                    maxlength="180"
                                    value="<?= e(
                                        $decisionData[
                                            $letter
                                        ]['labels'][$index]
                                        ?? ''
                                    ) ?>"
                                    required
                                >
                            </div>

                            <div class="col-lg-5">
                                <label class="form-label">
                                    Nivel de impacto *
                                </label>

                                <select
                                    class="form-select"
                                    name="decision_<?= e($letter) ?>_impacto[]"
                                    required
                                >
                                    <option
                                        value="0"
                                        <?= selected(
                                            $decisionData[
                                                $letter
                                            ]['impacts'][$index]
                                            ?? '',
                                            0
                                        ) ?>
                                    >
                                        0 — Impacto negativo
                                    </option>

                                    <option
                                        value="1"
                                        <?= selected(
                                            $decisionData[
                                                $letter
                                            ]['impacts'][$index]
                                            ?? '',
                                            1
                                        ) ?>
                                    >
                                        1 — Opción intermedia
                                    </option>

                                    <option
                                        value="2"
                                        <?= selected(
                                            $decisionData[
                                                $letter
                                            ]['impacts'][$index]
                                            ?? '',
                                            2
                                        ) ?>
                                    >
                                        2 — Mejor decisión
                                    </option>
                                </select>
                            </div>

                            <div class="col-12">
                                <label class="form-label">
                                    Retroalimentación *
                                </label>

                                <textarea
                                    class="form-control"
                                    name="decision_<?= e($letter) ?>_retroalimentacion[]"
                                    rows="2"
                                    maxlength="350"
                                    required
                                ><?= e(
                                    $decisionData[
                                        $letter
                                    ]['feedback'][$index]
                                    ?? ''
                                ) ?></textarea>
                            </div>
                        </div>
                    </section>
                    <?php endforeach; ?>
                </div>
            </div>

            <button
                type="button"
                class="btn btn-outline-danger
                       game-editor-remove"
                data-remove-editor-row
                aria-label="Eliminar situación"
            >
                <i class="bi bi-trash"></i>
            </button>
        </article>
        <?php endforeach; ?>
    </div>
</section>

<template data-editor-template="decisions">
    <article class="game-editor-row game-editor-question">
        <div class="game-editor-row-number"></div>

        <div class="flex-grow-1">
            <div class="row g-3">
                <div class="col-md-5">
                    <label class="form-label">
                        Habitación o ubicación *
                    </label>

                    <input
                        class="form-control"
                        type="text"
                        name="escenario_habitacion[]"
                        maxlength="60"
                        placeholder="Jardín"
                        required
                    >
                </div>

                <div class="col-md-7">
                    <label class="form-label">
                        Icono *
                    </label>

                    <select
                        class="form-select"
                        name="escenario_icono[]"
                        required
                    >
                        <?php foreach (
                            $scenarioIconOptions
                            as $value => $label
                        ): ?>
                        <option value="<?= e($value) ?>">
                            <?= e($label) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-12">
                    <label class="form-label">
                        Situación o pregunta *
                    </label>

                    <textarea
                        class="form-control"
                        name="escenario_pregunta[]"
                        rows="2"
                        maxlength="220"
                        required
                    ></textarea>
                </div>
            </div>

            <div class="game-editor-decisions mt-4">
                <?php foreach (
                    ['a', 'b', 'c'] as $letter
                ): ?>
                <section class="game-editor-decision">
                    <span class="game-editor-option-letter">
                        <?= e(strtoupper($letter)) ?>
                    </span>

                    <div class="row g-3 flex-grow-1">
                        <div class="col-lg-7">
                            <label class="form-label">
                                Decisión *
                            </label>

                            <input
                                class="form-control"
                                type="text"
                                name="decision_<?= e($letter) ?>_etiqueta[]"
                                maxlength="180"
                                required
                            >
                        </div>

                        <div class="col-lg-5">
                            <label class="form-label">
                                Nivel de impacto *
                            </label>

                            <select
                                class="form-select"
                                name="decision_<?= e($letter) ?>_impacto[]"
                                required
                            >
                                <option
                                    value="<?= $letter === 'a'
                                        ? '0'
                                        : ($letter === 'b' ? '1' : '2') ?>"
                                >
                                    <?= $letter === 'a'
                                        ? '0 — Impacto negativo'
                                        : (
                                            $letter === 'b'
                                            ? '1 — Opción intermedia'
                                            : '2 — Mejor decisión'
                                        ) ?>
                                </option>

                                <?php foreach ([0, 1, 2] as $impact): ?>
                                    <?php if (
                                        (string) $impact
                                        !== (
                                            $letter === 'a'
                                            ? '0'
                                            : (
                                                $letter === 'b'
                                                ? '1'
                                                : '2'
                                            )
                                        )
                                    ): ?>
                                    <option value="<?= (int) $impact ?>">
                                        <?= (int) $impact ?>
                                    </option>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-12">
                            <label class="form-label">
                                Retroalimentación *
                            </label>

                            <textarea
                                class="form-control"
                                name="decision_<?= e($letter) ?>_retroalimentacion[]"
                                rows="2"
                                maxlength="350"
                                required
                            ></textarea>
                        </div>
                    </div>
                </section>
                <?php endforeach; ?>
            </div>
        </div>

        <button
            type="button"
            class="btn btn-outline-danger
                   game-editor-remove"
            data-remove-editor-row
            aria-label="Eliminar situación"
        >
            <i class="bi bi-trash"></i>
        </button>
    </article>
</template>