<?php
declare(strict_types=1);

final class EncuestaController extends Controller
{
    private const STATUSES = [
        'borrador' => 'Borrador',
        'publicada' => 'Publicada',
        'cerrada' => 'Cerrada',
        'inactiva' => 'Inactiva',
    ];

    private const QUESTION_TYPES = [
        'opcion_multiple' =>
            'Selección de una opción',

        'respuesta_corta' =>
            'Respuesta corta',

        'escala' =>
            'Escala del 1 al 5',
    ];

    /**
     * Muestra las encuestas permitidas para el rol.
     */
    public function index(): void
    {
        $canManage = has_role(
            'admin',
            'coordinador'
        );

        $search = trim(
            (string) ($_GET['buscar'] ?? '')
        );

        $status = $canManage
            ? trim(
                (string) ($_GET['estado'] ?? '')
            )
            : '';

        if (
            $status !== ''
            && !array_key_exists(
                $status,
                self::STATUSES
            )
        ) {
            $status = '';
        }

        $user = auth_user();

        $surveys = (new Encuesta())->search(
            $search,
            $status,
            !$canManage,
            (int) $user['id_usuario']
        );

        $this->view(
            'surveys/index',
            [
                'pageTitle' =>
                    'Encuestas ambientales',

                'surveys' => $surveys,
                'search' => $search,
                'status' => $status,
                'statuses' => self::STATUSES,
                'canManage' => $canManage,
            ],
            'app'
        );
    }

    /**
     * Muestra el formulario de creación.
     */
    public function create(): void
    {
        $this->surveyForm(null);
    }

    /**
     * Registra una encuesta como borrador.
     */
    public function store(): void
    {
        $data = $this->validatedSurvey(false);

        /*
         | Toda encuesta inicia como borrador,
         | porque todavía no tiene preguntas.
         */
        $data['estado'] = 'borrador';

        $data['id_creador'] = (int) (
            auth_user()['id_usuario']
        );

        $surveyId = (new Encuesta())->create(
            $data
        );

        Security::audit(
            'encuesta_creada',
            ['id_encuesta' => $surveyId]
        );

        $this->success(
            'La encuesta se creó como borrador. '
            . 'Ahora agrega sus preguntas.',
            'encuestas/preguntas',
            ['id' => $surveyId]
        );
    }

    /**
     * Muestra el formulario de edición.
     */
    public function edit(): void
    {
        $survey = $this->findSurveyOrFail(
            (int) ($_GET['id'] ?? 0)
        );

        $this->surveyForm($survey);
    }

    /**
     * Actualiza información y estado.
     */
    public function update(): void
    {
        $surveyId = (int) (
            $_POST['id_encuesta'] ?? 0
        );

        $survey = $this->findSurveyOrFail(
            $surveyId
        );

        $data = $this->validatedSurvey(
            true,
            $surveyId
        );

        /*
         | No permito publicar una encuesta vacía.
         */
        if (
            $data['estado'] === 'publicada'
            && (new Encuesta())->activeQuestionCount(
                $surveyId
            ) === 0
        ) {
            $this->failure(
                'Agrega por lo menos una pregunta '
                . 'activa antes de publicar.',
                'encuestas/editar',
                ['id' => $surveyId]
            );
        }

        /*
         | Una encuesta utilizada ya no puede regresar
         | al estado de borrador.
         */
        if (
            (int) $survey['total_respuestas'] > 0
            && $data['estado'] === 'borrador'
        ) {
            $this->failure(
                'Una encuesta con respuestas no puede '
                . 'regresar a borrador.',
                'encuestas/editar',
                ['id' => $surveyId]
            );
        }

        (new Encuesta())->update(
            $surveyId,
            $data
        );

        Security::audit(
            'encuesta_actualizada',
            [
                'id_encuesta' => $surveyId,
                'estado' => $data['estado'],
            ]
        );

        $this->success(
            'La encuesta se actualizó correctamente.',
            'encuestas'
        );
    }

