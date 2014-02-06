<?php
/**
 * @author Antoine Pouch <ant-1@pouch.name>
 * @package gestionlite
 * @module event
 */

require_once 'listenable.class.php';

/**
 * Event class
 */
class event
{
  /**
   * Name of the event
   *
   * @var string
   */
  protected $name;
  
  /**
   * The event dispatcher
   *
   * @var listenable
   */
  protected $dispatcher;
  
  /**
   * Array of parameters for the event
   *
   * @var mixed Array of values (mixed indexed by name (string)
   */
  protected $parameters = array();
  
  /**
   * Constructor
   *
   * @param string $name
   * @param listenable $dispatcher
   */
  public function __construct($name, listenable $dispatcher)
  {
    $this->name = $name;
    $this->dispatcher = $dispatcher;
  }
  
  /**
   * Get teh name
   *
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }
  
  /**
   * Get the event's dispatcher
   *
   * @return listenable
   */
  public function getDispatcher()
  {
    return $this->dispatcher;
  }
  
  /**
   * Set an event's parameter
   *
   * @param string $name
   * @param mixed $value
   */
  public function setParameter($name, $value)
  {
    $this->parameters[$name] = $value;
  }
  
  /**
   * Get a parameter's value
   *
   * @param string $name Parameter's name
   * @param mixed $defaultValue Value to return if parameter is not set
   * @return mixed
   */
  public function getParameter($name, $defaultValue = null)
  {
    return isset($this->parameters[$name]) ? $this->parameters[$name] : $defaultValue;
  }
}
?>