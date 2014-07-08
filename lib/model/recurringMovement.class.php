<?php
/**
 * @author Antoine Pouch <ant-1@pouch.name>
 * @package gestionlite
 * @module model
 */

require_once 'observablePersistent.class.php';
require_once 'model/account.class.php';
require_once 'model/movement.class.php';

/**
 * Recurring movement class : represent a recurring movement of money. Not a
 * real movement !
 */
class recurringMovement extends observablePersistent
{
    /**
     * Name of the DB table
     *
     * @var string
     */
    const TABLE_NAME = 'recurring_movement';

    /**
     * Recurrence frequency : every X week
     * @var string
     */
    const FREQ_WEEK = 'W';

    /**
     * Recurrence frequency : every X month
     * @var string
     */
    const FREQ_MONTH = 'M';

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
     * The amount
     *
     * @var float
     */
    protected $amount;

    /**
     * The label
     *
     * @var string
     */
    protected $label;

    /**
     * The frequency type (see class constants)
     *
     * @var string
     */
    protected $frequencyType;

    /**
     * The frequency reccurence (the X in every X Month/Week)
     *
     * @var integer
     */
    protected $frequencyEvery;

    /**
     * The frequency 'on' : day number (1 = monday) for weekly, the day number
     * for monthly
     *
     * @var integer
     */
    protected $frequencyOn;

    /**
     * until when the recurring movement happens. After that date, it will be
     * automatically deleted.
     *
     * @var string Date in MySQL format
     */
    protected $until;


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
        if (!$this->isNew() && !$this->hasChanged('deleted')) {
            throw new modelException('Impossible de modifier un paiement recurrent, il faut le supprimer');
        }
        if (!$this->accountFrom || !$this->accountTo || !$this->amount
            || !$this->frequencyEvery || !$this->frequencyOn || !$this->frequencyType
        ) {
            throw new modelException('Champs obligatoires manquants');
        }
        if (!in_array($this->frequencyType, array(self::FREQ_MONTH, self::FREQ_WEEK))) {
            throw new modelException('Fréquence inconnue : ' . $this->frequencyType);
        }
        if ($this->frequencyType == self::FREQ_MONTH && ($this->frequencyOn < 1 || $this->frequencyOn > 31)) {
            throw new modelException('Mauvais jour de récurrence pour une frequence mensuelle : ' . $this->frequencyOn);
        }
        if ($this->frequencyType == self::FREQ_WEEK && ($this->frequencyOn < 1 || $this->frequencyOn > 7)) {
            throw new modelException('Mauvais jour de récurrence pour une frequence hebdomadaire : ' . $this->frequencyOn);
        }
        if (!preg_match('/\d+/', $this->frequencyEvery)) {
            throw new modelException('Récurrence erronnée : ' . $this->frequencyEvery);
        }
        if ($this->accountFrom->getId() == $this->accountTo->getId()) {
            throw new modelException('Les comptes de départ et d\'arrivée ne peuvent être les mêmes');
        }

        if ($this->until == '0000-00-00') {
            $this->until = null;
        }

        if ($this->isNew) {
            $sth = dbConnector::getDBH()->prepare(
                'INSERT INTO ' . self::TABLE_NAME . '
        (account_from, account_to, amount, label, frequency_type, frequency_every, frequency_on, until, deleted)
        VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?)');
            $sth->execute(array(
                $this->accountFrom->getId(),
                $this->accountTo->getId(),
                $this->amount,
                $this->label,
                $this->frequencyType,
                $this->frequencyEvery,
                $this->frequencyOn,
                $this->until,
                $this->deleted));
        } else {
            $sth = dbConnector::getDBH()->prepare(
                'UPDATE ' . self::TABLE_NAME . '
        SET deleted = ?
        WHERE id = ?');
            $sth->execute(array(
                $this->deleted,
                $this->id));
        }

        return $this;
    }

    /**
     * Get the last movement date by querying existing payments
     *
     * @return string The date in Y-m-d H:i:s format, null if no previous movement
     */
    public function getLastDate()
    {
        $lastMovement = movement::getLastForRecurring($this->id);
        return $lastMovement ? $lastMovement->getDatePassed() : null;
    }

    /**
     * Get the next recurring movement date. If no movements were made yet, will
     * calculate the next date without accounting for frequencyEvery
     *
     * @return string Date in Y-m-d format
     */
    public function getNextDate()
    {
        //If there was a previous movement, just add the frequency
        if ($last = $this->getLastDate()) {
            return $this->getNextDateFromLast($last);
        }

        //First movement, get the nearest date
        switch ($this->frequencyType) {
            case self::FREQ_MONTH:
                if (date('d') <= $this->frequencyOn) {
                    return date('Y-m-') . $this->frequencyOn;
                } else {
                    return date('Y-m-', strtotime('+1 MONTHS')) . $this->frequencyOn;
                }
                break;
            case self::FREQ_WEEK:
                if (date('N') <= $this->frequencyOn) {
                    $difference = $this->frequencyOn - date('N');
                } else {
                    $difference = 7 - (date('N') - $this->frequencyOn);
                }
                return date('Y-m-d', strtotime('+' . $difference . ' DAYS'));
                break;
        }
    }

    /**
     * Calculate the next occurence date from a previous date
     *
     * @param unknown_type $lastDate
     * @return unknown
     */
    protected function getNextDateFromLast($lastDate)
    {
        switch ($this->frequencyType) {
            case self::FREQ_MONTH:
                return date('Y-m-d', strtotime('+' . $this->frequencyEvery . ' MONTHS', strtotime($lastDate)));
            case self::FREQ_WEEK:
                $freq = $this->frequencyEvery * 7;
                return date('Y-m-d', strtotime('+' . $freq . ' DAYS', strtotime($lastDate)));
        }
    }

    /**
     * Get a prototype movement
     *
     * @return movement
     */
    public function getPrototypeMovement()
    {
        $instance = new movement();
        return $instance->loadFromArray(array(
            'occurence_of' => $this->getId(),
            'account_from' => $this->accountFrom->getId(),
            'account_to' => $this->accountTo->getId(),
            'label' => $this->label,
            'amount' => $this->amount
        ));
    }

    /**
     * Get all recurring movements in an array, ordered by label
     *
     * @param boolean $deletedToo Get the deleted accounts also
     * @return mixed Array of account
     */
    public static function getAll($deletedToo = false)
    {
        $sql = 'SELECT id FROM ' . self::TABLE_NAME;
        if (!$deletedToo) {
            $sql .= ' WHERE deleted = 0';
        }
        $sql .= ' ORDER BY label ASC';

        $sth = dbConnector::getDBH()->prepare($sql);
        $sth->execute();
        $collection = array();
        while ($data = $sth->fetch(PDO::FETCH_ASSOC)) {
            $collection[] = recurringMovement::retrieve($data['id']);
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