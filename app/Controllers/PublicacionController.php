<?php
declare(strict_types=1);

final class PublicacionController extends Controller
{
    private const TYPES = [
        'material_educativo',
        'ficha_informativa',
    ];

    private const STATUSES = [
        'borrador',
        'publicado',
        'inactivo',
    ];

    /**
     * Muestra el formulario de creación.
     */
    public function create(): void
    {
        $this->form([], false);
    }

    /**
     * Guarda una nueva publicación.
     */
    public function store(): void
    {
        $validated = $this->validated(
            false,
            null
        );

        $imagePath = $this->uploadCover(
            'educacion/crear'
        );

        if ($imagePath !== null) {
            $validated['publication']['imagen_portada'] =
                $imagePath;
        }

        $user = auth_user();

        $validated['publication']['id_autor'] =
            (int) $user['id_usuario'];

        try {
            $publicationId = (new Publicacion())
                ->saveWithCategories(
                    null,
                    $validated['publication'],
                    $validated['categories']
                );
        } catch (Throwable) {
            $this->removeUploadedImage(
                $imagePath
            );

            $this->failure(
                'No fue posible guardar la publicación.',
                'educacion/crear'
            );
        }

        Security::audit(
            'publicacion_educativa_creada',
            [
                'id_publicacion' => $publicationId,
                'tipo' => $validated[
                    'publication'
                ]['tipo_publicacion'],
            ]
        );

        $this->success(
            'La publicación se registró correctamente.',
            'educacion/detalle',
            ['id' => $publicationId]
        );
    }

    /**
     * Muestra el formulario de edición.
     */
    public function edit(): void
    {
        $publicationId = (int) (
            $_GET['id'] ?? 0
        );

        $publication = (new Publicacion())
            ->findKnowledge($publicationId);

        if ($publication === null) {
            $this->failure(
                'La publicación solicitada no existe.',
                'educacion'
            );
        }

        $this->form(
            $publication,
            true
        );
    }

    /**
     * Actualiza una publicación.
     */
    public function update(): void
    {
        $publicationId = (int) (
            $_POST['id_publicacion'] ?? 0
        );

        $model = new Publicacion();

        $current = $model->findKnowledge(
            $publicationId
        );

        if ($current === null) {
            $this->failure(
                'La publicación solicitada no existe.',
                'educacion'
            );
        }

        $validated = $this->validated(
            true,
            $current
        );

        $imagePath = $this->uploadCover(
            'educacion/editar',
            ['id' => $publicationId]
        );

        if ($imagePath !== null) {
            $validated['publication']['imagen_portada'] =
                $imagePath;
        }

        try {
            $model->saveWithCategories(
                $publicationId,
                $validated['publication'],
                $validated['categories']
            );
        } catch (Throwable) {
            $this->removeUploadedImage(
                $imagePath
            );

            $this->failure(
                'No fue posible actualizar la publicación.',
                'educacion/editar',
                ['id' => $publicationId]
            );
        }

        /*
         | La portada anterior se elimina únicamente
         | después de confirmar los cambios en MySQL.
         */
        if (
            $imagePath !== null
            && !empty($current['imagen_portada'])
        ) {
            $this->removeUploadedImage(
                (string) $current['imagen_portada']
            );
        }

        Security::audit(
            'publicacion_educativa_actualizada',
            ['id_publicacion' => $publicationId]
        );

        $this->success(
            'La publicación se actualizó correctamente.',
            'educacion/detalle',
            ['id' => $publicationId]
        );
    }

    /**
     * Desactiva sin eliminar físicamente.
     */
    public function deactivate(): void
    {
        $publicationId = (int) (
            $_POST['id_publicacion'] ?? 0
        );

        $model = new Publicacion();

        $publication = $model->findKnowledge(
            $publicationId
        );

        if ($publication === null) {
            $this->failure(
                'La publicación solicitada no existe.',
                'educacion'
            );
        }

        $model->update(
            $publicationId,
            ['estado' => 'inactivo']
        );

        Security::audit(
            'publicacion_educativa_desactivada',
            ['id_publicacion' => $publicationId]
        );

        $this->success(
            'La publicación se desactivó sin eliminar su historial.',
            'educacion'
        );
    }

