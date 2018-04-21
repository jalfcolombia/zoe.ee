<?php

namespace ZoeEE\View;

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
