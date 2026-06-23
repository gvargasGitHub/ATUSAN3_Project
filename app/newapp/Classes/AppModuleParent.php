<?php

namespace App\Classes;

use Atusan\Controller\Module;
use Atusan\Session\Session;

abstract class AppModuleParent extends Module
{
  function index()
  {
    $this->topbar->setTitle($_ENV['APP_TITLE']);
    $this->topbar->mi_account->setText(Session::get('nombreUsuario'));
    $this->topbar->setText('mi_version', 'Versión: ' . Session::get('version'));
  }
}
