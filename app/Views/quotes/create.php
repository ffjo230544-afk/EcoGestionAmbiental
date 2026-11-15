<?php
/*
 | Recupero los valores anteriores cuando
 | el formulario regresa por una validación.
 */
$oldPointId = (int) old(
    'id_punto',
    0
);

$oldCategoryId = (int) old(
    'id_categoria',
    0
);
?>

<div class="page-header">
    <div>
        <span class="section-label">
            Mis servicios
        </span>

        <h1 class="page-title mt-1">
            Cotizar materiales
        </h1>

        <p class="page-subtitle">
            Selecciona un centro y calcula el valor
            estimado de tus materiales reciclables.
        </p>
    </div>

    <a
        class="btn btn-eco-outline"
        href="<?= e(url('cotizaciones')) ?>"
    >
        <i class="bi bi-clock-history me-2"></i>
        Mis cotizaciones
    </a>
</div>

<?php if ($rates === []): ?>

    <section class="eco-card">
        <div class="p-5 text-center">
            <i
                class="bi bi-calculator
                       fs-1 text-eco"
            ></i>

            <h2 class="h5 mt-3">
                No hay tarifas disponibles
            </h2>

            <p class="text-muted-eco mb-0">
                En este momento ningún centro tiene
                tarifas activas y vigentes para cotizar.
            </p>
        </div>
    </section>

