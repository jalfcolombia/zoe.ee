<?php

namespace ZoeEE\View;

class View
{

  private $variables;
  private $vista;

  public function __construct(string $vista, array $variables = array())
  {
    $this->variables = $variables;
    $this->vista     = $vista;
    $this->caching   = $caching;
  }

  public function Render()
  {
    if (count($this->variables) > 0) {
      extract($this->variables);
    }
    require $this->vista;
  }

}
