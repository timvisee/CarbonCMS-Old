<?php

/**
 * PluginSettings.php
 * Plugin Settings class file of Carbon CMS.
 * @author Tim Vis�e
 * @website http://timvisee.com/
 * @copyright Copyright � Tim Vis�e 2012-2013, All rights reserved.
 */

namespace core\plugin;

use core\util\StringUtils;

// Prevent users from accessing this file directly
defined('CARBON_ROOT') or die('Access denied!');

class PluginSettings {
    
    private $settings_file;
    private $settings_array = null;
    
    /**
     * Constructor
     * @param string $settings_file Plugin settings file
     */
    public function __construct($settings_file) {
        $this->settings_file = $settings_file;
        
        // Load the settings from the file
        $this->reloadFromFile();
    }
    
    /**
     * Get the plugin settings file
     * @return string Plugin settings file
     */
    public function getFile() {
        return $this->settings_file;
    }
    
    /**
     * Get the plugin settings array
     * @return array Plugin settings array
     */
    public function getArray() {
        // Make sure any settings is loaded
        if($this->settings_array == null || !is_array($this->settings_array))
            $this->reloadFromFile();
        
        // Return the settings array
        return $this->settings_array;
    }
    
    /**
     * Reload the settings file
     */
    public function reloadFromFile() {
        // Check if the settings file exists
        if(!file_exists($this->getFile())) {
            $this->settings_array = null;
            return;
        }
        
        $this->settings_array = parse_ini_file($this->getFile(), true);
    }
    
    /**
     * Get a key's value from the plugin settings
     * @param string $section Key sectin
     * @param string $key Key to return the value from
     * @param mixed $def Default value returned if the key was not found (optional)
     * @return mixed Key value or default value if the key was not found
     */
    public function get($section, $key, $def = null) {
        // Check if the key exists, if not return the default value
        if(!$this->containsKey($section, $key))
            return $def;
        
        // Return the key value
        $arr = $this->getArray();
        return $arr[$section][$key];
    }
    
    /**
     * Get the plugin's main file from the plugin settings
     * @param string $def Default plugin's main file path (optional)
     * @return string Plugin's main file
     */
    public function getPluginMainFile($def = '') {
        // Get the 'main' value from the plugin config
        $main = $this->get('plugin', 'main', $def);

        // Make sure the file has an extention
        if(!StringUtils::contains($main, '.'))
            $main .= '.php';

        // Return the 'main' value
        return $main;
    }
    
    /**
     * Get the plugin's main class from the plugin settings
     * @param string $def Default main class (optional)
     * @return string Plugin's main class
     */
    public function getPluginMainClass($def  = '') {
        return $this->get('plugin', 'main_class', $def);
    }
    
    /**
     * Get the plugin's unique name from the plugin's settings file
     * @param string $def Default unique name (optional)
     * @return string Plugin's unique name
     */
    public function getPluginName($def = '') {
        // Remove the spaces from the plugin's name and return it
        return trim(str_replace(' ', '', $this->get('plugin', 'name', $def)));
    }
    
    /**
     * Get the plugin's display name from the plugin's settings file
     * @param string $def Default display name (if null, the name will automaticly be defined) (optional)
     * @return string Plugin's display name
     */
    public function getPluginDisplayName($def = null) {
        if($def == null)
            $def = ucfirst(strtolower($this->getPluginName()));
            
        return $this->get('plugin', 'display_name', $def);
    }
    
    /**
     * Get the plugin's description from the plugin's settings file
     * @param string $def Default description (optional)
     * @return string Plugin's description
     */
    public function getPluginDescription($def = '') {
        return $this->get('plugin', 'description', $def);
    }
    
    /**
     * Get the plugin's version from the plugin's settings file
     * @param string $def Default version (optional)
     * @return string Plugin's version
     */
    public function getPluginVersion($def = '1.0') {
        return $this->get('plugin', 'version', $def);
    }
    
