<?php

/**
 * This file is part of the ZoeEE package.
 *
 * (c) Julian Lasso <jalasso69@misena.edu.co>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ZoeEE\Validator;

/**
 * Clase para realizar validación de datos
 *
 * @author Julian Lasso <jalasso69@misena.edu.co>
 * @package ZoeEE
 * @subpackage Validator
 */
class Validation extends Validator
{

    /**
     * Nombre del input a validar
     *
     * @var string
     */
    private $name;

    /**
     * Método principal para realizar la validación, el cual devolverá
     *
     * @return bool VERDADERO si la validación pasó totalmente o de lo contrario, devolverá FALSO
     */
    public function isValid(): bool
    {
        return parent::isValid();
    }

    /**
     * Método para iniciar una nueva validación
     * 
     * @param string $name Nombre del la entrada a validar
     * @param mixed $value Valor a validar
     * @return Validation Instancia del objeto Validation
     */
    public function newValidation(string $name, $value = null): Validation
    {
        $this->name = $name;
        $this->form[$name] = array(
            'value' => $value
        );
        return $this;
    }

    /**
     * Validación del tipo ¿el valor será numérico?
     * 
     * @param string $error_message Mensaje de error
     * @return Validation Instancia de la clase Validation
     */
    public function isNumber(string $error_message): Validation
    {
        $this->form[$this->name][] = array(
            'type' => self::IS_NUMBER,
            'message' => $error_message
        );
        return $this;
    }

    /**
     * Validación del tipo ¿el valor principal será igual al valor secundario?
     * 
     * @param mixed $other_value Valor secundario a comprar
     * @param string $error_message Mensaje de error
     * @return Validation Instancia de la clase Validation
     */
    public function isEqual($other_value, string $error_message): Validation
    {
        $this->form[$this->name][] = array(
            'type' => self::IS_EQUAL,
            'message' => $error_message,
            'otherValue' => $other_value
        );
        return $this;
    }

    /**
     * Validación del tipo ¿el valor principal no es igual al valor secundario?
     * 
     * @param mixed $other_value Valor secundario a comprar
     * @param string $error_message Mensaje de error
     * @return Validation Instancia de la clase Validation
     */
    public function isNotEqual($other_value, string $error_message): Validation
    {
        $this->form[$this->name][] = array(
            'type' => self::IS_NOT_EQUAL,
            'message' => $error_message,
            'otherValue' => $other_value
        );
        return $this;
    }

    /**
     * Validación por medio de una expresión regular
     * 
     * @param string $pattern Patrón de expresión regular
     * @param string $error_message Mensaje de error
     * @return Validation Instancia de la clase Validation
     */
    public function pattern(string $pattern, string $error_message): Validation
    {
        $this->form[$this->name][] = array(
            'type' => self::PATTERN,
            'message' => $error_message,
            'pattern' => $pattern
        );
        return $this;
    }

    /**
     * Validación de un correo electrónico
     * 
     * @param string $error_message Mensaje de error
     * @return Validation Instancia de la clase Validation
     */
    public function isEmail(string $error_message): Validation
    {
        $this->form[$this->name][] = array(
            'type' => self::IS_EMAIL,
            'message' => $error_message
        );
        return $this;
    }

    /**
     * Validación del tipo ¿el valor princial es nulo?
     * 
     * @param string $error_message Mensaje de error
     * @return Validation Instancia de la clase Validation
     */
    public function isNull(string $error_message): Validation
    {
        $this->form[$this->name][] = array(
            'type' => self::IS_NULL,
            'message' => $error_message
        );
        return $this;
    }

    /**
     * Validación del tipo ¿el valor principal no será nulo?
     * 
     * @param string $error_message Mensaje de error
     * @return Validation Instancia de la clase Validation
     */
    public function isNotNull(string $error_message): Validation
    {
        $this->form[$this->name][] = array(
            'type' => self::IS_NOT_NULL,
            'message' => $error_message
        );
        return $this;
    }

    /**
     * Validación del tipo ¿el valor existe en base de datos?
     * 
     * @param string $error_message Mensaje de error
     * @param bool $answer Valor falso o verdero
     * @return Validation Instancia de la clase Validation
     */
    public function existsInDataBase(string $error_message, bool $answer): Validation
    {
        $this->form[$this->name][] = array(
            'type' => self::EXISTS_IN_DATABASE,
            'message' => $error_message,
            'answer' => $answer
        );
        return $this;
    }

    /**
     * Validación basada en un dato booleano VERDADERO
     * 
     * @param string $error_message Mensaje de error
     * @param bool $answer Valor falso o verdero
     * @return Validation Instancia de la clase Validation
     */
    public function booleanTrue(string $error_message, bool $answer): Validation
    {
        $this->form[$this->name][] = array(
            'type' => self::BOOLEAN_TRUE,
            'message' => $error_message,
            'answer' => $answer
        );
        return $this;
    }

    /**
     * Validación basada en un dato booleano FALSO
     *
     * @param string $error_message Mensaje de error
     * @param bool $answer Valor falso o verdero
     * @return Validation Instancia de la clase Validation
     */
    public function booleanFalse(string $error_message, bool $answer): Validation
    {
        $this->form[$this->name][] = array(
            'type' => self::BOOLEAN_FALSE,
            'message' => $error_message,
            'answer' => $answer
        );
        return $this;
    }

    /**
     * Validación personalizada basda en una clase con interfaz IValidator
     * 
     * @param string $error_message Mensaje de error
     * @param object $class Instancia de la clase de validación
     * @param array $params Arreglo de parámetros a usar en la clase
     * @return Validation Instancia de la clase Validation
     */
    public function custom(string $error_message, object $class, array $params = array()): Validation
    {
        $this->form[$this->name][] = array(
            'type' => self::CUSTOM,
            'message' => $error_message,
            'params' => $params,
            'class' => $class
        );
        return $this;
    }
}
