# Guía de aprendizaje — Etapa 1

## 1. Cómo está organizado MVC

MVC significa **Modelo, Vista y Controlador**:

- **Modelo (Model):** se comunica con la base de datos y representa sus datos. La carpeta ya existe, pero está vacía porque agregaremos MySQL en la próxima etapa.
- **Vista (View):** contiene el HTML que el usuario ve. No debe consultar directamente la base de datos.
- **Controlador (Controller):** recibe una petición, decide qué hacer y selecciona la vista que responderá.

En este proyecto también usamos un **Router o enrutador**, que identifica la URL solicitada y llama al controlador correcto.

## 2. Estructura de carpetas

```text
EcoGestionAmbiental_Etapa_01/
├── app/
│   ├── Controllers/       Controladores de inicio y login
│   ├── Core/              Motor MVC: Router, Controller y helpers
│   ├── Models/            Modelos de BD que agregaremos después
│   ├── Views/             Pantallas, layouts, mensajes y errores
│   └── bootstrap.php      Prepara clases, sesión y seguridad
├── config/                Configuración general
├── database/              Aquí incorporaremos el SQL y conexión
├── docs/                  Guías de aprendizaje
├── public/
│   ├── css/               Apariencia visual
│   ├── images/            Logotipo e ilustración
│   └── js/                Comportamiento del navegador
├── routes/web.php         Lista de rutas
├── storage/               Archivos internos que genere el sistema
├── .htaccess              Protege carpetas internas en Apache
└── index.php              Punto de entrada o Front Controller
```

## 3. Recorrido de una petición

Cuando visitas `index.php?ruta=login`, ocurre esto:

1. `index.php` carga `app/bootstrap.php`.
2. `bootstrap.php` inicia la sesión, registra el autoload y carga helpers.
3. `index.php` crea el `Router` y carga `routes/web.php`.
4. El Router encuentra `GET login`.
5. Llama a `AuthController::login()`.
6. El controlador pide la vista `auth/login` y el layout `auth`.
7. `render()` combina ambos archivos y devuelve el HTML al navegador.

Este patrón evita colocar toda la aplicación en un solo archivo.

## 4. Elementos y nombres que debes conocer

| Nombre | Qué significa en este proyecto |
| --- | --- |
| Front Controller | `index.php`; entrada única de la aplicación. |
| Ruta | Relación entre método, URL, controlador y acción. |
| Método HTTP | GET consulta/muestra; POST envía o modifica información. |
| Controller | Clase que coordina una petición. |
| Acción | Método público de un controlador, como `login()` o `submit()`. |
| View | Archivo PHP/HTML de una pantalla. |
| Layout | Plantilla general que envuelve una vista. |
| Partial | Fragmento reutilizable, como los mensajes de alerta. |
| Helper | Función auxiliar reutilizable: `url()`, `asset()`, `e()`. |
| Autoload | Carga automáticamente una clase cuando se usa su nombre. |
| Bootstrap de la app | Archivo que prepara configuración, clases, sesión y cabeceras. No es la biblioteca CSS Bootstrap. |
| Bootstrap CSS | Biblioteca externa usada para cuadrícula, botones y responsive. |
| Asset | Recurso público: CSS, JavaScript o imagen. |
| Responsive | Diseño que se adapta a computadora, tableta y celular. |
| CSRF | Token que comprueba que un formulario se originó en el sitio. |
| Flash message | Mensaje almacenado para mostrarse una sola vez tras redirigir. |
| Escape HTML | `e()` convierte caracteres peligrosos antes de mostrarlos. |
| Menú dinámico | Menú cuyas opciones cambian según el rol autenticado. Todavía no está implementado. |

## 5. Las tres rutas actuales

```php
$router->get('', 'InicioController', 'index');
$router->get('login', 'AuthController', 'login');
$router->post('login', 'AuthController', 'submit');
```

La segunda muestra el formulario. La tercera procesa su envío. Aunque comparten la palabra `login`, no son la misma ruta porque utilizan métodos HTTP diferentes.

## 6. Por qué ya existe una sesión si no hay autenticación

Una **sesión PHP** no significa automáticamente que el usuario inició sesión. En esta etapa se utiliza solamente para:

- Guardar el token CSRF.
- Mostrar mensajes flash después de una redirección.
- Recordar el correo cuando la validación falla.

Más adelante guardaremos en ella la identidad del usuario autenticado. Nunca almacenaremos la contraseña.

## 7. Lo que deliberadamente no existe todavía

- Conexión PDO a MySQL.
- Importación del SQL original.
- Modelos `Usuario` y `Rol`.
- `password_hash()` y `password_verify()`.
- Sesión de usuario autenticado.
- Autorización de rutas por rol.
- Menú dinámico para administrador, coordinador y ciudadano.
- Paneles de cada perfil.

Esto evita copiar código sin entenderlo. En la próxima etapa podremos agregar cada pieza y comprobar por qué es necesaria.

## 8. Próximo módulo recomendado

El siguiente módulo será **autenticación y roles**:

1. Agregar el SQL original y la conexión PDO.
2. Crear modelos `Usuario` y `Rol`.
3. Consultar el correo con una sentencia preparada.
4. Verificar el hash de contraseña.
5. Guardar el usuario y su rol en la sesión.
6. Proteger rutas mediante autorización.
7. Crear el **menú dinámico** según Administrador, Coordinador o Ciudadano.

En ese momento estudiaremos la diferencia entre **autenticación** (saber quién eres) y **autorización** (saber qué puedes hacer).
