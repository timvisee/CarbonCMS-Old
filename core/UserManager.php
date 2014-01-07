<?php

// TODO: File description

/**
 * UserManager.php
 *
 * The UserManager class manages the users registered on the site.
 *
 * @author Tim Visee
 * @version 0.1
 * @website http://timvisee.com/
 * @copyright Copyright (C) Tim Visee 2012-2013, All rights reserved.
 */

namespace core;

use core\CacheHandler;
use core\Database;

// Prevent users from accessing this file directly
defined('CARBON_ROOT') or die('Access denied!');

/**
 * Manages the users registered on the site.
 * @package core
 * @author Tim Visee
 */
class UserManager {
    
    /**
     * @var CacheHandler $cache Cache instance
     * @var Database $db Database instance
     */
    private $cache;
    private $db;
    
    /**
     * Constructor
     * @param CacheHandler $cache Cache instance
     * @param Database $db Database instance
     */
    public function __construct(CacheHandler $cache, Database $db) {
        // Store the cache and database instance
        $this->cache = $cache;
        $this->db = $db;
    }
    
    /**
     * Get the cache instance
     * @return CacheHandler Cache instance
     */
    public function getCache() {
        return $this->cache;
    }
    
    /**
     * Set the cache instance
     * @param CacheHandler $cache Cache instance
     */
    public function setCache(CacheHandler $cache) {
        $this->cache = $cache;
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

?>