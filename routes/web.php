<?php
declare(strict_types=1);

/*
 | Aquí defino grupos de roles para reutilizarlos
 | en las rutas protegidas.
 */
$everyone = [
    'admin',
    'coordinador',
    'ciudadano',
];
$operations = [
    'admin',
    'coordinador',
];

/* Rutas públicas. */
$router->get(
    '',
    'InicioController',
    'index'
);

$router->get(
    'login',
    'AuthController',
    'login'
);

$router->post(
    'login',
    'AuthController',
    'authenticate'
);

/* El cierre requiere una sesión de cualquier rol. */
$router->post(
    'logout',
    'AuthController',
    'logout',
    $everyone
);

/*  ADMIN   */

/* Ruta exclusiva del administrador. */
$router->get(
    'administrador',
    'AdministradorController',
    'index',
    ['admin']
);

/* Gestión de usuarios: exclusiva del administrador. */
$router->get(
    'usuarios',
    'UsuarioController',
    'index',
    ['admin']
);

$router->get(
    'usuarios/crear',
    'UsuarioController',
    'create',
    ['admin']
);

$router->post(
    'usuarios/guardar',
    'UsuarioController',
    'store',
    ['admin']
);

$router->get(
    'usuarios/editar',
    'UsuarioController',
    'edit',
    ['admin']
);

$router->post(
    'usuarios/actualizar',
    'UsuarioController',
    'update',
    ['admin']
);

$router->post(
    'usuarios/cambiar-estado',
    'UsuarioController',
    'changeStatus',
    ['admin']
);





/* Ruta exclusiva del coordinador. */
$router->get(
    'coordinador',
    'CoordinadorController',
    'index',
    ['coordinador']
);

/* Ruta exclusiva del ciudadano. */
$router->get(
    'ciudadano',
    'CiudadanoController',
    'index',
    ['ciudadano']
);


/*  CENTROS DE ACOPIO  */

/* Consulta de centros para todos los usuarios autenticados. */
$router->get(
    'centros',
    'CentroController',
    'index',
    $everyone
);

$router->get(
    'centros/detalle',
    'CentroController',
    'show',
    $everyone
);

/* CREAR CENTROS SOLO PARA ADMIN Y COORDINADOR */

$centerManagers = [
    'admin',
    'coordinador',
];

$router->get(
    'centros/crear',
    'CentroController',
    'create',
    $centerManagers
);

$router->post(
    'centros/guardar',
    'CentroController',
    'store',
    $centerManagers
);

$router->get(
    'centros/editar',
    'CentroController',
    'edit',
    $centerManagers
);

$router->post(
    'centros/actualizar',
    'CentroController',
    'update',
    $centerManagers
);

$router->post(
    'centros/cambiar-estado',
    'CentroController',
    'changeStatus',
    $centerManagers
);

$router->get(
    'centros/materiales',
    'CentroController',
    'materials',
    $centerManagers
);

$router->post(
    'centros/materiales/actualizar',
    'CentroController',
    'updateMaterials',
    $centerManagers
);






/*   CATEGORIAS  */
$categoryManagers = [
    'admin',
    'coordinador',
];

/* Consulta para todos los usuarios autenticados. */
$router->get(
    'categorias',
    'CategoriaController',
    'index',
    $everyone
);

/* Gestión para administrador y coordinador. */
$router->get(
    'categorias/crear',
    'CategoriaController',
    'create',
    $categoryManagers
);

$router->post(
    'categorias/guardar',
    'CategoriaController',
    'store',
    $categoryManagers
);

$router->get(
    'categorias/editar',
    'CategoriaController',
    'edit',
    $categoryManagers
);

$router->post(
    'categorias/actualizar',
    'CategoriaController',
    'update',
    $categoryManagers
);

$router->post(
    'categorias/cambiar-estado',
    'CategoriaController',
    'changeStatus',
    $categoryManagers
);



/*    HORARIOS    */
$scheduleManagers = [
    'admin',
    'coordinador',
];

/* Consulta para los tres roles. */
$router->get(
    'horarios',
    'HorarioController',
    'index',
    $everyone
);

/* Modificación exclusiva de responsables. */
$router->get(
    'horarios/editar',
    'HorarioController',
    'edit',
    $scheduleManagers
);

$router->post(
    'horarios/actualizar',
    'HorarioController',
    'update',
    $scheduleManagers
);

/* ==========================================================
   Mapa interactivo de centros de acopio
   ========================================================== */

$router->get(
    'mapa',
    'MapaController',
    'index',
    $everyone
);






/* ==========================================================
   Base de conocimiento ambiental
   ========================================================== */

/* Consulta para los tres roles. */
$router->get(
    'educacion',
    'EducacionController',
    'index',
    $everyone
);

$router->get(
    'educacion/detalle',
    'EducacionController',
    'show',
    $everyone
);

/* Administración: administrador y coordinador. */
$router->get(
    'educacion/crear',
    'PublicacionController',
    'create',
    $operations
);

