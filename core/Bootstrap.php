<?php

/**
 * Bootstrap.php
 *
 * The Bootstrap class constructs all the basic classes like the Database and the Config class.
 *
 * @author Tim Visee
 * @version 0.1
 * @website http://timvisee.com/
 * @copyright Copyright (C) Tim Visee 2012-2013, All rights reserved.
 */

namespace core;

use core\CacheHandler;
use core\ConfigHandler;
use core\Core;
use core\Database;
use core\ErrorHandler;
use core\EventManager;
use core\Listener;
use core\PluginManager;
use core\Router;
use core\UserManager;
use core\language\Language;
use core\util\LanguageUtils;
use core\language\Locale;
use core\util\LocaleUtils;
use core\plugin\PluginSettings;
use core\util\DateUtils;
use core\util\IpUtils;

// Prevent users from accessing this file directly
defined('CARBON_ROOT') or die('Access denied!');

/**
 * Constructs all the basic classes like the Database and the Config class.
 * @package core
 * @author Tim Visee
 */
class Bootstrap {

    /**
     * Constructor
     * @param bool $initialize True to initialize the bootstrap automatically
     */
    public function __construct($initialize = true) {
        // Initialize the bootstrap
        if($initialize)
            $this->init();
    }

    /**
     * Initialize the Bootstrap
     */
    public function init() {
        // Set up and initialize the error handler
        // TODO: Make sure the error handler is not showing sensitive data!
        ErrorHandler::init(true, true, true, false);

        // Initialize and loadLocalesList the config handler and set the config handler instance in the Core class
        $cfg = new ConfigHandler();
        Core::setConfig($cfg);

        // TODO: Enable or disable the debug mode in the error handler, based on the configuration file
        // TODO: Try to enable this earlier
        // TODO: Build better debugging system!

        // Should the debug mode be enabled
        if($cfg->getBool('carbon', 'debug', false) === true) {
            // Enable the debug mode in the Error Handler
            ErrorHandler::setDebug(true);

            // Enable PHP's debugging mode
            ini_set('display_errors', 'On');
            error_reporting(E_ALL);
        } else {
            // Disable the debug mode in the Error Handler
            ErrorHandler::setDebug(false);

            // TODO: Do this in bootstrap shutdown method?
            // Turn off the debugging mode (might still be enabled)
            ini_set('display_errors', 'Off');
        }



        // Initialize the language manager
        echo 'Languages:<br /><br /><pre>';
        print_r(LanguageUtils::getLanguages());
        echo '</pre>';
        die();

        echo 'Locales:<br /><br /><pre>';
        print_r(LocaleUtils::getLocales(true));
        echo '</pre>';
        die();




        // Some error causers, to debug the error handling system
        trigger_error('Test Error, to debug the Error Handler system...');
        //require_once("sdfoijsodijfs.phsdfsdfsdfp");

        // Initialize the router and set the router instance in the Core class
        $router = new Router();
        Core::setRouter($router);

        // Set up the database and set the database instance in the Core class
        $db = $this->setUpDatabase();
        Core::setDatabase($db);

        // Set up the registry handler class and set the registry handler in the Core class
        $options = new RegistryHandler(Core::getDatabase());
        Core::setRegistry($options);

        // Set up the caching system and set the cache instance in the Core class
        $cache = $this->setUpCache();
        Core::setCache($cache);

        // Set the cache instance in the RegistryHandler class
        $options->setCache($cache);

        // Set up and initialize the user manager and set the user manager instance in the Core class
        $user_man = new UserManager($cache, $db);
        Core::setUserManager($user_man);

        // Set the default timezone of the server
        $this->setServerTimezone();

        // Initialize the event manager and set the event manager instance in the Core class
        $event_man = new EventManager();
        Core::setEventManager($event_man);
        
        // Set up the plugin manager and set the plugin manager instance in the Core class
        $plugin_man = $this->setUpPluginManager();
        Core::setPluginManager($plugin_man);

        // Verify the client requesting the page, make sure this client was not banned
        $this->verifyClient();

        // TODO: Set up the MetaManager with it's Meta tags here?

        // Route the page request to through the router to the right controller
        $router->route();
    }

