<?php

namespace ZoeEE\Cache;

use ZoeEE\ExceptionHandler\ZOEException;

/**
 * Clase para controlar la cach� del sistema
 *
 * @author julian
 */
class Cache
{
    
    private const EXTENSION = '.cached';

    /**
     * Direcci�n de donde se encuentre el folder del cach�
     *
     * @var string
     */
    private $path;

    /**
     * Nombre de la carpeta del cach�
     *
     * @var string
     */
    private $dir;

    /**
     * Constructor de la clase Cache
     *
     * @param string $path
     *            Direcci�n de donde se encuentre el folder del cach�
     * @param string $dir
     *            Nombre de la carpeta del cach�
     */
    public function __construct(string $path, string $dir)
    {
        $this->path = $path;
        $this->dir = $dir;
    }

    /**
     * Devuelve el path de la cach�
     *
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * Comprueba que un archivo existe en la cach� del sistema
     *
     * @param string $file
     *            Ruta y nombre del archivo
     * @return bool VERDADERO si el archivo existe, de lo contrario FALSO
     */
    public function has(string $file): bool
    {
        return is_file($this->path . $this->dir . $file . self::EXTENSION);
    }

    /**
     * Establece un archivo con un contenido en cach�
     *
     * @param string $file
     *            Ruta y nombre del archivo en cach�
     * @param string $content
     *            Contenido a guardar
     * @throws ZOEException
     */
    public function set(string $file, string $content): void
    {
        $file = $this->path . $this->dir . $file;
        $pos = strrpos($file, DIRECTORY_SEPARATOR);
        if ($pos !== false) {
            $dir = substr($file, 0, $pos+1);
            if (is_dir($dir) === false) {
                mkdir($dir, 0766, true);
            }
        }
        $file = fopen($file . self::EXTENSION, 'w');
        fwrite($file, $content);
        fclose($file);
    }

    /**
     * Obtiene el contenido de un archivo en el cach�
     *
     * @param string $file
     *            Ruta y nombre del archivo
     * @return string Contenido del archivo
     * @throws ZOEException
     */
    public function get(string $file): string
    {
        if ($this->has($file) === false) {
            throw new ZOEException(ZOEException::F0001, 'F0001');
        } else {
            return file_get_contents($this->path . $this->dir . $file . self::EXTENSION);
        }
    }

    /**
     * Borra un archivo del cach�
     *
     * @param string $file
     * @return bool
     * @throws ZOEException
     */
    public function delete(string $file): bool
    {
        if ($this->has($file) === false) {
            throw new ZOEException(ZOEException::F0001, 'F0001');
        } else {
            return unlink($this->path . $this->dir . $file . self::EXTENSION);
        }
    }
}
