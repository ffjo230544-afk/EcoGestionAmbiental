# EcoGestión Ambiental

Sistema web desarrollado para administrar centros de acopio, materiales,
tarifas, cotizaciones, citas y contenido de educación ambiental.

## Tecnologías utilizadas

- PHP 8 con arquitectura MVC propia.
- MySQL/MariaDB mediante PDO.
- Bootstrap 5, CSS y JavaScript.
- Leaflet y OpenStreetMap para el mapa interactivo.
- Composer para las dependencias de correo, PDF y Excel.

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

## Seguridad del repositorio

Las credenciales locales, bitácoras, respaldos de la base de datos y archivos
temporales se excluyen mediante `.gitignore`.

