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
}