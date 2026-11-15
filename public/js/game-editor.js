(() => {
  "use strict";

  const editor = document.querySelector("[data-game-editor]");

  if (!editor) {
    return;
  }

  const updateNumbers = (type) => {
    const list = editor.querySelector(
      `[data-editor-list="${type}"]`
    );

    if (!list) {
      return;
    }

    const rows = [
      ...list.querySelectorAll(".game-editor-row"),
    ];

    rows.forEach((row, index) => {
      const number = row.querySelector(
        ".game-editor-row-number"
      );

      if (number) {
        number.textContent = (index + 1).toString();
      }
    });
  };

  const registerRemoveButton = (button) => {
    button.addEventListener("click", () => {
      const row = button.closest(".game-editor-row");
      const list = row?.parentElement;
      const type = list?.dataset.editorList || "";

      row?.remove();

      if (type !== "") {
        updateNumbers(type);
      }
    });
  };

  editor
    .querySelectorAll("[data-remove-editor-row]")
    .forEach(registerRemoveButton);

  editor
    .querySelectorAll("[data-add-editor-row]")
    .forEach((button) => {
      button.addEventListener("click", () => {
        const type = button.dataset.addEditorRow || "";

        const list = editor.querySelector(
          `[data-editor-list="${type}"]`
        );

        const template = editor.querySelector(
          `[data-editor-template="${type}"]`
        );

        if (
          !list
          || !(template instanceof HTMLTemplateElement)
        ) {
          return;
        }

        const fragment = template.content.cloneNode(true);
        const row = fragment.querySelector(
          ".game-editor-row"
        );

        const removeButton = fragment.querySelector(
          "[data-remove-editor-row]"
        );

        if (removeButton) {
          registerRemoveButton(removeButton);
        }

        list.append(fragment);
        updateNumbers(type);

        row?.querySelector(
          "input, textarea, select"
        )?.focus();
      });
    });

  editor.addEventListener("submit", (event) => {
    const submitButton = editor.querySelector(
      'button[type="submit"]'
    );

    if (!editor.checkValidity()) {
      event.preventDefault();
      editor.classList.add("was-validated");
      return;
    }

    if (submitButton) {
      submitButton.disabled = true;

      submitButton.innerHTML = `
        <span class="spinner-border
                     spinner-border-sm me-2"></span>
        Guardando
      `;
    }
  });
})();