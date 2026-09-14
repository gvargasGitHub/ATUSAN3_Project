<?php

namespace App\Services;

use Atusan\Controller\Service;
use Atusan\Session\Session;

class SessionClose extends Service
{
  function index()
  {
    Session::close();

    $this->response->json();
  }
}
