<?php
declare(strict_types=1);

final class NoticiaController extends Controller
{
    /**
     * Estos son los tipos que pertenecen exclusivamente
     * al módulo de noticias y campañas.
     */
    private const TYPES = [
        'noticia',
        'aviso',
        'campaña',
        'evento',
    ];

    /**
     * Cantidad de tarjetas mostradas por página.
     */
    private const PER_PAGE = 9;

    /**
     * Muestra el listado de noticias, avisos,
     * campañas y eventos.
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

        $type = trim(
            (string) ($_GET['tipo'] ?? '')
        );

        $status = trim(
            (string) ($_GET['estado'] ?? '')
        );

        $page = max(
            1,
            (int) ($_GET['pagina'] ?? 1)
        );

        /*
         | Solamente acepto tipos conocidos.
         | Esto evita utilizar directamente un valor arbitrario
         | recibido desde la URL.
         */
        if (
            $type !== ''
            && !in_array($type, self::TYPES, true)
        ) {
            $type = '';
        }

        /*
         | El ciudadano no puede filtrar borradores
         | porque solamente debe ver publicaciones activas.
         */
        if (
            !$canManage
            || !in_array(
                $status,
                ['borrador', 'publicado', 'inactivo'],
                true
            )
        ) {
            $status = '';
        }

        $publicationModel = new Publicacion();

