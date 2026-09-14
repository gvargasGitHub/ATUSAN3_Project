<?php
namespace App\Services;

use Atusan\Controller\Service;

class Health extends Service
{
  function index()
  {
    $this->response->json(['status' => 'healthy']);
  }
}