    /**
     * Muestra las preguntas de una encuesta.
     */
    public function questions(): void
    {
        $survey = $this->findSurveyOrFail(
            (int) ($_GET['id'] ?? 0)
        );

        $questions = (new Encuesta())
            ->questionsWithOptions(
                (int) $survey['id_encuesta']
            );

        $this->view(
            'surveys/questions',
            [
                'pageTitle' =>
                    'Preguntas de la encuesta',

                'survey' => $survey,
                'questions' => $questions,

                'questionTypes' =>
                    self::QUESTION_TYPES,

                'locked' => (
                    (int) $survey[
                        'total_respuestas'
                    ] > 0
                ),
            ],
            'app'
        );
    }

    /**
     * Muestra el formulario de una pregunta nueva.
     */
    public function createQuestion(): void
    {
        $survey = $this->findSurveyOrFail(
            (int) ($_GET['id'] ?? 0)
        );

        $this->ensureQuestionsEditable(
            $survey
        );

        $this->questionForm(
            $survey,
            null,
            [],
            (new PreguntaEncuesta())->nextOrder(
                (int) $survey['id_encuesta']
            )
        );
    }

    /**
     * Guarda una pregunta nueva.
     */
    public function storeQuestion(): void
    {
        $surveyId = (int) (
            $_POST['id_encuesta'] ?? 0
        );

        $survey = $this->findSurveyOrFail(
            $surveyId
        );

        $this->ensureQuestionsEditable(
            $survey
        );

        [
            $question,
            $options,
        ] = $this->validatedQuestion(
            false,
            $surveyId
        );

        $question['id_encuesta'] = $surveyId;

        $questionId = (
            new PreguntaEncuesta()
        )->saveWithOptions(
            null,
            $question,
            $options
        );

        Security::audit(
            'pregunta_encuesta_creada',
            [
                'id_encuesta' => $surveyId,
                'id_pregunta' => $questionId,
            ]
        );

        $this->success(
            'La pregunta se agregó correctamente.',
            'encuestas/preguntas',
            ['id' => $surveyId]
        );
    }

    /**
     * Muestra una pregunta para editarla.
     */
    public function editQuestion(): void
    {
        $surveyId = (int) (
            $_GET['encuesta'] ?? 0
        );

        $questionId = (int) (
            $_GET['id'] ?? 0
        );

        $survey = $this->findSurveyOrFail(
            $surveyId
        );

        $this->ensureQuestionsEditable(
            $survey
        );

        $model = new PreguntaEncuesta();

        $question = $model->findForSurvey(
            $questionId,
            $surveyId
        );

        if ($question === null) {
            $this->failure(
                'No se encontró la pregunta solicitada.',
                'encuestas/preguntas',
                ['id' => $surveyId]
            );
        }

        $this->questionForm(
            $survey,
            $question,
            $model->options($questionId),
            (int) $question['orden']
        );
    }

    /**
     * Actualiza una pregunta.
     */
    public function updateQuestion(): void
    {
        $surveyId = (int) (
            $_POST['id_encuesta'] ?? 0
        );

        $questionId = (int) (
            $_POST['id_pregunta'] ?? 0
        );

        $survey = $this->findSurveyOrFail(
            $surveyId
        );

        $this->ensureQuestionsEditable(
            $survey
        );

        $model = new PreguntaEncuesta();

        if (
            $model->findForSurvey(
                $questionId,
                $surveyId
            ) === null
        ) {
            $this->failure(
                'No se encontró la pregunta solicitada.',
                'encuestas/preguntas',
                ['id' => $surveyId]
            );
        }

        [
            $question,
            $options,
        ] = $this->validatedQuestion(
            true,
            $surveyId,
            $questionId
        );

        $model->saveWithOptions(
            $questionId,
            $question,
            $options
        );

        Security::audit(
            'pregunta_encuesta_actualizada',
            [
                'id_encuesta' => $surveyId,
                'id_pregunta' => $questionId,
            ]
        );

        $this->success(
            'La pregunta se actualizó correctamente.',
            'encuestas/preguntas',
            ['id' => $surveyId]
        );
    }

