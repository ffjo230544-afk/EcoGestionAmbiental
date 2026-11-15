<?php
$level = old(
    'nivel',
    $activity['nivel']
);

$status = old(
    'estado',
    $activity['estado']
);

$categoryNames = old(
    'categoria_nombre',
    array_map(
        static fn(array $category): string =>
            (string) $category['label'],
        $game['categories'] ?? []
    )
);

$itemNames = old(
    'residuo_nombre',
    array_column(
        $game['items'] ?? [],
        'name'
    )
);

$itemEmojis = old(
    'residuo_emoji',
    array_column(
        $game['items'] ?? [],
        'emoji'
    )
);

$itemCategories = old(
    'residuo_categoria',
    array_column(
        $game['items'] ?? [],
        'category'
    )
);

$questionTexts = old(
    'pregunta_texto',
    array_column(
        $game['questions'] ?? [],
        'question'
    )
);

$optionA = [];
$optionB = [];
$optionC = [];
$correctAnswers = [];
$explanations = [];

foreach ($game['questions'] ?? [] as $question) {
    $optionA[] = $question['options']['a'] ?? '';
    $optionB[] = $question['options']['b'] ?? '';
    $optionC[] = $question['options']['c'] ?? '';
    $correctAnswers[] = $question['correct'] ?? '';
    $explanations[] = $question['explanation'] ?? '';
}

$optionA = old('opcion_a', $optionA);
$optionB = old('opcion_b', $optionB);
$optionC = old('opcion_c', $optionC);

$correctAnswers = old(
    'respuesta_correcta',
    $correctAnswers
);

$explanations = old(
    'explicacion',
    $explanations
);


/*
 | Aquí preparo los valores del memorama
 | para conservarlos si el formulario produce un error.
 */
$pairFirstLabels = old(
    'pareja_primera_etiqueta',
    array_map(
        static fn(array $pair): string =>
            (string) (
                $pair['first']['label']
                ?? ''
            ),
        $game['pairs'] ?? []
    )
);

$pairFirstEmojis = old(
    'pareja_primer_emoji',
    array_map(
        static fn(array $pair): string =>
            (string) (
                $pair['first']['emoji']
                ?? ''
            ),
        $game['pairs'] ?? []
    )
);

$pairSecondLabels = old(
    'pareja_segunda_etiqueta',
    array_map(
        static fn(array $pair): string =>
            (string) (
                $pair['second']['label']
                ?? ''
            ),
        $game['pairs'] ?? []
    )
);

$pairSecondEmojis = old(
    'pareja_segundo_emoji',
    array_map(
        static fn(array $pair): string =>
            (string) (
                $pair['second']['emoji']
                ?? ''
            ),
        $game['pairs'] ?? []
    )
);

$pairExplanations = old(
    'pareja_explicacion',
    array_map(
        static fn(array $pair): string =>
            (string) (
                $pair['explanation']
                ?? ''
            ),
        $game['pairs'] ?? []
    )
);

/*
 | Aquí preparo los objetos que aparecen
 | dentro del escenario del parque.
 */
$objectLabels = old(
    'objeto_etiqueta',
    array_map(
        static fn(array $object): string =>
            (string) ($object['label'] ?? ''),
        $game['objects'] ?? []
    )
);

$objectEmojis = old(
    'objeto_emoji',
    array_map(
        static fn(array $object): string =>
            (string) ($object['emoji'] ?? ''),
        $game['objects'] ?? []
    )
);

$objectTypes = old(
    'objeto_tipo',
    array_map(
        static fn(array $object): string =>
            (string) ($object['type'] ?? ''),
        $game['objects'] ?? []
    )
);

$objectPositionsX = old(
    'objeto_x',
    array_map(
        static fn(array $object): int =>
            (int) ($object['x'] ?? 50),
        $game['objects'] ?? []
    )
);

$objectPositionsY = old(
    'objeto_y',
    array_map(
        static fn(array $object): int =>
            (int) ($object['y'] ?? 50),
        $game['objects'] ?? []
    )
);

$objectTips = old(
    'objeto_consejo',
    array_map(
        static fn(array $object): string =>
            (string) ($object['tip'] ?? ''),
        $game['objects'] ?? []
    )
);




?>






