<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace ZoeEE\ExceptionHandler;

use Exception;
use Throwable;

/**
 * Description of ZOEException
 *
 * @author julian
 */
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
