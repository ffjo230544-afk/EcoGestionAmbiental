<?php
declare(strict_types=1);

/**
 * Aquí concentro la generación, validación
 * y restauración de respaldos de MySQL.
 */
final class RespaldoBaseDatos
{
    private string $backupDirectory;

    private string $temporaryDirectory;

    public function __construct()
    {
        $this->backupDirectory =
            ROOT_PATH . '/storage/backups';

        $this->temporaryDirectory =
            ROOT_PATH . '/storage/temp';

        $this->ensureDirectory(
            $this->backupDirectory
        );

        $this->ensureDirectory(
            $this->temporaryDirectory
        );
    }

    /**
     * Obtengo los respaldos disponibles.
     */
    public function listBackups(): array
    {
        $paths = glob(
            $this->backupDirectory
            . '/*.sql'
        );

        if ($paths === false) {
            return [];
        }

        $backups = [];

        foreach ($paths as $path) {
            if (!is_file($path)) {
                continue;
            }

            $size = filesize($path);
            $modifiedAt = filemtime($path);

            $backups[] = [
                'name' => basename($path),

                'size' => $size !== false
                    ? $size
                    : 0,

                'size_label' => $this->formatBytes(
                    $size !== false
                        ? $size
                        : 0
                ),

                'modified_at' =>
                    $modifiedAt !== false
                        ? $modifiedAt
                        : 0,

                'modified_label' =>
                    $modifiedAt !== false
                        ? date(
                            'd/m/Y H:i',
                            $modifiedAt
                        )
                        : 'Fecha no disponible',
            ];
        }

        usort(
            $backups,
            static fn(
                array $first,
                array $second
            ): int =>
                $second['modified_at']
                <=> $first['modified_at']
        );

        return $backups;
    }

    /**
     * Genero un respaldo nuevo de la base.
     */
    public function createBackup(
        string $prefix = 'ecogestion'
    ): array {
        $this->assertProcessAvailable();

        $prefix = preg_replace(
            '/[^A-Za-z0-9_-]/',
            '_',
            $prefix
        );

        $prefix = trim(
            (string) $prefix,
            '_'
        );

        if ($prefix === '') {
            $prefix = 'ecogestion';
        }

        $filename = $prefix
            . '_'
            . date('Y-m-d_H-i-s')
            . '_'
            . bin2hex(random_bytes(3))
            . '.sql';

        $destination =
            $this->backupDirectory
            . '/'
            . $filename;

        $credentialsFile =
            $this->createCredentialsFile();

        try {
            $command = [
                (string) config(
                    'mysql_dump_binary'
                ),

                '--defaults-extra-file='
                    . $credentialsFile,

                '--default-character-set='
                    . config(
                        'db_charset',
                        'utf8mb4'
                    ),

                '--single-transaction',
                '--quick',
                '--skip-lock-tables',
                '--add-drop-table',
                '--triggers',

                (string) config('db_name'),
            ];

            $descriptors = [
                0 => ['pipe', 'r'],
                1 => [
                    'file',
                    $destination,
                    'wb',
                ],
                2 => ['pipe', 'w'],
            ];

            $pipes = [];

            $process = @proc_open(
                $command,
                $descriptors,
                $pipes,
                ROOT_PATH,
                null,
                ['bypass_shell' => true]
            );

            if (!is_resource($process)) {
                throw new RuntimeException(
                    'No pude iniciar mysqldump.'
                );
            }

            if (isset($pipes[0])) {
                fclose($pipes[0]);
            }

            $error = isset($pipes[2])
                ? (string) stream_get_contents(
                    $pipes[2]
                )
                : '';

            if (isset($pipes[2])) {
                fclose($pipes[2]);
            }

            $exitCode = proc_close(
                $process
            );

            if (
                $exitCode !== 0
                || !is_file($destination)
                || filesize($destination) === 0
            ) {
                if (is_file($destination)) {
                    @unlink($destination);
                }

                $this->logProcessError(
                    'generacion',
                    $error,
                    $exitCode
                );

                throw new RuntimeException(
                    'No se pudo generar el respaldo.'
                );
            }

            $size = filesize($destination);

            return [
                'name' => $filename,

                'path' => $destination,

                'size' => $size !== false
                    ? $size
                    : 0,

                'size_label' => $this->formatBytes(
                    $size !== false
                        ? $size
                        : 0
                ),
            ];
        } finally {
            if (is_file($credentialsFile)) {
                @unlink($credentialsFile);
            }
        }
    }

