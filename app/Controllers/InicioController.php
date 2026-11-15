<?php
declare(strict_types=1);

/** Controlador de la página pública de inicio. */
final class InicioController extends Controller
{
    public function index(): void
    {
        $this->view('public/home', [
            'pageTitle' => 'Reciclaje y educación ambiental en Jiutepec',
        ], 'public');
    }
}
