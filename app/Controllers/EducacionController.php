<?php
declare(strict_types=1);

final class EducacionController extends Controller
{
    /**
     * Muestra el listado de publicaciones educativas.
     */
    public function index(): void
    {
        $canManage =
            has_role('admin')
            || has_role('coordinador');

        $search = trim(
            (string) ($_GET['buscar'] ?? '')
        );

        $categoryId = (int) (
            $_GET['categoria'] ?? 0
        );

        $type = (string) (
            $_GET['tipo'] ?? ''
        );

        $status = $canManage
            ? (string) ($_GET['estado'] ?? '')
            : '';

        $page = max(
            1,
            (int) ($_GET['pagina'] ?? 1)
        );

        $model = new Publicacion();

        $result = $model->paginateKnowledge(
            [
                'search' => $search,
                'category' => $categoryId,
                'type' => $type,
                'status' => $status,
            ],
            !$canManage,
            $page
        );

        $categories = $model->categoriesForForm();

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

        /*
         | El tercer argumento obliga a utilizar
         | el layout que contiene menú y barra superior.
         */
        $this->view(
            'education/index',
            [
                'publications' => $result['records'],
                'total' => $result['total'],
                'page' => $result['page'],
                'pages' => $result['pages'],
                'search' => $search,
                'categoryId' => $categoryId,
                'type' => $type,
                'status' => $status,
                'categories' => $categories,
                'typeLabels' => $typeLabels,
                'statusLabels' => $statusLabels,
                'canManage' => $canManage,
                'pageTitle' =>
                    'Base de conocimiento ambiental',
            ],
            'app'
        );
    }

    /**
     * Muestra el contenido completo de una publicación.
     */
    public function show(): void
    {
        $publicationId = (int) (
            $_GET['id'] ?? 0
        );

        $canManage =
            has_role('admin')
            || has_role('coordinador');

        $model = new Publicacion();

        /*
         | Los ciudadanos solamente pueden localizar
         | contenido que se encuentre publicado.
         */
        $publication = $model->findKnowledge(
            $publicationId,
            !$canManage
        );

        if ($publication === null) {
            $this->failure(
                'La publicación no existe o no está disponible.',
                'educacion'
            );
        }

        $categories = $model
            ->categoriesForPublication(
                $publicationId,
                !$canManage
            );

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
            'education/show',
            [
                'publication' => $publication,
                'categories' => $categories,
                'typeLabels' => $typeLabels,
                'statusLabels' => $statusLabels,
                'canManage' => $canManage,
                'pageTitle' => $publication['titulo'],
            ],
            'app'
        );
    }
}