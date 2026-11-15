<?php
$selectedRoleId = (int) old(
    'id_rol',
    $record['id_rol'] ?? 0
);

$selectedCenterId = (int) old(
    'id_punto_asignado',
    $record['id_punto_asignado'] ?? 0
);

$coordinatorRoleId = 0;

foreach ($roles as $availableRole) {
    if (
        strtolower(
            (string) $availableRole['nombre_rol']
        ) === 'coordinador'
    ) {
        $coordinatorRoleId = (int) (
            $availableRole['id_rol']
        );

        break;
    }
}

$coordinatorSelected =
    $selectedRoleId === $coordinatorRoleId;
?>


<div class="page-header">
    <div>
        <h1 class="page-title">
            <?= $editing
                ? 'Editar usuario'
                : 'Registrar usuario' ?>
        </h1>

        <p class="page-subtitle">
            Define los datos y permisos de acceso.
        </p>
    </div>

    <a class="btn btn-eco-outline" href="<?= e(url('usuarios')) ?>">
        <i class="bi bi-arrow-left me-2"></i>
        Regresar
    </a>
</div>

<form action="<?= e(
        url(
            $editing
                ? 'usuarios/actualizar'
                : 'usuarios/guardar'
        )
    ) ?>" method="post" class="eco-card">
    <?= csrf_field() ?>

    <?php if ($editing): ?>
    <input type="hidden" name="id_usuario" value="<?= (int) $record['id_usuario'] ?>">
    <?php endif; ?>

    <div class="eco-card-body p-4">
        <div class="row g-4">
            <div class="col-md-6">
                <label for="nombre_completo" class="form-label">
                    Nombre completo
                </label>

                <input id="nombre_completo" type="text" name="nombre_completo" value="<?= e(
                        old(
                            'nombre_completo',
                            $record['nombre_completo'] ?? ''
                        )
                    ) ?>" class="form-control" minlength="3" maxlength="120" autocomplete="name" required>
            </div>

            <div class="col-md-6">
                <label for="correo" class="form-label">
                    Correo electrónico
                </label>

                <input id="correo" type="email" name="correo" value="<?= e(
                        old(
                            'correo',
                            $record['correo'] ?? ''
                        )
                    ) ?>" class="form-control" maxlength="120" autocomplete="email" required>
            </div>

            <div class="col-md-4">
                <label for="telefono" class="form-label">
                    Teléfono
                </label>

                <input id="telefono" type="tel" name="telefono" value="<?= e(
                        old(
                            'telefono',
                            $record['telefono'] ?? ''
                        )
                    ) ?>" class="form-control" maxlength="20" autocomplete="tel" placeholder="777 123 4567">
            </div>

            <div class="col-md-4">
                <label for="id_rol" class="form-label">
                    Rol
                </label>

                <select id="id_rol" name="id_rol" class="form-select" data-user-role required>
                    <option value="">
                        Selecciona un rol
                    </option>

                    <?php foreach ($roles as $role): ?>
                    <?php
            $roleCode = strtolower(
                (string) $role['nombre_rol']
            );
            ?>

                    <option value="<?= (int) $role['id_rol'] ?>" data-role-code="<?= e($roleCode) ?>" <?= $selectedRoleId
                    === (int) $role['id_rol']
                        ? 'selected'
                        : '' ?>>
                        <?= e($role['nombre_rol']) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>


            <div class="col-12 <?= $coordinatorSelected
        ? ''
        : 'd-none' ?>" data-coordinator-center>
                <label for="id_punto_asignado" class="form-label">
                    Centro asignado
                </label>

                <select id="id_punto_asignado" name="id_punto_asignado" class="form-select"
                    data-coordinator-center-select <?= $coordinatorSelected
            ? 'required'
            : 'disabled' ?>>
                    <option value="">
                        Selecciona un centro activo
                    </option>

                    <?php foreach ($centers as $center): ?>
                    <option value="<?= (int) $center['id_punto'] ?>" <?= $selectedCenterId
                    === (int) $center['id_punto']
                        ? 'selected'
                        : '' ?>>
                        <?= e($center['nombre']) ?>

                        <?php if (
                    !empty($center['municipio'])
                ): ?>
                        — <?= e($center['municipio']) ?>
                        <?php endif; ?>
                    </option>
                    <?php endforeach; ?>
                </select>

                <div class="form-text">
                    El coordinador solamente podrá administrar
                    información relacionada con este centro.
                </div>

                <?php if ($centers === []): ?>
                <div class="alert alert-warning mt-3 mb-0">
                    Primero debes registrar y activar un centro
                    antes de crear una cuenta de coordinador.
                </div>
                <?php endif; ?>
            </div>

            <div class="col-md-4">
                <label for="estado" class="form-label">
                    Estado
                </label>

                <?php
                $selectedStatus = (string) old(
                    'estado',
                    $record['estado'] ?? 'activo'
                );
                ?>

                <select id="estado" name="estado" class="form-select" required>
                    <option value="activo" <?= $selectedStatus === 'activo'
                            ? 'selected'
                            : '' ?>>
                        Activo
                    </option>

                    <option value="inactivo" <?= $selectedStatus === 'inactivo'
                            ? 'selected'
                            : '' ?>>
                        Inactivo
                    </option>

                    <option value="bloqueado" <?= $selectedStatus === 'bloqueado'
                            ? 'selected'
                            : '' ?>>
                        Bloqueado
                    </option>
                </select>
            </div>

            <div class="col-md-6">
                <label for="password" class="form-label">
                    <?= $editing
                        ? 'Nueva contraseña (opcional)'
                        : 'Contraseña' ?>
                </label>

                <div class="input-group">
                    <input id="password" type="password" name="password" class="form-control" minlength="8"
                        maxlength="72" autocomplete="new-password" <?= $editing ? '' : 'required' ?>>

                    <button class="btn btn-outline-secondary" type="button" data-password-toggle="#password"
                        aria-label="Mostrar contraseña">
                        <i class="bi bi-eye"></i>
                    </button>
                </div>

                <div class="form-text">
                    De 8 a 72 caracteres, con mayúscula,
                    minúscula y número.
                </div>
            </div>

            <div class="col-md-6">
                <label for="password_confirmacion" class="form-label">
                    Confirmar contraseña
                </label>

                <div class="input-group">
                    <input id="password_confirmacion" type="password" name="password_confirmacion" class="form-control"
                        minlength="8" maxlength="72" autocomplete="new-password" <?= $editing ? '' : 'required' ?>>

                    <button class="btn btn-outline-secondary" type="button"
                        data-password-toggle="#password_confirmacion" aria-label="Mostrar confirmación">
                        <i class="bi bi-eye"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="eco-card-head justify-content-end">
        <a class="btn btn-eco-outline" href="<?= e(url('usuarios')) ?>">
            Cancelar
        </a>

        <button class="btn btn-eco" type="submit">
            <i class="bi bi-check2-circle me-2"></i>

            <?= $editing
                ? 'Guardar cambios'
                : 'Registrar cuenta' ?>
        </button>
    </div>
</form>