<?php
declare(strict_types=1);

final class JuegoController extends Controller
{
    public function index(): void
    {
        $definitions = $this->definitions();
        $activityModel = new ActividadGamificacion();

        $records = $activityModel->byNames(
            array_column($definitions, 'database_name')
        );

        $recordsByName = [];

        foreach ($records as $record) {
            $recordsByName[$record['nombre']] = $record;
        }

        $userId = (int) auth_user()['id_usuario'];
        $resultModel = new ResultadoGamificacion();
        $bestResults = $resultModel->bestByActivity($userId);
        $games = [];

        foreach ($definitions as $slug => $definition) {
            $record = $recordsByName[
                $definition['database_name']
            ] ?? null;

            $activityId = (int) (
                $record['id_actividad'] ?? 0
            );

            $games[] = array_merge(
                $definition,
                [
                'slug' => $slug,
                'activity' => $record,
                'available' =>
                    $definition['implemented']
                    && $record !== null
                    && $record['estado'] === 'activa',
                'best_score' => (int) (
                    $bestResults[$activityId]['mejor_puntaje']
                    ?? 0
                ),
                'plays' => (int) (
                    $bestResults[$activityId]['total_partidas']
                    ?? 0
                ),
                ]
            );
        }

    $this->view(
    'games/index',
    [
        'pageTitle' => 'Juegos y actividades',

        'games' => $games,

        'summary' => $resultModel->summaryForUser(
            $userId
        ),

        'canManage' => in_array(
            auth_role(),
            [
                'admin',
                'coordinador',
            ],
            true
        ),

        /*
         | Aquí indico al layout que debe cargar
         | la hoja de estilos de los juegos.
         */
        'useGames' => true,
    ],
    'app'
);
    }

    public function classification(): void
    {
        $game = $this->gameOrFail('clasifica');
        $activity = $this->activityOrFail($game);

        /*
         | La categoría correcta se queda en el servidor.
         | La vista recibe únicamente los datos visuales.
         */
        $items = array_map(
            static function (array $item): array {
                unset($item['category']);
                return $item;
            },
            $game['items']
        );

        $bestScore = (new ResultadoGamificacion())
            ->bestForGame(
                (int) auth_user()['id_usuario'],
                (int) $activity['id_actividad']
            );

        $this->view(
            'games/classification',
            [
                'pageTitle' => $game['title'],
                'game' => $game,
                'items' => $items,
                'categories' => $game['categories'],
                'bestScore' => $bestScore,
                'useGames' => true,
            ],
            'app'
        );
    }

    public function trivia(): void
    {
        $game = $this->gameOrFail('trivia');
        $activity = $this->activityOrFail($game);

        $questions = array_map(
            static fn(array $question): array => [
                'id' => $question['id'],
                'question' => $question['question'],
                'options' => $question['options'],
            ],
            $game['questions']
        );

        $bestScore = (new ResultadoGamificacion())
            ->bestForGame(
                (int) auth_user()['id_usuario'],
                (int) $activity['id_actividad']
            );

        $this->view(
            'games/trivia',
            [
                'pageTitle' => $game['title'],
                'game' => $game,
                'questions' => $questions,
                'bestScore' => $bestScore,
                'useGames' => true,
            ],
            'app'
        );
    }

    public function memory(): void
    {
        $game = $this->gameOrFail('memorama');
        $activity = $this->activityOrFail($game);
        $cards = [];

        foreach ($game['pairs'] as $pair) {
            /*
             | La pareja real se convierte en una ficha opaca.
             | El navegador la usa para la animación, pero el servidor
             | vuelve a validar todas las jugadas al finalizar.
             */
            $matchToken = hash_hmac(
                'sha256',
                (string) $pair['id'],
                csrf_token()
            );

            foreach (['first', 'second'] as $side) {
                $cards[] = [
                    'id' => $pair[$side]['id'],
                    'label' => $pair[$side]['label'],
                    'emoji' => $pair[$side]['emoji'],
                    'match_token' => $matchToken,
                ];
            }
        }

        shuffle($cards);

        $bestScore = (new ResultadoGamificacion())
            ->bestForGame(
                (int) auth_user()['id_usuario'],
                (int) $activity['id_actividad']
            );

        $this->view(
            'games/memory',
            [
                'pageTitle' => $game['title'],
                'game' => $game,
                'cards' => $cards,
                'bestScore' => $bestScore,
                'useGames' => true,
            ],
            'app'
        );
    }

