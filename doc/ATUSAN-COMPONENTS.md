# ATUSAN 3 — Components Reference

Este documento es la referencia práctica para utilizar los Components de ATUSAN 3 desde `Components.xml`, PHP y JavaScript.

Debe utilizarse junto con `AGENTS.md`.

La regla general es:

```text
Components.xml
      ↓
Module
      ↓
findComponentById()
      ↓
Component PHP
      ↓
View
      ↓
$component->write()
      ↓
HTML + instancia JavaScript
```

Los ejemplos de este documento siguen los patrones de la aplicación de ejemplos de ATUSAN y las librerías JavaScript estándar del framework.

---

# 1. Reglas generales

## 1.1 Declarar primero en Components.xml

Los Components de un Module se declaran en `Components.xml`.

Ejemplo:

```xml
<DataForm id="form1" route="customers/save">
  <ControlText id="name" label="Nombre:" />
  <ControlSelect id="type" label="Tipo:" />
</DataForm>
```

No inventes atributos XML ni Controls. Revisa un ejemplo existente y la implementación real del Component.

## 1.2 Obtener el Component desde PHP

Usa:

```php
$this->findComponentById('componentId');
```

`findComponentById()` realiza una búsqueda recursiva y retorna la instancia del Component o `null`.

Ejemplo:

```php
use Atusan\Components\DataForm;

protected DataForm $form1;

protected function initializeComponents(): void
{
    $this->form1 = $this->findComponentById('form1');
}
```

Cuando ya se tiene un Component padre, también puede buscarse dentro de él si su API lo permite:

```php
$this->tab1 = $this->findComponentById('tab1');
$this->grid1 = $this->tab1->findComponentById('grid1');
```

## 1.3 Renderizar desde la View

Usa `write()` sobre la instancia:

```php
<?= $this->form1->write() ?>
```

No reproduzcas manualmente el HTML interno de un Component.

## 1.4 Instancia JavaScript automática

La plantilla PHP genera una variable JavaScript global con el mismo `id` del Component.

Ejemplo XML:

```xml
<DataGrid id="grid1">
```

produce conceptualmente:

```javascript
var grid1 = new DataGrid('grid1', 'NombreModulo');
```

La View puede usar directamente:

```javascript
grid1.onClick = function(ar) {
    this.selectRow(ar.row);
};
```

No crees manualmente una segunda instancia JavaScript del Component.

## 1.5 API cliente antes que DOM directo

Antes de usar `querySelector`, `innerHTML` o manipulación DOM interna:

1. revisa los métodos de la clase JavaScript del Component;
2. revisa sus eventos/callbacks;
3. usa la API oficial cuando exista;
4. manipula DOM directamente solo si la API no cubre la necesidad.

## 1.6 Ajuste de tamaño

Los Components derivados de la clase base cliente pueden ofrecer:

```javascript
fitToParentHeight(margin)
fitToParentWidth()
```

Un patrón común es registrar el resize desde el Module:

```javascript
DataGridSample.onOpen = function() {
    this.registerResizeListener();
};

DataGridSample.onResize = function() {
    grid1.fitToParentHeight(10);
};
```

---

# 2. DataForm

`DataForm` construye formularios a partir de Controls declarados en XML.

## 2.1 Ejemplo XML

```xml
<DataForm id="form1" route="dataforms/basicos">
  <ControlText id="texto" label="Texto:" />
  <ControlPassword id="contrasena" label="Contraseña:" />
  <ControlDate id="fecha" label="Fecha:" />
  <ControlTime id="hora" label="Hora:" />
  <ControlTextArea id="redaccion" label="Redacción:" />

  <ButtonGroupBar id="form1bg">
    <Button id="btnEnviar1" text="Enviar" html:type="submit" />
    <Button id="btnLimpiar1" text="Limpiar" html:type="reset" />
  </ButtonGroupBar>
</DataForm>
```

Otros Controls observados en los ejemplos:

```text
ControlAutocomplete
ControlSelect
ControlCheck
ControlSwitch
ControlRadio
ControlFile
```

## 2.2 PHP

```php
use Atusan\Components\DataForm;
use Atusan\Security\SecurityMiddleware;

protected DataForm $form1;

protected function initializeComponents(): void
{
    $this->form1 = $this->findComponentById('form1');
}

public function index()
{
    $this->initializeComponents();

    $csrf = SecurityMiddleware::generateCsrf();
    $this->form1->setCsrf($csrf);

    $this->form1->import([
        'texto' => 'Some text',
        'fecha' => date('Y-m-d')
    ]);

    $this->response->view($this);
}
```

