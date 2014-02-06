<?php
/**
 * @author Antoine Pouch <ant-1@pouch.name>
 * @package gestionlite
 * @module model
 */

require_once 'observablePersistent.class.php';

/**
 * Movement template class : represent a template of movement for the quick
 * movement creation form
 */
class movementTemplate extends observablePersistent
{
  /**
   * Name of the DB table
   *
   * @var string
   */
  const TABLE_NAME = 'movement_template';
  
  /**
   * The account where the movement comes from
   *
   * @var account
   */
  protected $accountFrom;
  
  
  /**
   * The account where the movement goes to
   *
   * @var account
   */
  protected $accountTo;
  
    
  /**
   * The movement's label
   *
   * @var float
   */
  protected $movementLabel;
  
  /**
   * The template's label
   *
   * @var string
   */
  protected $label;
  
  /**
   * The default amount for the movement (can be null)
   *
   * @var float
   */
  protected $amount;
  
  
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
   * Retrieve the last created instance of a recurring
   *
   * @param integer $id The recurring's ID
   * @return persistent The instance, null if none found
   */
  public static function getLastForRecurring($recurringId)
  {
    $sth = dbConnector::getDBH()->prepare(
      'SELECT id  FROM ' . self::TABLE_NAME . ' 
      WHERE occurence_of = ?
      ORDER BY date_passed DESC
      LIMIT 1');
    $sth->execute(array($recurringId));
    if ($data = $sth->fetch(PDO::FETCH_ASSOC))
    {
      return movement::retrieve($data['id']);
    }
  }
  
  /**
   * Set the account from from an ID
   *
   * @param integer $accountID
   * @return movement The current instance
   */
  public function setAccountFrom($accountID)
  {
    $this->setChanged('accountFrom', $this->accountFrom);
    $this->accountFrom = account::retrieve($accountID);
    return $this;
  }

  /**
   * Set the account to from an ID
   *
   * @param integer $accountID
   * @return movement The current instance
   */
  public function setAccountTo($accountID)
  {
    $this->setChanged('accountTo', $this->accountTo);
    $this->accountTo = account::retrieve($accountID);
    return $this;
  }
  
  /**
   * Actually performs the save operation
   *
   * @return observablePersistent The current instance
   */
  protected function doSave()
  {
    //Checks
    if (!$this->accountFrom || !$this->accountTo || !$this->label || !$this->movementLabel)
    {
      throw new modelException('Champs obligatoires manquants');
    }
    if ($this->accountFrom->getId() == $this->accountTo->getId())
    {
      throw new modelException('Les comptes de départ et d\'arrivée ne peuvent être les mêmes');
    }
    
    if ($this->isNew)
    {
      $sth = dbConnector::getDBH()->prepare(
        'INSERT INTO ' . self::TABLE_NAME . ' 
        (account_from, account_to, amount, label, movement_label, deleted)
        VALUES(?, ?, ?, ?, ?, ?)');
      $sth->execute(array(
        $this->accountFrom->getId(),
        $this->accountTo->getId(),
        $this->amount,
        $this->label,
        $this->movementLabel,
        $this->deleted));
    }
    else
    {
      $sth = dbConnector::getDBH()->prepare(
        'UPDATE ' . self::TABLE_NAME . '
        SET account_from = ?, account_to = ?, amount = ?, label = ?, movement_label = ?, deleted = ?
        WHERE id = ?');
      $sth->execute(array(
        $this->accountFrom->getId(),
        $this->accountTo->getId(),
        $this->amount,
        $this->label,
        $this->movementLabel,
        $this->deleted,
        $this->id));
    }
    
    return $this;
  }
  
  /**
   * Get all movement templates, ordered by template label ASC
   *
   * @return mixed Array of movementTemplate
   */
  public static function getAll($deletedToo = false)
  {
    $sql = 'SELECT id FROM ' . self::TABLE_NAME;
    if (!$deletedToo)
    {
      $sql .= ' WHERE deleted = 0';
    }
    $sql .= ' ORDER BY label ASC';
    
    $sth = dbConnector::getDBH()->prepare($sql);
    $sth->execute();
    $collection = array();
    while ($data = $sth->fetch(PDO::FETCH_ASSOC))
    {
      $collection[] = movementTemplate::retrieve($data['id']);
    }
    
    return $collection;
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