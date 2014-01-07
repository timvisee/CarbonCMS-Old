<?php

/**
 * LanguageManager.php
 *
 * @author Tim Vise
 * @website http://timvisee.com/
 * @copyright Copyright (C) Tim Visee 2012, All rights reserved.
 */

namespace core\language;

use core\language\Locale;

// Prevent users from accessing this file directly
defined('CARBON_ROOT') or die('Access denied!');

/**
 * LanguageManager class.
 * @package core\language
 * @author Tim Visee
 */
class LanguageManager {

    /** @var Locale $sys_locale System locale */
    private static $sys_locale = null;
    /** @var Locale $locale Locale used for this session */
    private static $locale = null;

    private static $langs = Array();

    /**
     * Initialize
     */
    public static function init() {
        // TODO: Get the default locales from the registry
        // TODO: Get the current locales that should be used, based on the user's setting, or use the default one
    }

    /**
     * Check whether the language manager is initialized or not.
     * The language manager must be initialized before it may be used.
     * @return bool True when the language manager is initialized, false if otherwise
     */
    public static function isInitialized() {
        return (self::$sys_locale != null && self::$locale != null);
    }

    /**
     * Get the system locale
     * @return Locale System locale
     */
    public static function getSystemLocale() {
        return self::$sys_locale;
    }

    /**
     * Set the system locale. The locale must be valid. Changes system locale for this session only.
     * @param Locale $locale System locale
     * @return bool True if the system locale was changed, false otherwise.
     */
    public static function setSystemLocale($locale) {
        // Make sure this locales is valid
        if(!$locale->isValid())
            return false;

        // Set the system locales and return true
        self::$sys_locale = $locale;

        // Set the locale used for this session if it isn't set yet
        if(self::$locale === null)
            self::$locale = $locale;

        // Locale changed successfully, return true
        return true;
    }

    /**
     * Get the locale used for this session
     * @return Locale Locale used for this session
     */
    public static function getLocale() {
        return self::$locale;
    }

    /**
     * Set the locale to use for this session.
     * @param Locale $locale Locale to use for this session
     * @return bool True if the locale was changed, false otherwise
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
}