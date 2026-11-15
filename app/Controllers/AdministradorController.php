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
                    $item['target'] !== 'inicio-panel'
            )
        );

        $this->view(
            'admin/dashboard',
            [
                'pageTitle' => 'Panel administrativo',
                'modules' => $modules,
            ],
            'app'
        );
    }
}