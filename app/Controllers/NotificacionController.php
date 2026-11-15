<?php
declare(strict_types=1);

final class NotificacionController extends Controller
{
    private const TYPE_LABELS = [
        'recuperacion_contrasena' =>
            'Recuperación de contraseña',

        'nuevo_aviso' =>
            'Noticias y avisos',

        'confirmacion_cita' =>
            'Confirmación de cita',

        'cambio_cita' =>
            'Cambios en citas',

        'cancelacion_cita' =>
            'Cancelación de cita',

        'actualizacion_cotizacion' =>
            'Cotizaciones',

        'sistema' =>
            'Sistema',
    ];

    private const TYPE_ICONS = [
        'recuperacion_contrasena' =>
            'bi-key',

        'nuevo_aviso' =>
            'bi-megaphone',

        'confirmacion_cita' =>
            'bi-calendar-check',

        'cambio_cita' =>
            'bi-calendar-event',

        'cancelacion_cita' =>
            'bi-calendar-x',

        'actualizacion_cotizacion' =>
            'bi-cash-coin',

        'sistema' =>
            'bi-info-circle',
    ];

    /**
     * Muestro únicamente las notificaciones
     * pertenecientes al usuario autenticado.
     */
    public function index(): void
    {
        $status = trim(
            (string) ($_GET['estado'] ?? '')
        );

        $type = trim(
            (string) ($_GET['tipo'] ?? '')
        );

        if (
            !in_array(
                $status,
                ['', 'no_leida', 'leida'],
                true
            )
        ) {
            $status = '';
        }

        if (
            $type !== ''
            && !array_key_exists(
                $type,
                self::TYPE_LABELS
            )
        ) {
            $type = '';
        }

        $userId = (int) (
            auth_user()['id_usuario'] ?? 0
        );

        $model = new Notificacion();

        $this->view(
            'notifications/index',
            [
                'pageTitle' => 'Notificaciones',

                'notifications' =>
                    $model->forUser(
                        $userId,
                        $status,
                        $type
                    ),

                'unreadCount' =>
                    $model->unreadCount($userId),

                'status' => $status,
                'type' => $type,

                'typeLabels' =>
                    self::TYPE_LABELS,

                'typeIcons' =>
                    self::TYPE_ICONS,
            ],
            'app'
        );
    }

    /**
     * Marco una notificación y después abro
     * el módulo relacionado con ella.
     */
    public function read(): void
    {
        $notificationId = max(
            0,
            (int) (
                $_POST['id_notificacion'] ?? 0
            )
        );

        $userId = (int) (
            auth_user()['id_usuario'] ?? 0
        );

        $model = new Notificacion();

        /*
         | Aquí no utilizo solamente el identificador.
         | También confirmo que pertenezca al usuario.
         */
        $notification = $model->findOwned(
            $notificationId,
            $userId
        );

        if ($notification === null) {
            $this->failure(
                'No se encontró la notificación solicitada.',
                'notificaciones'
            );
        }

        $model->markAsRead(
            $notificationId,
            $userId
        );

        Security::audit(
            'notificacion_leida',
            [
                'id_notificacion' =>
                    $notificationId,
            ]
        );

        /*
         | Redirijo según la relación guardada.
         | Si el registro relacionado ya no existe,
         | regreso al listado de notificaciones.
         */
        if (
            (int) (
                $notification['id_cita'] ?? 0
            ) > 0
        ) {
            redirect('citas');
        }

        if (
            (int) (
                $notification['id_cotizacion'] ?? 0
            ) > 0
        ) {
            redirect('cotizaciones');
        }

        if (
            (int) (
                $notification['id_publicacion'] ?? 0
            ) > 0
        ) {
            redirect(
                'noticias/detalle',
                [
                    'id' => (int) (
                        $notification[
                            'id_publicacion'
                        ]
                    ),
                ]
            );
        }

        $this->success(
            'La notificación se marcó como leída.',
            'notificaciones'
        );
    }

    /**
     * Marco todas las notificaciones pendientes
     * del usuario autenticado.
     */
    public function readAll(): void
    {
        $userId = (int) (
            auth_user()['id_usuario'] ?? 0
        );

        $updated = (
            new Notificacion()
        )->markAllAsRead($userId);

        Security::audit(
            'notificaciones_leidas',
            [
                'cantidad' => $updated,
            ]
        );

        $this->success(
            $updated > 0
                ? 'Todas las notificaciones se marcaron como leídas.'
                : 'No tenías notificaciones pendientes.',
            'notificaciones'
        );
    }
}