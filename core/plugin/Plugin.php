<?php

/**
 * Plugin.php
 * Plugin class file of Carbon CMS.
 * @author Tim Vis�e
 * @website http://timvisee.com/
 * @copyright Copyright � Tim Vis�e 2012-2013, All rights reserved.
 */

namespace core\plugin;

use core\event\plugin\PluginDisableEvent;
use core\event\plugin\PluginEnableEvent;

// Prevent users from accessing this file directly
defined('CARBON_ROOT') or die('Access denied!');

class Plugin {
    
    private $plugin_dir;
    private $plugin_settings;
    private $is_enabled = false;
    
    private $plugin_name;
    private $plugin_display_name;
    private $plugin_main_file;
    private $plugin_main_class;
    
    private $pugin_manager;
    
    private $plugin_cache;
    private $plugin_db;
    
    /**
     * Initialize the plugin
     * @param string $plugin_dir Plugin directory
     * @param PluginSettings $plugin_settings Plugin Settings
     */
    public function __construct($plugin_dir, $plugin_settings, $plugin_manager, $plugin_cache, $plugin_db) {
        // Store some params
        $this->plugin_dir = $plugin_dir;
        $this->plugin_settings = $plugin_settings;
        $this->plugin_manager = $plugin_manager;
        $this->plugin_cache = $plugin_cache;
        $this->plugin_db = $plugin_db;
        
        // Get some settings from the plugin's settings file
        $this->plugin_name = $this->getPluginSettings()->getPluginName();
        $this->plugin_display_name = $this->getPluginSettings()->getPluginDisplayName();
        $this->plugin_main_file = $this->getPluginSettings()->getPluginMainFile();
        $this->plugin_main_class = $this->getPluginSettings()->getPluginMainClass();
        
        // Run the onLoad() function
        $this->onLoad();
    }
    
    /**
     * Function called when the plugin is being loaded
     */
    public function onLoad() { }
    
    /**
     * Function called when the plugin is being enabled
     */
    public function onEnable() { }
    
    /**
     * Function called when the plugin is being disabled
     */
    public function onDisable() { }
    
    /**
     * Check if the plugin is enabled
     * @return boolean True if enabled
     */
    public function isEnabled() {
        return $this->is_enabled;
    }
    
    /**
     * Enable or disable the plugin
     * @param boolean $enabled True to enable the plugin
     */
    public function setEnabled($enabled) {
        // Make sure the param is of a boolean type
        if(!is_bool($enabled))
            return;
        
        // The new enabled state has to be different than the current state
        if($this->isEnabled() != $enabled) {
            
            // Get the plugin manager
            $plugin_manager = $this->getPluginManager();
                
            // Call the events
            if($enabled) {
                // Call the 'PluginEnableEvent' event
                $event = new PluginEnableEvent($this);
                $this->getPluginManager()->getEventManager()->callEvent($event);
                
                // Check if the plugin was being canceled
                if($event->isCanceled())
                    return;
            } else {
                // Call the 'PluginDisableEvent' event
                $event = new PluginDisableEvent($this);
                $this->getPluginManager()->getEventManager()->callEvent($event);
            }
            
            // Enable or disable the plugin
            $this->is_enabled = $enabled;
            
            // Run the functions according to the state
            if($enabled)
                $this->onEnable();
            else
                $this->onDisable();
        }
    }
    
    /**
     * Get the plugin's name
     * @return string Plugin's name
     */
    public function getPluginName() {
        return $this->plugin_name;
    }
    
    /**
     * Get the plugin's display name
     * @return string Plugin's display name
     */
    public function getPluginDisplayName() {
        return $this->plugin_display_name;
    }
    
    /**
     * Get the plugin's directory
     * @return string Plugin's directory
     */
    public function getPluginDir() {
        return $this->plugin_dir;
    }
    
    /**
     * Set the plugin's directory
     * @param string $plugin_dir Plugin's directory
     */
    public function setPluginDir($plugin_dir) {
        $this->plugin_dir = $plugin_dir;
    }
    
    /**
     * Get the plugin's main file
     * @return string Plugin main file
     */
    public function getPluginMainFile() {
        return $this->plugin_main_file;
    }
    
    /**
     * Get the plugin's settings
     * @return PluginSettings Plugin settings
     */
    public function getPluginSettings() {
        return $this->plugin_settings;
    }
    
    /**
     * Get the plugin manager
     * @return PluginManager Plugin manager
     */
    public function getPluginManager() {
        return $this->plugin_manager;
    }
    
    /**
     * Get the plugin cache instance
     * @return Cache Plugin cache instance
     */
    public function getPluginCache() {
        return $this->plugin_cache;
    }
    
    /**
     * Set the plugin cache instance
     * @param Cache $plugin_cache Plugin cache instance
     */
    public function setPluginCache(Cache $plugin_cache) {
        $this->plugin_cache = $plugin_cache;
    }
    
    /**
     * Get the database instance
     * @return Database Database instance
     */
    public function getPluginDatabase() {
        return $this->plugin_db;
    }
    
    /**
     * Set the database instance
     * @param Database @plugin_db Database instance
     */
    public function setPluginDatabase(Database $plugin_db) {
        $this->plugin_db = $plugin_db;
    }
    
    /**
     * Check if a plugin equals to another
     * @param Plugin $plugin Plugin to equal to
     * @return boolean True if equal
     */
    public function equals($plugin) {
        if($plugin == null)
            return false;
        
        return ($this->getPluginName() == $plugin->getPluginName());
    }
}

?>