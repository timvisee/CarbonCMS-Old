<?php

// TODO: Extend this class
// TODO: Update PHP Docs in this class

/**
 * Core.php
 *
 * The Core class supplies all the core class instances like the database and the config handler.
 *
 * @author Tim Visee
 * @website http://timvisee.com/
 * @copyright Copyright © Tim Visee 2012-2013, All rights reserved.
 */

namespace core;

use core\EventManager;
use core\PluginManager;

// Prevent users from accessing this file directly
defined('CARBON_ROOT') or die('Access denied!');

/**
 * Supplies all the core class instances.
 * @package core
 * @author Tim Visee
 */
class Core {

    /** @var ConfigHandler $cfg ConfigHandler holder */
    private static $cfg = null;
    /** @var Router $router Router holder */
    private static $router = null;
    /** @var Database $db Database holder */
    private static $db = null;
    /** @var RegistryHandler $options RegistryHandler holder */
    private static $registry = null;
    /** @var CacheHandler $cache CacheHandler holder */
    private static $cache = null;
    /** @var UserManager $user_man UserManager holder */
    private static $user_man = null;
    /** @var EventManager $event_man EventManager holder */
    private static $event_man = null;
    /** @var PluginManager $plugin_man PluginManager holder */
    private static $plugin_man = null;
    /** @var PageManager $page_man PageManager holder */
    private static $page_man = null;

    /**
     * Get the ConfigHandler instance
     * @return ConfigHandler ConfigHandler instance
     */
    public static function getConfig() {
        return self::$cfg;
    }

    /**
     * Set the ConfigHandler instance
     * @param ConfigHandler $cfg ConfigHandler instance
     */
    public static function setConfig(ConfigHandler $cfg) {
        self::$cfg = $cfg;
    }

    /**
     * Get the Router instance
     * @return Router Router instance
     */
    public static function getRouter() {
        return self::$router;
    }

    /**
     * Set the Router instance
     * @param Router $router Router instance
     */
    public static function setRouter(Router $router) {
        self::$router = $router;
    }

    /**
     * Get the Database instance
     * @return Database Database instance
     */
    public static function getDatabase() {
        return self::$db;
    }

    /**
     * Set the Database instance
     * @param Database $db Database instance
     */
    public static function setDatabase(Database $db) {
        self::$db = $db;
    }

    /**
     * Get the RegistryHandler instance
     * @return RegistryHandler RegistryHandler instance
     */
    public static function getRegistry() {
        return self::$registry;
    }

    /**
     * Set the RegistryHandler instance
     * @param RegistryHandler $options RegistryHandler instance
     */
    public static function setRegistry(RegistryHandler $options) {
        self::$registry = $options;
    }

    /**
     * Get the CacheHandler instance
     * @return CacheHandler instance
     */
    public static function getCache() {
        return self::$cache;
    }

    /**
     * Set the CacheHandler instance
     * @param CacheHandler $cache CacheHandler instance
     */
    public static function setCache(CacheHandler $cache) {
        self::$cache = $cache;
    }

    /**
     * Get the UserManager instance
     * @return UserManager UserManager instance
     */
    public static function getUserManager() {
        return self::$user_man;
    }

    /**
     * Set the UserManager instance
     * @param UserManager $user_man UserManager instance
     */
    public static function setUserManager(UserManager $user_man) {
        self::$user_man = $user_man;
    }

    /**
     * Get the EventManager instance
     * @return EventManager EventManager instance
     */
    public static function getEventManager() {
        return self::$event_man;
    }

    /**
     * Set the EventManager instance
     * @param EventManager $event_man EventManager instance
     */
    public static function setEventManager(EventManager $event_man) {
        self::$event_man = $event_man;
    }

    /**
     * Get the PluginManager instance
     * @return PluginManager PluginManager instance
     */
    public static function getPluginManager() {
        return self::$plugin_man;
    }

    /**
     * Set the PluginManager instnace
     * @param PluginManager $plugin_man PluginManager instance
     */
    public static function setPluginManager(PluginManager $plugin_man) {
        self::$plugin_man = $plugin_man;
    }

    /**
     * Get the PageManager instance
     * @return PageManager PageManager instance
     */
    public static function getPageManager() {
        return self::$page_man;
    }

    /**
     * Set the PageManager instance
     * @param PageManager $page_man PageManager instance
     */
    public static function setPageManager(PageManager $page_man) {
        self::$page_man = $page_man;
    }
}