<?php

// TODO: Improve the performance of the autoloader where possible!

// TODO: Feature that allows the registration of custom methods to load classes, for a more dynamic auto loader.

/**
 * Autoloader.php
 * The Autoloader class which takes care of all non-loaded classes and tries to loadLocalesList them when being used.
 *
 * @author Tim Visee
 * @website http://timvisee.com/
 * @copyright Copyright © Tim Visee 2012-2014. All rights reserved.
 */

namespace carbon\core;

use carbon\core\log\Logger;
use carbon\core\util\ArrayUtils;
use carbon\core\util\StringUtils;

// Prevent direct requests to this file due to security reasons
defined('CARBON_CORE_INIT') or die('Access denied!');

/**
 * Autoloader class
 *
 * @package carbon\core
 * @author Tim Visee
 */
class Autoloader {

    // TODO: Add support for interfaces!

    /**
     * @var string $root Root directory to load all classes from by default.
     * Is set to the Carbon Core root directory by default.
     */
    private static $root = CARBON_CORE_ROOT;
    /**
     * @var bool Defines whether the autoloader has been initialized.
     * True if the autoloader has been initialized, false otherwise.
     */
    private static $init = false;
    /** @var array $regClasses Array containing all registered classes */
    private static $regClasses = Array();
    /** @var array $regNamespaces Array containing all registered namespaces */
    private static $regNamespaces = Array();
    /** @var array $regExtensions Array containing all registered extensions */
    private static $regExtensions = Array('.php');

    // TODO: Should we use more general naming, since interfaces being loaded aren't classes?

    /**
     * Initialize the Autoloader, the autoloader may not be initialized yet.
     *
     * @param string|null $root Root path, to load all classes from by default. Null to use the default.
     *
     * @return bool True if succeed, false otherwise.
     * True will also be returned if the autoloader was initialized before.
     */
    public static function initialize($root = null) {
        // Make sure the autoloader hasn't been initialized yet
        if(self::isInitialized())
            return true;

        // Register the auto loader method
        // TODO: Check whether this method should throw errors or not, probably yes before the error handler will take care of everything.
        if(spl_autoload_register(__CLASS__ . '::loadClass', false, true) === false)
            return false;

        // Set the root path
        // TODO: Use File instances, if possible!
        if($root !== null)
            self::setRoot($root);

        // Make sure the .php extension is registered by default
        self::registerExtension('.php', true);

        // Set the initialization state, return the result
        self::$init = true;
        return true;
    }

    /**
     * Check whether the autoloader has been initialized.
     *
     * @return bool True if the autoloader has been initialized, false otherwise.
     */
    public static function isInitialized() {
        return self::$init;
    }

    /**
     * Finalize the autoloader, the autoloader must be initialized.
     *
     * @param bool $keepConfig True to keep the current root directory, classes, namespaces and extensions
     * configuration, false to clear this configuration.
     *
     * @return bool True on success, false on failure.
     */
    public static function finalize($keepConfig = false) {
        // Make sure the autoloader is initialized
        if(!self::isInitialized())
            return true;

        // Unregister the autoloader function
        if(spl_autoload_unregister(__CLASS__ . '::loadClass') === false)
            return false;

        // Check whether we should clear the current configuration
        if(!$keepConfig) {
            self::$root = CARBON_CORE_ROOT;
            self::$regClasses = Array();
            self::$regNamespaces = Array();
            self::$regExtensions = Array();
        }

        // Set the initialization state, return the result
        self::$init = false;
        return true;
    }

    /**
     * Called by spl_autoload when a class needs to be auto loaded
     *
     * @param string $className Name of the class to auto loadLocalesList (with namespace)
     *
     * @return bool True on success, false on failure.
     */
    public static function loadClass($className) {
        // TODO: Log this message using the Logger class, instead of a simple echo!
        echo '[AutoLoader] Loading: ' . $className . '<br />';

        // TODO: Is this test required?
        // Check whether the class is already loaded
        if(self::isClassLoaded($className))
            return true;

        // Try to loadLocalesList the class if it's registered
        self::loadRegisteredClass($className);

        // Check whether the class is loaded successfully, if so, return true
        if(self::isClassLoaded($className))
            return true;

        // Try to loadLocalesList the class from a registered namespace
        self::loadFromRegisteredNamespace($className);

        // Check whether the class is loaded successfully, if so, return true
        if(self::isClassLoaded($className))
            return true;

        // Try to loadLocalesList the file with every registered extension
        foreach(self::$regExtensions as $ext) {
            // Get the probable file path to the file of the class that needs to be loaded
            $classFile = self::getRoot() . DIRECTORY_SEPARATOR . $className . $ext;

            // Make sure this file exists
            if(!file_exists($classFile))
                continue;

            // TODO: Fix warning bellow
            // Load the class
            /** @noinspection PhpIncludeInspection */
            require($classFile);

            // Check whether the class is loaded successfully, if so, return true
            if(self::isClassLoaded($className))
                return true;
        }

        // Class could not be loaded, show error message
        // TODO: Show proper error message
        // TODO: Possibility to disable error msg'statements so other class loaders could be called too
        // die('Class \'' . $class_name . '\' could not be loaded!');
        return false;
    }

