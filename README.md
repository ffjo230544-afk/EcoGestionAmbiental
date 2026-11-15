# EcoGestión Ambiental

Sistema web MVC para administrar centros de acopio, materiales, tarifas,
cotizaciones, citas y contenido de educación ambiental.

## Funciones principales

- Gestión de usuarios con roles de administrador, coordinador y ciudadano.
- Centros, categorías de residuos, materiales aceptados y horarios.
- Mapa interactivo con Leaflet y OpenStreetMap.
- Tarifas independientes por centro y cálculo de cotizaciones.
- Citas, calendario y notificaciones internas y por correo.
- Base de conocimiento, noticias, campañas y encuestas.
- Seis juegos ambientales con contenido editable.
- Reportes PDF, exportación a Excel y respaldos de la base de datos.
- Dashboards con indicadores filtrables por fecha y centro.

## Tecnologías utilizadas

- PHP 8 con arquitectura MVC propia.
- MySQL/MariaDB mediante PDO y consultas preparadas.
- Bootstrap 5, CSS y JavaScript.
- Leaflet y OpenStreetMap para el mapa interactivo.
- Composer, PHPMailer, Dompdf y PhpSpreadsheet.

## Roles del sistema

- **Administrador:** gestiona la información general del sistema.
- **Coordinador:** administra la operación del centro que tiene asignado.
- **Ciudadano:** consulta información, realiza cotizaciones y agenda citas.

## Instalación local

1. Copiar el proyecto dentro de `C:\xampp\htdocs\EcoGestionAmbiental`.
2. Crear la base de datos utilizando `database/ecogestion_ambiental.sql`.
3. Revisar la conexión local en `config/config.php`.
4. Crear `config/mail.local.php` a partir de `config/mail.example.php` si se
   utilizará el envío de correos.
5. Ejecutar `composer install` para reconstruir la carpeta `vendor`.
6. Iniciar Apache y MySQL desde XAMPP.
7. Abrir `http://localhost/EcoGestionAmbiental/`.

La base pública incluye cuentas y operaciones demostrativas sin información
personal. La cuenta inicial es:

- Correo: `admin@ecogestion.local`
- Contraseña temporal: `EcoGestion2026!`

La contraseña debe cambiarse al instalar el sistema en otro equipo o servidor.

## Historial del proyecto

El historial fue reconstruido a partir de 15 respaldos conservados durante el
desarrollo. Cada etapa tiene un commit descriptivo y una etiqueta. El detalle se
encuentra en [docs/HISTORIAL_VERSIONES.md](docs/HISTORIAL_VERSIONES.md).

## Seguridad del repositorio

Las credenciales SMTP, bitácoras, respaldos, sesiones, tokens y archivos
temporales se excluyen del repositorio. El SQL público sustituye las cuentas por
datos genéricos y no incluye sesiones ni tokens de recuperación.

La depuración y la visualización de credenciales de demostración permanecen
desactivadas en la versión publicada.
