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

        $userId = (int) (
            auth_user()['id_usuario'] ?? 0
        );

        if ($userId <= 0) {
            deny_access();
        }

        /*
         | Aquí consulto solamente la información
         | perteneciente al ciudadano autenticado.
         */
        $summary = (
            new ResumenDashboard()
        )->citizen($userId);

        $this->view(
            'citizen/dashboard',
            [
                'pageTitle' =>
                    'Mi panel ciudadano',

                'modules' => $modules,

                'summary' => $summary,
            ],
            'app'
        );
    }
}