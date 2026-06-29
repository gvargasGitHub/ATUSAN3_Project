<?php

namespace App\Services;

use Atusan\Controller\Service;
use Atusan\Session\Session;

class KeepAliveSessionService extends Service
{
  function index()
  {
    Session::keepAlive();

    $this->response->json();
  }
}
