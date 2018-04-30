<?php

/**
 * This file is part of the ZoeEE package.
 *
 * (c) Julian Lasso <jalasso69@misena.edu.co>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ZoeEE\ExceptionHandler;

/**
 * Clase para manejar las excepciones del sistema
 *
 * @author Julian Lasso <jalasso69@misena.edu.co>
 * @package ZoeEE
 * @subpackage ExceptionHandler
 */
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
     * Constructor de la clase ZOEException
     *
     * @param string $message
     *            Mensaje de la excepción
     * @param string $code
     *            [opcional] Códio de la excepción
     * @param \Throwable $previous
     */
    public function __construct(string $message = "", string $code = '0', $previous = null)
    {
        parent::__construct($message, 0, $previous);
        $this->code = $code;
    }
}
