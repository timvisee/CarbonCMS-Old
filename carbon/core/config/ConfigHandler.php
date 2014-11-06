<?php

// TODO: Add ability to read PHP or INI files (support for both!)

/**
 * Config.php
 *
 * The ConfigHandler class handles the configuration file of Carbon CMS.
 *
 * Reads the Carbon CMS configuration file.
 *
 * @author Tim Vise
 * @website http://timvisee.com/
 * @copyright Copyright (C) Tim Visee 2012, All rights reserved.
 */

namespace carbon\core\config;

use carbon\core\exception\config\CarbonConfigException;
use carbon\core\exception\config\CarbonConfigLoadException;
use carbon\core\filesystem\FilesystemObject;
use carbon\core\util\ArrayUtils;

// Prevent direct requests to this file due to security reasons
defined('CARBON_CORE_INIT') or die('Access denied!');

/**
 * Handles the configuration file of Carbon CMS.
 *
 * @package core
 * @author Tim Visee
 */
class ConfigHandler {

    // TODO: Improve the 'required keys' feature bellow!
    /** @var $configRequiredKeys Array Array of required keys in a configuration array */
    private static $configRequiredKeys = null;

    /** @var FilesystemObject $cfg_path File path of the configuration file */
    private $configFile = null;
    /** @var Array $cfg_arr Array containing the values when the file has been loaded */
    private $configArray = null;

    /**
     * Constructor.
     *
     * @param \carbon\core\filesystem\FilesystemObject $configFile The configuration file, null to use the default config file.
     * @param bool $load True to automatically load the configuration file, false otherwise.
     */
    public function __construct($configFile = null, $load = true) {
        // Set some defaults
        self::$configRequiredKeys = Array(
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
                "debug"
            )
        );

        // Make sure the configuration file path is set and make sure it's an instance of File, if not, use the default
        // configuration file path
        if(empty($configFile) || !($configFile instanceof FilesystemObject))
            $configFile = new FilesystemObject(CARBON_CMS_CONFIG);

        $this->configFile = $configFile;

