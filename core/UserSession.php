<?php

/**
 * UserSession.php
 * User Session class file of Carbon CMS.
 * @author Tim Vis�e
 * @version 0.1
 * @website http://timvisee.com/
 * @copyright Copyright � Tim Vis�e 2012-2013, All rights reserved.
 */

namespace core;

use core\Config;
use core\CacheHandler;
use core\Database;
use core\Hash;
use core\User;

// Prevent users from accessing this file directly
defined('CARBON_ROOT') or die('Access denied!');

/**
 * User session class, stores the session of a user
 * @package core
 * @author Tim Visee
 */
class UserSession {
    
    /**
     * @var CacheHandler $cache Cache instance
     * @var Config $config Config instance
     * @var Database $db Database instance
     */
    private $cache;
    private $config;
    private $db;
    
    /**
     * @var User $user User instance
     */
    private $user;
    
    /**
     * Constructor
     * @param CacheHandler $cache Cache instance
     * @param Database $db Database instance
     * @param User $user User instance refering to it's session ID
     */
    public function __construct(CacheHandler $cache, Config $config, Database $db, User $user) {
        // Store the cache, database and user instance.
        $this->cache = $cache;
        $this->config = $config;
        $this->db = $db;
        $this->user = $user;
    }
    
    /**
     * Get the cache instance
     * @retur Cache Cache instance
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
     * Get the config instance
     * @return Config Config instance
     */
    public function getConfig() {
        return $this->config;
    }
    
    /**
     * Set the config instance
     * @param Config $config Config instance
     */
    public function setConfig(Config $config) {
        $this->config = $config;
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
    
    /**
     * Get the user instance
     * @return User User instance
     */
    public function getUser() {
        return $this->user;
    }
    
    /**
     * Return the session ID
     * @return string Session ID
     */
    public function getSessionId() {
        
        // TODO: Use caching
        
        // Get the session ID from the database
        $pages = $this->db->select('users', 'session_id', '`user_id`=\'' . $this->user->getId() . '\'');
        
        // Make sure any session ID was selected, if not return a default value
        if(sizeof($pages) == 0)
            return 0;
        
        // Return the session ID
        return $pages[0]['session_id'];
    }
    
    /**
     * Set the session ID of the user
     * @param string $session_id New session ID
     */
    private function setSessionId($session_id) {
        // TODO: Update cache
        
        // TODO: Check session ID length
        
        // Update the session ID of the user in the database
        $this->db->update('users', array('session_id' => $session_id), "`user_id`='" . $this->user->getId() . "'");
    }
    
    /**
     * Reset the session ID of the user
     */
    private function resetSessionId() {
        $this->setSessionId($this->getRandomSessionId());
    }
    
    /**
     * Generate a random session ID to use for a user
     * @return string Random session ID string
     */
    private static function getRandomSessionId() {
        // Get a random hash
        $hash = Hash::hash(mt_rand(0, 999999999), $this->config);

        // If the hash is long enough, return the hash with an exact length of 32 chars
        if(strlen($hash) >= 32)
            return substr($hash, 0, 32);
        
        // The hash was not long enough, generate an alternative hash
        return substr(md5(mt_rand(0, 999999999)), 0, 32);
    }
}