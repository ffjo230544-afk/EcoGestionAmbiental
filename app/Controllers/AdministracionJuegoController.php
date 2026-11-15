<?php
declare(strict_types=1);

final class AdministracionJuegoController extends Controller
{
   private const EDITABLE_GAMES = [
    'clasifica',
    'trivia',
    'memorama',
    'limpia-parque',
];

    /**
     * Muestro el formulario de administración del juego.
     */
    public function edit(): void
    {
        $slug = trim(
            (string) ($_GET['juego'] ?? '')
        );

        $game = $this->editableGameOrFail($slug);

        $activity = (new ActividadGamificacion())
            ->findByName(
                (string) $game['database_name']
            );

        if ($activity === null) {
            $this->failure(
                'No se encontró la actividad relacionada con el juego.',
                'juegos'
            );
        }

        $this->view(
            'games/edit',
            [
                'pageTitle' => 'Editar juego',
                'slug' => $slug,
                'game' => $game,
                'activity' => $activity,
                'useGames' => true,
                'useGameEditor' => true,
            ],
            'app'
        );
    }

    /**
     * Valido y guardo la configuración del juego.
     */
    public function update(): void
    {
        $slug = trim(
            (string) ($_POST['juego'] ?? '')
        );

        $game = $this->editableGameOrFail($slug);

        $title = trim(
            (string) ($_POST['titulo'] ?? '')
        );

        $description = trim(
            (string) ($_POST['descripcion'] ?? '')
        );

        $learning = trim(
            (string) ($_POST['aprendizaje'] ?? '')
        );

        $level = trim(
            (string) ($_POST['nivel'] ?? '')
        );

        $status = trim(
            (string) ($_POST['estado'] ?? '')
        );

        $allowedLevels = [
            'basico' => 'Básico',
            'intermedio' => 'Intermedio',
            'avanzado' => 'Avanzado',
        ];

        $allowedStatuses = [
            'activa',
            'inactiva',
        ];

        if (
            $this->textLength($title) < 3
            || $this->textLength($title) > 100
        ) {
            $this->formFailure(
                'El título debe contener entre 3 y 100 caracteres.',
                $slug
            );
        }

        if (
            $this->textLength($description) < 10
            || $this->textLength($description) > 250
        ) {
            $this->formFailure(
                'La descripción debe contener entre 10 y 250 caracteres.',
                $slug
            );
        }

        if (
            $this->textLength($learning) < 10
            || $this->textLength($learning) > 350
        ) {
            $this->formFailure(
                'El aprendizaje debe contener entre 10 y 350 caracteres.',
                $slug
            );
        }

        if (!isset($allowedLevels[$level])) {
            $this->formFailure(
                'Selecciona una dificultad válida.',
                $slug
            );
        }

        if (!in_array($status, $allowedStatuses, true)) {
            $this->formFailure(
                'Selecciona un estado válido.',
                $slug
            );
        }

        /*
         | Estos datos modifican la presentación general
         | del juego dentro del catálogo.
         */
        $content = [
            'title' => $title,
            'short_description' => $description,
            'learning' => $learning,
            'difficulty' => $allowedLevels[$level],
        ];

        if ($slug === 'clasifica') {
            $content = array_merge(
                $content,
                $this->classificationContent($game, $slug)
            );
        }

        if ($slug === 'trivia') {
            $content = array_merge(
                $content,
                $this->triviaContent($slug)
            );
        }

        if ($slug === 'memorama') {
    $content = array_merge(
        $content,
        $this->memoryContent($slug)
    );
}

if ($slug === 'limpia-parque') {
    $content = array_merge(
        $content,
        $this->cleanParkContent($slug)
    );
}



        $activityModel = new ActividadGamificacion();

        $activity = $activityModel->findByName(
            (string) $game['database_name']
        );

        if ($activity === null) {
            $this->formFailure(
                'No se encontró la actividad que se desea modificar.',
                $slug
            );
        }

        $contentRepository = new ContenidoJuego();

        /*
         | Conservo el contenido anterior para restaurarlo
         | si la actualización de MySQL produce un error.
         */
        $previousContent = $contentRepository->override($slug);

        try {
            $contentRepository->save(
                $slug,
                $content
            );

            $activityModel->update(
                (int) $activity['id_actividad'],
                [
                    'descripcion' => $description,
                    'nivel' => $level,
                    'instrucciones' => $learning,
                    'estado' => $status,
                ]
            );
        } catch (Throwable $exception) {
            try {
                $contentRepository->save(
                    $slug,
                    $previousContent
                );
            } catch (Throwable) {
                // Mantengo el error original de la actualización.
            }

            $this->formFailure(
                'No fue posible guardar los cambios del juego.',
                $slug
            );
        }

        Security::audit(
            'juego_actualizado',
            [
                'juego' => $slug,
                'id_actividad' => $activity['id_actividad'],
                'estado' => $status,
                'nivel' => $level,
            ]
        );

        $this->success(
            'El contenido del juego se actualizó correctamente.',
            'juegos/editar',
            ['juego' => $slug]
        );
    }

