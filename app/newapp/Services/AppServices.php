<?php

namespace App\Services;

use App\Models\AppModel;
use Atusan\Controller\Service;
use Atusan\Session\AppSession;

class AppServices extends Service
{
  function index()
  {
    $this->json();
  }

  function close()
  {
    AppSession::close();

    $this->response->json();
  }

  function keepAlive()
  {
    AppSession::keepAlive();

    $model = new AppModel();

    $this->response->json($model->callLogout());
  }

  function checkLogoutState()
  {
    $model = new AppModel();

    $this->response->json($model->callLogout());
  }
}
