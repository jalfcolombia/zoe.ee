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

namespace ZoeEE\Response;

/**
 * Clase para manejar la respuesta al cliente
 *
 * @author Julian Lasso <jalasso69@misena.edu.co>
 * @package ZoeEE
 * @subpackage Response
 */
class Response
{

    /**
     * Nombre del directorio donde se almacenan las vistas
     */
    private const DIR = 'View' . DIRECTORY_SEPARATOR;

    /**
     * Arreglo asociativo con los datos para usar en la vista
     *
     * @var array
     */
    private $variables;

    /**
     * Nombre de la vista
     *
     * @var string
     */
    private $view;

    /**
     * Ruta física del proyecto en el servidor
     *
     * @var string
     */
    private $path;

    /**
     * Constructor de la clase Response
     *
     * @param string $path Ruta física del proyecto en el servidor
     * @param string $view [opcional] Nombre de la vista a usar
     * @param array $variables [opcional] Arreglo asociativo con los datos para usar en la vista
     */
    public function __construct(string $path, string $view = null, array $variables = array())
    {
        $this->path = $path;
        $this->variables = $variables;
        $this->view = $view;
    }

    /**
     * Establece la vista a usar
     *
     * @param string $view Nombre de la visa a usar
     * @return Response Instancia de la clase Response
     */
    public function setView($view): Response
    {
        $this->view = $view;
        return $this;
    }

    /**
     * Establece las variables que se usarán en la vista
     *
     * @param array $variables Arreglo asociativo con los datos para la vista
     * @return Response Instancia de la clase Response
     */
    public function setVariables(array $variables): Response
    {
        $this->variables = $variables;
        return $this;
    }

    /**
     * Establece cabecera HTTP
     *
     * @param string $param Nombre del parámetro
     * @param string $value Valor del parámetro
     * @return Response Instancia de la clase Response
     */
    public function setHeader(string $param, string $value): Response
    {
        header("{$param}: {$value}");
        return $this;
    }

    /**
     * Establece el código de respuesta HTTP para el navegador.
     *
     * @param int $code Número del código
     * @return Response Instancia de la clase Response
     */
    public function setResponseCode(int $code): Response
    {
        http_response_code($code);
        return $this;
    }

    /**
     * Obtiene el codigo HTTP de respuesta.
     *
     * @return mixed Código HTTP de respuesta
     */
    public function getResponseCode()
    {
        return http_response_code();
    }

    /**
     * Renderiza la vista
     *
     * @param string|null $bundle [opcional] Nombre del paquete donde está la vista
     */
    public function render(?string $bundle = null): void
    {
        if (count($this->variables) > 0) {
            extract($this->variables);
        }
        
        if (is_file($this->path . self::DIR . $bundle . $this->view . '.template.php') === true and $bundle !== null) {
            require $this->path . self::DIR . $bundle . $this->view . '.template.php';
        } else {
            require $this->path . self::DIR . $this->view . '.template.php';
        }
    }
}
