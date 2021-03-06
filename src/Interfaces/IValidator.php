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
 * @category Interfaces
 * @package  ZoeEE
 * @author   Julian Lasso <jalasso69@misena.edu.co>
 * @license  https://github.com/jalfcolombia/zoe.ee/blob/master/LICENSE Apache2
 * @link     https://github.com/jalfcolombia/zoe.ee
 */

namespace ZoeEE\Interfaces;

/**
 * Interfaz para los validadores personalizados
 *
 * @category   Interfaces
 * @package    ZoeEE
 * @subpackage Validator
 * @author     Julian Lasso <jalasso69@misena.edu.co>
 * @license    https://github.com/jalfcolombia/zoe.ee/blob/master/LICENSE Apache2
 * @link       https://github.com/jalfcolombia/zoe.ee
 */
interface IValidator
{

    /**
     * Función principal para un validador personalizado
     *
     * @param mixed $value  Valor principal a validar
     * @param array $params [opcional] Parámetros necesarios para la validación
     *
     * @return bool VERDADERO si cumple con la validación, FALSO si no cumple con la validación.
     */
    public function validate($value, array $params = array());
}