$router->post(
    'educacion/guardar',
    'PublicacionController',
    'store',
    $operations
);

$router->get(
    'educacion/editar',
    'PublicacionController',
    'edit',
    $operations
);

$router->post(
    'educacion/actualizar',
    'PublicacionController',
    'update',
    $operations
);

$router->post(
    'educacion/desactivar',
    'PublicacionController',
    'deactivate',
    $operations
);



/*
 | Noticias, avisos, campañas y eventos.
 | Los tres roles pueden consultar publicaciones.
 */
$router->get(
    'noticias',
    'NoticiaController',
    'index',
    $everyone
);

$router->get(
    'noticias/detalle',
    'NoticiaController',
    'show',
    $everyone
);

/*
 | Solamente administrador y coordinador
 | pueden administrar contenido.
 */
$router->get(
    'noticias/crear',
    'NoticiaController',
    'create',
    [
        'admin',
        'coordinador',
    ]
);

$router->post(
    'noticias/guardar',
    'NoticiaController',
    'store',
    [
        'admin',
        'coordinador',
    ]
);

$router->get(
    'noticias/editar',
    'NoticiaController',
    'edit',
    [
        'admin',
        'coordinador',
    ]
);

$router->post(
    'noticias/actualizar',
    'NoticiaController',
    'update',
    [
        'admin',
        'coordinador',
    ]
);

$router->post(
    'noticias/desactivar',
    'NoticiaController',
    'deactivate',
    [
        'admin',
        'coordinador',
    ]
);



/* ==========================================================
   Encuestas ambientales
   ========================================================== */

/* Los tres roles pueden consultar y responder. */
$router->get(
    'encuestas',
    'EncuestaController',
    'index',
    $everyone
);

$router->get(
    'encuestas/responder',
    'EncuestaController',
    'respond',
    $everyone
);

$router->post(
    'encuestas/enviar',
    'EncuestaController',
    'submit',
    $everyone
);

/* Administración para administrador y coordinador. */
$router->get(
    'encuestas/crear',
    'EncuestaController',
    'create',
    $operations
);

$router->post(
    'encuestas/guardar',
    'EncuestaController',
    'store',
    $operations
);

$router->get(
    'encuestas/editar',
    'EncuestaController',
    'edit',
    $operations
);

$router->post(
    'encuestas/actualizar',
    'EncuestaController',
    'update',
    $operations
);

$router->get(
    'encuestas/preguntas',
    'EncuestaController',
    'questions',
    $operations
);

$router->get(
    'encuestas/preguntas/crear',
    'EncuestaController',
    'createQuestion',
    $operations
);

$router->post(
    'encuestas/preguntas/guardar',
    'EncuestaController',
    'storeQuestion',
    $operations
);

$router->get(
    'encuestas/preguntas/editar',
    'EncuestaController',
    'editQuestion',
    $operations
);

$router->post(
    'encuestas/preguntas/actualizar',
    'EncuestaController',
    'updateQuestion',
    $operations
);

$router->post(
    'encuestas/preguntas/cambiar-estado',
    'EncuestaController',
    'changeQuestionStatus',
    $operations
);

$router->get(
    'encuestas/resultados',
    'EncuestaController',
    'results',
    $operations
);



/* ==========================================================
   Tarifas de cotización
   ========================================================== */

/*
 | Solamente el administrador y el coordinador
 | pueden administrar los precios.
 */
$router->get(
    'tarifas',
    'TarifaController',
    'index',
    $operations
);

$router->get(
    'tarifas/crear',
    'TarifaController',
    'create',
    $operations
);

$router->post(
    'tarifas/guardar',
    'TarifaController',
    'store',
    $operations
);

$router->get(
    'tarifas/editar',
    'TarifaController',
    'edit',
    $operations
);

$router->post(
    'tarifas/actualizar',
    'TarifaController',
    'update',
    $operations
);

$router->post(
    'tarifas/cambiar-estado',
    'TarifaController',
    'changeStatus',
    $operations
);

/* ==========================================================
   Cotizador y gestión de cotizaciones
   ========================================================== */

/* Cotizador exclusivo del ciudadano. */
$router->get(
    'cotizador',
    'CotizacionController',
    'create',
    ['ciudadano']
);

$router->post(
    'cotizaciones/guardar',
    'CotizacionController',
    'store',
    ['ciudadano']
);

/*
 | Los tres roles pueden abrir la ruta,
 | pero el controlador limita los resultados.
 */
$router->get(
    'cotizaciones',
    'CotizacionController',
    'index',
    $everyone
);

/* Administración de estados. */
$router->post(
    'cotizaciones/cambiar-estado',
    'CotizacionController',
    'changeStatus',
    $operations
);

/* Cancelación exclusiva del propietario. */
$router->post(
    'cotizaciones/cancelar',
    'CotizacionController',
    'cancel',
    ['ciudadano']
);
