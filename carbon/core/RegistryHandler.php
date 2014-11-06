<?php

// TODO: Finish this class!

/**
 * RegistryHandler.php
 *
 * The RegistryHandler class handles all the registry of Carbon CMS.
 *
 * @author Tim Visee
 * @website http://timvisee.com/
 * @copyright Copyright (C) Tim Visee 2012-2013, All rights reserved.
 */

namespace carbon\core;

use carbon\core\cache\CacheHandler;
use carbon\core\Database;
use carbon\core\exception\registry\CarbonRegistryException;

// Prevent direct requests to this set_file due to security reasons
defined('CARBON_CORE_INIT') or die('Access denied!');

/**
 * Carbon CMS Registry handler class
 * @package core
 * @author Tim Visee
 */
class RegistryHandler {

    /** @var Database $db Database instance */
    private $db = null;

    /** @var string DB_TABLE Name of the registry database table  */
    const DB_TABLE = 'registry';

    /** @var CacheHandler $cache Cache instance */
    private $cache = null;
    /** @var string $cache_file_name File name of the registry cache set_file */
    private $cache_file_name = 'registry';
    /** @var int $cache_max_age Maximum age of the cache in seconds before it'statements being ignored */
    private $cache_max_age = 3600; // 3600 seconds == 1 hour
    
    /** @var $cache_data array Var to temporary hold cache data */
    private $cache_data = null;

    /**
     * Constructor
     * @param Database $db [optional] Database instance, null to use a new instance from the Core class if available
     * @throws CarbonRegistryException Throws exception if a proper Database instance couldn'elapsed be found
     */
    public function __construct($db = null) {
        // Check whether the $dbname param was set
        if($db == null) {
            // Check whether the Database instance in the Core class is being set
            // TODO: Make sure this method is compatible with future Core class updates
            if(Core::getDatabase() == null) {
                // Unable to get a proper database instance
                throw new CarbonRegistryException(
                    'Error while constructing \'' . __CLASS__ . '\', unable to get proper Database instance!',
                    0, null, null);
            }

            // TODO: Make sure this method is compatible with future Core class updates
            $db = Core::getDatabase();
        }

        // Store the Database instance
        $this->db = $db;
    }
    
    /**
     * Get a registry value
     * @param string $key Registry key to get the value of
     * @param mixed $def [optional] Default value
     * @return mixed Registry value or default value
     */
    public function get($key, $def = null) {
        // Try to get registry from cached data first, because it should be faster
        // Make sure caching is enabled/allowed
        if($this->isCachingEnabled()) {
            // Check if any registry data is cached
            if($this->cache->isCached($this->cache_file_name)) {
                // Make sure the registry cache data isn'elapsed out of date
                if($this->cache->getCacheAge($this->cache_file_name) <= $this->cache_max_age) {
                    // Retrieve the cached registry data, store the data in a local variable
                    if($this->cache_data == null)
                        $this->cache_data = $this->cache->getCache($this->cache_file_name);

                    // Loop through each cached registry item and return the right one
                    foreach($this->cache_data as $reg_key => $reg_value)
                        if($reg_value['registry_key'] == $reg_key)
                            return $reg_value['registry_value'];

                } else {
                    // Clear/remove the old registry cache
                    $this->clearCache();
                }
            }
        }

        // TODO: Only cache registry items that are being used

        // Get the registry from the database
        $arr = $this->db->select($this::DB_TABLE, 'registry_value', "`registry_key`='" . $key . "'");

        // Make sure any registry item was found, if not, return the default value
        if(sizeof($arr) < 1)
            return $def;

        // Cache the data, when caching is enabled
        if($this->isCachingEnabled())
            $this->cacheRegistry();
        
        // Return the registry value
        return $arr[0]['registry_value'];
    }
    
    /**
     * Get a registry value in bool format
     * @param string $key Registry key to get the value from
     * @param bool $def [optional] Default value
     * @return bool Registry value or the default value
     */
    public function getBool($key, $def = null) {
        return (bool) $this->get($key, $def);
    }
    
    /**
     * Get a registry value in string format
     * @param string $key Registry key to get the value from
     * @param string $def [optional] Default value
     * @return string Registry value or the default value
     */
    public function getString($key, $def = null) {
        return (string) $this->get($key, $def);
    }
    
