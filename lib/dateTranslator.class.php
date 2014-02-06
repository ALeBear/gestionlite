<?php
/**
 * @author Antoine Pouch <ant-1@pouch.name>
 * @package gestionlite
 */

/**
 * Date translation class
 *
 */
class dateTranslator
{
  /**
   * Get the label for a day number
   *
   * @param integer $dayId
   * @return string
   */
  public static function getDayLabel($dayId)
  {
    $days = array(
      1 => 'Lundi',
      2 => 'Mardi',
      3 => 'Mercredi',
      4 => 'Jeudi',
      5 => 'Vendredi',
      6 => 'Samedi',
      7 => 'Dimanche'
    );
    
    return $days[(integer) $dayId];
  }
  
  /**
   * Get the label for a month number
   *
   * @param integer $monthId
   * @return string
   */
  public static function getMonthLabel($monthId)
  {
    $months = array(
      1 => 'Janvier',
      2 => 'Février',
      3 => 'Mars',
      4 => 'Avril',
      5 => 'Mai',
      6 => 'Juin',
      7 => 'Juillet',
      8 => 'Août',
      9 => 'Septembre',
      10 => 'Octobre',
      11 => 'Novembre',
      12 => 'Décembre'
      );
    
    return $months[(integer) $monthId];
  }
  
  /**
   * Convert a date with a date taken from the french format (into DB format)
   *
   * @param string $date The date to set
   */
  public static function convertDateFromFrenchFormat($date)
  {
    return substr($date, 6, 4) . '-' . substr($date, 3, 2) . '-' . substr($date, 0, 2);
  }
}

?>