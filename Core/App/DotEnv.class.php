<?php
/**
 * DotEnv - Environment Variables Loader
 *
 * Carga variables de entorno desde el archivo .env ubicado en la raíz del proyecto.
 * Permite definir configuraciones sensibles (credenciales de base de datos, API keys, etc.)
 * fuera del código fuente.
 *
 * IMPORTANTE:
 * - El archivo .env debe estar en la raíz del proyecto y NO debe ser versionado en Git
 * - Puedes definir tantas variables de entorno como necesites, no solo las del ejemplo
 * - Las variables se cargan en $_ENV, $_SERVER y putenv()
 * - Las líneas que comienzan con # son tratadas como comentarios
 *
 * Formato del archivo .env:
 * ```
 * # Comentario
 * VARIABLE_NAME=valor
 * DB_HOST=localhost
 * DB_USER=usuario
 * API_KEY=tu_clave_secreta
 * ```
 *
 * Uso:
 * ```php
 * $dotenv = new DotEnv(__DIR__ . '/.env');
 * $dotenv->load();
 * $dbHost = $_ENV['DB_HOST'];
 * ```
 *
 * @file DotEnv.class.php
 * @version 1.0.0
 * @author F. Michel
 * @license MIT
 * @copyright 2021 F. Michel
 */

/*
 * MIT License
 *
 * Copyright (c) 2021 F. Michel
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 */

/**
 * Class DotEnv
 *
 * Gestor de variables de entorno desde archivo .env
 *
 * @package Core\App
 */
class DotEnv {

    /**
     * @var string Ruta al archivo .env
     */
    protected $path;

    /**
     * Constructor
     *
     * @param string $path Ruta completa al archivo .env
     * @throws \InvalidArgumentException Si el archivo no existe
     */
    public function __construct(string $path) {
        if (!file_exists($path)) {
            throw new \InvalidArgumentException(sprintf('%s does not exist', $path));
        }
        $this->path = $path;
    }

    /**
     * Carga las variables de entorno del archivo .env
     *
     * Lee el archivo línea por línea, ignora comentarios (líneas que comienzan con #)
     * y carga las variables en $_ENV, $_SERVER y putenv(). No sobrescribe variables
     * que ya existan en el entorno.
     *
     * @return void
     * @throws \RuntimeException Si el archivo no es legible
     */
    public function load(): void {
        if (!is_readable($this->path)) {
            throw new \RuntimeException(sprintf('%s file is not readable', $this->path));
        }
        $lines = file($this->path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            if (strpos(trim($line), '#') === 0) {
                continue;
            }
            list($name, $value) = explode('=', $line, 2);
            $name = trim($name);
            $value = trim($value);
            if (!array_key_exists($name, $_SERVER) && !array_key_exists($name, $_ENV)) {
                putenv(sprintf('%s=%s', $name, $value));
                $_ENV[$name] = $value;
                $_SERVER[$name] = $value;
            }
        }
    }
}
