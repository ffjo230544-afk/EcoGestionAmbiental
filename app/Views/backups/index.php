<div class="page-header">
    <div>
        <span class="section-label">
            Seguridad
        </span>

        <h1 class="page-title mt-1">
            Respaldo y restauración
        </h1>

        <p class="page-subtitle">
            Protege y recupera la información
            almacenada en la base de datos.
        </p>
    </div>

    <form
        action="<?= e(url('respaldos/generar')) ?>"
        method="post"
    >
        <?= csrf_field() ?>

        <button
            type="submit"
            class="btn btn-eco"
        >
            <i class="bi bi-database-add me-2"></i>
            Generar respaldo
        </button>
    </form>
</div>

<div class="stage-note mb-4">
    <i class="bi bi-shield-exclamation"></i>

    <div>
        <strong>
            Operación administrativa sensible
        </strong>

        <div class="small mt-1">
            Antes de restaurar se genera una copia
            preventiva. La restauración puede reemplazar
            usuarios, contraseñas, citas, cotizaciones
            y demás información actual.
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-6">
        <section class="eco-card metric-card h-100">
            <div class="metric-icon">
                <i class="bi bi-archive"></i>
            </div>

            <div class="metric-value">
                <?= count($backups) ?>
            </div>

            <div class="metric-label">
                Respaldos disponibles
            </div>
        </section>
    </div>

    <div class="col-md-6">
        <section class="eco-card metric-card h-100">
            <div class="metric-icon">
                <i class="bi bi-clock-history"></i>
            </div>

            <div class="metric-value fs-5">
                <?= $latestBackup !== null
                    ? e(
                        $latestBackup[
                            'modified_label'
                        ]
                    )
                    : 'Sin respaldos' ?>
            </div>

            <div class="metric-label">
                Último respaldo registrado
            </div>
        </section>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-xl-6">
        <section class="eco-card h-100">
            <div class="eco-card-head">
                <h2 class="eco-card-title">
                    Restaurar respaldo guardado
                </h2>
            </div>

            <div class="eco-card-body">
                <?php if ($backups !== []): ?>

                <form
                    action="<?= e(
                        url('respaldos/restaurar')
                    ) ?>"
                    method="post"
                    class="row g-3"
                >
                    <?= csrf_field() ?>

                    <div class="col-12">
                        <label
                            for="archivo-respaldo"
                            class="form-label"
                        >
                            Respaldo
                        </label>

                        <select
                            id="archivo-respaldo"
                            name="archivo"
                            class="form-select"
                            required
                        >
                            <option value="">
                                Selecciona un respaldo
                            </option>

                            <?php foreach (
                                $backups as $backup
                            ): ?>
                            <option
                                value="<?= e(
                                    $backup['name']
                                ) ?>"
                            >
                                <?= e($backup['name']) ?>
                                —
                                <?= e(
                                    $backup[
                                        'modified_label'
                                    ]
                                ) ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label
                            for="confirmacion-guardada"
                            class="form-label"
                        >
                            Escribe RESTAURAR
                        </label>

                        <input
                            id="confirmacion-guardada"
                            type="text"
                            name="confirmacion"
                            class="form-control"
                            autocomplete="off"
                            required
                        >
                    </div>

                    <div class="col-md-6">
                        <label
                            for="password-guardada"
                            class="form-label"
                        >
                            Contraseña actual
                        </label>

                        <input
                            id="password-guardada"
                            type="password"
                            name="password"
                            class="form-control"
                            autocomplete="current-password"
                            required
                        >
                    </div>

                    <div class="col-12 d-grid">
                        <button
                            type="submit"
                            class="btn btn-danger"
                        >
                            <i class="bi bi-arrow-counterclockwise me-2"></i>
                            Restaurar respaldo
                        </button>
                    </div>
                </form>

                <?php else: ?>

                <div class="empty-state py-4">
                    <i class="bi bi-database-x"></i>

                    <h4>
                        No hay respaldos guardados
                    </h4>

                    <p>
                        Genera un respaldo antes
                        de utilizar esta opción.
                    </p>
                </div>

                <?php endif; ?>
            </div>
        </section>
    </div>

    <div class="col-xl-6">
        <section class="eco-card h-100">
            <div class="eco-card-head">
                <h2 class="eco-card-title">
                    Subir y restaurar SQL
                </h2>
            </div>

            <div class="eco-card-body">
                <form
                    action="<?= e(
                        url(
                            'respaldos/restaurar-subido'
                        )
                    ) ?>"
                    method="post"
                    enctype="multipart/form-data"
                    class="row g-3"
                >
                    <?= csrf_field() ?>

                    <div class="col-12">
                        <label
                            for="archivo-sql"
                            class="form-label"
                        >
                            Archivo SQL
                        </label>

                        <input
                            id="archivo-sql"
                            type="file"
                            name="archivo_sql"
                            class="form-control"
                            accept=".sql"
                            required
                        >

                        <div class="form-text">
                            Solamente archivos .sql.
                            Máximo <?= (int) $maxUploadMb ?> MB.
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label
                            for="confirmacion-subida"
                            class="form-label"
                        >
                            Escribe RESTAURAR
                        </label>

                        <input
                            id="confirmacion-subida"
                            type="text"
                            name="confirmacion"
                            class="form-control"
                            autocomplete="off"
                            required
                        >
                    </div>

                    <div class="col-md-6">
                        <label
                            for="password-subida"
                            class="form-label"
                        >
                            Contraseña actual
                        </label>

                        <input
                            id="password-subida"
                            type="password"
                            name="password"
                            class="form-control"
                            autocomplete="current-password"
                            required
                        >
                    </div>

                    <div class="col-12 d-grid">
                        <button
                            type="submit"
                            class="btn btn-danger"
                        >
                            <i class="bi bi-upload me-2"></i>
                            Subir y restaurar
                        </button>
                    </div>
                </form>
            </div>
        </section>
    </div>
