<?php
/**
 * @author Antoine Pouch <ant-1@pouch.name>
 * @package gestionlite
 * @module controller
 */
require_once 'controller/controller.class.php';
require_once 'model/account.class.php';
require_once 'graph/monthly.class.php';

/**
 * Graphs for accounts action
 */
class controller_graph_account extends controller
{
  /**
   * The main execution method
   *
   */
  public function execute()
  {
    $this->viewVariables['title'] = 'Rapports par compte par mois';
    $this->viewVariables['accounts'] = account::getAll();
    $this->viewVariables['formaction'] = DIRECTORY_PREFIX . $this->module . '/' . $this->action;
    $this->viewVariables['graphAccounts'] = array();
    $this->viewVariables['graphType'] = 'bar';
    $this->viewVariables['report'] = 'expenses';
    $this->viewVariables['showValues'] = true;
    
    if (count($_POST) && isset($_POST['graphAccounts']))
    {
      $this->viewVariables['report'] = $_POST['report'];
      $this->viewVariables['graphAccounts'] = $_POST['graphAccounts'];
      $this->viewVariables['graphType'] = $_POST['graphType'];
      $this->viewVariables['showValues'] = isset($_POST['showValues']) ? $_POST['showValues'] : false;
      switch ($this->viewVariables['report'])
      {
        case 'expenses':
          $graphTitle = 'Depenses par compte par mois';
          break;
        case 'credits':
          $graphTitle = 'Crédits par compte par mois';
          break;
        case 'expenses-internals':
          $graphTitle = 'Depenses internes par compte par mois';
          break;
        case 'credits-internals':
          $graphTitle = 'Credits internes par compte par mois';
          break;
      }
      $graph = new monthlyGraph(
        $this->viewVariables['graphType'],
        $graphTitle,
        'Mois',
        'Montants en $');
      $graph->showValues($this->viewVariables['showValues']);

      //Create a linear plot per account and store some common values
      foreach ($this->viewVariables['graphAccounts'] as $anAccountID)
      {
        $anAccount = account::retrieve($anAccountID);
        switch ($this->viewVariables['report'])
        {
          case 'expenses':
            $data = $anAccount->getExpensesPerMonth(movement::getFirstMovementTimestamp(), time());
            break;
          case 'credits':
            $data = $anAccount->getCreditsPerMonth(movement::getFirstMovementTimestamp(), time());
            break;
          case 'expenses-internals':
            $data = $anAccount->getExpensesPerMonth(movement::getFirstMovementTimestamp(), time(), true);
            break;
          case 'credits-internals':
            $data = $anAccount->getCreditsPerMonth(movement::getFirstMovementTimestamp(), time(), true);
            break;
        }
        $graph->addValues($anAccount->getName(), $data);
      }
      
      $this->viewVariables['graph'] = $graph->draw();
    }
  }
}

?>