    /**
     * Set up and initialize the database system
     * @return Database Database instance
     */
    public function setUpDatabase() {
        // Get the ConfigHandler instance
        $cfg = Core::getConfig();

        // Retrieve the database connection details from the config
        $db_host = $cfg->getValue('database', 'host');
        $db_port = $cfg->getValue('database', 'port');
        $db_database = $cfg->getValue('database', 'database');
        $db_username = $cfg->getValue('database', 'username');
        $db_password = $cfg->getValue('database', 'password');

        // Get the database table prefix
        $table_prefix = $cfg->getValue('database', 'table_prefix', '');

        // Construct the database class
        $db = new Database($table_prefix);

        // TODO: Error handling for wrong database credentials and similar stuff

        // Connect to the database, try to reconnect if failed
        try {
            // Try to connect to the database
            $db->connectDatabase($db_host, $db_port, $db_database, $db_username, $db_password);
        } catch(\PDOException $ex) {
            // The connection to the database failed, try to connect once again
            try {
                // Try to connect to the database
                $db->connectDatabase($db_host, $db_port, $db_database, $db_username, $db_password);
            } catch(\PDOException $ex) {
                // The connection to the database failed twice, show an error message
                // TODO: Show proper error message
                die('Failed to connect to the database!<br />' . $ex->getMessage());
            }
        }

        // Return the database instance
        return $db;
    }

    /**
     * Set up and initialize the caching system
     * @return CacheHandler Cache instance
     */
    public function setUpCache() {
        // Get the RegistryHandler instance
        $options = Core::getRegistry();

        // Get the cache dir to use
        $cache_dir = CARBON_ROOT . DIRECTORY_SEPARATOR . ltrim($options->getString('cache.directory', DIRECTORY_SEPARATOR . 'cache'), '/\\');

        // Initialize the caching system
        $cache = new CacheHandler($cache_dir);

        // Set if cache is enabled or not
        $cache->setEnabled($options->getBool('cache.enabled', true));

        // Return the cache instance
        return $cache;
    }

    /**
     * Set up and initialize the plugin manager
     * @return PluginManager Instance of the plugin manager
     */
    public function setUpPluginManager() {
        // Get the plugins directory path from the registry
        $plugins_dir = CARBON_ROOT . DIRECTORY_SEPARATOR . ltrim(Core::getRegistry()->getString('plugin.directory', DIRECTORY_SEPARATOR . 'plugin'), '/\\');

        // Initialize/construct the Plugin Manager and store it in a variable
        $plugin_mngr = new PluginManager($plugins_dir, Core::getEventManager(), Core::getCache(), Core::getDatabase());
        
        // Load and enable all plugins
        $plugin_mngr->loadPlugins();
        $plugin_mngr->enablePlugins();
        
        // Return the plugin manager instance
        return $plugin_mngr;
    }

    /**
     * Set the default timezone of the server
     */
    public function setServerTimezone() {
        // Retrieve the default timezone from the Options database and trim the value
        $timezone = trim(Core::getRegistry()->getString("time.def_timezone", ""));

        // If the timezone is valid, set the timezone of the server
        if($timezone != null)
            if(DateUtils::isValidTimezone($timezone))
                DateUtils::setTimezone($timezone);
    }

    /**
     * Verify the client requesting the page
     */
    public function verifyClient() {
        // Check if the IP of the client is unknown or not
        if(IpUtils::isClientIpUnknown()) {
            // TODO: Check if clients from unknown IP's are blocked or not
            // TODO: Check for country, hosting provider, check if is proxy, etc...
        } else {
            // Get the IP address of the client
            //$client_ip = IpUtils::getClientIp();

            // TODO: Check if the IP address of the client is blocked

            /*if(fsockopen($client_ip, 80, $errstr, $errno, 1)) {
                die("Proxy access not allowed");
            }*/
        }

        // TODO: Check if proxies should be blocked

        // TODO: Show info message when using site through localhost
    }

    /**
     * Stop the Bootstrap, should only be called after the Bootstrap has been initialized.
     */
    public function shutdown() {
        // Get the PluginsManager instance
        $plugin_mngr = Core::getPluginManager();

        // Disable/shutdown all running plugins
        if($plugin_mngr != null)
            $plugin_mngr->disablePlugins();

        // TODO: Unregister all registered events (probably already done!)

        // TODO: Disconnect from database
        // TODO: Other shutdown stuff here...

        // TODO: Disable PHP's debug stuff?
    }
}