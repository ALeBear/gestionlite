<?php
/**
 * @author Antoine Pouch <ant-1@pouch.name>
 * @package gestionlite
 * @module controller
 */

require_once 'controller/controller.class.php';
require_once 'model/account.class.php';
require_once 'model/movement.class.php';

/**
 * Certification of all movements action
 */
class controller_movement_certify extends controller
{
  /**
   * The main execution method
   *
   */
  public function execute()
  {
    try
    {
      movement::certifyAll();
      $this->forward($this->module, CONTROLLER_DEFAULT_ACTION, array('messages' => 'Certification effectuée avec succès'));
    }
    catch (modelException $e)
    {
      $this->forward($this->module, CONTROLLER_DEFAULT_ACTION, array('messages' => 'Erreur : ' . $e->getMessage()));
    }
  }
}

?>