<?php
$roleName = (string) (
    $record['nombre_rol']
    ?? 'Usuario'
);

$centerName = (string) (
    $record['nombre_punto_asignado']
    ?? ''
);

$accountStatus = (string) (
    $record['estado'] ?? 'activo'
);
?>

<div class="page-header">
    <div>
        <span class="section-label">
            Mi cuenta
        </span>

        <h1 class="page-title mt-1">
            Mi perfil
        </h1>

        <p class="page-subtitle">
            Consulta y actualiza la información
            de tu cuenta.
        </p>
    </div>
</div>

<div class="row g-4">
    <div class="col-xl-4">
        <section class="eco-card h-100">
            <div
                class="eco-card-body p-4
                       text-center"
            >
                <div
                    class="report-icon mx-auto mb-3"
                    aria-hidden="true"
                >
                    <i class="bi bi-person"></i>
                </div>

                <h2 class="h4 fw-bold mb-1">
                    <?= e(
                        $record['nombre_completo']
                    ) ?>
                </h2>

                <p class="text-muted-eco mb-3">
                    <?= e($record['correo']) ?>
                </p>

                <div
                    class="d-flex flex-wrap
                           justify-content-center gap-2 mb-4"
                >
                    <span class="role-pill">
                        <?= e($roleName) ?>
                    </span>

                    <span
                        class="status-pill
                               status-<?= e($accountStatus) ?>"
                    >
                        <?= $accountStatus === 'activo'
                            ? 'Cuenta activa'
                            : ucfirst($accountStatus) ?>
                    </span>
                </div>

                <div class="text-start border-top pt-3">
                    <div class="mb-3">
                        <div class="text-muted-eco small">
                            <i
                                class="bi bi-telephone
                                       me-2"
                            ></i>

                            Teléfono
                        </div>

                        <strong>
                            <?= e(
                                $record['telefono']
                                ?: 'No registrado'
                            ) ?>
                        </strong>
                    </div>

                    <?php if (
                        strtolower($roleName)
                        === 'coordinador'
                    ): ?>

                    <div class="mb-3">
                        <div class="text-muted-eco small">
                            <i
                                class="bi bi-building
                                       me-2"
                            ></i>

                            Centro asignado
                        </div>

                        <strong>
                            <?= e(
                                $centerName
                                ?: 'Sin centro asignado'
                            ) ?>
                        </strong>
                    </div>

                    <?php endif; ?>

                    <div>
                        <div class="text-muted-eco small">
                            <i
                                class="bi bi-calendar-check
                                       me-2"
                            ></i>

                            Miembro desde
                        </div>

                        <strong>
                            <?= e(
                                date_mx(
                                    $record[
                                        'fecha_registro'
                                    ] ?? null
                                )
                            ) ?>
                        </strong>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <div class="col-xl-8">
        <section class="eco-card h-100">
            <div class="eco-card-head">
                <div>
                    <h2 class="eco-card-title">
                        Información personal
                    </h2>

                    <p class="text-muted-eco mb-0 small">
                        El rol, estado y centro asignado
                        solamente puede modificarlos
                        el administrador.
                    </p>
                </div>
            </div>

            <div class="eco-card-body p-4">
                <form
                    action="<?= e(
                        url('perfil/actualizar')
                    ) ?>"
                    method="post"
                    class="row g-4"
                >
                    <?= csrf_field() ?>

                    <div class="col-12">
                        <label
                            for="perfil-nombre"
                            class="form-label"
                        >
                            Nombre completo
                        </label>

                        <input
                            id="perfil-nombre"
                            type="text"
                            name="nombre_completo"
                            value="<?= e(
                                old(
                                    'nombre_completo',
                                    $record[
                                        'nombre_completo'
                                    ] ?? ''
                                )
                            ) ?>"
                            class="form-control"
                            minlength="3"
                            maxlength="120"
                            autocomplete="name"
                            required
                        >
                    </div>

                    <div class="col-md-6">
                        <label
                            for="perfil-correo"
                            class="form-label"
                        >
                            Correo electrónico
                        </label>

                        <input
                            id="perfil-correo"
                            type="email"
                            name="correo"
                            value="<?= e(
                                old(
                                    'correo',
                                    $record['correo']
                                    ?? ''
                                )
                            ) ?>"
                            class="form-control"
                            maxlength="120"
                            autocomplete="email"
                            required
                        >

                        <div class="form-text">
                            Este correo se utiliza para
                            iniciar sesión.
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label
                            for="perfil-telefono"
                            class="form-label"
                        >
                            Teléfono
                        </label>

                        <input
                            id="perfil-telefono"
                            type="tel"
                            name="telefono"
                            value="<?= e(
                                old(
                                    'telefono',
                                    $record['telefono']
                                    ?? ''
                                )
                            ) ?>"
                            class="form-control"
                            maxlength="20"
                            autocomplete="tel"
                            placeholder="777 123 4567"
                        >
                    </div>

                    <div class="col-12">
                        <label
                            for="confirmar-correo"
                            class="form-label"
                        >
                            Contraseña actual
                        </label>

                        <input
                            id="confirmar-correo"
                            type="password"
                            name="password_confirmar_correo"
                            class="form-control"
                            maxlength="72"
                            autocomplete="current-password"
                        >

                        <div class="form-text">
                            Solamente debes escribirla
                            si vas a cambiar tu correo
                            electrónico.
                        </div>
                    </div>

                    <div class="col-12 d-grid d-md-flex
                                justify-content-md-end">
                        <button
                            type="submit"
                            class="btn btn-eco"
                        >
                            <i
                                class="bi bi-check-circle
                                       me-2"
                            ></i>

                            Guardar información
                        </button>
                    </div>
                </form>
            </div>
        </section>
    </div>

    <div class="col-12">
        <section class="eco-card">
            <div class="eco-card-head">
                <div>
                    <h2 class="eco-card-title">
                        Cambiar contraseña
                    </h2>

                    <p class="text-muted-eco mb-0 small">
                        Después del cambio se cerrarán
                        las demás sesiones activas.
                    </p>
                </div>

                <i
                    class="bi bi-shield-lock
                           fs-3 text-eco"
                    aria-hidden="true"
                ></i>
            </div>

            <div class="eco-card-body p-4">
                <form
                    action="<?= e(
                        url('perfil/cambiar-password')
                    ) ?>"
                    method="post"
                    class="row g-4"
                >
                    <?= csrf_field() ?>

                    <div class="col-lg-4">
                        <label
                            for="password-actual"
                            class="form-label"
                        >
                            Contraseña actual
                        </label>

                        <input
                            id="password-actual"
                            type="password"
                            name="password_actual"
                            class="form-control"
                            maxlength="72"
                            autocomplete="current-password"
                            required
                        >
                    </div>

                    <div class="col-lg-4">
                        <label
                            for="password-nueva"
                            class="form-label"
                        >
                            Contraseña nueva
                        </label>

                        <input
                            id="password-nueva"
                            type="password"
                            name="password_nueva"
                            class="form-control"
                            minlength="8"
                            maxlength="72"
                            autocomplete="new-password"
                            required
                        >
                    </div>

                    <div class="col-lg-4">
                        <label
                            for="password-confirmacion"
                            class="form-label"
                        >
                            Confirmar contraseña
                        </label>

                        <input
                            id="password-confirmacion"
                            type="password"
                            name="password_nueva_confirmacion"
                            class="form-control"
                            minlength="8"
                            maxlength="72"
                            autocomplete="new-password"
                            required
                        >
                    </div>

                    <div class="col-12">
                        <div class="alert alert-light border mb-0">
                            <i
                                class="bi bi-info-circle
                                       text-eco me-2"
                            ></i>

                            La contraseña debe tener entre
                            8 y 72 caracteres e incluir una
                            mayúscula, una minúscula y un número.
                        </div>
                    </div>

                    <div class="col-12 d-grid d-md-flex
                                justify-content-md-end">
                        <button
                            type="submit"
                            class="btn btn-eco"
                        >
                            <i
                                class="bi bi-key
                                       me-2"
                            ></i>

                            Cambiar contraseña
                        </button>
                    </div>
                </form>
            </div>
        </section>
    </div>
</div>