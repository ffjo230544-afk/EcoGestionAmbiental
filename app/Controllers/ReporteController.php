<?php
declare(strict_types=1);

use Dompdf\Dompdf;
use Dompdf\Options;

final class ReporteController extends Controller
{
    private const USER_STATUS_LABELS = [
        'activo' => 'Activo',
        'inactivo' => 'Inactivo',
        'bloqueado' => 'Bloqueado',
    ];

    private const CENTER_STATUS_LABELS = [
        'activo' => 'Activo',
        'inactivo' => 'Inactivo',
    ];

    private const CENTER_TYPE_LABELS = [
        'empresa' => 'Empresa',
        'centro_acopio' => 'Centro de acopio',
        'punto_servicio' => 'Punto de servicio',
    ];

    private const SERVICE_TYPE_LABELS = [
        'compra_residuos' =>
            'Compra de residuos',

        'recepcion_reciclables' =>
            'Recepción de reciclables',

        'residuos_electronicos' =>
            'Residuos electrónicos',

        'materiales_toxicos' =>
            'Materiales tóxicos',

        'servicio_general' =>
            'Servicio general',
    ];

  /**
 * Aquí muestro todos los reportes disponibles
 * de acuerdo con el rol autenticado.
 */
public function index(): void
{
    $reportModel = new Reporte();

    /*
     | El administrador no tiene un centro restringido.
     | El coordinador solamente puede utilizar el suyo.
     */
    $restrictedCenterId = has_role('coordinador')
        ? auth_center_id()
        : null;

    if (
        has_role('coordinador')
        && $restrictedCenterId === null
    ) {
        deny_access();
    }

    $this->view(
        'reports/index',
        [
            'pageTitle' =>
                'Reportes y exportaciones',

            'isAdmin' => has_role('admin'),

            'assignedCenterName' => (string) (
                auth_user()['nombre_punto_asignado']
                ?? 'Centro asignado'
            ),

            'roles' => $reportModel->roles(),

            'municipalities' =>
                $reportModel->municipalities(
                    $restrictedCenterId
                ),

            'centers' =>
                $reportModel->centerOptions(
                    $restrictedCenterId
                ),

            'categories' =>
                $reportModel->categoryOptions(),

            /*
             | Utilizo como periodo inicial el mes actual.
             | El usuario puede cambiar o limpiar las fechas.
             */
            'defaultFrom' => date('Y-m-01'),
            'defaultTo' => date('Y-m-d'),
        ]
    );
}
 /**
 * Aquí decido qué reporte PDF se generará.
 */
public function pdf(): void
{
    $reportType = trim(
        (string) ($_GET['tipo'] ?? '')
    );

    match ($reportType) {
        'usuarios' => $this->usersPdf(),
        'centros' => $this->centersPdf(),
        'materiales' => $this->materialsPdf(),
        'tarifas' => $this->ratesPdf(),
        'cotizaciones' => $this->quotesPdf(),
        'citas' => $this->appointmentsPdf(),
        default => $this->invalidReport(),
    };
}





    /**
     * Genero el reporte PDF de usuarios.
     */
    private function usersPdf(): never
    {
        /*
         | El coordinador no puede consultar
         | la lista global de usuarios.
         */
        require_roles(['admin']);

        $roleId = max(
            0,
            (int) ($_GET['rol'] ?? 0)
        );

        $status = (string) (
            $_GET['estado'] ?? ''
        );

        if (
            !array_key_exists(
                $status,
                self::USER_STATUS_LABELS
            )
        ) {
            $status = '';
        }

        $model = new Reporte();
        $roles = $model->roles();

        $roleLabel = 'Todos los roles';

        foreach ($roles as $role) {
            if (
                (int) $role['id_rol']
                === $roleId
            ) {
                $roleLabel = (
                    $role['nombre_rol']
                );

                break;
            }
        }

        $records = $model->users(
            $roleId,
            $status
        );

        $html = $this->pdfView(
            'users',
            [
                'records' => $records,

                'filters' => [
                    'Rol' => $roleLabel,

                    'Estado' => $status !== ''
                        ? self::USER_STATUS_LABELS[
                            $status
                        ]
                        : 'Todos los estados',
                ],

                'generatedBy' =>
                    auth_user()['nombre_completo'],

                'generatedAt' =>
                    date('d/m/Y H:i'),

                'logoData' =>
                    $this->logoData(),
            ]
        );

        Security::audit(
            'reporte_usuarios_generado',
            [
                'id_rol' => $roleId,
                'estado' => $status,
                'registros' => count($records),
            ]
        );

        $this->streamPdf(
            $html,
            'reporte_usuarios_'
            . date('Ymd_His')
            . '.pdf',
            'landscape'
        );
    }

