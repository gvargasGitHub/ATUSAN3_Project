<?php

namespace App\Services;

use Atusan\Controller\Service;
use Atusan\Session\Session;

class CloseSessionService extends Service
{
  function index()
  {
    Session::close();

    $this->response->json();
  }

  function keepAlive()
  {
    Session::keepAlive();
  }
}
