<div class="page-header">
    <div>
        <span class="section-label">
            Administración
        </span>

        <h1 class="page-title mt-1">
            Reportes y exportaciones
        </h1>

        <p class="page-subtitle">
            Genera documentos con la información
            registrada en EcoGestión Ambiental.
        </p>
    </div>
</div>

<?php if (!$isAdmin): ?>

<div class="alert alert-light border mb-4">
    <i class="bi bi-building text-eco me-2"></i>

    Los reportes mostrarán únicamente información de:

    <strong>
        <?= e($assignedCenterName) ?>
    </strong>
</div>

<?php endif; ?>

<div class="row g-4">

    <?php if ($isAdmin): ?>

    <div class="col-xl-6">
        <section class="eco-card h-100">
            <div class="eco-card-body">
                <div class="report-icon mb-3">
                    <i class="bi bi-people"></i>
                </div>

                <h2 class="h4 fw-bold">
                    Usuarios por rol
                </h2>

                <p class="text-muted-eco">
                    Lista los usuarios registrados,
                    su rol, estado y centro asignado.
                </p>

                <form
                    action="<?= e(url('reportes/pdf')) ?>"
                    method="get"
                    target="_blank"
                    class="row g-3"
                >
                    <input
                        type="hidden"
                        name="ruta"
                        value="reportes/pdf"
                    >

                    <input
                        type="hidden"
                        name="tipo"
                        value="usuarios"
                    >

                    <div class="col-md-6">
                        <label
                            for="reporte-rol"
                            class="form-label"
                        >
                            Rol
                        </label>

                        <select
                            id="reporte-rol"
                            name="rol"
                            class="form-select"
                        >
                            <option value="0">
                                Todos los roles
                            </option>

                            <?php foreach ($roles as $role): ?>
                            <option
                                value="<?= (int) $role['id_rol'] ?>"
                            >
                                <?= e($role['nombre_rol']) ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label
                            for="estado-usuario"
                            class="form-label"
                        >
                            Estado
                        </label>

                        <select
                            id="estado-usuario"
                            name="estado"
                            class="form-select"
                        >
                            <option value="">
                                Todos
                            </option>
                            <option value="activo">
                                Activo
                            </option>
                            <option value="inactivo">
                                Inactivo
                            </option>
                            <option value="bloqueado">
                                Bloqueado
                            </option>
                        </select>
                    </div>

                    <div class="col-12 d-grid">
                        <button
                            type="submit"
                            class="btn btn-eco"
                        >
                            <i class="bi bi-file-earmark-pdf me-2"></i>
                            Generar PDF
                        </button>
                    </div>
                </form>
            </div>
        </section>
    </div>

    <?php endif; ?>

    <div class="col-xl-6">
        <section class="eco-card h-100">
            <div class="eco-card-body">
                <div class="report-icon mb-3">
                    <i class="bi bi-buildings"></i>
                </div>

                <h2 class="h4 fw-bold">
                    Centros de atención
                </h2>

                <p class="text-muted-eco">
                    Consulta centros por municipio,
                    estado y tipo de punto.
                </p>

                <form
                    action="<?= e(url('reportes/pdf')) ?>"
                    method="get"
                    target="_blank"
                    class="row g-3"
                >
                    <input
                        type="hidden"
                        name="ruta"
                        value="reportes/pdf"
                    >

                    <input
                        type="hidden"
                        name="tipo"
                        value="centros"
                    >

                    <div class="col-md-4">
                        <label
                            for="municipio"
                            class="form-label"
                        >
                            Municipio
                        </label>

                        <select
                            id="municipio"
                            name="municipio"
                            class="form-select"
                        >
                            <option value="">
                                Todos
                            </option>

                            <?php foreach (
                                $municipalities as $municipality
                            ): ?>
                            <option value="<?= e(
                                $municipality['municipio']
                            ) ?>">
                                <?= e(
                                    $municipality['municipio']
                                ) ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label
                            for="estado-centro"
                            class="form-label"
                        >
                            Estado
                        </label>

                        <select
                            id="estado-centro"
                            name="estado"
                            class="form-select"
                        >
                            <option value="">
                                Todos
                            </option>
                            <option value="activo">
                                Activo
                            </option>
                            <option value="inactivo">
                                Inactivo
                            </option>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label
                            for="tipo-centro"
                            class="form-label"
                        >
                            Tipo
                        </label>

                        <select
                            id="tipo-centro"
                            name="tipo_centro"
                            class="form-select"
                        >
                            <option value="">
                                Todos
                            </option>
                            <option value="empresa">
                                Empresa
                            </option>
                            <option value="centro_acopio">
                                Centro de acopio
                            </option>
                            <option value="punto_servicio">
                                Punto de servicio
                            </option>
                        </select>
                    </div>

                    <div class="col-12 d-grid">
                        <button
                            type="submit"
                            class="btn btn-eco"
                        >
                            <i class="bi bi-file-earmark-pdf me-2"></i>
                            Generar PDF
                        </button>
                    </div>
                </form>
            </div>
        </section>
    </div>

    <div class="col-xl-6">
        <section class="eco-card h-100">
            <div class="eco-card-body">
                <div class="report-icon mb-3">
                    <i class="bi bi-recycle"></i>
                </div>

                <h2 class="h4 fw-bold">
                    Materiales por centro
                </h2>

                <p class="text-muted-eco">
                    Muestra qué materiales compra o
                    recibe cada centro.
                </p>

                <form
                    action="<?= e(url('reportes/pdf')) ?>"
                    method="get"
                    target="_blank"
                    class="row g-3"
                >
                    <input
                        type="hidden"
                        name="ruta"
                        value="reportes/pdf"
                    >

                    <input
                        type="hidden"
                        name="tipo"
                        value="materiales"
                    >

                    <?php if ($isAdmin): ?>
                    <div class="col-md-7">
                        <label
                            for="material-centro"
                            class="form-label"
                        >
                            Centro
                        </label>

                        <select
                            id="material-centro"
                            name="centro"
                            class="form-select"
                        >
                            <option value="0">
                                Todos los centros
                            </option>

                            <?php foreach ($centers as $center): ?>
                            <option
                                value="<?= (int) $center['id_punto'] ?>"
                            >
                                <?= e($center['nombre']) ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <?php endif; ?>

                    <div class="<?= $isAdmin
                        ? 'col-md-5'
                        : 'col-12' ?>">
                        <label
                            for="material-estado"
                            class="form-label"
                        >
                            Estado del material
                        </label>

                        <select
                            id="material-estado"
                            name="estado_categoria"
                            class="form-select"
                        >
                            <option value="">
                                Todos
                            </option>
                            <option value="activo">
                                Activo
                            </option>
                            <option value="inactivo">
                                Inactivo
                            </option>
                        </select>
                    </div>

                    <div class="col-12 d-grid">
                        <button
                            type="submit"
                            class="btn btn-eco"
                        >
                            <i class="bi bi-file-earmark-pdf me-2"></i>
                            Generar PDF
                        </button>
                    </div>
                </form>
            </div>
        </section>
    </div>

    <div class="col-xl-6">
        <section class="eco-card h-100">
            <div class="eco-card-body">
                <div class="report-icon mb-3">
                    <i class="bi bi-cash-coin"></i>
                </div>

                <h2 class="h4 fw-bold">
                    Tarifas de cotización
                </h2>

                <p class="text-muted-eco">
                    Consulta los precios y periodos
                    de vigencia por centro.
                </p>

                <form
                    action="<?= e(url('reportes/pdf')) ?>"
                    method="get"
                    target="_blank"
                    class="row g-3"
                >
                    <input
                        type="hidden"
                        name="ruta"
                        value="reportes/pdf"
                    >

                    <input
                        type="hidden"
                        name="tipo"
                        value="tarifas"
                    >

                    <?php if ($isAdmin): ?>
                    <div class="col-md-4">
                        <label
                            for="tarifa-centro"
                            class="form-label"
                        >
                            Centro
                        </label>

                        <select
                            id="tarifa-centro"
                            name="centro"
                            class="form-select"
                        >
                            <option value="0">
                                Todos
                            </option>

                            <?php foreach ($centers as $center): ?>
                            <option
                                value="<?= (int) $center['id_punto'] ?>"
                            >
                                <?= e($center['nombre']) ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <?php endif; ?>

                    <div class="<?= $isAdmin
                        ? 'col-md-4'
                        : 'col-md-7' ?>">
                        <label
                            for="tarifa-categoria"
                            class="form-label"
                        >
                            Categoría
                        </label>

                        <select
                            id="tarifa-categoria"
                            name="categoria"
                            class="form-select"
                        >
                            <option value="0">
                                Todas
                            </option>

                            <?php foreach (
                                $categories as $category
                            ): ?>
                            <option
                                value="<?= (int) $category[
                                    'id_categoria'
                                ] ?>"
                            >
                                <?= e($category['nombre']) ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="<?= $isAdmin
                        ? 'col-md-4'
                        : 'col-md-5' ?>">
                        <label
                            for="tarifa-estado"
                            class="form-label"
                        >
                            Estado
                        </label>

                        <select
                            id="tarifa-estado"
                            name="estado_tarifa"
                            class="form-select"
                        >
                            <option value="">
                                Todas
                            </option>
                            <option value="activa">
                                Activa
                            </option>
                            <option value="inactiva">
                                Inactiva
                            </option>
                        </select>
                    </div>

                    <div class="col-12 d-grid">
                        <button
                            type="submit"
                            class="btn btn-eco"
                        >
                            <i class="bi bi-file-earmark-pdf me-2"></i>
                            Generar PDF
                        </button>
                    </div>
                </form>
            </div>
        </section>
    </div>