    /**
     * Genero el reporte PDF de centros.
     */
    private function centersPdf(): never
    {
        $restrictedCenterId = has_role(
            'coordinador'
        )
            ? auth_center_id()
            : null;

        $municipality = substr(
            trim(
                (string) (
                    $_GET['municipio'] ?? ''
                )
            ),
            0,
            100
        );

        /*
         | El coordinador no controla el municipio
         | mediante la URL. Su centro se aplica
         | directamente desde la sesión.
         */
        if (has_role('coordinador')) {
            $municipality = '';
        }

        $status = (string) (
            $_GET['estado'] ?? ''
        );

        if (
            !array_key_exists(
                $status,
                self::CENTER_STATUS_LABELS
            )
        ) {
            $status = '';
        }

        $type = (string) (
            $_GET['tipo_punto'] ?? ''
        );

        if (
            !array_key_exists(
                $type,
                self::CENTER_TYPE_LABELS
            )
        ) {
            $type = '';
        }

        $records = (
            new Reporte()
        )->centers(
            $municipality,
            $status,
            $type,
            $restrictedCenterId
        );

        $scope = has_role('coordinador')
            ? (
                auth_user()[
                    'nombre_punto_asignado'
                ]
                ?? 'Centro asignado'
            )
            : 'Todos los centros';

        $html = $this->pdfView(
            'centers',
            [
                'records' => $records,

                'filters' => [
                    'Alcance' => $scope,

                    'Municipio' =>
                        $municipality !== ''
                            ? $municipality
                            : 'Todos',

                    'Tipo' => $type !== ''
                        ? self::CENTER_TYPE_LABELS[
                            $type
                        ]
                        : 'Todos',

                    'Estado' => $status !== ''
                        ? self::CENTER_STATUS_LABELS[
                            $status
                        ]
                        : 'Todos',
                ],

                'typeLabels' =>
                    self::CENTER_TYPE_LABELS,

                'serviceLabels' =>
                    self::SERVICE_TYPE_LABELS,

                'generatedBy' =>
                    auth_user()['nombre_completo'],

                'generatedAt' =>
                    date('d/m/Y H:i'),

                'logoData' =>
                    $this->logoData(),
            ]
        );

        Security::audit(
            'reporte_centros_generado',
            [
                'id_punto_restringido' =>
                    $restrictedCenterId,

                'municipio' => $municipality,
                'tipo_punto' => $type,
                'estado' => $status,
                'registros' => count($records),
            ]
        );

        $this->streamPdf(
            $html,
            'reporte_centros_'
            . date('Ymd_His')
            . '.pdf',
            'landscape'
        );
    }

    /**
     * Cargo una vista especial y regreso
     * su contenido HTML como texto.
     */
    private function pdfView(
        string $view,
        array $data
    ): string {
        $viewFile = ROOT_PATH
            . '/app/Views/reports/pdf/'
            . $view
            . '.php';

        if (!is_file($viewFile)) {
            throw new RuntimeException(
                'No se encontró la plantilla PDF: '
                . $view
                . '.'
            );
        }

        extract($data, EXTR_SKIP);

        $initialBufferLevel = ob_get_level();

        try {
            ob_start();

            require $viewFile;

            return (string) ob_get_clean();
        } catch (Throwable $exception) {
            while (
                ob_get_level()
                > $initialBufferLevel
            ) {
                ob_end_clean();
            }

            throw $exception;
        }
    }

    /**
     * Convierto el HTML en PDF y lo envío
     * directamente al navegador.
     */
    private function streamPdf(
        string $html,
        string $filename,
        string $orientation = 'portrait'
    ): never {
        $options = new Options();

        /*
         | Utilizo una fuente con soporte para
         | acentos y caracteres en español.
         */
        $options->set(
            'defaultFont',
            'DejaVu Sans'
        );

        /*
         | Los reportes no necesitan descargar
         | imágenes o estilos desde sitios externos.
         */
        $options->set(
            'isRemoteEnabled',
            false
        );

        $dompdf = new Dompdf($options);

        $dompdf->loadHtml(
            $html,
            'UTF-8'
        );

        $dompdf->setPaper(
            'letter',
            $orientation
        );

        $dompdf->render();

        $dompdf->stream(
            $filename,
            [
                'Attachment' => true,
            ]
        );

        exit;
    }

