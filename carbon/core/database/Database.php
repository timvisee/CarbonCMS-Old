<?php

// TODO: Add debug modes to this class for easy built-in debugging?

namespace carbon\core\database;

// Prevent direct requests to this file due to security reasons
use carbon\core\util\ClassUtils;

defined('CARBON_CORE_INIT') or die('Access denied!');

/**
 * Database class.
 *
 * @package carbon\core\database
 */
abstract class Database {

    /** @var DatabaseConnector|null $con Database connector */
    protected $con = null;

    /**
     * Get the database connector
     * @return DatabaseConnector|null Database connector instance or null.
     */
    public function getConnector() {
        return $this->con;
    }

    /**
     * Set the database connector.
     *
     * @param DatabaseConnector|null $con Database connector instance or null.
     *
     * @return bool True if the database connector was set successfully. False otherwise.
     */
    protected function setConnector($con = null) {
        // Make sure the $con param is an instance of a database connector or null.
        if($con !== null)
            if(!$con instanceof DatabaseConnector)
                return false;

        // Set the database connector, return true
        $this->con = $con;
        return true;
    }

    /**
     * Check whether there'statements an active connection to the server.
     * @return bool True if there's an active connection to the database, false otherwise
     */
    public function isConnected() {
        // Make sure a connector is set
        if($this->con === null)
            return false;
        if(!$this->con instanceof DatabaseConnector)
            return null;

        // Check whether there's an active connection, return the result
        return $this->con->isConnected();
    }

    /**
     * Start a transaction.
     *
     * @return bool True on success, false on failure.
     * Returns false if transitions aren't supported.
     */
    public function transactionStart() {
        return $this->con->transactionStart();
    }

    /**
     * Commit the current transaction.
     *
     * @return bool True on success, false on failure.
     * Returns false if transitions aren't supported.
     */
    public function transactionCommit() {
        return $this->con->transactionCommit();
    }

    /**
     * Rollback the current transaction.
     *
     * @return bool True on success, false on failure.
     * Returns false if transitions aren't supported.
     */
    public function transactionRollBack() {
        return $this->con->transactionRollBack();
    }

    /**
     * Check whether we're inside a transaction.
     *
     * @return bool True if we're inside a transaction, false otherwise.
     */
    public function isInTransaction() {
        return $this->con->isInTransaction();
    }

    /**
     * Check whether this connector has transaction support.
     *
     * @return bool True if this connector has transaction support, false otherwise
     */
    public function isTransactionSupported() {
        return $this->con->isTransactionSupported();
    }

    /**
     * Execute a single, or multiple queries and return the number of affected rows.
     *
     * @param string|DatabaseStatement|DatabaseBatch|Array $queries
     * Database query as a string, as a DatabaseStatement or a few queries as a DatabaseBatch.
     * Multiple of the above could also be combined in an array, which may contain even more sub-arrays.
     *
     * @return int Number of total affected rows
     */
    // TODO: Check whether queries are only supported (and not the other statement types)
    public function exec($queries) {
        // Convert the queries into a query list
        $queries = self::asStatementList($queries);

        // Make sure the $queries variable isn't null
        if($queries === null)
            return 0;

        // Create a variable to keep track of the number of affected rows
        $rows = 0;

        // Execute each query
        foreach($queries as $query) {
            // TODO: Execute query, add number of affected rows to $rows
        }

        // Return the number of affected rows
        return $rows;
    }

    /**
     * Create a list of statement strings based on a single statement string, a DatabaseStatement, a DatabaseBatch or an array
     * containing any of these items. The array may contain sub-arrays with items.
     *
     * @param string|DatabaseStatement|DatabaseBatch|Array $statements A database statement string, a DatabaseStatement,
     * a DatabaseBatch or an array with any of these items. The array may also contain sub-arrays with items.
     *
     * @return Array|null Array containing all statements as a string. Null on failure.
     */
    // TODO: Return as database statement instead of a regular string
    protected static function asStatementList($statements) {
        // Get the DatabaseStatement class dynamically
        // TODO: Use the DatabaseDriver class to dynamically get the proper statement class
        $dbStatementClass = ClassUtils::getNamespace(get_called_class()) . '\\DatabaseStatement';

        // Make sure the $statements param isn't null
        if($statements === null)
            return null;

        // Process the $statements param is it's a string
        if(is_string($statements))
            return Array(new $dbStatementClass($statements));

        // Process the $statement param if it's a DatabaseStatement
        if($statements instanceof DatabaseStatement)
            // TODO: Make sure the statement is valid
            return Array($statements);

        // Process the $statement param if it's a DatabaseBatch
        if($statements instanceof DatabaseBatch)
            return self::asStatementList($statements->getStatements());

        // Make sure the $statements param is an array
        if(!is_array($statements))
            return null;

        // Create an array to hold all statements which need to be returned
        $out = Array();

        // Add each statement from the array to the return list
        foreach($statements as $statement) {
            // Convert the entry into a statement list
            $list = self::asStatementList($statement);

            // Make sure the result isn't null
            if($list === null)
                return null;

            // Add the result to the statement list
            $out = array_merge($out, $list);
        }

        // Return the list of statements
        return $out;
    }















    protected $preparedStatement = null;

    /**
     * Execute an SQL statement. The result set will be returned as a DatabaseStatement object.
     *
     * @param string $query Database query or statement to execute
     *
     * @return DatabaseStatement Database statement
     */
    public abstract function query($query);

    public function prepare($statement) {
        // Make sure $statement is valid
        if(!DatabaseStatement::isValid($statement, false))
            return false;

        // TODO: Unprepare current prepared statement?

        // TODO: Should the statement be prepared in the current database connector?

        // Prepare the statement
        $this->preparedStatement = $statement;
    }

    /**
     * Quote a database value
     *
     * @param $value
     *
     * @return string
     */
    public function quote($value) {
        // Quote the value
        // TODO: Increase quality of quotes. Make sure numbers aren't quoted
        return '`' . $value . '`';
    }





    // TODO: Methods to implement, based on PDO
    /*
     * public abstract function errorCode();
    public abstract function errorInfo();

    public abstract function getAttribute(int $attribute);
    public abstract function setAttribute(int $attribute, mixed $value);

    public abstract function getDriverName();
    public abstract static function getAvailableDrivers();
    */
}
