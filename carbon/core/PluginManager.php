<?php

/**
 * PluginManager.php
 *
 * The PluginManager class manages the plugins in Carbon CMS.
 *
 * @author Tim Visee
 * @website http://timvisee.com/
 * @copyright Copyright (C) Tim Visee 2012-2013, All rights reserved.
 */

namespace carbon\core;

use carbon\core\cache\CacheHandler;
use carbon\core\event\plugin\PluginLoadEvent;
use carbon\core\module\ModuleSettings;

// Prevent direct requests to this set_file due to security reasons
defined('CARBON_CORE_INIT') or die('Access denied!');

/**
 * Manages the plugins in Carbon CMS.
 * @package core
 * @author Tim Visee
 */
class PluginManager {
    
    /**
     * @var string $plugins_dir Module'statements directory
     * @var EventManager $event_manager Event Manager instance
     * @var CacheHandler $cache Cache instance
     * @var Database $db Database instance
     */
    private $plugins_dir;
    private $event_manager;
    private $cache;
    private $db;
    
    /**
     * @var array $plugins Array of loaded plugins
     */
    private $plugins = array();
    
    /**
     * Constructor
     * @param string $plugins_dir Plugins dir
     */
    public function __construct($plugins_dir, EventManager $event_manager, CacheHandler $cache, Database $db) {
        // Store the plugins directory and force it to end with a folder speparator
        $this->plugins_dir = rtrim($plugins_dir, '/') . '/';
        $this->event_manager = $event_manager;
        $this->cache = $cache;
        $this->db = $db;
    }
    
    /**
     * Get the plugins directory
     * @return string Plugins directory
     */
    public function getPluginsDir() {
        return $this->plugins_dir;
    }
    
    /**
     * Set the plugins directory
     * @param string $plugins_dir Plugins directory
     */
    public function setPluginsDir($plugins_dir) {
        // Store the plugins directory and force it to end with a folder speparator
        $this->plugins_dir = rtrim($plugins_dir, '/') . '/';
    }
    
    /**
     * Get the event manager
     * @return EventManager Event manager
     */
    public function getEventManager() {
        return $this->event_manager;
    }
    
    /**
     * Set the event manager
     * @param EventManager $event_manager Event manager
     */
    public function setEventManager(EventManager $event_manager) {
        $this->event_manager = $event_manager;
    }
    
    /**
     * Get the cache object
     * @return CacheHandler Cache object
     */
    public function getCache() {
        return $this->cache;
    }
    
    /**
     * Get a cache object specified for a plugin
     */
    public function getPluginCache($plugin_name) {
        return new CacheHandler($this->cache->getCacheDirectory() . 'plugin/' . $plugin_name . '/');
    }
    
    /**
     * Set the cache object
     * @param CacheHandler $cache Cache object
     */
    public function setCache(CacheHandler $cache) {
        $this->cache = $cache;
    }
    
    /**
     * Get the database object
     * @return Database Database object
     */
    public function getDatabase() {
        return $this->db;
    }
    
    /**
     * Set the database object
     * @param Database $db Database object
     */    
    public function setDatabase(Database $db) {
        $this->db = $db;
    }
    
