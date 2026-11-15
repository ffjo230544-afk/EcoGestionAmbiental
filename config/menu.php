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

    'route' => 'mapa',

    'match' => [
        'mapa',
    ],

    'target' => 'modulo-mapa',

    'roles' => [
        'admin',
        'coordinador',
        'ciudadano',
    ],
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
    'target' => 'modulo-educacion',
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

    'target' => 'modulo-noticias',

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

    'route' => 'encuestas',

    'match' => [
        'encuestas',
        'encuestas/responder',
        'encuestas/crear',
        'encuestas/editar',
        'encuestas/preguntas',
        'encuestas/preguntas/crear',
        'encuestas/preguntas/editar',
        'encuestas/resultados',
    ],

    'target' => 'modulo-encuestas',

    'roles' => [
        'admin',
        'coordinador',
        'ciudadano',
    ],
],
   [
    'section' => 'Educación ambiental',
    'label' => 'Juegos y actividades',
    'icon' => 'bi-controller',
    'route' => 'juegos',
    'match' => [
        'juegos',
        'juegos/clasificar',
        'juegos/trivia',
        'juegos/memorama',
        'juegos/limpia-parque',
        'juegos/ruta-reciclaje',
        'juegos/decisiones',
    ],
    'target' => 'modulo-actividades',
    'roles' => [
        'admin',
        'coordinador',
        'ciudadano',
    ],
],




              /*     OPERACIÓN  */

[
    'section' => 'Operación',
    'label' => 'Tarifas de cotización',
    'icon' => 'bi-tags-fill',
    'route' => 'tarifas',
    'match' => [
        'tarifas',
        'tarifas/crear',
        'tarifas/editar',
    ],
    'target' => 'modulo-tarifas',
    'roles' => [
        'admin',
        'coordinador',
    ],
],
  [
    'section' => 'Operación',
    'label' => 'Gestión de cotizaciones',
    'icon' => 'bi-cash-coin',
    'route' => 'cotizaciones',
    'match' => [
        'cotizaciones',
    ],
    'target' => 'modulo-cotizaciones',
    'roles' => [
        'admin',
        'coordinador',
    ],
],
   [
    'section' => 'Operación',
    'label' => 'Gestión de citas',
    'icon' => 'bi-calendar2-check-fill',

    'route' => 'citas',

    'match' => [
        'citas',
    ],

    'target' => 'modulo-citas',

    'roles' => [
        'admin',
        'coordinador',
    ],
],



   [
    'section' => 'Operación',
    'label' => 'Calendario',
    'icon' => 'bi-calendar3',

    'route' => 'calendario',

    'match' => [
        'calendario',
    ],

    'target' => 'modulo-calendario',

    'roles' => [
        'admin',
        'coordinador',
    ],
],



[
    'section' => 'Operación',
    'label' => 'Reportes y exportaciones',
    'icon' => 'bi-file-earmark-bar-graph-fill',

    'route' => 'reportes',

    'match' => [
        'reportes',
        'reportes/pdf',
        'reportes/excel',
    ],

    'target' => 'modulo-reportes',

    'roles' => [
        'admin',
        'coordinador',
    ],
],




 [
    'section' => 'Mis servicios',
    'label' => 'Cotizar materiales',
    'icon' => 'bi-calculator-fill',
    'route' => 'cotizador',
    'match' => [
        'cotizador',
    ],
    'target' => 'modulo-cotizador',
    'roles' => [
        'ciudadano',
    ],
],
   [
    'section' => 'Mis servicios',
    'label' => 'Mis cotizaciones',
    'icon' => 'bi-cash-stack',
    'route' => 'cotizaciones',
    'match' => [
        'cotizaciones',
    ],
    'target' => 'modulo-mis-cotizaciones',
    'roles' => [
        'ciudadano',
    ],
],
   [
    'section' => 'Mis servicios',
    'label' => 'Solicitar una cita',
    'icon' => 'bi-calendar-plus-fill',

    'route' => 'citas/crear',

    'match' => [
        'citas/crear',
    ],

    'target' => 'modulo-solicitar-cita',

    'roles' => [
        'ciudadano',
    ],
],



    [
    'section' => 'Mis servicios',
    'label' => 'Mis citas',
    'icon' => 'bi-calendar2-check-fill',

    'route' => 'citas',

    'match' => [
        'citas',
    ],

    'target' => 'modulo-mis-citas',

    'roles' => [
        'ciudadano',
    ],
],

[
    'section' => 'Mis servicios',
    'label' => 'Mi calendario',
    'icon' => 'bi-calendar3',

    'route' => 'calendario',

    'match' => [
        'calendario',
    ],

    'target' => 'modulo-mi-calendario',

    'roles' => [
        'ciudadano',
    ],
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

    'route' => 'notificaciones',

    'match' => [
        'notificaciones',
    ],

    'target' => 'modulo-notificaciones',

    'roles' => [
        'admin',
        'coordinador',
        'ciudadano',
    ],
],


    [
        'section' => 'Mi cuenta',
        'label' => 'Mi perfil',
        'icon' => 'bi-person-circle',
        'target' => 'modulo-perfil',
        'roles' => ['admin', 'coordinador', 'ciudadano'],
    ],




];