<div class="page-header">
    <div>
        <span class="section-label">
            Administración educativa
        </span>

        <h1 class="page-title mt-1">
            Editar <?= e($game['title']) ?>
        </h1>

        <p class="page-subtitle">
            Modifica el contenido que los usuarios encuentran
            al realizar esta actividad.
        </p>
    </div>

    <a
        class="btn btn-eco-outline"
        href="<?= e(url('juegos')) ?>"
    >
        <i class="bi bi-arrow-left me-2"></i>
        Regresar
    </a>
</div>

<form
    action="<?= e(url('juegos/actualizar')) ?>"
    method="post"
    data-game-editor
>
    <?= csrf_field() ?>

    <input
        type="hidden"
        name="juego"
        value="<?= e($slug) ?>"
    >

    <section class="eco-card mb-4">
        <div class="eco-card-head">
            <div>
                <h2 class="eco-card-title">
                    Información general
                </h2>

                <span class="small text-muted-eco">
                    Estos datos aparecen en el catálogo de juegos.
                </span>
            </div>

            <i class="bi bi-controller fs-3 text-eco"></i>
        </div>

        <div class="eco-card-body">
            <div class="row g-4">
                <div class="col-lg-8">
                    <label
                        for="titulo"
                        class="form-label"
                    >
                        Título visible *
                    </label>

                    <input
                        id="titulo"
                        name="titulo"
                        type="text"
                        class="form-control"
                        maxlength="100"
                        value="<?= e(
                            old(
                                'titulo',
                                $game['title']
                            )
                        ) ?>"
                        required
                    >
                </div>

                <div class="col-md-6 col-lg-2">
                    <label
                        for="nivel"
                        class="form-label"
                    >
                        Dificultad *
                    </label>

                    <select
                        id="nivel"
                        name="nivel"
                        class="form-select"
                        required
                    >
                        <option
                            value="basico"
                            <?= selected($level, 'basico') ?>
                        >
                            Básico
                        </option>

                        <option
                            value="intermedio"
                            <?= selected($level, 'intermedio') ?>
                        >
                            Intermedio
                        </option>

                        <option
                            value="avanzado"
                            <?= selected($level, 'avanzado') ?>
                        >
                            Avanzado
                        </option>
                    </select>
                </div>

                <div class="col-md-6 col-lg-2">
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
                        <option
                            value="activa"
                            <?= selected($status, 'activa') ?>
                        >
                            Activa
                        </option>

                        <option
                            value="inactiva"
                            <?= selected($status, 'inactiva') ?>
                        >
                            Inactiva
                        </option>
                    </select>
                </div>

                <div class="col-12">
                    <label
                        for="descripcion"
                        class="form-label"
                    >
                        Descripción *
                    </label>

                    <textarea
                        id="descripcion"
                        name="descripcion"
                        class="form-control"
                        rows="3"
                        maxlength="250"
                        required
                    ><?= e(
                        old(
                            'descripcion',
                            $game['short_description']
                        )
                    ) ?></textarea>
                </div>

                <div class="col-12">
                    <label
                        for="aprendizaje"
                        class="form-label"
                    >
                        Aprendizaje principal *
                    </label>

                    <textarea
                        id="aprendizaje"
                        name="aprendizaje"
                        class="form-control"
                        rows="3"
                        maxlength="350"
                        required
                    ><?= e(
                        old(
                            'aprendizaje',
                            $game['learning']
                        )
                    ) ?></textarea>

                    <div class="form-text">
                        Explica qué aprenderá el usuario al completar
                        la actividad.
                    </div>
                </div>
            </div>
        </div>
    </section>

    <?php if ($slug === 'clasifica'): ?>

    <section class="eco-card mb-4">
        <div class="eco-card-head">
            <div>
                <h2 class="eco-card-title">
                    Categorías de clasificación
                </h2>

                <span class="small text-muted-eco">
                    El identificador y el color se mantienen
                    para conservar el funcionamiento visual.
                </span>
            </div>
        </div>

        <div class="eco-card-body">
            <div class="row g-3">
                <?php foreach (
                    $game['categories'] as $key => $category
                ): ?>
                <div class="col-md-6 col-xl-4">
                    <div class="game-editor-category">
                        <span class="game-editor-category-icon">
                            <i class="bi <?= e(
                                $category['icon']
                            ) ?>"></i>
                        </span>

                        <div class="flex-grow-1">
                            <label
                                class="form-label"
                                for="category_<?= e($key) ?>"
                            >
                                Nombre de la categoría
                            </label>

                            <input
                                id="category_<?= e($key) ?>"
                                type="text"
                                class="form-control"
                                name="categoria_nombre[<?= e($key) ?>]"
                                maxlength="60"
                                value="<?= e(
                                    $categoryNames[$key]
                                    ?? $category['label']
                                ) ?>"
                                required
                            >
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <section class="eco-card mb-4">
        <div class="eco-card-head">
            <div>
                <h2 class="eco-card-title">
                    Residuos del juego
                </h2>

                <span class="small text-muted-eco">
                    Deben permanecer al menos cuatro residuos.
                </span>
            </div>

            <button
                class="btn btn-eco"
                type="button"
                data-add-editor-row="classification"
            >
                <i class="bi bi-plus-lg me-2"></i>
                Agregar residuo
            </button>
        </div>

        <div
            class="eco-card-body game-editor-list"
            data-editor-list="classification"
        >
            <?php foreach ($itemNames as $index => $name): ?>
            <article class="game-editor-row">
                <div class="game-editor-row-number">
                    <?= (int) ($index + 1) ?>
                </div>

                <div class="row g-3 flex-grow-1">
                    <div class="col-md-2">
                        <label class="form-label">
                            Emoji *
                        </label>

                        <input
                            type="text"
                            class="form-control text-center"
                            name="residuo_emoji[]"
                            maxlength="12"
                            value="<?= e(
                                $itemEmojis[$index] ?? ''
                            ) ?>"
                            required
                        >
                    </div>

                    <div class="col-md-5">
                        <label class="form-label">
                            Residuo *
                        </label>

                        <input
                            type="text"
                            class="form-control"
                            name="residuo_nombre[]"
                            maxlength="80"
                            value="<?= e($name) ?>"
                            required
                        >
                    </div>

                    <div class="col-md-5">
                        <label class="form-label">
                            Categoría correcta *
                        </label>

                        <select
                            class="form-select"
                            name="residuo_categoria[]"
                            required
                        >
                            <?php foreach (
                                $game['categories']
                                as $key => $category
                            ): ?>
                            <option
                                value="<?= e($key) ?>"
                                <?= selected(
                                    $itemCategories[$index]
                                        ?? '',
                                    $key
                                ) ?>
                            >
                                <?= e($category['label']) ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <button
                    type="button"
                    class="btn btn-outline-danger
                           game-editor-remove"
                    data-remove-editor-row
                    aria-label="Eliminar residuo"
                >
                    <i class="bi bi-trash"></i>
                </button>
            </article>
            <?php endforeach; ?>
        </div>
    </section>

    <template data-editor-template="classification">
        <article class="game-editor-row">
            <div class="game-editor-row-number"></div>

            <div class="row g-3 flex-grow-1">
                <div class="col-md-2">
                    <label class="form-label">
                        Emoji *
                    </label>

                    <input
                        type="text"
                        class="form-control text-center"
                        name="residuo_emoji[]"
                        maxlength="12"
                        placeholder="♻️"
                        required
                    >
                </div>

                <div class="col-md-5">
                    <label class="form-label">
                        Residuo *
                    </label>

                    <input
                        type="text"
                        class="form-control"
                        name="residuo_nombre[]"
                        maxlength="80"
                        placeholder="Nombre del residuo"
                        required
                    >
                </div>

                <div class="col-md-5">
                    <label class="form-label">
                        Categoría correcta *
                    </label>

                    <select
                        class="form-select"
                        name="residuo_categoria[]"
                        required
                    >
                        <?php foreach (
                            $game['categories']
                            as $key => $category
                        ): ?>
                        <option value="<?= e($key) ?>">
                            <?= e($category['label']) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <button
                type="button"
                class="btn btn-outline-danger
                       game-editor-remove"
                data-remove-editor-row
                aria-label="Eliminar residuo"
            >
                <i class="bi bi-trash"></i>
            </button>
        </article>
    </template>

    <?php endif; ?>

    <?php if ($slug === 'trivia'): ?>

    <section class="eco-card mb-4">
        <div class="eco-card-head">
            <div>
                <h2 class="eco-card-title">
                    Preguntas de la trivia
                </h2>

                <span class="small text-muted-eco">
                    Deben permanecer al menos tres preguntas.
                </span>
            </div>

            <button
                class="btn btn-eco"
                type="button"
                data-add-editor-row="trivia"
            >
                <i class="bi bi-plus-lg me-2"></i>
                Agregar pregunta
            </button>
        </div>

        <div
            class="eco-card-body game-editor-list"
            data-editor-list="trivia"
        >
            <?php foreach (
                $questionTexts as $index => $question
            ): ?>
            <article class="game-editor-row game-editor-question">
                <div class="game-editor-row-number">
                    <?= (int) ($index + 1) ?>
                </div>

                <div class="row g-3 flex-grow-1">
                    <div class="col-12">
                        <label class="form-label">
                            Pregunta *
                        </label>

                        <textarea
                            class="form-control"
                            name="pregunta_texto[]"
                            rows="2"
                            maxlength="200"
                            required
                        ><?= e($question) ?></textarea>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">
                            Opción A *
                        </label>

                        <input
                            type="text"
                            class="form-control"
                            name="opcion_a[]"
                            maxlength="160"
                            value="<?= e(
                                $optionA[$index] ?? ''
                            ) ?>"
                            required
                        >
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">
                            Opción B *
                        </label>

                        <input
                            type="text"
                            class="form-control"
                            name="opcion_b[]"
                            maxlength="160"
                            value="<?= e(
                                $optionB[$index] ?? ''
                            ) ?>"
                            required
                        >
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">
                            Opción C *
                        </label>

                        <input
                            type="text"
                            class="form-control"
                            name="opcion_c[]"
                            maxlength="160"
                            value="<?= e(
                                $optionC[$index] ?? ''
                            ) ?>"
                            required
                        >
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">
                            Respuesta correcta *
                        </label>

                        <select
                            class="form-select"
                            name="respuesta_correcta[]"
                            required
                        >
                            <option
                                value="a"
                                <?= selected(
                                    $correctAnswers[$index]
                                        ?? '',
                                    'a'
                                ) ?>
                            >
                                Opción A
                            </option>

                            <option
                                value="b"
                                <?= selected(
                                    $correctAnswers[$index]
                                        ?? '',
                                    'b'
                                ) ?>
                            >
                                Opción B
                            </option>

                            <option
                                value="c"
                                <?= selected(
                                    $correctAnswers[$index]
                                        ?? '',
                                    'c'
                                ) ?>
                            >
                                Opción C
                            </option>
                        </select>
                    </div>

                    <div class="col-md-8">
                        <label class="form-label">
                            Explicación educativa *
                        </label>

                        <textarea
                            class="form-control"
                            name="explicacion[]"
                            rows="3"
                            maxlength="350"
                            required
                        ><?= e(
                            $explanations[$index] ?? ''
                        ) ?></textarea>
                    </div>
                </div>

                <button
                    type="button"
                    class="btn btn-outline-danger
                           game-editor-remove"
                    data-remove-editor-row
                    aria-label="Eliminar pregunta"
                >
                    <i class="bi bi-trash"></i>
                </button>
            </article>
            <?php endforeach; ?>
        </div>
    </section>

    <template data-editor-template="trivia">
        <article class="game-editor-row game-editor-question">
            <div class="game-editor-row-number"></div>

            <div class="row g-3 flex-grow-1">
                <div class="col-12">
                    <label class="form-label">
                        Pregunta *
                    </label>

                    <textarea
                        class="form-control"
                        name="pregunta_texto[]"
                        rows="2"
                        maxlength="200"
                        required
                    ></textarea>
                </div>

                <div class="col-md-4">
                    <label class="form-label">
                        Opción A *
                    </label>

                    <input
                        type="text"
                        class="form-control"
                        name="opcion_a[]"
                        maxlength="160"
                        required
                    >
                </div>

                <div class="col-md-4">
                    <label class="form-label">
                        Opción B *
                    </label>

                    <input
                        type="text"
                        class="form-control"
                        name="opcion_b[]"
                        maxlength="160"
                        required
                    >
                </div>

                <div class="col-md-4">
                    <label class="form-label">
                        Opción C *
                    </label>

                    <input
                        type="text"
                        class="form-control"
                        name="opcion_c[]"
                        maxlength="160"
                        required
                    >
                </div>

                <div class="col-md-4">
                    <label class="form-label">
                        Respuesta correcta *
                    </label>

                    <select
                        class="form-select"
                        name="respuesta_correcta[]"
                        required
                    >
                        <option value="a">Opción A</option>
                        <option value="b">Opción B</option>
                        <option value="c">Opción C</option>
                    </select>
                </div>

                <div class="col-md-8">
                    <label class="form-label">
                        Explicación educativa *
                    </label>

                    <textarea
                        class="form-control"
                        name="explicacion[]"
                        rows="3"
                        maxlength="350"
                        required
                    ></textarea>
                </div>
            </div>

            <button
                type="button"
                class="btn btn-outline-danger
                       game-editor-remove"
                data-remove-editor-row
                aria-label="Eliminar pregunta"
            >
                <i class="bi bi-trash"></i>
            </button>
        </article>
    </template>

    <?php endif; ?>




    <?php if ($slug === 'memorama'): ?>

