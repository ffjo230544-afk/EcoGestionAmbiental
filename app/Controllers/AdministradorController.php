<?php
declare(strict_types=1);

final class AdministradorController extends Controller
{
    public function index(): void
    {
        $menu = menu_items('admin');

        $modules = array_values(
            array_filter(
                $menu,
                static fn(array $item): bool =>
                    ($item['target'] ?? '')
                    !== 'inicio-panel'
            )
        );

        $dashboardModel =
            new ResumenDashboard();

        /*
         | Conservo los indicadores principales
         | sin alterar las cifras ya validadas.
         */
        $summary =
            $dashboardModel->administrator();

        /*
         | Aquí recibo y valido el rango de fechas
         | utilizado en la consulta histórica.
         */
        [$dateFrom, $dateTo] =
            $this->dashboardDateRange();

        $centers =
            $dashboardModel->activeCenters();

        $validCenterIds = array_map(
            'intval',
            array_column(
                $centers,
                'id_punto'
            )
        );

        $pointId = max(
            0,
            (int) ($_GET['centro'] ?? 0)
        );

        /*
         | Impido consultar un centro inexistente
         | modificando manualmente la dirección.
         */
        if (
            $pointId > 0
            && !in_array(
                $pointId,
                $validCenterIds,
                true
            )
        ) {
            $this->invalidDashboardFilters(
                'El centro seleccionado no está disponible.'
            );
        }

        $periodSummary =
            $dashboardModel->administratorPeriod(
                $dateFrom,
                $dateTo,
                $pointId
            );

        $selectedCenterName =
            'Todos los centros';

        foreach ($centers as $center) {
            if (
                (int) $center['id_punto']
                === $pointId
            ) {
                $selectedCenterName =
                    (string) $center['nombre'];

                break;
            }
        }

        /*
         | Creo una descripción del periodo
         | para mostrarla junto a los resultados.
         */
        $periodLabel = 'Todos los periodos';

        if (
            $dateFrom !== ''
            && $dateTo !== ''
        ) {
            $periodLabel =
                date_mx($dateFrom)
                . ' al '
                . date_mx($dateTo);
        } elseif ($dateFrom !== '') {
            $periodLabel =
                'Desde '
                . date_mx($dateFrom);
        } elseif ($dateTo !== '') {
            $periodLabel =
                'Hasta '
                . date_mx($dateTo);
        }

        $this->view(
            'admin/dashboard',
            [
                'pageTitle' =>
                    'Panel administrativo',

                'modules' =>
                    $modules,

                'summary' =>
                    $summary,

                'periodSummary' =>
                    $periodSummary,

                'centers' =>
                    $centers,

                'filters' => [
                    'date_from' =>
                        $dateFrom,

                    'date_to' =>
                        $dateTo,

                    'point_id' =>
                        $pointId,
                ],

                'selectedCenterName' =>
                    $selectedCenterName,

                'periodLabel' =>
                    $periodLabel,
            ],
            'app'
        );
    }

    /**
     * Aquí valido las fechas enviadas
     * desde el filtro del dashboard.
     */
    private function dashboardDateRange(): array
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
            $this->invalidDashboardFilters(
                'La fecha inicial no es válida.'
            );
        }

        if (
            $dateTo !== ''
            && !valid_date($dateTo)
        ) {
            $this->invalidDashboardFilters(
                'La fecha final no es válida.'
            );
        }

        if (
            $dateFrom !== ''
            && $dateTo !== ''
            && $dateFrom > $dateTo
        ) {
            $this->invalidDashboardFilters(
                'La fecha inicial no puede ser '
                . 'posterior a la fecha final.'
            );
        }

        return [
            $dateFrom,
            $dateTo,
        ];
    }

    /**
     * Si los filtros no son válidos,
     * regreso al dashboard sin conservarlos.
     */
    private function invalidDashboardFilters(
        string $message
    ): never {
        flash(
            'error',
            $message
        );

        redirect('administrador');
    }
}