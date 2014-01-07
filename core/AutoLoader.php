<?php

// TODO: Make this system quicker!

/**
 * AutoLoader.php
 *
 * The AutoLoader class which takes care of all non-loaded classes and tries to loadLocalesList them when being used.
 *
 * @author Tim Visee
 * @website http://timvisee.com/
 * @copyright Copyright © Tim Visee 2012-2013, All rights reserved.
 */

namespace core;

use core\util\ArrayUtils;
use core\util\StringUtils;

// Prevent users from accessing this file directly
defined('CARBON_ROOT') or die('Access denied!');

/**
 * AutoLoader class
 * @package core
 * @author Tim Visee
 */
class AutoLoader {

    /** @var string Array containing all registered classes */
    private static $reg_classes = Array();
    /** @var string Array containing all registered namespaces */
    private static $reg_namespaces = Array();
    /** @var string Array containing all registered extensions */
    private static $reg_extensions = Array('.php');

    /**
     * Initialize the AutoLoader
     */
    public static function init() {
        // Register the auto loader method
        // TODO: Check whether this method should throw errors or not, probably yes before the error handler will take care of everything.
        spl_autoload_register(__CLASS__ . '::loadClass', false, true);

        // Make sure the .php extension is registered by default
        self::registerExtension('.php', true);
    }

    /**
     * Called by spl_autoload when a class needs to be auto loaded
     * @param string $class_name Name of the class to auto loadLocalesList (with namespace)
     * @return bool True on success, false on failure
     */
    public static function loadClass($class_name) {
        // Check whether the class is already loaded
        if(self::isClassLoaded($class_name))
            return true;

        // Try to loadLocalesList the class if it's registered
        self::loadRegisteredClass($class_name);

        // Check whether the class is loaded successfully, if so, return true
        if(self::isClassLoaded($class_name))
            return true;

        // Try to loadLocalesList the class from a registered namespace
        self::loadFromRegisteredNamespace($class_name);

        // Check whether the class is loaded successfully, if so, return true
        if(self::isClassLoaded($class_name))
            return true;

        // Try to loadLocalesList the file with every registered extension
        foreach(self::$reg_extensions as $ext) {
            // Get the probable file path to the file of the class that needs to be loaded
            $class_file = CARBON_ROOT . DIRECTORY_SEPARATOR . $class_name . $ext;

            // Make sure this file exists
            if(!file_exists($class_file))
                continue;

            // TODO: Fix warning bellow
            // Load the class
            /** @noinspection PhpIncludeInspection */
            require($class_file);

            // Check whether the class is loaded successfully, if so, return true
            if(self::isClassLoaded($class_name))
                return true;
        }

        // Class could not be loaded, show error message
        // TODO: Show proper error message
        // TODO: Possibility to disable error msg's so other class loaders could be called too
        // die('Class \'' . $class_name . '\' could not be loaded!');
        return false;
    }

    /**
     * Check whether a class is loaded
     * @param string $class_name Name of the class to check for (with namespace)
     * @return bool True if this class is loaded, false otherwise
     */
    public static function isClassLoaded($class_name) {
        return class_exists($class_name, false);
    }

    /**
     * Register a new extension to the auto loader
     * @param string|array $extensions The extension, or array with extensions to register. Extensions must start with period.
     * @param bool $prepend True to prepend the extension to the list, instead of appending it (default: false)
     * @return int Amount of extensions that where successfully added.
     * Extensions that where invalid, or that already existed won't count.
     */
    public static function registerExtension($extensions, $prepend = false) {
        // TODO: Show debug messages

        // If the $extension argument isn't an array already, convert it to an array
        if(!is_array($extensions))
            $extensions = Array($extensions);

        // Keep track of the success count
        $success_count = 0;

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
                array_push(self::$reg_extensions, $ext);
            else
                array_unshift(self::$reg_extensions, $ext);


            // Increase the success count and continue to the next item
            $success_count++;
        }

