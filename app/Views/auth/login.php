<div class="auth-symbol mb-4">
    <i class="bi bi-shield-lock"></i>
</div>

<h1 class="h2 fw-bold">
    Iniciar sesión
</h1>

<p class="text-muted-eco mb-4">
    Ingresa a los servicios de EcoGestión Ambiental.
</p>

<form
    action="<?= e(url('login')) ?>"
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
                autocomplete="username"
                required
                autofocus
            >
        </div>
    </div>

    <div class="mb-3">
        <label
            for="password"
            class="form-label"
        >
            Contraseña
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
                placeholder="Ingresa tu contraseña"
                autocomplete="current-password"
                required
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

    <button
        type="submit"
        class="btn btn-eco w-100 mt-2"
    >
        <i class="bi bi-box-arrow-in-right me-2"></i>
        Iniciar sesión
    </button>
</form>

<?php if (config('show_demo_credentials', false)): ?>

    <div class="demo-credentials mt-4">
        <strong class="small">
            <i class="bi bi-info-circle text-eco me-2"></i>
            Cuentas locales de prueba
        </strong>

        <p class="small text-muted-eco mt-2 mb-1">
            Administrador:
            <code>admin@ecogestion.test</code>
        </p>

        <p class="small text-muted-eco mb-1">
            Coordinador:
            <code>coordinador@ecogestion.test</code>
        </p>

        <p class="small text-muted-eco mb-1">
            Ciudadano:
            <code>ciudadano@ecogestion.test</code>
        </p>

        <p class="small text-muted-eco mb-0">
            Contraseña:
            <code>Password123!</code>
        </p>
    </div>

<?php endif; ?>

<a
    href="<?= e(url()) ?>"
    class="d-block text-center mt-4"
>
    <i class="bi bi-arrow-left me-2"></i>
    Volver a la página principal
</a>