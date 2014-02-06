<?php
/**
 * @author Antoine Pouch <ant-1@pouch.name>
 * @package gestionlite
 * @module controller
 */

require_once 'controller/controller.class.php';
require_once 'model/account.class.php';

/**
 * Account update action : update or create
 */
class controller_account_update extends controller
{
  /**
   * The main execution method
   *
   */
  public function execute()
  {
    $this->viewVariables['formaction'] = DIRECTORY_PREFIX . $this->module . '/' . $this->action;
    if (count($_POST))
    {
      //Form was posted, proceed
      if ($this->processPost($_POST))
      {
        //Update/creation went fine
        $this->forward($this->module, CONTROLLER_DEFAULT_ACTION, array('messages' => 'Compte mis à jour avec succès'));
      }
      
      //Update/creation failed, load account from data
      if ($_POST['id'])
      {
        $account = account::retrieve($_POST['id']);
        unset($_POST['id']);
      }
      else
      {
        $account = new account();
      }
      $account->loadFromArray($_POST);
    }
    else
    {
      //Form was not posted, load account
      $account = $this->getContext()->getParameter('id', false)
        ? account::retrieve($this->getContext()->getParameter('id'))
        : new account();
      //Cannot edit non-editable account
      if ($account->getId() && !$account->isEditable())
      {
        $this->forward($this->module, CONTROLLER_DEFAULT_ACTION, array('messages' => 'Impossible d\'éditer ce compte'));
      }
    }
    
    if ($account->getId())
    {
      $this->setTitle('Modification d\'un compte');
    }
    else
    {
      $this->setTitle('Création d\'un compte');
    }
    
    $this->viewVariables['account'] = $account;
  }
  
  /**
   * Process posted form values. If there was an error, a message will be
   * set in viewVariables
   *
   * @param mixed $values Array of values indexed by name
   * @return boolean True on succes, false on error
   */
  protected function processPost($values)
  {
    //Try to save
    try
    {
      if ($values['id'])
      {
        $account = account::retrieve($values['id']);
        account::update($account, $values);
      }
      else
      {
        account::create($values);
      }
      return true;
    }
    catch (modelException $e)
    {
      $this->viewVariables['messages'] = $e->getMessage();
      return false;
    }
  }
}

?>