    /**
     * Activa o desactiva una pregunta.
     */
    public function changeQuestionStatus(): void
    {
        $surveyId = (int) (
            $_POST['id_encuesta'] ?? 0
        );

        $questionId = (int) (
            $_POST['id_pregunta'] ?? 0
        );

        $status = (string) (
            $_POST['estado'] ?? ''
        );

        $survey = $this->findSurveyOrFail(
            $surveyId
        );

        $this->ensureQuestionsEditable(
            $survey
        );

        if (
            !in_array(
                $status,
                ['activa', 'inactiva'],
                true
            )
        ) {
            $this->failure(
                'El estado de la pregunta no es válido.',
                'encuestas/preguntas',
                ['id' => $surveyId]
            );
        }

        $model = new PreguntaEncuesta();

        if (
            $model->findForSurvey(
                $questionId,
                $surveyId
            ) === null
        ) {
            $this->failure(
                'No se encontró la pregunta solicitada.',
                'encuestas/preguntas',
                ['id' => $surveyId]
            );
        }

        $model->update(
            $questionId,
            ['estado' => $status]
        );

        Security::audit(
            'pregunta_encuesta_estado',
            [
                'id_pregunta' => $questionId,
                'estado' => $status,
            ]
        );

        $this->success(
            'El estado de la pregunta se actualizó.',
            'encuestas/preguntas',
            ['id' => $surveyId]
        );
    }

    /**
     * Muestra una encuesta para responder.
     */
    public function respond(): void
    {
        $surveyId = (int) (
            $_GET['id'] ?? 0
        );

        $survey = (new Encuesta())
            ->findAvailable($surveyId);

        if ($survey === null) {
            $this->failure(
                'La encuesta no existe, cerró o '
                . 'todavía no está disponible.',
                'encuestas'
            );
        }

        $userId = (int) (
            auth_user()['id_usuario']
        );

        if (
            (new RespuestaEncuesta())
                ->hasAnswered(
                    $surveyId,
                    $userId
                )
        ) {
            $this->failure(
                'Ya participaste en esta encuesta. '
                . 'Gracias por tu respuesta.',
                'encuestas'
            );
        }

        $questions = (new Encuesta())
            ->questionsWithOptions(
                $surveyId,
                true
            );

        if ($questions === []) {
            $this->failure(
                'La encuesta todavía no contiene '
                . 'preguntas activas.',
                'encuestas'
            );
        }

        $this->view(
            'surveys/respond',
            [
                'pageTitle' => $survey['titulo'],
                'survey' => $survey,
                'questions' => $questions,
            ],
            'app'
        );
    }

    /**
     * Valida y registra las respuestas.
     */
    public function submit(): void
    {
        $surveyId = (int) (
            $_POST['id_encuesta'] ?? 0
        );

        $survey = (new Encuesta())
            ->findAvailable($surveyId);

        if ($survey === null) {
            $this->failure(
                'La encuesta ya no se encuentra disponible.',
                'encuestas'
            );
        }

        $questions = (new Encuesta())
            ->questionsWithOptions(
                $surveyId,
                true
            );

        $answers = $this->validatedAnswers(
            $surveyId,
            $questions
        );

        $userId = (int) (
            auth_user()['id_usuario']
        );

        $ip = (string) (
            $_SERVER['REMOTE_ADDR'] ?? ''
        );

        try {
            $responseId = (
                new RespuestaEncuesta()
            )->saveAnswers(
                $surveyId,
                $userId,
                $ip,
                $answers
            );
        } catch (RuntimeException $exception) {
            $this->failure(
                $exception->getMessage(),
                'encuestas'
            );
        }

        Security::audit(
            'encuesta_respondida',
            [
                'id_encuesta' => $surveyId,
                'id_respuesta' => $responseId,
            ]
        );

        $this->success(
            'Tu respuesta se registró correctamente. '
            . 'Gracias por participar.',
            'encuestas'
        );
    }

