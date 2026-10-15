<?php
/** @var \Atusan\Controller\Module $module */

$topbar = $module->findComponentById('topbar');

$topbar->write() ?>
<content>
  <?php $module->write() ?>
</content>
<script>
topbar.onMenuItemClick = function (item) {
  if (item.id == "mi_close") {
    if (confirm('¿Cerrar?')) Atusan.send('/close', { done: rs => Atusan.openModule('/') });
  } else {
    Atusan.openModule(item.route);
  }
};
</script>