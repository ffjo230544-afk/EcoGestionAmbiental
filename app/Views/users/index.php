<div class="page-header">
    <div>
        <h1 class="page-title">
            Gestión de usuarios
        </h1>

        <p class="page-subtitle">
            Administra cuentas, roles y estados de acceso.
        </p>
    </div>

    <a class="btn btn-eco" href="<?= e(url('usuarios/crear')) ?>">
        <i class="bi bi-person-plus me-2"></i>
        Registrar usuario
    </a>
</div>

<section class="eco-card mb-4">
    <div class="eco-card-body p-4">
        <form action="<?= e(url('usuarios')) ?>" method="get" class="row g-3 align-items-end">
            <!-- GET necesita conservar el nombre de la ruta. -->
            <input type="hidden" name="ruta" value="usuarios">

            <div class="col-lg-5">
                <label for="buscar" class="form-label">
                    Buscar
                </label>

                <input id="buscar" type="search" name="buscar" value="<?= e($search) ?>" class="form-control"
                    placeholder="Nombre, correo o teléfono">
            </div>

            <div class="col-md-4 col-lg-3">
                <label for="rol" class="form-label">
                    Rol
                </label>

                <select id="rol" name="rol" class="form-select">
                    <option value="0">
                        Todos los roles
                    </option>

                    <?php foreach ($roles as $role): ?>
                    <option value="<?= (int) $role['id_rol'] ?>" <?= $roleFilter === (int) $role['id_rol']
                                ? 'selected'
                                : '' ?>>
                        <?= e($role['nombre_rol']) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-md-4 col-lg-2">
                <label for="estado" class="form-label">
                    Estado
                </label>

                <select id="estado" name="estado" class="form-select">
                    <option value="">
                        Todos
                    </option>

                    <option value="activo" <?= $statusFilter === 'activo'
                            ? 'selected'
                            : '' ?>>
                        Activo
                    </option>

                    <option value="inactivo" <?= $statusFilter === 'inactivo'
                            ? 'selected'
                            : '' ?>>
                        Inactivo
                    </option>

                    <option value="bloqueado" <?= $statusFilter === 'bloqueado'
                            ? 'selected'
                            : '' ?>>
                        Bloqueado
                    </option>
                </select>
            </div>

            <div class="col-md-4 col-lg-2 d-grid">
                <button class="btn btn-eco-outline" type="submit">
                    <i class="bi bi-search me-1"></i>
                    Buscar
                </button>
            </div>
        </form>
    </div>
</section>

<section class="eco-card">
    <div class="eco-card-head">
        <strong>
            Usuarios registrados
        </strong>

        <span class="text-muted-eco small">
            <?= (int) $total ?> resultado(s)
        </span>
    </div>

    <div class="table-responsive">
        <?php if ($users !== []): ?>

        <table class="table eco-table">
            <thead>
                <tr>
                    <th>Usuario</th>
                    <th>Rol</th>
                    <th>Centro asignado</th>
                    <th>Teléfono</th>
                    <th>Estado</th>
                    <th>Registro</th>
                    <th class="text-end">Acciones</th>
                </tr>
            </thead>

            <tbody>
                <?php foreach ($users as $item): ?>
                <tr>
                    <td>
                        <strong>
                            <?= e($item['nombre_completo']) ?>
                        </strong>

                        <div class="small text-muted-eco">
                            <?= e($item['correo']) ?>
                        </div>
                    </td>

                    <td>
                        <?= e($item['nombre_rol']) ?>
                    </td>

                    <td>
                        <?php if (
        strtolower(
            (string) $item['nombre_rol']
        ) === 'coordinador'
    ): ?>

                        <span class="d-inline-flex align-items-center gap-2">
                            <i class="bi bi-building text-eco"></i>

                            <?= e(
                $item['nombre_punto_asignado']
                ?: 'Sin centro asignado'
            ) ?>
                        </span>

                        <?php else: ?>

                        <span class="text-muted-eco">
                            No aplica
                        </span>

                        <?php endif; ?>
                    </td>

                    <td>
                        <?= e(
                                    $item['telefono']
                                    ?: 'Sin teléfono'
                                ) ?>
                    </td>

                    <td>
                        <span class="status-pill status-<?= e($item['estado']) ?>">
                            <?= e(ucfirst($item['estado'])) ?>
                        </span>
                    </td>

                    <td>
                        <?= e(
                                    (new DateTimeImmutable(
                                        $item['fecha_registro']
                                    ))->format('d/m/Y')
                                ) ?>
                    </td>

                    <td>
                        <div class="d-flex gap-2 justify-content-end">
                            <a href="<?= e(
                                            url(
                                                'usuarios/editar',
                                                [
                                                    'id' =>
                                                        $item['id_usuario'],
                                                ]
                                            )
                                        ) ?>" class="btn btn-sm btn-light-eco" title="Editar usuario">
                                <i class="bi bi-pencil"></i>
                            </a>

                            <?php if (
                                        (int) $item['id_usuario']
                                        !== (int) auth_user()['id_usuario']
                                    ): ?>

                            <?php
                                        $activate =
                                            $item['estado'] !== 'activo';

                                        $newStatus = $activate
                                            ? 'activo'
                                            : 'inactivo';
                                        ?>

                            <form action="<?= e(
                                                url(
                                                    'usuarios/cambiar-estado'
                                                )
                                            ) ?>" method="post" class="m-0" data-confirm="<?= e(
                                                $activate
                                                    ? '¿Deseas activar esta cuenta?'
                                                    : '¿Deseas desactivar esta cuenta?'
                                            ) ?>">
                                <?= csrf_field() ?>

                                <input type="hidden" name="id_usuario" value="<?= (int) $item['id_usuario'] ?>">

                                <input type="hidden" name="estado" value="<?= e($newStatus) ?>">

                                <button type="submit" class="btn btn-sm <?= $activate
                                                    ? 'btn-outline-success'
                                                    : 'btn-outline-danger' ?>" title="<?= $activate
                                                    ? 'Activar'
                                                    : 'Desactivar' ?>">
                                    <i class="bi <?= $activate
                                                    ? 'bi-person-check'
                                                    : 'bi-person-x' ?>"></i>
                                </button>
                            </form>

                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <?php else: ?>

        <div class="p-5 text-center">
            <i class="bi bi-people fs-1 text-eco"></i>

            <h2 class="h5 mt-3">
                No se encontraron usuarios
            </h2>

            <p class="text-muted-eco mb-0">
                Cambia los filtros o registra una cuenta.
            </p>
        </div>

        <?php endif; ?>
    </div>

    <?php if ($totalPages > 1): ?>
    <div class="eco-card-head justify-content-center">
        <nav aria-label="Páginas de usuarios">
            <ul class="pagination mb-0">

                <?php for (
                        $number = 1;
                        $number <= $totalPages;
                        $number++
                    ): ?>

                <li class="page-item <?= $number === $page
                            ? 'active'
                            : '' ?>">
                    <a class="page-link" href="<?= e(
                                    url(
                                        'usuarios',
                                        [
                                            'buscar' => $search,
                                            'rol' => $roleFilter,
                                            'estado' => $statusFilter,
                                            'pagina' => $number,
                                        ]
                                    )
                                ) ?>">
                        <?= $number ?>
                    </a>
                </li>

                <?php endfor; ?>
            </ul>
        </nav>
    </div>
    <?php endif; ?>
</section>