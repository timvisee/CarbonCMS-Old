<?php

/**
 * Locale.php
 *
 * Locale class.
 *
 * @author Tim Vise
 * @website http://timvisee.com/
 * @copyright Copyright (C) Tim Visee 2012, All rights reserved.
 */

namespace core\language;

use core\exception\language\CarbonUnknownLocaleException;
use core\util\LocaleUtils;
use core\util\StringUtils;

// Prevent users from accessing this file directly
defined('CARBON_ROOT') or die('Access denied!');

/**
 * Locale class.
 *
 * @package core\langauge
 * @author Tim Visee
 */
class Locale {

    /** @var string $locale Locale tag */
    private $locale;

    /**
     * Constructor
     * @param string|Locale $locale Locale or locales tag.
     * The canonical locales tag will be used if a Locale instance is supplied
     * @throws CarbonUnknownLocaleException Throws exceptoin when an invalid locales is supplied
     */
    public function __construct($locale) {
        // Get the locales tag if the locales param is an instance of Locale
        if($locale instanceof self)
            $locale = LocaleUtils::getCanonicalTag($locale->getTag());

        // Make sure the locales param isn't null or an empty string
        if(empty($locale)) {
            throw new CarbonUnknownLocaleException(
                'Invalid locales supplied while constructing Locale class.',
                0,
                null
            );
        }

        // Store the language
        $this->locale = trim($locale);
    }

    /**
     * Get the locales tag
     * @return string Locale tag
     */
    public function getTag() {
        return $this->locale;
    }

    /**
     * Get the language of this locales
     * @return Language|null Language of the locales, null when an invalid locales was supplied
     */
    public function getLanguage() {
        // Check whether the locales contains a underscore
        if(!StringUtils::contains($this->locale, '_'))
            return new Language($this->locale);

        // Split the locales into it's parts
        $locale_parts = explode('_', $this->locale, 2);

        // Return the language
        return new Language($locale_parts[0]);
    }

    /**
     * Get the territory tag of this locales
     * @return string|null Locale territory tag, null if the territory wasn't supplied or if the locales was invalid
     */
    public function getTerritoryTag() {// Make sure the locales contains an underscore
        if(!StringUtils::contains($this->locale, '_'))
            return null;

        // Split the locales into it's parts
        $locale_parts = explode('_', $this->locale, 2);

        // TODO: Make sure locales codesets and modifiers aren't implemented in the territory tag

        // Return the territory tag
        return $locale_parts[1];
    }

    /**
     * Check whether the locales is valid
     * @return bool True when the locales seems to be valid, false otherwise
     */
    public function isValid() {
        return LocaleUtils::isValidLocale($this->locale);
    }

    /**
     * Compare two locales
     * @param string|Locale $locale Other locale or locale tag
     * @return bool True if the two locales are equal to each other, false otherwise
     */
    public function equals($locale) {
        // Convert the param locale to a string if it's an instance of Locale
        if($locale instanceof self)
            $locale = $locale->getTag();

        // Make sure the locale param is a string
        if(!is_string($locale))
            return false;

        // Compare the two locales, return the result
        StringUtils::equals($locale, $this->getTag(), false, true);
    }
}