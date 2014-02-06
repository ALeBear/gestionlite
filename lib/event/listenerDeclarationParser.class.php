<?php
/**
 * @author Antoine Pouch <ant-1@pouch.name>
 * @package gestionlite
 * @module event
 */

/**
 * This class is used to parse event listeners declaration files. These are ini
 * files in the form :
 * [listenable_class_name]
 * eventName[] = "callback_class_name,callback_staticmethod[,callbackparam1=value,...]";
 */
abstract class listenerDeclarationParser
{
  const DECLARATION_FILENAME = 'listeners.ini';
  
  protected $files = array();
  
  /**
   * Parse the given directories. This function accepts a variable number of
   * arguments, each one of it is a directory that can contain listeners
   * declaration files, named after the class constant DECLARATION_FILENAME
   *
   */
  public static function parse()
  {
    foreach (func_get_args() as $aDirectory)
    {
      $filename = $aDirectory . '/' . self::DECLARATION_FILENAME;
      if (file_exists($filename))
      {
        $listeners = parse_ini_file($filename, true);
        foreach ($listeners as $listenableClass => $events)
        {
          if (!class_exists($listenableClass))
          {
            continue;
          }
          foreach ($events as $eventName => $callbacks)
          {
            foreach ($callbacks as $aCallbackDefinition)
            {
              $callbackDef = explode(',', $aCallbackDefinition);
              if (count($callbackDef) < 2) {
                continue;
              }
              $calledClass  = array_shift($callbackDef);
              $calledMethod = array_shift($callbackDef);
              $params = array();
              if (count($callbackDef))
              {
                foreach ($callbackDef as $aParamDef)
                {
                  list($paramKey, $paramValue) = explode('=', $aParamDef);
                  $params[$paramKey] = $paramValue;
                }
              }
              call_user_func(array($listenableClass, 'addListener'), $listenableClass, $eventName, array($calledClass, $calledMethod), $params);
            }
          }
        }
      }
    }
  }
}
?>