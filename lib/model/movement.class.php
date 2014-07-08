<?php
/**
 * @author Antoine Pouch <ant-1@pouch.name>
 * @package gestionlite
 * @module model
 */

require_once 'observablePersistent.class.php';
require_once 'model/recurringMovement.class.php';

/**
 * Movement class : represent a movement of money from an account to another
 */
class movement extends observablePersistent
{
    /**
     * Name of the DB table
     *
     * @var string
     */
    const TABLE_NAME = 'movement';

    /**
     * Date/time of the movement
     *
     * @var strign Date in MySQL DATETIME format
     */
    protected $datePassed = null;

    /**
     * If not null, indicates this movement is an instance of a recurring movement
     *
     * @var recurringMovement
     */
    protected $occurenceOf = null;

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
     * The movement's amount
     *
     * @var float
     */
    protected $amount;

    /**
     * The movement's label
     *
     * @var string
     */
    protected $label;

    /**
     * The movement's flags
     *
     * @var integer
     */
    protected $flags = 0;

    /**
     * When was this movement certified in the real bank account ? Can be null if
     * never was.
     *
     * @var string
     */
    protected $certifiedAt = null;


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
        if ($data = $sth->fetch(PDO::FETCH_ASSOC)) {
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
     * Set the occurenceOf from a recurring_movement ID
     *
     * @param integer $recurringID
     * @return movement The current instance
     */
    public function setOccurenceOf($recurringID)
    {
        $this->setChanged('occurenceOf', $this->occurenceOf);
        if ($recurringID) {
            $this->occurenceOf = recurringMovement::retrieve($recurringID);
        }
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
        if (!$this->accountFrom || !$this->accountTo || !$this->amount || !$this->datePassed) {
            throw new modelException('Champs obligatoires manquants');
        }
        if (!$this->isNew() && ($this->hasChanged('accountFrom') || $this->hasChanged('accountTo'))) {
            throw new modelException('Impossible de changer les comptes d\'un mouvement');
        }
        if (!$this->isNew() && $this->hasChanged('occurenceOf')) {
            throw new modelException('Impossible de changer le paiement recurrent d\'un mouvement');
        }
        if ($this->accountFrom->getId() == $this->accountTo->getId()) {
            throw new modelException('Les comptes de départ et d\'arrivée ne peuvent être les mêmes');
        }

        if ($this->isNew) {
            $sth = dbConnector::getDBH()->prepare(
                'INSERT INTO ' . self::TABLE_NAME . '
        (date_passed, occurence_of, account_from, account_to, amount, label, certified_at, flags, deleted)
        VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?)');
            $sth->execute(array(
                $this->datePassed,
                $this->occurenceOf ? $this->occurenceOf->getId() : null,
                $this->accountFrom->getId(),
                $this->accountTo->getId(),
                $this->amount,
                $this->label,
                $this->certifiedAt,
                $this->flags,
                $this->deleted));
        } else {
            $sth = dbConnector::getDBH()->prepare(
                'UPDATE ' . self::TABLE_NAME . '
        SET date_passed = ?, occurence_of = ?, account_from = ?, account_to = ?, amount = ?, label = ?, certified_at = ?, flags = ?, deleted = ?
        WHERE id = ?');
            $sth->execute(array(
                $this->datePassed,
                $this->occurenceOf ? $this->occurenceOf->getId() : null,
                $this->accountFrom->getId(),
                $this->accountTo->getId(),
                $this->amount,
                $this->label,
                $this->certifiedAt,
                $this->flags,
                $this->deleted,
                $this->id));
        }

        return $this;
    }

    /**
     * Get all movements for one month, ordered by date DESC
     *
     * @param integer $year
     * @param integer $month
     * @param boolean $deletedToo Should we get teh deleted movements too ?
     * @return mixed Array of movement
     */
    public static function getAllForMonth($year, $month, $deletedToo = false)
    {
        $sql = 'SELECT id FROM ' . self::TABLE_NAME
            . ' WHERE YEAR(date_passed) = ? AND MONTH(date_passed) = ?';
        if (!$deletedToo) {
            $sql .= ' AND deleted = 0';
        }
        $sql .= ' ORDER BY date_passed DESC';

        $sth = dbConnector::getDBH()->prepare($sql);
        $sth->execute(array($year, $month));
        $collection = array();
        while ($data = $sth->fetch(PDO::FETCH_ASSOC)) {
            $collection[] = movement::retrieve($data['id']);
        }

        return $collection;
    }

    /**
     * Get all movements for one account, ordered by date ASC
     *
     * @param integer $accountID
     * @param boolean $deletedToo Should we get teh deleted movements too ?
     * @return mixed Array of movement
     */
    public static function getAllForAccount($accountID, $deletedToo = false)
    {
        $sql = 'SELECT id FROM ' . self::TABLE_NAME
            . ' WHERE (account_from = ? OR account_to = ?)';
        if (!$deletedToo) {
            $sql .= ' AND deleted = 0';
        }
        $sql .= ' ORDER BY date_passed ASC';

        $sth = dbConnector::getDBH()->prepare($sql);
        $sth->execute(array($accountID, $accountID));
        $collection = array();
        while ($data = $sth->fetch(PDO::FETCH_ASSOC)) {
            $collection[] = movement::retrieve($data['id']);
        }

        return $collection;
    }

    /**
     * Get the number of movements for one account
     *
     * @param integer $accountID
     * @param boolean $deletedToo Should we get the deleted movements too ?
     * @return integer
     */
    public static function getCountForAccount($accountID, $deletedToo = false)
    {
        $sql = 'SELECT COUNT(*) FROM ' . self::TABLE_NAME
            . ' WHERE (account_from = ? OR account_to = ?)';
        if (!$deletedToo) {
            $sql .= ' AND deleted = 0';
        }
        $sql .= ' ORDER BY date_passed DESC';

        $sth = dbConnector::getDBH()->prepare($sql);
        $sth->execute(array($accountID, $accountID));

        return $sth->fetchColumn();
    }

    /**
     * Get the last movements for an account (ordered by date DESC)
     *
     * @param integer $accountID
     * @param integer $count How much to get ?
     * @param boolean $deletedToo Should we get the deleted movements too ?
     * @return integer
     */
    public static function getLastForAccount($accountID, $count, $deletedToo = false)
    {
        $sql = 'SELECT id FROM ' . self::TABLE_NAME
            . ' WHERE (account_from = ? OR account_to = ?)';
        if (!$deletedToo) {
            $sql .= ' AND deleted = 0';
        }
        $sql .= ' ORDER BY date_passed DESC LIMIT 0, ' . $count;

        $sth = dbConnector::getDBH()->prepare($sql);
        $sth->execute(array($accountID, $accountID));

        $movements = array();
        while ($data = $sth->fetch(PDO::FETCH_ASSOC)) {
            $movements[] = movement::retrieve($data['id']);
        }

        return $movements;
    }

    /**
     * Get months with movements
     *
     * @return mixed array of arrays: first index is year, then values are months
     */
    public static function getMonthsWithMovements()
    {
        $sql = 'SELECT CONCAT(YEAR(date_passed), "-", LPAD(MONTH(date_passed), 2, "0")) AS mth FROM ' . self::TABLE_NAME
            . ' WHERE deleted = 0';
        $sql .= ' GROUP BY mth ORDER BY mth DESC';

        $sth = dbConnector::getDBH()->prepare($sql);
        $sth->execute();

        $months = array();
        while ($data = $sth->fetch(PDO::FETCH_ASSOC)) {
            $months[] = $data['mth'];
        }

        return $months;
    }

    /**
     * Get the timestamp of the first movement in the whole DB
     *
     * @return integer the timestamp of the first movement
     */
    public static function getFirstMovementTimestamp()
    {
        $sql = 'SELECT MIN(date_passed) AS ts FROM ' . self::TABLE_NAME;

        $sth = dbConnector::getDBH()->prepare($sql);
        $sth->execute();

        $data = $sth->fetch(PDO::FETCH_ASSOC);
        return strtotime($data['ts']);
    }

    /**
     * Certify all movements not already certified
     * @return void
     */
    public static function certifyAll()
    {
        $sql = 'UPDATE ' . self::TABLE_NAME . ' SET certified_at = NOW() WHERE certified_at IS NULL';

        $sth = dbConnector::getDBH()->prepare($sql);
        $sth->execute();
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