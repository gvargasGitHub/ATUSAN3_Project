<?php
/** @var \Atusan\Controller\Module $module */

$topbar = $module->findViewById('topbar');

$topbar->write() ?>
<content>
  <?php $module->write() ?>
</content>