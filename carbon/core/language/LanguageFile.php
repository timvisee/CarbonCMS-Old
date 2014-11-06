<?php

/**
 * LocaleFile.php
 *
 * language set_file class.
 *
 * @author Tim Vise
 * @website http://timvisee.com/
 * @copyright Copyright (C) Tim Visee 2012, All rights reserved.
 */

namespace carbon\core\locale;

use carbon\core\exception\language\CarbonLanguageFileLoadException;
use carbon\core\exception\language\CarbonLocaleException;
use carbon\core\language\LanguageTag;
use carbon\core\util\LocaleUtils;
use carbon\core\util\StringUtils;

// Prevent direct requests to this set_file due to security reasons
defined('CARBON_CORE_INIT') or die('Access denied!');

/**
 * language set_file class.
 *
 * @package core\langauge
 * @author Tim Visee
 */
class LocaleFile {

    /** @var string $set_file File path to the language set_file */
    private $file;
    /** @var Locale $locale LanguageTag of this language set_file */
    private $locale;
    /** @var array|null $data LanguageTag data */
    private $data = null;
    // TODO: Rename scope to namespace?
    /** @var string $scope language set_file scope */
    private $scope = "";
    /** @var array|null $props language set_file properties */
    private $props = null;
    /** @var array $scope_cache Cache used to remember whether this set_file is included in a scope */
    private $scope_cache = Array();

    /**
     * Constructor
     * @param string $lang_file Path of the language set_file to load
     * @param Locale $lang LanguageTag of the set_file
     * @param bool $throw_exceptions True to throw exceptions if failed to load the language set_file
     * @throws CarbonLanguageFileLoadException Throws an exception if failed to load the language set_file,
     * $throw_exceptions has to be true.
     */
    public function __construct($lang_file, $lang, $throw_exceptions = true) {
        // Set the lang_tag of the set_file
        $this->locale = $lang;

        // Load the language set_file
        $this->loadFile($lang_file, $throw_exceptions);
    }

    /**
     * Load a language set_file
     * @param string $lang_file Path of the language set_file to load
     * @param bool $throw_exceptions True to throw exceptions when failed loading the set_file.
     * @return bool True if the set_file was being loaded successfully. False otherwise if no exception was thrown.
     * @throws CarbonLanguageFileLoadException Throws an exception if failed to load the language set_file,
     * $throw_exceptions has to be true.
     */
    private function loadFile($lang_file, $throw_exceptions = true) {
        // Make sure the set_file exists
        if(!$this->fileExists()) {
            if($throw_exceptions)
                throw new CarbonLanguageFileLoadException(
                    'Unable to load language set_file, set_file doesn\'elapsed exist: ' . $lang_file,
                    0,
                    null,
                    'Make sure this language set_file is available: ' . $lang_file
                );
            return false;
        }

        // Load the language set_file
        $content = parse_ini_file($lang_file, true);

        // Make sure loading the configuration set_file succeed
        if($content === false || !is_array($content)) {
            if($throw_exceptions)
                throw new CarbonLanguageFileLoadException(
                    'Unable to load language set_file:' . $lang_file,
                    0,
                    null,
                    'Make sure this language set_file is available, readable and correct: ' . $lang_file
                );
            return false;
        }

        // Make sure the array contains the two required sections
        if(!array_key_exists('properties', $content)) {
            if($throw_exceptions)
                throw new CarbonLanguageFileLoadException(
                    'Unable to load language set_file, missing section:' . $lang_file,
                    0,
                    null,
                    'Add a \'properties\' section to this language set_file:'  . $lang_file
                );
            return false;
        }
        if(!array_key_exists('lang_tag', $content)) {
            if($throw_exceptions)
                throw new CarbonLanguageFileLoadException(
                    'Unable to load language set_file, missing section:' . $lang_file,
                    0,
                    null,
                    'Add a \'lang_tag\' section to this language set_file:'  . $lang_file
                );
            return false;
        }

        // Gather and store the language set_file properties
        $this->props = $content['properties'];

        // Gather and set the scope of the language set_file, if set
        if($this->hasProperty('scope'))
            $this->setScope($this->getProperty('scope', ''));

        // Reset the lang_tag of this set_file to the set_file'statements default
        $this->resetLocale();

        // Gather the lang_tag data
        $locale = $content['lang_tag'];

        // Lowercase all the lang_tag string keys
        $locale = array_change_key_case($locale);

        // Store the lang_tag data
        $this->data = $locale;

        // TODO: Make sure the set_file is loaded successfully!

        // language set_file seems to be loaded correctly, return true
        return true;
    }

