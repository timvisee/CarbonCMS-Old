<?php

/**
 * Config.php
 *
 * The ConfigHandler class handles the configuration file of Carbon CMS.
 *
 * Reads the Carbon CMS configuration file.
 * @author Tim Vise
 * @website http://timvisee.com/
 * @copyright Copyright (C) Tim Visee 2012, All rights reserved.
 */

namespace core;

use core\exception\config\CarbonConfigException;
use core\exception\config\CarbonConfigLoadException;
use core\util\ArrayUtils;

// Prevent users from accessing this file directly
defined('CARBON_ROOT') or die('Access denied!');

/**
 * Handles the configuration file of Carbon CMS.
 * @package core
 * @author Tim Visee
 */
class ConfigHandler {

    /** @var $CONFIG_DEFAULT_FILE_PATH String Default path of the configuration file */
    private static $CONFIG_DEFAULT_FILE_PATH = null;
    /** @var $CONFIG_REQUIRED_KEYS Array Array of required keys in a configuration array */
    private static $CONFIG_REQUIRED_KEYS = null;

    /** @var $cfg_path String File path of the configuration file */
    private $cfg_path;
    /** @var $cfg_arr Array Array containing the configuration file values after the file has been loaded */
    private $cfg_arr = null;

    /**
     * Construct the Config class. Does not automatically loadLocalesList the config file, use loadLocalesList() instead.
     * @param string $cfg_file The path of the configuration file, null to use the default config file path
     * @param bool $load [optional] True to loadLocalesList the configuration file, false if not
     */
    public function __construct($cfg_file = null, $load = true) {
        // Set some default values
        self::$CONFIG_DEFAULT_FILE_PATH = CARBON_ROOT . DIRECTORY_SEPARATOR . "config/config.ini";
        self::$CONFIG_REQUIRED_KEYS = Array(
            "general" => Array(
                "site_url",
                "site_path"
            ),
            "database" => Array(
                "host",
                "port",
                "database",
                "username",
                "password",
                "table_prefix"
            ),
            "hash" => Array(
                "hash_algorithm",
                "hash_key"
            ),
            "carbon" => Array(
                "debug",
                "version"
            )
        );

        // Make sure the configuration path is set, if not, use the default configuration file
        if($cfg_file == null)
            $cfg_file = self::$CONFIG_DEFAULT_FILE_PATH;

        // Store the configuration file path
        $this->cfg_path = $cfg_file;

        // Check whether the configuration file should be loaded
        if($load)
            $this->load();
    }
    
    /**
     * Load the configuration file
     */
    public function load() {
        // Get the configuration file path
        $cfg_file = $this->cfg_path;

        // Make sure the configuration file exists
        if(!file_exists($cfg_file)) {
            // The configuration file doesn't exist, throw an exception
            throw new CarbonConfigLoadException(
                'Unable to loadLocalesList configuration file from \'' . $cfg_file . '\', file does not exist!',
                0, null,
                'Create a configuration file: \'' . $cfg_file . '\'');
        }

        // Load the configuration file
        $cfg_arr = parse_ini_file($cfg_file, true);

        // Make sure loading the configuration file succeed
        if($cfg_arr === false || !is_array($cfg_arr)) {
            // Error occured while loading the configuration file, throw an exception
            throw new CarbonConfigLoadException(
                'An error occured while parsing the configuration file!',
                0, null,
                'Make sure the data of the configuration file is valid.');
        }

        // Make sure the configuration array is valid
        $this->isValidConfig($cfg_arr, true);

        // Store a copy of the configuration values
        $this->cfg_arr = ArrayUtils::copyArray($cfg_arr);
    }
    
    /**
     * Unload the configuration file
     */
    public function unloadConfig() {
        // Reset the configuration values array to unload the config file
        $this->cfg_arr = array();
    }
    
    /**
     * Check if a configuration file is loaded
     * @return bool True if loaded
     */
    public function isConfigLoaded() {
        // Make sure the configuration data is an array
        if(!is_array($this->cfg_arr))
            return false;

        // Make sure the array contains any items
        return (sizeof($this->cfg_arr) > 0);
    }

    /**
     * Get akk keys from a configuration array section
     * @param string $section The section to get the keys from
     * @param array|null $cfg_arr [optional] The configuration array to get the keys from,
     * null to use the current loaded configuration array
     * @return array|null Array with keys from the section, or null if the section or configuration array was invalid.
     */
    public function getKeys($section, $cfg_arr = null) {
        // Use the default configuration array if the param equals to null
        if($cfg_arr == null)
            $cfg_arr = $this->cfg_arr;

        // The configuration array may not be null and must be an array
        if($cfg_arr == null || !is_array($cfg_arr))
            return false;

        // Make sure this section exists
        if(!$this->hasSection($section, $cfg_arr))
            return null;

        // Return the config keys
        return $cfg_arr[$section];
    }

