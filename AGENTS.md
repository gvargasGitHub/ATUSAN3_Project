# ATUSAN 3 — Agent Development Instructions

Este proyecto utiliza **ATUSAN 3** como framework de desarrollo.

Este documento define las reglas oficiales que debe seguir cualquier agente de inteligencia artificial al analizar, crear o modificar código dentro de un proyecto ATUSAN 3.

El objetivo principal es generar código compatible con ATUSAN 3, respetando su arquitectura, API, convenciones y herramientas existentes.

---

# 1. Información general

| Elemento                   | Estándar                             |
| -------------------------- | ------------------------------------ |
| Framework                  | ATUSAN 3                             |
| Backend                    | PHP 8.2+                             |
| Frontend                   | HTML, CSS y JavaScript               |
| Arquitectura               | MVC modular                          |
| Dependencias               | Composer                             |
| Autoload del framework     | PSR-4                                |
| Namespace del framework    | `Atusan\`                            |
| Namespace de la aplicación | `App\`                               |
| Entry point                | `public/index.php`                   |
| Routing                    | `Atusan\Route\Route`                 |
| Request                    | `Atusan\Http\Request\Request`        |
| Response                   | `Atusan\Http\Response\Response`      |
| Persistencia               | `Atusan\Persistence\DBConnection`    |
| Modelo base                | `Atusan\Model\ModelBase`             |
| Seguridad                  | `Atusan\Security\SecurityMiddleware` |
| Sesión                     | `Atusan\Session\Session`             |
| CLI                        | `atusan_cli`                         |

ATUSAN 3 **no es Laravel, Symfony, CodeIgniter ni otro framework PHP**.

No asumas APIs, estructuras, patrones o convenciones provenientes de otros frameworks.

Cuando ATUSAN proporcione una solución para una operación, utiliza primero la API de ATUSAN.

---

# 2. Fuentes de verdad

Antes de generar código, utiliza las siguientes fuentes de información en este orden:

1. Este archivo `AGENTS.md`.
2. `STACK.md`, cuando exista.
3. `atusan.json`, cuando exista.
4. Código existente de la aplicación.
5. `composer.json`.
6. API pública de ATUSAN 3.
7. Convenciones existentes del módulo relacionado.

`STACK.md` contiene las decisiones tecnológicas particulares del proyecto.

`atusan.json` contiene metadatos estructurados de la instalación ATUSAN.

No sustituyas decisiones expresamente definidas en estos archivos por preferencias genéricas.

---

# 3. Principio de cambios mínimos

ATUSAN 3 favorece soluciones simples y localizadas.

Cuando una tarea pueda resolverse mediante un cambio puntual, no realices una refactorización estructural.

Mantén la arquitectura existente y evita:

* reestructuraciones profundas;
* nuevas capas arquitectónicas innecesarias;
* abstracciones sin beneficio concreto;
* dependencias externas para tareas simples;
* reemplazar código funcional únicamente por estilo.

La prioridad debe ser:

```text
cambio puntual
    ↓
mejora localizada
    ↓
refactorización localizada
    ↓
refactorización estructural solamente cuando sea necesaria
```

---

# 4. Estructura de un proyecto ATUSAN

No asumas la estructura tradicional:

```text
app/
    Controllers/
    Models/
    Views/
```

ATUSAN organiza el desarrollo por **aplicaciones**.

La estructura conceptual es:

```text
project/
│
├── app/
│   └── NombreAplicacion/
│       ├── Modules/
│       ├── Models/
│       ├── Services/
│       ├── Classes/
│       ├── Components/
│       ├── Templates/
│       ├── Views/
│       ├── Config/
│       ├── Route.php
│       └── .env
│
├── public/
│   ├── index.php
│   └── ...
│
├── vendor/
│
├── composer.json
├── composer.lock
├── AGENTS.md
├── STACK.md
└── atusan.json
```

La estructura exacta debe verificarse en el proyecto antes de crear archivos.

No crees carpetas nuevas únicamente porque sean habituales en otros frameworks.

---

# 5. Bootstrap de ATUSAN

El inicio del framework está controlado por:

```php
Atusan\Bootstrap\Bootstrap
```

El método principal es:

```php
Bootstrap::app();
```

El flujo general es:

```text
public/index.php
      ↓
Bootstrap::app()
      ↓
Kernel::handle()
      ↓
Request::capture()
      ↓
Kernel::execute()
      ↓
SecurityMiddleware
      ↓
Route::resolve()
      ↓
Controller
      ↓