        $conditions = [
            "p.tipo_publicacion IN (
                'noticia',
                'aviso',
                'campaña',
                'evento'
            )",
        ];

        $parameters = [];

        /*
         | El administrador y coordinador pueden ver todos
         | los estados. El ciudadano solamente lo publicado.
         */
        if (!$canManage) {
            $conditions[] = "p.estado = 'publicado'";
        }

        if ($search !== '') {
            $conditions[] = '(
                p.titulo LIKE ?
                OR p.resumen LIKE ?
                OR p.contenido LIKE ?
            )';

            $searchValue = '%' . $search . '%';

            $parameters[] = $searchValue;
            $parameters[] = $searchValue;
            $parameters[] = $searchValue;
        }

        if ($type !== '') {
            $conditions[] = 'p.tipo_publicacion = ?';
            $parameters[] = $type;
        }

        if ($status !== '') {
            $conditions[] = 'p.estado = ?';
            $parameters[] = $status;
        }

        $where = implode(
            ' AND ',
            $conditions
        );

        $total = (int) $publicationModel->scalar(
            'SELECT COUNT(*)
             FROM publicaciones AS p
             WHERE ' . $where,
            $parameters
        );

        $pages = max(
            1,
            (int) ceil(
                $total / self::PER_PAGE
            )
        );

        /*
         | Si alguien escribe una página demasiado grande,
         | regreso a la última página disponible.
         */
        $page = min($page, $pages);

        $offset = (
            $page - 1
        ) * self::PER_PAGE;

        /*
         | LIMIT y OFFSET se insertan como enteros controlados.
         | El resto de valores se envía como parámetros preparados.
         */
        $publications = $publicationModel->rows(
            'SELECT
                p.*,
                u.nombre_completo AS nombre_autor,

                (
                    SELECT GROUP_CONCAT(
                        cr.nombre
                        ORDER BY cr.nombre
                        SEPARATOR ", "
                    )
                    FROM publicacion_categoria AS pc
                    INNER JOIN categorias_residuos AS cr
                        ON cr.id_categoria = pc.id_categoria
                    WHERE pc.id_publicacion = p.id_publicacion
                ) AS nombres_categorias

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

             LIMIT ' . self::PER_PAGE . '
             OFFSET ' . $offset,
            $parameters
        );

        $this->view(
            'news/index',
            [
                'pageTitle' => 'Noticias y campañas',
                'publications' => $publications,
                'search' => $search,
                'type' => $type,
                'status' => $status,
                'total' => $total,
                'page' => $page,
                'pages' => $pages,
                'canManage' => $canManage,
                'typeLabels' => $this->typeLabels(),
            ],
            'app'
        );
    }

    /**
     * Muestra el contenido completo de una publicación.
     */
    public function show(): void
    {
        $id = (int) ($_GET['id'] ?? 0);

        $canManage = has_role(
            'admin',
            'coordinador'
        );

        $publication = $this->findPublication(
            $id,
            $canManage
        );

        if ($publication === null) {
            $this->failure(
                'La publicación solicitada no está disponible.',
                'noticias'
            );
        }

        $categories = (new Publicacion())->rows(
            'SELECT
                cr.id_categoria,
                cr.nombre
             FROM publicacion_categoria AS pc
             INNER JOIN categorias_residuos AS cr
                ON cr.id_categoria = pc.id_categoria
             WHERE pc.id_publicacion = ?
             ORDER BY cr.nombre',
            [$id]
        );

        $this->view(
            'news/show',
            [
                'pageTitle' => $publication['titulo'],
                'publication' => $publication,
                'categories' => $categories,
                'canManage' => $canManage,
                'typeLabels' => $this->typeLabels(),
            ],
            'app'
        );
    }

    /**
     * Muestra el formulario de una nueva publicación.
     */
    public function create(): void
    {
        $this->form(
            null,
            []
        );
    }

    /**
     * Guarda una nueva noticia, aviso,
     * campaña o evento.
     */
    public function store(): void
    {
        $validated = $this->validatePublication(
            null
        );

        $publicationModel = new Publicacion();
        $database = Database::connection();

        $newCover = null;

        try {
            /*
             | Primero valido y preparo el archivo.
             | Si se subió correctamente, guardaré su ruta.
             */
            $newCover = $this->uploadCover();

            $database->beginTransaction();

            $publicationId = $publicationModel->create([
                'id_autor' => auth_user()['id_usuario'],

                'tipo_publicacion' => (
                    $validated['tipo_publicacion']
                ),

                'titulo' => $validated['titulo'],
                'resumen' => $validated['resumen'],
                'contenido' => $validated['contenido'],

                'imagen_portada' => $newCover,

                'fecha_publicacion' => (
                    $validated['estado'] === 'publicado'
                        ? date('Y-m-d H:i:s')
                        : null
                ),

                'estado' => $validated['estado'],
            ]);

            $this->syncCategories(
                $publicationId,
                $validated['categorias']
            );

            $database->commit();

            Security::audit(
                'publicacion_noticia_creada',
                [
                    'id_publicacion' => $publicationId,
                    'tipo' => (
                        $validated['tipo_publicacion']
                    ),
                ]
            );
        } catch (Throwable $exception) {
            if ($database->inTransaction()) {
                $database->rollBack();
            }

            /*
             | Si MySQL falla después de subir la imagen,
             | elimino el archivo para no dejar basura.
             */
            if ($newCover !== null) {
                $this->deleteCover($newCover);
            }

            throw $exception;
        }

        $this->success(
            'La publicación se registró correctamente.',
            'noticias'
        );
    }

    /**
     * Muestra el formulario de edición.
     */
    public function edit(): void
    {
        $id = (int) ($_GET['id'] ?? 0);

        $publication = $this->findPublication(
            $id,
            true
        );

        if ($publication === null) {
            $this->failure(
                'La publicación que intentas editar no existe.',
                'noticias'
            );
        }

        $selectedCategories = array_map(
            'intval',
            array_column(
                (new Publicacion())->rows(
                    'SELECT id_categoria
                     FROM publicacion_categoria
                     WHERE id_publicacion = ?',
                    [$id]
                ),
                'id_categoria'
            )
        );

        $this->form(
            $publication,
            $selectedCategories
        );
    }

    /**
     * Actualiza una publicación existente.
     */
    public function update(): void
    {
        $id = (int) ($_POST['id_publicacion'] ?? 0);

        $current = $this->findPublication(
            $id,
            true
        );

        if ($current === null) {
            $this->failure(
                'La publicación que intentas actualizar no existe.',
                'noticias'
            );
        }

        $validated = $this->validatePublication(
            $id
        );

        $publicationModel = new Publicacion();
        $database = Database::connection();

        $newCover = null;

        try {
            $newCover = $this->uploadCover();

            /*
             | Si no se envió una imagen nueva,
             | conservo la portada anterior.
             */
            $coverToSave = $newCover
                ?? $current['imagen_portada'];

            /*
             | Si la publicación pasa de borrador a publicada,
             | registro el momento de la primera publicación.
             */
            $publicationDate = null;

            if ($validated['estado'] === 'publicado') {
                $publicationDate = (
                    $current['fecha_publicacion']
                    ?: date('Y-m-d H:i:s')
                );
            }

            $database->beginTransaction();

            $publicationModel->update(
                $id,
                [
                    'tipo_publicacion' => (
                        $validated['tipo_publicacion']
                    ),

                    'titulo' => $validated['titulo'],
                    'resumen' => $validated['resumen'],
                    'contenido' => $validated['contenido'],

                    'imagen_portada' => $coverToSave,

                    'fecha_publicacion' => (
                        $publicationDate
                    ),

                    'estado' => $validated['estado'],
                ]
            );

            $this->syncCategories(
                $id,
                $validated['categorias']
            );

            $database->commit();

            /*
             | La imagen anterior solamente se elimina
             | después de confirmar los cambios en MySQL.
             */
            if (
                $newCover !== null
                && !empty($current['imagen_portada'])
                && $newCover !== $current['imagen_portada']
            ) {
                $this->deleteCover(
                    (string) $current['imagen_portada']
                );
            }

            Security::audit(
                'publicacion_noticia_actualizada',
                [
                    'id_publicacion' => $id,
                    'tipo' => (
                        $validated['tipo_publicacion']
                    ),
                ]
            );
        } catch (Throwable $exception) {
            if ($database->inTransaction()) {
                $database->rollBack();
            }

            if ($newCover !== null) {
                $this->deleteCover($newCover);
            }

            throw $exception;
        }

        $this->success(
            'La publicación se actualizó correctamente.',
            'noticias/detalle',
            ['id' => $id]
        );
    }

    /**
     * Realiza una baja lógica.
     *
     * No elimina físicamente la publicación ni su imagen.
     */
    public function deactivate(): void
    {
        $id = (int) ($_POST['id_publicacion'] ?? 0);

        $publication = $this->findPublication(
            $id,
            true
        );

        if ($publication === null) {
            $this->failure(
                'La publicación solicitada no existe.',
                'noticias'
            );
        }

        (new Publicacion())->update(
            $id,
            [
                'estado' => 'inactivo',
            ]
        );

        Security::audit(
            'publicacion_noticia_desactivada',
            [
                'id_publicacion' => $id,
            ]
        );

        $this->success(
            'La publicación quedó inactiva.',
            'noticias'
        );
    }

    /**
     * Prepara el formulario de creación o edición.
     */
    private function form(
        ?array $publication,
        array $selectedCategories
    ): void {
        $categories = (new CategoriaResiduo())->rows(
            "SELECT
                id_categoria,
                nombre
             FROM categorias_residuos
             WHERE estado = 'activo'
             ORDER BY nombre"
        );

        $editing = $publication !== null;

        $this->view(
            'news/form',
            [
                'pageTitle' => $editing
                    ? 'Editar publicación'
                    : 'Nueva publicación',

                'publication' => $publication,
                'categories' => $categories,

                'selectedCategories' => (
                    $selectedCategories
                ),

                'editing' => $editing,
                'typeLabels' => $this->typeLabels(),
            ],
            'app'
        );
    }

    /**
     * Valida los datos recibidos desde el formulario.
     */
    private function validatePublication(
        ?int $publicationId
    ): array {
        $editing = $publicationId !== null;

        $failureRoute = $editing
            ? 'noticias/editar'
            : 'noticias/crear';

        $failureParameters = $editing
            ? ['id' => $publicationId]
            : [];

        $type = trim(
            (string) (
                $_POST['tipo_publicacion'] ?? ''
            )
        );

        $title = trim(
            (string) ($_POST['titulo'] ?? '')
        );

        $summary = trim(
            (string) ($_POST['resumen'] ?? '')
        );

        $content = trim(
            (string) ($_POST['contenido'] ?? '')
        );

        $status = trim(
            (string) ($_POST['estado'] ?? '')
        );

        $rawCategories = $_POST['categorias'] ?? [];

        if (
            !in_array($type, self::TYPES, true)
        ) {
            $this->failure(
                'Selecciona un tipo de publicación válido.',
                $failureRoute,
                $failureParameters
            );
        }

        if (
            $title === ''
            || mb_strlen($title) > 150
        ) {
            $this->failure(
                'El título es obligatorio y no debe superar 150 caracteres.',
                $failureRoute,
                $failureParameters
            );
        }

        if (mb_strlen($summary) > 255) {
            $this->failure(
                'El resumen no debe superar 255 caracteres.',
                $failureRoute,
                $failureParameters
            );
        }

        if ($content === '') {
            $this->failure(
                'Escribe el contenido completo de la publicación.',
                $failureRoute,
                $failureParameters
            );
        }

        if (
            !in_array(
                $status,
                ['borrador', 'publicado', 'inactivo'],
                true
            )
        ) {
            $this->failure(
                'Selecciona un estado válido.',
                $failureRoute,
                $failureParameters
            );
        }

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
                    static fn (int $id): bool => $id > 0
                )
            )
        );

        /*
         | Compruebo que todas las categorías seleccionadas
         | existan y continúen activas.
         */
        foreach ($categoryIds as $categoryId) {
            $exists = (int) (
                new CategoriaResiduo()
            )->scalar(
                "SELECT COUNT(*)
                 FROM categorias_residuos
                 WHERE id_categoria = ?
                   AND estado = 'activo'",
                [$categoryId]
            );

            if ($exists !== 1) {
                $this->failure(
                    'Una de las categorías seleccionadas no está disponible.',
                    $failureRoute,
                    $failureParameters
                );
            }
        }

        return [
            'tipo_publicacion' => $type,
            'titulo' => $title,
            'resumen' => (
                $summary !== ''
                    ? $summary
                    : null
            ),
            'contenido' => $content,
            'estado' => $status,
            'categorias' => $categoryIds,
        ];
    }

    /**
     * Busca una publicación que pertenezca
     * específicamente al módulo de noticias.
     */
    private function findPublication(
        int $id,
        bool $canManage
    ): ?array {
        if ($id <= 0) {
            return null;
        }

        $conditions = [
            'p.id_publicacion = ?',

            "p.tipo_publicacion IN (
                'noticia',
                'aviso',
                'campaña',
                'evento'
            )",
        ];

        if (!$canManage) {
            $conditions[] = "p.estado = 'publicado'";
        }

        return (new Publicacion())->row(
            'SELECT
                p.*,
                u.nombre_completo AS nombre_autor
             FROM publicaciones AS p
             INNER JOIN usuarios AS u
                ON u.id_usuario = p.id_autor
             WHERE '
             . implode(' AND ', $conditions)
             . '
             LIMIT 1',
            [$id]
        );
    }

    /**
     * Reemplaza las categorías relacionadas
     * con una publicación.
     *
     * Este método se ejecuta dentro de una transacción.
     */
    private function syncCategories(
        int $publicationId,
        array $categoryIds
    ): void {
        $publicationModel = new Publicacion();

        $publicationModel->query(
            'DELETE FROM publicacion_categoria
             WHERE id_publicacion = ?',
            [$publicationId]
        );

        foreach ($categoryIds as $categoryId) {
            $publicationModel->query(
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
    }

    /**
     * Valida y guarda una nueva imagen de portada.
     *
     * Devuelve null cuando no se seleccionó archivo.
     */
    private function uploadCover(): ?string
    {
        $file = $_FILES['imagen_portada'] ?? null;

        if (
            $file === null
            || (int) $file['error'] === UPLOAD_ERR_NO_FILE
        ) {
            return null;
        }

        if (
            (int) $file['error'] !== UPLOAD_ERR_OK
            || !is_uploaded_file($file['tmp_name'])
        ) {
            $this->failure(
                'No fue posible recibir la imagen seleccionada.',
                $this->formReturnRoute(),
                $this->formReturnParameters()
            );
        }

        $maximumBytes = 2 * 1024 * 1024;

        if ((int) $file['size'] > $maximumBytes) {
            $this->failure(
                'La imagen no debe superar 2 MB.',
                $this->formReturnRoute(),
                $this->formReturnParameters()
            );
        }

        $imageInformation = @getimagesize(
            $file['tmp_name']
        );

        if ($imageInformation === false) {
            $this->failure(
                'El archivo seleccionado no es una imagen válida.',
                $this->formReturnRoute(),
                $this->formReturnParameters()
            );
        }

        $width = (int) $imageInformation[0];
        $height = (int) $imageInformation[1];

        if (
            $width < 1
            || $height < 1
            || $width > 6000
            || $height > 6000
        ) {
            $this->failure(
                'Las dimensiones de la imagen no son válidas.',
                $this->formReturnRoute(),
                $this->formReturnParameters()
            );
        }

        $mimeType = (string) (
            $imageInformation['mime'] ?? ''
        );

        $allowedTypes = [
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/webp' => 'webp',
        ];

        if (!isset($allowedTypes[$mimeType])) {
            $this->failure(
                'La portada debe ser JPG, PNG o WebP.',
                $this->formReturnRoute(),
                $this->formReturnParameters()
            );
        }

        $uploadDirectory = ROOT_PATH
            . '/public/uploads/publicaciones';

        if (
            !is_dir($uploadDirectory)
            && !mkdir(
                $uploadDirectory,
                0750,
                true
            )
            && !is_dir($uploadDirectory)
        ) {
            throw new RuntimeException(
                'No fue posible preparar la carpeta de imágenes.'
            );
        }

        /*
         | No utilizo el nombre original del archivo.
         | Genero un nombre aleatorio en el servidor.
         */
        $fileName = 'publication-'
            . bin2hex(random_bytes(16))
            . '.'
            . $allowedTypes[$mimeType];

        $destination = $uploadDirectory
            . '/'
            . $fileName;

        if (
            !move_uploaded_file(
                $file['tmp_name'],
                $destination
            )
        ) {
            throw new RuntimeException(
                'No fue posible guardar la portada.'
            );
        }

        @chmod($destination, 0640);

        return 'uploads/publicaciones/'
            . $fileName;
    }

    /**
     * Elimina únicamente archivos que estén dentro
     * de la carpeta autorizada para publicaciones.
     */
    private function deleteCover(
        ?string $relativePath
    ): void {
        if (
            $relativePath === null
            || !str_starts_with(
                $relativePath,
                'uploads/publicaciones/'
            )
        ) {
            return;
        }

        $uploadDirectory = realpath(
            ROOT_PATH
            . '/public/uploads/publicaciones'
        );

        $filePath = realpath(
            ROOT_PATH
            . '/public/'
            . ltrim($relativePath, '/')
        );

        if (
            $uploadDirectory === false
            || $filePath === false
            || !str_starts_with(
                $filePath,
                $uploadDirectory
                . DIRECTORY_SEPARATOR
            )
            || !is_file($filePath)
        ) {
            return;
        }

        @unlink($filePath);
    }

    /**
     * Determina a qué formulario debe regresar
     * cuando falla la carga de una imagen.
     */
    private function formReturnRoute(): string
    {
        $id = (int) (
            $_POST['id_publicacion'] ?? 0
        );

        return $id > 0
            ? 'noticias/editar'
            : 'noticias/crear';
    }

    /**
     * Conserva el identificador al regresar
     * al formulario de edición.
     */
    private function formReturnParameters(): array
    {
        $id = (int) (
            $_POST['id_publicacion'] ?? 0
        );

        return $id > 0
            ? ['id' => $id]
            : [];
    }

    /**
     * Nombres visibles de los tipos de publicación.
     */
    private function typeLabels(): array
    {
        return [
            'noticia' => 'Noticia',
            'aviso' => 'Aviso',
            'campaña' => 'Campaña ambiental',
            'evento' => 'Evento',
        ];
    }
}