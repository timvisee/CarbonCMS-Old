<?php

// TODO: Class not finished and/or tested yet!

namespace carbon\core\database;

// Prevent direct requests to this file due to security reasons
use carbon\core\database\driver\mysql\DatabaseStatement;

defined('CARBON_CORE_INIT') or die('Access denied!');

class DatabaseConnectionManager {

    // TODO: Decide whether this manager manages DatabaseConnector or Database instances!
    // TODO: Automatically add connection instances to the manager when connections are made!

    /** @var array Array holding all active database connection instances. */
    private static $cons = Array();

    /**
     * Initialize
     */
    public static function init() {
        // TODO: Initialize...
    }

    /**
     * Add a new connection to the connection manager
     *
     * @param $con DatabaseConnector instance to add
     *
     * @return bool True if the connection was successfully added, false otherwise.
     * False will be returned if the connection instance was invalid, or if the connection was managed already.
     */
    public static function add($con) {
        // Make sure the connection instance isn't null and that the connection is an instance of DatabaseConnector
        if($con === null)
            return false;
        if(!($con instanceof DatabaseStatement))
            return false;

        // Make sure this connection instance isn't added already
        if(self::isManaged($con))
            return false;

        // Add the connection instance,= to the list and return true
        array_push(self::$cons, $con);
        return true;
    }

    /**
     * Remove a connection from the connection manager
     *
     * @param DatabaseConnector $con Connection instance to remove
     *
     * @return int Count of removed connections. -1 will be returned if the connection instance was invalid.
     */
    public static function remove($con) {
        // Make sure the connection instance isn't null and that the connection is an instance of DatabaseConnector
        if($con === null)
            return -1;
        if(!($con instanceof DatabaseStatement))
            return -1;

        // Keep track of the count of removed instances
        $count = 0;

        // Remove the param instances from the connections list
        foreach(self::$cons as $key => &$val) {
            if($con == $val) {
                unset(self::$cons[$key]);
                $count++;
            }
        }
        return $count;
    }

    /**
     * Check whether a connection is being managed
     *
     * @param $con
     *
     * @return bool
     */
    public static function isManaged($con) {
        return in_array($con, self::$cons, true);
    }

    /**
     * Get a list of active database connections.
     *
     * @return array List of active database connection
     */
    public static function getConnections() {
        // TODO: Return a list of active database connections?
        return self::$cons;
    }

    /**
     * Get the count of active database connections.
     *
     * @return int Count of active database connections
     */
    public static function getConnectionCount() {
        return sizeof(self::$cons);
    }
}