Response
```

`Bootstrap::app()` realiza tareas fundamentales como:

* determinar el tipo de respuesta solicitado;
* definir constantes del framework;
* localizar `APP_ROOT`;
* cargar variables de entorno;
* determinar la aplicación activa;
* registrar manejo de errores;
* registrar namespace `App\`;
* configurar zona horaria;
* iniciar `Kernel`.

No dupliques esta inicialización dentro de controladores, módulos o servicios.

---

# 6. Aplicación activa

ATUSAN obtiene la aplicación desde las variables de entorno.

El framework establece internamente constantes como:

```php
APP_NAME
APP_DIRECTORY
APP_ROOT
APP_CORE
APP_ENV
APP_DEBUG
```

No hardcodees rutas absolutas hacia una aplicación si existe una constante del framework que representa esa ubicación.

Para código perteneciente a la aplicación utiliza normalmente el namespace:

```php
namespace App;
```

o sus subnamespaces:

```php
App\Modules
App\Models
App\Services
App\Classes
App\Config
```

Antes de asumir un namespace, revisa el código existente.

---

# 7. Modelo de aplicaciones

Un proyecto ATUSAN puede contener múltiples aplicaciones.

Sin embargo, el modelo estándar es **una aplicación principal por proyecto**.

La aplicación activa está definida por la configuración del proyecto y debe coincidir con la aplicación seleccionada en `atusan.json`.

Al generar o modificar código:

* trabaja sobre la aplicación activa;
* no crees una nueva aplicación salvo que sea solicitado explícitamente;
* no distribuyas una funcionalidad entre varias aplicaciones sin necesidad;
* no cambies la aplicación activa de forma implícita;
* utiliza aplicaciones adicionales únicamente cuando exista una separación funcional clara;
* antes de crear módulos, modelos o servicios, identifica primero la aplicación activa;
* cuando el CLI permita omitir `--app`, utiliza la aplicación activa como destino predeterminado;
* utiliza `--app` únicamente cuando sea necesario trabajar explícitamente sobre otra aplicación.

El proyecto base de ATUSAN utiliza inicialmente:

```text
app/
└── newapp/
```

La aplicación `newapp` es una plantilla temporal.

Al ejecutar:

```bash
atusan_cli publish --name nombre_aplicacion
```

la aplicación `newapp` debe ser renombrada al nombre indicado y convertirse en la aplicación activa del proyecto.

No trates `newapp` como una aplicación funcional permanente.

En un proyecto estándar, la estructura esperada después de publicar es:

```text
app/
└── nombre_aplicacion/
```

El soporte de múltiples aplicaciones debe considerarse una capacidad avanzada de ATUSAN, no un requisito para todos los proyectos.

Si existen varias aplicaciones, trabaja únicamente sobre la aplicación activa salvo indicación explícita.

---

# 8. Kernel

El Kernel se encuentra en:

```php
Atusan\Kernel\Kernel
```

Sus operaciones principales son:

```php
Kernel::handle();
Kernel::execute($request);
```

`handle()` carga las rutas mediante:

```php
Route::implement();
```

y captura la petición:

```php
Request::capture();
```

`execute()` ejecuta primero:

```php
SecurityMiddleware::handle($request);
```

y posteriormente resuelve la ruta:

```php
[$controller, $routeType] = Route::resolve();
```

Finalmente invoca el método del controlador pasando los parámetros definidos en la URI.

No implementes un segundo dispatcher o kernel para funcionalidades normales de la aplicación.

---

# 9. Request

La API oficial de Request es:

```php
Atusan\Http\Request\Request
```

`Request` utiliza patrón singleton.

Obtén la instancia mediante:

```php
$request = Request::instance();
```

o:

```php
$request = Request::capture();
```

Los controladores derivados de `Atusan\Controller\Controller` ya tienen disponible:

```php
$this->request
```

No crees manualmente una instancia de `Request`.

---

# 10. API de Request

Los métodos públicos relevantes son:

| Método                 | Propósito                                 |
| ---------------------- | ----------------------------------------- |
| `Request::instance()`  | Obtener instancia singleton               |
| `Request::capture()`   | Capturar la petición actual               |
| `method()`             | Método HTTP                               |
| `uri()`                | URI solicitada                            |
| `get($key, $default)`  | Obtener un dato                           |
| `all()`                | Obtener GET, POST y JSON combinados       |
| `has($key)`            | Verificar existencia de un dato           |
| `header($key)`         | Obtener encabezado HTTP                   |
| `isJson()`             | Determinar si el body utiliza JSON        |
| `json()`               | Obtener body JSON                         |
| `hasInvalidJsonBody()` | Detectar JSON inválido                    |
| `jsonBodyError()`      | Obtener detalle del error JSON            |
| `files()`              | Obtener archivos enviados                 |
| `addUriParam()`        | Registrar parámetros extraídos de la ruta |
| `getUriParams()`       | Obtener parámetros de URI                 |

---

# 11. Prioridad de Request::get()

Para:

```php
$this->request->get('campo');
```

ATUSAN busca actualmente el dato en este orden:

```text
JSON
POST
GET
URI parameters
default
```

Utiliza `Request::get()` cuando quieras obtener un parámetro independientemente del mecanismo HTTP mediante el cual fue recibido.

Ejemplo:

```php
$id = $this->request->get('id');
```

Evita acceder directamente a:

```php
$_GET
$_POST
$_FILES
php://input
```

cuando `Request` ya proporcione la funcionalidad correspondiente.

---

# 12. Peticiones JSON

Para enviar JSON al backend utiliza:

```http
Content-Type: application/json
```

La API es:

```php
$data = $this->request->json();
```

También pueden obtenerse campos individuales:

```php
$name = $this->request->get('name');
```

ATUSAN detecta JSON inválido mediante:

```php
$this->request->hasInvalidJsonBody();
```

y permite consultar el error:

```php
$this->request->jsonBodyError();
```

No implementes nuevamente lectura manual de:

```php
file_get_contents('php://input');
```

salvo que exista una razón específica no cubierta por `Request`.

---

# 13. Archivos enviados

Los archivos enviados se procesan mediante:

```php
$this->request->files();
```

El resultado utiliza:

```php
Atusan\Iterators\FilesUploadedIterator
```

y los archivos individuales utilizan:

```php
Atusan\Types\FileUploadedType
```

Utiliza esta abstracción antes de acceder directamente a `$_FILES`.

---

# 14. Response

La respuesta HTTP está centralizada en:

```php
Atusan\Http\Response\Response
```

Utiliza patrón singleton.

Puede obtenerse mediante:

```php
Response::instance();
```

Todo controlador ATUSAN ya dispone de:

```php
$this->response
```

No crees sistemas alternativos de Response sin necesidad.

---

# 15. API de Response

La API principal incluye:

| Método                         | Propósito                      |
| ------------------------------ | ------------------------------ |
| `Response::instance()`         | Obtener instancia singleton    |
| `Response::status($code)`      | Establecer código HTTP         |
| `view($module)`                | Renderizar un módulo           |
| `add($key, $value)`            | Agregar datos a respuesta JSON |
| `message($message)`            | Establecer mensaje             |
| `json($data)`                  | Generar respuesta JSON         |
| `exception($message, $detail)` | Generar respuesta de excepción |
| `notice($message)`             | Generar aviso                  |
| `warning($message)`            | Generar advertencia            |
| `unknow($message, $detail)`    | Manejar error desconocido      |

---

# 16. Respuestas JSON

La forma estándar es:

```php
$this->response->json([
    'id' => $id
]);
```

ATUSAN genera una estructura similar a:

```json
{
  "status": "ok",
  "message": "",
  "data": {
    "id": 10
  }
}
```

También puede construirse progresivamente:

```php
$this->response->message('Operación realizada.');