    /**
     * Convierto el logotipo local a base64.
     * Así Dompdf no necesita acceder a una URL.
     */
    private function logoData(): string
    {
        $logoFile = ROOT_PATH
            . '/public/images/eco-mark.svg';

        if (!is_file($logoFile)) {
            return '';
        }

        $contents = file_get_contents(
            $logoFile
        );

        if ($contents === false) {
            return '';
        }

        return 'data:image/svg+xml;base64,'
            . base64_encode($contents);
    }

    /**
     * Rechazo nombres de reportes desconocidos.
     */
    private function invalidReport(): never
    {
        flash(
            'error',
            'Selecciona un reporte válido.'
        );

        redirect('reportes');
    }








/**
 * Genera el reporte de materiales aceptados.
 */
private function materialsPdf(): never
{
    $reportModel = new Reporte();

    $pointId = $this->reportCenterId();

    $status = trim(
        (string) (
            $_GET['estado_categoria'] ?? ''
        )
    );

    if (
        !in_array(
            $status,
            ['', 'activo', 'inactivo'],
            true
        )
    ) {
        $status = '';
    }

    $materials = $reportModel->materials(
        $pointId,
        $status
    );

    $typeLabels = [
        'reciclable' => 'Reciclable',
        'reutilizable' => 'Reutilizable',
        'peligroso' => 'Peligroso',
        'especial' => 'Especial',
        'organico' => 'Orgánico',
        'otro' => 'Otro',
    ];

    $rows = array_map(
        static function (
            array $material
        ) use ($typeLabels): array {
            return [
                'centro' =>
                    $material['centro'],

                'municipio' =>
                    $material['municipio']
                    ?: 'No especificado',

                'material' =>
                    $material['categoria'],

                'tipo' =>
                    $typeLabels[
                        $material['tipo_residuo']
                    ] ?? ucfirst(
                        (string) $material[
                            'tipo_residuo'
                        ]
                    ),

                'compra' =>
                    (int) $material[
                        'acepta_compra'
                    ] === 1
                        ? 'Sí'
                        : 'No',

                'recepcion' =>
                    (int) $material[
                        'acepta_recepcion'
                    ] === 1
                        ? 'Sí'
                        : 'No',

                'estado' =>
                    $material['estado_categoria']
                    === 'activo'
                        ? 'Activo'
                        : 'Inactivo',

                'observaciones' =>
                    $material['observaciones']
                    ?: 'Sin observaciones',
            ];
        },
        $materials
    );

    Security::audit(
        'reporte_pdf_materiales',
        [
            'centro' => $pointId,
            'estado' => $status,
        ]
    );

    $this->tablePdf(
        'Materiales aceptados por centro',
        'Relación de materiales que cada centro compra o recibe.',
        [
            'centro' => 'Centro',
            'municipio' => 'Municipio',
            'material' => 'Material',
            'tipo' => 'Tipo',
            'compra' => 'Compra',
            'recepcion' => 'Recepción',
            'estado' => 'Estado',
            'observaciones' => 'Observaciones',
        ],
        $rows,
        [
            'Centro' =>
                $this->centerLabel(
                    $pointId,
                    $reportModel
                ),

            'Estado de categoría' =>
                $status === ''
                    ? 'Todos'
                    : ucfirst($status),
        ],
        [
            'Registros encontrados' =>
                count($materials),
        ],
        'reporte_materiales_'
            . date('Ymd_His')
            . '.pdf'
    );
}

/**
 * Genera el reporte de tarifas.
 */
private function ratesPdf(): never
{
    $reportModel = new Reporte();

    $pointId = $this->reportCenterId();

    $categoryId = max(
        0,
        (int) ($_GET['categoria'] ?? 0)
    );

    $status = trim(
        (string) (
            $_GET['estado_tarifa'] ?? ''
        )
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

    $rates = $reportModel->rates(
        $pointId,
        $categoryId,
        $status
    );

    $rows = array_map(
        static function (array $rate): array {
            $validityEnd =
                $rate['vigencia_fin'] !== null
                && $rate['vigencia_fin'] !== ''
                    ? date_mx(
                        $rate['vigencia_fin']
                    )
                    : 'Sin vencimiento';

            return [
                'centro' => $rate['centro'],
                'categoria' => $rate['categoria'],

                'precio_kg' =>
                    money($rate['precio_por_kg']),

                'precio_pieza' =>
                    money(
                        $rate['precio_por_pieza']
                    ),

                'minimo' =>
                    $rate['minimo_kg'] !== null
                        ? number_format(
                            (float) $rate[
                                'minimo_kg'
                            ],
                            2
                        ) . ' kg'
                        : 'Sin mínimo',

                'maximo' =>
                    $rate['maximo_kg'] !== null
                        ? number_format(
                            (float) $rate[
                                'maximo_kg'
                            ],
                            2
                        ) . ' kg'
                        : 'Sin máximo',

                'vigencia' =>
                    date_mx(
                        $rate['vigencia_inicio']
                    )
                    . ' al '
                    . $validityEnd,

                'estado' =>
                    ucfirst(
                        (string) $rate['estado']
                    ),

                'vigente' =>
                    (int) $rate['vigente_hoy'] === 1
                        ? 'Sí'
                        : 'No',
            ];
        },
        $rates
    );

    Security::audit(
        'reporte_pdf_tarifas',
        [
            'centro' => $pointId,
            'categoria' => $categoryId,
            'estado' => $status,
        ]
    );

    $this->tablePdf(
        'Tarifas de cotización',
        'Precios registrados para los materiales comprados por cada centro.',
        [
            'centro' => 'Centro',
            'categoria' => 'Categoría',
            'precio_kg' => 'Precio/kg',
            'precio_pieza' => 'Precio/pieza',
            'minimo' => 'Mínimo',
            'maximo' => 'Máximo',
            'vigencia' => 'Vigencia',
            'estado' => 'Estado',
            'vigente' => 'Vigente hoy',
        ],
        $rows,
        [
            'Centro' =>
                $this->centerLabel(
                    $pointId,
                    $reportModel
                ),

            'Categoría' =>
                $this->categoryLabel(
                    $categoryId,
                    $reportModel
                ),

            'Estado' =>
                $status === ''
                    ? 'Todos'
                    : ucfirst($status),
        ],
        [
            'Tarifas encontradas' =>
                count($rates),
        ],
        'reporte_tarifas_'
            . date('Ymd_His')
            . '.pdf'
    );
}

/**
 * Genera el reporte de cotizaciones.
 */
private function quotesPdf(): never
{
    $reportModel = new Reporte();

    [$dateFrom, $dateTo] =
        $this->reportDateRange();

    $pointId = $this->reportCenterId();

    $categoryId = max(
        0,
        (int) ($_GET['categoria'] ?? 0)
    );

    $status = trim(
        (string) (
            $_GET['estado_cotizacion'] ?? ''
        )
    );

    $allowedStatuses = [
        '',
        'pendiente',
        'revisada',
        'aprobada',
        'rechazada',
        'cancelada',
    ];

    if (
        !in_array(
            $status,
            $allowedStatuses,
            true
        )
    ) {
        $status = '';
    }

    $quotes = $reportModel->quotes(
        $dateFrom,
        $dateTo,
        $pointId,
        $categoryId,
        $status
    );

    $statusLabels = [
        'pendiente' => 'Pendiente',
        'revisada' => 'Revisada',
        'aprobada' => 'Aprobada',
        'rechazada' => 'Rechazada',
        'cancelada' => 'Cancelada',
    ];

    $totalAmount = 0.0;

    $rows = [];

    foreach ($quotes as $quote) {
        $totalAmount += (float) (
            $quote['monto_estimado'] ?? 0
        );

        $quantity = $quote['peso_kg'] !== null
            ? number_format(
                (float) $quote['peso_kg'],
                2
            ) . ' kg'
            : (int) $quote['cantidad_piezas']
                . ' pieza(s)';

        $rows[] = [
            'folio' =>
                '#' . $quote['id_cotizacion'],

            'fecha' =>
                date_mx(
                    $quote['fecha_cotizacion'],
                    'd/m/Y H:i'
                ),

            'ciudadano' =>
                $quote['ciudadano'],

            'centro' =>
                $quote['centro']
                ?: 'Sin centro',

            'categoria' =>
                $quote['categoria'],

            'cantidad' => $quantity,

            'precio' =>
                money($quote['precio_unitario']),

            'monto' =>
                money($quote['monto_estimado']),

            'estado' =>
                $statusLabels[
                    $quote['estado']
                ] ?? ucfirst(
                    (string) $quote['estado']
                ),
        ];
    }

    Security::audit(
        'reporte_pdf_cotizaciones',
        [
            'centro' => $pointId,
            'categoria' => $categoryId,
            'estado' => $status,
            'desde' => $dateFrom,
            'hasta' => $dateTo,
        ]
    );

    $this->tablePdf(
        'Reporte de cotizaciones',
        'Cotizaciones realizadas durante el periodo seleccionado.',
        [
            'folio' => 'Folio',
            'fecha' => 'Fecha',
            'ciudadano' => 'Ciudadano',
            'centro' => 'Centro',
            'categoria' => 'Material',
            'cantidad' => 'Cantidad',
            'precio' => 'Precio unitario',
            'monto' => 'Monto estimado',
            'estado' => 'Estado',
        ],
        $rows,
        [
            'Periodo' =>
                $this->periodLabel(
                    $dateFrom,
                    $dateTo
                ),

            'Centro' =>
                $this->centerLabel(
                    $pointId,
                    $reportModel
                ),

            'Categoría' =>
                $this->categoryLabel(
                    $categoryId,
                    $reportModel
                ),

            'Estado' =>
                $status === ''
                    ? 'Todos'
                    : (
                        $statusLabels[$status]
                        ?? ucfirst($status)
                    ),
        ],
        [
            'Cotizaciones encontradas' =>
                count($quotes),

            'Monto estimado total' =>
                money($totalAmount),
        ],
        'reporte_cotizaciones_'
            . date('Ymd_His')
            . '.pdf'
    );
}

/**
 * Genera el reporte de citas.
 */
private function appointmentsPdf(): never
{
    $reportModel = new Reporte();

    [$dateFrom, $dateTo] =
        $this->reportDateRange();

    $pointId = $this->reportCenterId();

    $status = trim(
        (string) (
            $_GET['estado_cita'] ?? ''
        )
    );

    $allowedStatuses = [
        '',
        'pendiente',
        'confirmada',
        'atendida',
        'cancelada',
    ];

    if (
        !in_array(
            $status,
            $allowedStatuses,
            true
        )
    ) {
        $status = '';
    }

    $appointments = $reportModel->appointments(
        $dateFrom,
        $dateTo,
        $pointId,
        $status
    );

    $statusLabels = [
        'pendiente' => 'Pendiente',
        'confirmada' => 'Confirmada',
        'atendida' => 'Atendida',
        'cancelada' => 'Cancelada',
    ];

    $reasonLabels = [
        'entrega_reciclables' =>
            'Entrega de reciclables',

        'entrega_electronicos' =>
            'Entrega de electrónicos',

        'consulta_residuos_toxicos' =>
            'Consulta de residuos tóxicos',

        'informacion_servicios' =>
            'Información de servicios',

        'otro' => 'Otro',
    ];

    $rows = array_map(
        static function (
            array $appointment
        ) use (
            $statusLabels,
            $reasonLabels
        ): array {
            return [
                'folio' =>
                    '#' . $appointment['id_cita'],

                'fecha' =>
                    date_mx(
                        $appointment['fecha_cita']
                    ),

                'hora' =>
                    substr(
                        (string) $appointment[
                            'hora_cita'
                        ],
                        0,
                        5
                    ),

                'ciudadano' =>
                    $appointment['ciudadano'],

                'telefono' =>
                    $appointment[
                        'telefono_ciudadano'
                    ]
                    ?: 'No disponible',

                'centro' =>
                    $appointment['centro'],

                'motivo' =>
                    $reasonLabels[
                        $appointment['motivo']
                    ] ?? 'Otro',

                'estado' =>
                    $statusLabels[
                        $appointment['estado']
                    ] ?? ucfirst(
                        (string) $appointment[
                            'estado'
                        ]
                    ),
            ];
        },
        $appointments
    );

    Security::audit(
        'reporte_pdf_citas',
        [
            'centro' => $pointId,
            'estado' => $status,
            'desde' => $dateFrom,
            'hasta' => $dateTo,
        ]
    );

    $this->tablePdf(
        'Reporte de citas',
        'Visitas programadas durante el periodo seleccionado.',
        [
            'folio' => 'Folio',
            'fecha' => 'Fecha',
            'hora' => 'Hora',
            'ciudadano' => 'Ciudadano',
            'telefono' => 'Teléfono',
            'centro' => 'Centro',
            'motivo' => 'Motivo',
            'estado' => 'Estado',
        ],
        $rows,
        [
            'Periodo' =>
                $this->periodLabel(
                    $dateFrom,
                    $dateTo
                ),

            'Centro' =>
                $this->centerLabel(
                    $pointId,
                    $reportModel
                ),

            'Estado' =>
                $status === ''
                    ? 'Todos'
                    : (
                        $statusLabels[$status]
                        ?? ucfirst($status)
                    ),
        ],
        [
            'Citas encontradas' =>
                count($appointments),
        ],
        'reporte_citas_'
            . date('Ymd_His')
            . '.pdf'
    );
}


/**
 * Aquí obtengo el centro permitido para el reporte.
 *
 * Aunque un coordinador modifique manualmente la URL,
 * siempre se utilizará el centro guardado en su sesión.
 */
private function reportCenterId(): int
{
    if (has_role('coordinador')) {
        $centerId = auth_center_id();

        if ($centerId === null) {
            deny_access();
        }

        return $centerId;
    }

    return max(
        0,
        (int) ($_GET['centro'] ?? 0)
    );
}

/**
 * Aquí valido el periodo recibido desde los filtros.
 */
private function reportDateRange(): array
{
    $dateFrom = trim(
        (string) ($_GET['desde'] ?? '')
    );

    $dateTo = trim(
        (string) ($_GET['hasta'] ?? '')
    );

    if (
        $dateFrom !== ''
        && !valid_date($dateFrom)
    ) {
        $this->invalidFilters(
            'La fecha inicial no es válida.'
        );
    }

    if (
        $dateTo !== ''
        && !valid_date($dateTo)
    ) {
        $this->invalidFilters(
            'La fecha final no es válida.'
        );
    }

    if (
        $dateFrom !== ''
        && $dateTo !== ''
        && $dateFrom > $dateTo
    ) {
        $this->invalidFilters(
            'La fecha inicial no puede ser posterior '
            . 'a la fecha final.'
        );
    }

    return [$dateFrom, $dateTo];
}

/**
 * Aquí obtengo el nombre legible del centro.
 */
private function centerLabel(
    int $pointId,
    Reporte $reportModel
): string {
    if ($pointId <= 0) {
        return 'Todos los centros';
    }

    foreach (
        $reportModel->centerOptions(null)
        as $center
    ) {
        if (
            (int) $center['id_punto']
            === $pointId
        ) {
            return (string) $center['nombre'];
        }
    }

    return 'Centro no disponible';
}

/**
 * Aquí obtengo el nombre legible de la categoría.
 */
private function categoryLabel(
    int $categoryId,
    Reporte $reportModel
): string {
    if ($categoryId <= 0) {
        return 'Todas las categorías';
    }

    foreach (
        $reportModel->categoryOptions()
        as $category
    ) {
        if (
            (int) $category['id_categoria']
            === $categoryId
        ) {
            return (string) $category['nombre'];
        }
    }

    return 'Categoría no disponible';
}

/**
 * Convierte el periodo seleccionado en texto.
 */
private function periodLabel(
    string $dateFrom,
    string $dateTo
): string {
    if (
        $dateFrom === ''
        && $dateTo === ''
    ) {
        return 'Todos los registros';
    }

    if ($dateFrom === '') {
        return 'Hasta '
            . date_mx($dateTo);
    }

    if ($dateTo === '') {
        return 'Desde '
            . date_mx($dateFrom);
    }

    return date_mx($dateFrom)
        . ' al '
        . date_mx($dateTo);
}

/**
 * Aquí preparo una vista general para los reportes
 * que utilizan tablas.
 */
private function tablePdf(
    string $title,
    string $subtitle,
    array $columns,
    array $rows,
    array $filters,
    array $summary,
    string $filename
): never {
    $html = $this->pdfView(
        'reports/pdf/table',
        [
            'title' => $title,
            'subtitle' => $subtitle,
            'columns' => $columns,
            'rows' => $rows,
            'filters' => $filters,
            'summary' => $summary,
            'logoData' => $this->logoData(),
            'generatedAt' =>
                date('d/m/Y H:i'),
        ]
    );

    $this->streamPdf(
        $html,
        $filename
    );
}

/**
 * Si los filtros son inválidos, regreso al módulo.
 */
private function invalidFilters(
    string $message
): never {
    flash('error', $message);
    redirect('reportes');
}



}