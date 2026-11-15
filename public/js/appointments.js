(() => {
  "use strict";

  const form = document.querySelector(
    "[data-appointment-form]"
  );

  if (!form) {
    return;
  }

  const center = form.querySelector(
    "[data-appointment-center]"
  );

  const appointmentDate = form.querySelector(
    "[data-appointment-date]"
  );

  const appointmentTime = form.querySelector(
    "[data-appointment-time]"
  );

  const summaryTitle = form.querySelector(
    "[data-appointment-summary-title]"
  );

  const summary = form.querySelector(
    "[data-appointment-summary]"
  );

  const schedulesElement =
    document.querySelector(
      "#appointment-schedules"
    );

  const occupiedElement =
    document.querySelector(
      "#appointment-occupied"
    );

  if (
    !center
    || !appointmentDate
    || !appointmentTime
    || !schedulesElement
    || !occupiedElement
  ) {
    return;
  }

  let schedules = {};
  let occupied = {};

  try {
    schedules = JSON.parse(
      schedulesElement.textContent || "{}"
    );

    occupied = JSON.parse(
      occupiedElement.textContent || "{}"
    );
  } catch {
    schedules = {};
    occupied = {};
  }

  const dayCodes = [
    "domingo",
    "lunes",
    "martes",
    "miercoles",
    "jueves",
    "viernes",
    "sabado",
  ];

  let oldTime =
    appointmentTime.dataset.oldTime || "";

  /**
   * Convierte una hora a minutos.
   */
  const toMinutes = (time) => {
    const [hours, minutes] = time
      .split(":")
      .map(Number);

    return (hours * 60) + minutes;
  };

  /**
   * Convierte minutos nuevamente a HH:MM.
   */
  const toTime = (minutes) => {
    const hours = Math.floor(
      minutes / 60
    );

    const remainingMinutes =
      minutes % 60;

    return `${String(hours).padStart(
      2,
      "0"
    )}:${String(
      remainingMinutes
    ).padStart(2, "0")}`;
  };

  /**
   * Limpia el selector de horarios.
   */
  const resetTimes = (message) => {
    appointmentTime.innerHTML = "";

    const option =
      document.createElement("option");

    option.value = "";
    option.textContent = message;

    appointmentTime.appendChild(option);
    appointmentTime.disabled = true;
  };

  /**
   * Cambia la información visual
   * presentada en el resumen.
   */
  const setSummary = (title, message) => {
    if (summaryTitle) {
      summaryTitle.textContent = title;
    }

    if (summary) {
      summary.textContent = message;
    }
  };

  /**
   * Obtiene la fecha local sin convertirla
   * a otra zona horaria.
   */
  const getDateObject = (value) => {
    if (!value) {
      return null;
    }

    const [year, month, day] = value
      .split("-")
      .map(Number);

    return new Date(
      year,
      month - 1,
      day
    );
  };

  /**
   * Genera espacios de 30 minutos utilizando
   * el horario real del centro.
   */
  const updateTimes = () => {
    const centerId = center.value;
    const dateValue =
      appointmentDate.value;

    if (!centerId) {
      resetTimes(
        "Selecciona primero un centro"
      );

      setSummary(
        "Selecciona un centro",
        "Aquí aparecerán sus horarios de atención."
      );

      return;
    }

    if (!dateValue) {
      resetTimes(
        "Selecciona una fecha"
      );

      setSummary(
        "Selecciona una fecha",
        "Después podrás elegir uno de los horarios disponibles."
      );

      return;
    }

    const dateObject =
      getDateObject(dateValue);

    if (!dateObject) {
      resetTimes("Fecha inválida");
      return;
    }

    const dayCode =
      dayCodes[dateObject.getDay()];

    const centerSchedules =
      schedules[centerId] || {};

    const schedule =
      centerSchedules[dayCode];

    if (
      !schedule
      || schedule.closed
      || !schedule.open
      || !schedule.close
    ) {
      resetTimes(
        "Centro cerrado ese día"
      );

      setSummary(
        "Sin atención",
        "El centro no brinda atención en la fecha seleccionada."
      );

      return;
    }

    const openingMinutes =
      toMinutes(schedule.open);

    const closingMinutes =
      toMinutes(schedule.close);

    const occupiedTimes =
      occupied[centerId]?.[dateValue]
      || [];

    appointmentTime.innerHTML = "";

    const initialOption =
      document.createElement("option");

    initialOption.value = "";
    initialOption.textContent =
      "Selecciona un horario";

    appointmentTime.appendChild(
      initialOption
    );

    let availableCount = 0;

    for (
      let minutes = openingMinutes;
      minutes + 30 <= closingMinutes;
      minutes += 30
    ) {
      const time = toTime(minutes);

      /*
       | Si la cita es para hoy, elimino
       | los horarios que ya pasaron.
       */
      const slotDate = new Date(
        dateObject.getFullYear(),
        dateObject.getMonth(),
        dateObject.getDate(),
        Math.floor(minutes / 60),
        minutes % 60
      );

      if (slotDate <= new Date()) {
        continue;
      }

      if (occupiedTimes.includes(time)) {
        continue;
      }

      const option =
        document.createElement("option");

      option.value = time;
      option.textContent = time;

      if (oldTime === time) {
        option.selected = true;
      }

      appointmentTime.appendChild(option);
      availableCount += 1;
    }

    appointmentTime.disabled =
      availableCount === 0;

    if (availableCount === 0) {
      initialOption.textContent =
        "No hay horarios disponibles";

      setSummary(
        "Agenda completa",
        `El centro atiende de ${schedule.open} a ${schedule.close}, pero ya no tiene espacios disponibles.`
      );

      return;
    }

    setSummary(
      `${availableCount} horarios disponibles`,
      `El centro atiende de ${schedule.open} a ${schedule.close}. Cada espacio dura 30 minutos.`
    );

    /*
     | El valor anterior solamente se utiliza
     | durante la primera carga del formulario.
     */
    oldTime = "";
  };

  center.addEventListener(
    "change",
    () => {
      oldTime = "";
      appointmentTime.value = "";
      updateTimes();
    }
  );

  appointmentDate.addEventListener(
    "change",
    () => {
      oldTime = "";
      appointmentTime.value = "";
      updateTimes();
    }
  );

  updateTimes();
})();