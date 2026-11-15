<?php
declare(strict_types=1);

final class TarifaController extends Controller
{
    /**
     * Aquí muestro las tarifas permitidas para
     * el usuario autenticado.
     */
    public function index(): void
    {
        $model = new ReglaCotizacion();

        $isCoordinator = has_role(
            'coordinador'
        );

        /*
         | El administrador puede seleccionar un centro.
         | El coordinador siempre utiliza el asignado
         | en su sesión.
         */
        $pointId = $isCoordinator
            ? (int) (auth_center_id() ?? 0)
            : max(
                0,
                (int) ($_GET['centro'] ?? 0)
            );

        if (
            $isCoordinator
            && $pointId <= 0
        ) {
            deny_access();
        }

        /*
         | Si el administrador escribió manualmente
         | un centro inexistente, elimino el filtro.
         */
        if (
            has_role('admin')
            && $pointId > 0
            && (new PuntoAtencion())->find(
                $pointId
            ) === null
        ) {
            $pointId = 0;
        }

        $categoryId = max(
            0,
            (int) ($_GET['categoria'] ?? 0)
        );

        $status = (string) (
            $_GET['estado'] ?? ''
        );

        if (
            !in_array(
                $status,
                ['', 'activa', 'inactiva'],
                true
            )
        ) {
            $status = '';
        }

        /*
         | Las categorías del filtro también respetan
         | el centro del coordinador.
         */
        $categories = $model->availableCategories(
            $pointId
        );

        $validCategoryIds = array_map(
            'intval',
            array_column(
                $categories,
                'id_categoria'
            )
        );

        if (
            $categoryId > 0
            && !in_array(
                $categoryId,
                $validCategoryIds,
                true
            )
        ) {
            $categoryId = 0;
        }

        $rates = $model->search(
            $pointId,
            $categoryId,
            $status
        );

        $this->view(
            'rates/index',
            [
                'pageTitle' =>
                    'Tarifas de cotización',

                'rates' => $rates,

                'categories' => $categories,

                /*
                 | Solamente el administrador necesita
                 | el catálogo completo de centros.
                 */
                'centers' => has_role('admin')
                    ? $model->centersForFilter()
                    : [],

                'pointId' => $pointId,

                'assignedCenterName' =>
                    auth_user()[
                        'nombre_punto_asignado'
                    ] ?? null,

                'categoryId' => $categoryId,

                'status' => $status,

                'canChooseCenter' =>
                    has_role('admin'),
            ],
            'app'
        );
    }

    /**
     * Aquí muestro el formulario de registro.
     */
    public function create(): void
    {
        $this->form(
            [],
            false
        );
    }

    /**
     * Aquí guardo una tarifa nueva.
     */
    public function store(): void
    {
        $model = new ReglaCotizacion();

        $data = $this->validated(
            $model,
            false
        );

        $rateId = $model->create($data);

        Security::audit(
            'tarifa_creada',
            [
                'id_regla' => $rateId,
                'id_punto' =>
                    $data['id_punto'],
                'id_categoria' =>
                    $data['id_categoria'],
            ]
        );

        $this->success(
            'La tarifa se registró correctamente.',
            'tarifas'
        );
    }

    /**
     * Aquí muestro el formulario de edición.
     */
    public function edit(): void
    {
        $id = max(
            0,
            (int) ($_GET['id'] ?? 0)
        );

        $record = (
            new ReglaCotizacion()
        )->findWithDetails($id);

        if ($record === null) {
            $this->failure(
                'No se encontró la tarifa solicitada.',
                'tarifas'
            );
        }

        /*
         | El administrador puede editar cualquier tarifa.
         | El coordinador solamente puede editar las
         | pertenecientes a su centro.
         */
        require_center_management(
            (int) $record['id_punto']
        );

        $this->form(
            $record,
            true
        );
    }

    /**
     * Aquí actualizo una tarifa existente.
     */
    public function update(): void
    {
        $id = max(
            0,
            (int) ($_POST['id_regla'] ?? 0)
        );

        $model = new ReglaCotizacion();

        $current = $model->findWithDetails($id);

        if ($current === null) {
            $this->failure(
                'No se encontró la tarifa solicitada.',
                'tarifas'
            );
        }

        require_center_management(
            (int) $current['id_punto']
        );

        $data = $this->validated(
            $model,
            true,
            $id,
            $current
        );

        $model->update(
            $id,
            $data
        );

        Security::audit(
            'tarifa_actualizada',
            [
                'id_regla' => $id,
                'id_punto' =>
                    $data['id_punto'],
                'id_categoria' =>
                    $data['id_categoria'],
            ]
        );

        $this->success(
            'La tarifa se actualizó correctamente.',
            'tarifas'
        );
    }