    public function cleanPark(): void
    {
        $game = $this->gameOrFail('limpia-parque');
        $activity = $this->activityOrFail($game);

        $objects = array_map(
            static fn(array $object): array => [
                'id' => $object['id'],
                'label' => $object['label'],
                'emoji' => $object['emoji'],
                'type' => $object['type'],
                'x' => $object['x'],
                'y' => $object['y'],
            ],
            $game['objects']
        );

        $wasteCount = count(
            array_filter(
                $game['objects'],
                static fn(array $object): bool =>
                    $object['type'] === 'waste'
            )
        );

        $bestScore = (new ResultadoGamificacion())
            ->bestForGame(
                (int) auth_user()['id_usuario'],
                (int) $activity['id_actividad']
            );

        $this->view(
            'games/clean_park',
            [
                'pageTitle' => $game['title'],
                'game' => $game,
                'objects' => $objects,
                'wasteCount' => $wasteCount,
                'bestScore' => $bestScore,
                'useGames' => true,
            ],
            'app'
        );
    }

    public function recyclingRoute(): void
    {
        $game = $this->gameOrFail('ruta-reciclaje');
        $activity = $this->activityOrFail($game);
        $routes = [];

        foreach ($game['routes'] as $route) {
            $stages = $route['stages'];
            shuffle($stages);

            $routes[] = [
                'id' => $route['id'],
                'title' => $route['title'],
                'icon' => $route['icon'],
                'color' => $route['color'],
                'description' => $route['description'],
                'stages' => $stages,
            ];
        }

        $bestScore = (new ResultadoGamificacion())
            ->bestForGame(
                (int) auth_user()['id_usuario'],
                (int) $activity['id_actividad']
            );

        $this->view(
            'games/recycling_route',
            [
                'pageTitle' => $game['title'],
                'game' => $game,
                'routes' => $routes,
                'bestScore' => $bestScore,
                'useGames' => true,
            ],
            'app'
        );
    }

    public function decisions(): void
    {
        $game = $this->gameOrFail('decisiones');
        $activity = $this->activityOrFail($game);

        $scenarios = array_map(
            static fn(array $scenario): array => [
                'id' => $scenario['id'],
                'room' => $scenario['room'],
                'icon' => $scenario['icon'],
                'question' => $scenario['question'],
                'options' => array_map(
                    static fn(array $option): array => [
                        'id' => $option['id'],
                        'label' => $option['label'],
                    ],
                    $scenario['options']
                ),
            ],
            $game['scenarios']
        );

        $bestScore = (new ResultadoGamificacion())
            ->bestForGame(
                (int) auth_user()['id_usuario'],
                (int) $activity['id_actividad']
            );

        $this->view(
            'games/decisions',
            [
                'pageTitle' => $game['title'],
                'game' => $game,
                'scenarios' => $scenarios,
                'bestScore' => $bestScore,
                'useGames' => true,
            ],
            'app'
        );
    }

