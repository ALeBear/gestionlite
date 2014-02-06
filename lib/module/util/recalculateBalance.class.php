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
 * Recalculate account balances
 */
class controller_util_recalculateBalance extends controller
{
  /**
   * The main execution method
   *
   */
  public function execute()
  {
    try
    {
      $accounts = account::getAll();
      foreach ($accounts as $anAccount)
      {
        $realBalance = $anAccount->isBlackhole() ? 0 : $anAccount->getCalculatedBalance();
        if ($realBalance != $anAccount->getBalance())
        {
          $anAccount->setBalance($realBalance);
          $anAccount->save();
        }
      }
    }
    catch (Exception $e)
    {
      $this->forward(CONTROLLER_DEFAULT_MODULE, CONTROLLER_DEFAULT_ACTION, array('messages' => 'Erreur: ' . $e->getMessage()));
    }
    
    $this->forward(CONTROLLER_DEFAULT_MODULE, CONTROLLER_DEFAULT_ACTION, array('messages' => 'Les soldes des comptes ont été recalculés'));
  }
}

?>