$this->response->add('id', $id);
$this->response->add('name', $name);

$this->response->json();
```

No serialices manualmente JSON mediante `json_encode()` si `Response` ya cubre el caso.

---

# 17. Códigos HTTP

Utiliza:

```php
Response::status(201);
```

antes de escribir la respuesta cuando necesites modificar el código HTTP.

Ejemplo:

```php
Response::status(201);

$this->response->json([
    'id' => $id
]);
```

Utiliza códigos HTTP coherentes con el resultado.

```text
200 OK
201 Created
204 No Content
400 Bad Request
401 Unauthorized
403 Forbidden
404 Not Found
409 Conflict
422 Unprocessable Entity
500 Internal Server Error
```

---

# 18. Selección HTML / JSON

ATUSAN determina el tipo solicitado durante el bootstrap.

La constante:

```php
CONTENT_TYPE_REQUESTED
```

puede contener:

```text
HTML
JSON
```

Se considera una petición JSON cuando se detecta:

```http
Accept: application/json
```

o:

```http
X-Requested-With: XMLHttpRequest
```

Esta información es utilizada principalmente por `Response` para determinar cómo representar errores, warnings y notices.

No dependas exclusivamente del hecho de utilizar una ruta `Route::ajax()` para determinar el formato de respuesta.

---

# 19. Controlador base

Todos los controladores derivan directa o indirectamente de:

```php
Atusan\Controller\Controller
```

El controlador proporciona:

```php
$this->request
$this->response
$this->name
```

La interfaz base requiere:

```php
public function index();
```

Una clase controlador típica no debe crear nuevamente `Request` ni `Response`.

---

# 20. Tipos oficiales de controlador

ATUSAN proporciona tres tipos principales para desarrollar aplicaciones:

| Tipo           | Clase base                       | Uso                           |
| -------------- | -------------------------------- | ----------------------------- |
| Módulo         | `Atusan\Controller\Module`       | Interfaz/página               |
| Módulo anidado | `Atusan\Controller\ModuleNested` | Contenido embebido o dinámico |
| Servicio       | `Atusan\Controller\Service`      | Backend/API/AJAX              |

Selecciona el tipo de controlador según su responsabilidad.

No crees una jerarquía alternativa si uno de estos tipos resuelve la necesidad.

---

# 21. Module

Los módulos visuales deben extender:

```php
Atusan\Controller\Module
```

Una implementación típica es:

```php
namespace App\Modules;

class modExample extends AppModuleParent
{
    public function index()
    {
        $this->response->view($this);
    }
}
```

`Module` proporciona, entre otros:

```php
getDirectory()
setTemplate()
getTemplate()
setTitle()
getTitle()
setView()
getView()
make()
write()
extend()
```

El módulo administra además sus componentes, manifiesto XML, template y vista.

No sustituyas este mecanismo por un motor de vistas externo salvo que `STACK.md` lo establezca expresamente.

---

# 22. Components.xml

Los módulos pueden disponer de:

```text
Components.xml
```

Este archivo forma parte del sistema declarativo de componentes de ATUSAN.

Ejemplo básico:

```xml
<?xml version="1.0" encoding="utf-8"?>

