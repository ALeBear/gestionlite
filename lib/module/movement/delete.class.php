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
 * Movement deletion action
 */
class controller_movement_delete extends controller
{
  /**
   * The main execution method
   *
   */
  public function execute()
  {
    $movementID = $this->getContext()->getParameter('id', null);
    if (!$movementID)
    {
      $this->forward($this->module, CONTROLLER_DEFAULT_ACTION, array('messages' => 'Il faut passer l\'ID d\'un mouvement'));
    }
    
    //All is good
    try
    {
      movement::delete(movement::retrieve($movementID));
      $this->forward($this->module, CONTROLLER_DEFAULT_ACTION, array('messages' => 'Mouvement supprimé avec succès'));
    }
    catch (modelException $e)
    {
      $this->forward($this->module, CONTROLLER_DEFAULT_ACTION, array('messages' => 'Erreur : ' . $e->getMessage()));
    }
  }
}

?>