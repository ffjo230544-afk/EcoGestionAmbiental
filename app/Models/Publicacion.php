<?php
declare(strict_types=1);

final class Publicacion extends Model
{
    protected string $table = 'publicaciones';

    protected string $primaryKey = 'id_publicacion';

    protected array $fillable = [
        'id_autor',
        'tipo_publicacion',
        'titulo',
        'resumen',
        'contenido',
        'imagen_portada',
        'fecha_publicacion',
        'estado',
    ];

    private const EDUCATIONAL_TYPES = [
        'material_educativo',
        'ficha_informativa',
    ];

    /**
     * Obtiene las publicaciones de la base de
     * conocimiento utilizando filtros y paginación.
     */
    public function paginateKnowledge(
        array $filters,
        bool $onlyPublished,
        int $page,
        int $perPage = 9
    ): array {
        $clauses = [
            "p.tipo_publicacion IN (
                'material_educativo',
                'ficha_informativa'
            )",
        ];

        $parameters = [];

        if ($onlyPublished) {
            $clauses[] = "p.estado = 'publicado'";
        }

        $search = trim(
            (string) ($filters['search'] ?? '')
        );

        if ($search !== '') {
            $clauses[] = '(
                p.titulo LIKE ?
                OR p.resumen LIKE ?
                OR p.contenido LIKE ?
            )';

            $like = '%' . $search . '%';

            $parameters[] = $like;
            $parameters[] = $like;
            $parameters[] = $like;
        }

        $type = (string) (
            $filters['type'] ?? ''
        );

        if (
            in_array(
                $type,
                self::EDUCATIONAL_TYPES,
                true
            )
        ) {
            $clauses[] = 'p.tipo_publicacion = ?';
            $parameters[] = $type;
        }

        $status = (string) (
            $filters['status'] ?? ''
        );

        if (
            !$onlyPublished
            && in_array(
                $status,
                [
                    'borrador',
                    'publicado',
                    'inactivo',
                ],
                true
            )
        ) {
            $clauses[] = 'p.estado = ?';
            $parameters[] = $status;
        }

        $categoryId = (int) (
            $filters['category'] ?? 0
        );

        if ($categoryId > 0) {
            $clauses[] = 'EXISTS (
                SELECT 1
                FROM publicacion_categoria AS filter_pc
                WHERE filter_pc.id_publicacion =
                      p.id_publicacion
                  AND filter_pc.id_categoria = ?
            )';

            $parameters[] = $categoryId;
        }

        $where = implode(
            ' AND ',
            $clauses
        );

        $total = (int) $this->scalar(
            'SELECT COUNT(*)
             FROM publicaciones AS p
             INNER JOIN usuarios AS u
                ON u.id_usuario = p.id_autor
             WHERE ' . $where,
            $parameters
        );

        $perPage = max(
            1,
            min(30, $perPage)
        );

        $totalPages = max(
            1,
            (int) ceil($total / $perPage)
        );

        $page = max(
            1,
            min($page, $totalPages)
        );

        $offset = ($page - 1) * $perPage;

        $visibleCategories = $onlyPublished
            ? " AND c.estado = 'activo'
                AND c.visible_base_conocimiento = 1"
            : '';

        $publications = $this->rows(
            'SELECT
                p.*,
                u.nombre_completo AS autor,

                (
                    SELECT GROUP_CONCAT(
                        c.nombre
                        ORDER BY c.nombre
                        SEPARATOR ", "
                    )
                    FROM publicacion_categoria AS pc
                    INNER JOIN categorias_residuos AS c
                        ON c.id_categoria =
                           pc.id_categoria
                    WHERE pc.id_publicacion =
                          p.id_publicacion'
                    . $visibleCategories .
                ') AS categorias

             FROM publicaciones AS p

             INNER JOIN usuarios AS u
                ON u.id_usuario = p.id_autor

             WHERE ' . $where . '

             ORDER BY
                COALESCE(
                    p.fecha_publicacion,
                    p.fecha_creacion
                ) DESC,
                p.id_publicacion DESC

             LIMIT ' . $perPage . '
             OFFSET ' . $offset,
            $parameters
        );

        return [
            'records' => $publications,
            'total' => $total,
            'page' => $page,
            'pages' => $totalPages,
            'per_page' => $perPage,
        ];
    }

    /**
     * Busca una publicación que pertenezca
     * exclusivamente a la sección educativa.
     */
    public function findKnowledge(
        int $publicationId,
        bool $onlyPublished = false
    ): ?array {
        $statusCondition = $onlyPublished
            ? " AND p.estado = 'publicado'"
            : '';

        return $this->row(
            'SELECT
                p.*,
                u.nombre_completo AS autor
             FROM publicaciones AS p
             INNER JOIN usuarios AS u
                ON u.id_usuario = p.id_autor
             WHERE p.id_publicacion = ?
               AND p.tipo_publicacion IN (
                    "material_educativo",
                    "ficha_informativa"
               )'
               . $statusCondition .
            ' LIMIT 1',
            [$publicationId]
        );
    }

    /**
     * Obtiene las categorías relacionadas con
     * una publicación.
     */
    public function categoriesForPublication(
        int $publicationId,
        bool $onlyVisible = false
    ): array {
        $visibleCondition = $onlyVisible
            ? " AND c.estado = 'activo'
                AND c.visible_base_conocimiento = 1"
            : '';

        return $this->rows(
            'SELECT
                c.id_categoria,
                c.nombre,
                c.icono,
                c.color_hex,
                c.estado,
                c.visible_base_conocimiento
             FROM publicacion_categoria AS pc
             INNER JOIN categorias_residuos AS c
                ON c.id_categoria = pc.id_categoria
             WHERE pc.id_publicacion = ?'
             . $visibleCondition .
            ' ORDER BY c.nombre',
            [$publicationId]
        );
    }

    /**
     * Obtiene las categorías que pueden relacionarse
     * con una publicación educativa.
     */
    public function categoriesForForm(
        int $publicationId = 0
    ): array {
        return $this->rows(
            'SELECT
                c.id_categoria,
                c.nombre,
                c.estado,
                c.visible_base_conocimiento
             FROM categorias_residuos AS c
             WHERE (
                c.estado = "activo"
                AND c.visible_base_conocimiento = 1
             )
             OR EXISTS (
                SELECT 1
                FROM publicacion_categoria AS pc
                WHERE pc.id_categoria =
                      c.id_categoria
                  AND pc.id_publicacion = ?
             )
             ORDER BY c.nombre',
            [$publicationId]
        );
    }

    /**
     * Guarda la publicación y sus categorías
     * dentro de una transacción.
     */
    public function saveWithCategories(
        ?int $publicationId,
        array $publication,
        array $categoryIds
    ): int {
        $database = $this->db();

        $database->beginTransaction();

        try {
            if ($publicationId === null) {
                $publicationId = $this->create(
                    $publication
                );
            } else {
                $this->update(
                    $publicationId,
                    $publication
                );
            }

            $this->query(
                'DELETE FROM publicacion_categoria
                 WHERE id_publicacion = ?',
                [$publicationId]
            );

            foreach ($categoryIds as $categoryId) {
                $this->query(
                    'INSERT INTO publicacion_categoria (
                        id_publicacion,
                        id_categoria
                     ) VALUES (?, ?)',
                    [
                        $publicationId,
                        $categoryId,
                    ]
                );
            }

            $database->commit();

            return $publicationId;
        } catch (Throwable $exception) {
            if ($database->inTransaction()) {
                $database->rollBack();
            }

            throw $exception;
        }
    }
}