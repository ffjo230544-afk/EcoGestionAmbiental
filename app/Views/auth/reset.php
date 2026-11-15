<div class="auth-symbol mb-4">
    <i class="bi bi-key"></i>
</div>

<h1 class="h2 fw-bold">
    Crear contraseña nueva
</h1>

<p class="text-muted-eco mb-4">
    Escribe una contraseña segura para recuperar
    el acceso a tu cuenta.
</p>

<form
    action="<?= e(
        url('restablecer-password/guardar')
    ) ?>"
    method="post"
    novalidate
>
    <?= csrf_field() ?>

    <input
        type="hidden"
        name="token_recuperacion"
        value="<?= e($token) ?>"
    >

    <div class="mb-3">
        <label
            for="password"
            class="form-label"
        >
            Contraseña nueva
        </label>

        <div class="input-group">
            <span class="input-group-text bg-white">
                <i class="bi bi-lock"></i>
            </span>

            <input
                id="password"
                type="password"
                name="password"
                class="form-control"
                minlength="8"
                maxlength="72"
                autocomplete="new-password"
                required
                autofocus
            >

            <button
                class="btn btn-outline-secondary"
                type="button"
                data-password-toggle="#password"
                aria-label="Mostrar contraseña"
            >
                <i class="bi bi-eye"></i>
            </button>
        </div>
    </div>

    <div class="mb-3">
        <label
            for="password-confirmacion"
            class="form-label"
        >
            Confirmar contraseña
        </label>

        <div class="input-group">
            <span class="input-group-text bg-white">
                <i class="bi bi-lock-fill"></i>
            </span>

            <input
                id="password-confirmacion"
                type="password"
                name="password_confirmacion"
                class="form-control"
                minlength="8"
                maxlength="72"
                autocomplete="new-password"
                required
            >

            <button
                class="btn btn-outline-secondary"
                type="button"
                data-password-toggle="#password-confirmacion"
                aria-label="Mostrar contraseña"
            >
                <i class="bi bi-eye"></i>
            </button>
        </div>
    </div>

    <div class="alert alert-light border small">
        <i class="bi bi-info-circle text-eco me-2"></i>

        Utiliza entre 8 y 72 caracteres, una
        mayúscula, una minúscula y un número.
    </div>

    <button
        type="submit"
        class="btn btn-eco w-100"
    >
        <i class="bi bi-shield-check me-2"></i>
        Guardar contraseña
    </button>
</form>

<a
    href="<?= e(url('login')) ?>"
    class="d-block text-center mt-4"
>
    Regresar al inicio de sesión
</a>