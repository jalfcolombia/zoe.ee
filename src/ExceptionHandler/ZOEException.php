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
    public const F0001_MESSAGE = 'The indicated file does not exist';
    public const F0001_CODE = 'F0001';

    /**
     * Indica un tipo de dato indeterminado
     */
    public const F0002 = 'The type of data indicated (%s) is not valid';
    public const F0002_MESSAGE = 'The type of data indicated (%s) is not valid';
    public const F0002_CODE = 'F0002';

    public const F0003_MESSAGE = 'The global configuration file does not exist';
    public const F0003_CODE = 'F0003';

    /**
     * Constructor de la clase ZOEException
     *
     * @param string $message Mensaje de la excepción
     * @param string $code [opcional] Códio de la excepción
     * @param \Throwable $previous
     */
    public function __construct(string $message = "", string $code = '0', $previous = null)
    {
        parent::__construct($message, 0, $previous);
        $this->code = $code;
    }
}
