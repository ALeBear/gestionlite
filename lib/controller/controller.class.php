<?php
/**
 * @author Antoine Pouch <ant-1@pouch.name>
 * @package gestionlite
 * @module controller
 */

require_once 'context.class.php';
require_once 'forwardException.class.php';

/**
 * Parent class for all the controllers
 */
abstract class controller
{
  /**
   * The name of the controller's module
   *
   * @var string
   */
  protected $module;
  
  /**
   * The name of the controller's action
   *
   * @var string
   */
  protected $action;
  
  /**
   * The name of the main slot, if different from the action name
   *
   * @var string
   */
  protected $mainSlot;
  
  /**
   * The variables that will be passed to the view
   *
   * @var mixed Array of mixed indexed by string
   */
  protected $viewVariables = array();
  
  /**
   * An alternate layout if set
   *
   * @var string
   */
  protected $layout = null;
  
  /**
   * Constructor
   *
   * @param string $module
   * @param string $action
   */
  public function __construct($module, $action)
  {
    $this->module = $module;
    $this->action = $action;
    $this->viewVariables['module'] = $module;
    $this->viewVariables['action'] = $action;
    $this->viewVariables['isMobile'] = $this->getContext()->getParameter('mobile', false);
    
    //Add messages to the view if any found
    if ($this->getContext()->getParameter('messages'))
    {
      $this->viewVariables['messages'] = urldecode($this->getContext()->getParameter('messages'));
    }
  }
  
  /**
   * Executes the main controller. All other will be run "inside" this method
   * IT actually monitors for forwardException and does the appropriate
   * forwards.
   */
  public function run()
  {
    try
    {
      $this->execute();
    }
    catch (forwardException $e)
    {
      $this->getContext()->forward($e->getModule(), $e->getAction(), $e->getParameters());
    }
  }
  
  /**
   * The main execution method
   *
   */
  public abstract function execute();
  
  /**
   * Forward to another action
   *
   * @param string $module The module to forward to
   * @param string $action The action to forward to
   * @param mixed $parameters Array of query string parameters to pass to 
   */
  public function forward($module, $action, $parameters = array())
  {
    throw new forwardException($module, $action, $parameters);
  }
  
  /**
   * Shortcut to get the context
   *
   * @return context
   */
  public function getContext()
  {
    return context::getInstance();
  }
  
  /**
   * Set the view's title
   *
   * @param string $value
   */
  public function setTitle($value)
  {
    $this->viewVariables['title'] = $value;
  }
  
  /**
   * Set the main slot name
   *
   * @param string $value
   */
  public function setMainSlot($value)
  {
    $this->mainSlot = $value;
  }
  
  /**
   * Display the view
   */
  public function show()
  {
    $layout = is_null($this->layout) ? DEFAULT_LAYOUT : $this->layout;
    $mainSlot = is_null($this->mainSlot) ? $this->action : $this->mainSlot;
    extract($this->viewVariables);
    $mainSlotPath = context::getModulesPath() . '/' . $this->module . '/' . $mainSlot . '.php';
    require 'view/' . $layout;
  }
}
?>