    /**
     * Get the root path, the path to load all classes from by default.
     *
     * @return string Root path
     */
    public static function getRoot() {
        return self::$root;
    }

    /**
     * Set the root path to load all classes from by default.
     *
     * @param string|null $root Root path, or null to reset the root path to it's default.
     * The Carbon Core path will be used as default root path.
     *
     * @return bool True if succeed, false if the root path was invalid.
     */
    public static function setRoot($root = null) {
        // Reset the root directory if the argument equals null
        if($root == null)
            $root = CARBON_CORE_ROOT;

        // Make sure the $root parameter is a string
        // TODO: Allow directory and file instances!
        if(!is_string($root))
            return false;

        // TODO: Make sure the root path is valid

        // Set the root path, return true
        self::$root = $root;
        return true;
    }

    /**
     * Check whether a class is loaded
     *
     * @param string $className Name of the class to check for (with namespace)
     *
     * @return bool True if this class is loaded, false otherwise
     */
    public static function isClassLoaded($className) {
        // Check whether the class is loaded
        if(class_exists($className, false))
            return true;

        // TODO: Temporary code until interface support has been added!
        return interface_exists($className, false);
    }

    /**
     * Register a new extension to the auto loader
     *
     * @param string|Array $extensions The extension, or array with extensions to register.
     * The parameter extensions must start with period.
     * @param bool $prepend True to prepend the extension to the list, instead of appending it (default: false)
     *
     * @return int Amount of extensions that where successfully added.
     * Extensions that where invalid, or that already existed won't count.
     */
    // TODO: Make sure this method works!
    public static function registerExtension($extensions, $prepend = false) {
        // TODO: Show debug messages

        // Convert the $extensions param to an array
        if(!is_array($extensions))
            $extensions = Array($extensions);

        // Keep track of the success count
        $successCount = 0;

        // Add each extension from the array
        foreach($extensions as &$ext) {
            // Trim the extension from unwanted white spaces and commas
            $ext = trim($ext, ", \t\n\r\0\x0B");

            // Make sure the extension isn't an empty string
            if(strlen($ext) <= 0)
                continue;

            // Make sure the string doesn't contain any unwanted slashes or spaces
            if(StringUtils::contains($ext, array('/', '\\', ' ')))
                continue;

            // Make sure the extension starts with a period, if not, prepend a period to the extension
            if(!StringUtils::startsWith($ext, '.'))
                $ext = '.' . $ext;

            // Make sure this extension isn't registered already
            if(self::isExtensionRegistered($ext))
                continue;

            // Append or prepend the extension to the list
            if(!$prepend)
                array_push(self::$regExtensions, $ext);
            else
                array_unshift(self::$regExtensions, $ext);

            // Increase the success count and continue to the next item
            $successCount++;
        }

        // Return the count of successfully registered extensions
        return $successCount;
    }

    /**
     * Get a list of registered extensions
     *
     * @param bool $asArray True to return the list of extensions as an array,
     * false to return the list as a string separated with commas (default: true)
     * @param bool $stripPeriods True to strip the periods from the extensions (default: false)
     *
     * @return array|string Array with extensions, or string with extensions separated with commas,
     * may return an empty array or string if no extension is registered
     */
    public static function getRegisteredExtensions($asArray = true, $stripPeriods = false) {
        // TODO: Show debug messages

        // Check whether the extensions should be returned as array or as string
        if($asArray || $stripPeriods) {
            // Get a copy of the array with registered extensions
            $extensions = ArrayUtils::copyArray(self::$regExtensions);

            // Remove white spaces and commas from all the extension items
            foreach($extensions as &$ext)
                $ext = trim($ext, ", \t\n\r\0\x0B");

            // Strip the periods if required
            if($stripPeriods)
                foreach($extensions as &$ext)
                    $ext = ltrim($ext, '.');

            // If the list should be returned as a string, implode the array and return the result
            if(!$asArray)
                return implode(',', $extensions);

            // Return the array of extensions
            return $extensions;

        } else {
            // Implode the array, and return the result
            return implode(',', self::$regExtensions);
        }
    }

