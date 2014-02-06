<?php
/**
 * @author Antoine Pouch <ant-1@pouch.name>
 * @package gestionlite
 * @module model
 */

require_once 'observablePersistent.class.php';

/**
 * Account class
 */
class account extends observablePersistent
{
  /**
   * Name of the DB table
   *
   * @var string
   */
  const TABLE_NAME = 'account';
  
  /**
   * ID of the 'EXTERNE' account
   *
   * @var integer
   */
  const EXTERNAL_ID = 1;
  
  /**
   * Is this account editable ?
   *
   * @var boolean
   */
  protected $editable = false;
  
  /**
   * Is this account a blackhole (i.e. the balance is always 0)
   *
   * @var boolean
   */
  protected $blackhole = false;
    
  /**
   * The name
   *
   * @var string
   */
  protected $name = null;
  
  /**
   * The balance, calculated each time a movement is recorded and stored in DB
   *
   * @var float
   */
  protected $balance = 0;
  
  
  /**
   * CRUD method : creates an instance from an array of parameters
   *
   * @param mixed $params The parameters to create the object
   * @return persistent The created instance
   */
  public static function create($params, $className = false)
  {
    return parent::create($params, __CLASS__);
  }
  
  /**
   * CRUD method : retrieve an instance from an ID
   *
   * @param integer $id The Object's ID
   * @return persistent The instance
   */
  public static function retrieve($id, $className = false)
  {
    return parent::retrieve($id, __CLASS__);
  }
  
