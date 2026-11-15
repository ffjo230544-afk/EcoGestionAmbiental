(() => {
  "use strict";

  /*
   * Aquí permito mostrar u ocultar la contraseña
   * sin modificar su contenido.
   */
  document.querySelectorAll("[data-password-toggle]").forEach((button) => {
    button.addEventListener("click", () => {
      const field = document.querySelector(button.dataset.passwordToggle);

      if (!field) {
        return;
      }

      field.type = field.type === "password" ? "text" : "password";

      button.innerHTML =
        field.type === "password"
          ? '<i class="bi bi-eye"></i>'
          : '<i class="bi bi-eye-slash"></i>';
    });
  });

  /*
   * Aquí obtengo la barra lateral y el fondo oscuro
   * utilizado en celulares.
   */
  const sidebar = document.querySelector("[data-sidebar]");
  const overlay = document.querySelector("[data-sidebar-overlay]");

  /*
   * Aquí controlo la apertura y cierre
   * del menú en celulares y tabletas.
   */
  document.querySelectorAll("[data-sidebar-toggle]").forEach((button) => {
    button.addEventListener("click", () => {
      sidebar?.classList.toggle("open");
      overlay?.classList.toggle("open");
    });
  });

  // Aquí cierro el menú al presionar el fondo oscuro.
  overlay?.addEventListener("click", () => {
    sidebar?.classList.remove("open");
    overlay.classList.remove("open");
  });

  /*
   * Aquí cierro automáticamente el menú móvil
   * cuando selecciono una opción.
   */
  document.querySelectorAll(".nav-eco-link").forEach((link) => {
    link.addEventListener("click", () => {
      sidebar?.classList.remove("open");
      overlay?.classList.remove("open");
    });
  });

  /*
 * Aquí utilizo un modal con el diseño del sistema
 * para confirmar las operaciones sensibles.
 */
const confirmationModal = document.querySelector(
  "[data-confirm-modal]"
);

const confirmationMessage = document.querySelector(
  "[data-confirm-message]"
);

const confirmationButton = document.querySelector(
  "[data-confirm-accept]"
);

let pendingForm = null;
let confirmedForm = null;

const modalInstance =
  confirmationModal && window.bootstrap
    ? new bootstrap.Modal(confirmationModal)
    : null;

/*
 * Intercepto los formularios que necesiten
 * una confirmación antes de enviarse.
 */
document.querySelectorAll("form[data-confirm]").forEach((form) => {
  form.addEventListener("submit", (event) => {
    /*
     * Si el formulario ya fue confirmado,
     * permito que continúe su envío.
     */
    if (confirmedForm === form) {
      confirmedForm = null;
      return;
    }

    event.preventDefault();

    pendingForm = form;

    const message =
      form.dataset.confirm
      || "¿Deseas continuar con esta operación?";

    if (confirmationMessage) {
      confirmationMessage.textContent = message;
    }

    /*
     * Si el modal está disponible, lo muestro.
     */
    if (modalInstance) {
      modalInstance.show();
      return;
    }

    /*
     * Mantengo una confirmación de respaldo
     * si Bootstrap no se pudo cargar.
     */
    if (window.confirm(message)) {
      confirmedForm = form;
      form.requestSubmit();
    }
  });
});

/*
 * Al confirmar la operación, vuelvo a enviar
 * únicamente el formulario seleccionado.
 */
confirmationButton?.addEventListener("click", () => {
  if (!pendingForm) {
    return;
  }

  const form = pendingForm;

  pendingForm = null;
  confirmedForm = form;

  modalInstance?.hide();

  form.requestSubmit();
});

/*
 * Al cerrar o cancelar el modal elimino
 * la operación que estaba pendiente.
 */
confirmationModal?.addEventListener(
  "hidden.bs.modal",
  () => {
    pendingForm = null;
  }
);


/*
 | Habilita o deshabilita las horas cuando
 | se marca un día como cerrado.
 */
document.querySelectorAll('[data-schedule-row]').forEach((row) => {
    const closedToggle = row.querySelector(
        '[data-closed-toggle]'
    );

    const timeFields = row.querySelectorAll(
        '[data-time-field]'
    );

    const updateFields = () => {
        const closed = closedToggle?.checked ?? false;

        timeFields.forEach((field) => {
            field.disabled = closed;
            field.required = !closed;
        });
    };

    closedToggle?.addEventListener(
        'change',
        updateFields
    );

    updateFields();
});

/*
 | Activa las opciones de compra, recepción y
 | observaciones solamente cuando la categoría
 | está seleccionada.
 */
document.querySelectorAll('[data-category-row]').forEach((row) => {
    const toggle = row.querySelector(
        '[data-category-toggle]'
    );

    const options = row.querySelectorAll(
        '[data-category-option]'
    );

    const updateOptions = () => {
        const selected = toggle?.checked ?? false;

        options.forEach((option) => {
            option.disabled = !selected;
        });

        row.classList.toggle(
            'opacity-50',
            !selected
        );
    };

    toggle?.addEventListener(
        'change',
        updateOptions
    );

    updateOptions();
});



/*
 | Muestro las opciones escritas solamente
 | cuando la pregunta es de selección.
 */
const questionType = document.querySelector(
  "[data-question-type]"
);

const questionOptions = document.querySelector(
  "[data-question-options]"
);

const updateQuestionOptions = () => {
  if (!questionType || !questionOptions) {
    return;
  }

  const visible =
    questionType.value === "opcion_multiple";

  const textarea =
    questionOptions.querySelector("textarea");

  questionOptions.classList.toggle(
    "d-none",
    !visible
  );

  if (textarea) {
    textarea.required = visible;
  }
};

questionType?.addEventListener(
  "change",
  updateQuestionOptions
);

updateQuestionOptions();

/*
 | Muestro el selector de centro solamente cuando
 | el administrador selecciona el rol Coordinador.
 */
const userRoleField = document.querySelector(
  "[data-user-role]"
);

const coordinatorCenterContainer =
  document.querySelector(
    "[data-coordinator-center]"
  );

const coordinatorCenterSelect =
  document.querySelector(
    "[data-coordinator-center-select]"
  );

const updateCoordinatorCenter = () => {
  if (
    !userRoleField ||
    !coordinatorCenterContainer ||
    !coordinatorCenterSelect
  ) {
    return;
  }

  const selectedOption =
    userRoleField.options[
      userRoleField.selectedIndex
    ];

  const isCoordinator =
    selectedOption?.dataset.roleCode
    === "coordinador";

  coordinatorCenterContainer.classList.toggle(
    "d-none",
    !isCoordinator
  );

  coordinatorCenterSelect.disabled =
    !isCoordinator;

  coordinatorCenterSelect.required =
    isCoordinator;

  if (!isCoordinator) {
    coordinatorCenterSelect.value = "";
  }
};

userRoleField?.addEventListener(
  "change",
  updateCoordinatorCenter
);

updateCoordinatorCenter();




})();



