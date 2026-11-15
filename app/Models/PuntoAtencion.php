<?php
declare(strict_types=1);

final class PuntoAtencion extends Model
{
    protected string $table = 'puntos_atencion';

    protected string $primaryKey = 'id_punto';

    protected array $fillable = [
        'nombre',
        'tipo_punto',
        'direccion',
        'municipio',
        'estado_region',
        'codigo_postal',
        'telefono',
        'correo_contacto',
        'tipo_servicio',
        'latitud',
        'longitud',
        'descripcion',
        'estado',
    ];

    public function search(
        string $search,
        string $type,
        string $status,
        bool $onlyActive
    ): array {
        $conditions = [];
        $parameters = [];

        if ($onlyActive) {
            $conditions[] = 'p.estado = ?';
            $parameters[] = 'activo';
        } elseif (
            in_array(
                $status,
                ['activo', 'inactivo'],
                true
            )
        ) {
            $conditions[] = 'p.estado = ?';
            $parameters[] = $status;
        }

        if (
            in_array(
                $type,
                [
                    'empresa',
                    'centro_acopio',
                    'punto_servicio',
                ],
                true
            )
        ) {
            $conditions[] = 'p.tipo_punto = ?';
            $parameters[] = $type;
        }

        if ($search !== '') {
            $conditions[] = '(
                p.nombre LIKE ?
                OR p.direccion LIKE ?
                OR p.municipio LIKE ?
                OR p.codigo_postal LIKE ?
            )';

            $term = '%' . $search . '%';

            $parameters[] = $term;
            $parameters[] = $term;
            $parameters[] = $term;
            $parameters[] = $term;
        }

        $where = $conditions !== []
            ? ' WHERE ' . implode(' AND ', $conditions)
            : '';

        return $this->rows(
            'SELECT
                p.*,
                (
                    SELECT COUNT(*)
                    FROM punto_categoria AS pc
                    WHERE pc.id_punto = p.id_punto
                ) AS total_categorias
             FROM puntos_atencion AS p'
            . $where
            . ' ORDER BY p.nombre',
            $parameters
        );
    }

    public function categories(
    int $pointId,
    bool $onlyActive = false
): array {
    $statusCondition = $onlyActive
        ? ' AND c.estado = ?'
        : '';

    $parameters = [$pointId];

    if ($onlyActive) {
        $parameters[] = 'activo';
    }

    return $this->rows(
        'SELECT
            c.id_categoria,
            c.nombre,
            c.tipo_residuo,
            c.color_hex,
            c.estado,
            pc.acepta_compra,
            pc.acepta_recepcion,
            pc.observaciones
         FROM categorias_residuos AS c
         INNER JOIN punto_categoria AS pc
            ON pc.id_categoria = c.id_categoria
         WHERE pc.id_punto = ?'
        . $statusCondition
        . ' ORDER BY c.nombre',
        $parameters
    );
}

    public function schedules(int $pointId): array
    {
        return $this->rows(
            "SELECT
                id_horario,
                dia_semana,
                hora_apertura,
                hora_cierre,
                cerrado,
                observaciones
             FROM horarios_atencion
             WHERE id_punto = ?
             ORDER BY FIELD(
                dia_semana,
                'lunes',
                'martes',
                'miercoles',
                'jueves',
                'viernes',
                'sabado',
                'domingo'
             )",
            [$pointId]
        );
    }

public function duplicateExists(
    string $name,
    string $address,
    int $exceptId = 0
): bool {
    $sql = 'SELECT COUNT(*)
            FROM puntos_atencion
            WHERE nombre = ?
              AND direccion = ?';

    $parameters = [
        $name,
        $address,
    ];

    if ($exceptId > 0) {
        $sql .= ' AND id_punto <> ?';
        $parameters[] = $exceptId;
    }

    return (int) $this->scalar(
        $sql,
        $parameters
    ) > 0;
}


    
}



