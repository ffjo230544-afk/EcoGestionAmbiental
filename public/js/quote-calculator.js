(() => {
  "use strict";

  const form = document.querySelector(
    "[data-quote-form]"
  );

  if (!form) {
    return;
  }

  const category = form.querySelector(
    "[data-quote-category]"
  );

  const unit = form.querySelector(
    "[data-quote-unit]"
  );

  const quantity = form.querySelector(
    "[data-quote-quantity]"
  );

  const quantityLabel = form.querySelector(
    "[data-quote-quantity-label]"
  );

  const center = form.querySelector(
    "[data-quote-center]"
  );

  const unitPriceElement = form.querySelector(
    "[data-quote-unit-price]"
  );

  const totalElement = form.querySelector(
    "[data-quote-total]"
  );

  const messageElement = form.querySelector(
    "[data-quote-message]"
  );

  const currency = new Intl.NumberFormat(
    "es-MX",
    {
      style: "currency",
      currency: "MXN",
    }
  );

  const updateCenters = () => {
    if (!center || !category) {
      return;
    }

    const categoryId = category.value;

    Array.from(center.options).forEach(
      (option, index) => {
        if (index === 0) {
          option.hidden = false;
          option.disabled = false;
          return;
        }

        const categories = (
          option.dataset.categories || ""
        )
          .split(",")
          .filter(Boolean);

        const available =
          categoryId !== ""
          && categories.includes(categoryId);

        option.hidden = !available;
        option.disabled = !available;
      }
    );

    if (center.selectedOptions[0]?.disabled) {
      center.value = "0";
    }
  };

  const updateCalculator = () => {
    if (
      !category
      || !unit
      || !quantity
      || !unitPriceElement
      || !totalElement
      || !messageElement
    ) {
      return;
    }

    const selectedCategory =
      category.selectedOptions[0];

    const selectedUnit = unit.value;

    if (selectedUnit === "pieza") {
      quantity.step = "1";
      quantity.min = "1";
      quantity.placeholder = "Ejemplo: 10";

      if (quantityLabel) {
        quantityLabel.textContent =
          "Cantidad de piezas *";
      }
    } else {
      quantity.step = "0.01";
      quantity.min = "0.01";
      quantity.placeholder = "Ejemplo: 5.50";

      if (quantityLabel) {
        quantityLabel.textContent =
          "Peso en kilogramos *";
      }
    }

    if (!selectedCategory?.value) {
      unitPriceElement.textContent =
        currency.format(0);

      totalElement.textContent =
        currency.format(0);

      messageElement.textContent =
        "Selecciona una categoría e ingresa una cantidad.";

      updateCenters();
      return;
    }

    const unitPrice = Number(
      selectedUnit === "kg"
        ? selectedCategory.dataset.priceKg
        : selectedCategory.dataset.pricePiece
    );

    const enteredQuantity = Number(
      quantity.value
    );

    const minimum = Number(
      selectedCategory.dataset.minimum
    );

    const maximum = Number(
      selectedCategory.dataset.maximum
    );

    unitPriceElement.textContent =
      currency.format(
        Number.isFinite(unitPrice)
          ? unitPrice
          : 0
      );

    if (
      !Number.isFinite(unitPrice)
      || unitPrice <= 0
    ) {
      totalElement.textContent =
        currency.format(0);

      messageElement.textContent =
        "Esta categoría no tiene precio disponible para la unidad seleccionada.";

      updateCenters();
      return;
    }

    if (
      !Number.isFinite(enteredQuantity)
      || enteredQuantity <= 0
    ) {
      totalElement.textContent =
        currency.format(0);

      messageElement.textContent =
        "Ingresa una cantidad mayor que cero.";

      updateCenters();
      return;
    }

    if (
      selectedUnit === "pieza"
      && !Number.isInteger(enteredQuantity)
    ) {
      totalElement.textContent =
        currency.format(0);

      messageElement.textContent =
        "La cantidad de piezas debe ser un número entero.";

      updateCenters();
      return;
    }

    if (
      selectedUnit === "kg"
      && Number.isFinite(minimum)
      && minimum > 0
      && enteredQuantity < minimum
    ) {
      totalElement.textContent =
        currency.format(0);

      messageElement.textContent =
        `El mínimo permitido es ${minimum} kg.`;

      updateCenters();
      return;
    }

    if (
      selectedUnit === "kg"
      && Number.isFinite(maximum)
      && maximum > 0
      && enteredQuantity > maximum
    ) {
      totalElement.textContent =
        currency.format(0);

      messageElement.textContent =
        `El máximo permitido es ${maximum} kg.`;

      updateCenters();
      return;
    }

    const estimatedAmount =
      unitPrice * enteredQuantity;

    totalElement.textContent =
      currency.format(estimatedAmount);

    messageElement.textContent =
      selectedUnit === "kg"
        ? `${enteredQuantity} kg × ${currency.format(unitPrice)}`
        : `${enteredQuantity} pieza(s) × ${currency.format(unitPrice)}`;

    updateCenters();
  };

  category?.addEventListener(
    "change",
    updateCalculator
  );

  unit?.addEventListener(
    "change",
    updateCalculator
  );

  quantity?.addEventListener(
    "input",
    updateCalculator
  );

  updateCalculator();
})();