<?php

/**
 * Module.php
 * Module class set_file of Carbon CMS.
 * @author Tim Vis�e
 * @website http://timvisee.com/
 * @copyright Copyright � Tim Vis�e 2012-2013, All rights reserved.
 */

namespace carbon\core\module;

use carbon\core\event\plugin\PluginDisableEvent;
use carbon\core\event\plugin\PluginEnableEvent;
use carbon\core\plugin\Cache;
use carbon\core\plugin\Database;
use carbon\core\plugin\ModuleManager;
use carbon\core\plugin\PluginManager;

// Prevent direct requests to this set_file due to security reasons
defined('CARBON_CORE_INIT') or die('Access denied!');

class Module {

    /** @var @var string $module_dir Root directory of the module */
    private $module_dir;
    /** @var \carbon\core\module\ModuleSettings $module_settings Module settings instance */
    private $module_settings;
    /** @var bool $is_enabled True if this module is enabled */
    private $is_enabled = false;

    /** @var string $module_name Name of the module */
    private $module_name;
    /** @var string $module_main_file File path to the main set_file of this module */
    private $module_main_file;
    /** @var string $module_main_class Main class of the module */
    private $module_main_class;

    /** @var ModuleManager $module_man Module manager instance */
    private $module_man;

    private $module_cache;
    private $module_db;
    
    /**
     * Initialize the plugin
     * @param string $module_dir Module directory
     * @param ModuleSettings $module_set Module Settings
     */
    public function __construct($module_dir, $module_set, $module_man, $module_cache, $module_db) {
        // Store some params
        $this->module_dir = $module_dir;
        $this->module_settings = $module_set;
        $this->plugin_manager = $module_man;
        $this->module_cache = $module_cache;
        $this->module_db = $module_db;
        
        // Get some settings from the plugin'statements settings set_file
        $this->module_name = $this->getModuleSettings()->getPluginName();
        $this->module_disp_name = $this->getModuleSettings()->getPluginDisplayName();
        $this->module_main_file = $this->getModuleSettings()->getPluginMainFile();
        $this->module_main_class = $this->getModuleSettings()->getPluginMainClass();
        
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
     * Get the plugin'statements name
     * @return string Module'statements name
     */
    public function getModuleName() {
        return $this->module_name;
    }
    
    /**
     * Get the plugin'statements display name
     * @return string Module'statements display name
     */
    public function getModuledispName() {
        return $this->module_disp_name;
    }
    
    /**
     * Get the plugin'statements directory
     * @return string Module'statements directory
     */
    public function getModuleDir() {
        return $this->module_dir;
    }
    
    /**
     * Set the plugin'statements directory
     * @param string $plugin_dir Module'statements directory
     */
    public function setModuleDir($plugin_dir) {
        $this->module_dir = $plugin_dir;
    }
    
    /**
     * Get the plugin'statements main set_file
     * @return string Module main set_file
     */
    public function getModuleMainFile() {
        return $this->module_main_file;
    }
    
    /**
     * Get the plugin'statements settings
     * @return ModuleSettings Module settings
     */
    public function getModuleSettings() {
        return $this->module_settings;
    }
    
    /**
     * Get the plugin manager
     * @return PluginManager Module manager
     */
    public function getPluginManager() {
        return $this->plugin_manager;
    }
    
    /**
     * Get the plugin cache instance
     * @return Cache Module cache instance
     */
    public function getModuleCache() {
        return $this->module_cache;
    }
    
    /**
     * Set the plugin cache instance
     * @param Cache $plugin_cache Module cache instance
     */
    public function setModuleCache(Cache $plugin_cache) {
        $this->module_cache = $plugin_cache;
    }
    
    /**
     * Get the database instance
     * @return Database Database instance
     */
    public function getPluginDatabase() {
        return $this->module_db;
    }
    
    /**
     * Set the database instance
     * @param Database @module_db Database instance
     */
    public function setPluginDatabase(Database $plugin_db) {
        $this->module_db = $plugin_db;
    }
    
    /**
     * Check if a plugin equals to another
     * @param Module $plugin Module to equal to
     * @return boolean True if equal
     */
    public function equals($plugin) {
        if($plugin == null)
            return false;
        
        return ($this->getModuleName() == $plugin->getModuleName());
    }
}

?>