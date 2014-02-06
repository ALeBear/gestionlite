<?php
/**
 * @author Antoine Pouch <ant-1@pouch.name>
 * @package gestionlite
 * @module view
 */

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <?php if ($isMobile): ?>
  <meta name="viewport" content="width=320" />
  <?php endif; ?>
  <title>GestionLite - <?php echo strip_tags($title); ?></title>
  <link rel="stylesheet" type="text/css" href="<?php echo APACHE_DIRECTORY_PREFIX; ?>css/layout.css" />
</head>
<body>
    <?php require 'view/menu.php'; ?>
    <h1><?php echo $title; ?></h1>
    <?php if (isset($messages)): ?>
    <div class="messages"><?php echo $messages; ?></div>
    <?php endif; ?>
    <div id="main">
      <?php require $mainSlotPath; ?>
    </div>
</body>
</html>
