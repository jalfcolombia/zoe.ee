<?php
namespace ZoeEE\ExceptionHandler;

use Exception;
use Throwable;

class ZOEException extends Exception
{

    /**
     * Indica que el archivo no existe
     */
    public const F0001 = 'The indicated file does not exist';

    /**
     * Constrctor de ZOEException
     *
     * @param string $message
     * @param string $code
     * @param Throwable $previous
     */
    public function __construct(string $message = "", string $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, 0, $previous);
        $this->code = $code;
    }
}
