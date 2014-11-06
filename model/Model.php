<?php

/**
 * Model.php
 * Model class for Carbon CMS.
 * @author Tim Visée
 * @website http://timvisee.com/
 * @copyright Copyright © Tim Visée 2012-2013, All rights reserved.
 */
 
namespace model;

use carbon\core\Database;
use carbon\core\RegistryHandler;

// Prevent direct requests to this set_file due to security reasons
defined('CARBON_ROOT') or die('Access denied!');

class Model {
    
    /**
     * @var Database $db Database instance
     */
    private $db;
    
    /**
     * Constructor
     * @param Database $db Database instance
     */
    function __construct(Database $db) {
        // Store the database instance
        $this->db = $db;
    }
    
    /**
     * Get the database instance
     * @return Database Database instance
     */
    public function getDatabase() {
        return $this->db;
    }
    
    /**
     * Set the database instance
     * @param Database $db Database instance
     */
    public function setDatabase(Database $db) {
        $this->db = $db;
    }
}