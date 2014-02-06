<?php
/**
 * @author Antoine Pouch <ant-1@pouch.name>
 * @package gestionlite
 * @module controller
 */

require 'controllerException.class.php';
require 'session.class.php';

/**
 * Context : instanciates the action from REQUEST_URI and store query string
 * parameters in its own parameters storage
 */
class context
{
  /**
   * The context instance
   *
   * @var context
   */
  protected static $instance;
  
  /**
   * The current module's name
   *
   * @var string
   */
  protected $moduleName;
  
  /**
   * The current action's name
   *
   * @var string
   */
  protected $actionName;
  
  /**
   * The current controller
   *
   * @var controller
   */
  protected $controller;
  
  /**
   * Query string parameters
   *
   * @var mixed Array of values indexed by key (haha)
   */
  protected $parameters = array();
  
  protected static $count = 0;
  
  /**
   * Constructor. Will analyse REQUEST_URI to build itself
   *
   */
  protected function __construct()
  {
    //Analyse REQUEST_URI
    if (strpos($_SERVER['REQUEST_URI'], '?') !== false)
    {
      $uri = substr($_SERVER['REQUEST_URI'], 0, strpos($_SERVER['REQUEST_URI'], '?'));
      $qs = substr($_SERVER['REQUEST_URI'], strpos($_SERVER['REQUEST_URI'], '?') + 1);
    }
    else
    {
      $uri = $_SERVER['REQUEST_URI'];
      $qs = '';
    }
    
    //Remove apache directory prefix
    $uri = substr($uri, strlen(APACHE_DIRECTORY_PREFIX));
    
    //Remove mobile directory prefix and set parameter if found
    if ($uri == 'm' || substr($uri, 0, 2) == 'm/')
    {
      $this->parameters['mobile'] = true;
      $uri = $uri == 'm' ? '' : substr($uri, 2);
    }
    
    //Define directory prefix
    define('DIRECTORY_PREFIX', $this->getParameter('mobile') ? APACHE_DIRECTORY_PREFIX . 'm/' : APACHE_DIRECTORY_PREFIX);
    
    //Find action and module
    if (!strlen($uri))
    {
      $this->moduleName = $this->getParameter('mobile') ? CONTROLLER_DEFAULT_MODULE_MOBILE : CONTROLLER_DEFAULT_MODULE;
      $this->actionName = $this->getParameter('mobile') ? CONTROLLER_DEFAULT_ACTION_MOBILE : CONTROLLER_DEFAULT_ACTION;
    }
    else
    {
      $modact = explode('/', $uri);
      $this->moduleName = $modact[0];
      $this->actionName = isset($modact[1]) ? $modact[1] : CONTROLLER_DEFAULT_ACTION;
    }
    
    //Parse query string into parameters
    if ($qs)
    {
      $params = explode('&', $qs);
      foreach ($params as $aParam)
      {
        list($key, $value) = explode('=', $aParam);
        $this->parameters[$key] = $value;
      }
    }
  }
  
  /**
   * Launches the execution of the controller depending on the parameters
   *
   */
  protected function buildController()
  {
    //Instanciate action
    $controllerFile = 'module/' . $this->moduleName . '/' . $this->actionName . '.class.php';
    require_once $controllerFile;
    
    $controllerClass = 'controller_' . $this->moduleName . '_' . $this->actionName;
    if (!class_exists($controllerClass))
    {
      throw new controllerException('Controller class does not exists: ' . $controllerClass);
    }
    $this->controller = new $controllerClass($this->moduleName, $this->actionName);
  }
  
  /**
   * Forward to another action (shortcut to the context)
   *
   * @param string $module The module to forward to
   * @param string $action The action to forward to
   * @param mixed $parameters Array of query string parameters to pass to 
   */
  public function forward($module, $action, $parameters = array())
  {
    foreach ($parameters as $key => $value)
    {
      $this->parameters[$key] = $value;
    }
    $this->moduleName = $module;
    $this->actionName = $action;
    $this->buildController();
    $this->controller->run();
  }
  
  /**
   * Get the current module name
   *
   * @return string
   */
  public function getModuleName()
  {
    return $this->moduleName;
  }

  /**
   * Get the current action name
   *
   * @return string
   */
  public function getActionName()
  {
    return $this->actionName;
  }

  /**
   * Get the current action
   *
   * @return action
   */
  public function getController()
  {
    if (!$this->controller)
    {
      $this->buildController();
    }
    return $this->controller;
  }
  
  /**
   * Add a query string parameter
   *
   * @param string $name
   * @param mixed $value The value to set
   */
  public function addParameter($name, $value)
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
  public function getParameter($name, $defaultValue = null)
  {
    return isset($this->parameters[$name]) ? $this->parameters[$name] : $defaultValue;
  }
  
  /**
   * Get all parameters
   *
   * @return mixed Array of values indexed by string
   */
  public function getAllParameters()
  {
    return $this->parameters;
  }
  
  /**
   * Get the current context
   *
   * @return context
   */
  public static function getInstance()
  {
    if (!self::$instance)
    {
      self::$instance = new context();
    }
    
    return self::$instance;
  }
  
  /**
   * Return the path to the modules
   *
   * @return string
   */
  public static function getModulesPath()
  {
    return 'module';
  }
  
  /**
   * @return session
   */
  public static function getSession()
  {
    return session::getInstance();
  }
}
?>