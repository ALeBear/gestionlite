<?php
/**
 * @author Antoine Pouch <ant-1@pouch.name>
 * @package gestionlite
 * @module controller
 */

require_once 'controller/controller.class.php';
require_once 'model/movement.class.php';
require_once 'dateTranslator.class.php';

/**
 * Movement listing action
 */
class controller_movement_list extends controller
{
  /**
   * The main execution method
   *
   */
  public function execute()
  {
    session_start();
    switch (true) {
      case (bool) $this->getContext()->getParameter('month'):
        $month = substr($this->getContext()->getParameter('month'), 5, 2);
        $year = substr($this->getContext()->getParameter('month'), 0, 4);
        break;
      case (bool) $this->getContext()->getSession()->get('month'):
        $month = $this->getContext()->getSession()->get('month');
        $year = $this->getContext()->getSession()->get('year');
        break;
      default:
        $month = date('m');
        $year = date('Y');
    }
    $this->getContext()->getSession()->set('month', $month);
    $this->getContext()->getSession()->set('year', $year);
    
    $this->viewVariables['title'] = 'Mouvements du mois de <span class="highlight">' . dateTranslator::getMonthLabel($month) . ' ' . $year . '</span>';
    $this->viewVariables['list'] = movement::getAllForMonth($year, $month);
    $this->viewVariables['months'] = movement::getMonthsWithMovements();
    $this->viewVariables['currentMonth'] = $year . '-' . $month;
  }
}

?>