<?php
declare(strict_types=1);

final class ContenidoJuego
{
    private string $directory;

    public function __construct()
    {
        /*
         | Aquí guardo el contenido modificable de los juegos.
         | Esta ubicación se encuentra protegida contra accesos web.
         */
        $this->directory = ROOT_PATH
            . '/storage/game_content';
    }

    /**
     * Combino las definiciones originales con los cambios guardados.
     */
    public function merge(array $defaults): array
    {
        foreach ($defaults as $slug => $definition) {
            if (!is_array($definition)) {
                continue;
            }

            $override = $this->override((string) $slug);

            if ($override !== []) {
                /*
                 | Conservo la configuración técnica original
                 | y reemplazo solamente los campos editados.
                 */
                $defaults[$slug] = array_replace(
                    $definition,
                    $override
                );
            }
        }

        return $defaults;
    }

    /**
     * Obtengo solamente las modificaciones de un juego.
     */
    public function override(string $slug): array
    {
        $file = $this->filePath($slug);

        if (!is_file($file)) {
            return [];
        }

        $json = file_get_contents($file);

        if ($json === false || trim($json) === '') {
            return [];
        }

        try {
            $content = json_decode(
                $json,
                true,
                512,
                JSON_THROW_ON_ERROR
            );
        } catch (JsonException $exception) {
            throw new RuntimeException(
                'El contenido del juego no tiene un formato válido.',
                0,
                $exception
            );
        }

        if (!is_array($content)) {
            throw new RuntimeException(
                'El contenido del juego debe ser un arreglo.'
            );
        }

        return $content;
    }

    /**
     * Guardo las modificaciones de un juego.
     */
    public function save(
        string $slug,
        array $content
    ): void {
        $this->prepareDirectory();

        try {
            $json = json_encode(
                $content,
                JSON_PRETTY_PRINT
                | JSON_UNESCAPED_UNICODE
                | JSON_UNESCAPED_SLASHES
                | JSON_THROW_ON_ERROR
            );
        } catch (JsonException $exception) {
            throw new RuntimeException(
                'No fue posible preparar el contenido del juego.',
                0,
                $exception
            );
        }

        $written = file_put_contents(
            $this->filePath($slug),
            $json . PHP_EOL,
            LOCK_EX
        );

        if ($written === false) {
            throw new RuntimeException(
                'No fue posible guardar el contenido del juego.'
            );
        }
    }

    /**
     * Creo la carpeta cuando se guarda el primer cambio.
     */
    private function prepareDirectory(): void
    {
        if (is_dir($this->directory)) {
            return;
        }

        if (
            !mkdir(
                $this->directory,
                0775,
                true
            )
            && !is_dir($this->directory)
        ) {
            throw new RuntimeException(
                'No fue posible crear la carpeta de los juegos.'
            );
        }
    }

    /**
     * Construyo una ruta segura para cada archivo.
     */
    private function filePath(string $slug): string
    {
        if (
            $slug === ''
            || preg_match(
                '/^[a-z0-9-]+$/',
                $slug
            ) !== 1
        ) {
            throw new InvalidArgumentException(
                'El identificador del juego no es válido.'
            );
        }

        return $this->directory
            . '/'
            . $slug
            . '.json';
    }
}