    /**
     * Recibe las respuestas, calcula el resultado en el servidor
     * y devuelve JSON al juego.
     */
    public function storeResult(): void
    {
        $slug = trim((string) ($_POST['juego'] ?? ''));

        $allowedGames = [
            'clasifica',
            'trivia',
            'memorama',
            'limpia-parque',
            'ruta-reciclaje',
            'decisiones',
        ];

        if (!in_array($slug, $allowedGames, true)) {
            $this->jsonResponse(
                ['success' => false, 'message' => 'El juego no es válido.'],
                422
            );
        }

        $game = $this->gameOrFailJson($slug);
        $activity = (new ActividadGamificacion())
            ->findActiveByName($game['database_name']);

        if ($activity === null) {
            $this->jsonResponse(
                [
                    'success' => false,
                    'message' => 'El juego no se encuentra disponible.',
                ],
                404
            );
        }

        $answers = json_decode(
            (string) ($_POST['respuestas'] ?? ''),
            true
        );

        if (!is_array($answers)) {
            $this->jsonResponse(
                [
                    'success' => false,
                    'message' => 'No se recibieron respuestas válidas.',
                ],
                422
            );
        }

        $evaluation = match ($slug) {
            'clasifica' =>
                $this->evaluateClassification($game, $answers),
            'trivia' =>
                $this->evaluateTrivia($game, $answers),
            'memorama' =>
                $this->evaluateMemory($game, $answers),
            'limpia-parque' =>
                $this->evaluateCleanPark($game, $answers),
            'ruta-reciclaje' =>
                $this->evaluateRecyclingRoute($game, $answers),
            'decisiones' =>
                $this->evaluateDecisions($game, $answers),
        };

        if (!$evaluation['complete']) {
            $this->jsonResponse(
                [
                    'success' => false,
                    'message' => 'Completa todas las actividades antes de finalizar.',
                ],
                422
            );
        }

        $total = (int) $evaluation['total'];
        $correct = (int) $evaluation['correct'];
        $errors = (int) (
            $evaluation['errors'] ?? ($total - $correct)
        );

        $percentage = isset($evaluation['percentage'])
            ? (float) $evaluation['percentage']
            : round(($correct / max(1, $total)) * 100, 2);

        $score = isset($evaluation['score'])
            ? (int) $evaluation['score']
            : (int) round($percentage);

        $score = max(0, min(100, $score));
        $percentage = max(0, min(100, $percentage));

        try {
            $resultId = (new ResultadoGamificacion())->create([
                'id_usuario' => (int) auth_user()['id_usuario'],
                'id_actividad' => (int) $activity['id_actividad'],
                'puntaje_obtenido' => $score,
                'aciertos' => $correct,
                'errores' => $errors,
                'porcentaje_acierto' => $percentage,
            ]);
        } catch (Throwable) {
            $this->jsonResponse(
                [
                    'success' => false,
                    'message' => 'No fue posible guardar el resultado.',
                ],
                500
            );
        }

        Security::audit(
            'juego_completado',
            [
                'id_resultado' => $resultId,
                'id_actividad' => $activity['id_actividad'],
                'puntaje' => $score,
            ]
        );

        $this->jsonResponse([
            'success' => true,
            'score' => $score,
            'correct' => $correct,
            'errors' => $errors,
            'percentage' => $percentage,
            'message' => $this->resultMessage($percentage),
            'feedback' => $evaluation['feedback'],
        ]);
    }

    private function evaluateClassification(
        array $game,
        array $answers
    ): array {
        $correct = 0;
        $feedback = [];
        $complete = true;

        foreach ($game['items'] as $item) {
            $answer = (string) ($answers[$item['id']] ?? '');

            if ($answer === '') {
                $complete = false;
            }

            $isCorrect = hash_equals(
                (string) $item['category'],
                $answer
            );

            if ($isCorrect) {
                $correct++;
            }

            $feedback[] = [
                'id' => $item['id'],
                'name' => $item['name'],
                'correct' => $isCorrect,
                'expected' => $game['categories'][
                    $item['category']
                ]['label'],
            ];
        }

        return [
            'complete' => $complete,
            'correct' => $correct,
            'total' => count($game['items']),
            'feedback' => $feedback,
        ];
    }

    private function evaluateTrivia(
        array $game,
        array $answers
    ): array {
        $correct = 0;
        $feedback = [];
        $complete = true;

        foreach ($game['questions'] as $question) {
            $answer = (string) ($answers[$question['id']] ?? '');

            if (!array_key_exists($answer, $question['options'])) {
                $complete = false;
            }

            $isCorrect = $answer !== ''
                && hash_equals((string) $question['correct'], $answer);

            if ($isCorrect) {
                $correct++;
            }

            $feedback[] = [
                'id' => $question['id'],
                'correct' => $isCorrect,
                'correct_answer' => $question['options'][
                    $question['correct']
                ],
                'explanation' => $question['explanation'],
            ];
        }

        return [
            'complete' => $complete,
            'correct' => $correct,
            'total' => count($game['questions']),
            'feedback' => $feedback,
        ];
    }