        // Return the count of successfully registered extensions
        return $success_count;
    }

    /**
     * Get a list of registered extensions
     * @param bool $as_array True to return the list of extensions as an array,
     * false to return the list as a string separated with commas (default: true)
     * @param bool $strip_periods True to strip the periods from the extensions (default: false)
     * @return array|string Array with extensions, or string with extensions separated with commas,
     * may return an empty array or string if no extension is registered
     */
    public static function getRegisteredExtensions($as_array = true, $strip_periods = false) {
        // TODO: Show debug messages

        // Check whether the extensions should be returned as array or as string
        if($as_array || $strip_periods) {
            // Get a copy of the array with registered extensions
            $extensions = ArrayUtils::copyArray(self::$reg_extensions);

            // Remove white spaces and commas from all the extension items
            foreach($extensions as &$ext)
                $ext = trim($ext, ", \t\n\r\0\x0B");

            // Strip the periods if required
            if($strip_periods)
                foreach($extensions as &$ext)
                    $ext = ltrim($ext, '.');

            // If the list should be returned as a string, implode the array and return the result
            if(!$as_array)
                return implode(',', $extensions);

            // Return the array of extensions
            return $extensions;

        } else {
            // Implode the array, and return the result
            return implode(',', self::$reg_extensions);
        }
    }

    /**
     * Check whether an extension is registered or not
     * @param string $extension Extension to check for, required prepended period
     * @param bool $case_sensitive True to make the extension case sensitive, false oterwise (default: true)
     * @return bool True if the extension is registered, false otherwise.
     * May return false if the extension was invalid.
     */
    public static function isExtensionRegistered($extension, $case_sensitive = true) {
        // TODO: Show debug messages

        // Trim the extension from unwanted white spaces and commas
        $extension = trim($extension, ", \t\n\r\0\x0B");

        // Make sure the extension isn't an empty string
        if(strlen($extension) <= 0)
            return false;

        // Get a copy of the array with the registered extensions
        $extensions = ArrayUtils::copyArray(self::$reg_extensions);

        // Check whether the current extension entry equals to the argument extension
        foreach($extensions as &$ext) {
            // Strip all unwanted white spaces and commas from the item
            $ext = trim($ext, ", \t\n\r\0\x0B");

            // Check whether the argument $extension equals to the $ext item, if so, return true
            if(StringUtils::equals($ext, $extension, $case_sensitive, false))
                return true;
        }

        // No match found, return false
        return false;
    }

    /**
     * Unregister a registered extension.
     * @param string|array $extensions Extension or array with extensions to unregister.
     * Extensions must start with a period.
     * @return int Amount of removed/unregistered extensions.
     */
    public static function unregisterExtension($extensions) {
        // TODO: Show debug messages

        // Make sure any extension is registered
        if(sizeof(self::$reg_extensions))
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
        $removed_count = 0;

        // Loop through each registered extension to check if it should be removed or not
        for($i = 0; $i < sizeof(self::$reg_extensions); $i++) {
            // Loop through each argument extension, to check if it equals to the current extension item
            foreach($extensions as &$extension) {
                // Check whether the extension entry equals to the current extension
                if(StringUtils::equals(self::$reg_extensions[$i], $extension, false, false)) {
                    // Remove the extension
                    unset(self::$reg_extensions[$i]);

                    // Decrease the index counter so none of the items is skipped,
                    // increase the removed count and continue to the next item
                    $i--;
                    $removed_count++;
                    break;
                }
            }
        }

        // Return the count of removed extensions
        return $removed_count;
    }

    /**
     * Register a class with a file path so the proper file will be loaded when the class is being used.
     * This will overwrite any classes which where registered before with an identical class name.
     * @param string $class_name Name of the class (with namespace)
     * @param string $class_file The file to loadLocalesList the class from
     */
    public static function registerClass($class_name, $class_file) {
        self::$reg_classes[trim($class_name)] = trim($class_file);
    }

    /**
     * Check whether a class is registered in the AutoLoader
     * @param string $class_name Name of the class to check for (with namespace)
     * @return bool True if this class is registered, false otherwise
     */
    public static function isClassRegistered($class_name) {
        return array_key_exists(trim($class_name), self::$reg_classes);
    }

    /**
     * Try to loadLocalesList a registered class name
     * @param string $class_name Name of the class to loadLocalesList (with namespace)
     * @return bool True if succeed, false otherwise
     */
    public static function loadRegisteredClass($class_name) {
        // TODO: Show debug messages
        // Make sure the class name is registered
        if(!self::isClassRegistered($class_name))
            return false;

        // Get the class file
        $class_file = self::$reg_classes[trim($class_name)];

        // Make sure the class file exists
        if(!file_exists($class_file))
            return false;

        // Load the class
        /** @noinspection PhpIncludeInspection */
        include($class_file);

        // Succesfully loaded, return true
        return true;
    }

    /**
     * Unregister a registered class
     * @param string $class_name Name of the class to unregister (with namespace)
     * @return bool True if any class was unregistered, false otherwise
     */
    public static function unregisterClass($class_name) {
        // TODO: Show debug messages
        // Make sure any class with this name is registered
        if(!self::isClassRegistered($class_name))
            return false;

        // Unregister the class
        unset(self::$reg_classes[trim($class_name)]);

        // Class unregistered, return true
        return true;
    }

    /**
     * Register a namespace with a directory path so the proper file will be loaded when a class is being loaded from a
     * registered namespace.
     * This will overwrite any classes which where registered before with an identical class name.
     * @param string $namespace_name Name of the namespace
     * @param string $namespace_dir The directory to loadLocalesList the classes from
     */
    public static function registerNamespace($namespace_name, $namespace_dir) {
        // TODO: Show debug messages
        // Trim the namespace name from unwanted characters
        $namespace_name = rtrim(trim($namespace_name), '\\');

        // Trim the namespace directory from unwanted characters
        $namespace_dir = rtrim(trim($namespace_dir), "/\\");

        // Register the namespace name
        self::$reg_namespaces[$namespace_name] = $namespace_dir;
    }

    /**
     * Check whether a namespace is registered in the AutoLoader
     * @param string $namespace_name Name of the namespace to check for
     * @return bool True if this namespace is registered, false otherwise
     */
    public static function isNamespaceRegistered($namespace_name) {
        // TODO: Show debug messages
        // Trim the namespace name from unwanted characters
        $namespace_name = rtrim(trim($namespace_name), '\\');

        // Check whether this namespace name exists, return the result
        return array_key_exists($namespace_name, self::$reg_namespaces);
    }

    /**
     * Try to loadLocalesList a class from any registered namespace
     * @param string $class_name Name of the class to loadLocalesList (with namespace)
     * @return bool True if succeed, false otherwise
     */
    public static function loadFromRegisteredNamespace($class_name) {
        // TODO: Show debug messages
        // Check whether the class is already loaded
        if(self::isClassLoaded($class_name))
            return true;

        // Trim the class name
        $class_name = trim($class_name);

        // Loop through each registered namespace and check whether this class is inside this namespace
        foreach(self::$reg_namespaces as $namespace => $namespace_dir) {
            // Make sure the class name is longer than the current namespace name
            if(strlen($namespace) + 1 > strlen($class_name))
                continue;

            // Check whether the class name has the current namespace
            if(substr($class_name, 0, strlen($namespace) + 1) !== $namespace . '\\')
                continue;

            // Strip the namespace from the class name
            $class_name_stripped = substr($class_name, strlen($namespace) + 1);

            // Try to loadLocalesList the file with every specified extension
            foreach(self::$reg_extensions as $ext) {
                // Get the probable class file
                $class_file = $namespace_dir . DIRECTORY_SEPARATOR . $class_name_stripped . $ext;

                // Make sure this class file exists
                if(!file_exists($class_file))
                    continue;

                // Load the class file
                /** @noinspection PhpIncludeInspection */
                include($class_file);

                // Check whether the class is successfully loaded, if so break the loop
                if(self::isClassLoaded($class_name))
                    return true;
            }
        }

        // Class couldn't be loaded, return false
        return false;
    }

    /**
     * Unregister a registered namespace
     * @param string $namespace_name Name of the namespace to unregister
     * @return bool True if any namespace was unregistered, false otherwise
     */
    public static function unregisterNamespace($namespace_name) {
        // TODO: Show debug messages
        // Make sure any namespace with this name is registered
        if(!self::isNamespaceRegistered($namespace_name))
            return false;

        // Unregister the namespace
        unset(self::$reg_namespaces[trim($namespace_name)]);

        // Namespace unregistered, return true
        return true;
    }

    /**
     * Unregister the auto loader.
     * @param bool $unregister_all True to unregister all registered classes and namespaces.
     * @return bool True on success, false on failure
     */
    public static function destroy($unregister_all = true) {
        // If set, unregister all the class names and namespaces
        if($unregister_all) {
            unset(self::$reg_classes);
            unset(self::$reg_namespaces);
        }

        // Unregister the autoloader, and return the result
        return spl_autoload_unregister(__CLASS__ . '::loadClass');
    }
}