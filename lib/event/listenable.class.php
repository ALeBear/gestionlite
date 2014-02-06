<?php
/**
 * @author Antoine Pouch <ant-1@pouch.name>
 * @package gestionlite
 * @module event
 */

require_once 'event.class.php';

/**
 * Parent for listenable objects: they will dispatch events and other objects
 * can listen to their events
 */
abstract class listenable
{
  /**
   * Array of listeners
   *
   * @var mixed Complex array, see addListener()
   */
  protected static $listeners = array();
  
  /**
   * Add a listener to this class (not instance !). All instances will use the
   * listeners when dispatching events.
   *
   * @param string $class The class that is listened to
   * @param string $eventName The name of the event
   * @param mixed $callback The callback definition
   * @param mixed $callbackParameters The callback parameters
   * @return void
   */
  public static function addListener($class, $eventName, $callback, $callbackParameters = array())
  {
    self::$listeners[$class][$eventName][] = array(
      'CALLBACK'            => $callback,
      'CALLBACK_PARAMETERS' => $callbackParameters
    );
  }
  
  /**
   * Dispatch an event
   *
   * @param event $event
   */
  public function dispatch(event $event)
  {
    if (!isset(self::$listeners[get_class($this)][$event->getName()]))
    {
      return;
    }
    
    foreach (self::$listeners[get_class($this)][$event->getName()] as $aListener)
    {
      if (is_array($aListener['CALLBACK_PARAMETERS']))
      {
        foreach ($aListener['CALLBACK_PARAMETERS'] as $key => $value)
        {
          $event->setParameter($key, $value);
        }
      }
      call_user_func($aListener['CALLBACK'], $event);
    }
  }
}
?>