    /**
     * Guardo y valido un archivo SQL subido.
     */
    public function importUploadedBackup(
        array $file
    ): string {
        $error = (int) (
            $file['error']
            ?? UPLOAD_ERR_NO_FILE
        );

        if ($error !== UPLOAD_ERR_OK) {
            throw new RuntimeException(
                'No se recibió correctamente '
                . 'el archivo SQL.'
            );
        }

        $temporaryPath = (string) (
            $file['tmp_name'] ?? ''
        );

        $originalName = (string) (
            $file['name'] ?? ''
        );

        if (
            $temporaryPath === ''
            || !is_uploaded_file(
                $temporaryPath
            )
        ) {
            throw new RuntimeException(
                'El archivo subido no es válido.'
            );
        }

        if (
            strtolower(
                pathinfo(
                    $originalName,
                    PATHINFO_EXTENSION
                )
            ) !== 'sql'
        ) {
            throw new RuntimeException(
                'Solamente puedo restaurar '
                . 'archivos con extensión .sql.'
            );
        }

        $size = filesize($temporaryPath);

        if (
            $size === false
            || $size <= 0
            || $size > $this->maxUploadBytes()
        ) {
            throw new RuntimeException(
                'El archivo SQL está vacío '
                . 'o supera el tamaño permitido.'
            );
        }

        $filename = 'importado_'
            . date('Y-m-d_H-i-s')
            . '_'
            . bin2hex(random_bytes(3))
            . '.sql';

        $destination =
            $this->backupDirectory
            . '/'
            . $filename;

        if (
            !move_uploaded_file(
                $temporaryPath,
                $destination
            )
        ) {
            throw new RuntimeException(
                'No pude guardar el archivo SQL.'
            );
        }

        try {
            $this->validateSqlFile(
                $destination
            );
        } catch (Throwable $exception) {
            @unlink($destination);
            throw $exception;
        }

        return $filename;
    }

    /**
     * Restauro un respaldo y genero antes
     * una copia preventiva de la base actual.
     */
    public function restoreBackup(
        string $filename
    ): array {
        $this->assertProcessAvailable();

        $source = $this->backupPath(
            $filename
        );

        $this->validateSqlFile(
            $source
        );

        /*
         | Conservo el estado actual antes de ejecutar
         | cualquier instrucción del respaldo.
         */
        $safetyBackup =
            $this->createBackup(
                'antes_restauracion'
            );

        $credentialsFile =
            $this->createCredentialsFile();

        try {
            $command = [
                (string) config(
                    'mysql_client_binary'
                ),

                '--defaults-extra-file='
                    . $credentialsFile,

                '--default-character-set='
                    . config(
                        'db_charset',
                        'utf8mb4'
                    ),

                (string) config('db_name'),
            ];

            $descriptors = [
                0 => [
                    'file',
                    $source,
                    'rb',
                ],
                1 => ['pipe', 'w'],
                2 => ['pipe', 'w'],
            ];

            $pipes = [];

            $process = @proc_open(
                $command,
                $descriptors,
                $pipes,
                ROOT_PATH,
                null,
                ['bypass_shell' => true]
            );

            if (!is_resource($process)) {
                throw new RuntimeException(
                    'No pude iniciar el cliente MySQL.'
                );
            }

            $output = isset($pipes[1])
                ? (string) stream_get_contents(
                    $pipes[1]
                )
                : '';

            $error = isset($pipes[2])
                ? (string) stream_get_contents(
                    $pipes[2]
                )
                : '';

            if (isset($pipes[1])) {
                fclose($pipes[1]);
            }

            if (isset($pipes[2])) {
                fclose($pipes[2]);
            }

            $exitCode = proc_close(
                $process
            );

            if ($exitCode !== 0) {
                $this->logProcessError(
                    'restauracion',
                    $error . PHP_EOL . $output,
                    $exitCode
                );

                throw new RuntimeException(
                    'MySQL no pudo completar '
                    . 'la restauración.'
                );
            }

            return [
                'restored_name' =>
                    basename($source),

                'safety_name' =>
                    $safetyBackup['name'],
            ];
        } finally {
            if (is_file($credentialsFile)) {
                @unlink($credentialsFile);
            }
        }
    }