Métodos PHP relevantes observados en la API:

```php
import(array $data)
getData()
rowsCount()
getItem(string $name)
setItem(string $name, mixed $value)
setCsrf(string $code)
feedList(string $name, array $data)
typeControl(string $name, ?string $type)
enableControl(string $name, bool $enable)
filesOf(string $name)
writeControl(string $name)
build()
make()
write()
```

### Alimentar listas

```php
$list = [
    ['text' => 'Opción A', 'value' => 'A'],
    ['text' => 'Opción B', 'value' => 'B']
];

$this->form1->feedList('listado', $list);
```

## 2.3 View

```php
<?= $this->form1->write() ?>
```

## 2.4 JavaScript

Métodos relevantes:

```javascript
load()
disableItem(control)
enableItem(control)
feedList(control, list)
inflate(content)
getData()
getItem(control)
setItem(control, value)
setFocus(control)
findByName(control)
reset()
lists()
```

Callbacks relevantes:

```javascript
onInput(ar)
onChange(ar)
onKeydown(ar)
onPopulateDone()
onSubmitDone(rs)
onSubmitFail(err)
```

Ejemplo:

```javascript
form1.onChange = function(ar) {
    if (ar.control === 'listado') {
        console.log(this.getItem('listado'));
    }
};

form1.onSubmitDone = function(rs) {
    console.log(rs);
};
```

## 2.5 Regla de uso

Usa `DataForm` para formularios ATUSAN antes de construir manualmente inputs y lógica de envío equivalentes.

---

# 3. DataGrid

`DataGrid` presenta y edita información tabular.

## 3.1 Ejemplo XML

```xml
<DataGrid id="grid1">
  <Thead>
    <Th style="width:40px;">-</Th>
    <Th>Nombre</Th>
    <Th>Puesto</Th>
    <Th>Fecha</Th>
  </Thead>

  <Tbody>
    <Td></Td>
    <ControlData id="nombre" />
    <ControlData id="puesto" />
    <ControlDate id="fecha" />
  </Tbody>
</DataGrid>
```

El ejemplo oficial también muestra:

```text
ControlCheck
ControlSwitch
ControlText
ControlSelect
ControlTime
ControlActions
ControlIcon
ControlCase
ControlMenu
```

`DataGrid` también puede cargar un layout XML externo:

```xml
<DataGrid id="grid1" layout="layouts/datagrid-layout" />
```

## 3.2 PHP

```php
use Atusan\Components\DataGrid;

protected DataGrid $grid1;

public function index()
{
    $this->grid1 = $this->findComponentById('grid1');

    $this->grid1->import([
        [
            'nombre' => 'Gabriel',
            'puesto' => 'Programador',
            'fecha' => date('Y-m-d')
        ]
    ]);

    $this->response->view($this);
}
```

Métodos PHP relevantes:

```php
import(array $data)
getData()
rowsCount()
build()
make()
write()
```

Los Controls pueden utilizar métodos resolver del Module.

Ejemplo:

```xml
<ControlActions id="actions" resolve="showActionBar">
  <Action id="ac_edit" resolve="activateAction" />
</ControlActions>
```

```php
public function showActionBar(string $barName, array $data)
{
    return true;
}

public function activateAction(string $actionName, array $data)
{
    return $actionName === 'ac_edit';
}
```

## 3.3 View

```php
<?= $this->grid1->write() ?>
```

## 3.4 JavaScript

Métodos relevantes:

```javascript
load()
inflate(content)
getData(row)
getItem(row, control)
setItem(row, control, value)
selectRow(row)
reset()
fitToParentHeight(margin)
fitToParentWidth()
```

Callbacks relevantes:

```javascript
onClick(ar)
onChange(ar)
onInput(ar)
onMenuOptionClick(ar)
onPopulateDone()
```

Ejemplo:

```javascript
grid1.onClick = function(ar) {
    this.selectRow(ar.row);
    console.log(ar);
};

grid1.onChange = ar => console.log(ar);
grid1.onInput = ar => console.log(ar);
grid1.onMenuOptionClick = ar => console.log(ar);
```

## 3.5 Regla de uso

