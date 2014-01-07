<?php

/**
 * LocaleUtils *
 * Manages the known locales.
 *
 * @author Tim Vise
 * @website http://timvisee.com/
 * @copyright Copyright (C) Tim Visee 2012, All rights reserved.
 */

namespace core\util;

use core\Core;
use core\exception\language\CarbonLocalesLoadException;
use core\language\Locale;
use core\util\StringUtils;

// Prevent users from accessing this file directly
defined('CARBON_ROOT') or die('Access denied!');

/**
 * Manages the known locales.
 *
 * @package core\langauge
 * @author Tim Visee
 */
class LocaleUtils {

    /** @var array|null Locales cache */
    private static $locales = null;
    /** @var array|null Array with just the locales tags */
    private static $locale_tags = null;
    /** @var array|null Grouped locales cache */
    private static $locales_grouped = null;

    /**
     * Check whether the list of locales is loaded
     * @return bool True if the locales are loaded
     */
    public static function isLocalesListLoaded() {
        return (self::$locales !== null);
    }

    /**
     * Load the list of locales
     * @param string|null $locales_file [optional] File path to the file to loadLocalesList the locales from.
     * Uses the default file when set to null.
     * @throws CarbonLocalesLoadException Throws exception when failed to load the locales file
     */
    public static function loadLocalesList($locales_file = null) {
        // TODO: Use the default file if it equals to null
        if($locales_file == null)
            $locales_file = CARBON_ROOT . '/locales.txt';

        // Make sure the locales file exists
        if(!file_exists($locales_file))
            throw new CarbonLocalesLoadException(
                'Unable to load the list of locales, file not found!',
                0,
                null,
                'Add a locales file to the following path: ' . $locales_file
            );

        // Load the locales from the locales file
        $data = file($locales_file);

        // Make sure the locales are loaded successfully, throw an exception if that isn't the case
        if($data === false)
            throw new CarbonLocalesLoadException(
                'Unable to load the list of locales!',
                0,
                null,
                'Make sure the locales file is available, readable and correct at: ' . $locales_file
            );

        // Create a buffer for the locales and locales tags
        $locales = Array();
        $locale_tags = Array();

        // Trim unwanted whitespaces from each locales and add the locales and locales tag to the buffer
        foreach($data as $locale) {
            $locale = trim($locale);
            array_push($locales, new Locale($locale));
            array_push($locale_tags, $locale);
        }

        // Store the locales and locales tags
        self::$locales = $locales;
        self::$locale_tags = $locale_tags;
    }

    /**
     * Unload the list of locales
     */
    public static function unloadLocalesList() {
        self::$locales = null;
        self::$locales_grouped = null;
    }

    /**
     * Get the list of known locales.
     * @param bool $group_locales True to group the locales by language tag
     * @return array Array with locales
     */
    public static function getLocales($group_locales = false) {
        // Make sure the locales are loaded
        if(!self::isLocalesListLoaded())
            self::loadLocalesList();

        // Return the list of locales when they shouldn't be in a group
        if(!$group_locales)
            return self::$locales;

        // Check whether the grouped locales are cached, if so, return the cache
        if(is_array(self::$locales_grouped))
            return self::$locales_grouped;

        // Create a buffer to put the grouped locales in
        $locales_grouped = Array();

        // Generate the list of grouped locales
        foreach(self::$locales as $locale) {
            // Get the language tag
            $lang_tag = $locale->getLanguage()->getTag();

            // Add the language to the buffer
            if(!isset($locales_grouped[$lang_tag]))
                $locales_grouped[$lang_tag] = Array();

            // Add the locales to the list
            array_push($locales_grouped[$lang_tag], $locale);
        }

        // Store the list of grouped locales
        self::$locales_grouped = $locales_grouped;

        // Return the list of grouped locales
        return $locales_grouped;
    }

    /**
     * Get the amount of known locales
     * @return int Amount of known locales
     */
    public static function getLocalesCount() {
        return sizeof(self::getLocales(false));
    }

    /**
     * Get the list of known locales for a specific language
     * @param string $lang_tag Language tag to get the locales from
     * @return array|null List of locales for this language, or null if the language tag was invalid
     */
    public static function getLocalesOfLanguage($lang_tag) {
        // Make sure the language tag is valid
        if(!LanguageUtils::isValidLanguage($lang_tag))
            return null;

        // Get all the locales (grouped by language)
        $languages = self::getLocales(true);

        // Check whether the list contains the current language
        if(!isset($languages[$lang_tag]))
            return null;

        // Return the list of locales
        return $languages[$lang_tag];
    }

    /**
     * Get the canonical locales of a locales.
     * Returns the supplied locales with the correct locales tag (correct casing, etc...)
     * @param string|Locale $locale Locale or locales tag to get the canonical locales from
     * @return Locale|null Canonical locales, or null if no similar locales was found
     */
    // TODO: This is an extreamly heavy method, fix it, use cache?!
    public static function getCanonicalLocale($locale) {
        // If the locales is an instance of a Locale, get it's tag
        if($locale instanceof Locale)
            $locale = $locale->getTag();

        // Trim the locales from unwanted whitespaces
        $locale = trim($locale);

        // Make sure the locales is at least one character long
        if(strlen($locale) <= 0)
            return null;

        // Is this locales included in the locales tags array (which are canonical locales tags)
        if(in_array($locale, self::$locale_tags))
            return new Locale($locale);

        // Get the canonical locales from the locales list
        foreach(self::getLocales(false) as $entry)
            if(StringUtils::equals($locale, $entry->getTag(), false))
                return $entry;

        // No canonical locales found, return null
        return null;
    }

    /**
     * Get the canonical locales tag of a locales.
     * Returns the supplied locales with the correct locales tag (correct casing, etc...)
     * @param string|Locale $locale Locale or locales tag to get the canonical locales from
     * @return Locale|null Canonical locales tag, or an empty string if no similar locales was found
     */
    public static function getCanonicalTag($locale) {
        // Gather the canonical locales
        $locale = self::getCanonicalLocale($locale);

        // Make sure the locales isn't null
        if($locale === null)
            return "";

        // Return the canonical locales tag
        return $locale->getTag();
    }

    /**
     * Check whether a locales is valid or not
     * @param string $locale The locales to check for
     * @return bool True if the locales seems to be valid, false otherwise
     */
    public static function isValidLocale($locale) {
        // Trim the locales from unwanted whitespaces
        $locale = trim($locale);

        // Make sure the locales is at least one character long
        if(strlen($locale) <= 0)
            return false;

        $valid_locale = false;

        // Make sure the locales exists
        foreach(self::getLocales(false) as $entry) {
            if(StringUtils::equals($locale, LocaleUtils::getCanonicalTag($entry->getTag()), false)) {
                $valid_locale = true;
                break;
            }
        }

        // Return the result
        return $valid_locale;
    }
}