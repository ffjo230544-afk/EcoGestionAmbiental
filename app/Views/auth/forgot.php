<div class="auth-symbol mb-4">
    <i class="bi bi-envelope-lock"></i>
</div>

<h1 class="h2 fw-bold">
    Recuperar contraseña
</h1>

<p class="text-muted-eco mb-4">
    Escribe el correo asociado con tu cuenta.
    Si se encuentra registrado, recibirás un
    enlace temporal para crear una contraseña nueva.
</p>

<form
    action="<?= e(
        url('recuperar-password/enviar')
    ) ?>"
    method="post"
    novalidate
>
    <?= csrf_field() ?>

    <div class="mb-3">
        <label
            for="correo"
            class="form-label"
        >
            Correo electrónico
        </label>

        <div class="input-group">
            <span class="input-group-text bg-white">
                <i class="bi bi-envelope"></i>
            </span>

            <input
                id="correo"
                type="email"
                name="correo"
                value="<?= e(old('correo')) ?>"
                class="form-control"
                placeholder="nombre@ejemplo.com"
                maxlength="120"
                autocomplete="email"
                required
                autofocus
            >
        </div>
    </div>

    <div class="alert alert-light border small">
        <i class="bi bi-shield-check text-eco me-2"></i>

        Por seguridad, el mensaje será el mismo
        aunque el correo no esté registrado.
    </div>

    <button
        type="submit"
        class="btn btn-eco w-100"
    >
        <i class="bi bi-send me-2"></i>
        Enviar instrucciones
    </button>
</form>

<a
    href="<?= e(url('login')) ?>"
    class="d-block text-center mt-4"
>
    <i class="bi bi-arrow-left me-2"></i>
    Regresar al inicio de sesión
</a>