<?php

/**
 * This file is part of the ZoeEE package.
 *
 * (c) Julian Lasso <jalasso69@misena.edu.co>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ZoeEE\Interfaces;

/**
 * Interfaz para los validadores personalizados
 *
 * @author Julián Lasso <jalasso69@misena.edu.co>
 * @package ZoeEE
 * @subpackage Interfaces
 */
interface IValidator
{

    /**
     * Función principal para un validador personalizado
     *
     * @param mixed $value
     *            Valor principal a validar
     * @param
     *            array [opcional] $params Parámetros necesarios para la validación
     * @return bool VERDADERO si cumple con la validación, FALSO si no cumple con la validación.
     */
    public function validate($value, array $params = array());
}
