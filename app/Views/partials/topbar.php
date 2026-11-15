<?php
/*
 | Obtengo la cantidad de notificaciones pendientes.
 | Si el historial falla, la navegación puede continuar.
 */
$unreadNotifications = 0;

try {
    $unreadNotifications = (
        new Notificacion()
    )->unreadCount(
        (int) $layoutUser['id_usuario']
    );
} catch (Throwable) {
    $unreadNotifications = 0;
}
?>

<header class="eco-topbar">
    <div class="d-flex align-items-center gap-3">
        <button
            class="mobile-toggle"
            type="button"
            data-sidebar-toggle
            aria-label="Abrir menú"
        >
            <i class="bi bi-list"></i>
        </button>

        <div>
            <strong class="d-block">
                <?= e(
                    $pageTitle
                    ?? $roleInterface['title']
                ) ?>
            </strong>

            <span class="topbar-subtitle">
                <?= e(
                    $roleInterface['subtitle']
                ) ?>
            </span>
        </div>
    </div>

    <div class="topbar-actions">
        <a
            class="topbar-icon"
            href="<?= e(url('notificaciones')) ?>"
            aria-label="Notificaciones"
            title="<?= e(
                $unreadNotifications > 0
                    ? $unreadNotifications
                        . ' notificación(es) pendiente(s)'
                    : 'No tienes notificaciones pendientes'
            ) ?>"
        >
            <i class="bi bi-bell"></i>

            <?php if (
                $unreadNotifications > 0
            ): ?>
            <span class="notification-count">
                <?= $unreadNotifications > 99
                    ? '99+'
                    : (int) $unreadNotifications ?>
            </span>
            <?php endif; ?>
        </a>

        <span class="role-pill">
            <?= e($layoutUser['nombre_rol']) ?>
        </span>

        <span
            class="topbar-profile-name
                   fw-semibold small"
            title="<?= e(
                $layoutUser['correo']
            ) ?>"
        >
            <?= e(
                $layoutUser['nombre_completo']
            ) ?>
        </span>

        <!-- Aquí cierro la sesión mediante POST y CSRF. -->
        <form
            action="<?= e(url('logout')) ?>"
            method="post"
            class="m-0"
        >
            <?= csrf_field() ?>

            <button
                class="topbar-icon border-0"
                type="submit"
                aria-label="Cerrar sesión"
                title="Cerrar sesión"
            >
                <i class="bi bi-box-arrow-right"></i>
            </button>
        </form>
    </div>
</header>