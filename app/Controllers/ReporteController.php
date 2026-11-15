<?php
declare(strict_types=1);

/**
 *REPORTES PDF
 */
use Dompdf\Dompdf;
use Dompdf\Options;



/**
 *REPORTES EXCEL
 */
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

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
 * Aquí decido qué reporte se exportará
 * como archivo de Excel.
 */
public function excel(): void
{
    $reportType = trim(
        (string) ($_GET['tipo'] ?? '')
    );

    /*
     | Por el momento la exportación requerida
     | corresponde al reporte de cotizaciones.
     */
    if ($reportType !== 'cotizaciones') {
        $this->invalidReport();
    }

    $this->quotesExcel();
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
    $_GET['tipo_centro'] ?? ''
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
 * Aquí genero el reporte de cotizaciones
 * en formato Excel.
 */
private function quotesExcel(): never
{
    $reportModel = new Reporte();

    /*
     | Reutilizo la misma validación de fechas
     | que ya utilizan los reportes PDF.
     */
    [$dateFrom, $dateTo] =
        $this->reportDateRange();

    /*
     | Si inició sesión un coordinador, este método
     | siempre devuelve su centro asignado.
     |
     | Aunque modifique manualmente la URL, no podrá
     | exportar información de otro centro.
     */
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

    /*
     | Obtengo las cotizaciones utilizando
     | los mismos filtros del reporte PDF.
     */
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

    $generatedBy = (string) (
        auth_user()['nombre_completo']
        ?? 'Usuario del sistema'
    );

    $centerLabel = $this->centerLabel(
        $pointId,
        $reportModel
    );

    $categoryLabel = $this->categoryLabel(
        $categoryId,
        $reportModel
    );

    $statusLabel = $status === ''
        ? 'Todos'
        : (
            $statusLabels[$status]
            ?? ucfirst($status)
        );

    $totalAmount = 0.0;

    /*
     | Creo el libro y selecciono
     | su primera hoja.
     */
    $spreadsheet = new Spreadsheet();

    $sheet = $spreadsheet->getActiveSheet();

    $sheet->setTitle('Cotizaciones');

    /*
     | Agrego propiedades que pueden consultarse
     | desde la información del archivo.
     */
    $spreadsheet
        ->getProperties()
        ->setCreator($generatedBy)
        ->setLastModifiedBy($generatedBy)
        ->setTitle(
            'Reporte de cotizaciones'
        )
        ->setSubject(
            'Cotizaciones de EcoGestión Ambiental'
        )
        ->setDescription(
            'Reporte generado desde '
            . 'EcoGestión Ambiental.'
        );

    /*
     | Esta función guarda texto de manera explícita.
     | Así un valor que comience con =, +, - o @
     | no será interpretado como fórmula de Excel.
     */
    $setText = static function (
        string $cell,
        mixed $value
    ) use ($sheet): void {
        $sheet->setCellValueExplicit(
            $cell,
            (string) ($value ?? ''),
            DataType::TYPE_STRING
        );
    };

    /* Título principal. */
    $sheet->mergeCells('A1:L1');

    $setText(
        'A1',
        'EcoGestión Ambiental — '
        . 'Reporte de cotizaciones'
    );

    $sheet->mergeCells('A2:L2');

    $setText(
        'A2',
        'Cotizaciones realizadas durante '
        . 'el periodo seleccionado.'
    );

    /*
     | Coloco quién generó el archivo
     | y cuándo fue generado.
     */
    $sheet->mergeCells('A3:F3');
    $sheet->mergeCells('G3:L3');

    $setText(
        'A3',
        'Generado por: ' . $generatedBy
    );

    $setText(
        'G3',
        'Fecha de generación: '
        . date('d/m/Y H:i')
    );

    /* Información de los filtros. */
    $setText('A5', 'Periodo:');

    $sheet->mergeCells('B5:F5');

    $setText(
        'B5',
        $this->periodLabel(
            $dateFrom,
            $dateTo
        )
    );

    $setText('G5', 'Centro:');

    $sheet->mergeCells('H5:L5');

    $setText('H5', $centerLabel);

    $setText('A6', 'Categoría:');

    $sheet->mergeCells('B6:F6');

    $setText('B6', $categoryLabel);

    $setText('G6', 'Estado:');

    $sheet->mergeCells('H6:L6');

    $setText('H6', $statusLabel);

    $setText('A7', 'Registros:');

    $sheet->mergeCells('B7:F7');

    $sheet->setCellValue(
        'B7',
        count($quotes)
    );

    $setText('G7', 'Tipo de archivo:');

    $sheet->mergeCells('H7:L7');

    $setText(
        'H7',
        'Microsoft Excel (.xlsx)'
    );

    /*
     | Encabezados de la tabla.
     */
    $headerRow = 9;

    $headers = [
        'A' => 'Folio',
        'B' => 'Fecha',
        'C' => 'Ciudadano',
        'D' => 'Correo',
        'E' => 'Centro',
        'F' => 'Municipio',
        'G' => 'Categoría',
        'H' => 'Cantidad',
        'I' => 'Unidad',
        'J' => 'Precio unitario',
        'K' => 'Monto estimado',
        'L' => 'Estado',
    ];

    foreach (
        $headers as $column => $label
    ) {
        $setText(
            $column . $headerRow,
            $label
        );
    }

    /*
     | Empiezo a colocar los registros
     | después de los encabezados.
     */
    $currentRow = $headerRow + 1;

    foreach ($quotes as $quote) {
        $amount = (float) (
            $quote['monto_estimado'] ?? 0
        );

        $totalAmount += $amount;

        /*
         | Una cotización puede registrar su cantidad
         | mediante kilogramos o piezas.
         */
        if ($quote['peso_kg'] !== null) {
            $quantity = (float) $quote['peso_kg'];
            $unit = 'kg';
        } else {
            $quantity = (int) (
                $quote['cantidad_piezas'] ?? 0
            );

            $unit = 'pieza(s)';
        }

        $setText(
            'A' . $currentRow,
            '#' . $quote['id_cotizacion']
        );

        /*
         | La fecha se guarda como una fecha real
         | para que Excel pueda ordenarla y filtrarla.
         */
        try {
            $date = new DateTimeImmutable(
                (string) $quote[
                    'fecha_cotizacion'
                ]
            );

            $sheet->setCellValue(
                'B' . $currentRow,
                ExcelDate::PHPToExcel($date)
            );
        } catch (Throwable) {
            $setText(
                'B' . $currentRow,
                (string) $quote[
                    'fecha_cotizacion'
                ]
            );
        }

        $setText(
            'C' . $currentRow,
            $quote['ciudadano']
        );

        $setText(
            'D' . $currentRow,
            $quote['correo_ciudadano']
        );

        $setText(
            'E' . $currentRow,
            $quote['centro']
                ?: 'Sin centro'
        );

        $setText(
            'F' . $currentRow,
            $quote['municipio']
                ?: 'No especificado'
        );

        $setText(
            'G' . $currentRow,
            $quote['categoria']
        );

        $sheet->setCellValue(
            'H' . $currentRow,
            $quantity
        );

        $setText(
            'I' . $currentRow,
            $unit
        );

        $sheet->setCellValue(
            'J' . $currentRow,
            (float) (
                $quote['precio_unitario']
                ?? 0
            )
        );

        $sheet->setCellValue(
            'K' . $currentRow,
            $amount
        );

        $setText(
            'L' . $currentRow,
            $statusLabels[
                $quote['estado']
            ] ?? ucfirst(
                (string) $quote['estado']
            )
        );

        /*
         | Alterno el color de las filas
         | para facilitar la lectura.
         */
        if ($currentRow % 2 === 0) {
            $sheet
                ->getStyle(
                    'A'
                    . $currentRow
                    . ':L'
                    . $currentRow
                )
                ->getFill()
                ->setFillType(
                    Fill::FILL_SOLID
                )
                ->getStartColor()
                ->setRGB('F2F8F4');
        }

        $currentRow++;
    }

    /*
     | Si no existen resultados, dejo una explicación
     | en lugar de una tabla aparentemente vacía.
     */
    if ($quotes === []) {
        $sheet->mergeCells(
            'A'
            . $currentRow
            . ':L'
            . $currentRow
        );

        $setText(
            'A' . $currentRow,
            'No se encontraron cotizaciones '
            . 'con los filtros seleccionados.'
        );

        $sheet
            ->getStyle(
                'A'
                . $currentRow
                . ':L'
                . $currentRow
            )
            ->getAlignment()
            ->setHorizontal(
                Alignment::HORIZONTAL_CENTER
            );

        $currentRow++;
    }

    /*
     | Agrego el monto total estimado al final.
     */
    $summaryRow = $currentRow + 1;

    $sheet->mergeCells(
        'A'
        . $summaryRow
        . ':J'
        . $summaryRow
    );

    $sheet->mergeCells(
        'K'
        . $summaryRow
        . ':L'
        . $summaryRow
    );

    $setText(
        'A' . $summaryRow,
        'Monto estimado total'
    );

    $sheet->setCellValue(
        'K' . $summaryRow,
        $totalAmount
    );

    /*
     | Diseño del encabezado principal.
     */
    $sheet
        ->getStyle('A1:L1')
        ->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 18,
                'color' => [
                    'rgb' => 'FFFFFF',
                ],
            ],

            'fill' => [
                'fillType' =>
                    Fill::FILL_SOLID,

                'startColor' => [
                    'rgb' => '16803C',
                ],
            ],

            'alignment' => [
                'horizontal' =>
                    Alignment::HORIZONTAL_CENTER,

                'vertical' =>
                    Alignment::VERTICAL_CENTER,
            ],
        ]);

    $sheet
        ->getRowDimension(1)
        ->setRowHeight(32);

    $sheet
        ->getStyle('A2:L2')
        ->applyFromArray([
            'font' => [
                'italic' => true,
                'color' => [
                    'rgb' => '607168',
                ],
            ],

            'alignment' => [
                'horizontal' =>
                    Alignment::HORIZONTAL_CENTER,
            ],
        ]);

    $sheet
        ->getStyle('A3:L3')
        ->applyFromArray([
            'font' => [
                'size' => 10,
                'color' => [
                    'rgb' => '405249',
                ],
            ],

            'alignment' => [
                'horizontal' =>
                    Alignment::HORIZONTAL_CENTER,
            ],
        ]);

    /*
     | Diseño del bloque de filtros.
     */
    $sheet
        ->getStyle('A5:L7')
        ->applyFromArray([
            'fill' => [
                'fillType' =>
                    Fill::FILL_SOLID,

                'startColor' => [
                    'rgb' => 'F4F8F5',
                ],
            ],

            'borders' => [
                'allBorders' => [
                    'borderStyle' =>
                        Border::BORDER_THIN,

                    'color' => [
                        'rgb' => 'D8E5DC',
                    ],
                ],
            ],
        ]);

    $sheet
        ->getStyle('A5:A7')
        ->getFont()
        ->setBold(true);

    $sheet
        ->getStyle('G5:G7')
        ->getFont()
        ->setBold(true);

    /*
     | Diseño de los encabezados de la tabla.
     */
    $sheet
        ->getStyle(
            'A'
            . $headerRow
            . ':L'
            . $headerRow
        )
        ->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => [
                    'rgb' => 'FFFFFF',
                ],
            ],

            'fill' => [
                'fillType' =>
                    Fill::FILL_SOLID,

                'startColor' => [
                    'rgb' => '1E6E3C',
                ],
            ],

            'alignment' => [
                'horizontal' =>
                    Alignment::HORIZONTAL_CENTER,

                'vertical' =>
                    Alignment::VERTICAL_CENTER,

                'wrapText' => true,
            ],

            'borders' => [
                'allBorders' => [
                    'borderStyle' =>
                        Border::BORDER_THIN,

                    'color' => [
                        'rgb' => 'C8D8CD',
                    ],
                ],
            ],
        ]);

    $sheet
        ->getRowDimension($headerRow)
        ->setRowHeight(30);

    /*
     | Diseño de los registros encontrados.
     */
    $lastDataRow = $quotes !== []
        ? $currentRow - 1
        : $headerRow;

    if ($quotes !== []) {
        $sheet
            ->getStyle(
                'A'
                . ($headerRow + 1)
                . ':L'
                . $lastDataRow
            )
            ->applyFromArray([
                'alignment' => [
                    'vertical' =>
                        Alignment::VERTICAL_CENTER,
                ],

                'borders' => [
                    'allBorders' => [
                        'borderStyle' =>
                            Border::BORDER_THIN,

                        'color' => [
                            'rgb' => 'DDE7E0',
                        ],
                    ],
                ],
            ]);

        $sheet
            ->getStyle(
                'C'
                . ($headerRow + 1)
                . ':G'
                . $lastDataRow
            )
            ->getAlignment()
            ->setWrapText(true);

        /*
         | Formatos para fecha, cantidades
         | y valores monetarios.
         */
        $sheet
            ->getStyle(
                'B'
                . ($headerRow + 1)
                . ':B'
                . $lastDataRow
            )
            ->getNumberFormat()
            ->setFormatCode(
                'dd/mm/yyyy hh:mm'
            );

        $sheet
            ->getStyle(
                'H'
                . ($headerRow + 1)
                . ':H'
                . $lastDataRow
            )
            ->getNumberFormat()
            ->setFormatCode('0.00');

        $sheet
            ->getStyle(
                'J'
                . ($headerRow + 1)
                . ':K'
                . $lastDataRow
            )
            ->getNumberFormat()
            ->setFormatCode(
                '$#,##0.00'
            );
    }

    /*
     | Diseño del total final.
     */
    $sheet
        ->getStyle(
            'A'
            . $summaryRow
            . ':L'
            . $summaryRow
        )
        ->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => [
                    'rgb' => '173F27',
                ],
            ],

            'fill' => [
                'fillType' =>
                    Fill::FILL_SOLID,

                'startColor' => [
                    'rgb' => 'DFF3E4',
                ],
            ],

            'borders' => [
                'allBorders' => [
                    'borderStyle' =>
                        Border::BORDER_THIN,

                    'color' => [
                        'rgb' => 'AFCFB8',
                    ],
                ],
            ],

            'alignment' => [
                'vertical' =>
                    Alignment::VERTICAL_CENTER,
            ],
        ]);

    $sheet
        ->getStyle(
            'A' . $summaryRow
        )
        ->getAlignment()
        ->setHorizontal(
            Alignment::HORIZONTAL_RIGHT
        );

    $sheet
        ->getStyle(
            'K' . $summaryRow
        )
        ->getNumberFormat()
        ->setFormatCode('$#,##0.00');

    /*
     | Defino anchos adecuados para evitar
     | que la información se vea amontonada.
     */
    $columnWidths = [
        'A' => 12,
        'B' => 20,
        'C' => 28,
        'D' => 31,
        'E' => 28,
        'F' => 20,
        'G' => 24,
        'H' => 14,
        'I' => 13,
        'J' => 18,
        'K' => 18,
        'L' => 16,
    ];

    foreach (
        $columnWidths
        as $column => $width
    ) {
        $sheet
            ->getColumnDimension($column)
            ->setWidth($width);
    }

    /*
     | Mantengo visibles los encabezados
     | cuando el usuario desplaza la hoja.
     */
    $sheet->freezePane(
        'A' . ($headerRow + 1)
    );

    /*
     | Excel permitirá filtrar cada columna.
     */
    $sheet->setAutoFilter(
        'A'
        . $headerRow
        . ':L'
        . $lastDataRow
    );

    /*
     | Configuro la impresión horizontal
     | por si después se imprime la hoja.
     */
    $sheet
        ->getPageSetup()
        ->setOrientation(
            PageSetup::ORIENTATION_LANDSCAPE
        );

    $sheet
        ->getPageSetup()
        ->setFitToWidth(1);

    $sheet
        ->getPageSetup()
        ->setFitToHeight(0);

    $sheet
        ->getPageMargins()
        ->setTop(0.4);

    $sheet
        ->getPageMargins()
        ->setBottom(0.4);

    $sheet
        ->getPageMargins()
        ->setLeft(0.3);

    $sheet
        ->getPageMargins()
        ->setRight(0.3);

    /*
     | Registro la exportación en la bitácora.
     */
    Security::audit(
        'reporte_excel_cotizaciones',
        [
            'centro' => $pointId,
            'categoria' => $categoryId,
            'estado' => $status,
            'desde' => $dateFrom,
            'hasta' => $dateTo,
            'registros' => count($quotes),
        ]
    );

    $filename =
        'reporte_cotizaciones_'
        . date('Ymd_His')
        . '.xlsx';

    /*
     | Elimino cualquier salida previa.
     | Un espacio o advertencia antes del archivo
     | podría provocar que Excel lo detecte dañado.
     */
    while (ob_get_level() > 0) {
        ob_end_clean();
    }

    header(
        'Content-Type: '
        . 'application/vnd.openxmlformats-officedocument.'
        . 'spreadsheetml.sheet'
    );

    header(
        'Content-Disposition: attachment; filename="'
        . $filename
        . '"'
    );

    header('Cache-Control: max-age=0');
    header('Pragma: public');
    header('Expires: 0');

    $writer = new Xlsx($spreadsheet);

    $writer->save('php://output');

    /*
     | Libero la memoria utilizada por el libro
     | después de terminar la descarga.
     */
    $spreadsheet->disconnectWorksheets();

    exit;
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
 * que utilizan una tabla reutilizable.
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
    /*
     | pdfView() ya agrega automáticamente la carpeta:
     |
     | app/Views/reports/pdf/
     |
     | Por eso solamente envío el nombre "table".
     */
    $html = $this->pdfView(
        'table',
       [
    'title' => $title,
    'subtitle' => $subtitle,
    'columns' => $columns,
    'rows' => $rows,
    'filters' => $filters,
    'summary' => $summary,

    'logoData' =>
        $this->logoData(),

    /*
     | Aquí identifico al usuario que solicitó
     | la generación del documento.
     */
    'generatedBy' => (string) (
        auth_user()['nombre_completo']
        ?? 'Usuario del sistema'
    ),

    'generatedAt' =>
        date('d/m/Y H:i'),
]
    );

    /*
     | Estos reportes tienen varias columnas.
     | Los genero en orientación horizontal para
     | que la información no quede amontonada.
     */
    $this->streamPdf(
        $html,
        $filename,
        'landscape'
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