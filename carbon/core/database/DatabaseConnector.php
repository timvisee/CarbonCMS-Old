<?php

/**
 * DatabaseConnector.php
 * The database connector class which is an abstract base for all database connector classes.
 *
 * @author Tim Visee
 * @website http://timvisee.com/
 * @copyright Copyright © Tim Visee 2012-2013, All rights reserved.
 */

namespace carbon\core\database;

// Prevent direct requests to this file due to security reasons
defined('CARBON_CORE_INIT') or die('Access denied!');

/**
 * DatabaseConnector class
 *
 * @package core\database
 * @author Tim Visee
 */
abstract class DatabaseConnector {

    // TODO: Support charsets?

    /** string Default database host */
    const DEF_HOST = '127.0.0.1';
    /** int|null Default database port */
    const DEF_PORT = 3306;
    /** string|null Default database username */
    const DEF_USER = null;
    /** string|null Default database password */
    const DEF_PASS = null;
    /** string Default database name */
    const DEF_DBNAME = '';

    /** @var string Database host */
    protected $host = self::DEF_HOST;
    /** @var int|null Database port */
    protected $port = self::DEF_PORT;
    /** @var string|null Database user */
    protected $user = self::DEF_USER;
    /** @var string|null Database password */
    protected $pass = self::DEF_PASS;
    /** @var string Database name to connect to */
    protected $dbname = self::DEF_DBNAME;

    /**
     * Constructor
     *
     * @param string $host Database host
     * @param int|null $port Database port
     * @param string|null $user Database username
     * @param string|null $pass Database password
     * @param string $dbname Database name
     * @param bool $connect True to automatically connect
     */
    public abstract function __construct($host = self::DEF_HOST, $port = self::DEF_PORT, $user = self::DEF_USER, $pass = self::DEF_PASS, $dbname = self::DEF_DBNAME, $connect = true);

    public function __destruct() {
        // TODO: Close the database connection nicely, if it's still active!
    }

    /**
     * Connect to the database
     *
     * @return bool True if succeed, false otherwise
     */
    public abstract function connect();

    /**
     * Disconnect from the database
     */
    public abstract function disconnect();

    /**
     * Reconnect to the database
     *
     * @return bool True if succeed
     */
    public function reconnect() {
        // Disconnect from the database
        $this->disconnect();

        // Reconnect to the database, return the result
        return $this->connect();
    }

    /**
     * Check whether there'statements an active connection
     *
     * @return bool True if connected, false otherwise
     */
    public abstract function isConnected();

    /**
     * Get the database host
     *
     * @return string Database host
     */
    public function getHost() {
        return $this->host;
    }

    /**
     * Set the database host. Has to reconnect to the database to use a new host.
     *
     * @param string $host Database host
     *
     * @return bool True if the host has been changed, false if the host was invalid
     */
    protected function setHost($host = self::DEF_HOST) {
        // Make sure the host is a string
        if(!is_string($host))
            return false;

        // Set the host
        $this->host = $host;
        return true;
    }

    /**
     * Get the database port. Null will be returned if no port is specified.
     *
     * @return int|null Database port
     */
    public function getPort() {
        return $this->port;
    }

    /**
     * Set the database port. Has to reconnect to the database to use a new port.
     * @param int|null $port Database port
     * @return bool True if the port number has been changed, false if the port number is invalid
     */
    protected function setPort($port = self::DEF_PORT) {
        // Make sure the port is a valid number
        if(!is_int($port) && $port !== null)
            return false;

        // Make sure the port is in valid range
        if(($port < 1 || $port > 65535) && $port !== null)
            return false;

        // Set the port number
        $this->port = $port;
        return true;
    }

    /**
     * Get the database user. Null will be returned if no user is specified.
     *
     * @return string|null Database user
     */
    public function getUser() {
        return $this->user;
    }

    /**
     * Set the database user. Has to reconnect to the database to use the new user.
     *
     * @param string|null $user Database user. May be null to connect anonymously.
     *
     * @return bool True if the user has been changed, false if the user was invalid
     */
    protected function setUser($user = self::DEF_USER) {
        // Make sure the user is a string
        if(!is_string($user) && $user !== null)
            return false;

        // Set the database user
        $this->user = $user;
        return true;
    }

    /**
     * Get the database password. Null will be returned if no password is specified.
     *
     * @return string|null Database password
     */
    public function getPass() {
        return $this->pass;
    }

    /**
     * Set the database password. Has to reconnect to the database to use the new pass.
     *
     * @param string|null $pass Database password. May be null to connect anonymously.
     *
     * @return bool True if the pass has been changed, false if the pass was invalid
     */
    protected function setPass($pass = self::DEF_PASS) {
        // Make sure the pass is a string
        if(!is_string($pass) && $pass !== null)
            return false;

        // Set the database pass
        $this->pass = $pass;
        return true;
    }

    /**
     * Get the database name
     *
     * @return string Database name being used
     */
    public function getDatabaseName() {
        return $this->dbname;
    }

    /**
     * Set the database name. Has to reconnect to the database to use the new database name.
     *
     * @param string $dbname Database name to use.
     *
     * @return bool True if the database name has been changed, false if the database name was invalid
     */
    protected function setDatabaseName($dbname = self::DEF_DBNAME) {
        // Make sure the database name is a string
        if(!is_string($dbname))
            return false;

        // Set the database name
        $this->dbname = $dbname;
        return true;
    }

    // TODO: Should we add a exec() method like PDO::exec() ?

    // TODO: Should we implement attributes like PDO::getAttribute() ?

    /**
     * Executes a database statement. The result will be returned as a DatabaseStatement.
     *
     * @param DatabaseStatement $statement Database statement to execute
     *
     * @return DatabaseStatement Returned result set as a DatabaseStatement
     */
    public abstract function query($statement);

    /**
     * Start a transaction.
     *
     * @return bool True on success, false on failure.
     * Returns false if transitions aren't supported.
     */
    public abstract function transactionStart();

    /**
     * Commit the current transaction.
     *
     * @return bool True on success, false on failure.
     * Returns false if transitions aren't supported.
     */
    public abstract function transactionCommit();

    /**
     * Rollback the current transaction.
     *
     * @return bool True on success, false on failure.
     * Returns false if transitions aren't supported.
     */
    public abstract function transactionRollBack();

    /**
     * Check whether we're inside a transaction.
     *
     * @return bool True if we're inside a transaction, false otherwise.
     */
    public abstract function isInTransaction();

    /**
     * Check whether this connector has transaction support.
     *
     * @return bool True if this connector has transaction support, false otherwise
     */
    public abstract function isTransactionSupported();
}
