# Historial reconstruido de EcoGestión Ambiental

Este historial fue reconstruido a partir de los 15 respaldos ZIP conservados
durante el desarrollo. Cada respaldo se comparó con el anterior para identificar
los archivos creados, modificados y eliminados.

Los commits se generan en la fecha real de reconstrucción. La fecha que aparece
en la tabla corresponde a la fecha original del respaldo y también queda
registrada dentro del mensaje del commit. De esta manera, el historial es útil
sin presentar como commits antiguos cambios que en realidad se recuperaron
posteriormente desde archivos ZIP.

## Resumen de versiones

| Versión | Fecha del respaldo | Mensaje del commit | Cambio principal |
|---:|:---:|---|---|
| 01 | 01/09/2026 | Establece la arquitectura MVC, autenticación y módulos base | Estructura MVC, seguridad, inicio de sesión, paneles, usuarios, centros, categorías, horarios y contenido ambiental. |
| 02 | 15/09/2026 | Agrega mapa, encuestas, tarifas y cotizaciones | Mapa con Leaflet/OpenStreetMap, participación ciudadana y operación inicial de cotizaciones. |
| 03 | 30/09/2026 | Incorpora los juegos ambientales y refuerza validaciones | Seis actividades educativas, registro de resultados y validaciones adicionales. |
| 04 | 10/10/2026 | Implementa citas, calendario, notificaciones y reportes | Agenda, calendario por rol, avisos internos y generación de reportes. |
| 05 | 25/10/2026 | Agrega perfil, recuperación de contraseña y correo SMTP | Perfil, cambio/recuperación de contraseña y notificaciones por correo. |
| 06 | 30/10/2026 | Añade respaldos y edición de cuatro juegos ambientales | Respaldo/restauración de la base y administración de contenido de cuatro juegos. |
| 07 | 31/10/2026 | Extiende la edición a ruta de reciclaje y decisiones | Editores de las dos actividades que faltaban. |
| 08 | 31/10/2026 | Habilita la edición de los seis juegos por rol | Catálogo visual y permisos de edición para administrador y coordinador. |
| 09 | 06/11/2026 | Corrige tarifas por centro y completa publicaciones | Categorías compradas por centro, tarjetas de noticias y contenido editable de juegos. |
| 10 | 06/11/2026 | Conecta los indicadores de los tres dashboards | Contadores reales de administrador, coordinador y ciudadano. |
| 11 | 08/11/2026 | Corrige la carga de indicadores y acciones de los dashboards | Corrección del resumen enviado por los controladores a las vistas. |
| 12 | 08/11/2026 | Reemplaza confirmaciones del navegador por un modal del sistema | Confirmación visual uniforme para operaciones sensibles. |
| 13 | 10/11/2026 | Agrega filtros por fecha y centro en los dashboards | Indicadores por periodo y alcance permitido para cada rol. |
| 14 | 10/11/2026 | Habilita temporalmente la depuración del dashboard | Activación temporal de mensajes técnicos para localizar errores. |
| 15 | 15/11/2026 | Consolida la versión funcional final | Integración de las correcciones finales y actualización del esquema de datos. |

## Detalle de la evolución

### Versión 01 — Base del sistema

- Se definió la arquitectura MVC con controladores, modelos y vistas.
- Se construyeron el enrutador, la conexión PDO y los ayudantes generales.
- Se implementaron autenticación, autorización por rol, sesiones, CSRF,
  limitación de intentos y consultas preparadas.
- Se agregaron los paneles iniciales para administrador, coordinador y ciudadano.
- Se incorporaron usuarios, centros, categorías, horarios, educación ambiental y
  noticias/campañas.

### Versión 02 — Operación y participación

- Se añadió el mapa interactivo mediante Leaflet y OpenStreetMap.
- Se agregaron encuestas con preguntas, opciones y respuestas.
- Se implementaron reglas de tarifa por centro y categoría.
- Se incorporó el cálculo y registro de cotizaciones.

### Versión 03 — Gamificación

- Se integraron clasificación de residuos, trivia, memorama, limpieza del
  parque, ruta del reciclaje y decisiones ecológicas.
- Se registraron puntajes y resultados por usuario.
- Se reforzaron validaciones en usuarios, centros y horarios.

### Versión 04 — Citas, seguimiento y reportes

- Se implementó el registro y administración de citas.
- Se añadió el calendario con información limitada según el rol.
- Se incorporaron notificaciones internas relacionadas con operaciones.
- Se agregó el módulo de reportes y sus plantillas PDF.

### Versión 05 — Cuenta y correo

- Se añadieron perfil y cambio de contraseña.
- Se implementó recuperación con tokens temporales de un solo uso.
- Se integró el envío SMTP y las notificaciones por correo.

### Versiones 06 a 08 — Administración de juegos y respaldos

- Se incorporaron respaldo y restauración controlada de MariaDB/MySQL.
- Se crearon editores para el contenido de las seis actividades.
- Se aplicaron permisos de administración al catálogo de juegos.

### Versión 09 — Tarifas y publicaciones

- Se corrigió la carga de categorías para mostrar únicamente las que el centro
  tiene marcadas como compra.
- Se completaron las acciones de detalle, edición y desactivación de noticias y
  campañas.
- Se actualizaron contenidos configurables de los juegos.

### Versiones 10 a 13 — Dashboards medibles

- Los paneles dejaron de utilizar valores de demostración y consultan la base de
  datos.
- Se corrigió el paso del resumen desde cada controlador a su vista.
- Se unificaron las confirmaciones con un modal de Bootstrap.
- Se añadieron filtros de fecha y centro respetando el alcance de cada rol.

### Versiones 14 y 15 — Diagnóstico y consolidación

- Se activó temporalmente la depuración para encontrar errores del dashboard.
- Se consolidaron las correcciones y la estructura final del proyecto.
- En la preparación pública se vuelve a desactivar la depuración.

## Tratamiento de archivos sensibles

Los respaldos originales contienen datos de prueba acumulados durante el
desarrollo, entre ellos nombres, correos, teléfonos, tokens de recuperación e
identificadores de sesiones. Esos datos no se incorporan al historial público.

El repositorio conserva un SQL de instalación depurado: mantiene el esquema y
datos demostrativos no personales, sustituye las cuentas por identidades
genéricas y elimina sesiones y tokens. También se excluyen credenciales SMTP,
bitácoras, respaldos generados, archivos temporales y dependencias instalables.

## Etiquetas

Cada commit reconstruido recibe una etiqueta con el formato
`version-01-2026-09-01` hasta `version-15-2026-11-15`. Esto permite consultar o
descargar una etapa concreta sin subir los ZIP como carpetas independientes.
