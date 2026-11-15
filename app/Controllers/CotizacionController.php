<?php
declare(strict_types=1);

final class CotizacionController extends Controller
{
    private const STATUS_LABELS = [
        'pendiente' => 'Pendiente',
        'revisada' => 'Revisada',
        'aprobada' => 'Aprobada',
        'rechazada' => 'Rechazada',
        'cancelada' => 'Cancelada',
    ];

    /**
     * Transiciones permitidas para administrador
     * y coordinador.
     */
    private const TRANSITIONS = [
        'pendiente' => [
            'revisada',
            'aprobada',
            'rechazada',
        ],

        'revisada' => [
            'aprobada',
            'rechazada',
        ],

        'aprobada' => [],
        'rechazada' => [],
        'cancelada' => [],
    ];

    /**
     * Muestra las cotizaciones.
     *
     * El ciudadano solamente obtiene sus registros.
     * Los responsables pueden consultar todos.
     */
    public function index(): void
    {
        $search = substr(
            trim((string) ($_GET['buscar'] ?? '')),
            0,
            80
        );

        $status = (string) (
            $_GET['estado'] ?? ''
        );

        if (
            !array_key_exists(
                $status,
                self::STATUS_LABELS
            )
        ) {
            $status = '';
        }

        $canManage = has_role(
            'admin',
            'coordinador'
        );

        $userId = $canManage
            ? null
            : (int) auth_user()['id_usuario'];

        $quotes = (new Cotizacion())->search(
            $search,
            $status,
            $userId
        );

        $this->view(
            'quotes/index',
            [
                'pageTitle' => $canManage
                    ? 'Gestión de cotizaciones'
                    : 'Mis cotizaciones',
                'quotes' => $quotes,
                'search' => $search,
                'status' => $status,
                'statusLabels' =>
                    self::STATUS_LABELS,
                'transitions' =>
                    self::TRANSITIONS,
                'canManage' => $canManage,
            ],
            'app'
        );
    }

    /**
     * Muestra el cotizador del ciudadano.
     */
    public function create(): void
    {
        $rates = (
            new ReglaCotizacion()
        )->currentRates();

        $centers = (
            new PuntoAtencion()
        )->quotationCenters();

        $this->view(
            'quotes/create',
            [
                'pageTitle' =>
                    'Cotizador de materiales',
                'rates' => $rates,
                'centers' => $centers,
                'useQuoteCalculator' => true,
            ],
            'app'
        );
    }

    /**
     * Valida, calcula y guarda una cotización.
     */
    public function store(): void
    {
        $categoryId = max(
            0,
            (int) ($_POST['id_categoria'] ?? 0)
        );

        $unit = (string) (
            $_POST['unidad'] ?? ''
        );

        $quantityRaw = trim(
            (string) ($_POST['cantidad'] ?? '')
        );

        $pointId = max(
            0,
            (int) ($_POST['id_punto'] ?? 0)
        );

        $notes = trim(
            (string) ($_POST['observaciones'] ?? '')
        );

        if (
            !in_array(
                $unit,
                ['kg', 'pieza'],
                true
            )
        ) {
            $this->failure(
                'Selecciona una unidad válida.',
                'cotizador'
            );
        }

        $rate = (
            new ReglaCotizacion()
        )->currentRate($categoryId);

        if ($rate === null) {
            $this->failure(
                'La categoría seleccionada no tiene '
                . 'una tarifa vigente.',
                'cotizador'
            );
        }

        if (
            $quantityRaw === ''
            || !is_numeric($quantityRaw)
        ) {
            $this->failure(
                'Escribe una cantidad válida.',
                'cotizador'
            );
        }

        $weight = null;
        $pieces = null;
        $unitPrice = 0.0;
        $calculationQuantity = 0.0;

        if ($unit === 'kg') {
            $weight = round(
                (float) $quantityRaw,
                2
            );

            if (
                $weight <= 0
                || $weight > 99999999.99
            ) {
                $this->failure(
                    'El peso debe ser mayor que cero.',
                    'cotizador'
                );
            }

            $minimum = $rate['minimo_kg'] !== null
                ? (float) $rate['minimo_kg']
                : null;

            $maximum = $rate['maximo_kg'] !== null
                ? (float) $rate['maximo_kg']
                : null;

            if (
                $minimum !== null
                && $weight < $minimum
            ) {
                $this->failure(
                    'El peso es menor que el mínimo '
                    . 'permitido para esta categoría.',
                    'cotizador'
                );
            }

            if (
                $maximum !== null
                && $weight > $maximum
            ) {
                $this->failure(
                    'El peso supera el máximo permitido '
                    . 'para esta categoría.',
                    'cotizador'
                );
            }

            $unitPrice = (float) (
                $rate['precio_por_kg']
            );

            $calculationQuantity = $weight;
        }

        if ($unit === 'pieza') {
            $validatedPieces = filter_var(
                $quantityRaw,
                FILTER_VALIDATE_INT,
                [
                    'options' => [
                        'min_range' => 1,
                        'max_range' => 1000000,
                    ],
                ]
            );

            if ($validatedPieces === false) {
                $this->failure(
                    'La cantidad de piezas debe ser '
                    . 'un número entero mayor que cero.',
                    'cotizador'
                );
            }

            $pieces = (int) $validatedPieces;

            $unitPrice = (float) (
                $rate['precio_por_pieza']
            );

            $calculationQuantity = $pieces;
        }

        if ($unitPrice <= 0) {
            $this->failure(
                'La categoría seleccionada no tiene '
                . 'un precio vigente para esa unidad.',
                'cotizador'
            );
        }

        /*
         | El centro es opcional. Si el ciudadano
         | selecciona uno, confirmo que esté activo
         | y que compre la categoría.
         */
        if (
            $pointId > 0
            && !(new PuntoAtencion())->acceptsPurchase(
                $pointId,
                $categoryId
            )
        ) {
            $this->failure(
                'El centro seleccionado no está '
                . 'disponible para comprar esa categoría.',
                'cotizador'
            );
        }

        if (strlen($notes) > 300) {
            $this->failure(
                'Las observaciones no pueden superar '
                . 'los 300 caracteres.',
                'cotizador'
            );
        }

        /*
         | El importe se calcula en PHP.
         | Nunca confío en un total enviado por JavaScript.
         */
        $estimatedAmount = round(
            $unitPrice * $calculationQuantity,
            2
        );

        $quoteId = (new Cotizacion())->create([
            'id_usuario' =>
                (int) auth_user()['id_usuario'],

            'id_categoria' => $categoryId,

            'id_punto' => $pointId > 0
                ? $pointId
                : null,

            'peso_kg' => $weight,

            'cantidad_piezas' => $pieces,

            /*
             | Guardo una copia del precio utilizado.
             | Si la tarifa cambia mañana, esta
             | cotización conservará su precio original.
             */
            'precio_unitario' => $unitPrice,

            'monto_estimado' =>
                $estimatedAmount,

            'observaciones' => $notes !== ''
                ? $notes
                : null,

            'estado' => 'pendiente',
        ]);

        Security::audit(
            'cotizacion_creada',
            [
                'id_cotizacion' => $quoteId,
                'id_categoria' => $categoryId,
                'unidad' => $unit,
                'monto_estimado' =>
                    $estimatedAmount,
            ]
        );

        $this->success(
            'La cotización se registró correctamente. '
            . 'El importe mostrado es una estimación.',
            'cotizaciones'
        );
    }

