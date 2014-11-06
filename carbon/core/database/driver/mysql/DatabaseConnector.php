<?php

namespace carbon\core\database\driver\mysql;

// Prevent direct requests to this file due to security reasons
defined('CARBON_CORE_INIT') or die('Access denied!');

class DatabaseConnector extends \carbon\core\database\lib\pdo\DatabaseConnector {

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
    public function __construct($host = self::DEF_HOST, $port = self::DEF_PORT, $user = self::DEF_USER, $pass = self::DEF_PASS, $dbname = self::DEF_DBNAME, $connect = true) {
        // Set all properties
        $this->setHost($host);
        $this->setPort($port);
        $this->setUser($user);
        $this->setPass($pass);
        $this->setDatabaseName($dbname);

        // Auto connect
        if($connect)
            $this->connect();
    }

    /**
     * Get the PDO DSN required to connect any database using PDO.
     *
     * @return string PDO DSN
     */
    public function getDsn() {
        $dsn = 'mysql:';

        // Append the host
        $dsn .= 'host=' . $this->getHost();

        // Append the port if specified
        if($this->getPort() !== null)
            $dsn .= ';port=' . $this->getPort();

        // Append the database name
        $dsn .= ';dbname=' . $this->getDatabaseName();

        // Return the DNS
        return $dsn;
    }
}