No construyas filas HTML manualmente cuando `import()` y los Controls de `DataGrid` cubran el requerimiento.

---

# 4. DataTree

`DataTree` representa información jerárquica por niveles.

## 4.1 Ejemplo XML

```xml
<DataTree id="tree1" title="DATA TREE">
  <Level model="App\Models\Tree1Model::Empresas">
    <ControlCaret />
    <ControlData id="nomEmpresa" />
    <ControlCheck id="check" />
  </Level>

  <Level model="App\Models\Tree1Model::Sucursales">
    <ControlNoCaret />
    <ControlData id="nomSucursal" />
    <ControlActions id="options">
      <Action id="ac_edit" icon="fa fa-pencil" />
      <Action id="ac_delete" icon="fa fa-close" />
    </ControlActions>
  </Level>
</DataTree>
```

Cada `Level` puede asociarse a una fuente de datos/modelo.

## 4.2 PHP

```php
use Atusan\Components\DataTree;

protected DataTree $tree1;

public function index()
{
    $this->tree1 = $this->findComponentById('tree1');

    $this->tree1->retrieve(1, 1);

    $this->response->view($this);
}
```

Métodos relevantes observados:

```php
retrieve(...)
import(array $data)
getData()
rowsCount()
build()
make()
write()
```

## 4.3 JavaScript

```javascript
getData(index)
selectItem(index)
unselectItems()
fitToParentHeight(margin)
fitToParentWidth()
```

Callbacks:

```javascript
onClick(ar)
onChange(ar)
```

Ejemplo:

```javascript
tree1.onClick = function(ar) {
    this.selectItem(ar.index);
};
```

## 4.4 Regla de uso

Usa `DataTree` para datos padre/hijo antes de implementar listas anidadas manuales.

---

# 5. DataMultiForm

`DataMultiForm` extiende el modelo de `DataForm` para trabajar con formularios por pasos.

## 5.1 Estructura XML

Las secciones observadas son:

```text
TopForm
StepForm
BottomForm
```

Ejemplo:

```xml
<DataMultiForm id="formMulti" route="/datamultiforms/finish">
  <TopForm view="views/topform.view">
    <ControlSelect id="tramite" label="Trámite:">
      <Option value="" text="Selecciona una opción" />
      <Option value="A" text="Trámite A" />
    </ControlSelect>
  </TopForm>

  <StepForm>
    <ControlText id="nombre" label="Nombre:" />
  </StepForm>

  <StepForm>
    <ControlText id="paterno" label="Paterno:" />
  </StepForm>

  <BottomForm>
    <ControlSwitch id="guardar" label="Guardar:" />
  </BottomForm>
</DataMultiForm>
```

## 5.2 PHP

```php
use Atusan\Components\DataMultiForm;

protected DataMultiForm $formMulti;

public function index()
{
    $this->formMulti = $this->findComponentById('formMulti');
    $this->response->view($this);
}
```

Métodos propios relevantes:

```php
writeTopForms()
writeStepForms()
writeBottomForms()
import(array $data)
build()
make()
write()
```

También hereda funcionalidad de `DataForm`.

## 5.3 JavaScript

Métodos relevantes:

```javascript
showFormByIndex(index)
disableButton(btnName, disable)
updateStepMarks(index)
processButton(command)
moveTo(index, command)
forward()
backward()
finish()
submit()
```

Callbacks:

```javascript
allowContinue(ar)
onCurrentForm(ar)
onForwardDone(ar)
onBackwardDone(ar)
```

Además hereda callbacks de `DataForm`, como `onSubmitDone`.

Ejemplo de validación de navegación:

```javascript
formMulti.allowContinue = function(ar) {
    if (ar.current === 0 && ar.command === 'forward') {
        if (this.getItem('tramite') === '') {
            alert('Seleccione el trámite.');
            return false;
        }
    }

    return true;
};
```

## 5.4 Regla de uso

No implementes manualmente navegación de wizard/pasos cuando `DataMultiForm` cubra la necesidad.

---

# 6. Modal

`Modal` genera una ventana modal que puede contener HTML y otros Components.

## 6.1 XML

```xml
<Modal id="md1" title="Modal Sample">
  <p>Contenido...</p>

  <ButtonGroupRow id="actions">
    <Button id="btnAccept" text="Aceptar" />
  </ButtonGroupRow>
</Modal>
```

