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


})();
