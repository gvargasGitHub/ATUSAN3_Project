<?php

namespace App\Modules;

use Atusan\Controller\Module;

class Home extends Module
{
  function index()
  {
    $this->response->view($this);
  }
}