    /**
     * Administrador o coordinador actualizan
     * el estado de una cotización.
     */
    public function changeStatus(): void
    {
        $quoteId = max(
            0,
            (int) ($_POST['id_cotizacion'] ?? 0)
        );

        $newStatus = (string) (
            $_POST['estado'] ?? ''
        );

        $model = new Cotizacion();
        $quote = $model->find($quoteId);

        if ($quote === null) {
            $this->failure(
                'No se encontró la cotización solicitada.',
                'cotizaciones'
            );
        }

        $currentStatus = (string) (
            $quote['estado']
        );

        $allowed = self::TRANSITIONS[
            $currentStatus
        ] ?? [];

        if (
            !in_array(
                $newStatus,
                $allowed,
                true
            )
        ) {
            $this->failure(
                'El cambio de estado solicitado '
                . 'no está permitido.',
                'cotizaciones'
            );
        }

        $model->update(
            $quoteId,
            ['estado' => $newStatus]
        );

        Security::audit(
            'cotizacion_actualizada',
            [
                'id_cotizacion' => $quoteId,
                'estado_anterior' => $currentStatus,
                'estado_nuevo' => $newStatus,
            ]
        );

        $this->success(
            'El estado de la cotización '
            . 'se actualizó correctamente.',
            'cotizaciones'
        );
    }

    /**
     * El ciudadano cancela una cotización propia.
     */
    public function cancel(): void
    {
        $quoteId = max(
            0,
            (int) ($_POST['id_cotizacion'] ?? 0)
        );

        $userId = (int) (
            auth_user()['id_usuario']
        );

        $model = new Cotizacion();

        /*
         | No utilizo solamente el folio.
         | También compruebo que pertenezca
         | al ciudadano autenticado.
         */
        $quote = $model->findOwned(
            $quoteId,
            $userId
        );

        if ($quote === null) {
            $this->failure(
                'No se encontró la cotización solicitada.',
                'cotizaciones'
            );
        }

        if (
            !in_array(
                $quote['estado'],
                ['pendiente', 'revisada'],
                true
            )
        ) {
            $this->failure(
                'Esta cotización ya no puede cancelarse.',
                'cotizaciones'
            );
        }

        $model->update(
            $quoteId,
            ['estado' => 'cancelada']
        );

        Security::audit(
            'cotizacion_cancelada',
            ['id_cotizacion' => $quoteId]
        );

        $this->success(
            'La cotización se canceló correctamente.',
            'cotizaciones'
        );
    }
}