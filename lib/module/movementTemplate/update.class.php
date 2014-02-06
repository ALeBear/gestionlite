<?php
/**
 * @author Antoine Pouch <ant-1@pouch.name>
 * @package gestionlite
 * @module controller
 */

require_once 'controller/controller.class.php';
require_once 'model/movementTemplate.class.php';
require_once 'model/account.class.php';

/**
 * Movement template update action : update or create
 */
class controller_movementTemplate_update extends controller
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
        $this->forward($this->module, CONTROLLER_DEFAULT_ACTION, array('messages' => 'Modèle de mouvemnent mis à jour avec succès'));
      }
      
      //Update/creation failed, load account from data
      if ($_POST['id'])
      {
        $movementTemplate = movementTemplate::retrieve($_POST['id']);
        unset($_POST['id']);
      }
      else
      {
        $movementTemplate = new movementTemplate();
      }
      $movementTemplate->loadFromArray($_POST);
    }
    else
    {
      //Form was not posted, load movement template
      $movementTemplate = $this->getContext()->getParameter('id', false)
        ? movementTemplate::retrieve($this->getContext()->getParameter('id'))
        : new movementTemplate();
      //Cannot edit non-editable account
    }
    
    if ($movementTemplate->getId())
    {
      $this->setTitle('Modification d\'un modèle de mouvement');
    }
    else
    {
      $this->setTitle('Création d\'un modèle de mouvement');
    }
    
    $this->viewVariables['movementTemplate'] = $movementTemplate;
    $this->viewVariables['accounts'] = account::getAll();
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
    if (!$values['amount']) {
      $values['amount'] = null;
    }
    try
    {
      if ($values['id'])
      {
        $movementTemplate = movementTemplate::retrieve($values['id']);
        movementTemplate::update($movementTemplate, $values);
      }
      else
      {
        movementTemplate::create($values);
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