<Root
    xmlns:html="http://schemas.atusan.com/html/attributes"
    xmlns:css="http://schemas.atusan.com/css/properties"
    title="Título"
    template="frame">

</Root>
```

Antes de crear manualmente elementos que puedan corresponder al sistema de componentes, revisa el `Components.xml` existente y las clases de `Atusan\Components`.

No elimines o ignores el manifiesto XML de un módulo sin comprender primero su función.

---

# 23. Vistas de módulos

Las vistas pueden contener HTML, CSS y JavaScript.

Una vista ATUSAN puede utilizar propiedades del módulo:

```php
<h3><?= $this->title ?></h3>
```

Respeta las convenciones existentes de cada aplicación.

No migres automáticamente vistas ATUSAN a React, Vue, Angular, JSX u otro sistema.

---

# 24. ModuleNested

Los módulos que deben ser renderizados dentro de otros componentes pueden extender:

```php
Atusan\Controller\ModuleNested
```

La API específica incluye:

```php
buildNested()
nested()
nestedToJson()
```

`nestedToJson()` genera una respuesta que contiene:

```text
name
title
content
```

Las rutas para estos módulos pueden declararse mediante:

```php
Route::nested('/ruta', MiModuloNested::class);
```

No reproduzcas manualmente el proceso de captura y serialización si `ModuleNested` ya proporciona esta funcionalidad.

---

# 25. Service

Los servicios backend deben extender:

```php
Atusan\Controller\Service
```

Ejemplo:

```php
namespace App\Services;

use Atusan\Controller\Service;

class CustomerService extends Service
{
    public function index()
    {
        $this->response->json([
            'message' => 'Hello World'
        ]);
    }
}
```

Utiliza `Service` para operaciones que no necesitan renderizar un módulo visual.

---

# 26. Routing

Las rutas de cada aplicación están definidas en:

```text
Route.php
```

La clase oficial es:

```php
Atusan\Route\Route
```

No introduzcas routers externos.

---

# 27. Métodos oficiales de Route

ATUSAN soporta:

```php
Route::get()
Route::post()
Route::ajax()
Route::nested()
Route::put()
Route::patch()
Route::delete()
Route::middleware()
```

Los métodos HTTP oficialmente soportados son:

```text
GET
POST
PUT
PATCH
DELETE
```

---

# 28. Route::get()

Ejemplo:

```php
Route::get(
    '/customers',
    CustomerModule::class
);
```

El método resolver utilizado por defecto es:

```text
index
```

También puede especificarse:

```php
Route::get(
    '/customers',
    CustomerModule::class,
    'list'
);
```

---

# 29. Route::post()

Ejemplo:

```php
Route::post(
    '/customers',
    CustomerService::class,
    'save'
);
```

Utilízalo para peticiones HTTP POST normales.

---

# 30. Route::ajax()

Ejemplo:

```php
Route::ajax(
    '/customers/save',
    CustomerService::class,
    'save'
);
```

`Route::ajax()` registra internamente una ruta:

```text
POST
```

El nombre `ajax` expresa la intención de uso de la ruta, pero no constituye un método HTTP diferente.

No asumas que `Route::ajax()` genera automáticamente JSON.

El controlador sigue siendo responsable de utilizar `Response`.

---

# 31. Route::nested()

Ejemplo:

```php
Route::nested(
    '/customers/detail',
    CustomerDetail::class
);
```

Esta ruta utiliza automáticamente:

```php
nestedToJson()
```

como método resolver.

Debe utilizarse con controladores compatibles con `ModuleNested`.

---

# 32. PUT, PATCH y DELETE

Ejemplos:

```php
Route::put(
    '/customers/{id}',
    CustomerService::class,
    'replace'
);

Route::patch(
    '/customers/{id}',
    CustomerService::class,
    'update'
);

