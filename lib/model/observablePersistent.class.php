<?php
/**
 * @author Antoine Pouch <ant-1@pouch.name>
 * @package gestionlite
 * @module model
 */

require_once 'event/listenable.class.php';
require_once 'persistent.if.php';
require_once 'dbConnector.class.php';
require_once 'modelException.class.php';

abstract class observablePersistent extends listenable implements persistent
{
  /**
   * Is this a new instance of the class ?
   *
   * @var boolean
   */
  protected $isNew = true;
  
  /**
   * Numeric ID
   *
   * @var integer
   */
  protected $id;
  
  /**
   * Is this an instance of a deleted object ?
   *
   * @var boolean
   */
  protected $deleted = 0;
  
  /**
   * Array of changed parameters names with their previous values
   *
   * @var mixed Array of values indexed by name
   */
  protected $changed = array();
  
  /**
   * CRUD method : creates an instance from an array of parameters
   *
   * @param mixed $params The parameters to create the object
   * @return persistent The created instance
   */
  public static function create($params, $className = false)
  {
    $instance = new $className();
    $instance->loadFromArray($params);
    return $instance->save();
  }
  
  /**
   * CRUD method : retrieve an instance from an ID
   *
   * @param integer $id The Object's ID
   * @return persistent The instance
   */
  public static function retrieve($id, $className = false)
  {
    $sth = dbConnector::getDBH()->prepare('SELECT * FROM ' . call_user_func(array($className, 'getTableName')) . ' WHERE id = ?');
    $sth->execute(array($id));
    $data = $sth->fetch(PDO::FETCH_ASSOC);
    if (!$data)
    {
      return null;
    }
    
    $instance = new $className();
    $instance->loadFromArray($data);
    $instance->setLoaded();
    
    return $instance;
  }
  
  /**
   * CRUD method : updates an instance from an array of parameters
   *
   * @param persistent $instance The instance to update
   * @param mixed $params The parameters to update for the instance
   * @return persistent The Updated instance
   */
  public static function update($instance, $params)
  {
    $instance->loadFromArray($params);
    return $instance->save();
  }
    
  /**
   * CRUD method : delete an object
   *
   * @param mixed $params The parameters to create the object
   * @return observablePersistent The deleted instance
   */
  public static function delete($instance)
  {
    $instance->setDeleted(true);
    return $instance->save();
  }
    
  /**
   * Saves the object into the persistence
   *
   * @return persistent The current object
   */
  public function save()
  {
    $this->dispatch(new event('save.pre', $this));
    $retVal = $this->doSave();
    if ($this->isNew)
    {
      $this->setId(dbConnector::getDBH()->lastInsertId());
    }
    $this->dispatch(new event('save.post', $this));
    $this->isNew = false;
    $this->changed = array();
    
    return $retVal;
  }
    
  /**
   * Is this an instance of a never saved object ?
   *
   * @return boolean
   */
  public function isNew()
  {
    return $this->isNew;
  }
    
  /**
   * Magic function
   *
   * @return mixed
   */
  public function __call($name, $arguments)
  {
    //Debug but useful
    if (strlen($name) < 3) {
      echo '<pre>';
      print_r(debug_backtrace());
      exit;
    }
    
    $property = strtolower($name{3}) . substr($name, 4);
    
    //Automatic getters and setters
    if (substr($name, 0, 3) == 'get')
    {
      return $this->$property;
    }
    if (substr($name, 0, 3) == 'set')
    {
      $this->setChanged($property, $this->$property);
      $this->$property = $arguments[0];
      
      return $this;
    }
  }
  
  /**
   * Set this property as changed
   *
   * @param string $name The name of the property
   * @param mixed $oldValue The previous value before the change
   */
  protected function setChanged($name, $oldValue)
  {
    $this->changed[$name] = $oldValue;
  }
  
  /**
   * Was the given property changed from the retrieved object ?
   *
   * @param string $name The name of the property
   * @return boolean
   */
  public function hasChanged($name)
  {
    return isset($this->changed[$name]);
  }
  
  /**
   * Get the value of a changed property of the current instance
   *
   * @param string $name The name of the property
   * @return mixed The previous value, or null if it was not changed
   */
  public function getChanged($name)
  {
    return $this-> hasChanged($name) ? $this->changed[$name] : null;
  }
  
  /**
   * Set the instance as just loaded. Will correct a few things.
   * DO NOT use, used when retrieved.
   *
   * @return observablePersistent The instanc
   */
  public function setLoaded()
  {
    $this->isNew = false;
    $this->changed = array();
    return $this;
  }
  
  /**
   * Load data into an instance from an array
   *
   * @param mixed $data The data, taken straight from DB, assoc array
   * @return observablePersistent The instance
   */
  public function loadFromArray($data)
  {
    foreach ($data as $field => $value)
    {
      $method = 'set' . str_replace(' ', '', ucwords(str_replace('_', ' ', $field)));
      $this->$method($value);
    }
    return $this;
  }
  
  /**
   * Actually performs the save operation
   *
   * @return observablePersistent The current instance
   */
  protected abstract function doSave();
  
  /**
   * Return the name of the table
   *
   * @return string
   */
  public static function getTableName()
  {
    throw new modelException();
  }
}
?>