</div>

<section class="eco-card">
    <div class="eco-card-head">
        <h2 class="eco-card-title">
            Historial de respaldos
        </h2>

        <span class="status-pill status-activo">
            <?= count($backups) ?> archivos
        </span>
    </div>

    <?php if ($backups !== []): ?>

    <div class="table-responsive">
        <table class="table eco-table align-middle">
            <thead>
                <tr>
                    <th>Archivo</th>
                    <th>Fecha</th>
                    <th>Tamaño</th>
                    <th class="text-end">Acciones</th>
                </tr>
            </thead>

            <tbody>
                <?php foreach (
                    $backups as $index => $backup
                ): ?>
                <tr>
                    <td>
                        <strong>
                            <?= e($backup['name']) ?>
                        </strong>
                    </td>

                    <td>
                        <?= e(
                            $backup[
                                'modified_label'
                            ]
                        ) ?>
                    </td>

                    <td>
                        <?= e(
                            $backup[
                                'size_label'
                            ]
                        ) ?>
                    </td>

                    <td>
                        <div class="d-flex flex-wrap gap-2 justify-content-end">
                            <a
                                class="btn btn-eco-outline btn-sm"
                                href="<?= e(
                                    url(
                                        'respaldos/descargar',
                                        [
                                            'archivo' =>
                                                $backup['name'],
                                        ]
                                    )
                                ) ?>"
                            >
                                <i class="bi bi-download me-1"></i>
                                Descargar
                            </a>

                            <form
                                action="<?= e(
                                    url(
                                        'respaldos/eliminar'
                                    )
                                ) ?>"
                                method="post"
                                class="d-flex align-items-center gap-2"
                            >
                                <?= csrf_field() ?>

                                <input
                                    type="hidden"
                                    name="archivo"
                                    value="<?= e(
                                        $backup['name']
                                    ) ?>"
                                >

                                <div class="form-check mb-0">
                                    <input
                                        id="confirmar-<?= (int) $index ?>"
                                        type="checkbox"
                                        name="confirmar"
                                        value="1"
                                        class="form-check-input"
                                        required
                                    >

                                    <label
                                        for="confirmar-<?= (int) $index ?>"
                                        class="form-check-label small"
                                    >
                                        Confirmar
                                    </label>
                                </div>

                                <button
                                    type="submit"
                                    class="btn btn-outline-danger btn-sm"
                                >
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <?php else: ?>

    <div class="empty-state">
        <i class="bi bi-database"></i>

        <h4>
            Todavía no hay respaldos
        </h4>

        <p>
            Utiliza el botón Generar respaldo
            para crear la primera copia.
        </p>
    </div>

    <?php endif; ?>
</section>