    private function evaluateMemory(
        array $game,
        array $moves
    ): array {
        if (count($moves) > 100) {
            return [
                'complete' => false,
                'correct' => 0,
                'errors' => 0,
                'total' => count($game['pairs']),
                'feedback' => [],
            ];
        }

        $cardPairs = [];
        $pairExplanations = [];

        foreach ($game['pairs'] as $pair) {
            $pairId = (string) $pair['id'];
            $cardPairs[(string) $pair['first']['id']] = $pairId;
            $cardPairs[(string) $pair['second']['id']] = $pairId;
            $pairExplanations[$pairId] = [
                'title' => $pair['first']['label']
                    . ' + '
                    . $pair['second']['label'],
                'explanation' => $pair['explanation'],
            ];
        }

        $matchedCards = [];
        $matchedPairs = [];
        $errors = 0;
        $valid = true;

        foreach ($moves as $move) {
            if (!is_array($move) || count($move) !== 2) {
                $valid = false;
                break;
            }

            $firstId = (string) ($move[0] ?? '');
            $secondId = (string) ($move[1] ?? '');

            if (
                $firstId === ''
                || $secondId === ''
                || $firstId === $secondId
                || !isset($cardPairs[$firstId], $cardPairs[$secondId])
                || isset($matchedCards[$firstId])
                || isset($matchedCards[$secondId])
            ) {
                $valid = false;
                break;
            }

            if (hash_equals($cardPairs[$firstId], $cardPairs[$secondId])) {
                $matchedCards[$firstId] = true;
                $matchedCards[$secondId] = true;
                $matchedPairs[$cardPairs[$firstId]] = true;
            } else {
                $errors++;
            }
        }

        $pairCount = count($game['pairs']);
        $complete = $valid && count($matchedPairs) === $pairCount;
        $score = max(20, 100 - ($errors * 8));
        $feedback = [];

        foreach (array_keys($matchedPairs) as $pairId) {
            $feedback[] = $pairExplanations[$pairId];
        }

        return [
            'complete' => $complete,
            'correct' => count($matchedPairs),
            'errors' => $errors,
            'total' => $pairCount,
            'score' => $score,
            'percentage' => $score,
            'feedback' => $feedback,
        ];
    }

    private function evaluateCleanPark(
        array $game,
        array $clicks
    ): array {
        if (count($clicks) > 100) {
            return [
                'complete' => false,
                'correct' => 0,
                'errors' => 0,
                'total' => 0,
                'feedback' => [],
            ];
        }

        $objectsById = [];
        $requiredWaste = [];

        foreach ($game['objects'] as $object) {
            $objectId = (string) $object['id'];
            $objectsById[$objectId] = $object;

            if ($object['type'] === 'waste') {
                $requiredWaste[$objectId] = true;
            }
        }

        $collectedWaste = [];
        $errors = 0;
        $tips = [];
        $valid = true;

        foreach ($clicks as $clickedId) {
            if (!is_string($clickedId) || !isset($objectsById[$clickedId])) {
                $valid = false;
                break;
            }

            $object = $objectsById[$clickedId];

            if ($object['type'] === 'waste') {
                $collectedWaste[$clickedId] = true;
                continue;
            }

            $errors++;
            $tips[$clickedId] = [
                'title' => $object['label'],
                'explanation' => $object['tip'],
            ];
        }

        $complete = $valid
            && count($collectedWaste) === count($requiredWaste);
        $score = max(20, 100 - ($errors * 10));

        if ($tips === []) {
            $tips[] = [
                'title' => '¡Parque protegido!',
                'explanation' =>
                    'Recogiste todos los residuos sin alterar los elementos naturales.',
            ];
        }

        return [
            'complete' => $complete,
            'correct' => count($collectedWaste),
            'errors' => $errors,
            'total' => count($requiredWaste),
            'score' => $score,
            'percentage' => $score,
            'feedback' => array_values($tips),
        ];
    }

    private function evaluateRecyclingRoute(
        array $game,
        array $answers
    ): array {
        $correct = 0;
        $total = 0;
        $complete = true;
        $feedback = [];

        foreach ($game['routes'] as $route) {
            $routeId = (string) $route['id'];
            $submitted = $answers[$routeId] ?? null;
            $expected = array_column($route['stages'], 'id');
            $total += count($expected);

            if (
                !is_array($submitted)
                || count($submitted) !== count($expected)
                || count(
                    array_filter(
                        $submitted,
                        static fn(mixed $stageId): bool =>
                            is_string($stageId)
                    )
                ) !== count($expected)
                || count(array_unique($submitted)) !== count($expected)
                || array_diff($submitted, $expected) !== []
                || array_diff($expected, $submitted) !== []
            ) {
                $complete = false;
                $submitted = [];
            }

            $routeCorrect = 0;

            foreach ($expected as $index => $stageId) {
                if (($submitted[$index] ?? '') === $stageId) {
                    $correct++;
                    $routeCorrect++;
                }
            }

            $feedback[] = [
                'title' => $route['title'],
                'correct' => $routeCorrect,
                'total' => count($expected),
                'order' => array_map(
                    static fn(array $stage): string =>
                        $stage['title'],
                    $route['stages']
                ),
            ];
        }

        return [
            'complete' => $complete,
            'correct' => $correct,
            'errors' => $total - $correct,
            'total' => $total,
            'feedback' => $feedback,
        ];
    }

