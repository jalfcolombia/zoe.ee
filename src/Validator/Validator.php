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

namespace ZoeEE\Validator;

/**
 * Clase para realizar validaciones en formularios o similares
 *
 * @author Julian Lasso <jalasso69@misena.edu.co>
 * @package ZoeEE
 * @subpackage Validator
 */
class Validator
{

    /**
     * Validación del tipo ¿el valor será numérico?
     */
    protected const IS_NUMBER = 0;

    /**
     * Validación del tipo ¿el valor principal será igual al valor secundario?
     */
    protected const IS_EQUAL = 1;

    /**
     * Validación del tipo ¿el valor principal no es igual al valor secundario?
     */
    protected const IS_NOT_EQUAL = 2;

    /**
     * Validación por medio de una expresión regular
     */
    protected const PATTERN = 3;

    /**
     * Validación de un correo electrónico
     */
    protected const IS_EMAIL = 4;

    /**
     * Validación del tipo ¿el valor princial es nulo?
     */
    protected const IS_NULL = 5;

    /**
     * Validación del tipo ¿el valor principal no será nulo?
     */
    protected const IS_NOT_NULL = 6;

    /**
     * Validación del tipo ¿el valor existe en base de datos?
     */
    protected const EXISTS_IN_DATABASE = 7;

    /**
     * Validación basada en un dato booleano VERDADERO
     */
    protected const BOOLEAN_TRUE = 8;

    /**
     * Validación basada en un dato booleano FALSO
     */
    protected const BOOLEAN_FALSE = 9;

    /**
     * Validación personalizada
     */
    protected const CUSTOM = 10;

    /**
     * Variable contenedora de la configuración para realizar las validaciones
     *
     * @var string
     */
    protected $form;

    /**
     * Variable recolectora de los errores presentados para la configuración de validación
     *
     * @var array
     */
    private $error = array();

    /**
     * Obtiene el arreglo de errores
     *
     * @return array
     */
    public function getErrors(): array
    {
        return $this->error;
    }

    /**
     * Establece un error a un input determinado
     *
     * @param string $input Nombre de para quien es el error
     * @param string $message Mensaje de error
     * @return Validator
     */
    protected function setError(string $input, string $message): Validator
    {
        $this->error[$input]['message'] = $message;
        return $this;
    }

    /**
     * Método principal para realizar la validación, el cual devolverá<br>
     * VERDADERO si la validación pasó totalmente o de lo contrario, devolverá FALSO
     *
     * @return bool
     */
    protected function isValid(): bool
    {
        $flagCnt = 0;
        foreach ($this->form as $input => $validations) {
            $cnt = count($validations) - 1;
            for ($x = 0; $x < $cnt; $x++) {
                $flag = true;
                switch ($validations[$x]['type']) {

                    // IS_NUMBER
                    case 0:
                        if (is_numeric($validations['value']) === false) {
                            $flag = false;
                            $flagCnt++;
                        }
                        break;

                    // IS_EQUAL
                    case 1:
                        if (!($validations['value'] == $validations[$x]['otherValue'])) {
                            $flag = false;
                            $flagCnt++;
                        }
                        break;

                    // IS_NOT_EQUAL
                    case 2:
                        if ($validations['value'] == $validations[$x]['otherValue']) {
                            $flag = false;
                            $flagCnt++;
                        }
                        break;

                    // PATTERN
                    case 3:
                        if (!preg_match($validations[$x]['pattern'], $validations['value'])) {
                            $flag = false;
                            $flagCnt++;
                        }
                        break;

                    // IS_EMAIL
                    case 4:
                        if (filter_var($validations['value'], FILTER_VALIDATE_EMAIL) === false) {
                            $flag = false;
                            $flagCnt++;
                        }
                        break;

                    // IS_NULL
                    case 5:
                        if (strlen($validations['value']) > 0) {
                            $flag = false;
                            $flagCnt++;
                        }
                        break;

                    // IS_NOT_NULL
                    case 6:
                        if (is_null($validations['value']) === true or $validations['value'] === '') {
                            $flag = false;
                            $flagCnt++;
                        }
                        break;

                    // EXISTS_IN_DATABASE
                    case 7:
                        if ($validations[$x]['answer'] === true) {
                            $flag = false;
                            $flagCnt++;
                        }
                        break;

                    // BOOLEAN_TRUE
                    case 8:
                        if ($validations[$x]['answer'] === true) {
                            $flag = false;
                            $flagCnt++;
                        }
                        break;

                    // BOOLEAN_FALSE
                    case 9:
                        if ($validations[$x]['answer'] === false) {
                            $flag = false;
                            $flagCnt++;
                        }
                        break;

                    // CUSTOM
                    case 10:
                        if ($validations[$x]['class']->validate($validations['value'], $validations[$x]['params']) === false) {
                            $flag = false;
                            $flagCnt++;
                        }
                        break;
                }
                if (!$flag) {
                    $this->setError($input, $validations[$x]['message']);
                    break;
                }
            }
        }
        return $flagCnt > 0 ? false : true;
    }
}
