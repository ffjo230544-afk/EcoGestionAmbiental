(() => {
  "use strict";

  const mapElement = document.querySelector(
    "#centers-map"
  );

  const dataElement = document.querySelector(
    "#map-points-data"
  );

  const messageElement = document.querySelector(
    "[data-map-message]"
  );

  if (!mapElement || !dataElement) {
    return;
  }

  /*
   * Muestra mensajes encima del mapa.
   */
  const showMessage = (message) => {
    if (!messageElement) {
      return;
    }

    messageElement.textContent = message;
    messageElement.classList.remove("d-none");
  };

  /*
   * L es el objeto proporcionado por Leaflet.
   */
  if (typeof window.L === "undefined") {
    showMessage(
      "No fue posible cargar el mapa. " +
      "Comprueba tu conexión a Internet."
    );

    return;
  }

  let points = [];

  try {
    points = JSON.parse(
      dataElement.textContent || "[]"
    );
  } catch {
    showMessage(
      "No fue posible interpretar " +
      "las ubicaciones registradas."
    );

    return;
  }

  /*
   * Coordenadas generales del estado de Morelos.
   * Solamente se utilizan cuando no hay marcadores.
   */
  const map = window.L.map(
    mapElement,
    {
      zoomControl: true,
    }
  ).setView(
    [18.6813, -99.1013],
    10
  );

  /*
   * Agrego el mapa base de OpenStreetMap.
   */
  window.L.tileLayer(
    "https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png",
    {
      maxZoom: 19,

      attribution:
        '&copy; <a href="https://www.openstreetmap.org/copyright">' +
        "OpenStreetMap</a>",
    }
  ).addTo(map);

  const markers = new Map();
  const bounds = [];

  /*
   * Creo una fila de información mediante textContent.
   * No utilizo innerHTML con información de MySQL.
   */
  const addTextRow = (
    container,
    iconClass,
    text
  ) => {
    const row = document.createElement("div");
    row.className = "map-popup-row";

    const icon = document.createElement("i");
    icon.className = `bi ${iconClass}`;

    const content = document.createElement("span");
    content.textContent = text;

    row.append(
      icon,
      content
    );

    container.append(row);
  };

  /*
   * Construye la ventana que aparece al
   * seleccionar un marcador.
   */
  const createPopup = (point) => {
    const popup = document.createElement("div");
    popup.className = "map-popup";

    const type = document.createElement("span");
    type.className = "map-popup-type";
    type.textContent = point.tipo_texto;

    const title = document.createElement("strong");
    title.className = "map-popup-title";
    title.textContent = point.nombre;

    popup.append(
      type,
      title
    );

    addTextRow(
      popup,
      "bi-geo-alt",
      point.direccion
    );

    addTextRow(
      popup,
      "bi-building",
      `${point.municipio}, ${point.estado_region}`
    );

    addTextRow(
      popup,
      "bi-briefcase",
      point.servicio
    );

    addTextRow(
      popup,
      "bi-telephone",
      point.telefono
    );

    addTextRow(
      popup,
      "bi-recycle",
      point.materiales
    );

    const link = document.createElement("a");

    link.className =
      "btn btn-eco btn-sm w-100 mt-2";

    link.href = point.detalle_url;
    link.textContent = "Ver detalle del centro";

    popup.append(link);

    return popup;
  };

  /*
   * Creo un marcador por cada centro.
   */
  points.forEach((point) => {
    const latitude = Number(point.latitud);
    const longitude = Number(point.longitud);

    if (
      !Number.isFinite(latitude)
      || !Number.isFinite(longitude)
    ) {
      return;
    }

    /*
     * Uso un marcador creado con CSS.
     * Así no necesito descargar imágenes
     * adicionales para los íconos.
     */
    const markerIcon = window.L.divIcon({
      className:
        "eco-map-marker-wrapper",

      html:
        `<span class="eco-map-marker eco-map-marker-${
          point.estado === "activo"
            ? "activo"
            : "inactivo"
        }">` +
        '<i class="bi bi-geo-alt-fill"></i>' +
        "</span>",

      iconSize: [38, 46],
      iconAnchor: [19, 43],
      popupAnchor: [0, -42],
    });

    const marker = window.L.marker(
      [
        latitude,
        longitude,
      ],
      {
        icon: markerIcon,
        title: point.nombre,
      }
    );

    marker.bindPopup(
      createPopup(point),
      {
        minWidth: 245,
        maxWidth: 320,
      }
    );

    marker.addTo(map);

    markers.set(
      Number(point.id),
      marker
    );

    bounds.push([
      latitude,
      longitude,
    ]);
  });

  /*
   * Ajusto automáticamente el mapa
   * para mostrar todos los centros.
   */
  if (bounds.length === 1) {
    map.setView(
      bounds[0],
      14
    );
  } else if (bounds.length > 1) {
    map.fitBounds(
      bounds,
      {
        padding: [35, 35],
        maxZoom: 14,
      }
    );
  }

  /*
   * Al seleccionar un centro de la lista,
   * enfoco su marcador.
   */
  document
    .querySelectorAll("[data-map-point-id]")
    .forEach((item) => {
      item.addEventListener(
        "click",
        () => {
          const pointId = Number(
            item.dataset.mapPointId
          );

          const marker = markers.get(
            pointId
          );

          if (!marker) {
            return;
          }

          map.setView(
            marker.getLatLng(),
            Math.max(
              map.getZoom(),
              15
            ),
            {
              animate: true,
            }
          );

          marker.openPopup();

          /*
           * En teléfono desplazo la pantalla
           * hasta el mapa.
           */
          if (window.innerWidth < 1200) {
            mapElement.scrollIntoView({
              behavior: "smooth",
              block: "center",
            });
          }
        }
      );
    });

  /*
   * Obtiene la ubicación del usuario
   * solamente cuando presiona el botón.
   */
  document
    .querySelector("[data-map-locate]")
    ?.addEventListener(
      "click",
      () => {
        if (!navigator.geolocation) {
          showMessage(
            "Tu navegador no permite " +
            "obtener la ubicación."
          );

          return;
        }

        navigator.geolocation
          .getCurrentPosition(
            (position) => {
              const userPosition = [
                position.coords.latitude,
                position.coords.longitude,
              ];

              map.setView(
                userPosition,
                15
              );

              window.L.circleMarker(
                userPosition,
                {
                  radius: 8,
                  color: "#0d6efd",
                  fillColor: "#4d96ff",
                  fillOpacity: 0.9,
                  weight: 3,
                }
              )
                .addTo(map)
                .bindPopup(
                  "Tu ubicación aproximada"
                )
                .openPopup();
            },

            () => {
              showMessage(
                "No fue posible obtener tu ubicación. " +
                "Revisa el permiso del navegador."
              );
            },

            {
              enableHighAccuracy: true,
              timeout: 10000,
              maximumAge: 60000,
            }
          );
      }
    );

  /*
   * Obligo a Leaflet a calcular nuevamente
   * el tamaño después de cargar el layout.
   */
  window.setTimeout(
    () => map.invalidateSize(),
    150
  );
})();