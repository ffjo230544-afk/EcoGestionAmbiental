<?php
declare(strict_types=1);

final class CoordinadorController extends Controller
{
    public function index(): void
    {
        $menu = menu_items('coordinador');

        $modules = array_values(
            array_filter(
                $menu,
                static fn(array $item): bool =>
                    ($item['target'] ?? '')
                    !== 'inicio-coordinador'
            )
        );

        /*
         | El coordinador solamente puede consultar
         | la información de su centro asignado.
         */
        $pointId = (int) (
            auth_center_id() ?? 0
        );

        if ($pointId <= 0) {
            deny_access();
        }

        $dashboardModel =
            new ResumenDashboard();

        /*
         | Mantengo los indicadores generales
         | correspondientes al centro asignado.
         */
        $summary =
            $dashboardModel->coordinator(
                $pointId
            );

        /*
         | Valido las fechas utilizadas
         | en la consulta del periodo.
         */
        [$dateFrom, $dateTo] =
            $this->dashboardDateRange();

        /*
         | El centro siempre se obtiene de la sesión.
         | No utilizo un centro recibido por la URL.
         */
        $periodSummary =
            $dashboardModel->coordinatorPeriod(
                $pointId,
                $dateFrom,
                $dateTo
            );

        $assignedCenterName = (string) (
            auth_user()[
                'nombre_punto_asignado'
            ] ?? 'Centro asignado'
        );

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
            'coordinator/dashboard',
            [
                'pageTitle' =>
                    'Panel del coordinador',

                'modules' =>
                    $modules,

                'summary' =>
                    $summary,

                'periodSummary' =>
                    $periodSummary,

                'assignedCenterName' =>
                    $assignedCenterName,

                'periodLabel' =>
                    $periodLabel,

                'filters' => [
                    'date_from' =>
                        $dateFrom,

                    'date_to' =>
                        $dateTo,
                ],
            ],
            'app'
        );
    }

    /**
     * Aquí valido el rango de fechas
     * enviado desde el dashboard.
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
     * Si el periodo no es válido,
     * regreso al dashboard del coordinador.
     */
    private function invalidDashboardFilters(
        string $message
    ): never {
        flash(
            'error',
            $message
        );

        redirect('coordinador');
    }
}