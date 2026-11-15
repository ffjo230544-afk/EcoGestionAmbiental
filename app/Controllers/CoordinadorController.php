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
         | El coordinador únicamente puede consultar
         | los datos del centro que tiene asignado.
         */
        $pointId = (int) (
            auth_center_id() ?? 0
        );

        if ($pointId <= 0) {
            deny_access();
        }

        $summary = (
            new ResumenDashboard()
        )->coordinator($pointId);

        $this->view(
            'coordinator/dashboard',
            [
                'pageTitle' =>
                    'Panel del coordinador',

                'modules' => $modules,

                'summary' => $summary,

                'assignedCenterName' =>
                    auth_user()[
                        'nombre_punto_asignado'
                    ] ?? 'Centro asignado',
            ],
            'app'
        );
    }
}