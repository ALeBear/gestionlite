<?php
/**
 * @author Antoine Pouch <ant-1@pouch.name>
 * @package gestionlite
 * @module graphs
 */

require_once 'vendor/jpgraph/jpgraph.php';
require_once 'vendor/jpgraph/jpgraph_line.php';
require_once 'vendor/jpgraph/jpgraph_bar.php';

/**
 * Monthly graph class : Allows to draw a graph with monthly values
 */
class glGraph
{
  /**
   * A graph type : Bar
   * @var string
   */
  const TYPE_BAR = 'bar';
  
  /**
   * A graph type : line
   * @var string
   */
  const TYPE_LINE = 'line';
  
  /**
   * Array of colors to use for different bars or lines
   *
   * @var array
   */
  protected $COLORS = array('blue', 'red', 'purple', 'orange', 'green',
    'magenta1', 'yellow', 'maroon', 'salmon', 'forestgreen', 'darkviolet');
  
  /**
   * Should we show the values ?
   *
   * @var boolean
   */
  protected $showValues = false;
  
  /**
   * The graph type (see class constants)
   *
   * @var string
   */
  protected $type = null;
  
  /**
   * The Graph object
   *
   * @var Graph
   */
  protected $graph;
  
  /**
   * The bars group for bar graphs
   *
   * @var unknown_type
   */
  protected $group = array();
  

  /**
   * Constructor
   *
   * @param string $type The graph type. See class constants
   * @param string $title Graph main title
   * @param string $xTitle X axis title
   * @param string $yTitle Y axis title
   */
  public function __construct($type, $title, $xTitle, $yTitle)
  {
    $this->graph = new Graph(850, 400, "auto");    
    $this->graph->SetScale("textlin");
    $this->graph->img->setMargin(60, 260, 40, 40);
    $this->graph->legend->pos(0.05, 0.5, "right", 'center');
    
    $this->type = $type;
    $this->graph->title->Set(self::cleanLegend($title));
    $this->graph->xaxis->title->Set(self::cleanLegend($xTitle));
    $this->graph->xaxis->SetTitleMargin(10); 
    $this->graph->yaxis->title->Set(self::cleanLegend($yTitle)); 
    $this->graph->yaxis->SetTitleMargin(40);
  }
  
  /**
   * Should we show the values ?
   *
   * @param integer $flag
   */
  public function showValues($flag)
  {
    $this->showValues = (boolean) $flag;
  }
  
  /**
   * Clean a legend string : remove accentuated characters by their
   * non-accentuated one
   *
   * @param string $legend
   * @return string
   */
  public static function cleanLegend($legend)
  {
    return strtr(utf8_decode($legend), utf8_decode("àâäèéêëîïôöùüû"), "aaaeeeeiioouuu");
  }
  
  /**
   * Get the current color
   *
   * @return string
   */
  protected function getCurrentColor()
  {
    return current($this->COLORS);
  }
  
  /**
   * Move the colors pointer
   */
  protected function nextColor()
  {
    if (!next($this->COLORS))
    {
      reset($this->COLORS);
    } 
  }
  
  /**
   * Add a group of values
   *
   * @param string $legend The legend for this group
   * @param array $values The values (float)
   */
  public function addValues($legend, $values)
  {
    if (!count($values))
    {
      return;
    }
    
    $legend = self::cleanLegend($legend);
    
    switch ($this->type)
    {
      case self::TYPE_BAR:
        $plot = new BarPlot($values);
        if ($this->showValues)
        {
          $plot->value->SetFormat("%0.2f");
          $plot->value->SetColor($this->getCurrentColor());
          $plot->value->Show();
        }
        $plot->SetFillColor($this->getCurrentColor());
        $plot->SetLegend($legend);
        $plot->SetColor($this->getCurrentColor());
        $this->group[] = $plot;
        break;
      case 'line':
        $plot = new LinePlot($values);
        if ($this->showValues)
        {
          $plot->value->SetFormat("%0.2f");
          $plot->value->SetColor($this->getCurrentColor());
          $plot->value->Show();
        }
        $plot->SetLegend($legend);
        $plot->mark->SetType(MARK_FILLEDCIRCLE);
        $plot->mark->SetFillColor($this->getCurrentColor());
        $plot->mark->SetWidth(4);        
        $plot->SetColor($this->getCurrentColor());
        $this->graph->Add($plot);
        break;
    }
    
    $this->nextColor();
  }
  
  /**
   * Prepare the graph before drawing
   */
  protected function prepare()
  {
    if ($this->type == self::TYPE_BAR)
    {
      $this->graph->Add(new GroupBarPlot($this->group));
    }
  }
  
  /**
   * Draw the graph to a file, and return its path (document-root relative)
   *
   * @return string
   */
  public function draw()
  {
    $this->prepare();
    
    $graphFilename = '/' . microtime(true) . '.png';
    $this->graph->Stroke(GRAPHS_DIRECTORY_ABSOLUTE . $graphFilename);
    
    return GRAPHS_DIRECTORY . $graphFilename;
  }
}
?>