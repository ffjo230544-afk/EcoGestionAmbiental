<?php
declare(strict_types=1);

final class PuntoCategoria extends Model
{
    protected string $table = 'punto_categoria';

    /*
     | La tabla tiene clave primaria compuesta.
     | No utilizaremos find(), create() ni update()
     | del modelo base.
     */
    protected string $primaryKey = 'id_punto';

    protected array $fillable = [
        'id_punto',
        'id_categoria',
        'acepta_compra',
        'acepta_recepcion',
        'observaciones',
    ];

    /**
     * Obtiene las categorías activas y también las inactivas
     * que ya estén relacionadas con el centro.
     */
    public function configuration(int $pointId): array
    {
        return $this->rows(
            'SELECT
                c.id_categoria,
                c.nombre,
                c.descripcion,
                c.tipo_residuo,
                c.icono,
                c.color_hex,
                c.estado,
                pc.id_punto IS NOT NULL AS relacionada,
                COALESCE(pc.acepta_compra, 0)
                    AS acepta_compra,
                COALESCE(pc.acepta_recepcion, 1)
                    AS acepta_recepcion,
                pc.observaciones
             FROM categorias_residuos AS c
             LEFT JOIN punto_categoria AS pc
                ON pc.id_categoria = c.id_categoria
               AND pc.id_punto = ?
             WHERE
                c.estado = ?
                OR pc.id_punto IS NOT NULL
             ORDER BY
                c.estado DESC,
                c.nombre',
            [
                $pointId,
                'activo',
            ]
        );
    }

    /**
     * Reemplaza la configuración completa dentro
     * de una transacción.
     */
    public function saveForCenter(
        int $pointId,
        array $relations
    ): void {
        $database = $this->db();

        $delete = $database->prepare(
            'DELETE FROM punto_categoria
             WHERE id_punto = ?'
        );

        $insert = $database->prepare(
            'INSERT INTO punto_categoria (
                id_punto,
                id_categoria,
                acepta_compra,
                acepta_recepcion,
                observaciones
             ) VALUES (?, ?, ?, ?, ?)'
        );

        $database->beginTransaction();

        try {
            $delete->execute([$pointId]);

            foreach ($relations as $relation) {
                $insert->execute([
                    $pointId,
                    $relation['id_categoria'],
                    $relation['acepta_compra'],
                    $relation['acepta_recepcion'],
                    $relation['observaciones'],
                ]);
            }

            $database->commit();
        } catch (Throwable $exception) {
            if ($database->inTransaction()) {
                $database->rollBack();
            }

            throw $exception;
        }
    }
}