    /**
     * Valido las categorías y residuos del clasificador.
     */
    private function classificationContent(
        array $game,
        string $slug
    ): array {
        $submittedLabels = $_POST['categoria_nombre']
            ?? [];

        if (!is_array($submittedLabels)) {
            $this->formFailure(
                'Las categorías recibidas no son válidas.',
                $slug
            );
        }

        $categories = $game['categories'];

        foreach ($categories as $key => $category) {
            $label = trim(
                (string) (
                    $submittedLabels[$key]
                    ?? ''
                )
            );

            if (
                $this->textLength($label) < 2
                || $this->textLength($label) > 60
            ) {
                $this->formFailure(
                    'Cada categoría debe tener un nombre válido.',
                    $slug
                );
            }

            $categories[$key]['label'] = $label;
        }

        $names = $_POST['residuo_nombre'] ?? [];
        $emojis = $_POST['residuo_emoji'] ?? [];
        $selectedCategories = $_POST['residuo_categoria'] ?? [];

        if (
            !is_array($names)
            || !is_array($emojis)
            || !is_array($selectedCategories)
        ) {
            $this->formFailure(
                'Los residuos recibidos no son válidos.',
                $slug
            );
        }

        if (count($names) < 4 || count($names) > 30) {
            $this->formFailure(
                'El juego debe tener entre 4 y 30 residuos.',
                $slug
            );
        }

        $items = [];
        $usedIdentifiers = [];

        foreach ($names as $index => $rawName) {
            $name = trim((string) $rawName);
            $emoji = trim(
                (string) ($emojis[$index] ?? '')
            );
            $category = trim(
                (string) (
                    $selectedCategories[$index]
                    ?? ''
                )
            );

            if (
                $this->textLength($name) < 2
                || $this->textLength($name) > 80
            ) {
                $this->formFailure(
                    'Cada residuo debe tener un nombre de 2 a 80 caracteres.',
                    $slug
                );
            }

            if (
                $emoji === ''
                || $this->textLength($emoji) > 12
            ) {
                $this->formFailure(
                    'Cada residuo debe tener un emoji válido.',
                    $slug
                );
            }

            if (!isset($categories[$category])) {
                $this->formFailure(
                    'Selecciona una categoría válida para cada residuo.',
                    $slug
                );
            }

            $identifier = $this->uniqueIdentifier(
                $name,
                'residuo',
                $usedIdentifiers
            );

            $usedIdentifiers[$identifier] = true;

            $items[] = [
                'id' => $identifier,
                'name' => $name,
                'emoji' => $emoji,
                'category' => $category,
            ];
        }

        return [
            'categories' => $categories,
            'items' => $items,
        ];
    }

