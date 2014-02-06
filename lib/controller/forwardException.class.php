<?php
/**
 * @author Antoine Pouch <ant-1@pouch.name>
 * @package gestionlite
 * @module controller
 */

require_once 'controllerException.class.php';

/**
 * Model exception class
 */
class forwardException extends controllerException
{
  /**
   * The module to forward to
   *
   * @var string
   */
  protected $module;
  
  /**
   * The action to forward to
   *
   * @var string
   */
  protected $action;
  
  /**
   * Parameters to pass to the context, as if given in the query string
   *
   * @var mixed Array of string indexed by string
   */
  protected $parameters;
  
  /**
   * Constructor. PAss it where you want to forward to
   *
   * @param string $module
   * @param string $action
   * @param mixed $parameters
   */
  public function __construct($module, $action, $parameters = array())
  {
    $this->module = $module;
    $this->action = $action;
    $this->parameters = $parameters;
  }
  
  /**
   * Return the module
   *
   * @return string
   */
  public function getModule()
  {
    return $this->module;
  }
    
  /**
   * Return the action
   *
   * @return string
   */
  public function getAction()
  {
    return $this->action;
  }
  
  /**
   * Return the parameters
   *
   * @return mixed
   */
  public function getParameters()
  {
    return $this->parameters;
  }
}
?>