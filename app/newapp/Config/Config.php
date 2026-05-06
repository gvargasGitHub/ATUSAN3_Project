<?php

namespace App\Config;

class Config
{
  public static $time_zone = 'America/Mexico_City';

  // ----------------------------------
  //  Resources
  // ----------------------------------
  public static array $cssResources = [
    'base.css',
    'colors.css'
  ];

  public static array $jsResources = [
    'app.js'
  ];

  // ----------------------------------
  // Session
  // ----------------------------------
  public static $sessHandler = 'files';

  public static $sessName = 'PHPSESSID';

  public static $sessMaxLifeTime = 1440;

  public static $sessCookieSecure = 0; // HTTPS obligatorio

  public static $sessCookieHttpOnly = 1; // Javascript no accede

  public static $sessCookieSamesite = 'None'; // Cross-site permitido

  public static $sessUseCookie = 1;

  public static $sessUseOnlyCookies = 1;
}
