<?php

namespace App\Services;

use Atusan\Controller\Service;
use Atusan\Session\Session;

class AppServices extends Service
{
  function index()
  {
    $this->response->json();
  }

  function close()
  {
    Session::close();

    $this->response->json();
  }

  function keepAlive()
  {
    Session::keepAlive();
  }
}
