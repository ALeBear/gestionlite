<?php
/**
 * @author Antoine Pouch <ant-1@pouch.name>
 * @package gestionlite
 * @module controller
 */

require_once 'controller/controller.class.php';
require_once 'model/movementTemplate.class.php';

/**
 * Movement template deletion action
 */
class controller_movementTemplate_delete extends controller
{
  /**
   * The main execution method
   *
   */
  public function execute()
  {
    $movementTemplateID = $this->getContext()->getParameter('id', null);
    if (!$movementTemplateID)
    {
      $this->forward($this->module, CONTROLLER_DEFAULT_ACTION, array('messages' => 'Il faut passer l\'ID d\'un modèle mouvement'));
    }
    
    //All is good
    try
    {
      movementTemplate::delete(movementTemplate::retrieve($movementTemplateID));
      $this->forward($this->module, CONTROLLER_DEFAULT_ACTION, array('messages' => 'Modèle de mouvement supprimé avec succès'));
    }
    catch (modelException $e)
    {
      $this->forward($this->module, CONTROLLER_DEFAULT_ACTION, array('messages' => 'Erreur : ' . $e->getMessage()));
    }
  }
}

?>