## 6.2 PHP

```php
use Atusan\Components\Modal;

protected Modal $md1;

public function index()
{
    $this->md1 = $this->findComponentById('md1');
    $this->response->view($this);
}
```

## 6.3 View

```php
<?= $this->md1->write() ?>
```

## 6.4 JavaScript

```javascript
openModal(params)
closeModal()
onOpen()
onClose()
```

Ejemplo:

```javascript
var btnOpcionA = () => md1.openModal();
```

## 6.5 Regla de uso

No agregues otra librería modal si `Modal` cubre el requerimiento.

---

# 7. Navbar

`Navbar` representa navegación principal horizontal.

## 7.1 XML

```xml
<Navbar id="navbar">
  <Left>
    <MenuItem id="mi_optionA" text="Opción A" />
    <MenuItem id="mi_optionB" text="Opción B" />
  </Left>

  <Right>
    <MenuItem id="mi_account" text="Editar" icon="fa fa-user" />
    <MenuItem id="mi_close" text="Cerrar" />
  </Right>
</Navbar>
```

Los ejemplos con submenús utilizan también `MenuContent` y `MenuView`.

## 7.2 PHP

```php
use Atusan\Components\Navbar;

protected Navbar $navbar;

public function index()
{
    $this->navbar = $this->findComponentById('navbar');

    $this->navbar->setTitle('NavbarTitle');
    $this->navbar->setText('mi_account', 'Item editado ...');
    $this->navbar->setIcon('mi_account', 'fa fa-pencil');

    $this->response->view($this);
}
```

Métodos PHP relevantes:

```php
setText($itemName, $newValue)
setIcon($itemName, $newValue)
setRoute($itemName, $newValue)
setView($itemName, $newValue)
setTitle($title)
```

## 7.3 JavaScript

```javascript
closeResponsiveView()
onMenuItemClick(item)
fitToParentHeight(margin)
fitToParentWidth()
```

`onMenuItemClick` es el callback principal de navegación.

---

# 8. Sidebar

`Sidebar` representa navegación lateral.

## 8.1 XML

```xml
<Sidebar id="sidebar">
  <Top>
    <MenuItem id="mi_index" text="Índice" route="/" />
    <MenuItem id="mi_optionA" text="Opción A" />
  </Top>

  <Bottom>
    <MenuItem id="mi_account" text="Usuario A" />
    <MenuItem id="mi_close" text="Cerrar" />
  </Bottom>
</Sidebar>
```

También soporta submenús mediante `MenuContent`.

## 8.2 PHP

```php
$this->sidebar = $this->findComponentById('sidebar');
$this->sidebar->setText('mi_account', 'Usuario B');
$this->sidebar->setIcon('mi_account', 'fa fa-user');
```

Comparte operaciones de menú con `Navbar`.

## 8.3 JavaScript

```javascript
closeResponsiveView()
onMenuItemClick(item)
fitToParentHeight(margin)
fitToParentWidth()
```

Ejemplo:

```javascript
sidebar.onMenuItemClick = function(ar) {
    if (ar.id === 'mi_index') {
        Atusan.openModule('/');
    }
};
```

---

# 9. Subnavbar

`Subnavbar` representa navegación secundaria horizontal.

## 9.1 XML

```xml
<Subnavbar id="navbar">
  <Left>
    <MenuItem id="mi_index" text="Indice" route="/" />

    <MenuContent id="mi_optionA" text="Opción A">
      <MenuItem id="mi_opcionA1" text="Opción A1" />
      <MenuItem id="mi_opcionA2" text="Opción A2" />
    </MenuContent>
  </Left>

  <Right>
    <MenuItem id="mi_close" text="Cerrar" />
    <MenuView id="mi_profile" text="Perfil" view="views/profile.view" />
  </Right>
</Subnavbar>
```

## 9.2 PHP

```php
$this->navbar = $this->findComponentById('navbar');
$this->navbar->setText('mi_close', 'Salir');
$this->navbar->setIcon('mi_optionB', 'fa fa-pencil');
```

Métodos PHP relevantes:

```php
setText(...)
setIcon(...)
setRoute(...)
setView(...)
setTitle(...)
```

## 9.3 JavaScript

```javascript
closeResponsiveView()
onMenuItemClick(item)
```

Ejemplo:

