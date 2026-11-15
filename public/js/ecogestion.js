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
 | Solicito confirmación antes de ejecutar
 | acciones sensibles como desactivar una cuenta.
 */
  document.querySelectorAll("[data-confirm]").forEach((form) => {
    form.addEventListener("submit", (event) => {
      const message = form.dataset.confirm || "¿Deseas continuar?";

      if (!window.confirm(message)) {
        event.preventDefault();
      }
    });
  });


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
