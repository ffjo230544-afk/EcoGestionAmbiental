<?php
declare(strict_types=1);

/**
 * Aquí defino las operaciones comunes que podrán utilizar mis modelos.
 * Cada modelo específico indicará su tabla y su llave primaria.
 */
abstract class Model
{
    // Aquí guardaré el nombre de la tabla utilizada por cada modelo.
    protected string $table;

    // Aquí guardaré el nombre de la llave primaria.
    protected string $primaryKey;

    // Aquí definiré qué columnas se pueden registrar o modificar.
    protected array $fillable = [];

    /**
     * Aquí obtengo la conexión general con MySQL.
     */
    protected function db(): PDO
    {
        return Database::connection();
    }

    /**
     * Aquí preparo y ejecuto una consulta SQL.
     */
    public function query(
        string $sql,
        array $parameters = []
    ): PDOStatement {
        $statement = $this->db()->prepare($sql);
        $statement->execute($parameters);

        return $statement;
    }

    /**
     * Aquí obtengo varios registros de una consulta.
     */
    public function rows(
        string $sql,
        array $parameters = []
    ): array {
        return $this->query(
            $sql,
            $parameters
        )->fetchAll();
    }

    /**
     * Aquí obtengo solamente un registro.
     */
    public function row(
        string $sql,
        array $parameters = []
    ): ?array {
        $result = $this->query(
            $sql,
            $parameters
        )->fetch();

        return $result === false
            ? null
            : $result;
    }

    /**
     * Aquí obtengo solamente un valor de una consulta.
     */
    public function scalar(
        string $sql,
        array $parameters = []
    ): mixed {
        return $this->query(
            $sql,
            $parameters
        )->fetchColumn();
    }

    /**
     * Aquí obtengo todos los registros de la tabla.
     */
    public function all(string $orderBy = ''): array
    {
        /*
         | Solo permito ordenar por una columna conocida.
         | Esto evita introducir directamente nombres peligrosos.
         */
        $allowedColumns = array_merge(
            [$this->primaryKey],
            $this->fillable
        );

        $order = in_array(
            $orderBy,
            $allowedColumns,
            true
        )
            ? $orderBy
            : $this->primaryKey;

        return $this->rows(
            "SELECT * FROM {$this->table}
             ORDER BY {$order}"
        );
    }

    /**
     * Aquí busco un registro utilizando su llave primaria.
     */
    public function find(int $id): ?array
    {
        return $this->row(
            "SELECT *
             FROM {$this->table}
             WHERE {$this->primaryKey} = ?
             LIMIT 1",
            [$id]
        );
    }

    /**
     * Aquí registro información en la tabla.
     */
    public function create(array $values): int
    {
        /*
         | Solamente conservo los campos permitidos.
         | Así evito registrar columnas que el modelo no autoriza.
         */
        $values = array_intersect_key(
            $values,
            array_flip($this->fillable)
        );

        if ($values === []) {
            throw new InvalidArgumentException(
                'No se proporcionaron campos válidos para registrar.'
            );
        }

        $columns = array_keys($values);

        $markers = implode(
            ', ',
            array_fill(0, count($columns), '?')
        );

        $sql = sprintf(
            'INSERT INTO %s (%s) VALUES (%s)',
            $this->table,
            implode(', ', $columns),
            $markers
        );

        $this->query(
            $sql,
            array_values($values)
        );

        return (int) $this->db()->lastInsertId();
    }

    /**
     * Aquí actualizo un registro existente.
     */
    public function update(
        int $id,
        array $values
    ): bool {
        // Solamente permito modificar campos autorizados.
        $values = array_intersect_key(
            $values,
            array_flip($this->fillable)
        );

        if ($values === []) {
            return false;
        }

        $assignments = implode(
            ', ',
            array_map(
                static fn(string $field): string =>
                    "{$field} = ?",
                array_keys($values)
            )
        );

        $parameters = [
            ...array_values($values),
            $id,
        ];

        $statement = $this->query(
            "UPDATE {$this->table}
             SET {$assignments}
             WHERE {$this->primaryKey} = ?",
            $parameters
        );

        return $statement->rowCount() >= 0;
    }
}