    /**
     * Muestra los resultados para responsables.
     */
    public function results(): void
    {
        $survey = $this->findSurveyOrFail(
            (int) ($_GET['id'] ?? 0)
        );

        $questions = (new Encuesta())
            ->questionsWithOptions(
                (int) $survey['id_encuesta']
            );

        $results = (
            new RespuestaEncuesta()
        )->resultsForQuestions(
            (int) $survey['id_encuesta'],
            $questions
        );

        $this->view(
            'surveys/results',
            [
                'pageTitle' =>
                    'Resultados de la encuesta',

                'survey' => $survey,
                'questions' => $questions,
                'results' => $results,

                'questionTypes' =>
                    self::QUESTION_TYPES,
            ],
            'app'
        );
    }

    /**
     * Renderiza el formulario general.
     */
    private function surveyForm(
        ?array $survey
    ): void {
        $editing = $survey !== null;

        $this->view(
            'surveys/form',
            [
                'pageTitle' => $editing
                    ? 'Editar encuesta'
                    : 'Crear encuesta',

                'editing' => $editing,
                'survey' => $survey ?? [],
                'statuses' => self::STATUSES,
            ],
            'app'
        );
    }

    /**
     * Renderiza el formulario de una pregunta.
     */
    private function questionForm(
        array $survey,
        ?array $question,
        array $options,
        int $defaultOrder
    ): void {
        $editing = $question !== null;

        $this->view(
            'surveys/question_form',
            [
                'pageTitle' => $editing
                    ? 'Editar pregunta'
                    : 'Agregar pregunta',

                'editing' => $editing,
                'survey' => $survey,
                'question' => $question ?? [],

                'optionsText' => implode(
                    PHP_EOL,
                    array_column(
                        $options,
                        'texto_opcion'
                    )
                ),

                'defaultOrder' => $defaultOrder,

                'questionTypes' =>
                    self::QUESTION_TYPES,
            ],
            'app'
        );
    }

    /**
     * Busca una encuesta o redirige.
     */
    private function findSurveyOrFail(
        int $surveyId
    ): array {
        $survey = (new Encuesta())
            ->findDetailed($surveyId);

        if ($survey === null) {
            $this->failure(
                'No se encontró la encuesta solicitada.',
                'encuestas'
            );
        }

        return $survey;
    }

    /**
     * Impide cambiar preguntas después
     * de recibir la primera respuesta.
     */
    private function ensureQuestionsEditable(
        array $survey
    ): void {
        if (
            (int) $survey['total_respuestas'] > 0
        ) {
            $this->failure(
                'Las preguntas quedaron bloqueadas '
                . 'porque la encuesta ya tiene respuestas.',
                'encuestas/preguntas',
                [
                    'id' => (int) (
                        $survey['id_encuesta']
                    ),
                ]
            );
        }
    }

