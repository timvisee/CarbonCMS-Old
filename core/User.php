<?php

/**
 * User.php
 *
 * The User class.
 *
 * @author Tim Visee
 * @version 0.1
 * @website http://timvisee.com/
 * @copyright Copyright (C) Tim Visee 2012-2013, All rights reserved.
 */

namespace core;

use core\CacheHandler;
use core\Database;
use core\UserSession;

// Prevent users from accessing this file directly
defined('CARBON_ROOT') or die('Access denied!');

/**
 * User class
 * @package core
 * @author Tim Visee
 */
class User {
    
    /** @var CacheHandler $cache Cache instance */
    private $cache;
    /** @var Database $db Database instance */
    private $db;
    
    /**
     * @var int $user_id User ID
     */
    private $user_id;
    
    /**
     * Constructor
     * @param int $user_id User ID
     */
    public function __construct($user_id, CacheHandler $cache, Database $db) {
        // Store the user_id, cache and the database instance
        $this->user_id = $user_id;
        $this->cache = $cache;
        $this->db = $db;
    }
    
    /**
     * Get the user ID of the user
     * @return int User ID of the user
     */
    public function getId() {
        return $this->user_id;
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
    
    
    
    
    public function getUserLogin() {
        // TODO: Return users login
    }
    
    public function setUserLogin($user_login) {
        // TODO: Set users login
    }
    
    public function setUserPass($user_pass) {
        // TODO: Set user's pass
    }
    
    /**
     * Get the user's session instance
     * @return UserSession UserSession instance
     */
    public function getUserSession() {
        return new UserSession($this->cache, $this->db, $this);
    }
}

?>