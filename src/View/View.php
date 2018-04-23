<?php

/**
 * This file is part of the ZoeEE package.
 *
 * (c) Julian Lasso <jalasso69@misena.edu.co>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ZoeEE\View;

/**
 * 
 * @author Julian Lasso <jalasso69@misena.edu.co>
 * @package ZoeEE
 * @subpackage View
 */
class View
{

    private $variables;

    private $view;

    public function __construct(string $view = null, array $variables = array())
    {
        $this->variables = $variables;
        $this->view = $view;
    }

    /**
     *
     * @param string $view
     * @return View
     */
    public function SetView($view): View
    {
        $this->view = $view;
        return $this;
    }

    public function SetVariables(array $variables): View
    {
        $this->variables = $variables;
        return $this;
    }

    public function Render()
    {
        if (count($this->variables) > 0) {
            extract($this->variables);
        }
        require $this->view;
    }
}
