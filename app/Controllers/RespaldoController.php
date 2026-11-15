<?php
declare(strict_types=1);

final class RespaldoController extends Controller
{
    /**
     * Muestro los respaldos disponibles y
     * los formularios de administración.
     */
    public function index(): void
    {
        $service =
            new RespaldoBaseDatos();

        $backups =
            $service->listBackups();

        $this->view(
            'backups/index',
            [
                'pageTitle' =>
                    'Respaldo y restauración',

                'backups' => $backups,

                'latestBackup' =>
                    $backups[0] ?? null,

                'maxUploadMb' => max(
                    1,
                    (int) config(
                        'backup_max_upload_mb',
                        50
                    )
                ),
            ]
        );
    }

    /**
     * Genero un respaldo manual.
     */
    public function generate(): void
    {
        try {
            $backup = (
                new RespaldoBaseDatos()
            )->createBackup();

            Security::audit(
                'respaldo_generado',
                [
                    'archivo' =>
                        $backup['name'],

                    'tamano' =>
                        $backup['size'],
                ]
            );

            $this->success(
                'El respaldo se generó correctamente: '
                . $backup['name'],
                'respaldos'
            );
        } catch (Throwable $exception) {
            Security::audit(
                'respaldo_no_generado',
                [
                    'tipo_error' =>
                        get_class($exception),
                ]
            );

            $this->failure(
                'No se pudo generar el respaldo. '
                . 'Revisa la configuración de MySQL '
                . 'y el registro backup_errors.log.',
                'respaldos'
            );
        }
    }

    /**
     * Descargo un respaldo validando su ubicación.
     */
    public function download(): never
    {
        try {
            $service =
                new RespaldoBaseDatos();

            $path = $service->backupPath(
                (string) (
                    $_GET['archivo'] ?? ''
                )
            );

            $filename = basename($path);
            $size = filesize($path);

            Security::audit(
                'respaldo_descargado',
                [
                    'archivo' => $filename,
                ]
            );

            while (
                ob_get_level() > 0
            ) {
                ob_end_clean();
            }

            header(
                'Content-Type: application/sql'
            );

            header(
                'Content-Disposition: attachment; '
                . 'filename="'
                . $filename
                . '"'
            );

            header(
                'X-Content-Type-Options: nosniff'
            );

            header(
                'Cache-Control: no-store, '
                . 'no-cache, must-revalidate'
            );

            if ($size !== false) {
                header(
                    'Content-Length: '
                    . $size
                );
            }

            readfile($path);
            exit;
        } catch (Throwable $exception) {
            Security::audit(
                'respaldo_no_descargado',
                [
                    'tipo_error' =>
                        get_class($exception),
                ]
            );

            $this->failure(
                'No se encontró el respaldo solicitado.',
                'respaldos'
            );
        }
    }

    /**
     * Elimino un respaldo confirmado.
     */
    public function delete(): void
    {
        if (
            (string) (
                $_POST['confirmar'] ?? ''
            ) !== '1'
        ) {
            $this->failure(
                'Confirma la eliminación del respaldo.',
                'respaldos'
            );
        }

        $filename = (string) (
            $_POST['archivo'] ?? ''
        );

        try {
            (
                new RespaldoBaseDatos()
            )->deleteBackup(
                $filename
            );

            Security::audit(
                'respaldo_eliminado',
                [
                    'archivo' =>
                        basename($filename),
                ]
            );

            $this->success(
                'El respaldo se eliminó correctamente.',
                'respaldos'
            );
        } catch (Throwable $exception) {
            Security::audit(
                'respaldo_no_eliminado',
                [
                    'tipo_error' =>
                        get_class($exception),
                ]
            );

            $this->failure(
                'No se pudo eliminar el respaldo.',
                'respaldos'
            );
        }
    }

    /**
     * Restauro un respaldo guardado.
     */
    public function restore(): void
    {
        $this->validateRestoration();

        $filename = (string) (
            $_POST['archivo'] ?? ''
        );

        try {
            $result = (
                new RespaldoBaseDatos()
            )->restoreBackup(
                $filename
            );

            $this->completeRestoration(
                $result
            );
        } catch (Throwable $exception) {
            Security::audit(
                'restauracion_fallida',
                [
                    'archivo' =>
                        basename($filename),

                    'tipo_error' =>
                        get_class($exception),
                ]
            );

            $this->failure(
                'No se pudo completar la restauración. '
                . 'La copia preventiva permanece '
                . 'en la lista de respaldos.',
                'respaldos'
            );
        }
    }

    /**
     * Guardo y restauro un respaldo subido.
     */
    public function restoreUploaded(): void
    {
        $this->validateRestoration();

        try {
            $service =
                new RespaldoBaseDatos();

            $filename =
                $service->importUploadedBackup(
                    $_FILES['archivo_sql']
                    ?? []
                );

            Security::audit(
                'respaldo_importado',
                [
                    'archivo' => $filename,
                ]
            );

            $result =
                $service->restoreBackup(
                    $filename
                );

            $this->completeRestoration(
                $result
            );
        } catch (Throwable $exception) {
            Security::audit(
                'restauracion_subida_fallida',
                [
                    'tipo_error' =>
                        get_class($exception),
                ]
            );

            $this->failure(
                'No se pudo validar o restaurar '
                . 'el archivo SQL.',
                'respaldos'
            );
        }
    }

    /**
     * Compruebo la confirmación y la contraseña
     * del administrador autenticado.
     */
    private function validateRestoration(): void
    {
        $confirmation = trim(
            (string) (
                $_POST['confirmacion'] ?? ''
            )
        );

        if ($confirmation !== 'RESTAURAR') {
            $this->failure(
                'Escribe RESTAURAR para confirmar.',
                'respaldos'
            );
        }

        $password = (string) (
            $_POST['password'] ?? ''
        );

        $userId = (int) (
            auth_user()['id_usuario'] ?? 0
        );

        $administrator = (
            new Usuario()
        )->findWithRole(
            $userId
        );

        if (
            $administrator === null
            || $password === ''
            || !password_verify(
                $password,
                (string) $administrator[
                    'password_hash'
                ]
            )
        ) {
            Security::audit(
                'restauracion_contrasena_rechazada'
            );

            $this->failure(
                'La contraseña del administrador '
                . 'no es correcta.',
                'respaldos'
            );
        }
    }

    /**
     * Registro la restauración y cierro la sesión
     * para validar los datos recuperados.
     */
    private function completeRestoration(
        array $result
    ): never {
        Security::audit(
            'base_datos_restaurada',
            [
                'archivo_restaurado' =>
                    $result['restored_name'],

                'respaldo_preventivo' =>
                    $result['safety_name'],
            ]
        );

        $_SESSION = [];

        session_regenerate_id(true);

        flash(
            'success',
            'La base de datos fue restaurada. '
            . 'Inicia sesión nuevamente. '
            . 'Copia preventiva: '
            . $result['safety_name']
        );

        redirect('login');
    }
}