    /**
     * Check whether an extension is registered or not
     *
     * @param string $extension Extension to check for, required prepended period
     * @param bool $caseSensitive True to make the extension case sensitive, false otherwise (default: true)
     *
     * @return bool True if the extension is registered, false otherwise.
     * May return false if the extension was invalid.
     */
    public static function isExtensionRegistered($extension, $caseSensitive = true) {
        // TODO: Show debug messages

        // Trim the extension from unwanted white spaces and commas
        $extension = trim($extension, ", \t\n\r\0\x0B");

        // Make sure the extension isn't an empty string
        if(strlen($extension) <= 0)
            return false;

        // Get a copy of the array with the registered extensions
        $extensions = ArrayUtils::copyArray(self::$regExtensions);

        // Check whether the current extension entry equals to the argument extension
        foreach($extensions as &$ext) {
            // Strip all unwanted white spaces and commas from the item
            $ext = trim($ext, ", \t\n\r\0\x0B");

            // Check whether the argument $extension equals to the $ext item, if so, return true
            if(StringUtils::equals($ext, $extension, $caseSensitive, false))
                return true;
        }

        // No match found, return false
        return false;
    }

    /**
     * Unregister a registered extension.
     *
     * @param string|array $extensions Extension or array with extensions to unregister.
     * Extensions must start with a period.
     *
     * @return int Amount of removed/unregistered extensions.
     */
    public static function unregisterExtension($extensions) {
        // TODO: Show debug messages

        // Make sure any extension is registered
        if(sizeof(self::$regExtensions))
            return 0;

        // Make sure any extension was supplied as argument
        if(is_array($extensions)) {
            if(sizeof($extensions) <= 0)
                return 0;
        } else
            if(strlen(trim($extensions)) <= 0)
                return 0;

        // If the $extension argument isn't an array already, convert it to an array
        if(!is_array($extensions))
            $extensions = Array($extensions);

        // Remove all unwanted characters from the extensions array, make sure each extension starts with a period
        foreach($extensions as &$extension) {
            // Trim the extension from unwanted white spaces and commas
            $extension = trim($extension, ", \t\n\r\0\x0B");

            // Make sure the extension starts with a period
            if(!StringUtils::startsWith($extension, '.'))
                $extension = '.' . $extension;
        }

        // Remove all invalid extensions
        for($i = 0; $i < sizeof($extensions); $i++) {
            // Get the current extension
            $ext = $extensions[$i];

            // Check whether the current item is blank or not
            if(strlen(trim($ext)) <= 0) {
                // Remove the current item
                unset($extensions[$i]);

                // Decrease the index counter, so nothing is skipped
                $i--;
            }

            // Make sure the extension doesn't contain any unwanted slashes or spaces
            if(StringUtils::contains($ext, array('/', '\\', ' '))) {
                // Remove the current item
                unset($extensions[$i]);

                // Decrease the index counter, so nothing is skipped
                $i--;
            }
        }

        // Keep track of the removed count
        $removedCount = 0;

        // Loop through each registered extension to check if it should be removed or not
        for($i = 0; $i < sizeof(self::$regExtensions); $i++) {
            // Loop through each argument extension, to check if it equals to the current extension item
            foreach($extensions as &$extension) {
                // Check whether the extension entry equals to the current extension
                if(StringUtils::equals(self::$regExtensions[$i], $extension, false, false)) {
                    // Remove the extension
                    unset(self::$regExtensions[$i]);

                    // Decrease the index counter so none of the items is skipped,
                    // increase the removed count and continue to the next item
                    $i--;
                    $removedCount++;
                    break;
                }
            }
        }

        // Return the count of removed extensions
        return $removedCount;
    }

    /**
     * Register a class with a file path so the proper file will be loaded when the class is being used.
     * This will overwrite any classes which where registered before with an identical class name.
     *
     * @param string $className Name of the class (with namespace)
     * @param string $classFile The file to loadLocalesList the class from
     */
    public static function registerClass($className, $classFile) {
        self::$regClasses[trim($className)] = trim($classFile);
    }

    /**
     * Check whether a class is registered in the Autoloader
     *
     * @param string $className Name of the class to check for (with namespace)
     *
     * @return bool True if this class is registered, false otherwise
     */
    public static function isClassRegistered($className) {
        return array_key_exists(trim($className), self::$regClasses);
    }