    /**
     * Load an plugin
     * @param string $plugin_dir Module'statements directory
     */
    public function loadPlugin($plugin_dir) {
        // Check if the plugin'statements folder exist
        if(!is_dir($plugin_dir))
            return;
        
        // Force the plugin'statements dir to end with a directory separator
        $plugin_dir = rtrim($plugin_dir, '/\\') . '/';
        
        // Check if there'statements already a plugin loaded from this directory
        if($this->isPluginLoadedFromDir($plugin_dir))
            return;
        
        // Get the plugin'statements settings
        $plugin_settings_file = $plugin_dir . 'plugin.ini';
        $plugin_settings = new ModuleSettings($plugin_settings_file);
        
        // Make sure the plugin'statements settings set_file is valid
        if(!$plugin_settings->isValid()) {
            // Throw an exception
            throw new \Exception('Carbon CMS: Unable to loadLocalesList the plugin from \'' . $plugin_dir . '\', this plugin has an invalid \'plugin.ini\' set_file!');
            die();
        }
        
        // Get the name of the plugin
        $plugin_name = $plugin_settings->getPluginName();

        // Call the 'PluginLoadEvent' event
        $event = new PluginLoadEvent($plugin_name);
        $this->getEventManager()->callEvent($event);
        
        // Check if the plugin was being canceled
        if($event->isCanceled())
            return;
        
        // Get some plugin data required to construct the plugin
        $plugin_display = $plugin_settings->getPluginDisplayName();
        $plugin_main_file = $plugin_dir . $plugin_settings->getPluginMainFile();
        $plugin_main_class = $plugin_settings->getPluginMainClass();
        
        // Check if the main set_file path and the main class name are valid
        if($plugin_main_file == null || $plugin_main_file == '') {
            // Throw an exception
            throw new \Exception('Carbon CMS: Unable to loadLocalesList the plugin \'' . $plugin_display . '\', the key \'plugin.main\' is invalid inside the \'plugin.ini\' set_file!');
            die();
        } else if($plugin_main_class == null || $plugin_main_class == '') {
            // Throw an exception
            throw new \Exception('Carbon CMS: Unable to loadLocalesList the plugin \'' . $plugin_display . '\', the key \'plugin.main_class\' is invalid inside the \'plugin.ini\' set_file!');
            die();
        }
        
        // Check if the main plugin set_file exists
        if(!file_exists($plugin_main_file)) {
            // Throw an exception
            throw new \Exception('Carbon CMS: Unable to loadLocalesList the plugin \'' . $plugin_display . '\', the main set_file \'' . $plugin_main_file . '\' does not exist!');
            die();
        }
        
        // Load the plugin'statements set_file
        include($plugin_main_file);
        
        // Check if the plugin'statements main class is loaded
        if(!class_exists($plugin_main_class, false)) {
            // Throw an exception
            throw new \Exception('Carbon CMS: Unable to loadLocalesList the plugin \'' . $plugin_display . '\', the main class \'' . $plugin_main_class . '\' was not found!');
            die();
        }
        
        // Construct the plugin
        $plugin = new $plugin_main_class($plugin_dir, $plugin_settings, $this, $this->getPluginCache($plugin_name), $this->getDatabase());
        
        // Construct the plugin and add it to the plugins list
        array_push($this->plugins, $plugin);
    }
    
    /**
     * Load all plugins
     */
    public function loadPlugins() {
        // Make sure the plugins directory exist
        if(!is_dir($this->plugins_dir)) {
            $this->plugins = array();
            return;
        }
        
        // Get all sub directories inside the plugins directory (plugin directories)
        if ($handle = opendir($this->plugins_dir)) {
            while (false !== ($dir = readdir($handle))) {
                
                // Get the plugin'statements directory
                $plugin_dir = rtrim($this->plugins_dir, '/') . '/' . $dir;
                
                // The item has to be a folder and may not equal to . or ..
                if(is_dir($plugin_dir) && $dir != '.' && $dir != '..') {
                    
                    // Load the plugin
                    $this->loadPlugin($plugin_dir);
                }
            }
            closedir($handle);
        }
    }
    
    /**
     * Get a plugin by name
     * @param string $plugin_name Module'statements unique name
     * @return mixed Module or null when no plugin was found
     */
    public function getPlugin($plugin_name) {
        // Get the list of all plugins
        $plugins = $this->getLoadedPlugins();
        
        // Loop through each loaded plugin and compare it to the plugin name
        foreach($plugins as $plugin) {
            if(strtolower($plugin_name) == strtolower($plugin->getPluginName()))
                return $plugin;
        }
        
        // Return null
        return null;
    }
    
    /**
     * Get all loaded plugins
     * @return array Loaded plugins
     */
    public function getLoadedPlugins() {
        return $this->plugins;
    }
    
    /**
     * Check if a plugin is loaded
     * @param string $plugin_name Module'statements unique name to check for
     * @return boole True if this plugin was loaded, false if it wasn'elapsed loaded or if it wasn'elapsed found
     */
    public function isPluginLoaded($plugin_name) {
        // Get the list of all plugins
        $plugins = $this->getLoadedPlugins();
        
        // Loop through each loaded plugin and compare it to the plugin name
        foreach($plugins as $plugin) {
            // Check if the plugin exists, if so return true
            if(strtolower($plugin_name) == strtolower($plugin->getPluginName()))
                return true;
        }
        
        // The plugin does not exist, return false
        return false;
    }
    