    /**
     * Prepara el formulario.
     */
    private function form(
        array $publication,
        bool $editing
    ): void {
        $publicationId = $editing
            ? (int) $publication['id_publicacion']
            : 0;

        $model = new Publicacion();

        $categories = $model->categoriesForForm(
            $publicationId
        );

        $selectedCategories = [];

        if ($editing) {
            $selectedCategories = array_map(
                static fn(array $category): int =>
                    (int) $category['id_categoria'],
                $model->categoriesForPublication(
                    $publicationId
                )
            );
        }

        $typeLabels = [
            'material_educativo' =>
                'Material educativo',

            'ficha_informativa' =>
                'Ficha informativa',
        ];

        $statusLabels = [
            'borrador' => 'Borrador',
            'publicado' => 'Publicado',
            'inactivo' => 'Inactivo',
        ];

        $this->view(
            'education/form',
            [
                'publication' => $publication,
                'editing' => $editing,
                'categories' => $categories,
                'selectedCategories' =>
                    $selectedCategories,
                'typeLabels' => $typeLabels,
                'statusLabels' => $statusLabels,
                'pageTitle' => $editing
                    ? 'Editar publicación'
                    : 'Nueva publicación',
            ],
            'app'
        );
    }

    /**
     * Valida la información enviada.
     */
    private function validated(
        bool $editing,
        ?array $current
    ): array {
        $publicationId = $editing
            ? (int) $current['id_publicacion']
            : 0;

        $route = $editing
            ? 'educacion/editar'
            : 'educacion/crear';

        $parameters = $editing
            ? ['id' => $publicationId]
            : [];

        $title = trim(
            (string) ($_POST['titulo'] ?? '')
        );

        $summary = trim(
            (string) ($_POST['resumen'] ?? '')
        );

        $content = trim(
            (string) ($_POST['contenido'] ?? '')
        );

        $type = (string) (
            $_POST['tipo_publicacion'] ?? ''
        );

        $status = (string) (
            $_POST['estado'] ?? 'borrador'
        );

        if (
            $title === ''
            || $this->textLength($title) > 150
        ) {
            $this->failure(
                'El título es obligatorio y debe tener máximo 150 caracteres.',
                $route,
                $parameters
            );
        }

        if ($this->textLength($summary) > 255) {
            $this->failure(
                'El resumen debe tener máximo 255 caracteres.',
                $route,
                $parameters
            );
        }

        /*
         | TEXT en MySQL admite aproximadamente 65 KB.
         | Utilizamos 60 KB para mantener un margen.
         */
        if (
            $content === ''
            || strlen($content) > 60000
        ) {
            $this->failure(
                'El contenido es obligatorio y no debe superar 60 KB.',
                $route,
                $parameters
            );
        }

        if (!in_array($type, self::TYPES, true)) {
            $this->failure(
                'Selecciona un tipo de publicación válido.',
                $route,
                $parameters
            );
        }

        if (
            !in_array(
                $status,
                self::STATUSES,
                true
            )
        ) {
            $this->failure(
                'Selecciona un estado válido.',
                $route,
                $parameters
            );
        }

        $rawCategories = $_POST['categorias'] ?? [];

        if (!is_array($rawCategories)) {
            $rawCategories = [];
        }

        $categoryIds = array_values(
            array_unique(
                array_filter(
                    array_map(
                        'intval',
                        $rawCategories
                    ),
                    static fn(int $id): bool => $id > 0
                )
            )
        );

        if ($categoryIds === []) {
            $this->failure(
                'Selecciona por lo menos una categoría.',
                $route,
                $parameters
            );
        }

        $availableCategories = (
            new Publicacion()
        )->categoriesForForm($publicationId);

        $availableIds = array_map(
            static fn(array $category): int =>
                (int) $category['id_categoria'],
            $availableCategories
        );

        foreach ($categoryIds as $categoryId) {
            if (
                !in_array(
                    $categoryId,
                    $availableIds,
                    true
                )
            ) {
                $this->failure(
                    'Una categoría seleccionada no está disponible.',
                    $route,
                    $parameters
                );
            }
        }

        if ($status === 'publicado') {
            $publicationDate = (
                $editing
                && $current['estado'] === 'publicado'
                && !empty($current['fecha_publicacion'])
            )
                ? $current['fecha_publicacion']
                : date('Y-m-d H:i:s');
        } else {
            $publicationDate = $editing
                ? $current['fecha_publicacion']
                : null;
        }

        return [
            'publication' => [
                'tipo_publicacion' => $type,
                'titulo' => $title,
                'resumen' => $summary !== ''
                    ? $summary
                    : null,
                'contenido' => $content,
                'fecha_publicacion' =>
                    $publicationDate,
                'estado' => $status,
            ],

            'categories' => $categoryIds,
        ];
    }

