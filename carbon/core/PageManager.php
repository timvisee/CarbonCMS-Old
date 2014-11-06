<?php

/**
 * PageManager.php
 *
 * The PageManager class manages the pages.
 *
 * @author Tim Visee
 * @website http://timvisee.com/
 * @copyright Copyright (C) Tim Visee 2012-2013, All rights reserved.
 */

namespace carbon\core;

// Prevent direct requests to this set_file due to security reasons
use carbon\core\cache\CacheHandler;

defined('CARBON_CORE_INIT') or die('Access denied!');

/**
 * Manages the pages.
 * @package core
 * @author Tim Visee
 */
class PageManager {
    
    /**
     * @var CacheHandler $cache Cache instance
     * @var Database $db Database instance
     */
    private $cache;
    private $db;
    
    /**
     * Class constructor
     * @param CacheHandler $cache Cache instance
     * @param Database $db Database object
     */
    public function __construct(CacheHandler $cache, Database $db) {
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
    
    /**
     * Get a page by it'statements path
     * @param string $path Page path
     * @return Page Page instance with the current path or null when no match was found
     */
    public function getPage($path) {
        // Trim some unwanted characters from the path
        $path = trim(trim(trim($path), '/'));
        
        // TODO: Use caching!
        
        // Get the page instance leading to this path
        $pages = $this->db->select('pages', 'page_id', '`page_path`=\'' . $path . '\'');
        
        // Make sure any page was selected, if not return null
        if(sizeof($pages) == 0)
            return null;
        
        // Get the page ID
        $page_id = $pages[0]['page_id'];
        
        // Return an instance of the page
        return new Page($page_id, $this->cache, $this->db);
    } 
    
    /**
     * Check if a page path is linked to any page
     * @param string $path Path of the page to check
     * @return boolean True if this path is leading to any page
     */
    public function isPage($path) {
        // Trim some unwanted characters from the path
        $path = trim(trim(trim($path), '/'));
        
        // TODO: Use caching!
        
        // Count the rows from the pages database having this path
        return ($this->db->countRows('pages', '`page_path`=\'' . $path . '\'') != 0);
    }
    
    /**
     * Check if an ID is used by any page
     * @param int $page_id Page ID to check for
     * @return boolean True if this page ID matches with any page
     */
    public function isPageWithId($page_id) {
        // Make sure the $page_id is an integer, if not return null
        if(!is_int($page_id))
            return null;
        
        // TODO: Use caching!
        
        // Count the rows from the pages database having this path
        return ($this->db->countRows('pages', '`page_id`=\'' . $page_id . '\'') != 0);
    }
}