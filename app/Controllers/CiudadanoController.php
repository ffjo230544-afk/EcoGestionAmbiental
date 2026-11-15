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
                    ($item['target'] ?? '') !== 'inicio-ciudadano'
            )
        );

        $this->view(
            'citizen/dashboard',
            [
                'pageTitle' => 'Mi panel ciudadano',
                'modules' => $modules,
            ],
            'app'
        );
    }
}
