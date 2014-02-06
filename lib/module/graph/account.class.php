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
      $this->viewVariables['allMonths'] = movement::getMonthsWithMovements();
      $this->viewVariables['startMonth'] = end($this->viewVariables['allMonths']);
      $this->viewVariables['endMonth'] = reset($this->viewVariables['allMonths']);
      $this->viewVariables['allMonths'] = movement::getMonthsWithMovements();

    if (count($_POST) && isset($_POST['graphAccounts']))
    {
        $this->viewVariables['startMonth'] = $_POST['startMonth'];
        $this->viewVariables['endMonth'] = $_POST['endMonth'];
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
            $data = $anAccount->getExpensesPerMonth(strtotime($this->viewVariables['startMonth'] . '-01'), strtotime($this->viewVariables['endMonth'] . '-01 +1 MONTH -1 DAY'));
            break;
          case 'credits':
            $data = $anAccount->getCreditsPerMonth(strtotime($this->viewVariables['startMonth'] . '-01'), strtotime($this->viewVariables['endMonth'] . '-01 +1 MONTH -1 DAY'));
            break;
          case 'expenses-internals':
            $data = $anAccount->getExpensesPerMonth(strtotime($this->viewVariables['startMonth'] . '-01'), strtotime($this->viewVariables['endMonth'] . '-01 +1 MONTH -1 DAY'), true);
            break;
          case 'credits-internals':
            $data = $anAccount->getCreditsPerMonth(strtotime($this->viewVariables['startMonth'] . '-01'), strtotime($this->viewVariables['endMonth'] . '-01 +1 MONTH -1 DAY'), true);
            break;
        }
        $graph->addValues($anAccount->getName(), $data);
      }
      
      $this->viewVariables['graph'] = $graph->draw();
    }
  }
}

?>