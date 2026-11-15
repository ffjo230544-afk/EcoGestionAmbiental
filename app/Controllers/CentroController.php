<?php
declare(strict_types=1);

final class CentroController extends Controller
{
    private const POINT_TYPES = [
        'empresa' => 'Empresa',
        'centro_acopio' => 'Centro de acopio',
        'punto_servicio' => 'Punto de servicio',
    ];

    private const SERVICE_TYPES = [
        'compra_residuos' => 'Compra de residuos',
        'recepcion_reciclables' =>
            'Recepción de reciclables',
        'residuos_electronicos' =>
            'Residuos electrónicos',
        'materiales_toxicos' =>
            'Materiales tóxicos',
        'servicio_general' =>
            'Servicio general',
    ];

    private const DAYS = [
        'lunes' => 'Lunes',
        'martes' => 'Martes',
        'miercoles' => 'Miércoles',
        'jueves' => 'Jueves',
        'viernes' => 'Viernes',
        'sabado' => 'Sábado',
        'domingo' => 'Domingo',
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

    if (
        !array_key_exists(
            $type,
            self::POINT_TYPES
        )
    ) {
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

    $onlyActive = has_role('ciudadano');

    /*
     | El administrador no tiene restricción.
     | El coordinador recibe el centro de su sesión.
     */
    $restrictedPointId = has_role('coordinador')
        ? auth_center_id()
        : null;

    /*
     | Normalmente require_auth() ya habrá retirado
     | una sesión de coordinador sin centro.
     | Esta validación se mantiene como segunda capa.
     */
    if (
        has_role('coordinador')
        && $restrictedPointId === null
    ) {
        deny_access();
    }

    $centers = (new PuntoAtencion())->search(
        $search,
        $type,
        $status,
        $onlyActive,
        $restrictedPointId
    );

    $this->view(
        'centers/index',
        [
            'pageTitle' => 'Centros de acopio',
            'centers' => $centers,
            'search' => $search,
            'type' => $type,
            'status' => $status,
            'pointTypes' => self::POINT_TYPES,

            /*
             | Puede editar y configurar materiales.
             */
            'canManage' => has_role(
                'admin',
                'coordinador'
            ),

            /*
             | Solamente el administrador puede
             | crear o cambiar estados.
             */
            'canCreate' => has_role('admin'),
            'canChangeStatus' => has_role('admin'),
        ],
        'app'
    );
}

    public function show(): void
{
    $id = max(
        0,
        (int) ($_GET['id'] ?? 0)
    );

    $model = new PuntoAtencion();

    $center = $model->find($id);

    if ($center === null) {
        $this->failure(
            'No se encontró el centro solicitado.',
            'centros'
        );
    }

    $canManage = can_manage_center($id);

    /*
     | Un centro inactivo solamente puede ser consultado
     | por el administrador o por su responsable.
     */
    if (
        $center['estado'] !== 'activo'
        && !has_role('admin')
        && !$canManage
    ) {
        $this->failure(
            'No se encontró el centro solicitado.',
            'centros'
        );
    }

    $categories = $model->categories(
        $id,
        has_role('ciudadano')
    );

    $schedules = $model->schedules($id);

    $this->view(
        'centers/show',
        [
            'pageTitle' => $center['nombre'],
            'center' => $center,
            'categories' => $categories,
            'schedules' => $schedules,
            'pointTypes' => self::POINT_TYPES,
            'serviceTypes' => self::SERVICE_TYPES,
            'days' => self::DAYS,
            'canManage' => $canManage,
        ],
        'app'
    );
}

public function create(): void
{
    $this->view(
        'centers/form',
        [
            'pageTitle' => 'Registrar centro',
            'editing' => false,
            'record' => [],
            'pointTypes' => self::POINT_TYPES,
            'serviceTypes' => self::SERVICE_TYPES,
            'canChangeStatus' => true,
        ],
        'app'
    );
}

public function store(): void
{
    $data = $this->validated(false);

    $model = new PuntoAtencion();

    if (
        $model->duplicateExists(
            $data['nombre'],
            $data['direccion']
        )
    ) {
        $this->failure(
            'Ya existe un centro con el mismo nombre y dirección.',
            'centros/crear'
        );
    }

    $pointId = $model->create($data);

    Security::audit(
        'centro_creado',
        [
            'id_punto' => $pointId,
            'tipo_punto' => $data['tipo_punto'],
        ]
    );

    $this->success(
        'El centro se registró correctamente.',
        'centros/detalle',
        ['id' => $pointId]
    );
}

public function edit(): void
{
    $id = max(
        0,
        (int) ($_GET['id'] ?? 0)
    );

    $record = (new PuntoAtencion())->find($id);

    if ($record === null) {
        $this->failure(
            'No se encontró el centro solicitado.',
            'centros'
        );
    }

    /*
     | Impide editar otro centro manipulando la URL.
     */
    require_center_management($id);

    $this->view(
        'centers/form',
        [
            'pageTitle' => 'Editar centro',
            'editing' => true,
            'record' => $record,
            'pointTypes' => self::POINT_TYPES,
            'serviceTypes' => self::SERVICE_TYPES,
            'canChangeStatus' => has_role('admin'),
        ],
        'app'
    );
}

public function update(): void
{
    $id = max(
        0,
        (int) ($_POST['id_punto'] ?? 0)
    );

    $model = new PuntoAtencion();

    $current = $model->find($id);

    if ($current === null) {
        $this->failure(
            'No se encontró el centro solicitado.',
            'centros'
        );
    }

    /*
     | La validación se realiza nuevamente en el servidor.
     */
    require_center_management($id);

    $data = $this->validated(true, $id);

    /*
     | Aunque un coordinador manipule el formulario,
     | no puede cambiar el estado del centro.
     */
    if (has_role('coordinador')) {
        $data['estado'] = $current['estado'];
    }

    if (
        $model->duplicateExists(
            $data['nombre'],
            $data['direccion'],
            $id
        )
    ) {
        $this->failure(
            'Ya existe otro centro con ese nombre y dirección.',
            'centros/editar',
            ['id' => $id]
        );
    }

    $model->update($id, $data);

    Security::audit(
        'centro_actualizado',
        [
            'id_punto' => $id,
            'rol' => auth_role(),
        ]
    );

    $this->success(
        'La información del centro se actualizó.',
        'centros/detalle',
        ['id' => $id]
    );
}

public function changeStatus(): void
{
    $id = max(
        0,
        (int) ($_POST['id_punto'] ?? 0)
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
            'centros'
        );
    }

    $model = new PuntoAtencion();

    if ($model->find($id) === null) {
        $this->failure(
            'No se encontró el centro solicitado.',
            'centros'
        );
    }

    $model->update(
        $id,
        ['estado' => $status]
    );

    Security::audit(
        'centro_estado_actualizado',
        [
            'id_punto' => $id,
            'nuevo_estado' => $status,
        ]
    );

    $message = $status === 'activo'
        ? 'El centro se activó correctamente.'
        : 'El centro se desactivó correctamente.';

    $this->success($message, 'centros');
}

public function materials(): void
{
    $pointId = max(
        0,
        (int) ($_GET['id'] ?? 0)
    );

    $center = (new PuntoAtencion())->find($pointId);

    if ($center === null) {
        $this->failure(
            'No se encontró el centro solicitado.',
            'centros'
        );
    }
    require_center_management($pointId);

    $categories = (new PuntoCategoria())
        ->configuration($pointId);

    $this->view(
        'centers/materials',
        [
            'pageTitle' =>
                'Materiales de ' . $center['nombre'],
            'center' => $center,
            'categories' => $categories,
        ],
        'app'
    );
}

public function updateMaterials(): void
{
    $pointId = max(
        0,
        (int) ($_POST['id_punto'] ?? 0)
    );

    $center = (new PuntoAtencion())->find($pointId);

    if ($center === null) {
        $this->failure(
            'No se encontró el centro solicitado.',
            'centros'
        );
    }
    
    require_center_management($pointId);

    $model = new PuntoCategoria();

    /*
     | Esta lista determina qué identificadores
     | son realmente válidos para este formulario.
     */
    $availableCategories = $model->configuration(
        $pointId
    );

    $allowed = [];

    foreach ($availableCategories as $category) {
        $allowed[
            (int) $category['id_categoria']
        ] = $category;
    }

    $submitted = $_POST['categorias'] ?? [];

    if (!is_array($submitted)) {
        $this->failure(
            'La selección de materiales no es válida.',
            'centros/materiales',
            ['id' => $pointId]
        );
    }

    $relations = [];

    foreach ($submitted as $categoryId => $values) {
        $categoryId = (int) $categoryId;

        if (
            !isset($allowed[$categoryId])
            || !is_array($values)
        ) {
            continue;
        }

        $selected = (
            (string) (
                $values['seleccionada'] ?? '0'
            )
        ) === '1';

        if (!$selected) {
            continue;
        }

        $acceptsPurchase = (
            (string) (
                $values['acepta_compra'] ?? '0'
            )
        ) === '1';

        $acceptsReception = (
            (string) (
                $values['acepta_recepcion'] ?? '0'
            )
        ) === '1';

        /*
         | Una categoría relacionada debe realizar
         | por lo menos una operación.
         */
        if (
            !$acceptsPurchase
            && !$acceptsReception
        ) {
            $this->failure(
                'Selecciona compra o recepción para '
                . $allowed[$categoryId]['nombre']
                . '.',
                'centros/materiales',
                ['id' => $pointId]
            );
        }

        $observations = trim(
            (string) (
                $values['observaciones'] ?? ''
            )
        );

        $length = function_exists('mb_strlen')
            ? mb_strlen($observations)
            : strlen($observations);

        if ($length > 150) {
            $this->failure(
                'Las observaciones de '
                . $allowed[$categoryId]['nombre']
                . ' no pueden superar 150 caracteres.',
                'centros/materiales',
                ['id' => $pointId]
            );
        }

        $relations[] = [
            'id_categoria' => $categoryId,
            'acepta_compra' =>
                $acceptsPurchase ? 1 : 0,
            'acepta_recepcion' =>
                $acceptsReception ? 1 : 0,
            'observaciones' =>
                $observations !== ''
                    ? $observations
                    : null,
        ];
    }

    $model->saveForCenter(
        $pointId,
        $relations
    );

    Security::audit(
        'materiales_centro_actualizados',
        [
            'id_punto' => $pointId,
            'total_categorias' =>
                count($relations),
        ]
    );

    $this->success(
        'Los materiales del centro se actualizaron.',
        'centros/detalle',
        ['id' => $pointId]
    );
}


private function validated(
    bool $editing,
    int $id = 0
): array {
    $returnRoute = $editing
        ? 'centros/editar'
        : 'centros/crear';

    $parameters = $editing
        ? ['id' => $id]
        : [];

    $name = trim(
        (string) ($_POST['nombre'] ?? '')
    );

    $pointType = (string) (
        $_POST['tipo_punto'] ?? ''
    );

    $address = trim(
        (string) ($_POST['direccion'] ?? '')
    );

    $municipality = trim(
        (string) ($_POST['municipio'] ?? '')
    );

    $region = trim(
        (string) ($_POST['estado_region'] ?? '')
    );

    $postalCode = trim(
        (string) ($_POST['codigo_postal'] ?? '')
    );

    $phone = trim(
        (string) ($_POST['telefono'] ?? '')
    );

    $email = strtolower(
        trim(
            (string) (
                $_POST['correo_contacto'] ?? ''
            )
        )
    );

    $serviceType = (string) (
        $_POST['tipo_servicio'] ?? ''
    );

    $latitude = str_replace(
        ',',
        '.',
        trim((string) ($_POST['latitud'] ?? ''))
    );

    $longitude = str_replace(
        ',',
        '.',
        trim((string) ($_POST['longitud'] ?? ''))
    );

    $description = trim(
        (string) ($_POST['descripcion'] ?? '')
    );

    $status = (string) (
        $_POST['estado'] ?? 'activo'
    );

    if (!$this->validLength($name, 3, 120)) {
        $this->failure(
            'El nombre debe contener entre 3 y 120 caracteres.',
            $returnRoute,
            $parameters
        );
    }

    if (
        !array_key_exists(
            $pointType,
            self::POINT_TYPES
        )
    ) {
        $this->failure(
            'Selecciona un tipo de punto válido.',
            $returnRoute,
            $parameters
        );
    }

    if (!$this->validLength($address, 5, 200)) {
        $this->failure(
            'La dirección debe contener entre 5 y 200 caracteres.',
            $returnRoute,
            $parameters
        );
    }

    if (
        $municipality !== ''
        && !$this->validLength($municipality, 2, 100)
    ) {
        $this->failure(
            'El municipio no es válido.',
            $returnRoute,
            $parameters
        );
    }

    if (
        $region !== ''
        && !$this->validLength($region, 2, 100)
    ) {
        $this->failure(
            'El estado o región no es válido.',
            $returnRoute,
            $parameters
        );
    }

    if (
        $postalCode !== ''
        && preg_match('/^[0-9]{5}$/', $postalCode) !== 1
    ) {
        $this->failure(
            'El código postal debe contener cinco números.',
            $returnRoute,
            $parameters
        );
    }

    if (
        $phone !== ''
        && preg_match(
            '/^[0-9+\s()\-]{7,20}$/',
            $phone
        ) !== 1
    ) {
        $this->failure(
            'Escribe un teléfono válido.',
            $returnRoute,
            $parameters
        );
    }

    if (
        $email !== ''
        && (
            !filter_var(
                $email,
                FILTER_VALIDATE_EMAIL
            )
            || strlen($email) > 120
        )
    ) {
        $this->failure(
            'Escribe un correo de contacto válido.',
            $returnRoute,
            $parameters
        );
    }

    if (
        !array_key_exists(
            $serviceType,
            self::SERVICE_TYPES
        )
    ) {
        $this->failure(
            'Selecciona un tipo de servicio válido.',
            $returnRoute,
            $parameters
        );
    }

    /*
     | Las coordenadas deben proporcionarse juntas.
     */
    if (
        ($latitude === '')
        !== ($longitude === '')
    ) {
        $this->failure(
            'Escribe tanto la latitud como la longitud.',
            $returnRoute,
            $parameters
        );
    }

    if ($latitude !== '') {
        if (
            !is_numeric($latitude)
            || (float) $latitude < -90
            || (float) $latitude > 90
        ) {
            $this->failure(
                'La latitud debe estar entre -90 y 90.',
                $returnRoute,
                $parameters
            );
        }

        if (
            !is_numeric($longitude)
            || (float) $longitude < -180
            || (float) $longitude > 180
        ) {
            $this->failure(
                'La longitud debe estar entre -180 y 180.',
                $returnRoute,
                $parameters
            );
        }
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
        'tipo_punto' => $pointType,
        'direccion' => $address,
        'municipio' => $municipality !== ''
            ? $municipality
            : null,
        'estado_region' => $region !== ''
            ? $region
            : 'Morelos',
        'codigo_postal' => $postalCode !== ''
            ? $postalCode
            : null,
        'telefono' => $phone !== ''
            ? $phone
            : null,
        'correo_contacto' => $email !== ''
            ? $email
            : null,
        'tipo_servicio' => $serviceType,
        'latitud' => $latitude !== ''
            ? number_format(
                (float) $latitude,
                7,
                '.',
                ''
            )
            : null,
        'longitud' => $longitude !== ''
            ? number_format(
                (float) $longitude,
                7,
                '.',
                ''
            )
            : null,
        'descripcion' => $description !== ''
            ? $description
            : null,
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