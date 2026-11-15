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
     * Aquí defino los cambios de estado
     * permitidos para una cotización.
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
     * Muestra las cotizaciones correspondientes
     * al usuario que inició sesión.
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

        /*
         | El ciudadano solamente consulta
         | las cotizaciones creadas por él.
         */
        $userId = has_role('ciudadano')
            ? (int) auth_user()['id_usuario']
            : null;

        /*
         | El coordinador solamente consulta
         | las cotizaciones de su centro.
         |
         | El administrador mantiene este valor
         | en null para poder consultar todos.
         */
        $pointId = has_role('coordinador')
            ? auth_center_id()
            : null;

        /*
         | Mantengo esta comprobación como
         | una segunda capa de protección.
         */
        if (
            has_role('coordinador')
            && $pointId === null
        ) {
            deny_access();
        }

        $quotes = (new Cotizacion())->search(
            $search,
            $status,
            $userId,
            $pointId
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
     * Muestra el cotizador para el ciudadano.
     */
    public function create(): void
    {
        /*
         | Obtengo todas las tarifas vigentes.
         | Cada registro ya incluye su centro.
         */
        $rates = (
            new ReglaCotizacion()
        )->currentRates();

        /*
         | Construyo el listado de centros a partir
         | de las tarifas vigentes.
         |
         | De esta manera no muestro un centro
         | que todavía no tenga precios configurados.
         */
        $centersById = [];

        foreach ($rates as $rate) {
            $pointId = (int) $rate['id_punto'];

            if (isset($centersById[$pointId])) {
                continue;
            }

            $centersById[$pointId] = [
                'id_punto' => $pointId,
                'nombre' => $rate['centro'],
                'municipio' => $rate['municipio'],
            ];
        }

        $centers = array_values(
            $centersById
        );

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
        $pointId = max(
            0,
            (int) ($_POST['id_punto'] ?? 0)
        );

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

        $notes = trim(
            (string) ($_POST['observaciones'] ?? '')
        );

        /*
         | El centro ahora es obligatorio porque
         | cada uno maneja sus propias tarifas.
         */
        if ($pointId <= 0) {
            $this->failure(
                'Selecciona el centro donde deseas '
                . 'realizar la cotización.',
                'cotizador'
            );
        }

        if ($categoryId <= 0) {
            $this->failure(
                'Selecciona una categoría válida.',
                'cotizador'
            );
        }

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

        /*
         | Busco la tarifa utilizando el centro
         | y la categoría al mismo tiempo.
         *
         | Este método también comprueba que:
         | - El centro esté activo.
         | - La categoría esté activa.
         | - El centro compre la categoría.
         | - La tarifa esté vigente.
         */
        $rate = (
            new ReglaCotizacion()
        )->currentRate(
            $pointId,
            $categoryId
        );

        if ($rate === null) {
            $this->failure(
                'El centro seleccionado no tiene '
                . 'una tarifa vigente para esa categoría.',
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

        /*
         | Validación para una cotización
         | calculada por kilogramos.
         */
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

            $minimum =
                $rate['minimo_kg'] !== null
                    ? (float) $rate['minimo_kg']
                    : null;

            $maximum =
                $rate['maximo_kg'] !== null
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

        /*
         | Validación para una cotización
         | calculada por piezas.
         */
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
                'El centro seleccionado no tiene '
                . 'un precio disponible para esa unidad.',
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
         | Calculo el importe nuevamente en PHP.
         |
         | No confío en el resultado mostrado
         | por JavaScript porque puede manipularse
         | desde el navegador.
         */
        $estimatedAmount = round(
            $unitPrice * $calculationQuantity,
            2
        );

        $quoteId = (new Cotizacion())->create([
            'id_usuario' =>
                (int) auth_user()['id_usuario'],

            'id_categoria' => $categoryId,

            /*
             | Todas las cotizaciones nuevas
             | quedan relacionadas con un centro.
             */
            'id_punto' => $pointId,

            'peso_kg' => $weight,

            'cantidad_piezas' => $pieces,

            /*
             | Guardo una copia del precio utilizado.
             | Si la tarifa cambia después, esta
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
                'id_punto' => $pointId,
                'id_categoria' => $categoryId,
                'unidad' => $unit,
                'monto_estimado' =>
                    $estimatedAmount,
            ]
        );


       

/*
 | Consulto la cotización completa para mostrar
 | sus datos en el correo de confirmación.
 */
$quoteDetails = (
    new Cotizacion()
)->findDetailed(
    $quoteId
);

$emailSent = false;

if ($quoteDetails !== null) {
    /*
     | Muestro la cantidad utilizando la unidad
     | con la que fue calculada la cotización.
     */
    if ($quoteDetails['peso_kg'] !== null) {
        $quantityLabel = number_format(
            (float) $quoteDetails['peso_kg'],
            2,
            '.',
            ','
        ) . ' kg';
    } else {
        $pieces = (int) (
            $quoteDetails[
                'cantidad_piezas'
            ] ?? 0
        );

        $quantityLabel = $pieces
            . (
                $pieces === 1
                    ? ' pieza'
                    : ' piezas'
            );
    }

    try {
        /*
         | Confirmo al ciudadano que su cotización
         | fue registrada correctamente.
         */
        (new Correo())->sendTransactional(
            (string) $quoteDetails[
                'correo_ciudadano'
            ],
            (string) $quoteDetails[
                'ciudadano'
            ],
            'Cotización registrada #'
                . $quoteId,
            'Recibimos tu cotización',
            'La cotización quedó registrada '
                . 'y se encuentra pendiente de revisión.',
            [
                'Folio' =>
                    '#' . $quoteId,

                'Centro' =>
                    $quoteDetails['centro']
                    ?? 'No especificado',

                'Categoría' =>
                    $quoteDetails['categoria'],

                'Cantidad' =>
                    $quantityLabel,

                'Precio unitario' => money(
                    $quoteDetails[
                        'precio_unitario'
                    ]
                ),

                'Importe estimado' => money(
                    $quoteDetails[
                        'monto_estimado'
                    ]
                ),

                'Estado' =>
                    'Pendiente',
            ],
            absolute_url('cotizaciones'),
            'Consultar mis cotizaciones'
        );

        $emailSent = true;
    } catch (Throwable) {
        /*
         | La cotización permanece registrada aunque
         | el correo no pueda enviarse.
         */
        Security::audit(
            'correo_no_enviado',
            [
                'origen' =>
                    'cotizacion_creada',

                'id_cotizacion' =>
                    $quoteId,
            ]
        );
    }
}

try {
    $notificationModel =
        new Notificacion();

    /*
     | Confirmo al ciudadano dentro del sistema
     | que su cotización quedó registrada.
     */
    $notificationModel->notifyUser(
        (int) auth_user()['id_usuario'],
        [
            'id_cotizacion' => $quoteId,

            'tipo_notificacion' =>
                'actualizacion_cotizacion',

            'titulo' =>
                'Cotización registrada',

            'mensaje' =>
                'La cotización #'
                . $quoteId
                . ' fue registrada con un importe '
                . 'estimado de '
                . money($estimatedAmount)
                . '.',

            'medio' => $emailSent
                ? 'interna_correo'
                : 'interna',
        ]
    );

    /*
     | Notifico al administrador y al coordinador
     | responsable del centro seleccionado.
     */
    $notificationModel->notifyManagers(
        $pointId,
        [
            'id_cotizacion' => $quoteId,

            'tipo_notificacion' =>
                'actualizacion_cotizacion',

            'titulo' =>
                'Nueva cotización recibida',

            'mensaje' =>
                'Se registró la cotización #'
                . $quoteId
                . ' y necesita revisión.',

            'medio' => 'interna',
        ]
    );
} catch (Throwable) {
    /*
     | La cotización permanece registrada aunque
     | no pueda generarse alguna notificación.
     */
    Security::audit(
        'notificacion_no_generada',
        [
            'origen' =>
                'cotizacion_creada',

            'id_cotizacion' =>
                $quoteId,
        ]
    );
}





        $this->success(
            'La cotización se registró correctamente. '
            . 'El importe mostrado es una estimación.',
            'cotizaciones'
        );
    }

    /**
     * El administrador o el coordinador
     * actualizan el estado de una cotización.
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

        /*
         | Aunque el coordinador modificara manualmente
         | el formulario, aquí vuelvo a comprobar
         | que la cotización sea de su centro.
         */
        if (has_role('coordinador')) {
            $assignedPointId = auth_center_id();

            if (
                $assignedPointId === null
                || (int) $quote['id_punto']
                    !== $assignedPointId
            ) {
                Security::audit(
                    'cotizacion_centro_rechazada',
                    [
                        'id_cotizacion' => $quoteId,
                        'id_punto_cotizacion' =>
                            $quote['id_punto'],
                        'id_punto_asignado' =>
                            $assignedPointId,
                    ]
                );

                deny_access();
            }
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
                'id_punto' => $quote['id_punto'],
                'estado_anterior' => $currentStatus,
                'estado_nuevo' => $newStatus,
            ]
        );


    



$statusLabel = self::STATUS_LABELS[
    $newStatus
] ?? ucfirst($newStatus);

/*
 | Consulto nuevamente la cotización para obtener
 | los datos del ciudadano, centro y categoría.
 */
$quoteDetails =
    $model->findDetailed(
        $quoteId
    );

$emailSent = false;

if ($quoteDetails !== null) {
    try {
        /*
         | Informo al ciudadano sobre el nuevo
         | estado de su cotización.
         */
        (new Correo())->sendTransactional(
            (string) $quoteDetails[
                'correo_ciudadano'
            ],
            (string) $quoteDetails[
                'ciudadano'
            ],
            'Actualización de la cotización #'
                . $quoteId,
            'Tu cotización cambió de estado',
            'Se actualizó el estado de tu cotización '
                . 'en EcoGestión Ambiental.',
            [
                'Folio' =>
                    '#' . $quoteId,

                'Centro' =>
                    $quoteDetails['centro']
                    ?? 'No especificado',

                'Categoría' =>
                    $quoteDetails['categoria'],

                'Importe estimado' => money(
                    $quoteDetails[
                        'monto_estimado'
                    ]
                ),

                'Estado anterior' =>
                    self::STATUS_LABELS[
                        $currentStatus
                    ] ?? ucfirst(
                        $currentStatus
                    ),

                'Estado actual' =>
                    $statusLabel,
            ],
            absolute_url('cotizaciones'),
            'Consultar mi cotización'
        );

        $emailSent = true;
    } catch (Throwable) {
        /*
         | El nuevo estado permanece guardado aunque
         | el correo no pueda enviarse.
         */
        Security::audit(
            'correo_no_enviado',
            [
                'origen' =>
                    'cotizacion_actualizada',

                'id_cotizacion' =>
                    $quoteId,
            ]
        );
    }
}

try {
    /*
     | También registro el cambio dentro
     | de la campana del ciudadano.
     */
    (new Notificacion())->notifyUser(
        (int) $quote['id_usuario'],
        [
            'id_cotizacion' => $quoteId,

            'tipo_notificacion' =>
                'actualizacion_cotizacion',

            'titulo' =>
                'Tu cotización cambió de estado',

            'mensaje' =>
                'La cotización #'
                . $quoteId
                . ' ahora se encuentra en estado: '
                . $statusLabel
                . '.',

            'medio' => $emailSent
                ? 'interna_correo'
                : 'interna',
        ]
    );
} catch (Throwable) {
    /*
     | El estado permanece actualizado aunque
     | falle la notificación interna.
     */
    Security::audit(
        'notificacion_no_generada',
        [
            'origen' =>
                'cotizacion_actualizada',

            'id_cotizacion' =>
                $quoteId,
        ]
    );
}







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
         | Además del folio, compruebo que
         | la cotización pertenezca al ciudadano.
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
            [
                'id_cotizacion' => $quoteId,
                'id_punto' => $quote['id_punto'],
            ]
        );

    



/*
 | Consulto los datos completos para confirmar
 | al ciudadano la cancelación.
 */
$quoteDetails =
    $model->findDetailed(
        $quoteId
    );

$emailSent = false;

if ($quoteDetails !== null) {
    try {
        /*
         | Confirmo por correo que la cancelación
         | fue registrada correctamente.
         */
        (new Correo())->sendTransactional(
            (string) $quoteDetails[
                'correo_ciudadano'
            ],
            (string) $quoteDetails[
                'ciudadano'
            ],
            'Cancelación de la cotización #'
                . $quoteId,
            'Tu cotización fue cancelada',
            'La cancelación de tu cotización '
                . 'se registró correctamente.',
            [
                'Folio' =>
                    '#' . $quoteId,

                'Centro' =>
                    $quoteDetails['centro']
                    ?? 'No especificado',

                'Categoría' =>
                    $quoteDetails['categoria'],

                'Importe estimado' => money(
                    $quoteDetails[
                        'monto_estimado'
                    ]
                ),

                'Estado' =>
                    'Cancelada',
            ],
            absolute_url('cotizaciones'),
            'Consultar mis cotizaciones'
        );

        $emailSent = true;
    } catch (Throwable) {
        /*
         | La cancelación permanece guardada aunque
         | no pueda enviarse el correo.
         */
        Security::audit(
            'correo_no_enviado',
            [
                'origen' =>
                    'cotizacion_cancelada',

                'id_cotizacion' =>
                    $quoteId,
            ]
        );
    }
}

try {
    $notificationModel =
        new Notificacion();

    /*
     | Registro la cancelación dentro de las
     | notificaciones del ciudadano.
     */
    $notificationModel->notifyUser(
        $userId,
        [
            'id_cotizacion' => $quoteId,

            'tipo_notificacion' =>
                'actualizacion_cotizacion',

            'titulo' =>
                'Cotización cancelada',

            'mensaje' =>
                'La cotización #'
                . $quoteId
                . ' fue cancelada correctamente.',

            'medio' => $emailSent
                ? 'interna_correo'
                : 'interna',
        ]
    );

    /*
     | Informo al administrador y al coordinador
     | responsable del centro.
     */
    $notificationModel->notifyManagers(
        (int) ($quote['id_punto'] ?? 0) > 0
            ? (int) $quote['id_punto']
            : null,
        [
            'id_cotizacion' => $quoteId,

            'tipo_notificacion' =>
                'actualizacion_cotizacion',

            'titulo' =>
                'Cotización cancelada',

            'mensaje' =>
                'El ciudadano canceló la '
                . 'cotización #'
                . $quoteId
                . '.',

            'medio' => 'interna',
        ]
    );
} catch (Throwable) {
    /*
     | La cancelación permanece guardada aunque
     | falle alguna notificación interna.
     */
    Security::audit(
        'notificacion_no_generada',
        [
            'origen' =>
                'cotizacion_cancelada',

            'id_cotizacion' =>
                $quoteId,
        ]
    );
}






        $this->success(
            'La cotización se canceló correctamente.',
            'cotizaciones'
        );
    }
}