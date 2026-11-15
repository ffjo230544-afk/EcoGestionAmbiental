<main class="public-section">
    <div class="container text-center">
        <div class="auth-symbol mx-auto mb-4">
            <i class="bi bi-shield-lock"></i>
        </div>

        <h1 class="section-title">
            Acceso restringido
        </h1>

        <p class="text-muted-eco">
            Tu cuenta no tiene permiso para consultar
            o modificar esta sección.
        </p>

        <a
            href="<?= e(url(role_home())) ?>"
            class="btn btn-eco mt-3"
        >
            <i class="bi bi-arrow-left me-2"></i>
            Regresar a mi panel
        </a>
    </div>
</main>