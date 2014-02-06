<?php
/**
 * @author Antoine Pouch <ant-1@pouch.name>
 * @package gestionlite
 * @module controller
 */

require_once 'controller/controller.class.php';
require_once 'model/account.class.php';

/**
 * Account listing action
 */
class controller_account_list extends controller
{
  /**
   * The main execution method
   *
   */
  public function execute()
  {
    $this->viewVariables['title'] = 'Comptes';
    $this->viewVariables['list'] = account::getAll();
  }
}

?>