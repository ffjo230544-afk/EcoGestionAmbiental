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
                    $item['target'] !== 'inicio-coordinador'
            )
        );

        $this->view(
            'coordinator/dashboard',
            [
                'pageTitle' => 'Panel del coordinador',
                'modules' => $modules,
            ],
            'app'
        );
    }
}