    /**
     * Valida la información general.
     */
    private function validatedSurvey(
        bool $editing,
        int $surveyId = 0
    ): array {
        $returnRoute = $editing
            ? 'encuestas/editar'
            : 'encuestas/crear';

        $parameters = $editing
            ? ['id' => $surveyId]
            : [];

        $title = trim(
            (string) ($_POST['titulo'] ?? '')
        );

        $objective = trim(
            (string) ($_POST['objetivo'] ?? '')
        );

        $description = trim(
            (string) ($_POST['descripcion'] ?? '')
        );

        $start = trim(
            (string) ($_POST['fecha_inicio'] ?? '')
        );

        $close = trim(
            (string) ($_POST['fecha_cierre'] ?? '')
        );

        $status = $editing
            ? (string) (
                $_POST['estado'] ?? 'borrador'
            )
            : 'borrador';

        if (
            !$this->validLength(
                $title,
                5,
                150
            )
        ) {
            $this->failure(
                'El título debe contener entre '
                . '5 y 150 caracteres.',
                $returnRoute,
                $parameters
            );
        }

        if (
            $objective !== ''
            && $this->length($objective) > 255
        ) {
            $this->failure(
                'El objetivo no puede superar '
                . '255 caracteres.',
                $returnRoute,
                $parameters
            );
        }

        if (
            $description !== ''
            && $this->length($description) > 3000
        ) {
            $this->failure(
                'La descripción no puede superar '
                . '3000 caracteres.',
                $returnRoute,
                $parameters
            );
        }

        if (
            $start !== ''
            && !$this->validDate($start)
        ) {
            $this->failure(
                'La fecha de inicio no es válida.',
                $returnRoute,
                $parameters
            );
        }

        if (
            $close !== ''
            && !$this->validDate($close)
        ) {
            $this->failure(
                'La fecha de cierre no es válida.',
                $returnRoute,
                $parameters
            );
        }

        if (
            $start !== ''
            && $close !== ''
            && $start > $close
        ) {
            $this->failure(
                'La fecha de inicio debe ser anterior '
                . 'o igual al cierre.',
                $returnRoute,
                $parameters
            );
        }

        if (
            !array_key_exists(
                $status,
                self::STATUSES
            )
        ) {
            $this->failure(
                'Selecciona un estado válido.',
                $returnRoute,
                $parameters
            );
        }

        return [
            'titulo' => $title,

            'objetivo' => $objective !== ''
                ? $objective
                : null,

            'descripcion' => $description !== ''
                ? $description
                : null,

            'fecha_inicio' => $start !== ''
                ? $start
                : null,

            'fecha_cierre' => $close !== ''
                ? $close
                : null,

            'estado' => $status,
        ];
    }

    /**
     * Valida pregunta, tipo, orden y opciones.
     */
    private function validatedQuestion(
        bool $editing,
        int $surveyId,
        int $questionId = 0
    ): array {
        $returnRoute = $editing
            ? 'encuestas/preguntas/editar'
            : 'encuestas/preguntas/crear';

        $parameters = $editing
            ? [
                'encuesta' => $surveyId,
                'id' => $questionId,
            ]
            : ['id' => $surveyId];

        $text = trim(
            (string) (
                $_POST['texto_pregunta'] ?? ''
            )
        );

        $type = (string) (
            $_POST['tipo_pregunta'] ?? ''
        );

        $order = (int) (
            $_POST['orden'] ?? 1
        );

        $required = (
            (string) (
                $_POST['obligatoria'] ?? '0'
            )
        ) === '1';

        $status = (string) (
            $_POST['estado'] ?? 'activa'
        );

        $optionsText = trim(
            (string) ($_POST['opciones'] ?? '')
        );

        if (
            !$this->validLength(
                $text,
                5,
                255
            )
        ) {
            $this->failure(
                'La pregunta debe contener entre '
                . '5 y 255 caracteres.',
                $returnRoute,
                $parameters
            );
        }

        if (
            !array_key_exists(
                $type,
                self::QUESTION_TYPES
            )
        ) {
            $this->failure(
                'Selecciona un tipo de pregunta válido.',
                $returnRoute,
                $parameters
            );
        }

        if ($order < 1 || $order > 999) {
            $this->failure(
                'El orden debe encontrarse entre '
                . '1 y 999.',
                $returnRoute,
                $parameters
            );
        }

        if (
            !in_array(
                $status,
                ['activa', 'inactiva'],
                true
            )
        ) {
            $this->failure(
                'Selecciona un estado válido.',
                $returnRoute,
                $parameters
            );
        }

        $options = [];

        if ($type === 'opcion_multiple') {
            $lines = preg_split(
                '/\R/u',
                $optionsText
            ) ?: [];

            foreach ($lines as $line) {
                $option = trim($line);

                if ($option === '') {
                    continue;
                }

                if (
                    !$this->validLength(
                        $option,
                        1,
                        150
                    )
                ) {
                    $this->failure(
                        'Cada opción puede contener '
                        . 'hasta 150 caracteres.',
                        $returnRoute,
                        $parameters
                    );
                }

                if (
                    !in_array(
                        $option,
                        $options,
                        true
                    )
                ) {
                    $options[] = $option;
                }
            }

            if (
                count($options) < 2
                || count($options) > 10
            ) {
                $this->failure(
                    'Las preguntas de selección necesitan '
                    . 'entre 2 y 10 opciones.',
                    $returnRoute,
                    $parameters
                );
            }
        }

        return [
            [
                'texto_pregunta' => $text,
                'tipo_pregunta' => $type,
                'obligatoria' => $required ? 1 : 0,
                'orden' => $order,
                'estado' => $status,
            ],
            $options,
        ];
    }