    /**
     * Get the plugin's website from the plugin's settings file
     * @param string $def Default website
     * @return string Plugin's website
     */
    public function getPluginWebsite($def = '') {
        return $this->get('plugin', 'website', $def);   
    }
    
    /**
     * Get the plugins this plugin depends on from the plugin's settings file
     * @param array $def Default depend plugins
     * @return array Plugin's depend plugins
     */
    public function getPluginDepends($def = array()) {
        // Convert the default array to a string
        $def_string = implode(',', $def);
        
        // Get the list from the plugin's settings file
        $depends_string = $this->get('plugin', 'depends', $def_string);
        
        // If the $depends_string is empty, return an empty array
        if(trim($depends_string) == '')
            return array();
        
        // Build an array with all the dependend plugins
        $depends = array();
        foreach(explode(',', $depends_string) as $plugin_name) {
            // Make sure the plugins name doesn't equal to the current plugins name
            if($this->getPluginName() == trim($plugin_name))
                continue;
                
            // Trim the plugin names for unwanted spaces and stuff
            array_push($depends, trim($plugin_name));
        }
        
        // Return the list
        return $depends;
    }
    
    /**
     * Get the plugins this plugin depends on from the plugin's settings file
     * @param array $def Default depend plugins
     * @return array Plugin's depend plugins
     */
    public function getPluginSoftDepends($def = array()) {
        // Convert the default array to a string
        $def_string = implode(',', $def);
        
        // Get the list from the plugin's settings file
        $depends_string = $this->get('plugin', 'softdepends', $def_string);
        
        // If the $depends_string is empty, return an empty array
        if(trim($depends_string) == '')
            return array();
        
        // Build an array with all the dependend plugins
        $depends = array();
        foreach(explode(',', $depends_string) as $plugin_name) {
            // Make sure the plugins name doesn't equal to the current plugins name
            if($this->getPluginName() == trim($plugin_name))
                continue;
            
            // Trim the plugin names for unwanted spaces and stuff
            array_push($depends, trim($plugin_name));
        }
        
        // Return the list
        return $depends;
    }
    
    /**
     * Get the names of the authors of the plugin's settings file
     * @param array $def Default author names
     * @return array Author names
     */
    public function getAuthorNames($def = array()) {
        // Convert the default array to a string
        $def_string = implode(',', $def);
        
        // Get the list from the plugin's settings file
        $authors_string = $this->get('author', 'authors', $def_string);
        
        // Build an array with all the author names
        $authors = array();
        foreach(explode(',', $authors_string) as $author_name) {
            // Trim the plugin names for unwanted spaces and stuff
            array_push($authors, trim($author_name));
        }
        
        // Return the list
        return $authors;
    }
    
    /**
     * Get the author website from the plugin's settings file
     * @param string $def Default author website
     * @return Author website
     */
    public function getAuthorWebsite($def = '') {
        return $this->get('author', 'website', $def);
    }
    
    /**
     * Check if the plugin settings contains a section
     * @param string $section Section to check
     * @return boolean True if section exists
     */
    public function containsSection($section) {
        // Check if the section exists
        return array_key_exists($section, $this->getArray());
    }
    
    /**
     * Check if the plugin settings contains a key
     * @param string $section Key section
     * @param string $key Key to check
     * @return boolean True if key exists
     */
    public function containsKey($section, $key) {
        // Check if the section exists
        if(!$this->containsSection($section))
            return false;
        
        $arr = $this->getArray();
        
        // Check if the key exists
        return array_key_exists($key, $arr[$section]);
    }
    
    /**
     * Check if the plugin settings contains all the required sections and keys
     * @return boolean True if valid
     */
    public function isValid() {
        // Check if the plugin's settings file is a valid array
        if($this->getArray() == null || !is_array($this->getArray()))
            return false;
        
        // Check if the plugin section exists
        if(!$this->containsSection('plugin'))
            return false;
        
        // Check if the required keys exist
        if(!$this->containsKey('plugin', 'main') ||
                !$this->containsKey('plugin', 'main_class') ||
                !$this->containsKey('plugin', 'version'))
            return false;
        return true;
    }
}