Route::delete(
    '/customers/{id}',
    CustomerService::class,
    'delete'
);
```

No emules estos métodos mediante POST si la API del proyecto ya utiliza los métodos HTTP correspondientes.

---

# 33. Parámetros de ruta

ATUSAN soporta parámetros mediante llaves:

```php
Route::get(
    '/customers/{id}',
    CustomerModule::class,
    'detail'
);
```

El método del controlador recibe los parámetros:

```php
public function detail($id)
{
}
```

También están disponibles en `Request`:

```php
$id = $this->request->get('id');
```

No analices manualmente `REQUEST_URI` para obtener parámetros ya declarados en una ruta.

---

# 34. Middleware de rutas

ATUSAN permite agrupar rutas condicionadas por una variable de sesión:

```php
Route::middleware('auth', function () {

    Route::get(
        '/customers',
        CustomerModule::class
    );

    Route::ajax(
        '/customers/save',
        CustomerService::class,
        'save'
    );

}, '/login');
```

El primer argumento corresponde a la clave de sesión utilizada como filtro.

El tercer argumento corresponde a la ruta GET utilizada como redireccionamiento cuando el filtro no se cumple.

El estado de middleware aplica **únicamente a las rutas declaradas dentro del callback**.

Las rutas declaradas posteriormente fuera del callback no deben heredar ese middleware.

No sustituyas este mecanismo por otro sistema de middleware sin necesidad.

---

# 35. Seguridad

La clase principal es:

```php
Atusan\Security\SecurityMiddleware
```

`Kernel` ejecuta automáticamente:

```php
SecurityMiddleware::handle($request);
```

Este middleware se encarga de funciones globales como sesión, encabezados de seguridad y validación del método HTTP.

Los métodos HTTP permitidos incluyen:

```text
GET
POST
PUT
PATCH
DELETE
```

No invoques nuevamente `handle()` desde controladores.

---

# 36. CSRF

La API oficial proporciona:

```php
SecurityMiddleware::generateCsrf();
SecurityMiddleware::validateCsrf($request);
SecurityMiddleware::regenerateCsrf();
```

La validación acepta el token desde:

```text
csrf_token
```

o:

```http
X-CSRF-TOKEN
```

Para operaciones que requieran protección CSRF utiliza esta API en lugar de implementar tokens independientes.

---

# 37. Authorization

La API oficial permite obtener el encabezado:

```php
SecurityMiddleware::getAuthorizationHeader();
```

Bearer:

```php
$token = SecurityMiddleware::getBearerToken();
```

Basic:

```php
$credentials = SecurityMiddleware::getBasicAuth();
```

Para Basic, el resultado esperado es:

```php
[
    'username' => $username,
    'password' => $password
]
```

o `null` si el encabezado no es válido.

No analices manualmente `$_SERVER['HTTP_AUTHORIZATION']` cuando esta API sea suficiente.

---

# 38. Bearer Token

ATUSAN permite autenticación con token Bearer:

```http
Authorization: Bearer TOKEN
```

Obtén el token:

```php
$token = SecurityMiddleware::getBearerToken();
```

No introduzcas JWT automáticamente.

Si la aplicación utiliza tokens fijos almacenados en un catálogo de clientes, conserva ese mecanismo salvo requerimiento explícito en contrario.

---

# 39. Sesiones

La API oficial es:

```php
Atusan\Session\Session
```

Métodos relevantes:

| Método                       | Propósito                        |
| ---------------------------- | -------------------------------- |
| `Session::start()`           | Iniciar sesión                   |
| `Session::keepAlive()`       | Mantener sesión                  |
| `Session::writeClose()`      | Cerrar escritura                 |
| `Session::destroy()`         | Destruir sesión                  |
| `Session::close()`           | Cambiar estado de autenticación  |
| `Session::get($key)`         | Obtener variable                 |
| `Session::set($key, $value)` | Establecer variable              |
| `Session::auth($state)`      | Obtener/establecer autenticación |
| `Session::id()`              | Obtener identificador            |

El middleware de seguridad inicia la sesión automáticamente.

No llames `session_start()` dentro de controladores normales si el framework ya inicializó la sesión.

---

# 40. ModelBase

Los modelos pueden extender:

```php
Atusan\Model\ModelBase
```

Ejemplo:

```php
namespace App\Models;

use Atusan\Model\ModelBase;

class CustomerModel extends ModelBase
{
    public function selectAll()
    {
        return $this->conn->query(
            'SELECT id, name FROM customers'
        );
    }
}
```

`ModelBase` proporciona:

```php
$this->conn
```

y:

```php
ModelBase::connect();
ModelBase::model();
```

No abras conexiones nuevas para cada operación si el modelo ya dispone de `$this->conn`.

---

# 41. Configuración de base de datos

Por defecto `ModelBase` utiliza variables de entorno equivalentes a:

```text
DB_DRIVER
DB_HOST
DB_USER
DB_PASS
DB_NAME
```

No hardcodees credenciales de base de datos.

No incluyas passwords o secretos dentro del repositorio.

---

# 42. DBConnection

La API de persistencia oficial es:

```php
Atusan\Persistence\DBConnection
```

La conexión se obtiene mediante:

```php
DBConnection::connect(
    $driver,
    $host,
    $user,
    $pass,
    $database,
    $ssl
);
```

El parámetro SSL es opcional y utiliza:

```php
false
```

como valor predeterminado cuando no se especifica.

---

# 43. API de DBConnection

| Método                   | Propósito                             |
| ------------------------ | ------------------------------------- |
| `connect()`              | Crear conexión                        |
| `close()`                | Cerrar conexión                       |
| `query($sql, $params)`   | Ejecutar consulta y obtener resultado |
| `execute($sql, $params)` | Ejecutar operación                    |
| `routine(...)`           | Ejecutar rutina/procedimiento         |
| `autocommit($mode)`      | Configurar autocommit                 |
| `commit()`               | Confirmar transacción                 |
| `rollback()`             | Revertir transacción                  |
| `sqlstate()`             | Obtener estado SQL                    |
| `affectedRows()`         | Consultar filas afectadas             |

Utiliza parámetros separados del SQL.

Correcto:

```php
$result = $this->conn->query(
    'SELECT id, name FROM customers WHERE id = ?',
    [$id]
);
```

Incorrecto:

```php
$result = $this->conn->query(
    "SELECT id, name FROM customers WHERE id = {$id}"
);
```

No concatenes datos externos directamente en SQL.

---

# 44. Transacciones

Utiliza la API existente:

```php
$this->conn->autocommit(false);

$result = $this->conn->execute(
    $sql,
    $params
);

