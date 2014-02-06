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
 * Movement flagging action
 */
class controller_movement_flag extends controller
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
      movement::retrieve($movementID)->setFlags($this->getContext()->getParameter('flags', 0))->save();
      $this->forward($this->module, CONTROLLER_DEFAULT_ACTION, array('messages' => 'Mouvement (dé)flaggé avec succès'));
    }
    catch (modelException $e)
    {
      $this->forward($this->module, CONTROLLER_DEFAULT_ACTION, array('messages' => 'Erreur : ' . $e->getMessage()));
    }
  }
}

?>