<?php else: ?>

    <form
        action="<?= e(url(
            'cotizaciones/guardar'
        )) ?>"
        method="post"
        class="row g-4"
        data-quote-form
    >
        <?= csrf_field() ?>

        <div class="col-xl-7">
            <section class="eco-card h-100">
                <div class="eco-card-head">
                    <h2 class="eco-card-title">
                        Información del material
                    </h2>
                </div>

                <div class="eco-card-body">
                    <div class="row g-4">

                        <!-- Centro de compra -->
                        <div class="col-12">
                            <label
                                for="id_punto"
                                class="form-label"
                            >
                                Centro de compra *
                            </label>

                            <select
                                id="id_punto"
                                name="id_punto"
                                class="form-select"
                                data-quote-center
                                required
                            >
                                <option value="">
                                    Selecciona un centro
                                </option>

                                <?php foreach (
                                    $centers as $center
                                ): ?>

                                    <option
                                        value="<?= (int) $center[
                                            'id_punto'
                                        ] ?>"
                                        <?= selected(
                                            $oldPointId,
                                            $center['id_punto']
                                        ) ?>
                                    >
                                        <?= e($center['nombre']) ?>

                                        <?php if (
                                            !empty(
                                                $center['municipio']
                                            )
                                        ): ?>
                                            —
                                            <?= e(
                                                $center['municipio']
                                            ) ?>
                                        <?php endif; ?>
                                    </option>

                                <?php endforeach; ?>
                            </select>

                            <div class="form-text">
                                Cada centro maneja sus propios
                                materiales y precios.
                            </div>
                        </div>

                        <!-- Categoría -->
                        <div class="col-12">
                            <label
                                for="id_categoria"
                                class="form-label"
                            >
                                Categoría *
                            </label>

                            <select
                                id="id_categoria"
                                name="id_categoria"
                                class="form-select"
                                data-quote-category
                                required
                            >
                                <option value="">
                                    Primero selecciona un centro
                                </option>

                                <?php foreach (
                                    $rates as $rate
                                ): ?>

                                    <option
                                        value="<?= (int) $rate[
                                            'id_categoria'
                                        ] ?>"

                                        data-center-id="<?= (int) $rate[
                                            'id_punto'
                                        ] ?>"

                                        data-price-kg="<?= e(
                                            $rate['precio_por_kg']
                                        ) ?>"

                                        data-price-piece="<?= e(
                                            $rate['precio_por_pieza']
                                        ) ?>"

                                        data-minimum="<?= e(
                                            $rate['minimo_kg']
                                                ?? ''
                                        ) ?>"

                                        data-maximum="<?= e(
                                            $rate['maximo_kg']
                                                ?? ''
                                        ) ?>"

                                        <?= (
                                            $oldPointId
                                                === (int) $rate[
                                                    'id_punto'
                                                ]
                                            && $oldCategoryId
                                                === (int) $rate[
                                                    'id_categoria'
                                                ]
                                        )
                                            ? 'selected'
                                            : '' ?>
                                    >
                                        <?= e($rate['categoria']) ?>
                                    </option>

                                <?php endforeach; ?>
                            </select>

                            <div class="form-text">
                                Solamente aparecerán las categorías
                                que el centro compre y que tengan
                                una tarifa vigente.
                            </div>
                        </div>

                        <!-- Unidad -->
                        <div class="col-md-6">
                            <label
                                for="unidad"
                                class="form-label"
                            >
                                Forma de cálculo *
                            </label>

                            <select
                                id="unidad"
                                name="unidad"
                                class="form-select"
                                data-quote-unit
                                required
                            >
                                <option
                                    value="kg"
                                    <?= selected(
                                        old(
                                            'unidad',
                                            'kg'
                                        ),
                                        'kg'
                                    ) ?>
                                >
                                    Por kilogramo
                                </option>

                                <option
                                    value="pieza"
                                    <?= selected(
                                        old('unidad'),
                                        'pieza'
                                    ) ?>
                                >
                                    Por pieza
                                </option>
                            </select>
                        </div>

                        <!-- Cantidad -->
                        <div class="col-md-6">
                            <label
                                for="cantidad"
                                class="form-label"
                                data-quote-quantity-label
                            >
                                Peso en kilogramos *
                            </label>

                            <input
                                id="cantidad"
                                type="number"
                                name="cantidad"
                                class="form-control"
                                min="0.01"
                                step="0.01"
                                value="<?= e(old(
                                    'cantidad'
                                )) ?>"
                                placeholder="Ejemplo: 5.50"
                                data-quote-quantity
                                required
                            >
                        </div>

                        <!-- Observaciones -->
                        <div class="col-12">
                            <label
                                for="observaciones"
                                class="form-label"
                            >
                                Observaciones
                            </label>

                            <textarea
                                id="observaciones"
                                name="observaciones"
                                class="form-control"
                                rows="4"
                                maxlength="300"
                                placeholder="Describe el estado o presentación del material."
                            ><?= e(old(
                                'observaciones'
                            )) ?></textarea>

                            <div class="form-text">
                                Máximo 300 caracteres.
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>

        <!-- Resultado de la estimación -->
        <div class="col-xl-5">
            <section class="eco-card">
                <div class="eco-card-head">
                    <h2 class="eco-card-title">
                        Estimación
                    </h2>
                </div>

                <div class="eco-card-body">
                    <div
                        class="p-4 rounded-4
                               bg-light border"
                    >
                        <div class="small text-muted-eco">
                            Precio unitario
                        </div>

                        <div
                            class="h4 fw-bold mb-4"
                            data-quote-unit-price
                        >
                            $0.00
                        </div>

                        <div class="small text-muted-eco">
                            Importe estimado
                        </div>

                        <div
                            class="display-6 fw-bold
                                   text-eco mb-3"
                            data-quote-total
                        >
                            $0.00
                        </div>

                        <p
                            class="small text-muted-eco mb-0"
                            data-quote-message
                        >
                            Selecciona un centro,
                            una categoría y una cantidad.
                        </p>
                    </div>

                    <div
                        class="alert alert-warning
                               mt-4 mb-0"
                    >
                        <i
                            class="bi bi-info-circle
                                   me-2"
                        ></i>

                        El resultado es una estimación.
                        El valor final puede cambiar después
                        de revisar el peso, la calidad y el
                        estado del material.
                    </div>
                </div>

                <div
                    class="eco-card-head
                           justify-content-end"
                >
                    <button
                        type="submit"
                        class="btn btn-eco"
                    >
                        <i class="bi bi-save me-2"></i>
                        Guardar cotización
                    </button>
                </div>
            </section>
        </div>
    </form>

<?php endif; ?>