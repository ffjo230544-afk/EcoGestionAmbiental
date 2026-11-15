<?php
declare(strict_types=1);

final class Usuario extends Model
{
    protected string $table = 'usuarios';

    protected string $primaryKey = 'id_usuario';

    protected array $fillable = [
        'id_rol',
        'nombre_completo',
        'correo',
        'password_hash',
        'telefono',
        'estado',
    ];

    /**
     * Método utilizado por el login.
     */
    public function findByEmailWithRole(
        string $email
    ): ?array {
        return $this->row(
            'SELECT
                u.*,
                r.nombre_rol,
                r.estado AS estado_rol
             FROM usuarios AS u
             INNER JOIN roles AS r
                ON r.id_rol = u.id_rol
             WHERE u.correo = ?
             LIMIT 1',
            [$email]
        );
    }

    public function findWithRole(int $id): ?array
    {
        return $this->row(
            'SELECT
                u.*,
                r.nombre_rol,
                r.estado AS estado_rol
             FROM usuarios AS u
             INNER JOIN roles AS r
                ON r.id_rol = u.id_rol
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

        /*
         | LIMIT y OFFSET se convierten previamente a enteros.
         | Por eso es seguro incorporarlos en esta parte del SQL.
         */
        return $this->rows(
            'SELECT
                u.id_usuario,
                u.id_rol,
                u.nombre_completo,
                u.correo,
                u.telefono,
                u.estado,
                u.fecha_registro,
                r.nombre_rol
             FROM usuarios AS u
             INNER JOIN roles AS r
                ON r.id_rol = u.id_rol'
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