    /**
     * Valido las preguntas y respuestas de la trivia.
     */
    private function triviaContent(string $slug): array
    {
        $questions = $_POST['pregunta_texto'] ?? [];
        $optionA = $_POST['opcion_a'] ?? [];
        $optionB = $_POST['opcion_b'] ?? [];
        $optionC = $_POST['opcion_c'] ?? [];
        $correctAnswers = $_POST['respuesta_correcta'] ?? [];
        $explanations = $_POST['explicacion'] ?? [];

        if (
            !is_array($questions)
            || !is_array($optionA)
            || !is_array($optionB)
            || !is_array($optionC)
            || !is_array($correctAnswers)
            || !is_array($explanations)
        ) {
            $this->formFailure(
                'Las preguntas recibidas no son válidas.',
                $slug
            );
        }

        if (
            count($questions) < 3
            || count($questions) > 30
        ) {
            $this->formFailure(
                'La trivia debe tener entre 3 y 30 preguntas.',
                $slug
            );
        }

        $result = [];

        foreach ($questions as $index => $rawQuestion) {
            $question = trim((string) $rawQuestion);

            $options = [
                'a' => trim(
                    (string) ($optionA[$index] ?? '')
                ),
                'b' => trim(
                    (string) ($optionB[$index] ?? '')
                ),
                'c' => trim(
                    (string) ($optionC[$index] ?? '')
                ),
            ];

            $correct = trim(
                (string) (
                    $correctAnswers[$index]
                    ?? ''
                )
            );

            $explanation = trim(
                (string) (
                    $explanations[$index]
                    ?? ''
                )
            );

            if (
                $this->textLength($question) < 10
                || $this->textLength($question) > 200
            ) {
                $this->formFailure(
                    'Cada pregunta debe contener entre 10 y 200 caracteres.',
                    $slug
                );
            }

            foreach ($options as $option) {
                if (
                    $this->textLength($option) < 2
                    || $this->textLength($option) > 160
                ) {
                    $this->formFailure(
                        'Cada respuesta debe contener entre 2 y 160 caracteres.',
                        $slug
                    );
                }
            }

            if (!array_key_exists($correct, $options)) {
                $this->formFailure(
                    'Selecciona la respuesta correcta de cada pregunta.',
                    $slug
                );
            }

            if (
                $this->textLength($explanation) < 10
                || $this->textLength($explanation) > 350
            ) {
                $this->formFailure(
                    'Cada explicación debe contener entre 10 y 350 caracteres.',
                    $slug
                );
            }

            $result[] = [
                'id' => 'q' . ($index + 1),
                'question' => $question,
                'options' => $options,
                'correct' => $correct,
                'explanation' => $explanation,
            ];
        }

        return [
            'questions' => $result,
        ];
    }



/**
 * Valido las parejas utilizadas por el memorama.
 */
private function memoryContent(string $slug): array
{
    $firstLabels = $_POST['pareja_primera_etiqueta']
        ?? [];

    $firstEmojis = $_POST['pareja_primer_emoji']
        ?? [];

    $secondLabels = $_POST['pareja_segunda_etiqueta']
        ?? [];

    $secondEmojis = $_POST['pareja_segundo_emoji']
        ?? [];

    $explanations = $_POST['pareja_explicacion']
        ?? [];

    if (
        !is_array($firstLabels)
        || !is_array($firstEmojis)
        || !is_array($secondLabels)
        || !is_array($secondEmojis)
        || !is_array($explanations)
    ) {
        $this->formFailure(
            'Las parejas recibidas no son válidas.',
            $slug
        );
    }

    if (
        count($firstLabels) < 3
        || count($firstLabels) > 12
    ) {
        $this->formFailure(
            'El memorama debe tener entre 3 y 12 parejas.',
            $slug
        );
    }

    $pairs = [];
    $usedPairs = [];
    $usedCards = [];

    foreach ($firstLabels as $index => $rawFirstLabel) {
        $firstLabel = trim(
            (string) $rawFirstLabel
        );

        $firstEmoji = trim(
            (string) (
                $firstEmojis[$index]
                ?? ''
            )
        );

        $secondLabel = trim(
            (string) (
                $secondLabels[$index]
                ?? ''
            )
        );

        $secondEmoji = trim(
            (string) (
                $secondEmojis[$index]
                ?? ''
            )
        );

        $explanation = trim(
            (string) (
                $explanations[$index]
                ?? ''
            )
        );

        if (
            $this->textLength($firstLabel) < 2
            || $this->textLength($firstLabel) > 80
        ) {
            $this->formFailure(
                'El primer elemento de cada pareja debe tener entre 2 y 80 caracteres.',
                $slug
            );
        }

        if (
            $this->textLength($secondLabel) < 2
            || $this->textLength($secondLabel) > 80
        ) {
            $this->formFailure(
                'El segundo elemento de cada pareja debe tener entre 2 y 80 caracteres.',
                $slug
            );
        }

        if (
            $firstEmoji === ''
            || $this->textLength($firstEmoji) > 12
            || $secondEmoji === ''
            || $this->textLength($secondEmoji) > 12
        ) {
            $this->formFailure(
                'Cada tarjeta debe contener un emoji válido.',
                $slug
            );
        }

        if (
            $this->textLength($explanation) < 10
            || $this->textLength($explanation) > 350
        ) {
            $this->formFailure(
                'La explicación de cada pareja debe contener entre 10 y 350 caracteres.',
                $slug
            );
        }

        $pairIdentifier = $this->uniqueIdentifier(
            $firstLabel . '_' . $secondLabel,
            'pareja',
            $usedPairs
        );

        $usedPairs[$pairIdentifier] = true;

        $firstIdentifier = $this->uniqueIdentifier(
            'primera_' . $firstLabel,
            'primera_tarjeta',
            $usedCards
        );

        $usedCards[$firstIdentifier] = true;

        $secondIdentifier = $this->uniqueIdentifier(
            'segunda_' . $secondLabel,
            'segunda_tarjeta',
            $usedCards
        );

        $usedCards[$secondIdentifier] = true;

        $pairs[] = [
            'id' => $pairIdentifier,
            'first' => [
                'id' => $firstIdentifier,
                'label' => $firstLabel,
                'emoji' => $firstEmoji,
            ],
            'second' => [
                'id' => $secondIdentifier,
                'label' => $secondLabel,
                'emoji' => $secondEmoji,
            ],
            'explanation' => $explanation,
        ];
    }

    return [
        'pairs' => $pairs,
    ];


}



/**
 * Valido los residuos y elementos naturales del parque.
 */
private function cleanParkContent(string $slug): array
{
    $labels = $_POST['objeto_etiqueta'] ?? [];
    $emojis = $_POST['objeto_emoji'] ?? [];
    $types = $_POST['objeto_tipo'] ?? [];
    $positionsX = $_POST['objeto_x'] ?? [];
    $positionsY = $_POST['objeto_y'] ?? [];
    $tips = $_POST['objeto_consejo'] ?? [];

    if (
        !is_array($labels)
        || !is_array($emojis)
        || !is_array($types)
        || !is_array($positionsX)
        || !is_array($positionsY)
        || !is_array($tips)
    ) {
        $this->formFailure(
            'Los objetos del parque no son válidos.',
            $slug
        );
    }

    if (
        count($labels) < 6
        || count($labels) > 30
    ) {
        $this->formFailure(
            'El parque debe tener entre 6 y 30 objetos.',
            $slug
        );
    }

    $allowedTypes = [
        'waste',
        'nature',
    ];

    $objects = [];
    $usedIdentifiers = [];
    $wasteCount = 0;
    $natureCount = 0;

    foreach ($labels as $index => $rawLabel) {
        $label = trim(
            (string) $rawLabel
        );

        $emoji = trim(
            (string) (
                $emojis[$index]
                ?? ''
            )
        );

        $type = trim(
            (string) (
                $types[$index]
                ?? ''
            )
        );

        $rawX = $positionsX[$index] ?? '';
        $rawY = $positionsY[$index] ?? '';

        $tip = trim(
            (string) (
                $tips[$index]
                ?? ''
            )
        );

        if (
            $this->textLength($label) < 2
            || $this->textLength($label) > 80
        ) {
            $this->formFailure(
                'Cada objeto debe tener un nombre de 2 a 80 caracteres.',
                $slug
            );
        }

        if (
            $emoji === ''
            || $this->textLength($emoji) > 12
        ) {
            $this->formFailure(
                'Cada objeto debe tener un emoji válido.',
                $slug
            );
        }

        if (!in_array($type, $allowedTypes, true)) {
            $this->formFailure(
                'Selecciona un tipo válido para cada objeto.',
                $slug
            );
        }

        $positionX = filter_var(
            $rawX,
            FILTER_VALIDATE_INT,
            [
                'options' => [
                    'min_range' => 5,
                    'max_range' => 95,
                ],
            ]
        );

        $positionY = filter_var(
            $rawY,
            FILTER_VALIDATE_INT,
            [
                'options' => [
                    'min_range' => 5,
                    'max_range' => 95,
                ],
            ]
        );

        if (
            $positionX === false
            || $positionY === false
        ) {
            $this->formFailure(
                'Las posiciones deben estar entre 5 y 95.',
                $slug
            );
        }

        if ($type === 'nature') {
            $natureCount++;

            if (
                $this->textLength($tip) < 10
                || $this->textLength($tip) > 300
            ) {
                $this->formFailure(
                    'Cada elemento natural debe incluir un consejo de 10 a 300 caracteres.',
                    $slug
                );
            }
        } else {
            $wasteCount++;

            /*
             | Los residuos no necesitan una recomendación
             | porque son los objetos que el usuario recoge.
             */
            $tip = '';
        }

        $identifier = $this->uniqueIdentifier(
            $type . '_' . $label,
            'objeto',
            $usedIdentifiers
        );

        $usedIdentifiers[$identifier] = true;

        $object = [
            'id' => $identifier,
            'label' => $label,
            'emoji' => $emoji,
            'type' => $type,
            'x' => (int) $positionX,
            'y' => (int) $positionY,
        ];

        if ($type === 'nature') {
            $object['tip'] = $tip;
        }

        $objects[] = $object;
    }

    if ($wasteCount < 4) {
        $this->formFailure(
            'El parque debe tener al menos cuatro residuos.',
            $slug
        );
    }

    if ($natureCount < 2) {
        $this->formFailure(
            'El parque debe tener al menos dos elementos naturales.',
            $slug
        );
    }

    return [
        'objects' => $objects,
    ];
}




