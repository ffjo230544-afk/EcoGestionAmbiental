<?php
declare(strict_types=1);

final class Notificacion extends Model
{
    protected string $table = 'notificaciones';

    protected string $primaryKey = 'id_notificacion';

    protected array $fillable = [
        'id_usuario',
        'id_cita',
        'id_cotizacion',
        'id_publicacion',
        'tipo_notificacion',
        'titulo',
        'mensaje',
        'medio',
        'estado',
        'fecha_lectura',
    ];

    private const TYPES = [
        'recuperacion_contrasena',
        'nuevo_aviso',
        'confirmacion_cita',
        'cambio_cita',
        'cancelacion_cita',
        'actualizacion_cotizacion',
        'sistema',
    ];

    private const MEDIA = [
        'interna',
        'correo',
        'interna_correo',
    ];

    /**
     * Obtengo las notificaciones pertenecientes
     * exclusivamente al usuario indicado.
     */
    public function forUser(
        int $userId,
        string $status = '',
        string $type = ''
    ): array {
        $conditions = [
            'n.id_usuario = ?',
        ];

        $parameters = [
            $userId,
        ];

        if (
            in_array(
                $status,
                ['no_leida', 'leida'],
                true
            )
        ) {
            $conditions[] = 'n.estado = ?';
            $parameters[] = $status;
        }

        if (
            in_array(
                $type,
                self::TYPES,
                true
            )
        ) {
            $conditions[] = 'n.tipo_notificacion = ?';
            $parameters[] = $type;
        }

        return $this->rows(
            'SELECT n.*
             FROM notificaciones AS n
             WHERE '
             . implode(' AND ', $conditions)
             . '
             ORDER BY
                CASE
                    WHEN n.estado = "no_leida" THEN 0
                    ELSE 1
                END,
                n.fecha_creacion DESC,
                n.id_notificacion DESC',
            $parameters
        );
    }

    /**
     * Cuento las notificaciones que todavía
     * no ha leído el usuario.
     */
    public function unreadCount(int $userId): int
    {
        return (int) $this->scalar(
            'SELECT COUNT(*)
             FROM notificaciones
             WHERE id_usuario = ?
               AND estado = "no_leida"',
            [$userId]
        );
    }

    /**
     * Busco una notificación comprobando
     * también que pertenezca al usuario.
     */
    public function findOwned(
        int $notificationId,
        int $userId
    ): ?array {
        return $this->row(
            'SELECT *
             FROM notificaciones
             WHERE id_notificacion = ?
               AND id_usuario = ?
             LIMIT 1',
            [
                $notificationId,
                $userId,
            ]
        );
    }

    /**
     * Marco una notificación como leída.
     */
    public function markAsRead(
        int $notificationId,
        int $userId
    ): bool {
        $statement = $this->query(
            'UPDATE notificaciones
             SET
                estado = "leida",
                fecha_lectura = COALESCE(
                    fecha_lectura,
                    NOW()
                )
             WHERE id_notificacion = ?
               AND id_usuario = ?',
            [
                $notificationId,
                $userId,
            ]
        );

        return $statement->rowCount() >= 0;
    }

    /**
     * Marco todas las notificaciones del usuario.
     */
    public function markAllAsRead(int $userId): int
    {
        $statement = $this->query(
            'UPDATE notificaciones
             SET
                estado = "leida",
                fecha_lectura = COALESCE(
                    fecha_lectura,
                    NOW()
                )
             WHERE id_usuario = ?
               AND estado = "no_leida"',
            [$userId]
        );

        return $statement->rowCount();
    }