<div class="col-12">
    <section class="eco-card">
        <div class="eco-card-body">
            <div class="report-icon mb-3">
                <i class="bi bi-calculator"></i>
            </div>

            <h2 class="h4 fw-bold">
                Cotizaciones por periodo
            </h2>

            <p class="text-muted-eco">
                Genera el reporte de cotizaciones
                en formato PDF o Excel.
            </p>

            <!--
             | Utilizo la dirección principal porque
             | cada botón enviará una ruta diferente.
             -->
            <form
                action="<?= e(url()) ?>"
                method="get"
                class="row g-3 align-items-end"
            >
                <input
                    type="hidden"
                    name="tipo"
                    value="cotizaciones"
                >

                <div class="col-md-3 col-xl-2">
                    <label
                        for="cotizacion-desde"
                        class="form-label"
                    >
                        Desde
                    </label>

                    <input
                        id="cotizacion-desde"
                        type="date"
                        name="desde"
                        value="<?= e($defaultFrom) ?>"
                        class="form-control"
                    >
                </div>

                <div class="col-md-3 col-xl-2">
                    <label
                        for="cotizacion-hasta"
                        class="form-label"
                    >
                        Hasta
                    </label>

                    <input
                        id="cotizacion-hasta"
                        type="date"
                        name="hasta"
                        value="<?= e($defaultTo) ?>"
                        class="form-control"
                    >
                </div>

                <?php if ($isAdmin): ?>

                <div class="col-md-6 col-xl-3">
                    <label
                        for="cotizacion-centro"
                        class="form-label"
                    >
                        Centro
                    </label>

                    <select
                        id="cotizacion-centro"
                        name="centro"
                        class="form-select"
                    >
                        <option value="0">
                            Todos
                        </option>

                        <?php foreach (
                            $centers as $center
                        ): ?>

                        <option
                            value="<?= (int) $center[
                                'id_punto'
                            ] ?>"
                        >
                            <?= e($center['nombre']) ?>
                        </option>

                        <?php endforeach; ?>
                    </select>
                </div>

                <?php endif; ?>

                <div class="col-md-6 col-xl">
                    <label
                        for="cotizacion-categoria"
                        class="form-label"
                    >
                        Categoría
                    </label>

                    <select
                        id="cotizacion-categoria"
                        name="categoria"
                        class="form-select"
                    >
                        <option value="0">
                            Todas
                        </option>

                        <?php foreach (
                            $categories as $category
                        ): ?>

                        <option
                            value="<?= (int) $category[
                                'id_categoria'
                            ] ?>"
                        >
                            <?= e($category['nombre']) ?>
                        </option>

                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-6 col-xl-2">
                    <label
                        for="cotizacion-estado"
                        class="form-label"
                    >
                        Estado
                    </label>

                    <select
                        id="cotizacion-estado"
                        name="estado_cotizacion"
                        class="form-select"
                    >
                        <option value="">
                            Todos
                        </option>

                        <option value="pendiente">
                            Pendiente
                        </option>

                        <option value="revisada">
                            Revisada
                        </option>

                        <option value="aprobada">
                            Aprobada
                        </option>

                        <option value="rechazada">
                            Rechazada
                        </option>

                        <option value="cancelada">
                            Cancelada
                        </option>
                    </select>
                </div>

                <div class="col-12">
                    <div
                        class="d-grid d-md-flex
                               justify-content-md-end gap-2"
                    >
                        <!--
                         | Solamente el botón presionado
                         | enviará su respectiva ruta.
                         -->
                        <button
                            type="submit"
                            name="ruta"
                            value="reportes/pdf"
                            formtarget="_blank"
                            class="btn btn-eco-outline"
                        >
                            <i
                                class="bi bi-file-earmark-pdf
                                       me-2"
                            ></i>

                            Generar PDF
                        </button>

                        <button
                            type="submit"
                            name="ruta"
                            value="reportes/excel"
                            class="btn btn-eco"
                        >
                            <i
                                class="bi bi-file-earmark-excel
                                       me-2"
                            ></i>

                            Exportar Excel
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </section>
</div>

    <div class="col-12">
        <section class="eco-card">
            <div class="eco-card-body">
                <div class="report-icon mb-3">
                    <i class="bi bi-calendar-check"></i>
                </div>

                <h2 class="h4 fw-bold">
                    Citas por periodo
                </h2>

                <p class="text-muted-eco">
                    Consulta las citas programadas y
                    su estado dentro de un periodo.
                </p>

                <form
                    action="<?= e(url('reportes/pdf')) ?>"
                    method="get"
                    target="_blank"
                    class="row g-3 align-items-end"
                >
                    <input
                        type="hidden"
                        name="ruta"
                        value="reportes/pdf"
                    >

                    <input
                        type="hidden"
                        name="tipo"
                        value="citas"
                    >

                    <div class="col-md-3 col-xl-2">
                        <label
                            for="cita-desde"
                            class="form-label"
                        >
                            Desde
                        </label>

                        <input
                            id="cita-desde"
                            type="date"
                            name="desde"
                            value="<?= e($defaultFrom) ?>"
                            class="form-control"
                        >
                    </div>

                    <div class="col-md-3 col-xl-2">
                        <label
                            for="cita-hasta"
                            class="form-label"
                        >
                            Hasta
                        </label>

                        <input
                            id="cita-hasta"
                            type="date"
                            name="hasta"
                            value="<?= e($defaultTo) ?>"
                            class="form-control"
                        >
                    </div>

                    <?php if ($isAdmin): ?>
                    <div class="col-md-6 col-xl-4">
                        <label
                            for="cita-centro"
                            class="form-label"
                        >
                            Centro
                        </label>

                        <select
                            id="cita-centro"
                            name="centro"
                            class="form-select"
                        >
                            <option value="0">
                                Todos
                            </option>

                            <?php foreach ($centers as $center): ?>
                            <option
                                value="<?= (int) $center['id_punto'] ?>"
                            >
                                <?= e($center['nombre']) ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <?php endif; ?>

                    <div class="col-md-6 col-xl">
                        <label
                            for="cita-estado"
                            class="form-label"
                        >
                            Estado
                        </label>

                        <select
                            id="cita-estado"
                            name="estado_cita"
                            class="form-select"
                        >
                            <option value="">
                                Todos
                            </option>
                            <option value="pendiente">
                                Pendiente
                            </option>
                            <option value="confirmada">
                                Confirmada
                            </option>
                            <option value="atendida">
                                Atendida
                            </option>
                            <option value="cancelada">
                                Cancelada
                            </option>
                        </select>
                    </div>

                    <div class="col-md-6 col-xl-auto d-grid">
                        <button
                            type="submit"
                            class="btn btn-eco"
                        >
                            <i class="bi bi-file-earmark-pdf me-2"></i>
                            Generar PDF
                        </button>
                    </div>
                </form>
            </div>
        </section>
    </div>
</div>