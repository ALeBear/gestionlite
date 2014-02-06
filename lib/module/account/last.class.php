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
 * List last account movements
 */
class controller_account_last extends controller
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
    $account = account::retrieve($accountID);
    
    $lastCount = $this->getContext()->getParameter('count')
      ? $this->getContext()->getParameter('count')
      : LAST_MOVEMENTS_FOR_ACCOUNT_COUNT;
    $this->viewVariables['title'] = 'Les ' . $lastCount . ' derniers mouvements pour le compte <span class="highlight">' . $account->getName() . '</span>';
    $this->viewVariables['list'] = movement::getLastForAccount($accountID, $lastCount);
    $this->viewVariables['account'] = $account;
  }
}

?>