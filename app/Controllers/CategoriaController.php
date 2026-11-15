<?php
declare(strict_types=1);

final class CategoriaController extends Controller
{
    private const TYPES = [
        'reciclable' => 'Reciclable',
        'reutilizable' => 'Reutilizable',
        'peligroso' => 'Peligroso',
        'especial' => 'Especial',
        'organico' => 'Orgánico',
        'otro' => 'Otro',
    ];

    /*
     | Limito los íconos a una lista conocida.
     | Así no permito introducir clases HTML arbitrarias.
     */
    private const ICONS = [
        'bi-recycle' => 'Reciclaje',
        'bi-trash3' => 'Contenedor',
        'bi-box-seam' => 'Caja',
        'bi-cup-straw' => 'Envases',
        'bi-phone' => 'Electrónicos',
        'bi-battery-charging' => 'Baterías',
        'bi-lightbulb' => 'Focos',
        'bi-droplet' => 'Líquidos',
        'bi-tree' => 'Orgánicos',
        'bi-exclamation-triangle' => 'Peligrosos',
    ];

    public function index(): void
    {
        $search = trim(
            (string) ($_GET['buscar'] ?? '')
        );

        $type = (string) (
            $_GET['tipo'] ?? ''
        );

        $status = (string) (
            $_GET['estado'] ?? ''
        );

        if (!array_key_exists($type, self::TYPES)) {
            $type = '';
        }

        if (
            !in_array(
                $status,
                ['', 'activo', 'inactivo'],
                true
            )
        ) {
            $status = '';
        }

        $canManage = has_role(
            'admin',
            'coordinador'
        );

        $categories = (new CategoriaResiduo())->search(
            $search,
            $type,
            $status,
            has_role('ciudadano')
        );

        $this->view(
            'categories/index',
            [
                'pageTitle' => 'Categorías de residuos',
                'categories' => $categories,
                'search' => $search,
                'type' => $type,
                'status' => $status,
                'types' => self::TYPES,
                'canManage' => $canManage,
            ],
            'app'
        );
    }

    public function create(): void
    {
        $this->view(
            'categories/form',
            [
                'pageTitle' => 'Registrar categoría',
                'editing' => false,
                'record' => [],
                'types' => self::TYPES,
                'icons' => self::ICONS,
            ],
            'app'
        );
    }

    public function store(): void
    {
        $data = $this->validated(false);

        $model = new CategoriaResiduo();

        if ($model->nameExists($data['nombre'])) {
            $this->failure(
                'Ya existe una categoría con ese nombre.',
                'categorias/crear'
            );
        }

        $categoryId = $model->create($data);

        Security::audit(
            'categoria_creada',
            [
                'id_categoria' => $categoryId,
                'tipo_residuo' =>
                    $data['tipo_residuo'],
            ]
        );

        $this->success(
            'La categoría se registró correctamente.',
            'categorias'
        );
    }

    public function edit(): void
    {
        $id = max(
            0,
            (int) ($_GET['id'] ?? 0)
        );

        $record = (new CategoriaResiduo())->find($id);

        if ($record === null) {
            $this->failure(
                'No se encontró la categoría solicitada.',
                'categorias'
            );
        }

        $this->view(
            'categories/form',
            [
                'pageTitle' => 'Editar categoría',
                'editing' => true,
                'record' => $record,
                'types' => self::TYPES,
                'icons' => self::ICONS,
            ],
            'app'
        );
    }

    public function update(): void
    {
        $id = max(
            0,
            (int) ($_POST['id_categoria'] ?? 0)
        );

        $model = new CategoriaResiduo();

        if ($model->find($id) === null) {
            $this->failure(
                'No se encontró la categoría solicitada.',
                'categorias'
            );
        }

        $data = $this->validated(true, $id);

        if (
            $model->nameExists(
                $data['nombre'],
                $id
            )
        ) {
            $this->failure(
                'Ya existe otra categoría con ese nombre.',
                'categorias/editar',
                ['id' => $id]
            );
        }

        $model->update($id, $data);

        Security::audit(
            'categoria_actualizada',
            ['id_categoria' => $id]
        );

        $this->success(
            'La categoría se actualizó correctamente.',
            'categorias'
        );
    }