<section class="eco-card mb-4">
    <div class="eco-card-head">
        <div>
            <h2 class="eco-card-title">
                Parejas del memorama
            </h2>

            <span class="small text-muted-eco">
                Cada pareja relaciona dos conceptos ambientales.
                Deben permanecer al menos tres parejas.
            </span>
        </div>

        <button
            class="btn btn-eco"
            type="button"
            data-add-editor-row="memory"
        >
            <i class="bi bi-plus-lg me-2"></i>
            Agregar pareja
        </button>
    </div>

    <div
        class="eco-card-body game-editor-list"
        data-editor-list="memory"
    >
        <?php foreach (
            $pairFirstLabels as $index => $firstLabel
        ): ?>
        <article class="game-editor-row game-editor-question">
            <div class="game-editor-row-number">
                <?= (int) ($index + 1) ?>
            </div>

            <div class="row g-3 flex-grow-1">
                <div class="col-12">
                    <div class="game-instruction-banner">
                        <span class="game-instruction-number">
                            1
                        </span>

                        <div>
                            <strong>Primera tarjeta</strong>
                            <p>
                                Puede ser el residuo o concepto inicial.
                            </p>
                        </div>

                        <i class="bi bi-arrow-left-right
                                  instruction-arrow"></i>

                        <span class="game-instruction-number">
                            2
                        </span>

                        <div>
                            <strong>Segunda tarjeta</strong>
                            <p>
                                Debe ser su destino o relación correcta.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="col-md-2">
                    <label class="form-label">
                        Emoji 1 *
                    </label>

                    <input
                        type="text"
                        class="form-control text-center"
                        name="pareja_primer_emoji[]"
                        maxlength="12"
                        value="<?= e(
                            $pairFirstEmojis[$index]
                            ?? ''
                        ) ?>"
                        required
                    >
                </div>

                <div class="col-md-4">
                    <label class="form-label">
                        Primera tarjeta *
                    </label>

                    <input
                        type="text"
                        class="form-control"
                        name="pareja_primera_etiqueta[]"
                        maxlength="80"
                        value="<?= e($firstLabel) ?>"
                        required
                    >
                </div>

                <div class="col-md-2">
                    <label class="form-label">
                        Emoji 2 *
                    </label>

                    <input
                        type="text"
                        class="form-control text-center"
                        name="pareja_segundo_emoji[]"
                        maxlength="12"
                        value="<?= e(
                            $pairSecondEmojis[$index]
                            ?? ''
                        ) ?>"
                        required
                    >
                </div>

                <div class="col-md-4">
                    <label class="form-label">
                        Segunda tarjeta *
                    </label>

                    <input
                        type="text"
                        class="form-control"
                        name="pareja_segunda_etiqueta[]"
                        maxlength="80"
                        value="<?= e(
                            $pairSecondLabels[$index]
                            ?? ''
                        ) ?>"
                        required
                    >
                </div>

                <div class="col-12">
                    <label class="form-label">
                        Explicación educativa *
                    </label>

                    <textarea
                        class="form-control"
                        name="pareja_explicacion[]"
                        rows="3"
                        maxlength="350"
                        required
                    ><?= e(
                        $pairExplanations[$index]
                        ?? ''
                    ) ?></textarea>

                    <div class="form-text">
                        Esta explicación aparece cuando el usuario
                        completa correctamente la pareja.
                    </div>
                </div>
            </div>

            <button
                type="button"
                class="btn btn-outline-danger
                       game-editor-remove"
                data-remove-editor-row
                aria-label="Eliminar pareja"
            >
                <i class="bi bi-trash"></i>
            </button>
        </article>
        <?php endforeach; ?>
    </div>
