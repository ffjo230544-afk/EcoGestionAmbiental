<?php
declare(strict_types=1);

final class TarifaController extends Controller
{
    /**
     * Muestra las tarifas registradas.
     */
    public function index(): void
    {
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

        $model = new ReglaCotizacion();

        $categories = $model->availableCategories();

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
                'categoryId' => $categoryId,
                'status' => $status,
            ],
            'app'
        );
    }

    /**
     * Muestra el formulario de registro.
     */
    public function create(): void
    {
        $this->form(
            [],
            false
        );
    }

    /**
     * Guarda una tarifa nueva.
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
     * Muestra el formulario de edición.
     */
    public function edit(): void
    {
        $id = max(
            0,
            (int) ($_GET['id'] ?? 0)
        );

        $record = (new ReglaCotizacion())->find(
            $id
        );

        if ($record === null) {
            $this->failure(
                'No se encontró la tarifa solicitada.',
                'tarifas'
            );
        }

        $this->form(
            $record,
            true
        );
    }

    /**
     * Actualiza una tarifa existente.
     */
    public function update(): void
    {
        $id = max(
            0,
            (int) ($_POST['id_regla'] ?? 0)
        );

        $model = new ReglaCotizacion();

        if ($model->find($id) === null) {
            $this->failure(
                'No se encontró la tarifa solicitada.',
                'tarifas'
            );
        }

        $data = $this->validated(
            $model,
            true,
            $id
        );

        $model->update(
            $id,
            $data
        );

        Security::audit(
            'tarifa_actualizada',
            ['id_regla' => $id]
        );

        $this->success(
            'La tarifa se actualizó correctamente.',
            'tarifas'
        );
    }

    /**
     * Activa o desactiva una tarifa.
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
        $record = $model->find($id);

        if ($record === null) {
            $this->failure(
                'No se encontró la tarifa solicitada.',
                'tarifas'
            );
        }

        if ($status === 'activa') {
            $categoryId = (int) (
                $record['id_categoria']
            );

            if (
                !$model->categoryAvailable(
                    $categoryId
                )
            ) {
                $this->failure(
                    'La categoría ya no está disponible '
                    . 'para cotización.',
                    'tarifas'
                );
            }

            if (
                $model->hasOverlappingRate(
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
                    . 'otra tarifa activa que coincide '
                    . 'con ese periodo.',
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
     * Renderiza el formulario compartido.
     */
    private function form(
        array $record,
        bool $editing
    ): void {
        $categories = (
            new ReglaCotizacion()
        )->availableCategories();

        $this->view(
            'rates/form',
            [
                'pageTitle' => $editing
                    ? 'Editar tarifa'
                    : 'Registrar tarifa',
                'record' => $record,
                'editing' => $editing,
                'categories' => $categories,
            ],
            'app'
        );
    }

    /**
     * Valida la información del formulario.
     */
    private function validated(
        ReglaCotizacion $model,
        bool $editing,
        int $id = 0
    ): array {
        $back = $editing
            ? 'tarifas/editar'
            : 'tarifas/crear';

        $parameters = $editing
            ? ['id' => $id]
            : [];

        $categoryId = max(
            0,
            (int) ($_POST['id_categoria'] ?? 0)
        );

        if (
            !$model->categoryAvailable(
                $categoryId
            )
        ) {
            $this->failure(
                'Selecciona una categoría activa '
                . 'y disponible para cotización.',
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
                'Los precios deben contener '
                . 'valores numéricos válidos.',
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
                'Registra al menos un precio mayor '
                . 'que cero por kilogramo o por pieza.',
                $back,
                $parameters
            );
        }

        if (
            $priceKg > 99999999.99
            || $pricePiece > 99999999.99
        ) {
            $this->failure(
                'Uno de los precios supera '
                . 'el valor permitido.',
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
                'El peso mínimo no puede ser '
                . 'mayor que el peso máximo.',
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
                'La fecha final no puede ser '
                . 'anterior a la fecha inicial.',
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

        /*
         | Una categoría no debe tener dos tarifas
         | activas durante el mismo periodo.
         */
        if (
            $status === 'activa'
            && $model->hasOverlappingRate(
                $categoryId,
                $startDate,
                $normalizedEnd,
                $id
            )
        ) {
            $this->failure(
                'Ya existe una tarifa activa para '
                . 'esa categoría durante el periodo '
                . 'seleccionado.',
                $back,
                $parameters
            );
        }

        return [
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