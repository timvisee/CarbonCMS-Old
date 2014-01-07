<?php

/**
 * LocaleFile.php
 *
 * language file class.
 *
 * @author Tim Vise
 * @website http://timvisee.com/
 * @copyright Copyright (C) Tim Visee 2012, All rights reserved.
 */

namespace core\locale;

use core\exception\language\CarbonLanguageFileLoadException;
use core\exception\language\CarbonLocaleException;
use core\language\Locale;
use core\util\LocaleUtils;
use core\util\StringUtils;

// Prevent users from accessing this file directly
defined('CARBON_ROOT') or die('Access denied!');

/**
 * language file class.
 *
 * @package core\langauge
 * @author Tim Visee
 */
class LocaleFile {

    /** @var string $file File path to the language file */
    private $file;
    /** @var Locale $locale Locale of this language file */
    private $locale;
    /** @var array|null $data Locale data */
    private $data = null;
    /** @var string $scope language file scope */
    private $scope = "";
    /** @var array|null $props language file properties */
    private $props = null;
    /** @var array $scope_cache Cache used to remember whether this file is included in a scope */
    private $scope_cache = Array();

    /**
     * Constructor
     * @param string $lang_file Path of the language file to load
     * @param Locale $lang Locale of the file
     * @param bool $throw_exceptions True to throw exceptions if failed to load the language file
     * @throws CarbonLanguageFileLoadException Throws an exception if failed to load the language file,
     * $throw_exceptions has to be true.
     */
    public function __construct($lang_file, $lang, $throw_exceptions = true) {
        // Set the locale of the file
        $this->locale = $lang;

        // Load the language file
        $this->loadFile($lang_file, $throw_exceptions);
    }

    /**
     * Load a language file
     * @param string $lang_file Path of the language file to load
     * @param bool $throw_exceptions True to throw exceptions when failed loading the file.
     * @return bool True if the file was being loaded successfully. False otherwise if no exception was thrown.
     * @throws CarbonLanguageFileLoadException Throws an exception if failed to load the language file,
     * $throw_exceptions has to be true.
     */
    private function loadFile($lang_file, $throw_exceptions = true) {
        // Make sure the file exists
        if(!$this->fileExists()) {
            if($throw_exceptions)
                throw new CarbonLanguageFileLoadException(
                    'Unable to load language file, file doesn\'t exist: ' . $lang_file,
                    0,
                    null,
                    'Make sure this language file is available: ' . $lang_file
                );
            return false;
        }

        // Load the language file
        $content = parse_ini_file($lang_file, true);

        // Make sure loading the configuration file succeed
        if($content === false || !is_array($content)) {
            if($throw_exceptions)
                throw new CarbonLanguageFileLoadException(
                    'Unable to load language file:' . $lang_file,
                    0,
                    null,
                    'Make sure this language file is available, readable and correct: ' . $lang_file
                );
            return false;
        }

        // Make sure the array contains the two required sections
        if(!array_key_exists('properties', $content)) {
            if($throw_exceptions)
                throw new CarbonLanguageFileLoadException(
                    'Unable to load language file, missing section:' . $lang_file,
                    0,
                    null,
                    'Add a \'properties\' section to this language file:'  . $lang_file
                );
            return false;
        }
        if(!array_key_exists('locale', $content)) {
            if($throw_exceptions)
                throw new CarbonLanguageFileLoadException(
                    'Unable to load language file, missing section:' . $lang_file,
                    0,
                    null,
                    'Add a \'locale\' section to this language file:'  . $lang_file
                );
            return false;
        }

        // Gather and store the language file properties
        $this->props = $content['properties'];

        // Gather and set the scope of the language file, if set
        if($this->hasProperty('scope'))
            $this->setScope($this->getProperty('scope', ''));

        // Reset the locale of this file to the file's default
        $this->resetLocale();

        // Gather the locale data
        $locale = $content['locale'];

        // Lowercase all the locale string keys
        $locale = array_change_key_case($locale);

        // Store the locale data
        $this->data = $locale;

        // TODO: Make sure the file is loaded successfully!

        // language file seems to be loaded correctly, return true
        return true;
    }

    /**
     * Check whether the language file is loaded or not
     * @return bool True when the language file is loaded
     */
    public function isLoaded() {
        return is_array($this->data);
    }

    /**
     * Return the path of the loaded language file
     * @return string Path of the loaded language file
     */
    public function getFile() {
        return $this->file;
    }

    /**
     * Check whether the language file exists or not
     * @return bool True when the langauge file exists
     */
    public function fileExists() {
        return (file_exists($this->file));
    }

    /**
     * Get the locale of this language file
     * @return Locale Locale of this language file
     */
    public function getLocale() {
        return $this->locale;
    }

    /**
     * Check whether this language file is for a specific locale
     * @param Locale $locale Locale to check for
     * @return bool True whether this language file is for this locale
     */
    public function isLocale($locale) {
        // Make sure the locale param isn't null
        if($locale === null)
            return false;

        // Make sure the locale is valid
        if($locale instanceof Locale)
            if(!$locale->isValid())
                return false;

        // If the locale param is a string, convert it to a locale
        if(is_string($locale)) {
            // Make sure the locale is valid
            if(!LocaleUtils::isValidLocale($locale))
                return false;

            // Convert the locale tag to a locale
            $locale = new Locale($locale);
        }

        // Compare the tags of the locale and return the result
        return $this->getLocale()->equals($locale);
    }

