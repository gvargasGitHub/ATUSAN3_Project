<?php

namespace App\Modules;

use Atusan\Exceptions\WarningException;
use Atusan\Controller\Module;
use Atusan\Security\SecurityMiddleware;
use Atusan\Session\Session;
use Atusan\XML\XMLLoader;

class modAcceso extends Module
{
  public string $version;
  public string $updated;
  public string $migration;

  function index()
  {
    // obtiene version y migration del XML
    $xml = XMLLoader::load(APP_DIRECTORY . DS . 'version.xml');

    $this->version = $xml->version[0]->name;
    $this->updated = $xml->version[0]->date;
    $this->migration = $xml->version[0]->migration;

    // Genera código Csrf (buenas practicas)
    $csrfCode = SecurityMiddleware::generateCsrf();

    $this->df_login->setCsrf($csrfCode);
    
    $this->df_login->import([
      'version' => $this->version,
      'migration' => $this->migration
    ]);

    $this->response->view($this);
  }

  function login()
  {
    if (!SecurityMiddleware::validateCsrf($this->request))
      throw new WarningException('No estas autorizado para ingresar.');

    if ($this->request->get('cuenta') != 'admin') throw new WarningException('La cuenta '
      . $this->request->get('cuenta') . ' no existe');

    if ( hash('sha256', '1234') !== hash('sha256', $this->request->get('contrasena')))
      throw new WarningException('La contraseña es incorrecta');

    // Establece en Sesión que el Usuario se ha autenticado
    Session::auth(true);

    // Valores de Sesion
    Session::set('idUsuario', 1);
    Session::set('nombreUsuario', 'Admin');
    Session::set('nombrePerfil', 'Admin');
    Session::set('version', '1.0');

    Session::writeClose();

    // Regenera código Csrf (buenas practicas)
    SecurityMiddleware::regenerateCsrf();

    $this->response->json();
  }
}
