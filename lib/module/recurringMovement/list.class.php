<?php
/**
 * @author Antoine Pouch <ant-1@pouch.name>
 * @package gestionlite
 * @module controller
 */

require_once 'controller/controller.class.php';
require_once 'model/recurringMovement.class.php';
require_once 'dateTranslator.class.php';

/**
 * Recurring movement listing action
 */
class controller_recurringMovement_list extends controller
{
  /**
   * The main execution method
   *
   */
  public function execute()
  {
    $this->viewVariables['title'] = 'Mouvements récurrents';
    $this->viewVariables['list'] = recurringMovement::getAll();
  }
}

?>