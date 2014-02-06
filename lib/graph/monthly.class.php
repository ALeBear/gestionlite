<?php
/**
 * @author Antoine Pouch <ant-1@pouch.name>
 * @package gestionlite
 * @module graphs
 */

require_once 'glGraph.class.php';

/**
 * Monthly graph class : Allows to draw a graph with monthly values
 */
class monthlyGraph extends glGraph
{
  /**
   * The months
   *
   * @var array
   */
  protected $months = array();
  

  /**
   * Add a group of values
   *
   * @param string $legend The legend for this group
   * @param array $values The values (float) indexed by string (month labels)
   */
  public function addValues($legend, $values)
  {
    $this->months = array_merge($this->months, array_keys($values));
    parent::addValues($legend, array_values($values));
  }
  
  /**
   * Prepare the graph before drawing
   */
  protected function prepare()
  {
    parent::prepare();
    
    //Set the X labels depending on the months returned
    $this->months = array_unique($this->months);
    sort($this->months);
    $this->graph->xaxis->SetTickLabels($this->months);
  }
}
?>