    public function changeStatus(): void
    {
        $id = max(
            0,
            (int) ($_POST['id_categoria'] ?? 0)
        );

        $status = (string) (
            $_POST['estado'] ?? ''
        );

        if (
            !in_array(
                $status,
                ['activo', 'inactivo'],
                true
            )
        ) {
            $this->failure(
                'El estado solicitado no es válido.',
                'categorias'
            );
        }

        $model = new CategoriaResiduo();

        if ($model->find($id) === null) {
            $this->failure(
                'No se encontró la categoría solicitada.',
                'categorias'
            );
        }

        /*
         | No elimino sus relaciones con centros,
         | publicaciones o cotizaciones.
         */
        $model->update(
            $id,
            ['estado' => $status]
        );

        Security::audit(
            'categoria_estado_actualizado',
            [
                'id_categoria' => $id,
                'nuevo_estado' => $status,
            ]
        );

        $message = $status === 'activo'
            ? 'La categoría se activó correctamente.'
            : 'La categoría se desactivó correctamente.';

        $this->success($message, 'categorias');
    }

    private function validated(
        bool $editing,
        int $id = 0
    ): array {
        $returnRoute = $editing
            ? 'categorias/editar'
            : 'categorias/crear';

        $parameters = $editing
            ? ['id' => $id]
            : [];

        $name = trim(
            (string) ($_POST['nombre'] ?? '')
        );

        $description = trim(
            (string) ($_POST['descripcion'] ?? '')
        );

        $type = (string) (
            $_POST['tipo_residuo'] ?? ''
        );

        $icon = (string) (
            $_POST['icono'] ?? ''
        );

        $color = strtoupper(
            trim(
                (string) (
                    $_POST['color_hex'] ?? ''
                )
            )
        );

        $availableQuote = (
            (string) (
                $_POST['disponible_cotizacion']
                ?? '0'
            )
        ) === '1';

        $visibleKnowledge = (
            (string) (
                $_POST['visible_base_conocimiento']
                ?? '0'
            )
        ) === '1';

        $status = (string) (
            $_POST['estado'] ?? 'activo'
        );

        if (!$this->validLength($name, 2, 80)) {
            $this->failure(
                'El nombre debe contener entre 2 y 80 caracteres.',
                $returnRoute,
                $parameters
            );
        }

        if (
            $description !== ''
            && !$this->validLength(
                $description,
                1,
                255
            )
        ) {
            $this->failure(
                'La descripción no puede superar 255 caracteres.',
                $returnRoute,
                $parameters
            );
        }

        if (!array_key_exists($type, self::TYPES)) {
            $this->failure(
                'Selecciona un tipo de residuo válido.',
                $returnRoute,
                $parameters
            );
        }

        if (!array_key_exists($icon, self::ICONS)) {
            $this->failure(
                'Selecciona un ícono válido.',
                $returnRoute,
                $parameters
            );
        }

        if (
            preg_match(
                '/^#[0-9A-F]{6}$/',
                $color
            ) !== 1
        ) {
            $this->failure(
                'Selecciona un color hexadecimal válido.',
                $returnRoute,
                $parameters
            );
        }

        if (
            !in_array(
                $status,
                ['activo', 'inactivo'],
                true
            )
        ) {
            $this->failure(
                'Selecciona un estado válido.',
                $returnRoute,
                $parameters
            );
        }

        return [
            'nombre' => $name,
            'descripcion' => $description !== ''
                ? $description
                : null,
            'tipo_residuo' => $type,
            'icono' => $icon,
            'color_hex' => $color,
            'disponible_cotizacion' =>
                $availableQuote ? 1 : 0,
            'visible_base_conocimiento' =>
                $visibleKnowledge ? 1 : 0,
            'estado' => $status,
        ];
    }

    private function validLength(
        string $value,
        int $minimum,
        int $maximum
    ): bool {
        $length = function_exists('mb_strlen')
            ? mb_strlen($value)
            : strlen($value);

        return $length >= $minimum
            && $length <= $maximum;
    }
}