<?php

// TODO: Remove this depricated file

/**
 * Plugin.php
 *
 * Plugin class.
 *
 * @author Tim Visee
 * @website http://timvisee.com/
 * @copyright Copyright (C) Tim Visee 2012-2013, All rights reserved.
 */

namespace core;

use \Exception;
use core\CacheHandler;
use core\Database;

// Prevent users from accessing this file directly
defined('CARBON_ROOT') or die('Access denied!');

die('file depricated? core/Plugin.php');

/**
 * Plugin class
 * @package core
 * @author Tim Visee
 */
class Plugin {
    
    /** @var string $plugin_name Plugin's name */
    private $plugin_name;
    /** @var string $plugin_dir Plugin's directory */
    private $plugin_dir;
    
    /**
     * Constructor
     * @param string $plugin_name Unique plugin name
     * @param string $plugin_dir Plugin directory
     * @param CacheHandler $plugin_cache Plugin cache
     * @param Database $plugin_database Plugin database
     */
    public function __construct($plugin_name, $plugin_dir) {
        // Store the parameters
        $this->plugin_name = $plugin_name;
        $this->plugin_dir = rtrim($plugin_dir, '/') . '/';
    }
    
    /**
     * Get the unique plugin name
     * @return string Unique plugin name
     */
    public function getPluginName() {
        return $this->plugin_name;
    }
    
    /**
     * Get the plugins directory
     * @return string Plugins directory
     */
    public function getPluginDirectory() {
        return $this->plugin_dir;
    }
    
    /**
     * Get the plugins settings
     * @return array Plugin settings
     */
    public function getPluginSettings() {
        // TODO: Use cache for this!
        
        $plugin_ini = $this->plugin_dir . 'plugin.ini';
        
        return parse_ini_file($plugin_ini, true);
    }
    
    /**
     * Check if the plugin settings contains a section
     * @param string $section Section to check
     * @return boolean True if section exists
     */
    public function getPluginSettingsContainsSection($section) {
        // TODO: Use caching
        
        // Get the plugin settings
        $settings = getPluginSettings();
        
        // Return true if the settings contains this section
        return array_key_exists($section, $settings);
    }
    
    /**
     * Check if the plguin settings contains a key
     * @param string $section Key section
     * @param string $key Key to check
     * @return boolean True if the key exists
     */
    public function getPluginSettingsContainsKey($section, $key) {
        // TODO: Use caching
        
        // Get the plugin settings
        $settings = $this->getPluginSettings();
        
        // Check if the section exists
        if(!array_key_exists($section, $settings))
            return false;
        
        // Return true if the settings contains this key
        return array_key_exists($key, $settings[$section]);
    }
    
    /**
     * Get a value from the plugins settings
     * @param string $section Key section
     * @param string $key Key name
     * @param mixed $def Default value returned when an error occured (optional)
     * @return mixed Value from plugin settings or default value
     */
    public function getPluginSettingsValue($section, $key, $def = null) {
        // TODO: Use caching
        
        // Get the plugin settings
        $settings = $this->getPluginSettings();
        
        // Make sure the plugin settings contains the plugin section
        if(!array_key_exists($section, $settings))
            return $def;
        
        // Make sure the plugin settings contains the required main keys
        if(!array_key_exists($key, $settings[$section]))
            return $def;
        
        // Return the value
        return $settings[$section][$key];
    }
    
    /**
     * Get the main file of the plugin
     * @param boolean @full_path True to return the full path of the plugin's main file (optional)
     * @param boolean @check_file True to check if the plugins main file exists (optional)
     * @return string Plugins main file or main file path, emptry string if an error occured
     */
    public function getPluginMain($full_path = true, $check_file = true) {
        // Make sure the plugin settings contains this key
        if(!$this->getPluginSettingsContainsKey('plugin', 'main')) {
            throw new Exception('Carbon CMS: Plugin \'' . $this->getPluginName() . '\' doesn\'t have the required key \'plugin.main\' in it\'s settings!');
            return '';
        }
        
        // Get the plugins main file
        $plugin_main = $this->getPluginSettingsValue('plugin', 'main', '');
        $plugin_main_path = $this->getPluginDirectory() . $plugin_main;
        
        // Should be checked if the main plugins file exist
        if($check_file) {
            // Make sure the plugin's main file exists
            if(!file_exists($plugin_main_path)) {
                throw new Exception('Carbon CMS: Main file \'' . $plugin_main . '\' of the plugin \'' . $this->getPluginName() . '\' doesn\'t exist!');
                return '';
            }
        }
        
        // Return the plugin's full path or it's main file according to $full_path
        if($full_path)
            return $plugin_main_path;
        return $plugin_main;
    }
}