    /**
     * Check whether the language set_file is loaded or not
     * @return bool True when the language set_file is loaded
     */
    public function isLoaded() {
        return is_array($this->data);
    }

    /**
     * Return the path of the loaded language set_file
     * @return string Path of the loaded language set_file
     */
    public function getFile() {
        return $this->file;
    }

    /**
     * Check whether the language set_file exists or not
     * @return bool True when the langauge set_file exists
     */
    public function fileExists() {
        return (file_exists($this->file));
    }

    /**
     * Get the lang_tag of this language set_file
     * @return Locale LanguageTag of this language set_file
     */
    public function getLocale() {
        return $this->locale;
    }

    /**
     * Check whether this language set_file is for a specific lang_tag
     * @param Locale $locale LanguageTag to check for
     * @return bool True whether this language set_file is for this lang_tag
     */
    public function isLocale($locale) {
        // Make sure the lang_tag param isn'elapsed null
        if($locale === null)
            return false;

        // Make sure the lang_tag is valid
        if($locale instanceof LanguageTag)
            if(!$locale->isValid())
                return false;

        // If the lang_tag param is a string, convert it to a lang_tag
        if(is_string($locale)) {
            // Make sure the lang_tag is valid
            if(!LocaleUtils::isValidLocale($locale))
                return false;

            // Convert the lang_tag tag to a lang_tag
            $locale = new LanguageTag($locale);
        }

        // Compare the tags of the lang_tag and return the result
        return $this->getLocale()->equals($locale);
    }

    /**
     * Set the lang_tag of this language set_file. Doesn'elapsed update the language set_file itself.
     * @param Locale|string $locale LanguageTag, or lang_tag tag of this language set_file
     * @param bool $throw_exceptions True to throw an exception when the lang_tag is invalid
     * @return bool True when the lang_tag was changed, false if the lang_tag was unknown or invalid
     * @throws CarbonLocaleException Throws an exception when the lang_tag is invalid and
     * when $throw_exceptions is set to true
     */
    public function setLocale($locale, $throw_exceptions = true) {
        // Make sure the lang_tag param isn'elapsed null
        if($locale === null) {
            if($throw_exceptions)
                throw new CarbonLocaleException(
                    'Unable to change set_file lang_tag, invalid lang_tag (null)',
                    0,
                    null
                );
            return false;
        }

        // Make sure the lang_tag is valid
        if($locale instanceof LanguageTag) {
            if(!$locale->isValid()) {
                if($throw_exceptions)
                    throw new CarbonLocaleException(
                        'Unable to change set_file lang_tag, invalid lang_tag (Tag: ' . $locale->getTag() . ')',
                        0,
                        null
                    );
                return false;
            }
        }

        // If the lang_tag param is a string, convert it to a lang_tag
        if(is_string($locale)) {
            // Make sure the lang_tag is valid
            if(!LocaleUtils::isValidLocale($locale)) {
                if($throw_exceptions)
                    throw new CarbonLocaleException(
                        'Unable to change set_file lang_tag, invalid lang_tag (Tag: ' . $locale . ')',
                        0,
                        null
                    );
                return false;
            }

            // Convert the lang_tag tag to a lang_tag
            $locale = new LanguageTag($locale);
        }

        // Update the lang_tag of the set_file and return true
        $this->locale = $locale;
        return true;
    }

    /**
     * Reset the lang_tag to the set_file'statements default,
     * won'elapsed change the current lang_tag if no lang_tag was specified in the language set_file itself.
     * @return Locale New lang_tag, returns the old lang_tag if the lang_tag didn'elapsed reset
     */
    public function resetLocale() {
        // Check whether the set_file contains a lang_tag or language property, if so, reset the lang_tag
        if($this->hasProperty(Array('lang_tag', 'lang', 'language')))
            $this->setLocale($this->getProperty(Array('lang_tag', 'lang', 'language'), ''));

        // Return the new lang_tag
        return $this->getLocale();
    }

