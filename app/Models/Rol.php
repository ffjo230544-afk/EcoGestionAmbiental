<?php
declare(strict_types=1);

final class Rol extends Model
{
    protected string $table = 'roles';

    protected string $primaryKey = 'id_rol';

    protected array $fillable = [
        'nombre_rol',
        'descripcion',
        'estado',
    ];

    public function active(): array
    {
        return $this->rows(
            'SELECT
                id_rol,
                nombre_rol,
                descripcion
             FROM roles
             WHERE estado = ?
             ORDER BY id_rol',
            ['activo']
        );
    }
}