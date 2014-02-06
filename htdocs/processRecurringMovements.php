<?php 
/**
 * Process pending recurring movements
 * 
 * @author Antoine Pouch <ant-1@pouch.name>
 * @package gestionlite
 * @module htdocs
 */

foreach (recurringMovement::getAll() as $rm)
{
  //End of the recurring movement may be reached
  if ($rm->getUntil() && date('Y-m-d') > $rm->getUntil())
  {
    $rm->setDeleted(true);
    $rm->save();
    continue;
  }

  while (date('Y-m-d', strtotime($rm->getNextDate())) <= date('Y-m-d'))
  {
    $movement = $rm->getPrototypeMovement();
    $movement->setDatePassed($rm->getNextDate());
    $movement->save();
  }
}

?>