    /**
     * Set the locale of this language file. Doesn't update the language file itself.
     * @param Locale|string $locale Locale, or locale tag of this language file
     * @param bool $throw_exceptions True to throw an exception when the locale is invalid
     * @return bool True when the locale was changed, false if the locale was unknown or invalid
     * @throws CarbonLocaleException Throws an exception when the locale is invalid and
     * when $throw_exceptions is set to true
     */
    public function setLocale($locale, $throw_exceptions = true) {
        // Make sure the locale param isn't null
        if($locale === null) {
            if($throw_exceptions)
                throw new CarbonLocaleException(
                    'Unable to change file locale, invalid locale (null)',
                    0,
                    null
                );
            return false;
        }

        // Make sure the locale is valid
        if($locale instanceof Locale) {
            if(!$locale->isValid()) {
                if($throw_exceptions)
                    throw new CarbonLocaleException(
                        'Unable to change file locale, invalid locale (Tag: ' . $locale->getTag() . ')',
                        0,
                        null
                    );
                return false;
            }
        }

        // If the locale param is a string, convert it to a locale
        if(is_string($locale)) {
            // Make sure the locale is valid
            if(!LocaleUtils::isValidLocale($locale)) {
                if($throw_exceptions)
                    throw new CarbonLocaleException(
                        'Unable to change file locale, invalid locale (Tag: ' . $locale . ')',
                        0,
                        null
                    );
                return false;
            }

            // Convert the locale tag to a locale
            $locale = new Locale($locale);
        }

        // Update the locale of the file and return true
        $this->locale = $locale;
        return true;
    }

    /**
     * Reset the locale to the file's default,
     * won't change the current locale if no locale was specified in the language file itself.
     * @return Locale New locale, returns the old locale if the locale didn't reset
     */
    public function resetLocale() {
        // Check whether the file contains a locale or language property, if so, reset the locale
        if($this->hasProperty(Array('locale', 'lang', 'language')))
            $this->setLocale($this->getProperty(Array('locale', 'lang', 'language'), ''));

        // Return the new locale
        return $this->getLocale();
    }

    /**
     * Get a property from this language file
     * @param string $key Key or array of keys of the property to get
     * @param mixed $default [optional] Default value, returned when none of the keys exist
     * @return mixed Key value of the first key occurrence, or the default value when none of the keys exist
     */
    public function getProperty($key, $default = null) {
        // Make sure the key param isn't null
        if($key === null)
            return $default;

        // Make sure the key param is an array, if not convert the key value to an array
        if(!is_array($key))
            $key = Array($key);

        // Try to get the propert for each of the keys
        foreach($key as $entry) {
            // Make sure this property is set
            if(!$this->hasProperty($entry))
                continue;

            // Return the property
            return $this->props[$entry];
        }

        // None of the keys exist, return the default
        return $default;
    }

    /**
     * Check whether the language file has a specific property
     * @param string|array $key Key or array of keys of the properties to check for
     * @return bool True when all properties exist, false otherwise
     */
    public function hasProperty($key) {
        // Make sure the key value is an array, if not, convert the value to an array
        if(!is_array($key))
            $key = Array($key);

        // Check for each key if it exists
        foreach($key as $entry) {
            // Trim the key from unwanted whitespaces
            $entry = trim($entry);

            // Make sure this key exists
            if(!array_key_exists($entry, $this->props))
                return false;
        }

        // All keys seem to be available, return true
        return true;
    }

    /**
     * Check whether this language file has any properties
     * @return bool
     */
    public function hasProperties() {
        // Make sure the properties value is an array
        if(!is_array($this->props))
            return false;

        // Make sure any property was set
        return (sizeof($this->props) > 0);
    }

    /**
     * Check whether this language file has a scope set
     * @return bool True when this language file has a scope set
     */
    public function hasScope() {
        return (sizeof(trim($this->scope)) > 0);
    }

    /**
     * Get the scope of this language file, returns an empty string if no scope was set
     * @return string language file scope
     */
    public function getScope() {
        return $this->scope;
    }

