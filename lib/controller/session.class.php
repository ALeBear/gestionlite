<?php
/**
 * @author Antoine Pouch <ant-1@pouch.name>
 * @package gestionlite
 * @module controller
 */

/**
 * Session: bridge to $_SESSION, basically
 */
class session
{
  /**
   * The session instance
   *
   * @var context
   */
  protected static $instance;
  
  /**
   * @var mixed Array of values indexed by key (haha)
   */
  protected $parameters = array();
  
  /**
   * Constructor. Will set parameters from $_SESSION
   *
   */
  protected function __construct()
  {
    $this->parameters =& $_SESSION;
  }
  
  /**
   * sets a session parameter
   *
   * @param string $name
   * @param mixed $value The value to set
   */
  public function set($name, $value)
  {
    $this->parameters[$name] = $value;
  }
  
  /**
   * Get a query string parameter
   *
   * @param string $name
   * @param mixed $defaultValue The default value to return if the parameter is
   * not found
   */
  public function get($name, $defaultValue = null)
  {
    return isset($this->parameters[$name]) ? $this->parameters[$name] : $defaultValue;
  }
  
  /**
   * Get all parameters
   *
   * @return mixed Array of values indexed by string
   */
  public function getAll()
  {
    return $this->parameters;
  }
  
  /**
   * Get the current session
   *
   * @return session
   */
  public static function getInstance()
  {
    if (!self::$instance)
    {
      self::$instance = new session();
    }
    
    return self::$instance;
  }
  
  public function __destruct()
  {
    session_write_close();
  }
}
?>