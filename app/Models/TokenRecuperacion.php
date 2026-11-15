<?php
declare(strict_types=1);

/**
 * Aquí administro los enlaces temporales
 * utilizados para recuperar contraseñas.
 */
final class TokenRecuperacion extends Model
{
    protected string $table =
        'tokens_recuperacion';

    protected string $primaryKey = 'id_token';

    protected array $fillable = [
        'id_usuario',
        'token',
        'fecha_expiracion',
        'usado',
    ];

    /**
     * Aquí genero un token nuevo.
     *
     * Solamente regreso el token original una vez.
     * MySQL almacena únicamente su hash.
     */
    public function issue(
        int $userId,
        int $minutes
    ): string {
        if ($userId <= 0) {
            throw new InvalidArgumentException(
                'El usuario del token no es válido.'
            );
        }

        $minutes = max(
            5,
            min($minutes, 120)
        );

        $plainToken = bin2hex(
            random_bytes(32)
        );

        $tokenHash = hash(
            'sha256',
            $plainToken
        );

        $database = $this->db();

        $startedTransaction =
            !$database->inTransaction();

        if ($startedTransaction) {
            $database->beginTransaction();
        }

        try {
            /*
             | Invalido los enlaces anteriores.
             | Solamente debe existir una recuperación
             | vigente por cuenta.
             */
            $this->query(
                'UPDATE tokens_recuperacion
                 SET usado = 1
                 WHERE id_usuario = ?
                   AND usado = 0',
                [$userId]
            );

            $this->create([
                'id_usuario' => $userId,
                'token' => $tokenHash,

                'fecha_expiracion' => date(
                    'Y-m-d H:i:s',
                    time() + ($minutes * 60)
                ),

                'usado' => 0,
            ]);

            if ($startedTransaction) {
                $database->commit();
            }
        } catch (Throwable $exception) {
            if (
                $startedTransaction
                && $database->inTransaction()
            ) {
                $database->rollBack();
            }

            throw $exception;
        }

        return $plainToken;
    }

    /**
     * Aquí busco un token válido mediante
     * el hash del valor recibido.
     */
    public function findValid(
        string $plainToken,
        bool $lockRecord = false
    ): ?array {
        if (
            !preg_match(
                '/^[a-f0-9]{64}$/i',
                $plainToken
            )
        ) {
            return null;
        }

        $sql = '
            SELECT
                t.*,
                u.nombre_completo,
                u.correo,
                u.estado AS estado_usuario,
                r.estado AS estado_rol

            FROM tokens_recuperacion AS t

            INNER JOIN usuarios AS u
                ON u.id_usuario = t.id_usuario

            INNER JOIN roles AS r
                ON r.id_rol = u.id_rol

            WHERE t.token = ?
              AND t.usado = 0
              AND t.fecha_expiracion >= NOW()
              AND u.estado = "activo"
              AND r.estado = "activo"

            LIMIT 1
        ';

        if ($lockRecord) {
            /*
             | Durante el cambio bloqueo el registro
             | para impedir dos usos simultáneos.
             */
            $sql .= ' FOR UPDATE';
        }

        return $this->row(
            $sql,
            [
                hash(
                    'sha256',
                    $plainToken
                ),
            ]
        );
    }

    /**
     * Aquí marco el enlace como utilizado.
     */
    public function markAsUsed(int $tokenId): bool
    {
        return $this->update(
            $tokenId,
            ['usado' => 1]
        );
    }

    /**
     * Después de cambiar la contraseña invalido
     * cualquier otro enlace de esa cuenta.
     */
    public function invalidateOthers(
        int $userId,
        int $usedTokenId
    ): int {
        $statement = $this->query(
            'UPDATE tokens_recuperacion
             SET usado = 1
             WHERE id_usuario = ?
               AND id_token <> ?
               AND usado = 0',
            [
                $userId,
                $usedTokenId,
            ]
        );

        return $statement->rowCount();
    }
}