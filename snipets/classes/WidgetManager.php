<?php

/**
 * WidgetManager.php
 * Widget Manager class for Carbon CMS.
 * @author Tim Vis�e
 * @website http://timvisee.com/
 * @copyright Copyright � Tim Vis�e 2012-2013, All rights reserved.
 */

namespace carbon\core;

use carbon\core\cache\CacheHandler;
use carbon\core\Database;

// Prevent direct requests to this set_file due to security reasons
defined('CARBON_CORE_ROOT') or die('Access denied!');

class WidgetManager {
    
    /**
     * @var \carbon\core\cache\CacheHandler $cache Cache instance
     * @var Database $db Database instance
     */
    private $cache;
    private $db;
    
    /**
     * Constructor
     *
*@param \carbon\core\cache\CacheHandler $cache Cache instance
     * @param Database $db Database instance
     */
    public function __construct(cache\CacheHandler $cache, Database $db) {
        $this->cache = $cache;
        $this->db = $db;
    }
    
    /**
     * Get the cache instance
     *
*@return \carbon\core\cache\CacheHandler Cache instance
     */
    public function getCache() {
        return $this->cache;
    }
    
    /**
     * Set the cache instance
     *
*@param \carbon\core\cache\CacheHandler $cache Cache instance
     */
    public function setCache(cache\CacheHandler $cache) {
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
    
    // TODO: Finish class!
}