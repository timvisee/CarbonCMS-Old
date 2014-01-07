<?php

/**
 * Cache.php
 *
 * The CacheHandler class handles all the cache.
 *
 * @author Tim Visee
 * @website http://timvisee.com/
 * @copyright Copyright (C) Tim Visee 2012-2013, All rights reserved.
 */

namespace core;

// Prevent users from accessing this file directly
defined('CARBON_ROOT') or die('Access denied!');

/**
 * Handles all the cache
 * @package core\language
 * @author Tim Visee
 */
class CacheHandler {
    /** @var bool Is caching enabled */
    private $cache_enabled = true;

    /** @var String $cache_dir Caching directory */
    private $cache_dir;

    /** @var string $CACHE_FILE_PREFIX Cache file prefix */
    private $CACHE_FILE_PREFIX = '';
    /** @var string $CACHE_FILE_SUFFIX Cache file suffix */
    private $CACHE_FILE_SUFFIX = '';
    /** @var string $CACHE_FILE_EXTENTION Cache file extention */
    private $CACHE_FILE_EXTENTION = '.cache';
    
    /**
     * Constructor
     * @param String $cache_dir Caching directory
     */
    public function __construct($cache_dir = null) {
        // Check if the $cache_dir var is set
        if($cache_dir == null) {
            // Unable to construct Cache class, cache directroy not set
            // TODO: Show proper error message
            die('Error while constructing Cache class!');
        }

        // Store the cache directory
        $this->cache_dir = rtrim($cache_dir, '/') . '/';

        // TODO: Take a look at those lines bellow
        // Generate the .htaccess file (for security)
        if(file_exists($this->cache_dir))
            $this->createCacheHtaccessFile($this->cache_dir);
    }

    /**
     * Check if caching is enabled
     * @return bool True if caching is enabled
     */
    public function isEnabled() {
        return $this->cache_enabled;
    }

    /**
     * Set if cache is enabled or not
     * @param \bool $cache_enabled True to enable
     */
    public function setEnabled($cache_enabled) {
        $this->cache_enabled = $cache_enabled;
    }

    /**
     * Get the file path of a cached file by name
     * @param String $name Cache name
     * @return String File path of cached file
     */
    private function getCacheFile($name) {
        $cache_dir = $this->getCacheDir();
        $cache_prefix = $this->CACHE_FILE_PREFIX;
        $cache_suffix = $this->CACHE_FILE_SUFFIX;
        $cache_ext = $this->CACHE_FILE_EXTENTION;
        return $cache_dir . $cache_prefix . $name . $cache_suffix . $cache_ext;
    }
    
    /**
     * Cache any type of data
     * @param String $name Cache name
     * @param mixed $data Data to cache
     */
    public function cache($name, $data) {
        // Convert the data to a string
        $data_str = serialize($data);
        
        // Get the file to cache the data to
        $cache_file = $this->getCacheFile($name);
        
        // Make sure the parent directory does exist, if it doesn't, create it
        if(!is_dir(dirname($cache_file)))
            mkdir(dirname($cache_file), 0777, true);
        
        // Write the cache string to a file
        $fh = fopen($cache_file, 'w') or die ('Carbon CMS: Error while writing cache!');
        fwrite($fh, $data_str);
        fclose($fh);
    }
    
    /**
     * Cache a string
     * @param String $name Cache name
     * @param String $string String to cache
     */
    public function cacheString($name, $string) {
        // Get the file to cache the data to
        $cache_file = $this->getCacheFile($name);
        
        // Make sure the parent directory does exist, if it doesn't, create it
        if(!is_dir(dirname($cache_file)))
            mkdir(dirname($cache_file), 0777, true);
        
        // Write the string to a file
        $fh = fopen($cache_file, 'w') or die ('Carbon CMS: Error while writing cache!');
        fwrite($fh, $string);
        fclose($fh);
    }
    
    /**
     * Get cached data
     * @param String $name Cache name
     * @return mixed Cached data
     */
    public function getCache($name) {
        // This file has to be available, if not return null
        if(!$this->isCached($name))
            return null;
        
        // Get the file to cache the data to
        $cache_file = $this->getCacheFile($name);
        
        // Get the cached file contents
        $data_str = file_get_contents($cache_file) or die('Carbon CMS: Error while reading cache!');
        
        // Decode the data string
        $data = unserialize($data_str);
        
        // Return the data
        return $data;
    }
    
    /**
     * Get a cached string
     * @param string $name Cache name
     * @return string Cached string
     */
    public function getCachedString($name) {
        // This file has to be available, if not return an empty string
        if(!$this->isCached($name))
            return '';
        
        // Get the file to cache the data to
        $cache_file = $this->getCacheFile($name);
        
        // Get the cached string
        $string = file_get_contents($cache_file) or die('Error while reading cache!');
        
        // Return the string
        return $string;
    }
    
    /**
     * Check if something is cached
     * @param string $name Cache name to check for
     * @return boolean true if cached
     */
    public function isCached($name) {
        // Check if something is cached
        return file_exists($this->getCacheFile($name));
    }
    
    /**
     * Remove cached data
     * @param string $name Cache name to remove
     */
    public function removeCache($name) {
        // Get the file to cache the data to
        $cache_file = $this->getCacheFile($name);
        
        // Make sure the file exists and remove the file
        if(file_exists($cache_file))
            unlink($cache_file) or die('Carbon CMS: Error while deleting cache!');
    }
    