    private function evaluateDecisions(
        array $game,
        array $answers
    ): array {
        $correct = 0;
        $impactPoints = 0;
        $maximumPoints = 0;
        $complete = true;
        $feedback = [];

        foreach ($game['scenarios'] as $scenario) {
            $rawSelectedId = $answers[$scenario['id']] ?? '';

            if (!is_string($rawSelectedId)) {
                $complete = false;
                continue;
            }

            $selectedId = $rawSelectedId;
            $selectedOption = null;
            $bestOption = null;

            foreach ($scenario['options'] as $option) {
                if (
                    $bestOption === null
                    || $option['impact'] > $bestOption['impact']
                ) {
                    $bestOption = $option;
                }

                if (hash_equals((string) $option['id'], $selectedId)) {
                    $selectedOption = $option;
                }
            }

            if ($selectedOption === null || $bestOption === null) {
                $complete = false;
                continue;
            }

            $impactPoints += (int) $selectedOption['impact'];
            $maximumPoints += (int) $bestOption['impact'];
            $isBestDecision =
                $selectedOption['impact'] === $bestOption['impact'];

            if ($isBestDecision) {
                $correct++;
            }

            $feedback[] = [
                'id' => $scenario['id'],
                'room' => $scenario['room'],
                'correct' => $isBestDecision,
                'chosen' => $selectedOption['label'],
                'recommended' => $bestOption['label'],
                'explanation' => $selectedOption['feedback'],
            ];
        }

        $total = count($game['scenarios']);
        $score = $maximumPoints > 0
            ? (int) round(
                ($impactPoints / $maximumPoints) * 100
            )
            : 0;

        return [
            'complete' => $complete && count($feedback) === $total,
            'correct' => $correct,
            'errors' => $total - $correct,
            'total' => $total,
            'score' => $score,
            'percentage' => $score,
            'feedback' => $feedback,
        ];
    }

    private function resultMessage(float $percentage): string
    {
        return match (true) {
            $percentage >= 100 =>
                '¡Resultado perfecto! Dominas muy bien este tema.',
            $percentage >= 80 =>
                '¡Excelente trabajo! Estás tomando grandes decisiones ambientales.',
            $percentage >= 60 =>
                '¡Buen trabajo! Revisa las recomendaciones para seguir aprendiendo.',
            default =>
                'Cada intento cuenta. Revisa las respuestas y vuelve a intentarlo.',
        };
    }

    private function activityOrFail(array $game): array
    {
        $activity = (new ActividadGamificacion())
            ->findActiveByName($game['database_name']);

        if ($activity === null) {
            $this->failure(
                'El juego todavía no se encuentra disponible.',
                'juegos'
            );
        }

        return $activity;
    }

    private function gameOrFail(string $slug): array
    {
        $definitions = $this->definitions();
        $game = $definitions[$slug] ?? null;

        if ($game === null || !$game['implemented']) {
            $this->failure(
                'No se encontró el juego solicitado.',
                'juegos'
            );
        }

        return $game;
    }

    private function gameOrFailJson(string $slug): array
    {
        $definitions = $this->definitions();
        $game = $definitions[$slug] ?? null;

        if ($game === null || !$game['implemented']) {
            $this->jsonResponse(
                ['success' => false, 'message' => 'No se encontró el juego.'],
                404
            );
        }

        return $game;
    }

    private function definitions(): array
{
    /*
     | Primero obtengo el contenido original
     | y después aplico las modificaciones guardadas.
     */
    $defaults = require ROOT_PATH
        . '/config/games.php';

    return (new ContenidoJuego())
        ->merge($defaults);
}

    private function jsonResponse(
        array $data,
        int $status = 200
    ): never {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');

        echo json_encode(
            $data,
            JSON_UNESCAPED_UNICODE
            | JSON_UNESCAPED_SLASHES
            | JSON_THROW_ON_ERROR
        );

        exit;
    }
}