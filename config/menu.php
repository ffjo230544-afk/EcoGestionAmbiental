<?php
declare(strict_types=1);

/*
 | Este es el menú general de la aplicación.
 | Cada opción indica qué roles pueden verla.
 |
 | "route" contiene una ruta funcional.
 | "target" identifica temporalmente las tarjetas
 | de los módulos que todavía no construimos.
 */
return [
    [
        'section' => 'Principal',
        'label' => 'Panel administrativo',
        'icon' => 'bi-grid-1x2-fill',
        'route' => 'administrador',
        'target' => 'inicio-panel',
        'roles' => ['admin'],
    ],
    [
        'section' => 'Principal',
        'label' => 'Panel del coordinador',
        'icon' => 'bi-grid-1x2-fill',
        'route' => 'coordinador',
        'target' => 'inicio-coordinador',
        'roles' => ['coordinador'],
    ],
    [
        'section' => 'Principal',
        'label' => 'Mi panel',
        'icon' => 'bi-grid-1x2-fill',
        'route' => 'ciudadano',
        'target' => 'inicio-ciudadano',
        'roles' => ['ciudadano'],
    ],

 [
    'section' => 'Administración',
    'label' => 'Gestión de usuarios',
    'icon' => 'bi-people-fill',

    // Esta ruta hace que abra el módulo real.
    'route' => 'usuarios',

    // Estas rutas mantienen seleccionada la opción del menú.
    'match' => [
        'usuarios',
        'usuarios/crear',
        'usuarios/editar',
    ],

    'target' => 'modulo-usuarios',
    'roles' => ['admin'],
],


/*       CENTROS Y RESIDUOS    */
[
    'section' => 'Centros y residuos',
    'label' => 'Centros de acopio',
    'icon' => 'bi-geo-alt-fill',
    'route' => 'centros',
    'match' => [
        'centros',
        'centros/detalle',
        'centros/crear',
        'centros/editar',
        'centros/materiales',
    ],
    'target' => 'modulo-centros',
    'roles' => [
        'admin',
        'coordinador',
        'ciudadano',
    ],
],
    [
    'section' => 'Centros y residuos',
    'label' => 'Categorías de residuos',
    'icon' => 'bi-recycle',
    'route' => 'categorias',
    'match' => [
        'categorias',
        'categorias/crear',
        'categorias/editar',
    ],
    'target' => 'modulo-categorias',
    'roles' => [
        'admin',
        'coordinador',
        'ciudadano',
    ],
],
   [
    'section' => 'Centros y residuos',
    'label' => 'Horarios de atención',
    'icon' => 'bi-clock-history',
    'route' => 'horarios',
    'match' => [
        'horarios',
        'horarios/editar',
    ],
    'target' => 'modulo-horarios',
    'roles' => [
        'admin',
        'coordinador',
        'ciudadano',
    ],
],
    [
        'section' => 'Centros y residuos',
        'label' => 'Mapa interactivo',
        'icon' => 'bi-map-fill',
        'target' => 'modulo-mapa',
        'roles' => ['admin', 'coordinador', 'ciudadano'],
    ],



    /*       EDUCACION AMBIENTAL  */
   [
    'section' => 'Educación ambiental',
    'label' => 'Base de conocimiento',
    'icon' => 'bi-journal-richtext',
    'route' => 'educacion',
    'match' => [
        'educacion',
        'educacion/detalle',
        'educacion/crear',
        'educacion/editar',
    ],
    'roles' => [
        'admin',
        'coordinador',
        'ciudadano',
    ],
],
   [
    'section' => 'Educación ambiental',
    'label' => 'Noticias y campañas',
    'icon' => 'bi-megaphone-fill',
    'route' => 'noticias',

    'match' => [
        'noticias',
        'noticias/detalle',
        'noticias/crear',
        'noticias/editar',
    ],

    'roles' => [
        'admin',
        'coordinador',
        'ciudadano',
    ],
],
    [
        'section' => 'Educación ambiental',
        'label' => 'Encuestas',
        'icon' => 'bi-ui-checks-grid',
        'target' => 'modulo-encuestas',
        'roles' => ['admin', 'coordinador', 'ciudadano'],
    ],
    [
        'section' => 'Educación ambiental',
        'label' => 'Juegos y actividades',
        'icon' => 'bi-controller',
        'target' => 'modulo-actividades',
        'roles' => ['admin', 'coordinador', 'ciudadano'],
    ],

    [
        'section' => 'Operación',
        'label' => 'Tarifas de cotización',
        'icon' => 'bi-tags-fill',
        'target' => 'modulo-tarifas',
        'roles' => ['admin', 'coordinador'],
    ],
    [
        'section' => 'Operación',
        'label' => 'Gestión de cotizaciones',
        'icon' => 'bi-cash-coin',
        'target' => 'modulo-cotizaciones',
        'roles' => ['admin', 'coordinador'],
    ],
    [
        'section' => 'Operación',
        'label' => 'Gestión de citas',
        'icon' => 'bi-calendar2-check-fill',
        'target' => 'modulo-citas',
        'roles' => ['admin', 'coordinador'],
    ],
    [
        'section' => 'Operación',
        'label' => 'Calendario',
        'icon' => 'bi-calendar3',
        'target' => 'modulo-calendario',
        'roles' => ['admin', 'coordinador'],
    ],
    [
        'section' => 'Operación',
        'label' => 'Reportes PDF',
        'icon' => 'bi-file-earmark-pdf-fill',
        'target' => 'modulo-reportes',
        'roles' => ['admin', 'coordinador'],
    ],

    [
        'section' => 'Mis servicios',
        'label' => 'Cotizar materiales',
        'icon' => 'bi-calculator-fill',
        'target' => 'modulo-cotizador',
        'roles' => ['ciudadano'],
    ],
    [
        'section' => 'Mis servicios',
        'label' => 'Mis cotizaciones',
        'icon' => 'bi-cash-stack',
        'target' => 'modulo-mis-cotizaciones',
        'roles' => ['ciudadano'],
    ],
    [
        'section' => 'Mis servicios',
        'label' => 'Solicitar una cita',
        'icon' => 'bi-calendar-plus-fill',
        'target' => 'modulo-solicitar-cita',
        'roles' => ['ciudadano'],
    ],
    [
        'section' => 'Mis servicios',
        'label' => 'Mis citas',
        'icon' => 'bi-calendar2-check-fill',
        'target' => 'modulo-mis-citas',
        'roles' => ['ciudadano'],
    ],

    [
        'section' => 'Seguridad',
        'label' => 'Respaldo y restauración',
        'icon' => 'bi-database-fill-gear',
        'target' => 'modulo-respaldos',
        'roles' => ['admin'],
    ],

    [
        'section' => 'Mi cuenta',
        'label' => 'Notificaciones',
        'icon' => 'bi-bell-fill',
        'target' => 'modulo-notificaciones',
        'roles' => ['admin', 'coordinador', 'ciudadano'],
    ],
    [
        'section' => 'Mi cuenta',
        'label' => 'Mi perfil',
        'icon' => 'bi-person-circle',
        'target' => 'modulo-perfil',
        'roles' => ['admin', 'coordinador', 'ciudadano'],
    ],




];