</section>

<template data-editor-template="memory">
    <article class="game-editor-row game-editor-question">
        <div class="game-editor-row-number"></div>

        <div class="row g-3 flex-grow-1">
            <div class="col-12">
                <div class="game-instruction-banner">
                    <span class="game-instruction-number">
                        1
                    </span>

                    <div>
                        <strong>Primera tarjeta</strong>
                        <p>Residuo o concepto inicial.</p>
                    </div>

                    <i class="bi bi-arrow-left-right
                              instruction-arrow"></i>

                    <span class="game-instruction-number">
                        2
                    </span>

                    <div>
                        <strong>Segunda tarjeta</strong>
                        <p>Destino o relación correcta.</p>
                    </div>
                </div>
            </div>

            <div class="col-md-2">
                <label class="form-label">
                    Emoji 1 *
                </label>

                <input
                    type="text"
                    class="form-control text-center"
                    name="pareja_primer_emoji[]"
                    maxlength="12"
                    placeholder="🧴"
                    required
                >
            </div>

            <div class="col-md-4">
                <label class="form-label">
                    Primera tarjeta *
                </label>

                <input
                    type="text"
                    class="form-control"
                    name="pareja_primera_etiqueta[]"
                    maxlength="80"
                    placeholder="Botella de plástico"
                    required
                >
            </div>

            <div class="col-md-2">
                <label class="form-label">
                    Emoji 2 *
                </label>

                <input
                    type="text"
                    class="form-control text-center"
                    name="pareja_segundo_emoji[]"
                    maxlength="12"
                    placeholder="♻️"
                    required
                >
            </div>

            <div class="col-md-4">
                <label class="form-label">
                    Segunda tarjeta *
                </label>

                <input
                    type="text"
                    class="form-control"
                    name="pareja_segunda_etiqueta[]"
                    maxlength="80"
                    placeholder="Contenedor de plástico"
                    required
                >
            </div>

            <div class="col-12">
                <label class="form-label">
                    Explicación educativa *
                </label>

                <textarea
                    class="form-control"
                    name="pareja_explicacion[]"
                    rows="3"
                    maxlength="350"
                    placeholder="Explica por qué ambos elementos forman una pareja."
                    required
                ></textarea>
            </div>
        </div>

        <button
            type="button"
            class="btn btn-outline-danger
                   game-editor-remove"
            data-remove-editor-row
            aria-label="Eliminar pareja"
        >
            <i class="bi bi-trash"></i>
        </button>
    </article>