```javascript
navbar.onMenuItemClick = function(ar) {
    if (ar.id === 'mi_index') {
        Atusan.openModule('/');
    }
};
```

---

# 10. Panel

`Panel` divide el contenido en áreas `Left`, `Content` y `Right`.

## 10.1 XML básico

```xml
<Panel id="panel">
  <Left>
    <h2>Left</h2>
  </Left>

  <Content>
    <h2>Content</h2>
  </Content>

  <Right>
    <h2>Right</h2>
  </Right>
</Panel>
```

Las secciones pueden contener otros Components.

## 10.2 Panel dinámico

```xml
<Panel id="panel">
  <Left>
    <SideBar id="menu">
      <Top>
        <MenuItem id="mi_opcion1" text="Opción 1" route="/panel/option1" />
        <MenuItem id="mi_opcion2" text="Opción 2" route="/panel/option2" />
      </Top>
    </SideBar>
  </Left>

  <Content></Content>
</Panel>
```

## 10.3 PHP

```php
use Atusan\Components\Panel;

protected Panel $panel;

public function index()
{
    $this->panel = $this->findComponentById('panel');
    $this->response->view($this);
}
```

## 10.4 JavaScript

Método principal para carga dinámica:

```javascript
openModule(route, data)
```

Ejemplo:

```javascript
DynamicPanelSample.onOpen = () =>
    DynamicPanelSample.registerResizeListener();

DynamicPanelSample.onResize = () =>
    menu.fitToParentHeight(10);

menu.onMenuItemClick = item =>
    panel.openModule(item.route);
```

## 10.5 Regla de uso

Usa `Panel.openModule()` para cargar Modules en el panel cuando ese patrón resuelva la necesidad; no implementes una infraestructura paralela de `fetch + innerHTML`.

---

# 11. TabGroup

`TabGroup` administra un conjunto de pestañas y sus contenidos.

## 11.1 XML

```xml
<TabGroup id="tab1">
  <TabGroupContent id="page1" text="Grid 1">
    <DataGrid id="grid1">
      <Thead>
        <Th>Nombre</Th>
      </Thead>
      <Tbody>
        <ControlData id="nombre" />
      </Tbody>
    </DataGrid>
  </TabGroupContent>

  <TabGroupContent id="page2" text="Grid 2">
    <DataGrid id="grid2">
      ...
    </DataGrid>
  </TabGroupContent>
</TabGroup>
```

## 11.2 PHP y Components anidados

```php
use Atusan\Components\DataGrid;
use Atusan\Components\TabGroup;

protected TabGroup $tab1;
protected DataGrid $grid1;

protected function initializeComponents(): void
{
    $this->tab1 = $this->findComponentById('tab1');
    $this->grid1 = $this->tab1->findComponentById('grid1');
}
```

También puede utilizarse la búsqueda recursiva del Module cuando resulte más simple:

```php
$this->grid1 = $this->findComponentById('grid1');
```

## 11.3 JavaScript

Callbacks:

```javascript
onClick(ar)
onChange(ar)
```

Propiedades y métodos relevantes:

```javascript
current
openModule(route, data)
```

Ejemplo:

```javascript
tab1.onClick = function(ar) {
    console.log(ar);
};

tab1.onChange = function(ar) {
    const grids = {
        page1: grid1,
        page2: grid2
    };

    grids[ar.current].fitToParentHeight(10);
};
```

## 11.4 Tabs dinámicos con Modules

`openModule()` puede cargar Modules anidados dinámicamente como nuevas pestañas.

```javascript
mainTab.openModule('/ruta/modulo');
```

El Component administra:

```text
creación de botón
creación de contenido
selección de tab
cierre de tabs dinámicos
adjuntar el Module cargado
onChange
```

No implementes manualmente estas operaciones cuando `openModule()` cubra el caso.

---

# 12. TabGroupContent

`TabGroupContent` representa el contenido de una pestaña de `TabGroup`.

El objeto JavaScript se crea internamente como propiedad del `TabGroup`.

Por ejemplo, para:

```xml
<TabGroup id="tab1">
  <TabGroupContent id="page1" text="Página 1">
    ...
  </TabGroupContent>
</TabGroup>
```

puede accederse conceptualmente mediante:

```javascript
tab1.page1
```

Métodos relevantes:

```javascript
show()
hide()
update(content)
fitToParentHeight(margin)
fitToParentWidth()
```

