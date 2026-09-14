<?php
/** @var \Atusan\Controller\Module $module */

$topbar = self::$module->findViewById('topbar');
?>
<?php $topbar->write() ?>
<content>
  <?php self::$module->write() ?>
</content>