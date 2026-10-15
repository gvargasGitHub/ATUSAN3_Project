<?php
/** @var \Atusan\Controller\Module $module */

$topbar = $module->findComponentById('topbar');

$topbar->write() ?>
<content>
  <?php $module->write() ?>
</content>
<script>
topbar.onMenuItemClick = function (item) {
  if (item.name == "mi_close") {
    if (confirm('¿Cerrar?')) ats.send('/close', { done: rs => ats.openModule('/') });
  } else {
    ats.openModule(item.route);
  }
};
</script>