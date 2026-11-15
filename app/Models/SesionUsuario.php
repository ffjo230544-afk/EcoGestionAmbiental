<?php
declare(strict_types=1);

/**
 * Aquí represento el historial de sesiones del sistema.
 */
final class SesionUsuario extends Model
{
    protected string $table = 'sesiones_usuario';

    protected string $primaryKey = 'id_sesion';

    protected array $fillable = [
        'id_usuario',
        'token_sesion',
        'ip_acceso',
        'navegador',
        'fecha_ultima_actividad',
        'fecha_cierre',
        'motivo_cierre',
        'estado',
    ];



    
/**
 * Aquí cierro las demás sesiones activas
 * después de cambiar una contraseña.
 *
 * La sesión utilizada para realizar el cambio
 * permanece abierta.
 */
public function closeOtherActiveSessions(
    int $userId,
    int $currentSessionId
): int {
    $statement = $this->query(
        'UPDATE sesiones_usuario
         SET
            fecha_cierre = COALESCE(
                fecha_cierre,
                CURRENT_TIMESTAMP
            ),
            motivo_cierre = ?,
            estado = ?
         WHERE id_usuario = ?
           AND estado = ?
           AND id_sesion <> ?',
        [
            'seguridad',
            'cerrada',
            $userId,
            'activa',
            $currentSessionId,
        ]
    );

    return $statement->rowCount();
}


/**
 * Aquí cierro todas las sesiones activas
 * después de recuperar una contraseña.
 */
public function closeAllActiveSessions(
    int $userId
): int {
    $statement = $this->query(
        'UPDATE sesiones_usuario
         SET
            fecha_cierre = COALESCE(
                fecha_cierre,
                CURRENT_TIMESTAMP
            ),
            motivo_cierre = ?,
            estado = ?
         WHERE id_usuario = ?
           AND estado = ?',
        [
            'seguridad',
            'cerrada',
            $userId,
            'activa',
        ]
    );

    return $statement->rowCount();
}





}