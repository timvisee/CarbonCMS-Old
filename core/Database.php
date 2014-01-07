<?php

/**
 * Database.php
 *
 * The Database class handles the database connection and database queries.
 *
 * @author Tim Visee
 * @website http://timvisee.com/
 * @copyright Copyright (C) Tim Visee 2012-2013, All rights reserved.
 */

namespace core;

use \PDO;

// Prevent users from accessing this file directly
defined('CARBON_ROOT') or die('Access denied!');

/**
 * Handles the database connection and database queries.
 * @package core
 * @author Tim Visee
 */
class Database extends PDO {
    
    /** @var String $table_prefix Database table prefix, 'carbon_' by default */
    private $table_prefix = 'carbon_';

    /**
     * Constructor
     * @param String $table_prefix Table prefix, null to use the default one
     */
    public function __construct($table_prefix = null) {
        // If the $table_prefix param is not null, set the table prefix
        if($table_prefix != null)
            $this->table_prefix = $table_prefix;
    }

    /**
     * Connect to the database
     * @param String $host Database host
     * @param int $port Database port (if null port 3306 will be used)
     * @param String $database Database name
     * @param String $username Database user username
     * @param String $password Database user password
     * @throws Exception
     */
    public function connectDatabase($host = null, $port = 3306, $database = null, $username = null, $password = null) {
        // Validate the host
        if($host == null)
            $host = 'localhost';

        // Validate the port number
        if($port == null || !is_int($port))
            $port = 3306;

        // Validate the database
        if($database == null || strlen(trim($database)) == 0) {
            // TODO: Show propper database error page
            throw new Exception('No valid database defined while constructing Database class!');
            die();
        }

        // TODO: Put some error handling here!
        // TODO: Support different database systems
        // Connect to the database
        $pdo_dsn = 'mysql:host=' . $host . ';port=' . $port . ';dbname=' . $database;
        parent::__construct($pdo_dsn, $username, $password);
    }

    /**
     * Select rows from a database table
     * @param $table
     * @param mixed $fields Collumns to get from the table as Array or String
     * @param string $where The WHERE part of the statement
     * @param int|\core\constant $fetchMode A PDO fetch mode
     * @internal param string $sql Sql query
     * @return mixed
     */
    public function select($table, $fields, $where = null, $fetchMode = PDO::FETCH_ASSOC) {
        // Add the table prefix to the table variable
        $table = $this->getTablePrefix() . $table;
        
        // The $fields parameter has to be an array
        if($fields == null || !is_array($fields)) {
            // If the variable is a string, put the string into an array
            if(is_string($fields))
                $fields = array($fields);
            else
                $fields = array();
        }
        
        // Sort the fields array by key in alphabetical order
        ksort($fields);
        
        // Build the fields string
        $fieldsString = '';
        foreach($fields as $key) {
            if($key != '*')
                $fieldsString .= '`'.$key.'`,';
            else {
                // If one of the fields is a star, force the fields string to be a star
                $fieldsString = $key;
                break;
            }
        }
        
        // Remove the comma from the end of the string
        $fieldsString = rtrim($fieldsString, ',');
        
        // Generate the query
        $query = "SELECT ".$fieldsString." FROM `".$table.'`';
        if($where != null && trim($where) != '')
            $query .= " WHERE ".$where;
        $sth = $this->prepare($query);
        
        // Execute the statement and return the fetched rows
        $sth->execute();
        return $sth->fetchAll($fetchMode);
    }
    
    /**
     * Insert a row into a database table
     * @param string $table Database table name
     * @param array $data An associative array
     * @return boolean false if failed
     */
    public function insert($table, $data) {
        // Add the table prefix to the table variable
        $table = $this->getTablePrefix() . $table;
        
        // The $data parameter has to be an array
        if($data == null || !is_array($data))
            $data = array();
        
        // Sort the data array by key in alphabetical order
        ksort($data);

        // TODO: Make sure the keys don't contain injection stuff!!! (check in every method!)

        // Generate the field names string to use in the statement
        // Make the fieldNames string start with a ` if there's any item inside the data array
        $fieldNames = '';
        if(sizeof($data) > 0)
            $fieldNames .= '`';
        
        // Implode the data array into the field names string
        $fieldNames .= implode('`,`', array_keys($data));
        
        // Make sure the string ends with a ` when there's any item inside the data array
        if(sizeof($data) > 0)
            $fieldNames .= '`';
        
        // Generate the field values string to use in the statement
        // Make the fieldValues string start with a colon if there's any item inside the data array
        $fieldValues = '';
        if(sizeof($data) > 0)
            $fieldValues .= ':value_';
            
        // Implode the data array into the field values string
        $fieldValues .= implode(',:value_', array_keys($data));
        
        // Prepare the query
        $sth = $this->prepare("INSERT INTO `".$table."` (".$fieldNames.") VALUES (".$fieldValues.")");
        
        // Bind the values
        foreach($data as $key => $value) {
            $sth->bindValue(':value_'.$key, $value);
        }
        
        // Execute the statement
        return $sth->execute();
    }
    