    /**
     * Aquí activo o desactivo una tarifa.
     */
    public function changeStatus(): void
    {
        $id = max(
            0,
            (int) ($_POST['id_regla'] ?? 0)
        );

        $status = (string) (
            $_POST['estado'] ?? ''
        );

        if (
            !in_array(
                $status,
                ['activa', 'inactiva'],
                true
            )
        ) {
            $this->failure(
                'El estado solicitado no es válido.',
                'tarifas'
            );
        }

        $model = new ReglaCotizacion();

        $record = $model->findWithDetails($id);

        if ($record === null) {
            $this->failure(
                'No se encontró la tarifa solicitada.',
                'tarifas'
            );
        }

        require_center_management(
            (int) $record['id_punto']
        );

        /*
         | Antes de activar vuelvo a comprobar
         | el centro, la categoría y las fechas.
         */
        if ($status === 'activa') {
            $pointId = (int) (
                $record['id_punto']
            );

            $categoryId = (int) (
                $record['id_categoria']
            );

            if (
                !$model->categoryAvailableForCenter(
                    $pointId,
                    $categoryId
                )
            ) {
                $this->failure(
                    'El centro ya no compra esta categoría.',
                    'tarifas'
                );
            }

            if (
                $model->hasOverlappingRate(
                    $pointId,
                    $categoryId,
                    (string) $record[
                        'vigencia_inicio'
                    ],
                    $record['vigencia_fin'] !== null
                        ? (string) $record[
                            'vigencia_fin'
                        ]
                        : null,
                    $id
                )
            ) {
                $this->failure(
                    'No se puede activar porque existe '
                    . 'otra tarifa del mismo centro que '
                    . 'coincide con ese periodo.',
                    'tarifas'
                );
            }
        }

        $model->update(
            $id,
            ['estado' => $status]
        );

        Security::audit(
            'tarifa_cambio_estado',
            [
                'id_regla' => $id,
                'id_punto' =>
                    $record['id_punto'],
                'estado' => $status,
            ]
        );

        $this->success(
            $status === 'activa'
                ? 'La tarifa se activó correctamente.'
                : 'La tarifa se desactivó correctamente.',
            'tarifas'
        );
    }

    /**
     * Aquí preparo el formulario compartido
     * de registro y edición.
     */
    private function form(
        array $record,
        bool $editing
    ): void {
        $model = new ReglaCotizacion();

        $isCoordinator = has_role(
            'coordinador'
        );

        /*
         | Durante la edición no permito trasladar
         | una tarifa de un centro a otro.
         */
        if ($editing) {
            $selectedPointId = (int) (
                $record['id_punto'] ?? 0
            );
        } elseif ($isCoordinator) {
            $selectedPointId = (int) (
                auth_center_id() ?? 0
            );
        } else {
            $selectedPointId = max(
                0,
                (int) old('id_punto', 0)
            );
        }

        if (
            $isCoordinator
            && $selectedPointId <= 0
        ) {
            deny_access();
        }

        /*
         | El administrador que registra necesita todas
         | las categorías y la lista de centros donde
         | cada una puede utilizarse.
         */
        if (
            has_role('admin')
            && !$editing
        ) {
            $categories =
                $model->categoriesWithCenters();
        } else {
            $categories =
                $model->availableCategories(
                    $selectedPointId
                );
        }

        $fixedCenterName = null;

        if ($editing) {
            $fixedCenterName =
                $record['centro'] ?? null;
        } elseif ($isCoordinator) {
            $fixedCenterName =
                auth_user()[
                    'nombre_punto_asignado'
                ] ?? null;
        }

        $this->view(
            'rates/form',
            [
                'pageTitle' => $editing
                    ? 'Editar tarifa'
                    : 'Registrar tarifa',

                'record' => $record,

                'editing' => $editing,

                'categories' => $categories,

                'centers' => (
                    has_role('admin')
                    && !$editing
                )
                    ? $model
                        ->activePurchasingCenters()
                    : [],

                'selectedPointId' =>
                    $selectedPointId,

                'fixedCenterName' =>
                    $fixedCenterName,

                'canChooseCenter' =>
                    has_role('admin')
                    && !$editing,
            ],
            'app'
        );
    }

