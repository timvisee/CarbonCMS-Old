<?php

namespace carbon\core\database\lib\pdo;

use PDO;
use PDOException;

// Prevent direct requests to this file due to security reasons
defined('CARBON_CORE_INIT') or die('Access denied!');

abstract class DatabaseConnector extends \carbon\core\database\lib\sql\DatabaseConnector {

    /** @var PDO|null $pdo PDO connection instance */
    protected $pdo = null;

    /**
     * Connect to the database
     * @return bool True if succeed, false otherwise
     */
    public function connect() {
        // Get the PDO DSN, username and password
        $dsn = $this->getDsn();
        $user = $this->getUser();
        $pass = $this->getPass();

        try {
            // Connect to the database using PDO
            if($user !== null && $pass !== null)
                $this->pdo = new PDO($dsn, $user, $pass);
            else if($user !== null)
                $this->pdo = new PDO($dsn, $user);
            else
                $this->pdo = new PDO($dsn);

            // Return the result
            return true;

        } catch(PDOException $ex) {
            // Catch connection errors, reset the PDO state and return the result
            $this->pdo = null;
            return false;
        }
    }

    /**
     * Disconnect from the database
     */
    public function disconnect() {
        // Disconnect PDO by destroying it's object, set the instance to null afterwards
        unset($this->pdo);
        $this->pdo = null;
    }

    /**
     * Check whether there'statements an active connection
     * @return bool True if connected, false otherwise
     */
    public function isConnected() {
        return ($this->pdo !== null && $this->pdo instanceof PDO);
    }

    /**
     * Get the PDO DSN required to connect any database using PDO
     * @return string PDO DSN
     */
    public abstract function getDsn();

    // TODO: Should these transaction methods bellow be here, or should they be put somewhere else?

    /**
     * Start a transaction.
     *
     * @return bool True on success, false on failure.
     * Returns false if transitions aren't supported.
     */
    public function transactionStart() {
        return $this->pdo->beginTransaction();
    }

    /**
     * Commit the current transaction.
     *
     * @return bool True on success, false on failure.
     * Returns false if transitions aren't supported.
     */
    public function transactionCommit() {
        return $this->pdo->commit();
    }

    /**
     * Rollback the current transaction.
     *
     * @return bool True on success, false on failure.
     * Returns false if transitions aren't supported.
     */
    public function transactionRollBack() {
        return $this->pdo->rollBack();
    }

    /**
     * Check whether we're inside a transaction.
     *
     * @return bool True if we're inside a transaction, false otherwise.
     */
    public function isInTransaction() {
        return $this->pdo->inTransaction();
    }

    /**
     * Check whether this connector has transaction support.
     *
     * @return bool True if this connector has transaction support, false otherwise
     */
    public function isTransactionSupported() {
        // TODO: Make sure PDO supports transactions with the current driver!
        return true;
    }
}