  /**
   * Actually performs the save operation
   *
   * @return observablePersistent The current instance
   */
  protected function doSave()
  {
    //Checks
    if (!$this->name)
    {
      throw new modelException('Champs obligatoires manquants');
    }
    
    //Do not update balance for blackhole account
    if ($this->isBlackhole())
    {
      $this->balance = 0;
    }
    
    if ($this->isNew)
    {
      $sth = dbConnector::getDBH()->prepare(
        'INSERT INTO ' . self::TABLE_NAME . ' 
        (name, balance, deleted)
        VALUES(?, ?, ?)');
      $sth->execute(array(
        $this->name,
        $this->balance,
        $this->deleted));
    }
    else
    {
      $sth = dbConnector::getDBH()->prepare(
        'UPDATE ' . self::TABLE_NAME . ' 
        SET name = ?, balance = ?, deleted = ?
        WHERE id = ?');
      $sth->execute(array(
        $this->name,
        $this->balance,
        $this->deleted,
        $this->id));
    }
    
    return $this;
  }

  /**
   * Is this account editable ?
   *
   * @return boolean
   */
  public function isEditable()
  {
    return (boolean) $this->editable;
  }

  /**
   * Is this account a blackhole ?
   *
   * @return boolean
   */
  public function isBlackhole()
  {
    return (boolean) $this->blackhole;
  }
  
  /**
   * Event callback: when a movement is created or edited, update the concerned
   * accounts balances
   *
   * @param event $event
   * @return void
   */
  public static function updateBalance(event $event)
  {
    /* @var $movement movement */
    $movement = $event->getDispatcher();
    $from = self::retrieve($movement->getAccountFrom()->getId());
    $to = self::retrieve($movement->getAccountTo()->getId());
    
    if ($movement->isNew())
    {
      //New movement
      $from->setBalance($from->getBalance() - $movement->getAmount());
      $to->setBalance($to->getBalance() + $movement->getAmount());
      $from->save();
      $to->save();
    }
    else
    {
      //Updated movement
      if ($movement->hasChanged('deleted'))
      {
        //Deleted movement
        $from->setBalance($from->getBalance() + $movement->getAmount());
        $to->setBalance($to->getBalance() - $movement->getAmount());
        $from->save();
        $to->save();
      }
      elseif ($movement->getChanged('amount'))
      {
        //Amount changed
        $difference = $movement->getAmount() - $movement->getChanged('amount');
        $from->setBalance($from->getBalance() - $difference);
        $to->setBalance($to->getBalance() + $difference);
        $from->save();
        $to->save();
      }
    }
  }
  
  /**
   * Get the balance calculated from all previous movements. Useful if you want
   * to check that the balance calculated by events is accurate
   *
   * @return float
   */
  public function getCalculatedBalance()
  {
    if ($this->isNew())
    {
      return 0;
    }
    
    $balance = 0;
    foreach (movement::getAllForAccount($this->id) as $aMovement)
    {
      if ($aMovement->getAccountFrom()->getId() == $this->id)
      {
        $balance -= $aMovement->getAmount();
      }
      else
      {
        $balance += $aMovement->getAmount();
      }
    }
    
    return $balance;
  }
  
  /**
   * Get all accounts in an array, ordered by name
   *
   * @param boolean $deletedToo Get the deleted accounts also
   * @return mixed Array of account
   */
  public static function getAll($deletedToo = false)
  {
    $sql = 'SELECT id FROM ' . self::TABLE_NAME;
    if (!$deletedToo)
    {
      $sql .= ' WHERE deleted = 0';
    }
    $sql .= ' ORDER BY name ASC';
    
    $sth = dbConnector::getDBH()->prepare($sql);
    $sth->execute();
    $collection = array();
    while ($data = $sth->fetch(PDO::FETCH_ASSOC))
    {
      $collection[] = account::retrieve($data['id']);
    }
    
    return $collection;
  }

  /**
   * Return the expenses per month. Returns an array of amounts indexed by month
   * string (format YYYY-MM)
   *
   * @param integer $from A timestamp that will be translated into a month and
   * a year where data should begin to be drawn from
   * @param integer $to A timestamp that will be translated into a month and
   * a year where data should end to be drawn from
   * @param boolean $internals If true, show the expenses to another account. If
   * false, show the expenses to the EXTERNAL account
   * @return array
   */
  public function getExpensesPerMonth($from, $to, $internals = false)
  {
    if ($from >= $to)
    {
      return array();
    }
    
    //prepare dates
    $expenses = array();
    $currentTS = $from;
    while (date('Y-m', $currentTS) <= date('Y-m', $to))
    {
      $expenses[date('Y-m', $currentTS)] = 0;
      $currentTS = strtotime('+1 MONTH', $currentTS);
    }
    
    foreach (movement::getAllForAccount($this->id) as $aMovement)
    {
      $date = substr($aMovement->getDatePassed(), 0, 7);
      if (!isset($expenses[$date]))
      {
        continue;
      }
      if ($aMovement->getAccountFrom()->getId() == $this->id &&
          (($internals && $aMovement->getAccountTo()->getId() != self::EXTERNAL_ID)
          || (!$internals && $aMovement->getAccountTo()->getId() == self::EXTERNAL_ID)))
      {
        $expenses[$date] += $aMovement->getAmount();
      }
    }
    
    return $expenses;
  }

  /**
   * Return the credits per month. Returns an array of amounts indexed by month
   * string (format YYYY-MM)
   *
   * @param integer $from A timestamp that will be translated into a month and
   * a year where data should begin to be drawn from
   * @param integer $to A timestamp that will be translated into a month and
   * a year where data should end to be drawn from
   * @param boolean $internals If true, show the expenses to another account. If
   * false, show the expenses to the EXTERNAL account
   * @return array
   */
  public function getCreditsPerMonth($from, $to, $internals = false)
  {
    if ($from >= $to)
    {
      return array();
    }
    
    //prepare dates
    $credits = array();
    $currentTS = $from;
    while (date('Y-m', $currentTS) <= date('Y-m', $to))
    {
      $credits[date('Y-m', $currentTS)] = 0;
      $currentTS = strtotime('+1 MONTH', $currentTS);
    }
    
    foreach (movement::getAllForAccount($this->id) as $aMovement)
    {
      $date = substr($aMovement->getDatePassed(), 0, 7);
      if (!isset($credits[$date]))
      {
        continue;
      }
      if ($aMovement->getAccountTo()->getId() == $this->id &&
          (($internals && $aMovement->getAccountFrom()->getId() != self::EXTERNAL_ID)
          || (!$internals && $aMovement->getAccountFrom()->getId() == self::EXTERNAL_ID)))
      {
        $credits[$date] += $aMovement->getAmount();
      }
    }
    
    return $credits;
  }
  
  /**
   * Return the name of the table
   *
   * @return string
   */
  public static function getTableName()
  {
    return self::TABLE_NAME;
  }
}
?>