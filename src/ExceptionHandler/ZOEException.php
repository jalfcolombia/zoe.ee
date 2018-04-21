<?php

namespace ZoeEE\ExceptionHandler;

class ZOEException extends \Exception
{

    /**
     * Indica que el archivo no existe
     */
    public const F0001 = 'The indicated file does not exist';

    /**
     * Indica un tipo de dato indeterminado
     */
    public const F0002 = 'The type of data indicated (%s) is not valid';

    /**
     * Constrctor de ZOEException
     *
     * @param string $message
     * @param string $code
     * @param \Throwable $previous
     */
    public function __construct(string $message = "", string $code = '0', $previous = null)
    {
        parent::__construct($message, 0, $previous);
        $this->code = $code;
    }
}
