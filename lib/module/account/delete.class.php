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
 * Account deletion action
 */
class controller_account_delete extends controller
{
  /**
   * The main execution method
   *
   */
  public function execute()
  {
    $accountID = $this->getContext()->getParameter('id', null);
    if (!$accountID)
    {
      $this->forward($this->module, CONTROLLER_DEFAULT_ACTION, array('messages' => 'Il faut passer l\'ID d\'un compte'));
    }
    if ($accountID == account::EXTERNAL_ID)
    {
      $this->forward($this->module, CONTROLLER_DEFAULT_ACTION, array('messages' => 'Impossible de supprimer le compte externe'));
    }
    if (movement::getCountForAccount($accountID))
    {
      $this->forward($this->module, CONTROLLER_DEFAULT_ACTION, array('messages' => 'Impossible de supprimer un compte qui a des mouvements'));
    }
    
    //All is good
    try
    {
      account::delete(account::retrieve($accountID));
      $this->forward($this->module, CONTROLLER_DEFAULT_ACTION, array('messages' => 'Compte supprimé avec succès'));
    }
    catch (modelException $e)
    {
      $this->forward($this->module, CONTROLLER_DEFAULT_ACTION, array('messages' => 'Erreur : ' . $e->getMessage()));
    }
  }
}

?>