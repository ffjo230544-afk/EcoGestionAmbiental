(() => {
  "use strict";

  const dataElement =
    document.querySelector("#calendar-data");

  const calendarGrid =
    document.querySelector(
      "[data-calendar-grid]"
    );

  const mobileAgenda =
    document.querySelector(
      "[data-calendar-mobile]"
    );

  const agendaSection =
    document.querySelector(
      "[data-calendar-agenda]"
    );

  const agendaTitle =
    document.querySelector(
      "[data-calendar-agenda-title]"
    );

  const agendaCount =
    document.querySelector(
      "[data-calendar-agenda-count]"
    );

  const agendaContent =
    document.querySelector(
      "[data-calendar-agenda-content]"
    );

  if (
    !dataElement
    || !calendarGrid
    || !mobileAgenda
  ) {
    return;
  }

  let config;

  try {
    config = JSON.parse(
      dataElement.textContent || "{}"
    );
  } catch {
    return;
  }

  const events = Array.isArray(
    config.events
  )
    ? config.events
    : [];

  const statusLabels =
    config.statusLabels || {};

  const motiveLabels =
    config.motiveLabels || {};

  const summaryMode =
    Boolean(config.summaryMode);

  const canManage =
    Boolean(config.canManage);

  const [year, month] = String(
    config.month || ""
  )
    .split("-")
    .map(Number);

  if (!year || !month) {
    return;
  }

  const monthNames = [
    "enero",
    "febrero",
    "marzo",
    "abril",
    "mayo",
    "junio",
    "julio",
    "agosto",
    "septiembre",
    "octubre",
    "noviembre",
    "diciembre",
  ];

  const eventsByDate = {};

  events.forEach((event) => {
    const date = event.fecha_cita;

    if (!eventsByDate[date]) {
      eventsByDate[date] = [];
    }

    eventsByDate[date].push(event);
  });

  Object.values(eventsByDate).forEach(
    (dayEvents) => {
      dayEvents.sort((first, second) =>
        String(first.hora_cita).localeCompare(
          String(second.hora_cita)
        )
      );
    }
  );

  /**
   * Construye una fecha local evitando
   * cambios producidos por zonas horarias.
   */
  const localDate = (dateString) => {
    const [dateYear, dateMonth, dateDay] =
      dateString.split("-").map(Number);

    return new Date(
      dateYear,
      dateMonth - 1,
      dateDay
    );
  };

  /**
   * Devuelve la fecha con formato Y-m-d.
   */
  const dateKey = (
    dateYear,
    dateMonth,
    dateDay
  ) => {
    return [
      dateYear,
      String(dateMonth).padStart(2, "0"),
      String(dateDay).padStart(2, "0"),
    ].join("-");
  };

  /**
   * Formatea una fecha completa en español.
   */
  const formatLongDate = (dateString) => {
    return new Intl.DateTimeFormat(
      "es-MX",
      {
        weekday: "long",
        day: "numeric",
        month: "long",
        year: "numeric",
      }
    ).format(localDate(dateString));
  };

  /**
   * Devuelve solamente HH:MM.
   */
  const shortTime = (time) =>
    String(time || "").slice(0, 5);

  /**
   * Agrega una línea de información
   * dentro de una tarjeta.
   */
  const appendInformation = (
    container,
    icon,
    label,
    value
  ) => {
    if (!value) {
      return;
    }

    const row =
      document.createElement("div");

    row.className =
      "calendar-event-information";

    const iconElement =
      document.createElement("i");

    iconElement.className =
      `bi ${icon}`;

    const text =
      document.createElement("span");

    const strong =
      document.createElement("strong");

    strong.textContent = `${label}: `;

    text.appendChild(strong);
    text.appendChild(
      document.createTextNode(value)
    );

    row.appendChild(iconElement);
    row.appendChild(text);

    container.appendChild(row);
  };

  /**
   * Crea la tarjeta utilizada en la agenda
   * del día y en la vista móvil.
   */
  const createEventCard = (event) => {
    const article =
      document.createElement("article");

    article.className =
      `calendar-agenda-event status-border-${event.estado}`;

    const header =
      document.createElement("div");

    header.className =
      "calendar-agenda-event-header";

    const time =
      document.createElement("strong");

    time.className =
      "calendar-agenda-event-time";

    time.textContent =
      shortTime(event.hora_cita);

    const status =
      document.createElement("span");

    status.className =
      `status-pill status-${event.estado}`;

    status.textContent =
      statusLabels[event.estado]
      || event.estado;

    header.appendChild(time);
    header.appendChild(status);

    const title =
      document.createElement("h3");

    title.className =
      "h6 fw-bold mt-3 mb-3";

    title.textContent =
      motiveLabels[event.motivo]
      || "Otro motivo";

    article.appendChild(header);
    article.appendChild(title);

    appendInformation(
      article,
      "bi-building",
      "Centro",
      event.centro
    );

    if (canManage) {
      appendInformation(
        article,
        "bi-person",
        "Ciudadano",
        event.ciudadano
      );

      appendInformation(
        article,
        "bi-envelope",
        "Correo",
        event.correo_ciudadano
      );

      appendInformation(
        article,
        "bi-telephone",
        "Teléfono",
        event.telefono_ciudadano
      );
    }

    if (event.observaciones) {
      const observations =
        document.createElement("div");

      observations.className =
        "calendar-event-observations";

      const strong =
        document.createElement("strong");

      strong.textContent =
        "Observaciones: ";

      observations.appendChild(strong);

      observations.appendChild(
        document.createTextNode(
          event.observaciones
        )
      );

      article.appendChild(observations);
    }

    return article;
  };

  /**
   * Muestra todas las citas de un día.
   */
  const showDayAgenda = (date) => {
    const dayEvents =
      eventsByDate[date] || [];

    if (
      !agendaTitle
      || !agendaCount
      || !agendaContent
    ) {
      return;
    }

    agendaTitle.textContent =
      formatLongDate(date);

    agendaCount.textContent =
      `${dayEvents.length} ${
        dayEvents.length === 1
          ? "cita"
          : "citas"
      }`;

    agendaContent.innerHTML = "";

    document
      .querySelectorAll(
        ".calendar-day.is-selected"
      )
      .forEach((element) => {
        element.classList.remove(
          "is-selected"
        );
      });

    document
      .querySelector(
        `[data-calendar-date="${date}"]`
      )
      ?.classList.add("is-selected");

    if (dayEvents.length === 0) {
      const empty =
        document.createElement("div");

      empty.className =
        "text-center py-4 text-muted-eco";

      empty.textContent =
        "No existen citas registradas para este día.";

      agendaContent.appendChild(empty);
      return;
    }

    /*
     | En el resumen general del administrador
     | agrupo las citas por centro.
     */
    if (summaryMode) {
      const groups = {};

      dayEvents.forEach((event) => {
        const centerName =
          event.centro || "Sin centro";

        if (!groups[centerName]) {
          groups[centerName] = [];
        }

        groups[centerName].push(event);
      });

      Object.entries(groups).forEach(
        ([centerName, centerEvents]) => {
          const group =
            document.createElement(
              "section"
            );

          group.className =
            "calendar-center-group";

          const heading =
            document.createElement("div");

          heading.className =
            "calendar-center-group-heading";

          const title =
            document.createElement("h3");

          title.className =
            "h6 fw-bold mb-0";

          title.textContent = centerName;

          const count =
            document.createElement("span");

          count.className = "chip";

          count.textContent =
            `${centerEvents.length} ${
              centerEvents.length === 1
                ? "cita"
                : "citas"
            }`;

          heading.appendChild(title);
          heading.appendChild(count);

          const list =
            document.createElement("div");

          list.className =
            "calendar-agenda-list";

          centerEvents.forEach((event) => {
            list.appendChild(
              createEventCard(event)
            );
          });

          group.appendChild(heading);
          group.appendChild(list);
          agendaContent.appendChild(group);
        }
      );
    } else {
      const list =
        document.createElement("div");

      list.className =
        "calendar-agenda-list";

      dayEvents.forEach((event) => {
        list.appendChild(
          createEventCard(event)
        );
      });

      agendaContent.appendChild(list);
    }

    /*
     | En pantallas pequeñas llevo al usuario
     | directamente hasta el detalle.
     */
    if (
      window.matchMedia(
        "(max-width: 767.98px)"
      ).matches
    ) {
      agendaSection?.scrollIntoView({
        behavior: "smooth",
        block: "start",
      });
    }
  };

  /**
   * Crea el resumen para todos los centros.
   */
  const appendSummary = (
    container,
    dayEvents
  ) => {
    const centers = new Set(
      dayEvents.map(
        (event) => event.id_punto
      )
    );

    const total =
      document.createElement("div");

    total.className =
      "calendar-day-summary-total";

    total.textContent =
      `${dayEvents.length} ${
        dayEvents.length === 1
          ? "cita"
          : "citas"
      }`;

    const centerCount =
      document.createElement("div");

    centerCount.className =
      "calendar-day-summary-centers";

    centerCount.textContent =
      `${centers.size} ${
        centers.size === 1
          ? "centro"
          : "centros"
      }`;

    container.appendChild(total);
    container.appendChild(centerCount);

    const statuses =
      document.createElement("div");

    statuses.className =
      "calendar-status-summary";

    [
      "pendiente",
      "confirmada",
      "atendida",
      "cancelada",
    ].forEach((status) => {
      const count = dayEvents.filter(
        (event) =>
          event.estado === status
      ).length;

      if (count === 0) {
        return;
      }

      const item =
        document.createElement("span");

      item.className =
        `calendar-status-count status-${status}`;

      item.textContent = count;

      item.title =
        `${statusLabels[status] || status}: ${count}`;

      statuses.appendChild(item);
    });

    container.appendChild(statuses);
  };

  /**
   * Crea los eventos pequeños mostrados
   * directamente dentro de cada día.
   */
  const appendEventPreviews = (
    container,
    dayEvents
  ) => {
    const visibleEvents =
      dayEvents.slice(0, 3);

    visibleEvents.forEach((event) => {
      const preview =
        document.createElement("div");

      preview.className =
        `calendar-event-preview status-${event.estado}`;

      const time =
        document.createElement("strong");

      time.textContent =
        shortTime(event.hora_cita);

      const text =
        document.createElement("span");

      text.textContent =
        motiveLabels[event.motivo]
        || "Cita";

      preview.appendChild(time);
      preview.appendChild(text);

      container.appendChild(preview);
    });

    if (dayEvents.length > 3) {
      const more =
        document.createElement("div");

      more.className =
        "calendar-more-events";

      more.textContent =
        `+${dayEvents.length - 3} más`;

      container.appendChild(more);
    }
  };

  /**
   * Construye la cuadrícula mensual.
   */
  const renderCalendar = () => {
    calendarGrid.innerHTML = "";

    const firstDay =
      new Date(year, month - 1, 1);

    const daysInMonth =
      new Date(year, month, 0)
        .getDate();

    /*
     | La semana comienza en lunes.
     */
    const initialOffset =
      (firstDay.getDay() + 6) % 7;

    const requiredCells =
      initialOffset + daysInMonth;

    const totalCells =
      Math.ceil(requiredCells / 7) * 7;

    const today = new Date();

    for (
      let cell = 0;
      cell < totalCells;
      cell += 1
    ) {
      const day =
        cell - initialOffset + 1;

      if (
        day < 1
        || day > daysInMonth
      ) {
        const empty =
          document.createElement("div");

        empty.className =
          "calendar-day calendar-day-empty";

        calendarGrid.appendChild(empty);
        continue;
      }

      const date = dateKey(
        year,
        month,
        day
      );

      const dayEvents =
        eventsByDate[date] || [];

      const button =
        document.createElement("button");

      button.type = "button";

      button.className = "calendar-day";

      button.dataset.calendarDate = date;

      if (
        today.getFullYear() === year
        && today.getMonth() + 1
          === month
        && today.getDate() === day
      ) {
        button.classList.add(
          "is-today"
        );
      }

      const number =
        document.createElement("span");

      number.className =
        "calendar-day-number";

      number.textContent = String(day);

      button.appendChild(number);

      if (dayEvents.length > 0) {
        button.classList.add(
          "has-events"
        );

        if (summaryMode) {
          appendSummary(
            button,
            dayEvents
          );
        } else {
          appendEventPreviews(
            button,
            dayEvents
          );
        }
      }

      button.addEventListener(
        "click",
        () => showDayAgenda(date)
      );

      calendarGrid.appendChild(button);
    }
  };

  /**
   * Construye la vista cronológica utilizada
   * principalmente en teléfonos.
   */
  const renderMobileAgenda = () => {
    mobileAgenda.innerHTML = "";

    const dates = Object.keys(
      eventsByDate
    ).sort();

    if (dates.length === 0) {
      const empty =
        document.createElement("div");

      empty.className =
        "calendar-mobile-empty";

      empty.textContent =
        `No existen citas registradas en ${monthNames[month - 1]} de ${year}.`;

      mobileAgenda.appendChild(empty);
      return;
    }

    dates.forEach((date) => {
      const section =
        document.createElement("section");

      section.className =
        "calendar-mobile-day";

      const heading =
        document.createElement("button");

      heading.type = "button";

      heading.className =
        "calendar-mobile-day-heading";

      const title =
        document.createElement("strong");

      title.textContent =
        formatLongDate(date);

      const count =
        document.createElement("span");

      const length =
        eventsByDate[date].length;

      count.className = "chip";

      count.textContent =
        `${length} ${
          length === 1
            ? "cita"
            : "citas"
        }`;

      heading.appendChild(title);
      heading.appendChild(count);

      heading.addEventListener(
        "click",
        () => showDayAgenda(date)
      );

      section.appendChild(heading);

      /*
       | Muestro un resumen corto para evitar
       | una lista demasiado extensa.
       */
      eventsByDate[date]
        .slice(0, 3)
        .forEach((event) => {
          const preview =
            document.createElement("div");

          preview.className =
            "calendar-mobile-preview";

          const time =
            document.createElement("strong");

          time.textContent =
            shortTime(event.hora_cita);

          const information =
            document.createElement("span");

          information.textContent =
            summaryMode
              ? event.centro
              : motiveLabels[event.motivo]
                || "Cita";

          preview.appendChild(time);
          preview.appendChild(
            information
          );

          section.appendChild(preview);
        });

      if (
        eventsByDate[date].length > 3
      ) {
        const more =
          document.createElement("div");

        more.className =
          "calendar-more-events px-3 pb-3";

        more.textContent =
          `+${
            eventsByDate[date].length - 3
          } citas más`;

        section.appendChild(more);
      }

      mobileAgenda.appendChild(section);
    });
  };

  renderCalendar();
  renderMobileAgenda();

  /*
   | Al abrir el calendario selecciono primero
   | el día actual, si tiene citas.
   | De lo contrario utilizo el primer día
   | que tenga registros.
   */
  const todayKey = dateKey(
    new Date().getFullYear(),
    new Date().getMonth() + 1,
    new Date().getDate()
  );

  if (eventsByDate[todayKey]) {
    showDayAgenda(todayKey);
  } else {
    const firstEventDate =
      Object.keys(eventsByDate).sort()[0];

    if (firstEventDate) {
      showDayAgenda(firstEventDate);
    }
  }
})();