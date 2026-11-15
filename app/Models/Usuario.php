<?php
declare(strict_types=1);

final class Usuario extends Model
{
    protected string $table = 'usuarios';

    protected string $primaryKey = 'id_usuario';

    protected array $fillable = [
        'id_rol',
        'id_punto_asignado',
        'nombre_completo',
        'correo',
        'password_hash',
        'telefono',
        'estado',
    ];

    /**
     * Obtiene una cuenta con su rol y centro.
     * Es utilizado durante el inicio de sesión.
     */
    public function findByEmailWithRole(
        string $email
    ): ?array {
        return $this->row(
            'SELECT
                u.*,
                r.nombre_rol,
                r.estado AS estado_rol,
                p.nombre AS nombre_punto_asignado,
                p.estado AS estado_punto_asignado
             FROM usuarios AS u
             INNER JOIN roles AS r
                ON r.id_rol = u.id_rol
             LEFT JOIN puntos_atencion AS p
                ON p.id_punto = u.id_punto_asignado
             WHERE u.correo = ?
             LIMIT 1',
            [$email]
        );
    }

    /**
     * Obtiene un usuario con su rol y centro asignado.
     */
    public function findWithRole(int $id): ?array
    {
        return $this->row(
            'SELECT
                u.*,
                r.nombre_rol,
                r.estado AS estado_rol,
                p.nombre AS nombre_punto_asignado,
                p.estado AS estado_punto_asignado
             FROM usuarios AS u
             INNER JOIN roles AS r
                ON r.id_rol = u.id_rol
             LEFT JOIN puntos_atencion AS p
                ON p.id_punto = u.id_punto_asignado
             WHERE u.id_usuario = ?
             LIMIT 1',
            [$id]
        );
    }

    public function emailExists(
        string $email,
        int $exceptId = 0
    ): bool {
        $sql = 'SELECT COUNT(*)
                FROM usuarios
                WHERE correo = ?';

        $parameters = [$email];

        if ($exceptId > 0) {
            $sql .= ' AND id_usuario <> ?';
            $parameters[] = $exceptId;
        }

        return (int) $this->scalar(
            $sql,
            $parameters
        ) > 0;
    }

    public function filtered(
        string $search,
        int $role,
        string $status,
        int $limit,
        int $offset
    ): array {
        [$where, $parameters] = $this->filters(
            $search,
            $role,
            $status
        );

        return $this->rows(
            'SELECT
                u.id_usuario,
                u.id_rol,
                u.id_punto_asignado,
                u.nombre_completo,
                u.correo,
                u.telefono,
                u.estado,
                u.fecha_registro,
                r.nombre_rol,
                p.nombre AS nombre_punto_asignado
             FROM usuarios AS u
             INNER JOIN roles AS r
                ON r.id_rol = u.id_rol
             LEFT JOIN puntos_atencion AS p
                ON p.id_punto = u.id_punto_asignado'
            . $where
            . ' ORDER BY u.id_usuario DESC'
            . ' LIMIT ' . $limit
            . ' OFFSET ' . $offset,
            $parameters
        );
    }

    public function countFiltered(
        string $search,
        int $role,
        string $status
    ): int {
        [$where, $parameters] = $this->filters(
            $search,
            $role,
            $status
        );

        return (int) $this->scalar(
            'SELECT COUNT(*)
             FROM usuarios AS u
             INNER JOIN roles AS r
                ON r.id_rol = u.id_rol'
            . $where,
            $parameters
        );
    }

    public function countActiveAdministrators(): int
    {
        return (int) $this->scalar(
            'SELECT COUNT(*)
             FROM usuarios AS u
             INNER JOIN roles AS r
                ON r.id_rol = u.id_rol
             WHERE r.nombre_rol = ?
               AND u.estado = ?',
            [
                'Administrador',
                'activo',
            ]
        );
    }

    private function filters(
        string $search,
        int $role,
        string $status
    ): array {
        $conditions = [];
        $parameters = [];

        if ($search !== '') {
            $conditions[] = '(
                u.nombre_completo LIKE ?
                OR u.correo LIKE ?
                OR u.telefono LIKE ?
            )';

            $term = '%' . $search . '%';

            $parameters[] = $term;
            $parameters[] = $term;
            $parameters[] = $term;
        }

        if ($role > 0) {
            $conditions[] = 'u.id_rol = ?';
            $parameters[] = $role;
        }

        if (
            in_array(
                $status,
                ['activo', 'inactivo', 'bloqueado'],
                true
            )
        ) {
            $conditions[] = 'u.estado = ?';
            $parameters[] = $status;
        }

        $where = $conditions !== []
            ? ' WHERE ' . implode(' AND ', $conditions)
            : '';

        return [$where, $parameters];
    }
}