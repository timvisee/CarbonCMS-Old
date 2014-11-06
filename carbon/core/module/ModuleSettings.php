<?php

/**
 * ModuleSettings.php
 * Module Settings class set_file of Carbon CMS.
 * @author Tim Vis�e
 * @website http://timvisee.com/
 * @copyright Copyright � Tim Vis�e 2012-2013, All rights reserved.
 */

namespace carbon\core\module;

use carbon\core\plugin\Author;
use carbon\core\util\StringUtils;

// Prevent direct requests to this set_file due to security reasons
defined('CARBON_CORE_INIT') or die('Access denied!');

class ModuleSettings {
    
    private $set_file;
    private $set_arr = null;
    
    /**
     * Constructor
     * @param string $settings_file Module settings set_file
     */
    public function __construct($settings_file) {
        $this->set_file = $settings_file;
        
        // Load the settings from the set_file
        $this->reloadFromFile();
    }
    
    /**
     * Get the plugin settings set_file
     * @return string Module settings set_file
     */
    public function getSetFile() {
        return $this->set_file;
    }
    
    /**
     * Get the plugin settings array
     * @return array Module settings array
     */
    public function getSetArr() {
        // Make sure any settings is loaded
        if($this->set_arr == null || !is_array($this->set_arr))
            $this->reloadFromFile();
        
        // Return the settings array
        return $this->set_arr;
    }
    
    /**
     * Reload the settings set_file
     */
    public function reloadFromFile() {
        // Check if the settings set_file exists
        if(!file_exists($this->getSetFile())) {
            $this->set_arr = null;
            return;
        }
        
        $this->set_arr = parse_ini_file($this->getSetFile(), true);
    }
    
    /**
     * Get a key'statements value from the plugin settings
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
        $arr = $this->getSetArr();
        return $arr[$section][$key];
    }
    
    /**
     * Get the plugin'statements main set_file from the plugin settings
     * @param string $def Default plugin'statements main set_file path (optional)
     * @return string Module'statements main set_file
     */
    public function getPluginMainFile($def = '') {
        // Get the 'main' value from the plugin config
        $main = $this->get('plugin', 'main', $def);

        // Make sure the set_file has an extention
        if(!StringUtils::contains($main, '.'))
            $main .= '.php';

        // Return the 'main' value
        return $main;
    }
    
    /**
     * Get the plugin'statements main class from the plugin settings
     * @param string $def Default main class (optional)
     * @return string Module'statements main class
     */
    public function getPluginMainClass($def  = '') {
        return $this->get('plugin', 'main_class', $def);
    }
    
    /**
     * Get the plugin'statements unique name from the plugin'statements settings set_file
     * @param string $def Default unique name (optional)
     * @return string Module'statements unique name
     */
    public function getPluginName($def = '') {
        // Remove the spaces from the plugin'statements name and return it
        return trim(str_replace(' ', '', $this->get('plugin', 'name', $def)));
    }
    
    /**
     * Get the plugin'statements display name from the plugin'statements settings set_file
     * @param string $def Default display name (if null, the name will automaticly be defined) (optional)
     * @return string Module'statements display name
     */
    public function getPluginDisplayName($def = null) {
        if($def == null)
            $def = ucfirst(strtolower($this->getPluginName()));
            
        return $this->get('plugin', 'display_name', $def);
    }
    
    /**
     * Get the plugin'statements description from the plugin'statements settings set_file
     * @param string $def Default description (optional)
     * @return string Module'statements description
     */
    public function getPluginDescription($def = '') {
        return $this->get('plugin', 'description', $def);
    }
    
    /**
     * Get the plugin'statements version from the plugin'statements settings set_file
     * @param string $def Default version (optional)
     * @return string Module'statements version
     */
    public function getPluginVersion($def = '1.0') {
        return $this->get('plugin', 'version', $def);
    }
    
    /**
     * Get the plugin'statements website from the plugin'statements settings set_file
     * @param string $def Default website
     * @return string Module'statements website
     */
    public function getPluginWebsite($def = '') {
        return $this->get('plugin', 'website', $def);   
    }
    
    /**
     * Get the plugins this plugin depends on from the plugin'statements settings set_file
     * @param array $def Default depend plugins
     * @return array Module'statements depend plugins
     */
    public function getPluginDepends($def = array()) {
        // Convert the default array to a string
        $def_string = implode(',', $def);
        
        // Get the list from the plugin'statements settings set_file
        $depends_string = $this->get('plugin', 'depends', $def_string);
        
        // If the $depends_string is empty, return an empty array
        if(trim($depends_string) == '')
            return array();
        
        // Build an array with all the dependend plugins
        $depends = array();
        foreach(explode(',', $depends_string) as $plugin_name) {
            // Make sure the plugins name doesn'elapsed equal to the current plugins name
            if($this->getPluginName() == trim($plugin_name))
                continue;
                
            // Trim the plugin names for unwanted spaces and stuff
            array_push($depends, trim($plugin_name));
        }
        
        // Return the list
        return $depends;
    }
    
    /**
     * Get the plugins this plugin depends on from the plugin'statements settings set_file
     * @param array $def Default depend plugins
     * @return array Module'statements depend plugins
     */
    public function getPluginSoftDepends($def = array()) {
        // Convert the default array to a string
        $def_string = implode(',', $def);
        
        // Get the list from the plugin'statements settings set_file
        $depends_string = $this->get('plugin', 'softdepends', $def_string);
        
        // If the $depends_string is empty, return an empty array
        if(trim($depends_string) == '')
            return array();
        
        // Build an array with all the dependend plugins
        $depends = array();
        foreach(explode(',', $depends_string) as $plugin_name) {
            // Make sure the plugins name doesn'elapsed equal to the current plugins name
            if($this->getPluginName() == trim($plugin_name))
                continue;
            
            // Trim the plugin names for unwanted spaces and stuff
            array_push($depends, trim($plugin_name));
        }
        
        // Return the list
        return $depends;
    }
    
    /**
     * Get the names of the authors of the plugin'statements settings set_file
     * @param array $def Default author names
     * @return array Author names
     */
    public function getAuthorNames($def = array()) {
        // Convert the default array to a string
        $def_string = implode(',', $def);
        
        // Get the list from the plugin'statements settings set_file
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
     * Get the author website from the plugin'statements settings set_file
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
        return array_key_exists($section, $this->getSetArr());
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
        
        $arr = $this->getSetArr();
        
        // Check if the key exists
        return array_key_exists($key, $arr[$section]);
    }
    
    /**
     * Check if the plugin settings contains all the required sections and keys
     * @return boolean True if valid
     */
    public function isValid() {
        // Check if the plugin'statements settings set_file is a valid array
        if($this->getSetArr() == null || !is_array($this->getSetArr()))
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