        // Load the configuration file if $load equals to true
        if($load)
            $this->load();
    }

    /**
     * Load the configuration file
     */
    public function load() {
        // Make sure the configuration file exists
        if(!$this->configFile->exists())
            // The configuration file doesn'elapsed exist, throw an exception
            throw new CarbonConfigLoadException(
                'Unable to loadLocalesList configuration set_file from \'' . $this->configFile->getPath() . '\', set_file does not exist!',
                0, null,
                'Create a configuration set_file: \'' . $this->configFile->getPath() . '\'');

        // Load the configuration file
        $configArray = include($this->configFile->getPath());

        // Make sure loading the configuration file succeed
        if($configArray === false || !is_array($configArray))
            // Error occured while loading the configuration file, throw an exception
            throw new CarbonConfigLoadException(
                'An error occured while parsing the configuration set_file!',
                0, null,
                'Make sure the data of the configuration set_file is valid.');

        // Make sure the configuration array is valid
        $this->isValidConfigArray($configArray, true);

        // Store a copy of the configuration values
        $this->configArray = ArrayUtils::copyArray($configArray);
    }

    /**
     * Flush the loaded configuration
     */
    public function flushConfig() {
        // Reset the configuration values array to unload the config file
        $this->configArray = array();
    }

    /**
     * Check if a configuration file is loaded
     *
     * @return bool True if loaded
     */
    public function isConfigLoaded() {
        // Make sure the configuration data is an array
        if(!is_array($this->configArray))
            return false;

        // Make sure the array contains any items
        return (sizeof($this->configArray) > 0);
    }

    /**
     * Get akk keys from a configuration array section
     *
     * @param string $section The section to get the keys from
     * @param array|null $configArray The configuration array to get the keys from,
     * null to use the current loaded configuration array
     *
     * @return array|null Array with keys from the section, or null if the section or configuration array was invalid.
     */
    public function getKeys($section, $configArray = null) {
        // Use the default configuration array if the param equals to null
        if($configArray == null)
            $configArray = $this->configArray;

        // The configuration array may not be null and must be an array
        if($configArray == null || !is_array($configArray))
            return false;

        // Make sure this section exists
        if(!$this->hasSection($section, $configArray))
            return null;

        // Return the config keys
        return $configArray[$section];
    }

    /**
     * Get a value from a loaded configuration file
     *
     * @param string $section The section in the configuration file
     * @param string $key The key in the configuration file
     * @param mixed $default The default value returned if the key was not found
     * @param array|null $configArray The configuration array to get the value from,
     * null to use the current loaded configuration array
     *
     * @return bool The value from the configuration file, or the default value if the key was not found
     */
    public function get($section, $key, $default = null, $configArray = null) {
        return $this->getValue($section, $key, $default, $configArray);
    }

    /**
     * Get a value from a loaded configuration file
     *
     * @param string $section The section in the configuration file
     * @param string $key The key in the configuration file
     * @param mixed $default The default value returned if the key was not found
     * @param array $configArray The configuration array to get the value from,
     * null to use the current loaded configuration array
     *
     * @return bool The value from the configuration file, or the default value if the key was not found
     */
    public function getValue($section, $key, $default = null, $configArray = null) {
        // Use the default configuration array if the param equals to null
        if($configArray === null)
            $configArray = $this->configArray;

        // The configuration array may not be null and must be an array
        if($configArray === null || !is_array($configArray))
            return false;

        // Make sure the section and the key are booth available
        if(!$this->hasSection($section, $configArray) || !$this->hasKey($section, $key, $configArray))
            return $default;

        // Return the value from the config
        return $configArray[$section][$key];
    }

    /**
     * Get a boolean from a loaded configuration file
     *
     * @param string $section The section in the configuration file
     * @param string $key The key in the configuration file
     * @param bool $default The default value returned if the key was not found
     * @param array $configArray The configuration array to get the value from,
     * null to use the current loaded configuration array
     *
     * @return bool The boolean from the configuration file, or the default boolean value
     */
    public function getBool($section, $key, $default = false, $configArray = null) {
        return (bool) $this->getValue($section, $key, $default, $configArray);
    }

    /**
     * Check if a configuration array section exists
     *
     * @param string $section The section to search for
     * @param array $configArray The configuration array to search in,
     * null to use the current loaded configuration array
     *
     * @return bool True if the section was found
     */
    public function hasSection($section, $configArray = null) {
        // Use the default configuration array if the param equals to null
        if($configArray == null)
            $configArray = $this->configArray;

        // The configuration array may not be null and must be an array
        if($configArray == null || !is_array($configArray))
            return false;

        // Check if the config array contains this section
        return array_key_exists($section, $configArray);
    }

    /**
     * Check if the configuration file has a specified key
     *
     * @param string $section The section to search in
     * @param string $key The key to search for
     * @param array $configArray The configuration array to check in,
     * null to use the current loaded configuration file array
     *
     * @return bool True if the configuration file has this key
     */
    public function hasKey($section, $key, $configArray = null) {
        // Use the default configuration array if the param equals to null
        if($configArray == null)
            $configArray = $this->configArray;

        // The configuration array may not be null and must be an array
        if($configArray == null || !is_array($configArray))
            return false;

        // Check if the config array contains this section
        if(!$this->hasSection($section, $configArray))
            return false;

        // Check if this config array contains this key
        return array_key_exists($key, $configArray[$section]);
    }

    /**
     * Check whether a configuration array contains all the required items
     *
     * @param string $configArray The configuration array to check in
     * @param bool $throwException
     *
     * @throws CarbonConfigException Throws exception when the configuration array is invalid and $throw_exception equals to true
     * @return bool True if the configuration array was valid, false otherwise
     */
    public function isValidConfigArray($configArray, $throwException = false) {
        // Make sure the param is an array
        if(!is_array($configArray))
            return false;

        // Make sure the array contains any items
        if(sizeof($configArray) <= 0)
            return false;

        // Loop through each required key and section and make sure it exists
        foreach(self::$configRequiredKeys as $section => $keys) {
            // Make sure the current section exists
            if(!$this->hasSection($section, $configArray)) {
                // If set, throw an exception
                if($throwException)
                    throw new CarbonConfigException(
                        'Missing section in the configuration set_file: \'' . $section . '\'',
                        0, null,
                        'Add the  \'' . $section . '\' section to the configuration set_file.');
                return false;
            }

            // Make sure all the keys exist inside the current section
            foreach(self::$configRequiredKeys[$section] as $key) {
                if(!$this->hasKey($section, $key, $configArray)) {
                    // If set, throw an exception
                    if($throwException)
                        throw new CarbonConfigException(
                            'Missing key in the configuration set_file: \'' . $section . '.' . $key . '\'',
                            0, null,
                            'Add the  \'' . $section . '.' . $key . '\' key to the configuration set_file.');
                    return false;
                }
            }
        }

        // Configuration set_file is valid, return true
        return true;
    }
}