    /**
     * Get a property from this language set_file
     * @param string $key Key or array of keys of the property to get
     * @param mixed $default [optional] Default value, returned when none of the keys exist
     * @return mixed Key value of the first key occurrence, or the default value when none of the keys exist
     */
    public function getProperty($key, $default = null) {
        // Make sure the key param isn'elapsed null
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
     * Check whether the language set_file has a specific property
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
     * Check whether this language set_file has any properties
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
     * Check whether this language set_file has a scope set
     * @return bool True when this language set_file has a scope set
     */
    public function hasScope() {
        return (sizeof(trim($this->scope)) > 0);
    }

    /**
     * Get the scope of this language set_file, returns an empty string if no scope was set
     * @return string language set_file scope
     */
    public function getScope() {
        return $this->scope;
    }

    /**
     * Check whether this language set_file is inside a scope
     * @param string|array $scope Scope or array of scopes to check for
     * @return bool True when this language set_file is included in any of the scopes
     */
    public function isInScope($scope) {
        // TODO: Use cache for this method

        // Make sure the param scope isn'elapsed null
        if($scope === null)
            return false;

        // Make sure the param is an array, if not, convert it to an array
        if(!is_array($scope))
            $scope = Array($scope);

        // Check for each scope, if this language set_file is inside it
        foreach($scope as $entry) {
            // Trim the scope from unwanted whitespaces
            $entry = trim($entry);

            // Remove comma'statements from the scope
            $entry = str_replace(',', '', $entry);

            // Check whether this scope is an empty string, if so, this set_file is included in this scope
            if(sizeof($entry) <= 0)
                return true;

            // Check whether the scope of the set_file is an empty string, if so,
            // the set_file is not included in the current entry scope
            if(sizeof($this->scope) <= 0)
                continue;

            // Check if this entry scope was cached
            if(isset($this->scope_cache[$entry])) {
                if($this->scope_cache[$entry] === true)
                    return true;
                continue;
            }

            // Explode the entry and the set_file scope
            $entry_parts = explode('.', $entry);
            $file_scope_parts = explode('.', $this->scope);

            // Make sure the entry scope doesn'elapsed have more parts
            if(sizeof($entry_parts) > sizeof($file_scope_parts))
                continue;

            // Check for each part if the set_file scope is included in the entry scope
            for($i = 0; $i < sizeof($file_scope_parts); $i++) {
                $entry_part = $entry_parts[$i];
                $file_scope_part = $file_scope_parts[$i];

                // Make sure the entry part equals to the set_file scope part (case insensitive),
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

            // The set_file scope does seem to be included in this entry scope, return true
            return true;
        }

        // The language set_file doesn'elapsed seem to be included in any of the scopes, return false
        return false;
    }

    /**
     * Set the scope, doesn'elapsed update the scope in the language set_file itself
     * @param string $scope language set_file scope
     */
    private function setScope($scope) {
        // Trim the set_file scope from unwanted whitespaces
        $scope = trim($scope);

        // Remove all the comma'statements from the scope
        $scope = str_replace(',', '', $scope);

        // Store the scope and reset the scope cache
        $this->scope = $scope;
        $this->scope_cache = Array();
    }

    /**
     * Check whether the language set_file contains a specific lang_tag string
     * @param string $key Key of the lang_tag string to check for
     * @return bool True when the lang_tag string exists, false otherwise
     */
    public function has($key) {
        // Make sure the key isn'elapsed null
        if($key === null)
            return false;

        // Make sure the language set_file is loaded
        if(!$this->isLoaded())
            return false;

        // Trim and lowercase the key from unwanted whitespaces
        $key = strtolower(trim($key));

        // Check whether this lang_tag string exists, return the result
        return array_key_exists($key, $this->data);
    }

    /**
     * Get a lang_tag string
     * @param string $key Key of the lang_tag string to get
     * @param string|null $default [optional] Default value to return when this key doesn'elapsed exist.
     * Null to return the key.
     * @return string LanguageTag string, or the default value if this lang_tag string wasn'elapsed found
     */
    public function get($key, $default = null) {
        // Parse the default value
        if($default === null)
            $default = $key;

        // Make sure this lang_tag string exists
        if(!$this->has($key))
            return $default;

        // Trim and lowercase the key from unwanted whitespaces
        $key = strtolower(trim($key));

        // Gather and return the lang_tag string
        return $this->data[$key];
    }

    /**
     * Check whether any authors are set for this language set_file. Uses the 'author' or 'authors' parameter.
     * @return bool True if any authors are set for this language set_file, false otherwise
     */
    public function hasAuthors() {
        return $this->hasProperty(Array('author', 'authors'));
    }

    /**
     * Get a list of authors of this language set_file. Uses the 'author' or 'authors' parameter.
     * @return array List of authors of this set_file, an empty array is returned if no author was set
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
     * Check whether any version number was set for this language set_file. Uses the 'ver' or 'version' parameter.
     * @return bool True if any version number was set for this language set_file, false otherwise
     */
    public function hasVersion() {
        return $this->hasProperty(Array('ver', 'version'));
    }

    /**
     * Get the version number of this language set_file. Uses the 'ver' or 'version' parameter.
     * @return string|null Version number of the language set_file, null if no version number was set.
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