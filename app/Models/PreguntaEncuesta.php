<?php
declare(strict_types=1);

/**
 * Administra preguntas y opciones de una encuesta.
 */
final class PreguntaEncuesta extends Model
{
    protected string $table = 'preguntas_encuesta';

    protected string $primaryKey = 'id_pregunta';

    protected array $fillable = [
        'id_encuesta',
        'texto_pregunta',
        'tipo_pregunta',
        'obligatoria',
        'orden',
        'estado',
    ];

    /**
     * Busca una pregunta y comprueba que pertenezca
     * a la encuesta indicada.
     */
    public function findForSurvey(
        int $questionId,
        int $surveyId
    ): ?array {
        return $this->row(
            'SELECT *
             FROM preguntas_encuesta
             WHERE id_pregunta = ?
               AND id_encuesta = ?
             LIMIT 1',
            [
                $questionId,
                $surveyId,
            ]
        );
    }

    /**
     * Obtiene las opciones actuales de una pregunta.
     */
    public function options(
        int $questionId
    ): array {
        return $this->rows(
            'SELECT *
             FROM opciones_pregunta
             WHERE id_pregunta = ?
             ORDER BY orden, id_opcion',
            [$questionId]
        );
    }

    /**
     * Obtiene el siguiente número de orden.
     */
    public function nextOrder(
        int $surveyId
    ): int {
        return (int) $this->scalar(
            'SELECT COALESCE(MAX(orden), 0) + 1
             FROM preguntas_encuesta
             WHERE id_encuesta = ?',
            [$surveyId]
        );
    }

    /**
     * Guarda una pregunta y sus opciones
     * dentro de una transacción.
     */
    public function saveWithOptions(
        ?int $questionId,
        array $question,
        array $options
    ): int {
        $database = Database::connection();

        $database->beginTransaction();

        try {
            if ($questionId === null) {
                $questionId = $this->create(
                    $question
                );
            } else {
                $this->update(
                    $questionId,
                    $question
                );

                /*
                 | Reemplazo las opciones anteriores.
                 | Esto solamente se permite antes de recibir respuestas.
                 */
                $this->query(
                    'DELETE FROM opciones_pregunta
                     WHERE id_pregunta = ?',
                    [$questionId]
                );
            }

            if (
                $question['tipo_pregunta']
                === 'opcion_multiple'
            ) {
                $statement = $database->prepare(
                    'INSERT INTO opciones_pregunta (
                        id_pregunta,
                        texto_opcion,
                        valor,
                        orden
                     ) VALUES (?, ?, ?, ?)'
                );

                foreach ($options as $index => $option) {
                    $statement->execute([
                        $questionId,
                        $option,
                        $index + 1,
                        $index + 1,
                    ]);
                }
            }

            $database->commit();

            return $questionId;
        } catch (Throwable $exception) {
            if ($database->inTransaction()) {
                $database->rollBack();
            }

            throw $exception;
        }
    }
}