(() => {
  "use strict";

  const form = document.querySelector(
    "[data-quote-form]"
  );

  if (!form) {
    return;
  }

  const center = form.querySelector(
    "[data-quote-center]"
  );

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

  /*
   | Guardo todas las opciones originales
   | de categorías para filtrarlas por centro.
   */
  const categoryOptions = category
    ? Array.from(category.options)
    : [];

  /**
   * Coloca la estimación nuevamente en cero.
   */
  const resetAmounts = (message) => {
    if (unitPriceElement) {
      unitPriceElement.textContent =
        currency.format(0);
    }

    if (totalElement) {
      totalElement.textContent =
        currency.format(0);
    }

    if (messageElement) {
      messageElement.textContent = message;
    }
  };

  /**
   * Muestra únicamente las categorías que
   * pertenecen al centro seleccionado.
   */
  const updateCategories = () => {
    if (!center || !category) {
      return;
    }

    const centerId = center.value;

    categoryOptions.forEach(
      (option, index) => {
        /*
         | Mantengo visible la primera opción
         | utilizada como mensaje inicial.
         */
        if (index === 0) {
          option.hidden = false;
          option.disabled = false;
          return;
        }

        const available =
          centerId !== ""
          && option.dataset.centerId
            === centerId;

        option.hidden = !available;
        option.disabled = !available;
      }
    );

    const selectedOption =
      category.selectedOptions[0];

    /*
     | Si la categoría seleccionada no pertenece
     | al nuevo centro, limpio la selección.
     */
    if (
      selectedOption
      && selectedOption.value !== ""
      && selectedOption.disabled
    ) {
      category.value = "";
    }

    category.disabled = centerId === "";

    categoryOptions[0].textContent =
      centerId === ""
        ? "Primero selecciona un centro"
        : "Selecciona un material";
  };

  /**
   * Cambia las propiedades del campo de cantidad
   * según se calcule por kilogramos o piezas.
   */
  const updateQuantityField = () => {
    if (!unit || !quantity) {
      return;
    }

    if (unit.value === "pieza") {
      quantity.step = "1";
      quantity.min = "1";
      quantity.placeholder = "Ejemplo: 10";

      if (quantityLabel) {
        quantityLabel.textContent =
          "Cantidad de piezas *";
      }

      return;
    }

    quantity.step = "0.01";
    quantity.min = "0.01";
    quantity.placeholder = "Ejemplo: 5.50";

    if (quantityLabel) {
      quantityLabel.textContent =
        "Peso en kilogramos *";
    }
  };

  /**
   * Calcula la estimación mostrada al ciudadano.
   *
   * Este cálculo solamente sirve para la interfaz.
   * PHP vuelve a calcular el importe antes
   * de guardar la cotización.
   */
  const updateCalculator = () => {
    if (
      !center
      || !category
      || !unit
      || !quantity
    ) {
      return;
    }

    updateQuantityField();

    if (center.value === "") {
      resetAmounts(
        "Selecciona primero un centro de compra."
      );

      return;
    }

    const selectedRate =
      category.selectedOptions[0];

    if (!selectedRate?.value) {
      resetAmounts(
        "Selecciona una categoría disponible."
      );

      return;
    }

    const selectedUnit = unit.value;

    const unitPrice = Number(
      selectedUnit === "kg"
        ? selectedRate.dataset.priceKg
        : selectedRate.dataset.pricePiece
    );

    const enteredQuantity = Number(
      quantity.value
    );

    const minimum = Number(
      selectedRate.dataset.minimum
    );

    const maximum = Number(
      selectedRate.dataset.maximum
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
      resetAmounts(
        "El centro no tiene precio disponible para la unidad seleccionada."
      );

      return;
    }

    /*
     | Después de reiniciar el importe,
     | vuelvo a mostrar el precio unitario.
     */
    unitPriceElement.textContent =
      currency.format(unitPrice);

    if (
      !Number.isFinite(enteredQuantity)
      || enteredQuantity <= 0
    ) {
      totalElement.textContent =
        currency.format(0);

      messageElement.textContent =
        "Ingresa una cantidad mayor que cero.";

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

      return;
    }

    const estimatedAmount =
      unitPrice * enteredQuantity;

    totalElement.textContent =
      currency.format(estimatedAmount);

    messageElement.textContent =
      selectedUnit === "kg"
        ? `${enteredQuantity} kg × ${currency.format(
            unitPrice
          )}`
        : `${enteredQuantity} pieza(s) × ${currency.format(
            unitPrice
          )}`;
  };

  /*
   | Cuando cambia el centro, limpio la categoría
   | anterior porque podría pertenecer a otro centro.
   */
  center?.addEventListener(
    "change",
    () => {
      if (category) {
        category.value = "";
      }

      updateCategories();
      updateCalculator();
    }
  );

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

  /*
   | Inicializo el formulario al cargar la página.
   | Esto también recupera los datos anteriores
   | cuando PHP devuelve una validación.
   */
  updateCategories();
  updateCalculator();
})();