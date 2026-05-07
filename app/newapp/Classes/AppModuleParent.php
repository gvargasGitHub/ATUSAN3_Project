<?php

namespace App\Classes;

use Atusan\Controller\Module;
use Atusan\Session\AppSession;

abstract class AppModuleParent extends Module
{
  function index()
  {
    $this->topbar->setTitle($this->app->title);
    $this->topbar->editItem('mi_account', 'text', AppSession::get('nombreUsuario'));
    $this->topbar->editItem('mi_version', 'text', 'Versión: ' . AppSession::get('version'));
  }
}
