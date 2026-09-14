<?php

namespace App\Classes;

use Atusan\Components\Navbar;
use Atusan\Controller\Module;
use Atusan\Session\Session;

abstract class AppModuleParent extends Module
{
  protected Navbar $topbar;

  function index()
  {
    $this->topbar = $this->findViewById('topbar');

    $this->topbar->setTitle($_ENV['APP_TITLE']);
    $this->topbar->mi_account->setText(Session::get('nombreUsuario'));
    $this->topbar->setText('mi_version', 'Versión: ' . Session::get('version'));
  }
}