/*
 | Aquí filtro las categorías del formulario de tarifas
 | de acuerdo con el centro seleccionado.
 */
(() => {
  "use strict";

  const center = document.querySelector(
    "[data-rate-center]"
  );

  const category = document.querySelector(
    "[data-rate-category]"
  );

  /*
   | Este código solamente se ejecuta cuando
   | estoy registrando una tarifa como administrador.
   */
  if (!center || !category) {
    return;
  }

  const updateRateCategories = () => {
    const centerId = center.value;

    Array.from(category.options).forEach(
      (option, index) => {
        /*
         | La primera opción es la instrucción
         | "Selecciona una categoría".
         */
        if (index === 0) {
          option.hidden = false;
          option.disabled = false;
          return;
        }

        const allowedCenters = (
          option.dataset.rateCenters || ""
        )
          .split(",")
          .filter(Boolean);

        const available =
          centerId !== ""
          && allowedCenters.includes(centerId);

        option.hidden = !available;
        option.disabled = !available;
      }
    );

    /*
     | Si cambio el centro y la categoría anterior
     | ya no corresponde, limpio la selección.
     */
    const selectedOption =
      category.selectedOptions[0];

    if (
      selectedOption
      && selectedOption.disabled
    ) {
      category.value = "";
    }
  };

  center.addEventListener(
    "change",
    updateRateCategories
  );

  /*
   | También ejecuto el filtro cuando
   | la página termina de abrirse.
   */
  updateRateCategories();
})();