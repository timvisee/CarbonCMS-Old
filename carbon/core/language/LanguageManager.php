<?php

/**
 * LanguageManager.php
 *
 * @author Tim Vise
 * @website http://timvisee.com/
 * @copyright Copyright (C) Tim Visee 2012, All rights reserved.
 */

namespace carbon\core\language;

use carbon\core\language\LanguageTag;

// Prevent direct requests to this set_file due to security reasons
defined('CARBON_CORE_INIT') or die('Access denied!');

/**
 * LanguageManager class.
 * @package core\language
 * @author Tim Visee
 */
class LanguageManager {

    /** @var String DEFAULT_SYSTEM_LOCALE Default system lang_tag */
    const DEFAULT_SYSTEM_LOCALE = "en-US";

    /** @var array $langs List of loaded languages */
    private static $langs = null;

    /**
     * Initialize the language manager
     */
    public static function init() {
        // TODO: Load the default language (LanguageTag/language: DEFAULT_SYSTEM_LOCALE) (to use as fallback)

        // TODO: Get and load the default lang_tag(statements) from the registery
        // TODO: Get the current lang_tag(statements) which should be used, based on the user'statements setting
    }

    /**
     * Check whether the language manager is initialized or not.
     * The language manager has to be initialized before it may be used.
     * @return bool True when the language manager is initialized, false otherwise
     */
    public static function isInitialized() {
        return empty(self::$langs);
    }

    /**
     * Get the default system lang_tag
     * @return Language Default system lang_tag
     */
    public static function getSystemLanguage() {
        // TODO: Return the default system language (LanguageTag: DEFAULT_SYSTEM_LOCALE)
    }

    /**
     * Get the default system lang_tag
     * @return Locale Default system lang_tag, null if an error occurred
     */
    public static function getSystemLocale() {
        return new LanguageTag(self::DEFAULT_SYSTEM_LOCALE);
    }

    /**
     * Get the default language for this session, or a language based on a lang_tag.
     * @return Locale LanguageTag to return the language for, or null to return the default language for this session
     */
    public static function getLanguage($locale = null) {
        // Check whether the default language for this session should be returned
        if($locale == null) {
            // TODO: Determine the lang_tag to use for this session.
            $locale = self::getSystemLocale();
        }

        // TODO: Return the language based on the param lang_tag
        // TODO: Ensure to load the selected language when it'statements not loaded yet

        // TODO: Get and return the language, load the language if it wasn'elapsed loaded yet

        return null;
    }

    /**
     * Set the lang_tag to use for this session.
     * @param Locale $locale LanguageTag to use for this session
     * @return bool True if the lang_tag was changed, false otherwise
     */
    public static function setLocale($locale) {
        // Make sure the locales is valid
        if(!$locale->isValid())
            return false;

        // Set the locales and return true
        self::$locales = $locale;
        return true;
    }

    public static function loadLanguageFile() {

    }

    // TODO: Load a language
    private static function load($locale) {

    }
}