if ($result) {
    $this->conn->commit();
} else {
    $this->conn->rollback();
}
```

No implementes manejo de transacciones directamente sobre `mysqli`, PDO u otro driver cuando trabajes mediante `DBConnection`.

---

# 45. Frontend

El frontend estándar de ATUSAN utiliza:

```text
HTML
CSS
JavaScript
```

No agregues automáticamente:

```text
React
Vue
Angular
Svelte
Bootstrap
Tailwind
jQuery
TypeScript
```

La existencia o incorporación de estas tecnologías debe estar definida en `STACK.md` o en el código existente.

Si un proyecto ya utiliza una librería, conserva su convención mientras no exista una solicitud de migración.

---

# 46. JavaScript

Prefiere JavaScript moderno cuando sea compatible con el proyecto.

Para nuevo código puede utilizarse:

```javascript
const
let
fetch
async
await
Promise
class
modules
```

No conviertas automáticamente código existente de jQuery a JavaScript nativo.

No agregues una dependencia para operaciones que puedan resolverse de forma simple con APIs nativas.

---

# 47. Fetch y AJAX

Para peticiones JSON:

```javascript
const response = await fetch(url, {
    method: 'POST',
    headers: {
        'Accept': 'application/json',
        'Content-Type': 'application/json',
        'X-Requested-With': 'XMLHttpRequest'
    },
    body: JSON.stringify(data)
});
```

Cuando exista CSRF:

```javascript
headers['X-CSRF-TOKEN'] = csrfToken;
```

Cuando exista Bearer:

```javascript
headers['Authorization'] = `Bearer ${token}`;
```

Verifica siempre:

```javascript
response.ok
```

y maneja correctamente respuestas no exitosas.

---

# 48. HTML

Mantén HTML claro y semántico.

Respeta el sistema de layout, componentes y clases CSS existente.

No reestructures completamente una vista para resolver un cambio local.

No agregues estilos inline cuando puedan mantenerse en la sección CSS correspondiente.

---

# 49. CSS

Mantén las convenciones existentes de ATUSAN y de la aplicación.

Evita duplicación de reglas y especificidad innecesaria.

No reemplaces el sistema CSS existente por otro framework salvo requerimiento explícito.

---

# 50. Templates

ATUSAN dispone de:

```php
Atusan\Template\Template
```

Los módulos utilizan este sistema para renderizar su contenido.

`Response::view()` delega en `Template::render()`.

No invoques manualmente `include` o `require` para construir una vista cuando el flujo normal del módulo y `Response::view()` sea suficiente.

---

# 51. Componentes

ATUSAN dispone de componentes propios bajo:

```php
Atusan\Components
```

Entre las clases existentes pueden encontrarse componentes como:

```text
DataForm
DataGrid
DataMultiForm
DataTree
HTML
Modal
Navbar
Panel
Sidebar
Subnavbar
TabGroup
ButtonGroup
```

Antes de implementar manualmente una funcionalidad visual equivalente, revisa si ATUSAN ya dispone del componente.

No inventes propiedades o métodos de un componente.

Revisa primero su implementación real.

---

# 52. Logging

Utiliza:

```php
Atusan\Log\Log
```

cuando el proyecto requiera logging y la clase existente cubra la necesidad.

No dejes como mecanismo permanente:

```php
var_dump()
print_r()
echo
die()
```

para depuración.

Nunca registres:

* passwords;
* tokens completos;
* credenciales;
* secretos;
* información sensible innecesaria.

---

# 53. Errores

ATUSAN registra su sistema de manejo de errores durante `Bootstrap::app()`.

Utiliza los mecanismos existentes de `Response` y las excepciones del framework antes de crear un segundo sistema de errores.

En producción no expongas innecesariamente:

* stack traces;
* rutas internas;
* consultas SQL;
* credenciales;
* tokens;
* variables de entorno.

---

# 54. ATUSAN CLI

Antes de crear manualmente una aplicación, módulo, módulo anidado, modelo o servicio, verifica si puede generarse mediante:

```text
atusan_cli
```

Los comandos oficiales registrados actualmente son:

```text
make:app
make:module
make:nested
make:model
make:service
publish
```

No inventes comandos inexistentes.

---

# 55. Crear aplicación

Comando:

```bash
atusan_cli make:app --name Nombre
```

Este comando debe utilizarse principalmente cuando se requiere una aplicación adicional.

El caso normal de un proyecto ATUSAN no requiere múltiples aplicaciones.

No crees una nueva aplicación salvo que exista una necesidad explícita.

---

# 56. Publicar aplicación base

El proyecto ATUSAN se distribuye inicialmente con:

```text
app/newapp
```

Para publicar la aplicación base utiliza:

```bash
atusan_cli publish --name NombreAplicacion
```

Este comando debe:

* renombrar `newapp`;
* configurar la nueva aplicación;
* establecerla como aplicación activa;
* mantener sincronizado `atusan.json` cuando este archivo exista.

Después de publicar, `newapp` no debe permanecer como aplicación activa.

---

# 57. Crear módulo

Comando:

```bash
atusan_cli make:module \
    --app NombreAplicacion \
    --name NombreModulo
