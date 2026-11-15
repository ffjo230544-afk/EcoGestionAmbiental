<?php
declare(strict_types=1);

final class AdministradorController extends Controller
{
    public function index(): void
    {
        $menu = menu_items('admin');

        /*
         | Aquí elimino del listado la opción principal
         | porque ya corresponde al dashboard actual.
         */
        $modules = array_values(
            array_filter(
                $menu,
                static fn(array $item): bool =>
                    ($item['target'] ?? '')
                    !== 'inicio-panel'
            )
        );

        /*
         | Aquí consulto los indicadores generales
         | que se mostrarán en el dashboard.
         */
        $summary = (
            new ResumenDashboard()
        )->administrator();

        /*
         | Envío los módulos y los indicadores
         | a la vista del administrador.
         */
        $this->view(
            'admin/dashboard',
            [
                'pageTitle' =>
                    'Panel administrativo',

                'modules' =>
                    $modules,

                'summary' =>
                    $summary,
            ],
            'app'
        );
    }
}