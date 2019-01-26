<?php

/**
 * Copyright 2018 Servicio Nacional de Aprendizaje - SENA
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *
 * PHP version 7.2
 *
 * @category Cache
 * @package  ZoeEE
 * @author   Julian Lasso <jalasso69@misena.edu.co>
 * @license  https://github.com/jalfcolombia/zoe.ee/blob/master/LICENSE Apache2
 * @link     https://github.com/jalfcolombia/zoe.ee
 */

namespace ZoeEE\Cache;

use ZoeEE\ExceptionHandler\ZOEException;

/**
 * Clase para controlar la caché del sistema
 *
 * @category Cache
 * @package  ZoeEE
 * @author   Julian Lasso <jalasso69@misena.edu.co>
 * @license  https://github.com/jalfcolombia/zoe.ee/blob/master/LICENSE Apache2
 * @link     https://github.com/jalfcolombia/zoe.ee
 */
class Cache
{

    /**
     * Nombre de la carpeta del caché
     */
    private const DIR = '.cache' . DIRECTORY_SEPARATOR;

    /**
     * Extención de los archivos en la caché
     */
    private const EXTENSION = '.cache';

    /**
     * Dirección de donde se encuentre el folder del caché
     *
     * @var string
     */
    private $path;

    /**
     * Constructor de la clase Cache
     *
     * @param string $path Dirección de donde se encuentre el folder del caché
     */
    public function __construct(string $path)
    {
        $this->path = $path;
    }

    /**
     * Devuelve el path de la caché
     *
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * Comprueba que un archivo existe en la caché del sistema
     *
     * @param string $file Ruta y nombre del archivo
     *
     * @return bool VERDADERO si el archivo existe, de lo contrario FALSO
     */
    public function has(string $file): bool
    {
        return is_file($this->path . self::DIR . $file . self::EXTENSION);
    }

    /**
     * Establece un archivo con un contenido en caché
     *
     * @param string $file    Ruta y nombre del archivo
     * @param string $content Contenido a guardar
     *
     * @throws ZOEException
     *
     * @return void
     */
    public function set(string $file, string $content): void
    {
        $file = $this->path . self::DIR . $file;
        $pos = strrpos($file, DIRECTORY_SEPARATOR);
        if ($pos !== false) {
            $dir = substr($file, 0, $pos + 1);
            if (is_dir($dir) === false) {
                mkdir($dir, 0766, true);
            }
        }
        $file = fopen($file . self::EXTENSION, 'w');
        fwrite($file, $content);
        fclose($file);
    }

    /**
     * Obtiene el contenido de un archivo en el caché
     *
     * @param string $file Ruta y nombre del archivo
     *
     * @throws ZOEException
     *
     * @return string Contenido del archivo
     */
    public function get(string $file): string
    {
        if ($this->has($file) === false) {
            throw new ZOEException(ZOEException::F0001, 'F0001');
        } else {
            return file_get_contents(
                $this->path . self::DIR . $file . self::EXTENSION
            );
        }
    }

    /**
     * Borra un archivo del caché
     *
     * @param string $file Ruta y nombre del archivo
     *
     * @throws ZOEException
     *
     * @return bool
     */
    public function delete(string $file): bool
    {
        if ($this->has($file) === false) {
            throw new ZOEException(ZOEException::F0001, 'F0001');
        } else {
            return unlink($this->path . self::DIR . $file . self::EXTENSION);
        }
    }
}