    /**
     * Remove all cached data
     * @return integer amount of removed cache
     */
    public function removeAllCache() {
        // Get the cache folder
        $cache_dir = $this->getCacheDir();

        // Make sure the cache folder exists
        if(!file_exists($cache_dir))
            return 0;
        
        // Store the amount of removed cache files
        $removed_amount = $this->deleteDirContent($cache_dir, '.htaccess');

        // Recreate the .htaccess file in the cache dire
        $this->createCacheHtaccessFile($cache_dir);

        // Return amount of removed cache
        return $removed_amount;
    }
    
    /**
     * Remove the data of a directory
     * @param string $path Directory path
     * @param mixed $exceptions Exceptions to NOT remove
     * @return integer amount of removed files
     */
    // TODO: Fix this function, it throws errors with sub folders, files and stuff
    private function deleteDirContent($path, $exceptions = array()) {
        if(!file_exists($path))
            return 0;
        
        // Force the $exceptions variable to be an array
        if($exceptions == null)
            $exceptions = array();
        elseif(!is_array($exceptions))
            $exceptions = array($exceptions);
        
        $removed_amount = 0;
        
        // Create the file handler
        $handle = opendir($path);
        
        // Remove all data from the cache folder (except for ., .. and .htaccess)
        while (false !== ($entry = readdir($handle))) {
            // Don't delete exceptions
            if(!in_array($entry, $exceptions) && $entry != '.' && $entry != '..') {
                // Get the path of the file/directory
                $file = $path.$entry;
                
                if(!is_dir($file)) {
                    // Remove the file
                    unlink($file) or die('Carbon CMS: Error while deleting file!');
                    
                    // Count the removed file
                    $removed_amount++;
                } else {
                    // Remove the directory data and count the removed files
                    $removed_amount += $this->deleteDirContent(rtrim($file, DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR);
                    
                    // Remove the directory
                    rmdir($file) or die('Carbon CMS: Error while deleting folder!');
                }
            }
        }
        
        // Return amount of removed files
        return $removed_amount;
    }

    /**
     * Automaticly generate a .htaccess file in the cache folder, because of security reasons.
     * @param $cache_path String Path to the cache folder to generate the .htaccess file int
     */
    private function createCacheHtaccessFile($cache_path) {
        // Get the file path to the .htaccess file
        $file_path = rtrim($cache_path, '/') . '/.htaccess';

        // Generate the file contents
        $file_contents = "# This file has automaticly been generated by Carbon CMS." . PHP_EOL;
        $file_contents .= "#" . PHP_EOL;
        $file_contents .= "# This .htaccess file used to protect the cache folder against hackers and other security issues." . PHP_EOL;
        $file_contents .= "# Do not remove this file!" . PHP_EOL;
        $file_contents .= PHP_EOL;
        $file_contents .= "deny from all";

        // Create the file and write the file contents in it
        $fh = fopen($file_path, 'w') or die ('Carbon CMS: Error while creating .htaccess file in the cache directory!');
        fwrite($fh, $file_contents);
        fclose($fh);
    }

    /**
     * Get the age in seconds cache
     * @param string $name Cache name to get the age from
     * @return integer Age in seconds, negative 1 if an error occured
     */
    public function getCacheAge($name) {
        // This file has to be available, if not return negative 1
        if(!$this->isCached($name))
            return -1;
            
        // Get the cache file
        $cache_file = $this->getCacheFile($name);
        
        // Get the modification timestamp of the file
        $file_mod_time = filemtime($cache_file);
        
        // Return time difference in seconds
        return time() - $file_mod_time;
    }
    
    /**
     * Get the location of the cache directory
     * @return string Cache directory location
     */
    public function getCacheDir() {
        return $this->cache_dir;
    }
    
    /**
     * Set the location of the cache directory
     * @param string $cache_dir Cache directory location
     */
    public function setCacheDir($cache_dir) {
        $this->cache_dir = rtrim($cache_dir, '/') . '/';;
    }
    
    /**
     * Get the cache file prefix
     * @return string Cache file prefix
     */
    public function getCachePrefix() {
        return $this->CACHE_FILE_PREFIX;
    }
    
    /**
     * Set the cache file prefix
     * @param string $prefix Cache file prefix
     */
    public function setCachePrefix($prefix) {
        // Validate the new cahche file prefix
        if($prefix == null)
            return;
        
        // Set the cahche filre prefix
        $this->CACHE_FILE_PREFIX = $prefix;
    }
    
    /**
     * Get the cache file suffix
     * @return string Cache file suffix
     */
    public function getCacheSuffix() {
        return $this->CACHE_FILE_SUFFIX;
    }
    
    /**
     * Set the cache file suffix
     * @param string $suffix Cache file suffix
     */
    public function setCacheSuffix($suffix) {
        // Validate the new cache file suffix
        if($suffix == null)
            return;
        
        // Set the cache file suffix
        $this->CACHE_FILE_SUFFIX = $suffix;
    }
    
    /**
     * Get the cache file extention
     * @return string Cache file extention
     */
    public function getCacheExtention() {
        return $this->CACHE_FILE_EXTENTION;
    }
    
    /**
     * Set the cache file extention
     * @param string $extention Cache extention
     */
    public function setCacheExtention($extention) {
        // Validate the new cache file extention
        if($extention == null)
            return;
        
        // Set the cache file extention
        $this->CACHE_FILE_EXTENTION = $extention;
    }
}