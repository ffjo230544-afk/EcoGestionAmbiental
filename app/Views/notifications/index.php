<div class="page-header">
    <div>
        <span class="section-label">
            Mi cuenta
        </span>

        <h1 class="page-title mt-1">
            Notificaciones
        </h1>

        <p class="page-subtitle">
            Consulta avisos relacionados con tus
            citas, cotizaciones y servicios.
        </p>
    </div>

    <?php if ($unreadCount > 0): ?>
    <form
        action="<?= e(url(
            'notificaciones/leer-todas'
        )) ?>"
        method="post"
        class="m-0"
    >
        <?= csrf_field() ?>

        <button
            type="submit"
            class="btn btn-eco-outline"
        >
            <i class="bi bi-check2-all me-2"></i>

            Marcar todas como leídas
        </button>
    </form>
    <?php endif; ?>
</div>

<section class="eco-card mb-4">
    <div class="eco-card-body">
        <form
            action="<?= e(url('notificaciones')) ?>"
            method="get"
            class="row g-3 align-items-end"
        >
            <input
                type="hidden"
                name="ruta"
                value="notificaciones"
            >

            <div class="col-md-4">
                <label
                    for="estado"
                    class="form-label"
                >
                    Estado
                </label>

                <select
                    id="estado"
                    name="estado"
                    class="form-select"
                >
                    <option value="">
                        Todas
                    </option>

                    <option
                        value="no_leida"
                        <?= selected(
                            $status,
                            'no_leida'
                        ) ?>
                    >
                        No leídas
                    </option>

                    <option
                        value="leida"
                        <?= selected(
                            $status,
                            'leida'
                        ) ?>
                    >
                        Leídas
                    </option>
                </select>
            </div>

            <div class="col-md-5">
                <label
                    for="tipo"
                    class="form-label"
                >
                    Tipo
                </label>

                <select
                    id="tipo"
                    name="tipo"
                    class="form-select"
                >
                    <option value="">
                        Todos los tipos
                    </option>

                    <?php foreach (
                        $typeLabels as $value => $label
                    ): ?>
                    <option
                        value="<?= e($value) ?>"
                        <?= selected(
                            $type,
                            $value
                        ) ?>
                    >
                        <?= e($label) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-md-3">
                <div class="d-grid gap-2">
                    <button
                        type="submit"
                        class="btn btn-eco"
                    >
                        <i class="bi bi-funnel me-2"></i>
                        Filtrar
                    </button>

                    <a
                        href="<?= e(url(
                            'notificaciones'
                        )) ?>"
                        class="btn btn-eco-outline"
                    >
                        Limpiar
                    </a>
                </div>
            </div>
        </form>
    </div>
</section>

<div class="d-flex justify-content-between
            align-items-center gap-3 mb-3">
    <strong>
        <?= count($notifications) ?>
        notificación(es)
    </strong>

    <?php if ($unreadCount > 0): ?>
    <span class="status-pill status-no_leida">
        <?= (int) $unreadCount ?>
        pendiente(s)
    </span>
    <?php endif; ?>
</div>

<?php if ($notifications !== []): ?>

<div class="notification-list">
    <?php foreach (
        $notifications as $notification
    ): ?>

    <?php
        $isUnread =
            $notification['estado'] === 'no_leida';

        $notificationType = (
            $notification['tipo_notificacion']
        );

        $hasRelatedResource =
            (int) (
                $notification['id_cita'] ?? 0
            ) > 0
            || (int) (
                $notification['id_cotizacion'] ?? 0
            ) > 0
            || (int) (
                $notification['id_publicacion'] ?? 0
            ) > 0;
    ?>

    <article
        class="notification-item
               <?= $isUnread
                   ? 'is-unread'
                   : '' ?>"
    >
        <div class="notification-icon">
            <i class="bi <?= e(
                $typeIcons[$notificationType]
                ?? 'bi-bell'
            ) ?>"></i>
        </div>

        <div class="notification-content">
            <div class="d-flex flex-wrap
                        justify-content-between
                        align-items-start gap-2">
                <div>
                    <div class="small text-eco fw-bold mb-1">
                        <?= e(
                            $typeLabels[$notificationType]
                            ?? 'Notificación'
                        ) ?>
                    </div>

                    <h2 class="h5 fw-bold mb-1">
                        <?= e(
                            $notification['titulo']
                        ) ?>
                    </h2>
                </div>

                <span
                    class="status-pill
                           status-<?= e(
                               $notification['estado']
                           ) ?>"
                >
                    <?= $isUnread
                        ? 'No leída'
                        : 'Leída' ?>
                </span>
            </div>

            <p class="text-muted-eco mb-2">
                <?= e(
                    $notification['mensaje']
                ) ?>
            </p>

            <div class="small text-muted-eco">
                <i class="bi bi-clock me-1"></i>

                <?= e(date_mx(
                    $notification['fecha_creacion'],
                    'd/m/Y H:i'
                )) ?>
            </div>
        </div>

        <?php if (
            $isUnread
            || $hasRelatedResource
        ): ?>
        <form
            action="<?= e(url(
                'notificaciones/leer'
            )) ?>"
            method="post"
            class="notification-action"
        >
            <?= csrf_field() ?>

            <input
                type="hidden"
                name="id_notificacion"
                value="<?= (int) (
                    $notification[
                        'id_notificacion'
                    ]
                ) ?>"
            >

            <button
                type="submit"
                class="btn btn-eco-outline"
            >
                <?php if ($hasRelatedResource): ?>
                    <i class="bi bi-box-arrow-up-right me-1"></i>
                    Abrir
                <?php else: ?>
                    <i class="bi bi-check2 me-1"></i>
                    Marcar como leída
                <?php endif; ?>
            </button>
        </form>
        <?php endif; ?>
    </article>

    <?php endforeach; ?>
</div>

<?php else: ?>

<section class="eco-card">
    <div class="eco-card-body text-center py-5">
        <div class="empty-icon mb-3">
            <i class="bi bi-bell-slash"></i>
        </div>

        <h2 class="h4 fw-bold">
            No hay notificaciones
        </h2>

        <p class="text-muted-eco mb-0">
            No se encontraron avisos con los
            filtros seleccionados.
        </p>
    </div>
</section>

<?php endif; ?>