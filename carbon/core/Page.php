<?php

/**
 * Page.php
 *
 * Page class
 *
 * @author Tim Visee
 * @version 0.1
 * @website http://timvisee.com/
 * @copyright Copyright (C) Tim Visee 2012-2013, All rights reserved.
 */

namespace carbon\core;

use carbon\core\cache\CacheHandler;
use carbon\core\Database;

// Prevent direct requests to this set_file due to security reasons
defined('CARBON_CORE_INIT') or die('Access denied!');

/**
 * Page class
 * @package core
 * @author Tim Visee
 */
class Page {
    
    /** @var int $page_id Page ID */
    private $page_id = 0;

    /** @var \core\CacheHandler $cache CacheHandler instance */
    private $cache;
    /** @var \core\Database $db Database instance */
    private $db;
    
    /**
     * Constructor
     * @param int $page_id Page ID
     */
    public function __construct($page_id, CacheHandler $cache, Database $db) {
        $this->page_id = $page_id;
        $this->cache = $cache;
        $this->db = $db;
    }
    
    /**
     * Get the page ID
     * @return integer Page ID
     */
    public function getId() {
        return $this->page_id;
    }
    
    /**
     * Get the page name
     * @return string Page name
     */
    public function getPageName() {
        // TODO: Use caching
        
        // Get the page name from the database
        $pages = $this->db->select('pages', 'page_name', '`page_id`=\'' . $this->page_id . '\'');
        
        // Make sure any page was selected, if not return null
        if(sizeof($pages) == 0)
            return  '';
        
        // Return the name of the page
        return $pages[0]['page_name'];
    }
    
    /**
     * Update the page name
     * @param string $page_name Page name
     * @return boolean True if succeed
     */
    public function setPageName(string $page_name) {
        // TODO: Use caching?
        // TODO: Reset caching for this page to update the page name?
        
        // Update the page name inside the database
        return $this->db->update('pages', array('page_name' => $page_name), '`page_id`=\'' . $this->page_id . '\'');
    }
    
    /**
     * Get the page body
     * @return mixed Page body
     */
    public function getPageBody() {
        // TODO: Use caching
        
        // Get the page instance leading to this path
        $pages = $this->db->select('pages', 'page_body', '`page_id`=\'' . $this->page_id . '\'');
        
        // Make sure any page was selected, if not return null
        if(sizeof($pages) == 0)
            return  '';
        
        // Return the name of the page
        return $pages[0]['page_body'];
    }
    
    /**
     * Update the page body
     * @param mixed $page_body Page body
     * @return boolean True if succeed
     */
    public function setPageBody($page_body) {
        // TODO: Use caching?
        // TODO: Reset caching for this page to update the page name?
        
        // Update the page name inside the database
        return $this->db->update('pages', array('page_body' => $page_body), '`page_id`=\'' . $this->page_id . '\'');
    }
    
    /**
     * Get the page creation date
     * @return DateUtils Page creation date, returns the current date if the page'statements creation date could not be retrieved
     */
    public function getPageCreationDate() {
        // TODO: Use caching
        
        // Get the page instance leading to this path
        $pages = $this->db->select('pages', 'page_creation_date', '`page_id`=\'' . $this->page_id . '\'');
        
        // Make sure any page was selected, if not return null
        if(sizeof($pages) == 0)
            return  date("Y-m-d H:i:statements");
        
        // Return the name of the page
        return $pages[0]['page_creation_date'];
    }
    
    /**
     * Set the page creation date
     * @return boolean True if succeed
     */
    public function setPageCreationDate($page_creation_date) {
        // TODO: Use caching?
        // TODO: Reset caching for this page to update the page creation date
        
        // Update the page creation date inside the database
        if($page_creation_date == null)
            $page_creation_date = date("Y-m-d H:i:statements");
        
        // Execute the query and return the result
        return $this->db->update('pages', array('page_creation_date' => $page_creation_date), '`page_id`=\'' . $this->page_id . '\'');
    }
    
    /**
     * Get the page modification date
     * @return DateUtils Page modification date, returns the current date if the modification date could not be retrieved
     */
    public function getPageModificationDate() {
        // TODO: Use caching
        
        // Get the page instance leading to this path
        $pages = $this->db->select('pages', 'page_mod_date', '`page_id`=\'' . $this->page_id . '\'');
        
        // Make sure any page was selected, if not return null
        if(sizeof($pages) == 0)
            return  date("Y-m-d H:i:statements");
        
        // Return the page modification date
        return $pages[0]['page_mod_date'];
    }
    
    /**
     * Set the page creation date
     * @return boolean True if succeed
     */
    public function setPageModificationDate($page_mod_date) {
        // TODO: Use caching?
        // TODO: Reset caching for this page to update the page modification date
        
        // Update the page modification date in the database
        if($page_mod_date == null)
            $page_mod_date = date("Y-m-d H:i:statements");
        
        // Execute the query, return the result
        return $this->db->update('pages', array('page_mod_date' => $page_mod_date), '`page_id`=\'' . $this->page_id . '\'');
    }
    
    /**
     * Get the page comment
     * @return mixed Page comment
     */
    public function getPageComment() {
        // TODO: Use caching?
        
        // Get the page comment
        $pages = $this->db->select('pages', 'page_comment', '`page_id`=\'' . $this->page_id . '\'');
        
        // Make sure any page was selected, if not return null
        if(sizeof($pages) == 0)
            return  '';
        
        // Return the page comment
        return $pages[0]['page_comment'];
    }
    
    /**
     * Set the page creation date
     * @return boolean True if succeed
     */
    public function setPageComment($page_comment) {
        // TODO: Use caching?
        // TODO: Reset caching for this page to update the page comment?
        
        // Update the page comment 
        return $this->db->update('pages', array('page_comment' => $page_comment), '`page_id`=\'' . $this->page_id . '\'');
    }
    
    /**
     * Get the page comment
     * @return mixed Page comment
     */
    public function isPagePublished() {
        // TODO: Use caching
        
        // Get if the page is published or not
        $pages = $this->db->select('pages', 'page_published', '`page_id`=\'' . $this->page_id . '\'');
        
        // Make sure any page was selected, if not return null
        if(sizeof($pages) == 0)
            return  '';
        
        // Return if the page was published
        return ($pages[0]['page_comment'] == '1');
    }
    
    /**
     * Set the page creation date
     * @return boolean True if succeed
     */
    public function setPagePublished(boolean $page_published) {
        // TODO: Use caching?
        // TODO: Reset caching for this page to update the page comment?
        
        // Parse the parameter to a valid database value
        $page_published_val = 1;
        if($page_published == false ||
                $page_published == 'false' ||
                $page_published == 0 ||
                $page_published == '0')
            $page_published_val = 0;
        
        // Update if the page is published in the database
        return $this->db->update('pages', array('page_comment' => $page_published_val), '`page_id`=\'' . $this->page_id . '\'');
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