<?php
declare(strict_types=1);

/**
 * Administra los encabezados y detalles
 * de las respuestas de una encuesta.
 */
final class RespuestaEncuesta extends Model
{
    protected string $table = 'respuestas_encuesta';

    protected string $primaryKey = 'id_respuesta';

    protected array $fillable = [
        'id_encuesta',
        'id_usuario',
        'fecha_respuesta',
        'ip_respuesta',
    ];

    /**
     * Comprueba si el usuario ya participó.
     */
    public function hasAnswered(
        int $surveyId,
        int $userId
    ): bool {
        return (int) $this->scalar(
            'SELECT COUNT(*)
             FROM respuestas_encuesta
             WHERE id_encuesta = ?
               AND id_usuario = ?',
            [
                $surveyId,
                $userId,
            ]
        ) > 0;
    }

    /**
     * Registra el encabezado y los detalles
     * dentro de una sola transacción.
     */
    public function saveAnswers(
        int $surveyId,
        int $userId,
        string $ip,
        array $answers
    ): int {
        $database = Database::connection();

        $database->beginTransaction();

        try {
            /*
             | Bloqueo brevemente la encuesta mientras se registra
             | la participación.
             */
            $survey = $this->row(
                'SELECT id_encuesta
                 FROM encuestas
                 WHERE id_encuesta = ?
                   AND estado = "publicada"

                   AND (
                        fecha_inicio IS NULL
                        OR fecha_inicio <= CURRENT_DATE()
                   )

                   AND (
                        fecha_cierre IS NULL
                        OR fecha_cierre >= CURRENT_DATE()
                   )

                 FOR UPDATE',
                [$surveyId]
            );

            if ($survey === null) {
                throw new RuntimeException(
                    'La encuesta ya no se encuentra disponible.'
                );
            }

            if (
                $this->hasAnswered(
                    $surveyId,
                    $userId
                )
            ) {
                throw new RuntimeException(
                    'Ya registraste una respuesta en esta encuesta.'
                );
            }

            $responseId = $this->create([
                'id_encuesta' => $surveyId,
                'id_usuario' => $userId,

                'fecha_respuesta' => date(
                    'Y-m-d H:i:s'
                ),

                'ip_respuesta' => substr(
                    $ip,
                    0,
                    45
                ),
            ]);

            $statement = $database->prepare(
                'INSERT INTO detalle_respuestas_encuesta (
                    id_respuesta,
                    id_pregunta,
                    id_opcion,
                    respuesta_texto,
                    respuesta_valor
                 ) VALUES (?, ?, ?, ?, ?)'
            );

            foreach ($answers as $answer) {
                $statement->execute([
                    $responseId,
                    $answer['id_pregunta'],
                    $answer['id_opcion'],
                    $answer['respuesta_texto'],
                    $answer['respuesta_valor'],
                ]);
            }

            $database->commit();

            return $responseId;
        } catch (Throwable $exception) {
            if ($database->inTransaction()) {
                $database->rollBack();
            }

            throw $exception;
        }
    }

    /**
     * Construye los resultados de cada pregunta.
     */
    public function resultsForQuestions(
        int $surveyId,
        array $questions
    ): array {
        $results = [];

        foreach ($questions as $question) {
            $questionId = (int) (
                $question['id_pregunta']
            );

            $type = (string) (
                $question['tipo_pregunta']
            );

            if ($type === 'opcion_multiple') {
                $options = $this->rows(
                    'SELECT
                        o.id_opcion,
                        o.texto_opcion,
                        o.orden,
                        COUNT(r.id_respuesta) AS total

                     FROM opciones_pregunta AS o

                     LEFT JOIN detalle_respuestas_encuesta AS d
                        ON d.id_opcion = o.id_opcion

                     LEFT JOIN respuestas_encuesta AS r
                        ON r.id_respuesta = d.id_respuesta
                       AND r.id_encuesta = ?

                     WHERE o.id_pregunta = ?

                     GROUP BY
                        o.id_opcion,
                        o.texto_opcion,
                        o.orden

                     ORDER BY
                        o.orden,
                        o.id_opcion',
                    [
                        $surveyId,
                        $questionId,
                    ]
                );

                $answered = array_sum(
                    array_map(
                        static fn(array $option): int =>
                            (int) $option['total'],
                        $options
                    )
                );

                $results[$questionId] = [
                    'tipo' => $type,
                    'total_contestado' => $answered,
                    'opciones' => $options,
                ];

                continue;
            }

            if ($type === 'escala') {
                $summary = $this->row(
                    'SELECT
                        COUNT(*) AS total,
                        AVG(d.respuesta_valor) AS promedio

                     FROM detalle_respuestas_encuesta AS d

                     INNER JOIN respuestas_encuesta AS r
                        ON r.id_respuesta = d.id_respuesta

                     WHERE r.id_encuesta = ?
                       AND d.id_pregunta = ?
                       AND d.respuesta_valor IS NOT NULL',
                    [
                        $surveyId,
                        $questionId,
                    ]
                ) ?? [
                    'total' => 0,
                    'promedio' => null,
                ];

                $distributionRows = $this->rows(
                    'SELECT
                        d.respuesta_valor AS valor,
                        COUNT(*) AS total

                     FROM detalle_respuestas_encuesta AS d

                     INNER JOIN respuestas_encuesta AS r
                        ON r.id_respuesta = d.id_respuesta

                     WHERE r.id_encuesta = ?
                       AND d.id_pregunta = ?
                       AND d.respuesta_valor BETWEEN 1 AND 5

                     GROUP BY d.respuesta_valor',
                    [
                        $surveyId,
                        $questionId,
                    ]
                );

                $distribution = array_fill(
                    1,
                    5,
                    0
                );

                foreach ($distributionRows as $row) {
                    $distribution[
                        (int) $row['valor']
                    ] = (int) $row['total'];
                }

                $results[$questionId] = [
                    'tipo' => $type,

                    'total_contestado' => (int) (
                        $summary['total']
                    ),

                    'promedio' => (
                        $summary['promedio'] !== null
                            ? (float) $summary['promedio']
                            : null
                    ),

                    'distribucion' => $distribution,
                ];

                continue;
            }

            /*
             | Recupero como máximo 100 respuestas abiertas
             | para no cargar una página excesivamente grande.
             */
            $texts = $this->rows(
                'SELECT
                    d.respuesta_texto,
                    r.fecha_respuesta

                 FROM detalle_respuestas_encuesta AS d

                 INNER JOIN respuestas_encuesta AS r
                    ON r.id_respuesta = d.id_respuesta

                 WHERE r.id_encuesta = ?
                   AND d.id_pregunta = ?
                   AND d.respuesta_texto IS NOT NULL
                   AND d.respuesta_texto <> ""

                 ORDER BY r.fecha_respuesta DESC
                 LIMIT 100',
                [
                    $surveyId,
                    $questionId,
                ]
            );

            $results[$questionId] = [
                'tipo' => $type,
                'total_contestado' => count($texts),
                'textos' => $texts,
            ];
        }

        return $results;
    }
}