</template>

<?php endif; ?>






<?php if ($slug === 'limpia-parque'): ?>

<section class="eco-card mb-4">
    <div class="eco-card-head">
        <div>
            <h2 class="eco-card-title">
                Objetos del parque
            </h2>

            <span class="small text-muted-eco">
                Agrega residuos que deben recogerse y elementos
                naturales que deben protegerse.
            </span>
        </div>

        <button
            class="btn btn-eco"
            type="button"
            data-add-editor-row="park"
        >
            <i class="bi bi-plus-lg me-2"></i>
            Agregar objeto
        </button>
    </div>

    <div class="eco-card-body">
        <div class="alert alert-success border-0 mb-4">
            <div class="d-flex gap-3">
                <i class="bi bi-info-circle-fill fs-4"></i>

                <div>
                    <strong>Posiciones del escenario</strong>

                    <p class="mb-0 mt-1">
                        X controla la posición horizontal y Y la
                        posición vertical. Utiliza valores de 5 a 95.
                    </p>
                </div>
            </div>
        </div>

        <div
            class="game-editor-list"
            data-editor-list="park"
        >
            <?php foreach (
                $objectLabels as $index => $label
            ): ?>
            <article class="game-editor-row game-editor-question">
                <div class="game-editor-row-number">
                    <?= (int) ($index + 1) ?>
                </div>

                <div class="row g-3 flex-grow-1">
                    <div class="col-md-2">
                        <label class="form-label">
                            Emoji *
                        </label>

                        <input
                            type="text"
                            class="form-control text-center"
                            name="objeto_emoji[]"
                            maxlength="12"
                            value="<?= e(
                                $objectEmojis[$index]
                                ?? ''
                            ) ?>"
                            required
                        >
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">
                            Nombre del objeto *
                        </label>

                        <input
                            type="text"
                            class="form-control"
                            name="objeto_etiqueta[]"
                            maxlength="80"
                            value="<?= e($label) ?>"
                            required
                        >
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">
                            Tipo *
                        </label>

                        <select
                            class="form-select"
                            name="objeto_tipo[]"
                            required
                        >
                            <option
                                value="waste"
                                <?= selected(
                                    $objectTypes[$index]
                                        ?? '',
                                    'waste'
                                ) ?>
                            >
                                Residuo
                            </option>

                            <option
                                value="nature"
                                <?= selected(
                                    $objectTypes[$index]
                                        ?? '',
                                    'nature'
                                ) ?>
                            >
                                Elemento natural
                            </option>
                        </select>
                    </div>

                    <div class="col-6 col-md-1">
                        <label class="form-label">
                            X *
                        </label>

                        <input
                            type="number"
                            class="form-control"
                            name="objeto_x[]"
                            min="5"
                            max="95"
                            value="<?= (int) (
                                $objectPositionsX[$index]
                                ?? 50
                            ) ?>"
                            required
                        >
                    </div>

                    <div class="col-6 col-md-1">
                        <label class="form-label">
                            Y *
                        </label>

                        <input
                            type="number"
                            class="form-control"
                            name="objeto_y[]"
                            min="5"
                            max="95"
                            value="<?= (int) (
                                $objectPositionsY[$index]
                                ?? 50
                            ) ?>"
                            required
                        >
                    </div>

                    <div class="col-12">
                        <label class="form-label">
                            Consejo para elemento natural
                        </label>

                        <textarea
                            class="form-control"
                            name="objeto_consejo[]"
                            rows="2"
                            maxlength="300"
                            placeholder="Es obligatorio cuando el objeto es un elemento natural."
                        ><?= e(
                            $objectTips[$index]
                            ?? ''
                        ) ?></textarea>

                        <div class="form-text">
                            Para los residuos puede dejarse vacío.
                            Para los elementos naturales es obligatorio.
                        </div>
                    </div>
                </div>

                <button
                    type="button"
                    class="btn btn-outline-danger
                           game-editor-remove"
                    data-remove-editor-row
                    aria-label="Eliminar objeto"
                >
                    <i class="bi bi-trash"></i>
                </button>
            </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<template data-editor-template="park">
    <article class="game-editor-row game-editor-question">
        <div class="game-editor-row-number"></div>

        <div class="row g-3 flex-grow-1">
            <div class="col-md-2">
                <label class="form-label">
                    Emoji *
                </label>

                <input
                    type="text"
                    class="form-control text-center"
                    name="objeto_emoji[]"
                    maxlength="12"
                    placeholder="🧴"
                    required
                >
            </div>

            <div class="col-md-4">
                <label class="form-label">
                    Nombre del objeto *
                </label>

                <input
                    type="text"
                    class="form-control"
                    name="objeto_etiqueta[]"
                    maxlength="80"
                    placeholder="Nombre visible"
                    required
                >
            </div>

            <div class="col-md-3">
                <label class="form-label">
                    Tipo *
                </label>

                <select
                    class="form-select"
                    name="objeto_tipo[]"
                    required
                >
                    <option value="waste">
                        Residuo
                    </option>

                    <option value="nature">
                        Elemento natural
                    </option>
                </select>
            </div>

            <div class="col-6 col-md-1">
                <label class="form-label">
                    X *
                </label>

                <input
                    type="number"
                    class="form-control"
                    name="objeto_x[]"
                    min="5"
                    max="95"
                    value="50"
                    required
                >
            </div>

            <div class="col-6 col-md-1">
                <label class="form-label">
                    Y *
                </label>

                <input
                    type="number"
                    class="form-control"
                    name="objeto_y[]"
                    min="5"
                    max="95"
                    value="50"
                    required
                >
            </div>

            <div class="col-12">
                <label class="form-label">
                    Consejo para elemento natural
                </label>

                <textarea
                    class="form-control"
                    name="objeto_consejo[]"
                    rows="2"
                    maxlength="300"
                    placeholder="Explica por qué debe protegerse."
                ></textarea>
            </div>
        </div>

        <button
            type="button"
            class="btn btn-outline-danger
                   game-editor-remove"
            data-remove-editor-row
            aria-label="Eliminar objeto"
        >
            <i class="bi bi-trash"></i>
        </button>
    </article>
</template>

<?php endif; ?>





    <div class="game-editor-save">
        <div>
            <strong>Guardar cambios del juego</strong>

            <span>
                El contenido será validado antes de utilizarse.
            </span>
        </div>

        <button
            class="btn btn-eco btn-lg"
            type="submit"
        >
            <i class="bi bi-floppy me-2"></i>
            Guardar configuración
        </button>
    </div>
</form>