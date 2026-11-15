<?php
declare(strict_types=1);

final class MapaController extends Controller
{
    private const POINT_TYPES = [
        'empresa' => 'Empresa',

        'centro_acopio' =>
            'Centro de acopio',

        'punto_servicio' =>
            'Punto de servicio',
    ];

    private const SERVICE_TYPES = [
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
     * Muestra el mapa y aplica los filtros.
     */
    public function index(): void
    {
        $search = trim(
            (string) ($_GET['buscar'] ?? '')
        );

        $type = (string) (
            $_GET['tipo'] ?? ''
        );

        $service = (string) (
            $_GET['servicio'] ?? ''
        );

        $status = (string) (
            $_GET['estado'] ?? ''
        );

        $categoryId = max(
            0,
            (int) ($_GET['categoria'] ?? 0)
        );

        /*
         | Solamente permito tipos conocidos.
         */
        if (
            !array_key_exists(
                $type,
                self::POINT_TYPES
            )
        ) {
            $type = '';
        }

        if (
            !array_key_exists(
                $service,
                self::SERVICE_TYPES
            )
        ) {
            $service = '';
        }

        if (
            !in_array(
                $status,
                [
                    '',
                    'activo',
                    'inactivo',
                ],
                true
            )
        ) {
            $status = '';
        }

        /*
         | El ciudadano solamente puede localizar
         | centros que estén activos.
         */
        $onlyActive = has_role('ciudadano');

        $model = new PuntoAtencion();

        $categories = $model->mapCategories();

        /*
         | Compruebo que la categoría recibida
         | realmente exista entre las categorías activas.
         */
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

        $records = $model->forMap(
            $search,
            $type,
            $service,
            $status,
            $categoryId,
            $onlyActive
        );

        /*
         | Preparo exclusivamente la información
         | que necesitará el JavaScript del mapa.
         */
        $points = array_map(
            function (array $record): array {
                return [
                    'id' => (int) (
                        $record['id_punto']
                    ),

                    'nombre' => (string) (
                        $record['nombre']
                    ),

                    'tipo' => (string) (
                        $record['tipo_punto']
                    ),

                    'tipo_texto' => (
                        self::POINT_TYPES[
                            $record['tipo_punto']
                        ]
                        ?? 'Punto de atención'
                    ),

                    'direccion' => (string) (
                        $record['direccion']
                    ),

                    'municipio' => (string) (
                        $record['municipio']
                        ?: 'No especificado'
                    ),

                    'estado_region' => (string) (
                        $record['estado_region']
                        ?: 'Morelos'
                    ),

                    'telefono' => (string) (
                        $record['telefono']
                        ?: 'No disponible'
                    ),

                    'servicio' => (
                        self::SERVICE_TYPES[
                            $record['tipo_servicio']
                        ]
                        ?? 'Servicio ambiental'
                    ),

                    'materiales' => (string) (
                        $record['materiales']
                        ?: 'Sin materiales registrados'
                    ),

                    'total_materiales' => (int) (
                        $record['total_materiales']
                    ),

                    'estado' => (string) (
                        $record['estado']
                    ),

                    'latitud' => (float) (
                        $record['latitud']
                    ),

                    'longitud' => (float) (
                        $record['longitud']
                    ),

                    /*
                     | La dirección del detalle se genera
                     | desde PHP, no desde JavaScript.
                     */
                    'detalle_url' => url(
                        'centros/detalle',
                        [
                            'id' => $record[
                                'id_punto'
                            ],
                        ]
                    ),
                ];
            },
            $records
        );

        $this->view(
            'map/index',
            [
                'pageTitle' =>
                    'Mapa interactivo',

                /*
                 | Esta variable hará que el layout
                 | cargue Leaflet solamente en esta página.
                 */
                'useLeaflet' => true,

                'records' => $records,
                'points' => $points,
                'categories' => $categories,

                'pointTypes' =>
                    self::POINT_TYPES,

                'serviceTypes' =>
                    self::SERVICE_TYPES,

                'search' => $search,
                'type' => $type,
                'service' => $service,
                'status' => $status,
                'categoryId' => $categoryId,

                'canManage' => has_role(
                    'admin',
                    'coordinador'
                ),

                'withoutCoordinates' =>
                    $model->countWithoutCoordinates(
                        $onlyActive
                    ),
            ],
            'app'
        );
    }
}