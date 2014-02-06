<?php
/**
 * @author Antoine Pouch <ant-1@pouch.name>
 * @package gestionlite
 * @module controller
 */

require_once 'controller/controller.class.php';
require_once 'model/movementTemplate.class.php';

/**
 * Movement template listing action
 */
class controller_movementTemplate_list extends controller
{
  /**
   * The main execution method
   *
   */
  public function execute()
  {
    $this->viewVariables['title'] = 'Modèles de mouvement';
    $this->viewVariables['list'] = movementTemplate::getAll();
  }
}

?>