    /**
     * Aquí valido todos los datos recibidos.
     */
    private function validated(
        ReglaCotizacion $model,
        bool $editing,
        int $id = 0,
        array $current = []
    ): array {
        $back = $editing
            ? 'tarifas/editar'
            : 'tarifas/crear';

        $parameters = $editing
            ? ['id' => $id]
            : [];

        /*
         | Si estoy editando conservo el centro original.
         | Esto impide cambiarlo modificando el HTML.
         */
        if ($editing) {
            $pointId = (int) (
                $current['id_punto'] ?? 0
            );
        } elseif (has_role('coordinador')) {
            $pointId = (int) (
                auth_center_id() ?? 0
            );
        } else {
            $pointId = max(
                0,
                (int) ($_POST['id_punto'] ?? 0)
            );
        }

        if ($pointId <= 0) {
            $this->failure(
                'Selecciona el centro al que pertenece la tarifa.',
                $back,
                $parameters
            );
        }

        /*
         | Esta comprobación protege al Coordinador
         | aunque intente cambiar el centro desde HTML.
         */
        require_center_management($pointId);

        $categoryId = max(
            0,
            (int) ($_POST['id_categoria'] ?? 0)
        );

        if (
            !$model->categoryAvailableForCenter(
                $pointId,
                $categoryId
            )
        ) {
            $this->failure(
                'Selecciona una categoría que el centro '
                . 'tenga configurada para compra.',
                $back,
                $parameters
            );
        }

        $priceKgRaw = trim(
            (string) (
                $_POST['precio_por_kg'] ?? ''
            )
        );

        $pricePieceRaw = trim(
            (string) (
                $_POST['precio_por_pieza'] ?? ''
            )
        );

        if (
            !is_numeric($priceKgRaw)
            || !is_numeric($pricePieceRaw)
        ) {
            $this->failure(
                'Los precios deben contener valores numéricos.',
                $back,
                $parameters
            );
        }

        $priceKg = round(
            (float) $priceKgRaw,
            2
        );

        $pricePiece = round(
            (float) $pricePieceRaw,
            2
        );

        if (
            $priceKg < 0
            || $pricePiece < 0
            || (
                $priceKg === 0.0
                && $pricePiece === 0.0
            )
        ) {
            $this->failure(
                'Registra al menos un precio mayor que cero.',
                $back,
                $parameters
            );
        }

        if (
            $priceKg > 99999999.99
            || $pricePiece > 99999999.99
        ) {
            $this->failure(
                'Uno de los precios supera el valor permitido.',
                $back,
                $parameters
            );
        }

        $minimumRaw = trim(
            (string) ($_POST['minimo_kg'] ?? '')
        );

        $maximumRaw = trim(
            (string) ($_POST['maximo_kg'] ?? '')
        );

        if (
            $minimumRaw !== ''
            && (
                !is_numeric($minimumRaw)
                || (float) $minimumRaw < 0
            )
        ) {
            $this->failure(
                'El peso mínimo no es válido.',
                $back,
                $parameters
            );
        }

        if (
            $maximumRaw !== ''
            && (
                !is_numeric($maximumRaw)
                || (float) $maximumRaw < 0
            )
        ) {
            $this->failure(
                'El peso máximo no es válido.',
                $back,
                $parameters
            );
        }

        $minimum = $minimumRaw !== ''
            ? round((float) $minimumRaw, 2)
            : null;

        $maximum = $maximumRaw !== ''
            ? round((float) $maximumRaw, 2)
            : null;

        if (
            $minimum !== null
            && $maximum !== null
            && $minimum > $maximum
        ) {
            $this->failure(
                'El peso mínimo no puede ser mayor '
                . 'que el peso máximo.',
                $back,
                $parameters
            );
        }

        $startDate = trim(
            (string) (
                $_POST['vigencia_inicio'] ?? ''
            )
        );

        $endDate = trim(
            (string) (
                $_POST['vigencia_fin'] ?? ''
            )
        );

        if (!valid_date($startDate)) {
            $this->failure(
                'La fecha inicial no es válida.',
                $back,
                $parameters
            );
        }

        if (
            $endDate !== ''
            && !valid_date($endDate)
        ) {
            $this->failure(
                'La fecha final no es válida.',
                $back,
                $parameters
            );
        }

        if (
            $endDate !== ''
            && $endDate < $startDate
        ) {
            $this->failure(
                'La fecha final no puede ser anterior '
                . 'a la fecha inicial.',
                $back,
                $parameters
            );
        }

        $status = (string) (
            $_POST['estado'] ?? 'activa'
        );

        if (
            !in_array(
                $status,
                ['activa', 'inactiva'],
                true
            )
        ) {
            $this->failure(
                'Selecciona un estado válido.',
                $back,
                $parameters
            );
        }

        $normalizedEnd = $endDate !== ''
            ? $endDate
            : null;

        if (
            $status === 'activa'
            && $model->hasOverlappingRate(
                $pointId,
                $categoryId,
                $startDate,
                $normalizedEnd,
                $id
            )
        ) {
            $this->failure(
                'Ya existe una tarifa activa para '
                . 'esa categoría, centro y periodo.',
                $back,
                $parameters
            );
        }

        return [
            'id_punto' => $pointId,
            'id_categoria' => $categoryId,
            'precio_por_kg' => $priceKg,
            'precio_por_pieza' => $pricePiece,
            'minimo_kg' => $minimum,
            'maximo_kg' => $maximum,
            'vigencia_inicio' => $startDate,
            'vigencia_fin' => $normalizedEnd,
            'estado' => $status,
        ];
    }
}