```

Cuando el CLI permita omitir `--app`, debe utilizarse la aplicación activa.

El scaffolding genera los archivos correspondientes al módulo, incluyendo:

```text
Controller.php
Components.xml
View.php
```

No crees manualmente un módulo si el CLI puede generarlo y posteriormente adaptarlo.

---

# 58. Crear módulo anidado

Comando:

```bash
atusan_cli make:nested \
    --app NombreAplicacion \
    --name NombreModulo
```

El resultado está preparado para trabajar con el flujo de `ModuleNested`.

---

# 59. Crear modelo

Comando:

```bash
atusan_cli make:model \
    --app NombreAplicacion \
    --name CustomerModel
```

Cuando exista una aplicación activa y el CLI permita omitir `--app`, utiliza dicha aplicación.

Revisa el resultado generado antes de agregar métodos específicos.

---

# 60. Crear servicio

Comando:

```bash
atusan_cli make:service \
    --app NombreAplicacion \
    --name CustomerService
```

El servicio generado extiende:

```php
Atusan\Controller\Service
```

Utilízalo como punto de partida para endpoints backend.

---

# 61. Scaffolding como fuente de convenciones

Las plantillas de `atusan_cli` representan las convenciones oficiales para nuevo código ATUSAN.

Cuando exista duda sobre cómo debe estructurarse un nuevo:

```text
Module
ModuleNested
Model
Service
Application
```

consulta primero el resultado generado por `atusan_cli`.

No copies patrones de otros frameworks.

---

# 62. Composer y autoload

El core de ATUSAN utiliza PSR-4:

```json
{
  "autoload": {
    "psr-4": {
      "Atusan\\": "src/"
    }
  }
}
```

El namespace de aplicación `App\` es registrado por el bootstrap sobre el directorio de la aplicación activa.

No crees un autoloader adicional para clases normales del framework o la aplicación.

Cuando se modifique configuración de Composer puede ser necesario ejecutar:

```bash
composer dump-autoload
```

No modifiques manualmente archivos generados dentro de:

```text
vendor/composer/
```

---

# 63. Dependencias externas

Antes de agregar un paquete Composer verifica si:

* ATUSAN ya proporciona la funcionalidad;
* PHP proporciona la funcionalidad de forma nativa;
* el proyecto ya tiene una solución equivalente;
* `STACK.md` autoriza o define la dependencia.

No agregues una dependencia únicamente porque sea habitual en otros proyectos.

No reemplaces APIs de ATUSAN por librerías externas sin una necesidad concreta.

---

# 64. Modificación del framework

El código de la aplicación y ATUSAN Core deben mantenerse separados.

En una aplicación instalada mediante Composer:

```text
vendor/
```

no debe modificarse directamente.

Si una funcionalidad requiere un cambio en ATUSAN Core, identifica claramente que se trata de una modificación del framework y realiza el cambio en el repositorio correspondiente.

No parches `vendor/` como solución permanente.

---

# 65. Seguridad de entrada

Toda entrada externa debe considerarse no confiable.

Esto incluye:

```text
GET
POST
JSON
URI parameters
headers
Authorization
archivos
cookies
datos enviados desde JavaScript
```

Valida los datos en backend incluso cuando ya hayan sido validados en frontend.

No confíes únicamente en validación JavaScript.

---

# 66. Secretos y configuración

Credenciales y secretos deben mantenerse fuera del código fuente.

Utiliza variables de entorno para:

```text
base de datos
tokens privados
credenciales
claves externas
configuración dependiente del entorno
```

No escribas secretos reales en:

```text
PHP
JavaScript
HTML
repositorio Git
AGENTS.md
STACK.md
atusan.json
```

---

# 67. Antes de modificar código

Antes de implementar una tarea, el agente debe seguir esta secuencia:

1. Identificar la aplicación activa.
2. Identificar el módulo o servicio involucrado.
3. Leer `STACK.md` si existe.
4. Leer `atusan.json` si existe.
5. Revisar `Route.php`.
6. Revisar controlador, modelo, servicio, vista o componente relacionado.
7. Identificar las APIs existentes de ATUSAN que resuelven la necesidad.
8. Realizar el cambio mínimo necesario.
9. Verificar efectos sobre rutas, Request, Response, seguridad y persistencia.
10. Mantener compatibilidad con PHP 8.2+.
11. Evitar cambios no relacionados con la tarea.

---

# 68. Creación de nuevas funcionalidades

Para una nueva funcionalidad determina primero si corresponde a:

```text
Module
ModuleNested
Service
Model
Component
Route
```

Utiliza el CLI cuando exista scaffolding para ese tipo.

Después adapta únicamente lo necesario.

No agregues automáticamente capas como:

```text
Repository
UseCase
DTO
Entity
Domain
Handler
Manager
Provider
Facade
```

salvo que el proyecto ya utilice esas capas o exista una necesidad explícita.

---

# 69. Convenciones de código PHP

Todo nuevo código debe ser compatible con PHP 8.2 o superior.

Utiliza tipado cuando mejore claridad y seguridad.

Mantén los namespaces coherentes con la ubicación de la clase.

Prefiere código simple y explícito.

No conviertas archivos existentes a un nuevo estilo completo si la tarea únicamente requiere modificar una pequeña sección.

Mantén compatibilidad con las APIs públicas existentes.

---

# 70. Convenciones de JavaScript

Para nuevo código utiliza preferentemente:

```javascript
const
let
async
await
fetch
```

Evita variables globales nuevas cuando no sean necesarias.

Mantén compatibilidad con la arquitectura JavaScript existente de ATUSAN y del módulo.

No agregues herramientas de build, npm o frameworks frontend si `STACK.md` no los requiere.

---

# 71. Compatibilidad

Cada cambio debe procurar conservar:

```text
rutas existentes
contratos de métodos
estructura de respuesta
componentes existentes
sesiones
seguridad
base de datos
frontend
API pública del framework
```

Antes de cambiar la firma de un método público, busca primero sus usos.

Antes de mover una clase, comprueba namespaces y dependencias.

Antes de renombrar una ruta, comprueba llamadas JavaScript, enlaces y redirecciones.

---

# 72. Lo que un agente NO debe asumir

No asumas que:

```text
existe Laravel
existe Symfony
existe CodeIgniter
existe un ORM
existe Doctrine
existe Eloquent
existe React
existe Vue
existe Bootstrap
existe Tailwind
existe jQuery
todos los endpoints son REST
todas las peticiones AJAX son JSON
todos los módulos son controladores simples
todas las vistas están en app/Views
los controladores están en app/Controllers
JWT es obligatorio para Bearer
un proyecto requiere múltiples aplicaciones
```

Comprueba primero el proyecto, la aplicación activa y `STACK.md`.

---

# 73. No inventar API

Nunca inventes métodos, propiedades, clases o comandos de ATUSAN.

Incorrecto:

```php
$request->input('name');
$response->success($data);
Route::resource(...);
atusan_cli make:controller ...
```

si esas APIs no existen.

Utiliza las APIs reales.

Por ejemplo:

```php
$this->request->get('name');

