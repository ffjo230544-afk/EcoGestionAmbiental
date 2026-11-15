<?php
declare(strict_types=1);

final class ActividadGamificacion extends Model
{
    protected string $table = 'actividades_gamificacion';

    protected string $primaryKey = 'id_actividad';

    protected array $fillable = [
        'nombre',
        'tipo_actividad',
        'descripcion',
        'nivel',
        'puntos_maximos',
        'instrucciones',
        'estado',
    ];

    public function findActiveByName(string $name): ?array
    {
        return $this->row(
            'SELECT *
             FROM actividades_gamificacion
             WHERE nombre = ?
               AND estado = ?
             LIMIT 1',
            [$name, 'activa']
        );
    }

    public function byNames(array $names): array
    {
        $names = array_values(
            array_filter(
                $names,
                static fn(mixed $name): bool =>
                    is_string($name) && trim($name) !== ''
            )
        );

        if ($names === []) {
            return [];
        }

        $markers = implode(
            ', ',
            array_fill(0, count($names), '?')
        );

        return $this->rows(
            "SELECT *
             FROM actividades_gamificacion
             WHERE nombre IN ({$markers})
             ORDER BY id_actividad",
            $names
        );
    }
}