    /**
     * Get a value from a loaded configuration file
     * @param string $section The section in the configuration file
     * @param string $key The key in the configuration file
     * @param mixed $default The default value returned if the key was not found
     * @param array|null $cfg_arr [optional] The configuration array to get the value from,
     * null to use the current loaded configuration array
     * @return bool The value from the configuration file, or the default value if the key was not found
     */
    public function get($section, $key, $default = null, $cfg_arr = null) {
        return $this->getValue($section, $key, $default, $cfg_arr);
    }

    /**
     * Get a value from a loaded configuration file
     * @param string $section The section in the configuration file
     * @param string $key The key in the configuration file
     * @param mixed $default The default value returned if the key was not found
     * @param array $cfg_arr [optional] The configuration array to get the value from,
     * null to use the current loaded configuration array
     * @return bool The value from the configuration file, or the default value if the key was not found
     */
    public function getValue($section, $key, $default = null, $cfg_arr = null) {
        // Use the default configuration array if the param equals to null
        if($cfg_arr === null)
            $cfg_arr = $this->cfg_arr;

        // The configuration array may not be null and must be an array
        if($cfg_arr === null || !is_array($cfg_arr))
            return false;

        // Make sure the section and the key are booth available
        if(!$this->hasSection($section, $cfg_arr) || !$this->hasKey($section, $key, $cfg_arr))
            return $default;

        // Return the value from the config
        return $cfg_arr[$section][$key];
    }

    /**
     * Get a boolean from a loaded configuration file
     * @param string $section The section in the configuration file
     * @param string $key The key in the configuration file
     * @param bool $default The default value returned if the key was not found
     * @param array $cfg_arr [optional] The configuration array to get the value from,
     * null to use the current loaded configuration array
     * @return bool The boolean from the configuration file, or the default boolean value
     */
    public function getBool($section, $key, $default = false, $cfg_arr = null) {
        return (bool) $this->getValue($section, $key, $default, $cfg_arr);
    }
    
    /**
     * Check if a configuration array section exists
     * @param string $section The section to search for
     * @param array $cfg_arr [optional] The configuration array to search in,
     * null to use the current loaded configuration array
     * @return bool True if the section was found
     */
    public function hasSection($section, $cfg_arr = null) {
        // Use the default configuration array if the param equals to null
        if($cfg_arr == null)
            $cfg_arr = $this->cfg_arr;

        // The configuration array may not be null and must be an array
        if($cfg_arr == null || !is_array($cfg_arr))
            return false;
        
        // Check if the config array contains this section
        return array_key_exists($section, $cfg_arr);
    }
    
    /**
     * Check if the configuration file has a specified key
     * @param string $section The section to search in
     * @param string $key The key to search for
     * @param array $cfg_arr [optional] The configuration array to check in,
     * null to use the current loaded configuration file array
     * @return bool True if the configuration file has this key
     */
    public function hasKey($section, $key, $cfg_arr = null) {
        // Use the default configuration array if the param equals to null
        if($cfg_arr == null)
            $cfg_arr = $this->cfg_arr;

        // The configuration array may not be null and must be an array
        if($cfg_arr == null || !is_array($cfg_arr))
            return false;
        
        // Check if the config array contains this section
        if(!$this->hasSection($section, $cfg_arr))
            return false;
        
        // Check if this config array contains this key
        return array_key_exists($key, $cfg_arr[$section]);
    }

    /**
     * Check whether a configuration file array contains all the required items
     * @param string $cfg_arr The configuration file array to check in
     * @param bool $throw_exception
     * @throws CarbonConfigException Throws exception when the configuration array is invalid and $throw_exception == true
     * @return bool True if the configuration file array was valid, false otherwise
     */
    public function isValidConfig($cfg_arr, $throw_exception = false) {
        // Make sure the param is an array
        if(!is_array($cfg_arr))
            return false;

        // Make sure the array contains any items
        if(sizeof($cfg_arr) <= 0)
            return false;

        // Loop through each required key and section and make sure it exists
        foreach(self::$CONFIG_REQUIRED_KEYS as $section => $keys) {
            // Make sure the current section exists
            if(!$this->hasSection($section, $cfg_arr)) {
                // If set, throw an exception
                if($throw_exception)
                    throw new CarbonConfigException(
                        'Missing section in the configuration file: \'' . $section . '\'',
                        0, null,
                        'Add the  \'' . $section . '\' section to the configuration file.');
                return false;
            }

            // Make sure all the keys exist inside the current section
            foreach(self::$CONFIG_REQUIRED_KEYS[$section] as $key) {
                if(!$this->hasKey($section, $key, $cfg_arr)) {
                    // If set, throw an exception
                    if($throw_exception)
                        throw new CarbonConfigException(
                            'Missing key in the configuration file: \'' . $section . '.' . $key . '\'',
                            0, null,
                            'Add the  \'' . $section . '.' . $key . '\' key to the configuration file.');
                    return false;
                }
            }
        }

        // Configuration file is valid, return true
        return true;
    }
}