    /**
     * Update a row inside a database table
     * @param string $table Database table name
     * @param array $data An associative array
     * @param string $where The WHERE query part
     * @return boolean true if succeed
     */
    public function update($table, $data, $where = null) {
        // Add the table prefix to the table variable
        $table = $this->getTablePrefix() . $table;
        
        // The $data parameter has to be an array
        if($data == null || !is_array($data))
            $data = array();
        
        // Sort the data array by key
        ksort($data);
        
        // Build the values string
        $fieldDetails = '';
        foreach($data as $key => $value) {
            $fieldDetails .= '`'.$key.'`=:value_'.$key.',';
        }
        
        // Remove the comma from the end of the string
        $fieldDetails = rtrim($fieldDetails, ',');
        
        // Prepare the query
        $query = "UPDATE `".$table."` SET ".$fieldDetails;
        if($where != null && trim($where) != '')
            $query .= " WHERE ".$where;
            
        $sth = $this->prepare($query);
        
        // Bind the values
        foreach($data as $key => $value) {
            $sth->bindValue(':value_'.$key, $value);
        }
        
        // Execute the statement
        return $sth->execute();
    }
    
    /**
     * Delete rows from a database table
     * @param string $table Database table name
     * @param string $where The WHERE query part
     * @param int $limit Delete limit
     * @param array $bindParams Bind values
     * @return boolean false if failed
     */
    public function delete($table, $where, $limit = -1, $bindParams = array()) {
        // Add the table prefix to the table variable
        $table = $this->getTablePrefix() . $table;
        
        // Build the query
        $query = "DELETE FROM ".$table." WHERE ".$where;
        
        // Handle the limit
        if(is_int($limit) && $limit >= 0)
            $query .= " LIMIT ".$limit;
        
        // Prepare the statement
        $sth = $this->prepare($query);
        
        // The $bindParams variable has to be an array, if it's not an array replace it with an empty array
        if(!is_array($bindParams))
            $bindParams = array();
        
        // Bind the values
        foreach($bindParams as $key => $value) {
            $sth->bindValue(':'.$key, $value);
        }
        
        // Execute the statement
        return $sth->execute();
    }

    /**
     * Select rows from a database table
     * @param $table
     * @param String $where The WHERE part of the statement
     * @param int|\core\constant $fetchMode A PDO fetch mode
     * @internal param string $sql Sql query
     * @internal param mixed $fields Collumns to get from the table as Array or String
     * @return mixed
     */
    public function countRows($table, $where = null, $fetchMode = PDO::FETCH_ASSOC) {
        // Add the table prefix to the table variable
        $table = $this->getTablePrefix() . $table;
        
        // Generate the query
        $query = "SELECT COUNT(*) FROM `".$table.'`';
        if($where != null && trim($where) != '')
            $query .= " WHERE ".$where;
        $sth = $this->prepare($query);
        
        // Execute the statement and return the fetched rows
        $sth->execute();
        
        // Count the rows and return the count
        return $sth->fetchColumn();
    }
    
    /**
     * Get the table prefix
     * @return String Table prefix
     */
    public function getTablePrefix() {
        return $this->table_prefix;
    }
    
    /**
     * Set the table prefix
     * @param String $table_prefix Table prefix, null to clear the prefix
     */
    public function setTablePrefix($table_prefix = null) {
        // Check if the param equals to null, if so, clear the table prefix
        if($table_prefix != null)
            $this->table_prefix = $table_prefix;
        else
            $this->table_prefix = '';
    }
}