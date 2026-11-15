<?php
declare(strict_types=1);

final class CiudadanoController extends Controller
{
    public function index(): void
    {
        $menu = menu_items('ciudadano');

        $modules = array_values(
            array_filter(
                $menu,
                static fn(array $item): bool =>
                    ($item['target'] ?? '')
                    !== 'inicio-ciudadano'
            )
        );

        /*
         | Obtengo el identificador directamente
         | de la sesión del ciudadano.
         */
        $userId = (int) (
            auth_user()['id_usuario'] ?? 0
        );

        if ($userId <= 0) {
            deny_access();
        }

        $dashboardModel =
            new ResumenDashboard();

        /*
         | Mantengo los indicadores personales
         | que ya fueron comprobados.
         */
        $summary =
            $dashboardModel->citizen(
                $userId
            );

        /*
         | Valido las fechas recibidas
         | desde el formulario de filtros.
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
         | Compruebo que el centro seleccionado
         | exista y se encuentre activo.
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

        /*
         | El identificador del ciudadano siempre
         | se obtiene de la sesión y no de la URL.
         */
        $periodSummary =
            $dashboardModel->citizenPeriod(
                $userId,
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
            'citizen/dashboard',
            [
                'pageTitle' =>
                    'Mi panel ciudadano',

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
     * Aquí valido el periodo solicitado
     * por el ciudadano.
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
     * regreso al dashboard ciudadano.
     */
    private function invalidDashboardFilters(
        string $message
    ): never {
        flash(
            'error',
            $message
        );

        redirect('ciudadano');
    }
}