    /**
     * Check whether this language file is inside a scope
     * @param string|array $scope Scope or array of scopes to check for
     * @return bool True when this language file is included in any of the scopes
     */
    public function isInScope($scope) {
        // TODO: Use cache for this method

        // Make sure the param scope isn't null
        if($scope === null)
            return false;

        // Make sure the param is an array, if not, convert it to an array
        if(!is_array($scope))
            $scope = Array($scope);

        // Check for each scope, if this language file is inside it
        foreach($scope as $entry) {
            // Trim the scope from unwanted whitespaces
            $entry = trim($entry);

            // Remove comma's from the scope
            $entry = str_replace(',', '', $entry);

            // Check whether this scope is an empty string, if so, this file is included in this scope
            if(sizeof($entry) <= 0)
                return true;

            // Check whether the scope of the file is an empty string, if so,
            // the file is not included in the current entry scope
            if(sizeof($this->scope) <= 0)
                continue;

            // Check if this entry scope was cached
            if(isset($this->scope_cache[$entry])) {
                if($this->scope_cache[$entry] === true)
                    return true;
                continue;
            }

            // Explode the entry and the file scope
            $entry_parts = explode('.', $entry);
            $file_scope_parts = explode('.', $this->scope);

            // Make sure the entry scope doesn't have more parts
            if(sizeof($entry_parts) > sizeof($file_scope_parts))
                continue;

            // Check for each part if the file scope is included in the entry scope
            for($i = 0; $i < sizeof($file_scope_parts); $i++) {
                $entry_part = $entry_parts[$i];
                $file_scope_part = $file_scope_parts[$i];

                // Make sure the entry part equals to the file scope part (case insensitive),
                // if not, continue to the next scope
                if(!StringUtils::equals($entry_part, $file_scope_part, false, true)) {
                    // Cache this entry
                    $this->scope_cache[$entry] = false;

                    // Continue to the next entry
                    continue 2;
                }
            }

            // Cache this entry
            $this->scope_cache[$entry] = true;

            // The file scope does seem to be included in this entry scope, return true
            return true;
        }

        // The language file doesn't seem to be included in any of the scopes, return false
        return false;
    }

    /**
     * Set the scope, doesn't update the scope in the language file itself
     * @param string $scope language file scope
     */
    private function setScope($scope) {
        // Trim the file scope from unwanted whitespaces
        $scope = trim($scope);

        // Remove all the comma's from the scope
        $scope = str_replace(',', '', $scope);

        // Store the scope and reset the scope cache
        $this->scope = $scope;
        $this->scope_cache = Array();
    }

    /**
     * Check whether the language file contains a specific locale string
     * @param string $key Key of the locale string to check for
     * @return bool True when the locale string exists, false otherwise
     */
    public function has($key) {
        // Make sure the key isn't null
        if($key === null)
            return false;

        // Make sure the language file is loaded
        if(!$this->isLoaded())
            return false;

        // Trim and lowercase the key from unwanted whitespaces
        $key = strtolower(trim($key));

        // Check whether this locale string exists, return the result
        return array_key_exists($key, $this->data);
    }

    /**
     * Get a locale string
     * @param string $key Key of the locale string to get
     * @param string|null $default [optional] Default value to return when this key doesn't exist.
     * Null to return the key.
     * @return string Locale string, or the default value if this locale string wasn't found
     */
    public function get($key, $default = null) {
        // Parse the default value
        if($default === null)
            $default = $key;

        // Make sure this locale string exists
        if(!$this->has($key))
            return $default;

        // Trim and lowercase the key from unwanted whitespaces
        $key = strtolower(trim($key));

        // Gather and return the locale string
        return $this->data[$key];
    }

    /**
     * Check whether any authors are set for this language file. Uses the 'author' or 'authors' parameter.
     * @return bool True if any authors are set for this language file, false otherwise
     */
    public function hasAuthors() {
        return $this->hasProperty(Array('author', 'authors'));
    }

    /**
     * Get a list of authors of this language file. Uses the 'author' or 'authors' parameter.
     * @return array List of authors of this file, an empty array is returned if no author was set
     */
    public function getAuthors() {
        // Create an authors buffer
        $authors_val = "";

        // Get the authors
        if($this->hasProperty(Array('author', 'authors')))
            $authors_val = $this->getproperty(Array('author', 'authors'), '');

        // Trim the authors string form unwanted whitespaces
        $authors_val = trim($authors_val);

        // Make sure the authors string is not just an empty string
        if(sizeof($authors_val) <= 0)
            return Array();

        // Explode the authors string by a comma
        $authors = explode(',', $authors_val);

        // Trim each author from unwanted whitespaces, remove invalid/blank authors
        foreach($authors as &$author) {
            $author = trim($author);

            // Make sure this author is not just an empty string, if not, remove this author
            if(sizeof($author) <= 0)
                unset($author);
        }

        // Return the list of authors
        return $authors;
    }

    /**
     * Check whether any version number was set for this language file. Uses the 'ver' or 'version' parameter.
     * @return bool True if any version number was set for this language file, false otherwise
     */
    public function hasVersion() {
        return $this->hasProperty(Array('ver', 'version'));
    }

    /**
     * Get the version number of this language file. Uses the 'ver' or 'version' parameter.
     * @return string|null Version number of the language file, null if no version number was set.
     */
    public function getVersion() {
        // Create a version buffer
        $ver = "";

        // Get the version from the properties
        if($this->hasProperty(Array('ver', 'version')))
            $ver = $this->getproperty(Array('ver', 'version'), '');

        // Trim the version string form unwanted whitespaces
        $ver = trim($ver);

        // Make sure the version string is not just an empty string
        if(sizeof($ver) <= 0)
            return null;

        // Return the version number
        return $ver;
    }

    /**
     * Clear the local cache
     */
    public function clearCache() {
        $this->scope_cache = Array();
    }
}