    /**
     * Registro una notificación para un usuario.
     */
    public function notifyUser(
        int $userId,
        array $notification
    ): int {
        if ($userId <= 0) {
            throw new InvalidArgumentException(
                'El usuario de la notificación no es válido.'
            );
        }

        $type = (string) (
            $notification['tipo_notificacion']
            ?? 'sistema'
        );

        if (!in_array($type, self::TYPES, true)) {
            $type = 'sistema';
        }

        $medium = (string) (
            $notification['medio'] ?? 'interna'
        );

        if (!in_array($medium, self::MEDIA, true)) {
            $medium = 'interna';
        }

        $title = trim(
            (string) ($notification['titulo'] ?? '')
        );

        $message = trim(
            (string) ($notification['mensaje'] ?? '')
        );

        if ($title === '' || $message === '') {
            throw new InvalidArgumentException(
                'La notificación necesita título y mensaje.'
            );
        }

        return $this->create([
            'id_usuario' => $userId,

            'id_cita' => $this->relatedId(
                $notification['id_cita'] ?? null
            ),

            'id_cotizacion' => $this->relatedId(
                $notification['id_cotizacion'] ?? null
            ),

            'id_publicacion' => $this->relatedId(
                $notification['id_publicacion'] ?? null
            ),

            'tipo_notificacion' => $type,

            'titulo' => mb_substr(
                $title,
                0,
                120
            ),

            'mensaje' => mb_substr(
                $message,
                0,
                500
            ),

            'medio' => $medium,
            'estado' => 'no_leida',
        ]);
    }

    /**
     * Notifico a los administradores y únicamente
     * a los coordinadores del centro correspondiente.
     */
    public function notifyManagers(
        ?int $centerId,
        array $notification,
        ?int $exceptUserId = null
    ): void {
        $parameters = [];

        $sql = '
            SELECT DISTINCT u.id_usuario
            FROM usuarios AS u
            INNER JOIN roles AS r
                ON r.id_rol = u.id_rol
            WHERE u.estado = "activo"
              AND r.estado = "activo"
              AND (
                    LOWER(r.nombre_rol) = "administrador"
        ';

        if ($centerId !== null && $centerId > 0) {
            $sql .= '
                    OR (
                        LOWER(r.nombre_rol) = "coordinador"
                        AND u.id_punto_asignado = ?
                    )
            ';

            $parameters[] = $centerId;
        }

        $sql .= ')';

        if (
            $exceptUserId !== null
            && $exceptUserId > 0
        ) {
            $sql .= ' AND u.id_usuario <> ?';
            $parameters[] = $exceptUserId;
        }

        $recipients = $this->rows(
            $sql,
            $parameters
        );

        /*
         | Uso una transacción para que todos los responsables
         | reciban el evento o ninguno quede registrado.
         */
        $database = $this->db();
        $ownsTransaction = !$database->inTransaction();

        if ($ownsTransaction) {
            $database->beginTransaction();
        }

        try {
            foreach ($recipients as $recipient) {
                $this->notifyUser(
                    (int) $recipient['id_usuario'],
                    $notification
                );
            }

            if ($ownsTransaction) {
                $database->commit();
            }
        } catch (Throwable $exception) {
            if (
                $ownsTransaction
                && $database->inTransaction()
            ) {
                $database->rollBack();
            }

            throw $exception;
        }
    }

    /**
     * Notifico a todos los usuarios activos.
     * Se utilizará al publicar noticias o avisos.
     */
    public function notifyAllActiveUsers(
        array $notification,
        ?int $exceptUserId = null
    ): void {
        $parameters = [];

        $sql = '
            SELECT DISTINCT u.id_usuario
            FROM usuarios AS u
            INNER JOIN roles AS r
                ON r.id_rol = u.id_rol
            WHERE u.estado = "activo"
              AND r.estado = "activo"
        ';

        if (
            $exceptUserId !== null
            && $exceptUserId > 0
        ) {
            $sql .= ' AND u.id_usuario <> ?';
            $parameters[] = $exceptUserId;
        }

        $recipients = $this->rows(
            $sql,
            $parameters
        );

        $database = $this->db();
        $ownsTransaction = !$database->inTransaction();

        if ($ownsTransaction) {
            $database->beginTransaction();
        }

        try {
            foreach ($recipients as $recipient) {
                $this->notifyUser(
                    (int) $recipient['id_usuario'],
                    $notification
                );
            }

            if ($ownsTransaction) {
                $database->commit();
            }
        } catch (Throwable $exception) {
            if (
                $ownsTransaction
                && $database->inTransaction()
            ) {
                $database->rollBack();
            }

            throw $exception;
        }
    }

    /**
     * Convierto una relación opcional a entero o null.
     */
    private function relatedId(mixed $value): ?int
    {
        $id = (int) ($value ?? 0);

        return $id > 0
            ? $id
            : null;
    }
}