    /**
     * Valida las respuestas enviadas.
     */
    private function validatedAnswers(
        int $surveyId,
        array $questions
    ): array {
        $submitted = (
            $_POST['respuestas'] ?? []
        );

        if (!is_array($submitted)) {
            $this->failure(
                'Las respuestas enviadas no son válidas.',
                'encuestas/responder',
                ['id' => $surveyId]
            );
        }

        $answers = [];

        foreach ($questions as $question) {
            $questionId = (int) (
                $question['id_pregunta']
            );

            $type = (string) (
                $question['tipo_pregunta']
            );

            $required = (
                (int) $question['obligatoria'] === 1
            );

            $value = $submitted[
                $questionId
            ] ?? null;

            $detail = [
                'id_pregunta' => $questionId,
                'id_opcion' => null,
                'respuesta_texto' => null,
                'respuesta_valor' => null,
            ];

            if ($type === 'respuesta_corta') {
                if (is_array($value)) {
                    $value = '';
                }

                $text = trim(
                    (string) ($value ?? '')
                );

                if ($text === '') {
                    if ($required) {
                        $this->missingAnswer(
                            $surveyId
                        );
                    }

                    continue;
                }

                if ($this->length($text) > 500) {
                    $this->failure(
                        'Una respuesta corta no puede '
                        . 'superar 500 caracteres.',
                        'encuestas/responder',
                        ['id' => $surveyId]
                    );
                }

                $detail['respuesta_texto'] = $text;
                $answers[] = $detail;

                continue;
            }

            $numericValue = is_scalar($value)
                ? (int) $value
                : 0;

            if ($numericValue <= 0) {
                if ($required) {
                    $this->missingAnswer(
                        $surveyId
                    );
                }

                continue;
            }

            if ($type === 'escala') {
                if (
                    $numericValue < 1
                    || $numericValue > 5
                ) {
                    $this->failure(
                        'Selecciona un valor entre 1 y 5.',
                        'encuestas/responder',
                        ['id' => $surveyId]
                    );
                }

                $detail['respuesta_valor'] =
                    $numericValue;

                $answers[] = $detail;

                continue;
            }

            $validOptionIds = array_map(
                static fn(array $option): int =>
                    (int) $option['id_opcion'],
                $question['opciones']
            );

            if (
                !in_array(
                    $numericValue,
                    $validOptionIds,
                    true
                )
            ) {
                $this->failure(
                    'Una de las opciones seleccionadas '
                    . 'no pertenece a la encuesta.',
                    'encuestas/responder',
                    ['id' => $surveyId]
                );
            }

            $detail['id_opcion'] =
                $numericValue;

            $answers[] = $detail;
        }

        return $answers;
    }

    private function missingAnswer(
        int $surveyId
    ): never {
        $this->failure(
            'Responde todas las preguntas '
            . 'marcadas como obligatorias.',
            'encuestas/responder',
            ['id' => $surveyId]
        );
    }

    private function validDate(
        string $date
    ): bool {
        $parsed = DateTimeImmutable::createFromFormat(
            '!Y-m-d',
            $date
        );

        return (
            $parsed instanceof DateTimeImmutable
            && $parsed->format('Y-m-d') === $date
        );
    }

    private function validLength(
        string $value,
        int $minimum,
        int $maximum
    ): bool {
        $length = $this->length($value);

        return (
            $length >= $minimum
            && $length <= $maximum
        );
    }

    private function length(
        string $value
    ): int {
        return function_exists('mb_strlen')
            ? mb_strlen($value)
            : strlen($value);
    }
}