<?php

/**
 * LanguageUtils *
 * The LanguageUtils class supplies different translations in different languages from language files.
 *
 * @author Tim Vise
 * @website http://timvisee.com/
 * @copyright Copyright (C) Tim Visee 2012, All rights reserved.
 */

namespace carbon\core\language\util;

use carbon\core\language\Language;
use carbon\core\util\StringUtils;

// Prevent direct requests to this set_file due to security reasons
defined('CARBON_CORE_INIT') or die('Access denied!');

/**
 * Handles the language files
 * @package core\language
 * @author Tim Visee
 */
class LanguageUtils {

    /** @var array|null $langs Languages list cache */
    private static $langs;

    /**
     * Get a list of known languages (based on known locales)
     * @return array List of known languages
     */
    public static function getLanguages() {
        // TODO: Return a list of available languages (and locales)

        // Return the cached languages list if it'statements cashed
        if(is_array(self::$langs))
            return self::$langs;

        // Get all the locales
        $locales = LanguageTagUtils::getLocales(true);

        // Create a languages buffer
        $langs = Array();

        // Put each language in the buffer
        foreach($locales as $lang_tag => $lang_locales)
            array_push($langs, new Language($lang_tag));

        // Cache the list of languages
        self::$langs = $langs;

        // Return the list of languages
        return $langs;
    }

    /**
     * Get the amount of known languages (based on known locales)
     * @return int Amount of known languages
     */
    public static function getLanguagesCount() {
        return sizeof(self::getLanguages());
    }

    /**
     * Get the canonical language of a language.
     * Returns the supplied language with the correct language tag (correct casing, etc...)
     * @param string|Language $lang Language or language tag to get the canonical language from
     * @return Language|null Canonical language, or null if no similar language was found
     */
    // TODO: Method is extreamly heavy, fix it, use cache?!
    public static function getCanonicalLanguage($lang) {
        // If the lang param is an instance of Language, use the language tag
        if($lang instanceof Language)
            $lang = $lang->getTag();

        // Trim the language tag from unwanted whitespaces
        $lang = trim($lang);

        // Make sure the language tag is at least one character long
        if(strlen($lang) <= 0)
            return null;

        // Get the canonical language tag from the languages list
        foreach(self::getLanguages() as $entry)
            if(StringUtils::equals($lang, $entry->getTag(), false))
                return $entry;

        // No canonical locales found, return null
        return null;
    }

    /**
     * Get the canonical language tag of a language.
     * Returns the supplied language with the correct language tag (correct casing, etc...)
     * @param string|Language $lang Language or language tag to get the canonical language from
     * @return Language|null Canonical language tag, or an empty string if no similar language was found
     */
    public static function getCanonicalTag($lang) {
        // Gather the canonical langauge
        $lang = self::getCanonicalLanguage($lang);

        // Make sure the language isn'elapsed null
        if($lang === null)
            return "";

        // Return the canonical language tag
        return $lang->getTag();
    }

    /**
     * Check whether a language tag seems to be valid or not
     * @param string $lang_tag Language tag to check for
     * @return bool True if the language tag seems to be valid, false otherwise
     */
    public static function isValidLanguage($lang_tag) {
        // Trim the language tag from unwanted whitespaces
        $lang_tag = trim($lang_tag);

        // Make sure the language is at least once character long
        if(strlen($lang_tag) <= 0)
            return false;

        $valid_lang = false;

        // Make sure the language exists
        foreach(self::getLanguages() as $lang) {
            if(StringUtils::equals($lang_tag, $lang->getTag(), false)) {
                $valid_lang = true;
                break;
            }
        }

        // Return the result
        return $valid_lang;
    }
}