    /**
     * Elimino un respaldo seleccionado.
     */
    public function deleteBackup(
        string $filename
    ): void {
        $path = $this->backupPath(
            $filename
        );

        if (!@unlink($path)) {
            throw new RuntimeException(
                'No pude eliminar el respaldo.'
            );
        }
    }

    /**
     * Resuelvo de manera segura la ubicación
     * de un archivo de respaldo.
     */
    public function backupPath(
        string $filename
    ): string {
        $filename = basename(
            trim($filename)
        );

        if (
            $filename === ''
            || !preg_match(
                '/^[A-Za-z0-9._-]+\.sql$/',
                $filename
            )
        ) {
            throw new RuntimeException(
                'El nombre del respaldo no es válido.'
            );
        }

        $path = realpath(
            $this->backupDirectory
            . '/'
            . $filename
        );

        $directory = realpath(
            $this->backupDirectory
        );

        if (
            $path === false
            || $directory === false
            || dirname($path) !== $directory
            || !is_file($path)
        ) {
            throw new RuntimeException(
                'No se encontró el respaldo solicitado.'
            );
        }

        return $path;
    }

    /**
     * Obtengo el límite permitido para archivos SQL.
     */
    public function maxUploadBytes(): int
    {
        return max(
            1,
            (int) config(
                'backup_max_upload_mb',
                50
            )
        ) * 1024 * 1024;
    }

    /**
     * Compruebo que el archivo sea SQL y que no incluya
     * operaciones administrativas del servidor.
     */
    private function validateSqlFile(
        string $path
    ): void {
        $size = filesize($path);

        if (
            $size === false
            || $size <= 0
            || $size > $this->maxUploadBytes()
        ) {
            throw new RuntimeException(
                'El respaldo está vacío o supera '
                . 'el tamaño permitido.'
            );
        }

        $handle = fopen(
            $path,
            'rb'
        );

        if ($handle === false) {
            throw new RuntimeException(
                'No pude revisar el archivo SQL.'
            );
        }

        try {
            while (
                ($line = fgets($handle))
                !== false
            ) {
                if (str_contains($line, "\0")) {
                    throw new RuntimeException(
                        'El archivo contiene datos '
                        . 'binarios no permitidos.'
                    );
                }

                $instruction = ltrim(
                    $line
                );

                if (
                    preg_match(
                        '/^(?:CREATE|DROP|ALTER)'
                        . '\s+DATABASE\b/i',
                        $instruction
                    )
                    || preg_match(
                        '/^USE\s+/i',
                        $instruction
                    )
                    || preg_match(
                        '/^(?:GRANT|REVOKE)\s+/i',
                        $instruction
                    )
                    || preg_match(
                        '/^(?:CREATE|DROP)\s+USER\b/i',
                        $instruction
                    )
                    || preg_match(
                        '/^LOAD\s+DATA\b/i',
                        $instruction
                    )
                    || preg_match(
                        '/^SOURCE\s+/i',
                        $instruction
                    )
                    || preg_match(
                        '/\bINTO\s+'
                        . '(?:OUTFILE|DUMPFILE)\b/i',
                        $instruction
                    )
                ) {
                    throw new RuntimeException(
                        'El archivo contiene una operación '
                        . 'no permitida.'
                    );
                }
            }
        } finally {
            fclose($handle);
        }
    }

