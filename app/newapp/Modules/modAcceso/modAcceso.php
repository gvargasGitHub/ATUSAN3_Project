<?php

namespace App\Modules;

use App\Models\AccesoModel;
use Atusan\Exceptions\WarningException;
use Atusan\Controller\Module;
use Atusan\Security\SecurityMiddleware;
use Atusan\Session\AppSession;
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

    // df_login incluye campo Csrf
    $this->df_login->import([
      'csrf' => $csrfCode,
      'version' => $this->version,
      'migration' => $this->migration
    ]);

    $this->response->view($this);
  }

  function login()
  {
    if (!SecurityMiddleware::validateCsrf($this->request))
      throw new WarningException('No estas autorizado para ingresar.');

    $model = new AccesoModel();

    // Valida estado "logout"
    $lo = $model->callLogout();

    if ($lo['logout'] == 1)
      throw new WarningException('Sistema en mantenimiento.');

    // Valida la migracion de la estrctura PRM
    $db = $model->getPrmVersion();

    if ($db[0]['migration'] != $this->request->get('migration'))
      throw new WarningException("Migración {$this->request->get('migration')} no corresponde.");

    // Valida la cuenta del Usuario
    $user = $model->getUser($this->request->get('cuenta'));

    if (!$user) throw new WarningException('La cuenta '
      . $this->request->get('cuenta') . ' no existe');

    if ($user[0]['contrasena'] !== hash('sha256', $this->request->get('contrasena')))
      throw new WarningException('La contraseña es incorrecta');

    // Establece en Sesión que el Usuario se ha autenticado
    AppSession::auth(true);

    // Valores de Sesion
    AppSession::set('idUsuario', $user[0]['idUsuario']);
    AppSession::set('nombreUsuario', $user[0]['nombreUsuario']);
    AppSession::set('nombrePerfil', $user[0]['nombrePerfil']);
    AppSession::set('version', $this->request->get('version'));
    // AppSession::set('basedatosConn', 'permisos');

    AppSession::writeClose();

    // Regenera código Csrf (buenas practicas)
    SecurityMiddleware::regenerateCsrf();

    $this->response->json();
  }
}