No crees manualmente una instancia `TabGroupContent`.

---

# 13. Components anidados y búsqueda recursiva

Una característica importante del sistema es que los Components pueden contener otros Components.

Ejemplo:

```text
TabGroup
└── TabGroupContent
    └── DataGrid
```

O:

```text
Panel
├── Left
│   └── Sidebar
└── Content
```

La búsqueda oficial es:

```php
$this->findComponentById('id');
```

Cuando se conoce el padre:

```php
$this->tab1->findComponentById('grid1');
```

Prefiere estas búsquedas sobre recorridos manuales de las colecciones internas.

---

# 14. Components y Modules anidados

ATUSAN permite incluir `ModuleNested` dentro de Components como `TabGroupContent`.

Ejemplo conceptual observado:

```xml
<TabGroup id="mainTab">
  <TabGroupContent id="page1" text="Nested 1">
    <App:TabNested1 />
  </TabGroupContent>
</TabGroup>
```

Este patrón debe preferirse cuando el contenido corresponde realmente a un Module reutilizable o cargable de forma independiente.

No conviertas cualquier fragmento pequeño en `ModuleNested`; usa esta capacidad cuando haya una separación funcional clara.

---

# 15. Module JavaScript y Components

ATUSAN genera también una instancia JavaScript del Module.

El Module cliente ofrece funcionalidades como:

```javascript
send()
addComponent()
initComponents()
registerResizeListener()
unregisterResizeListener()
```

Callbacks habituales:

```javascript
onOpen()
onActivate()
onResize()
```

Ejemplo:

```javascript
DataGridSample.onOpen = function() {
    this.registerResizeListener();
};

DataGridSample.onResize = function() {
    grid1.fitToParentHeight(10);
};
```

Para peticiones asociadas al Module, utiliza el patrón existente de `Module.send()` cuando sea el mecanismo utilizado por el módulo actual.

---

# 16. Estilos CSS

ATUSAN proporciona estilos estándar para los Components.

Entre los archivos de estilos estándar se encuentran:

```text
dataform.css
datagrid.css
datamultiform.css
datatree.css
modal.css
navbar.css
panels.css
sidebar.css
subnavbar.css
tabgroup.css
```

Antes de escribir CSS nuevo:

1. revisa el estilo estándar del Component;
2. conserva las clases y estructura generadas;
3. agrega únicamente ajustes específicos del proyecto;
4. evita reemplazar completamente el estilo del Component sin necesidad.

---

# 17. Guía rápida de selección

| Necesidad | Component recomendado |
|---|---|
| Captura de datos | `DataForm` |
| Formulario por pasos | `DataMultiForm` |
| Tabla/listado | `DataGrid` |
| Información jerárquica | `DataTree` |
| Ventana modal | `Modal` |
| Navegación principal | `Navbar` |
| Navegación lateral | `Sidebar` |
| Navegación secundaria | `Subnavbar` |
| Layout izquierda/contenido/derecha | `Panel` |
| Contenido por pestañas | `TabGroup` + `TabGroupContent` |
| Modules dinámicos dentro de panel | `Panel.openModule()` |
| Modules dinámicos como pestañas | `TabGroup.openModule()` |

---

# 18. Checklist para agentes IA

Antes de generar código con Components:

1. revisa `AGENTS.md`;
2. revisa este documento;
3. identifica el Module destino;
4. revisa su `Components.xml` actual;
5. localiza un ejemplo del mismo Component;
6. declara el Component y Controls en XML;
7. tipa la propiedad PHP con la clase correspondiente;
8. obtén la instancia con `findComponentById()`;
9. configura el Component mediante su API PHP;
10. renderiza con `write()`;
11. utiliza la instancia JavaScript generada automáticamente;
12. utiliza métodos y callbacks oficiales antes de manipular DOM;
13. conserva los estilos estándar;
14. no inventes APIs;
15. no agregues librerías externas para resolver una función ya cubierta por ATUSAN.

---

# 19. Regla final

Cuando una interfaz pueda resolverse mediante un Component de ATUSAN, utiliza primero ese Component.

La implementación debe respetar este orden:

```text
Components.xml
→ findComponentById()
→ API PHP
→ write()
→ API JavaScript
→ CSS estándar
```

Solo recurre a HTML, JavaScript o CSS manual adicional cuando la API del Component no cubra el requerimiento concreto.
