# EcoGestión Ambiental — Etapa 1

Primera versión didáctica del proyecto final de Joel Figueroa Figueroa. Contiene la arquitectura MVC básica, la página pública y el login visual del diseño final. **Todavía no tiene base de datos, usuarios, autenticación real, roles ni menú dinámico.**

## Abrirlo en XAMPP

1. Extrae `EcoGestionAmbiental_Etapa_01` en `C:\xampp\htdocs\`.
2. Inicia solamente **Apache** en XAMPP. MySQL todavía no es necesario.
3. Abre `http://localhost/EcoGestionAmbiental_Etapa_01/`.
4. Presiona **Iniciar sesión** para revisar y probar el formulario.

No necesitas Composer, Laravel, Artisan ni comandos de terminal.

## Qué funciona en esta etapa

- Enrutamiento GET y POST.
- Controladores de inicio y login.
- Vistas con layouts reutilizables.
- Página de inicio responsive igual a la referencia final.
- Formulario de login con validación básica, CSRF y mensajes flash.
- Mostrar/ocultar contraseña con JavaScript.
- Página 404 y manejo general de errores.

El formulario no compara credenciales: aceptar un correo y una contraseña únicamente demuestra la ruta POST, el controlador, la validación, el mensaje y la redirección. Consulta `docs/GUIA_ETAPA_01.md` para estudiar cada pieza.