    /**
     * Try to loadLocalesList a registered class name
     *
     * @param string $className Name of the class to loadLocalesList (with namespace)
     *
     * @return bool True if succeed, false otherwise
     */
    public static function loadRegisteredClass($className) {
        // TODO: Show debug messages
        // Make sure the class name is registered
        if(!self::isClassRegistered($className))
            return false;

        // Get the class file
        $classFile = self::$regClasses[trim($className)];

        // Make sure the class file exists
        if(!file_exists($classFile))
            return false;

        // Load the class
        /** @noinspection PhpIncludeInspection */
        include($classFile);

        // Succesfully loaded, return true
        return true;
    }

    /**
     * Unregister a registered class
     *
     * @param string $className Name of the class to unregister (with namespace)
     *
     * @return bool True if any class was unregistered, false otherwise
     */
    public static function unregisterClass($className) {
        // TODO: Show debug messages
        // Make sure any class with this name is registered
        if(!self::isClassRegistered($className))
            return false;

        // Unregister the class
        unset(self::$regClasses[trim($className)]);

        // Class unregistered, return true
        return true;
    }

    /**
     * Register a namespace with a directory path so the proper file will be loaded when a class is being loaded from a
     * registered namespace.
     * This will overwrite any classes which where registered before with an identical class name.
     *
     * @param string $namespaceName Name of the namespace
     * @param string $namespaceDir The directory to loadLocalesList the classes from
     */
    public static function registerNamespace($namespaceName, $namespaceDir) {
        // TODO: Show debug messages
        // Trim the namespace name from unwanted characters
        $namespaceName = rtrim(trim($namespaceName), '\\');

        // Trim the namespace directory from unwanted characters
        $namespaceDir = rtrim(trim($namespaceDir), "/\\");

        // Register the namespace name
        self::$regNamespaces[$namespaceName] = $namespaceDir;
    }

    /**
     * Check whether a namespace is registered in the Autoloader
     *
     * @param string $namespaceName Name of the namespace to check for
     *
     * @return bool True if this namespace is registered, false otherwise
     */
    public static function isNamespaceRegistered($namespaceName) {
        // TODO: Show debug messages
        // Trim the namespace name from unwanted characters
        $namespaceName = rtrim(trim($namespaceName), '\\');

        // Check whether this namespace name exists, return the result
        return array_key_exists($namespaceName, self::$regNamespaces);
    }

    /**
     * Try to loadLocalesList a class from any registered namespace
     *
     * @param string $className Name of the class to loadLocalesList (with namespace)
     *
     * @return bool True if succeed, false otherwise
     */
    public static function loadFromRegisteredNamespace($className) {
        // TODO: Show debug messages
        // Check whether the class is already loaded
        if(self::isClassLoaded($className))
            return true;

        // Trim the class name
        $className = trim($className);

        // Loop through each registered namespace and check whether this class is inside this namespace
        foreach(self::$regNamespaces as $namespace => $namespace_dir) {
            // Make sure the class name is longer than the current namespace name
            if(strlen($namespace) + 1 > strlen($className))
                continue;

            // Check whether the class name has the current namespace
            // TODO: Run a canonical check!
            // TODO: Namespaces aren't case sensitive!
            // TODO: Use the ClassUtils class for namespacing and stuff?
            if(substr($className, 0, strlen($namespace) + 1) !== $namespace . '\\')
                continue;

            // Strip the namespace from the class name
            $classNameStripped = substr($className, strlen($namespace) + 1);

            // Try to loadLocalesList the file with every specified extension
            foreach(self::$regExtensions as $ext) {
                // Get the probable class file
                $classFile = $namespace_dir . DIRECTORY_SEPARATOR . $classNameStripped . $ext;

                // Make sure this class file exists
                if(!file_exists($classFile))
                    continue;

                // Load the class file
                /** @noinspection PhpIncludeInspection */
                include($classFile);

                // Check whether the class is successfully loaded, if so break the loop
                if(self::isClassLoaded($className))
                    return true;
            }
        }

        // Class couldn't be loaded, return false
        return false;
    }

    /**
     * Unregister a registered namespace
     *
     * @param string $namespaceName Name of the namespace to unregister
     *
     * @return bool True if any namespace was unregistered, false otherwise
     */
    public static function unregisterNamespace($namespaceName) {
        // TODO: Show debug messages
        // Make sure any namespace with this name is registered
        if(!self::isNamespaceRegistered($namespaceName))
            return false;

        // Unregister the namespace
        unset(self::$regNamespaces[trim($namespaceName)]);

        // Namespace unregistered, return true
        return true;
    }
}