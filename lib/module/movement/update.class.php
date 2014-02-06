<?php
/**
 * @author Antoine Pouch <ant-1@pouch.name>
 * @package gestionlite
 * @module controller
 */

require_once 'controller/controller.class.php';
require_once 'model/movement.class.php';
require_once 'model/movementTemplate.class.php';
require_once 'dateTranslator.class.php';

/**
 * Movements update action : update or create
 */
class controller_movement_update extends controller
{
  /**
   * The main execution method
   *
   */
  public function execute()
  {
    $this->viewVariables['externalAccountID'] = account::EXTERNAL_ID;
    $this->viewVariables['formaction'] = DIRECTORY_PREFIX . $this->module . '/' . $this->action;
    if (count($_POST))
    {
      //Form was posted, proceed
      //Prepare values array
      $massagedValues = $_POST;
      if ($_POST['formMode'] == 'quick') {
        $massagedValues['amount'] = $massagedValues['amountQuick'];
        $massagedValues['datePassed'] = $massagedValues['datePassedQuick'];
      }
      unset($massagedValues['formMode']);
      unset($massagedValues['template']);
      unset($massagedValues['datePassedQuick']);
      unset($massagedValues['amountQuick']);
      
      if (isset($massagedValues['datePassed']) && $massagedValues['datePassed'])
      {
        $massagedValues['date_passed'] = dateTranslator::convertDateFromFrenchFormat($massagedValues['datePassed']);
      }
      if ($this->processPost($massagedValues))
      {
        //Update/creation went fine
        $parameters = array('messages' => 'Mouvement mis à jour avec succès');
        if (isset($massagedValues['date_passed'])) {
          $parameters['month'] = substr($massagedValues['date_passed'], 0, 7);
        }
        $this->forward($this->module, CONTROLLER_DEFAULT_ACTION, $parameters);
      }
      
      //Update/creation failed, load account from data
      if ($massagedValues['id'])
      {
        $movement = movement::retrieve($massagedValues['id']);
        unset($massagedValues['id']);
      }
      else
      {
        $movement = new movement();
      }
      $movement->loadFromArray($massagedValues);
      $formMode = $_POST['formMode'];
      $template = $_POST['template'];
    }
    else
    {
      //Form was not posted, load movement
      $movement = $this->getContext()->getParameter('id', false)
        ? movement::retrieve($this->getContext()->getParameter('id'))
        : new movement();
      $formMode = $this->getContext()->getSession()->get('movementFormMode')
        ? $this->getContext()->getSession()->get('movementFormMode')
        : CONTROLLER_MOVEMENT_DEFAUT_MODE;
      $template = false;
    }
    
    if ($movement->getId()) {
      $this->setTitle('Modification d\'un mouvement');
      $this->viewVariables['allow_quick_form'] = false;
      $this->viewVariables['templates'] = array();
      $formMode = 'complete';
    } else {
      $this->setTitle('Création d\'un mouvement');
      $this->viewVariables['allow_quick_form'] = true;
      $this->viewVariables['templates'] = movementTemplate::getAll();
    }
    
    $this->viewVariables['movement'] = $movement;
    $this->viewVariables['accounts'] = account::getAll();
    $this->viewVariables['form_mode'] = $formMode;
    $this->viewVariables['template'] = $template;
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
        $movement = movement::retrieve($values['id']);
        
        //If movement is certified, its date passed cannot be set after its certification date
        if ($movement->getCertifiedAt() && $movement->getCertifiedAt() < $values['datePassed'])
        {
          $this->viewVariables['messages'] = 'Impossible de deplacer un mouvement à une date postérieure à sa date de certification (' . $movement->getCertifiedAt() . ')';
          return false;
        }
        
        movement::update($movement, $values);
      }
      else
      {
        movement::create($values);
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