    /**
     * Valida y guarda la portada.
     */
    private function uploadCover(
        string $failureRoute,
        array $parameters = []
    ): ?string {
        $upload = $_FILES['imagen_portada'] ?? null;

        if (
            $upload === null
            || (int) ($upload['error'] ?? UPLOAD_ERR_NO_FILE)
                === UPLOAD_ERR_NO_FILE
        ) {
            return null;
        }

        if (
            (int) $upload['error'] !== UPLOAD_ERR_OK
            || !is_uploaded_file(
                (string) $upload['tmp_name']
            )
        ) {
            $this->failure(
                'La imagen no pudo cargarse correctamente.',
                $failureRoute,
                $parameters
            );
        }

        if (
            (int) $upload['size']
            > 2 * 1024 * 1024
        ) {
            $this->failure(
                'La imagen no debe superar 2 MB.',
                $failureRoute,
                $parameters
            );
        }

        $image = @getimagesize(
            (string) $upload['tmp_name']
        );

        $extensions = [
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/webp' => 'webp',
        ];

        $mime = (string) ($image['mime'] ?? '');

        if (
            $image === false
            || !isset($extensions[$mime])
        ) {
            $this->failure(
                'Selecciona una imagen JPG, PNG o WebP válida.',
                $failureRoute,
                $parameters
            );
        }

        $width = (int) ($image[0] ?? 0);
        $height = (int) ($image[1] ?? 0);

        if (
            $width < 1
            || $height < 1
            || $width > 6000
            || $height > 6000
        ) {
            $this->failure(
                'Las dimensiones de la imagen no son válidas.',
                $failureRoute,
                $parameters
            );
        }

        $directory =
            ROOT_PATH
            . '/public/uploads/publicaciones';

        if (
            !is_dir($directory)
            && !mkdir($directory, 0755, true)
        ) {
            $this->failure(
                'No fue posible preparar la carpeta de imágenes.',
                $failureRoute,
                $parameters
            );
        }

        $fileName =
            'publication-'
            . bin2hex(random_bytes(16))
            . '.'
            . $extensions[$mime];

        $absolutePath =
            $directory . '/' . $fileName;

        if (
            !move_uploaded_file(
                (string) $upload['tmp_name'],
                $absolutePath
            )
        ) {
            $this->failure(
                'No fue posible guardar la imagen.',
                $failureRoute,
                $parameters
            );
        }

        return 'uploads/publicaciones/' . $fileName;
    }

    /**
     * Elimina únicamente portadas generadas por
     * este controlador.
     */
    private function removeUploadedImage(
        ?string $relativePath
    ): void {
        if ($relativePath === null) {
            return;
        }

        if (
            !preg_match(
                '#^uploads/publicaciones/'
                . 'publication-[a-f0-9]{32}'
                . '\.(jpg|png|webp)$#',
                $relativePath
            )
        ) {
            return;
        }

        $absolutePath =
            ROOT_PATH
            . '/public/'
            . $relativePath;

        if (is_file($absolutePath)) {
            @unlink($absolutePath);
        }
    }

    private function textLength(string $text): int
    {
        return function_exists('mb_strlen')
            ? mb_strlen($text)
            : strlen($text);
    }
}