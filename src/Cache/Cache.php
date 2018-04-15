<?php
namespace ZoeEE\Cache;

use ZoeEE\ExceptionHandler\ZOEException;

/**
 * Clase para controlar la caché del sistema
 *
 * @author julian
 */
class Cache
{

    /**
     * Dirección de donde se encuentre el folder del caché
     *
     * @var string
     */
    private $path;

    /**
     * Nombre de la carpeta del caché
     *
     * @var string
     */
    private $dir;

    /**
     * Constructor de la clase Cache
     *
     * @param string $path
     *            Dirección de donde se encuentre el folder del caché
     * @param string $dir
     *            Nombre de la carpeta del caché
     */
    public function __construct(string $path, string $dir)
    {
        $this->path = $path;
        $this->dir = $dir;
    }

    /**
     * Comprueba que un archivo existe en la caché del sistema
     *
     * @param string $file
     *            Ruta y nombre del archivo
     * @return bool VERDADERO si el archivo existe, de lo contrario FALSO
     */
    public function Has(string $file): bool
    {
        return is_file($this->path . $this->dir . $file);
    }

    /**
     * Establece un archivo con un contenido en caché
     *
     * @param string $file
     *            Ruta y nombre del archivo en caché
     * @param string $content
     *            Contenido a guardar
     * @throws ZOEException
     */
    public function Set(string $file, string $content): void
    {
        if ($this->Has($file) === false) {
            throw new ZOEException(ZOEException::F0001, 'F0001');
        } else {
            $file = fopen($this->path . $this->dir . $file, 'w');
            fwrite($file, $content);
            fclose($file);
        }
    }

    /**
     * Obtiene el contenido de un archivo en el caché
     *
     * @param string $file
     *            Ruta y nombre del archivo
     * @return string Contenido del archivo
     * @throws ZOEException
     */
    public function Get(string $file): string
    {
        if ($this->Has($file) === false) {
            throw new ZOEException(ZOEException::F0001, 'F0001');
        } else {
            return file_get_contents($this->path . $this->dir . $file);
        }
    }

    /**
     * Borra un archivo del caché
     *
     * @param string $file
     * @return bool
     * @throws ZOEException
     */
    public function Delete(string $file): bool
    {
        if ($this->Has($file) === false) {
            throw new ZOEException(ZOEException::F0001, 'F0001');
        } else {
            return unlink($this->path . $this->dir . $file);
        }
    }
}
