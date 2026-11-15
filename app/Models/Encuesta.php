<?php
declare(strict_types=1);

/**
 * Administra los datos generales de las encuestas
 * y la lectura de sus preguntas y opciones.
 */
final class Encuesta extends Model
{
    protected string $table = 'encuestas';

    protected string $primaryKey = 'id_encuesta';

    protected array $fillable = [
        'id_creador',
        'titulo',
        'objetivo',
        'descripcion',
        'fecha_inicio',
        'fecha_cierre',
        'estado',
    ];

    /**
     * Obtiene las encuestas permitidas para el rol actual.
     *
     * El ciudadano solamente recibe encuestas publicadas
     * y que se encuentren dentro de la vigencia.
     */
    public function search(
        string $search,
        string $status,
        bool $onlyAvailable,
        int $userId
    ): array {
        $conditions = [];
        $parameters = [];

        if ($onlyAvailable) {
            $conditions[] = "e.estado = 'publicada'";

            $conditions[] = '(
                e.fecha_inicio IS NULL
                OR e.fecha_inicio <= CURRENT_DATE()
            )';

            $conditions[] = '(
                e.fecha_cierre IS NULL
                OR e.fecha_cierre >= CURRENT_DATE()
            )';
        } elseif ($status !== '') {
            $conditions[] = 'e.estado = ?';
            $parameters[] = $status;
        }

        if ($search !== '') {
            $conditions[] = '(
                e.titulo LIKE ?
                OR e.objetivo LIKE ?
                OR e.descripcion LIKE ?
            )';

            $term = '%' . $search . '%';

            $parameters[] = $term;
            $parameters[] = $term;
            $parameters[] = $term;
        }

        $where = $conditions !== []
            ? ' WHERE ' . implode(' AND ', $conditions)
            : '';

        /*
         | La primera interrogación de la consulta pertenece
         | al identificador del usuario.
         */
        array_unshift($parameters, $userId);

        return $this->rows(
            'SELECT
                e.*,
                u.nombre_completo AS creador,

                (
                    SELECT COUNT(*)
                    FROM preguntas_encuesta AS p
                    WHERE p.id_encuesta = e.id_encuesta
                      AND p.estado = "activa"
                ) AS total_preguntas,

                (
                    SELECT COUNT(*)
                    FROM respuestas_encuesta AS r
                    WHERE r.id_encuesta = e.id_encuesta
                ) AS total_respuestas,

                EXISTS (
                    SELECT 1
                    FROM respuestas_encuesta AS ru
                    WHERE ru.id_encuesta = e.id_encuesta
                      AND ru.id_usuario = ?
                ) AS respondida_usuario,

                CASE
                    WHEN e.estado = "publicada"
                     AND (
                        e.fecha_inicio IS NULL
                        OR e.fecha_inicio <= CURRENT_DATE()
                     )
                     AND (
                        e.fecha_cierre IS NULL
                        OR e.fecha_cierre >= CURRENT_DATE()
                     )
                    THEN 1
                    ELSE 0
                END AS disponible

             FROM encuestas AS e

             INNER JOIN usuarios AS u
                ON u.id_usuario = e.id_creador'
            . $where .
            ' ORDER BY
                e.fecha_creacion DESC,
                e.id_encuesta DESC',
            $parameters
        );
    }

    /**
     * Obtiene una encuesta junto con la información
     * de su creador.
     */
    public function findDetailed(
        int $surveyId
    ): ?array {
        return $this->row(
            'SELECT
                e.*,
                u.nombre_completo AS creador,

                (
                    SELECT COUNT(*)
                    FROM preguntas_encuesta AS p
                    WHERE p.id_encuesta = e.id_encuesta
                      AND p.estado = "activa"
                ) AS total_preguntas,

                (
                    SELECT COUNT(*)
                    FROM respuestas_encuesta AS r
                    WHERE r.id_encuesta = e.id_encuesta
                ) AS total_respuestas

             FROM encuestas AS e

             INNER JOIN usuarios AS u
                ON u.id_usuario = e.id_creador

             WHERE e.id_encuesta = ?
             LIMIT 1',
            [$surveyId]
        );
    }

    /**
     * Localiza una encuesta publicada
     * que se pueda responder en la fecha actual.
     */
    public function findAvailable(
        int $surveyId
    ): ?array {
        return $this->row(
            'SELECT
                e.*,
                u.nombre_completo AS creador

             FROM encuestas AS e

             INNER JOIN usuarios AS u
                ON u.id_usuario = e.id_creador

             WHERE e.id_encuesta = ?
               AND e.estado = "publicada"

               AND (
                    e.fecha_inicio IS NULL
                    OR e.fecha_inicio <= CURRENT_DATE()
               )

               AND (
                    e.fecha_cierre IS NULL
                    OR e.fecha_cierre >= CURRENT_DATE()
               )

             LIMIT 1',
            [$surveyId]
        );
    }

    /**
     * Cuenta las preguntas activas.
     */
    public function activeQuestionCount(
        int $surveyId
    ): int {
        return (int) $this->scalar(
            'SELECT COUNT(*)
             FROM preguntas_encuesta
             WHERE id_encuesta = ?
               AND estado = "activa"',
            [$surveyId]
        );
    }

    /**
     * Obtiene las preguntas y agrupa sus opciones.
     */
    public function questionsWithOptions(
        int $surveyId,
        bool $onlyActive = false
    ): array {
        $activeCondition = $onlyActive
            ? ' AND p.estado = "activa"'
            : '';

        $records = $this->rows(
            'SELECT
                p.*,
                o.id_opcion,
                o.texto_opcion,
                o.valor AS valor_opcion,
                o.orden AS orden_opcion

             FROM preguntas_encuesta AS p

             LEFT JOIN opciones_pregunta AS o
                ON o.id_pregunta = p.id_pregunta

             WHERE p.id_encuesta = ?'
            . $activeCondition .
            ' ORDER BY
                p.orden,
                p.id_pregunta,
                o.orden,
                o.id_opcion',
            [$surveyId]
        );

        $questions = [];

        foreach ($records as $record) {
            $questionId = (int) $record['id_pregunta'];

            if (!isset($questions[$questionId])) {
                $questions[$questionId] = [
                    'id_pregunta' => $questionId,

                    'id_encuesta' => (int) (
                        $record['id_encuesta']
                    ),

                    'texto_pregunta' => (
                        $record['texto_pregunta']
                    ),

                    'tipo_pregunta' => (
                        $record['tipo_pregunta']
                    ),

                    'obligatoria' => (int) (
                        $record['obligatoria']
                    ),

                    'orden' => (int) $record['orden'],

                    'estado' => $record['estado'],

                    'opciones' => [],
                ];
            }

            if ($record['id_opcion'] !== null) {
                $questions[$questionId]['opciones'][] = [
                    'id_opcion' => (int) (
                        $record['id_opcion']
                    ),

                    'texto_opcion' => (
                        $record['texto_opcion']
                    ),

                    'valor' => (
                        $record['valor_opcion'] !== null
                            ? (int) $record['valor_opcion']
                            : null
                    ),

                    'orden' => (int) (
                        $record['orden_opcion']
                    ),
                ];
            }
        }

        return array_values($questions);
    }
}