    /**
     * Obtengo un juego que ya cuenta con formulario de edición.
     */
    private function editableGameOrFail(string $slug): array
    {
        if (!in_array($slug, self::EDITABLE_GAMES, true)) {
            $this->failure(
                'El juego solicitado todavía no tiene un editor disponible.',
                'juegos'
            );
        }

        $defaults = require ROOT_PATH
            . '/config/games.php';

        $definitions = (new ContenidoJuego())
            ->merge($defaults);

        $game = $definitions[$slug] ?? null;

        if (
            !is_array($game)
            || empty($game['implemented'])
        ) {
            $this->failure(
                'No se encontró el juego solicitado.',
                'juegos'
            );
        }

        return $game;
    }

    /**
     * Creo un identificador interno sin caracteres peligrosos.
     */
    private function uniqueIdentifier(
        string $text,
        string $prefix,
        array $used
    ): string {
        $converted = iconv(
            'UTF-8',
            'ASCII//TRANSLIT//IGNORE',
            $text
        );

        if ($converted === false) {
            $converted = $text;
        }

        $identifier = strtolower($converted);

        $identifier = preg_replace(
            '/[^a-z0-9]+/',
            '_',
            $identifier
        );

        $identifier = trim(
            (string) $identifier,
            '_'
        );

        if ($identifier === '') {
            $identifier = $prefix;
        }

        $base = $identifier;
        $number = 2;

        while (isset($used[$identifier])) {
            $identifier = $base . '_' . $number;
            $number++;
        }

        return $identifier;
    }

    /**
     * Calculo la longitud respetando caracteres acentuados.
     */
    private function textLength(string $text): int
    {
        if (function_exists('mb_strlen')) {
            return mb_strlen(
                $text,
                'UTF-8'
            );
        }

        return strlen($text);
    }

    /**
     * Regreso al mismo formulario conservando sus datos.
     */
    private function formFailure(
        string $message,
        string $slug
    ): never {
        $this->failure(
            $message,
            'juegos/editar',
            ['juego' => $slug]
        );
    }
}