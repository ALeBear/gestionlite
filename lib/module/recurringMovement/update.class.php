<?php
/**
 * @author Antoine Pouch <ant-1@pouch.name>
 * @package gestionlite
 * @module controller
 */

require_once 'controller/controller.class.php';
require_once 'model/recurringMovement.class.php';

/**
 * Recurring movements update action : update or create
 */
class controller_recurringMovement_update extends controller
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
      if (isset($_POST['dateUntil']) && $_POST['dateUntil'])
      {
        $_POST['until'] = dateTranslator::convertDateFromFrenchFormat($_POST['dateUntil']);
      }
      if ($this->processPost($_POST))
      {
        //Update/creation went fine
        $this->forward($this->module, CONTROLLER_DEFAULT_ACTION, array('messages' => 'Mouvement récurrent mis à jour avec succès'));
      }
      
      //Update/creation failed, load account from data
      if ($_POST['id'])
      {
        $rm = recurringMovement::retrieve($_POST['id']);
        unset($_POST['id']);
      }
      else
      {
        $rm = new recurringMovement();
      }
      $rm->loadFromArray($_POST);
    }
    else
    {
      //Form was not posted, load account
      $rm = $this->getContext()->getParameter('id', false)
        ? recurringMovement::retrieve($this->getContext()->getParameter('id'))
        : new recurringMovement();
    }
    
    if ($rm->getId())
    {
      $this->setTitle('Modification d\'un mouvement récurrent');
    }
    else
    {
      $this->setTitle('Création d\'un mouvement récurrent');
    }
    
    $this->viewVariables['rm'] = $rm;
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
    try
    {
      if ($values['id'])
      {
        $rm = recurringMovement::retrieve($values['id']);
        recurringMovement::update($rm, $values);
      }
      else
      {
        recurringMovement::create($values);
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