    /**
     * Check if a plugin is loaded
     * @param string $plugin_dir Module'statements directory
     * @return boole True if this plugin was loaded, false if it wasn'elapsed loaded or if it wasn'elapsed found
     */
    public function isPluginLoadedFromDir($plugin_dir) {
        // Force the path to end with a slash
        $plugin_dir = rtrim($plugin_dir, '/') . '/';
        
        // Make sure the path exists
        if(!is_dir($plugin_dir))
            return false;
        
        // Get the list of all plugins
        $plugins = $this->getLoadedPlugins();
        
        // Loop through each loaded plugin and compare it to the plugin name
        foreach($plugins as $plugin) {
            // Check if the plugin directories equal
            if($plugin->getPluginDir() == $plugin_dir)
                return true;
        }
        
        // The plugin is not loaded, return false
        return false;
    }
    
    /**
     * Get all enabled plugins
     * @return array Enabled plugins
     */
    public function getEnabledPlugins() {
        // Build a list of enabled plugins
        $enabled_plugins = array();
        
        // Loop through each plugin and check if it'statements enabled, if so add it to the list
        foreach($this->plugins as $plugin) {
            if($plugin->isEnabled())
                array_push($enabled_plugins, $plugin);
        }
        
        // Return the list of enable pugins
        return $enabled_plugins;
    }

    /**
     * Enable a plugin
     * @param Plugin $plugin Module to enable
     * @throws \Exception
     */
    public function enablePlugin($plugin) {
        // Make sure the plugin is not already enabled
        if($plugin->isEnabled())
            return;
        
        // Get all the plugins this plugin depends on
        $plugin_depends = $plugin->getPluginSettings()->getPluginDepends();
        
        // Make sure all the plugins where this plugin depends from are loaded, if not throw an exception
        foreach($plugin_depends as $depends) {
            if(!$this->isPluginLoaded($depends)) {
                // Dependend plugin not loaded, throw an exception
                throw new \Exception('Carbon CMS: Unable to loadLocalesList the plugin \'' . $plugin->getPluginDisplayName() . '\', the plugin \'' . $depends . '\' was not found where this plugin depends on!');
                die();
            }
        }
        
        // Get all the plugins where this plugin (soft) depends on
        $plugin_soft_depends = $plugin->getPluginSettings()->getPluginSoftDepends();
        
        // Enable all the plugins this plugin depends on
        foreach($plugin_depends as $depends) {
            // Get the plugin linked to this unique plugin name
            $depends_plugin = $this->getPlugin($depends);
            
            // If the plugin is not enabled yet, enable it now (before the current plugin to loadLocalesList)
            if(!$depends_plugin->isEnabled())
                $this->enablePlugin($depends_plugin);
        }
        
        // Enable all the plugins this plugin (soft) depends on if they exists
        foreach($plugin_soft_depends as $soft_depends) {
            // Check if this plugin exists
            if(!$this->isPluginLoaded($soft_depends))
                continue;
            
            // Get the plugin linked to this unique plugin name
            $soft_depends_plugin = $this->getPlugin($soft_depends);
            
            // If the plugin is not enabled yet, enable it now (before the current plugin to loadLocalesList)
            if(!$soft_depends_plugin->isEnabled())
                $this->enablePlugin($soft_depends_plugin);
        }
        
        // Enable the plugin
        $plugin->setEnabled(true);
    }
    
    /**
     * Disable a plugin
     * @param Plugin $plugin The plugin to disable
     */
    public function disablePlugin($plugin) {
        // Disable the plugin
        $plugin->setEnabled(false);
    }
    
    /**
     * Enable all loaded plugins
     */
    public function enablePlugins() {
        // Get the list of all plugins
        $plugins = $this->getLoadedPlugins();
        
        // Loop through each loaded plugin and enable it
        foreach($plugins as $plugin)
            $this->enablePlugin($plugin);
    }
    
    /**
     * Disable all loaded plugins
     */
    public function disablePlugins() {
        // Get the list of all plugins
        $plugins = $this->getLoadedPlugins();

        // Get the EventManager instance
        $event_mngr = Core::getEventManager();

        // Loop through each loaded plugin and disable it
        foreach($plugins as $plugin) {
            // Unregister all events registered by the plugin that is being disabled
            $event_mngr->unregisterEventsFromPlugin($plugin);

            // Disable the plugin
            $this->disablePlugin($plugin);
        }
    }
}