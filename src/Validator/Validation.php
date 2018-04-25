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

    private $name;

    public function __construct(array $validations = array())
    {
        $this->form = $validations;
    }

    public function isValid(): bool
    {
        return parent::isValid();
    }

    public function newValidation(string $name, $value = null): Validation
    {
        $this->name = $name;
        $this->form[$name] = array(
            'value' => $value
        );
        return $this;
    }

    public function isNumber(string $error_message): Validation
    {
        $this->form[$this->name][] = array(
            'type' => self::IS_NUMBER,
            'message' => $error_message
        );
        return $this;
    }

    public function isEqual($other_value, string $error_message): Validation
    {
        $this->form[$this->name][] = array(
            'type' => self::IS_EQUAL,
            'message' => $error_message,
            'otherValue' => $other_value
        );
        return $this;
    }

    public function isNotEqual($other_value, string $error_message): Validation
    {
        $this->form[$this->name][] = array(
            'type' => self::IS_NOT_EQUAL,
            'message' => $error_message,
            'otherValue' => $other_value
        );
        return $this;
    }

    public function pattern(string $pattern, string $error_message): Validation
    {
        $this->form[$this->name][] = array(
            'type' => self::PATTERN,
            'message' => $error_message,
            'pattern' => $pattern
        );
        return $this;
    }

    public function isEmail(string $error_message): Validation
    {
        $this->form[$this->name][] = array(
            'type' => self::IS_EMAIL,
            'message' => $error_message
        );
        return $this;
    }

    public function isNull(string $error_message): Validation
    {
        $this->form[$this->name][] = array(
            'type' => self::IS_NULL,
            'message' => $error_message
        );
        return $this;
    }

    public function isNotNull(string $error_message): Validation
    {
        $this->form[$this->name][] = array(
            'type' => self::IS_NOT_NULL,
            'message' => $error_message
        );
        return $this;
    }

    public function existsInDataBase(string $error_message, bool $answer): Validation
    {
        $this->form[$this->name][] = array(
            'type' => self::EXISTS_IN_DATABASE,
            'message' => $error_message,
            'answer' => $answer
        );
        return $this;
    }

    public function booleanTrue(string $error_message, bool $answer): Validation
    {
        $this->form[$this->name][] = array(
            'type' => self::BOOLEAN_TRUE,
            'message' => $error_message,
            'answer' => $answer
        );
        return $this;
    }

    public function booleanFalse(string $error_message, bool $answer): Validation
    {
        $this->form[$this->name][] = array(
            'type' => self::BOOLEAN_FALSE,
            'message' => $error_message,
            'answer' => $answer
        );
        return $this;
    }

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
