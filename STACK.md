# STACK.md

Este archivo define las decisiones tecnológicas específicas de este proyecto.

Debe utilizarse junto con:

* `AGENTS.md`
* `atusan.json`
* `composer.json`

`AGENTS.md` define cómo trabajar con ATUSAN 3.

`atusan.json` describe estructuralmente el proyecto.

`STACK.md` define el stack tecnológico elegido para esta aplicación.

---

# 1. Backend

## Framework

* Framework: ATUSAN 3
* Lenguaje: PHP
* Versión mínima: PHP 8.2+
* Arquitectura: MVC modular
* Dependencias: Composer
* Autoload: PSR-4

El backend debe desarrollarse utilizando las APIs y convenciones de ATUSAN 3.

No introducir otro framework backend.

---

# 2. Aplicación

Aplicación activa:

```text
newapp
```

La aplicación `newapp` es temporal durante la creación inicial del proyecto.

Después de ejecutar:

```bash
atusan_cli publish --name nombre_aplicacion
```

este archivo debe actualizarse con el nombre real de la aplicación.

Ejemplo:

```text
Aplicación activa: ventas
```

Un proyecto utiliza normalmente una sola aplicación principal.

Las aplicaciones adicionales deben agregarse únicamente cuando exista una necesidad funcional clara.

---

# 3. Frontend

El frontend base utiliza:

* HTML5
* CSS
* JavaScript

Framework frontend:

```text
Ninguno
```

Herramienta de build:

```text
Ninguna
```

No agregar automáticamente React, Vue, Angular, Svelte u otro framework.

No agregar Bootstrap, Tailwind u otro framework CSS salvo que se defina expresamente en este archivo.

---

# 4. JavaScript

Para nuevo código se recomienda utilizar:

* JavaScript moderno
* `const`
* `let`
* `async`
* `await`
* `fetch`
* Promises

No introducir TypeScript salvo decisión explícita del proyecto.

Si el proyecto utiliza jQuery en código existente, mantener compatibilidad.

No realizar migraciones de jQuery a JavaScript nativo salvo que se solicite expresamente.

---

# 5. Comunicación frontend-backend

Las peticiones HTTP desde JavaScript deben utilizar preferentemente:

```text
fetch
```

Para peticiones JSON:

```http
Accept: application/json
Content-Type: application/json
X-Requested-With: XMLHttpRequest
```

Cuando exista protección CSRF:

```http
X-CSRF-TOKEN: <token>
```

Cuando exista autenticación Bearer:

```http
Authorization: Bearer <token>
```

El backend debe utilizar:

```text
Atusan\Http\Request\Request
Atusan\Http\Response\Response
```

para procesar la petición y generar la respuesta.

---

# 6. API

Tipo de API:

```text
Por definir
```

Opciones posibles:

```text
REST
Endpoints internos
AJAX
Mixta
```

No asumir arquitectura REST si no se define expresamente.

Las rutas deben utilizar el sistema oficial:

```text
Atusan\Route\Route
```

Métodos HTTP soportados:

* GET
* POST
* PUT
* PATCH
* DELETE

---

# 7. Respuestas

Formato principal:

```text
HTML y JSON
```

El backend utiliza `Response` para generar respuestas.

Para endpoints JSON mantener una estructura consistente.

Ejemplo conceptual:

```json
{
  "status": "ok",
  "message": "",
  "data": {}
}
```

No crear formatos de respuesta alternativos sin necesidad.

---

# 8. Base de datos

Motor:

```text
Por definir
```

Opciones habituales:

```text
MySQL
MariaDB
```

Acceso a datos:

```text
Atusan\Model\ModelBase
Atusan\Persistence\DBConnection
```

ORM:

```text
No
```

No introducir ORM salvo decisión explícita en este archivo.

Las consultas deben utilizar parámetros.

No concatenar valores externos directamente en SQL.

---

# 9. Variables de entorno

La configuración sensible debe almacenarse mediante variables de entorno.

Variables base de base de datos:

```text
DB_DRIVER
DB_HOST
DB_USER
DB_PASS
DB_NAME
```

No almacenar credenciales en:

* PHP
* JavaScript
* `atusan.json`
* `STACK.md`
* repositorio Git

---

# 10. Autenticación

Mecanismo:

```text
Por definir
```

Opciones soportadas por ATUSAN:

* sesión
* Bearer Token
* Basic Authentication

JWT:

```text
No requerido por defecto
```

No implementar JWT automáticamente.

Si el proyecto utiliza tokens fijos por cliente, mantener ese mecanismo.

---

# 11. Autorización

Estrategia:

```text
Por definir
```

Para rutas condicionadas por sesión utilizar:

```php
Route::middleware(...)
```

No introducir un sistema externo de autorización sin necesidad.

---

# 12. Sesiones

Mecanismo:

```text
Atusan\Session\Session
```

El manejo de sesión debe permanecer centralizado en ATUSAN.

No utilizar directamente `session_start()` dentro de controladores normales.

Almacenamiento de sesión:

```text
Por definir según entorno
```

Ejemplos:

```text
filesystem
Redis
```

---

# 13. Seguridad

Utilizar:

```text
Atusan\Security\SecurityMiddleware
```

Funciones disponibles:

* seguridad HTTP
* validación de método HTTP
* sesiones
* CSRF
* Bearer
* Basic Authentication

CSRF:

```text
Habilitado cuando corresponda
```

Encabezado:

```http
X-CSRF-TOKEN
```

Campo alternativo:

```text
csrf_token
```

---

# 14. Módulos

Las interfaces visuales deben implementarse mediante:

```text
Atusan\Controller\Module
```

Los módulos anidados deben utilizar:

```text
Atusan\Controller\ModuleNested
```

La generación debe realizarse preferentemente mediante:

```bash
atusan_cli make:module
atusan_cli make:nested
```

No crear controladores visuales genéricos fuera del modelo de módulos salvo necesidad explícita.

---

# 15. Servicios

Las operaciones backend sin interfaz visual deben utilizar:

```text
Atusan\Controller\Service
```

La generación debe realizarse preferentemente mediante:

```bash
atusan_cli make:service
```

---

# 16. Modelos

Los modelos deben extender:

```text
Atusan\Model\ModelBase
```

La generación debe realizarse preferentemente mediante:

```bash
atusan_cli make:model
```

Los modelos deben encargarse principalmente de:

* persistencia
* consultas
* procedimientos almacenados
* transacciones
* operaciones relacionadas con datos

No generar HTML desde modelos.

---

# 17. Componentes

Antes de crear manualmente una funcionalidad visual, revisar los componentes existentes de ATUSAN.

Entre ellos pueden existir:

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

No reemplazar componentes existentes sin necesidad.

---

# 18. Templates y vistas

Los módulos deben utilizar el sistema de templates de ATUSAN.

Clase principal:

```text
Atusan\Template\Template
```

Las vistas pueden contener:

* HTML
* CSS
* JavaScript

No introducir motores de templates externos salvo decisión explícita.

---

# 19. Logging

Sistema:

```text
Atusan\Log\Log
```

No utilizar permanentemente:

```text
var_dump
print_r
echo
die
```

para depuración.

No registrar información sensible.

---

# 20. Servidor web

Entorno de desarrollo:

```text
Por definir
```

Opciones habituales:

```text
Apache
NGINX
```

Entorno de producción:

```text
Por definir
```

La configuración del servidor debe respetar el entry point:

```text
public/index.php
```

---

# 21. Dependencias externas

Política:

```text
Mínimas
```

Antes de agregar una dependencia:

1. verificar si ATUSAN ya proporciona la funcionalidad;
2. verificar si PHP o JavaScript nativo la proporciona;
3. revisar si el proyecto ya tiene una solución;
4. justificar técnicamente la dependencia.

No agregar paquetes únicamente por conveniencia.

---

# 22. Herramientas de desarrollo

Editor recomendado:

```text
Visual Studio Code
```

Agente IA:

```text
OpenCode
```

Gestión de dependencias:

```text
Composer
```

Control de versiones:

```text
Git
```

---

# 23. Archivos de referencia para agentes

Antes de generar o modificar código, revisar:

```text
AGENTS.md
STACK.md
atusan.json
composer.json
Route.php
```

Y posteriormente los archivos relacionados con la funcionalidad solicitada.

---

# 24. Convenciones del proyecto

Las decisiones definidas en este archivo tienen prioridad sobre recomendaciones genéricas.

No modificar automáticamente:

* framework backend;
* motor de base de datos;
* mecanismo de autenticación;
* librerías frontend;
* sistema CSS;
* arquitectura de API;
* servidor;
* almacenamiento de sesiones.

Cuando una decisión esté marcada como:

```text
Por definir
```

no asumir una tecnología sin necesidad.

Si la tarea requiere esa decisión, proponer una alternativa compatible con ATUSAN 3 antes de introducirla.

---

# 25. Stack inicial

El stack mínimo oficial de un proyecto nuevo ATUSAN 3 es:

```text
Backend
  PHP 8.2+
  ATUSAN 3
  Composer

Frontend
  HTML5
  CSS
  JavaScript

Arquitectura
  MVC modular

Routing
  ATUSAN Route

HTTP
  ATUSAN Request
  ATUSAN Response

Persistencia
  ATUSAN ModelBase
  ATUSAN DBConnection

Seguridad
  ATUSAN SecurityMiddleware
  ATUSAN Session

Herramientas
  Git
  Visual Studio Code
  OpenCode
```

Cualquier tecnología adicional debe definirse explícitamente en este archivo.

---

# 26. Estado de decisiones

| Área                 | Decisión                  |
| -------------------- | ------------------------- |
| Backend              | ATUSAN 3                  |
| PHP                  | 8.2+                      |
| Arquitectura         | MVC modular               |
| Frontend             | HTML + CSS + JavaScript   |
| Framework frontend   | Ninguno                   |
| Build frontend       | Ninguno                   |
| Base de datos        | Por definir               |
| ORM                  | No                        |
| API                  | Por definir               |
| Autenticación        | Por definir               |
| Autorización         | Por definir               |
| Sesiones             | ATUSAN Session            |
| CSRF                 | ATUSAN SecurityMiddleware |
| Servidor desarrollo  | Por definir               |
| Servidor producción  | Por definir               |
| Control de versiones | Git                       |
| Agente IA            | OpenCode                  |

Actualizar esta tabla cuando se tome una decisión tecnológica relevante.
