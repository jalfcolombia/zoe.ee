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

    private const DIR = 'View' . DIRECTORY_SEPARATOR;
    
    private $variables;

    private $view;
    
    private $path;

    public function __construct(string $path, string $view = null, array $variables = array())
    {
        $this->path = $path;
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

    public function Render(string $bundle)
    {
        if (count($this->variables) > 0) {
            extract($this->variables);
        }
        
        if (is_file($this->path . self::DIR . $bundle . $this->view . '.template.php') === true) {
            require $this->path . self::DIR . $bundle . $this->view . '.template.php';
        } else {
            require $this->path . self::DIR . $this->view . '.template.php';
        }
    }
}