    /**
     * Creo un archivo temporal con las credenciales
     * utilizadas por las herramientas de MySQL.
     */
    private function createCredentialsFile(): string
    {
        $path = $this->temporaryDirectory
            . '/mysql_'
            . bin2hex(random_bytes(8))
            . '.cnf';

        $content = '[client]'
            . PHP_EOL

            . 'host='
            . $this->optionValue(
                (string) config('db_host')
            )
            . PHP_EOL

            . 'port='
            . (int) config('db_port')
            . PHP_EOL

            . 'user='
            . $this->optionValue(
                (string) config('db_user')
            )
            . PHP_EOL

            . 'password='
            . $this->optionValue(
                (string) config('db_password')
            )
            . PHP_EOL

            . 'default-character-set='
            . $this->optionValue(
                (string) config(
                    'db_charset',
                    'utf8mb4'
                )
            )
            . PHP_EOL;

        if (
            file_put_contents(
                $path,
                $content,
                LOCK_EX
            ) === false
        ) {
            throw new RuntimeException(
                'No pude preparar la conexión '
                . 'para el respaldo.'
            );
        }

        @chmod($path, 0600);

        return $path;
    }

    /**
     * Escapo un valor antes de escribirlo
     * dentro del archivo temporal de MySQL.
     */
    private function optionValue(
        string $value
    ): string {
        $value = str_replace(
            [
                '\\',
                '"',
                "\r",
                "\n",
            ],
            [
                '\\\\',
                '\\"',
                '',
                '',
            ],
            $value
        );

        return '"' . $value . '"';
    }

    /**
     * Compruebo que PHP permita ejecutar procesos
     * y que existan las herramientas configuradas.
     */
    private function assertProcessAvailable(): void
    {
        if (!function_exists('proc_open')) {
            throw new RuntimeException(
                'PHP no permite ejecutar procesos externos.'
            );
        }

        $dumpBinary = (string) config(
            'mysql_dump_binary'
        );

        $clientBinary = (string) config(
            'mysql_client_binary'
        );

        if (!is_file($dumpBinary)) {
            throw new RuntimeException(
                'No se encontró mysqldump.'
            );
        }

        if (!is_file($clientBinary)) {
            throw new RuntimeException(
                'No se encontró el cliente mysql.'
            );
        }
    }

    /**
     * Creo una carpeta protegida cuando todavía
     * no se encuentra disponible.
     */
    private function ensureDirectory(
        string $directory
    ): void {
        if (
            !is_dir($directory)
            && !mkdir(
                $directory,
                0775,
                true
            )
            && !is_dir($directory)
        ) {
            throw new RuntimeException(
                'No pude preparar la carpeta '
                . 'de respaldos.'
            );
        }
    }

    /**
     * Registro los errores técnicos dentro de storage.
     */
    private function logProcessError(
        string $operation,
        string $error,
        int $exitCode
    ): void {
        $entry = [
            'fecha' => date('c'),
            'operacion' => $operation,
            'codigo' => $exitCode,

            'mensaje' => mb_substr(
                trim($error),
                0,
                1500
            ),
        ];

        @file_put_contents(
            ROOT_PATH
            . '/storage/backup_errors.log',

            json_encode(
                $entry,
                JSON_UNESCAPED_UNICODE
                | JSON_UNESCAPED_SLASHES
            )
            . PHP_EOL,

            FILE_APPEND | LOCK_EX
        );
    }

    /**
     * Convierto bytes a una medida legible.
     */
    private function formatBytes(
        int $bytes
    ): string {
        if ($bytes < 1024) {
            return $bytes . ' B';
        }

        if ($bytes < 1024 * 1024) {
            return number_format(
                $bytes / 1024,
                2
            ) . ' KB';
        }

        return number_format(
            $bytes / 1024 / 1024,
            2
        ) . ' MB';
    }
}