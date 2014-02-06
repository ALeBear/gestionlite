<?php
/**
 * @author Antoine Pouch <ant-1@pouch.name>
 * @package gestionlite
 */

/**
 * DB connector class. Use it to get a DB handle, for instance
 *
 */
class dbConnector
{
  /**
   * The DB handle
   *
   * @var PDO
   */
  protected static $dbh = false;
  
  /**
   * Initializes the DBHandle
   */
  public static function initialize($host, $db, $user, $pass)
  {
    self::$dbh = new PDO(sprintf('mysql:host=%s;dbname=%s', $host, $db), $user, $pass);
    self::$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  }
  
  /**
   * Returns a DB handle
   *
   */
  public static function getDBH()
  {
    return self::$dbh;
  }
}
?>