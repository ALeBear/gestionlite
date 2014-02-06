<?php
/**
 * @author Antoine Pouch <ant-1@pouch.name>
 * @package gestionlite
 * @module controller
 */

require_once 'controller/controller.class.php';
require_once 'model/recurringMovement.class.php';

/**
 * Recurring movements deletion action
 */
class controller_recurringMovement_delete extends controller
{
  /**
   * The main execution method
   *
   */
  public function execute()
  {
    $rmID = $this->getContext()->getParameter('id', null);
    if (!$rmID)
    {
      $this->forward($this->module, CONTROLLER_DEFAULT_ACTION, array('messages' => 'Il faut passer l\'ID d\'un mouvement récurrent'));
    }
    
    //All is good
    try
    {
      recurringMovement::delete(recurringMovement::retrieve($rmID));
      $this->forward($this->module, CONTROLLER_DEFAULT_ACTION, array('messages' => 'Mouvement récurrent supprimé avec succès'));
    }
    catch (modelException $e)
    {
      $this->forward($this->module, CONTROLLER_DEFAULT_ACTION, array('messages' => 'Erreur : ' . $e->getMessage()));
    }
  }
}

?>