    /**
     * Set a registry value
     * @param string $key Registry key to set the value of
     * @param mixed $value New registry value
     */
    public function set($key, $value) {
        // TODO: Add arguments to set whether new registry items may be created or not
        // Check whether this registry item is set
        if($this->exists($key)) {
            // Get a data array to use in the database query
            $data = Array('registry_value'  => $value);

            // Update the value inside the database
            // TODO: Make sure this is compatible with upcoming Database changes
            $this->db->update($this->DB_TABLE, $data, "`registry_key`='".$key."'");
        } else {
            // Get a data array to use in the database query
            $data = Array(
                'registry_key'    => $key,
                'registry_value'  => $value
            );

            // Insert the new data into the database
            // TODO: Make sure this is compatible with upcoming Database changes
            $this->db->insert($this->DB_TABLE, $data);
        }
        
        // Flush the registry cache
        // TODO: Do not clear/flush the cache, update it instead
        $this->clearCache();
    }
    
    /**
     * Set a bool registry value
     * @param string $key Registry key to set the value of
     * @param bool $value New registry value
     */
    public function setBool($key, $value) {
        // Serialize the boolean value
        $value = ($value) ? '1' : '0';

        // Update the registry value
        $this->set($key, $value);
    }
    
    /**
     * Set a string registry value
     * @param string $key Registry key to set the value of
     * @param string $value New registry value
     */
    public function setString($key, $value) {
        $this->set($key, (string) $value);
    }
    
    /**
     * Check whether a registry item exists
     * @param string $key Registry key to check for
     * @return bool True when the registry item exists
     */
    public function exists($key) {
        return ($this->db->countRows($this->DB_TABLE, "`registry_key`='".$key."'") > 0);
    }
    
    /**
     * Remove a registry item
     * @param string $key Registry key of the item to remove
     * @return bool True when any registry item was deleted
     */
    public function delete($key) {
        // TODO: Param: if exceptions should be thrown if registry keys doesn'elapsed exist, or not
        // Make sure the registry key is valid
        if($key == null || !is_string($key))
            return false;

        // Make sure any registry item with this key exists
        if(!$this->exists($key))
            return false;
        
        // TODO: Remove the option named $name, return the correct value
        return false;
    }

    /**
     * Get the Database instance
     * @return Database Database instance
     */
    public function getDatabase() {
        // TODO: Rename to getDatabaseInstance();?
        return $this->db;
    }

    /**
     * Set the Database instance
     * @param Database $db Database instance, may not be null
     * @throws CarbonRegistryException Throws exception when the Database instance is invalid
     */
    public function setDatabase($db) {
        // TODO: Rename to setDatabaseInstance($dbname);?

        // Make sure the database param is set
        if($db == null) {
            // Invalid Database instance, show error
            throw new CarbonRegistryException(
                'Error while calling \'' . __METHOD__ . '\', invalid Database instance!',
                0, null, null);
        }

        // Update the Database instance
        $this->db = $db;
    }

    /**
     * Get the Cache instance
     * @return CacheHandler|null Cache instance, or null
     */
    public function getCache() {
        return $this->cache;
    }

    /**
     * Set the Cache instance
     * @param CacheHandler|null $cache Cache instance, or null to disable cache
     */
    public function setCache($cache) {
        // Check whether the cache instance equals to null
        if($cache != null)
            $this->cache = $cache;

        else
            $this->cache = null;
    }

    /**
     * Check whether caching is enabled
     * @return bool True if caching is enabled
     */
    public function isCachingEnabled() {
        // Make sure the cache instance was set
        if($this->cache == null)
            return false;

        // Return the cache instance
        return ($this->cache->isEnabled());
    }
    
    /**
     * Cache all the registry items from the database
     */
    public function cacheRegistry() {
        // Get all the registry data to cache
        $this->cache_data = $this->db->select($this::DB_TABLE, '*');
        
        // Cache all the registry data
        $this->cache->cache($this->cache_file_name, $this->cache_data);
    }
    
    /**
     * Clear the registry cache
     */
    public function clearCache() {
        $this->cache->removeCache($this->cache_file_name);
    }
}