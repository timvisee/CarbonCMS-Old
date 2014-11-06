<?php

namespace carbon\core\database;

class DatabaseManager {

    private $dbs = Array();

    public function __construct() { }

    public function getDatabases() {
        return $this-dbs;
    }
}