$this->response->json($data);

Route::get(...);

atusan_cli make:module ...
```

Cuando una capacidad no exista, implementa la solución más simple compatible con ATUSAN.

---

# 74. STACK.md

`STACK.md` define las decisiones tecnológicas particulares de cada proyecto.

Puede especificar, entre otros:

```text
base de datos
frontend
librerías JavaScript
CSS
autenticación
APIs
servidor
servicios externos
convenciones específicas
```

No modifiques automáticamente el stack.

Si `STACK.md` indica una tecnología concreta, utilízala mientras sea compatible con ATUSAN 3.

---

# 75. atusan.json

`atusan.json` describe el proyecto ATUSAN actual.

Debe utilizarse para conocer información como:

```text
framework
versión
PHP mínimo
aplicaciones registradas
aplicación activa
rutas principales
frontend
archivos para agentes
```

Un proyecto puede contener:

```json
{
  "applications": {
    "active": "ventas",
    "items": {
      "ventas": {
        "namespace": "App",
        "directory": "app/ventas",
        "routing": "Route.php"
      }
    }
  }
}
```

La propiedad:

```text
applications.active
```

identifica la aplicación sobre la cual debe trabajar normalmente el agente.

La colección:

```text
applications.items
```

representa las aplicaciones conocidas por el proyecto.

No crees una aplicación adicional únicamente porque `applications.items` soporte múltiples elementos.

En el caso normal debe existir una sola aplicación funcional.

---

# 76. Sincronización de aplicaciones

La aplicación activa definida en la configuración del proyecto y en `atusan.json` debe permanecer sincronizada.

Después de ejecutar:

```bash
atusan_cli publish --name ventas
```

el manifiesto debe representar:

```json
{
  "applications": {
    "active": "ventas",
    "items": {
      "ventas": {
        "namespace": "App",
        "directory": "app/ventas",
        "routing": "Route.php"
      }
    }
  }
}
```

No mantengas `newapp` registrado después de que haya sido reemplazado por la aplicación publicada.

Si existen aplicaciones adicionales legítimas, no las elimines al publicar otra aplicación.

---

# 77. Regla de decisión

Antes de implementar cualquier funcionalidad, responde internamente esta pregunta:

> ¿ATUSAN 3 ya proporciona una API, componente, clase, comando o convención para resolver esta necesidad?

Si la respuesta es sí, utiliza ATUSAN.

Si la respuesta es no, utiliza primero las capacidades nativas de PHP, JavaScript, HTML o CSS.

Introduce una dependencia externa únicamente cuando exista una necesidad concreta y sea compatible con `STACK.md`.

---

# 78. Objetivo final

El objetivo del agente no es rediseñar ATUSAN 3.

El objetivo es desarrollar aplicaciones sobre ATUSAN 3 de forma:

```text
compatible
simple
segura
mantenible
predecible
localizada
```

Cuando existan varias soluciones válidas, prefiere aquella que:

```text
utilice APIs existentes de ATUSAN
requiera menos cambios
introduzca menos dependencias
mantenga compatibilidad
sea fácil de entender
sea fácil de mantener
```

La solución correcta para un proyecto ATUSAN 3 debe sentirse como código ATUSAN 3, no como código de otro framework adaptado artificialmente.
