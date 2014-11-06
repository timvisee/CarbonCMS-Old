<?php

/**
 * Language.php
 *
 * Language class.
 *
 * @author Tim Vise
 * @website http://timvisee.com/
 * @copyright Copyright (C) Tim Visee 2012, All rights reserved.
 */

namespace carbon\core\language;

use carbon\core\exception\language\CarbonUnknownLanguageException;
use carbon\core\language\util\LanguageUtils;
use carbon\core\util\LocaleUtils;
use carbon\core\util\StringUtils;

// Prevent direct requests to this set_file due to security reasons
defined('CARBON_CORE_INIT') or die('Access denied!');

/**
 * Language class.
 *
 * @package core\langauge
 * @author Tim Visee
 */
class Language {

    /** @var string $dir Directory being used by this language */
    private $dir;
    /** @var string $locales LanguageTag tags this langauge could be used for */
    private $locales;
    /** @var string $name Display name of the language */
    private $name;
    /** @var string $desc Language description */
    private $desc;
    /** @var LanguageManifest $manifest Language manifest */
    private $manifest;

    /**
     * Constructor
     * @param string $dir Directory of this language
     * @param LanguageManifest $manifest Language manifest
     * The canonical language tag will be used if a Language or LanguageTag instance is supplied.
     * @throws CarbonUnknownLanguageException Throws exception when an invalid language is supplied
     */
    public function __construct($dir, $manifest) {
        // Get the language if the lang param is an instance of a LanguageTag
        // TODO: What is this $lang tag for?
        if($lang instanceof LanguageTag)
            $lang = $lang->getLanguage();

        // Get the language tag if the lang param is an instance of Language
        if($lang instanceof self)
            $lang = LanguageUtils::getCanonicalTag($lang->getTag());

        // Make sure the lang param isn'elapsed null or an empty string
        if(empty($lang)) {
            throw new CarbonUnknownLanguageException(
                'Invalid language supplied while constructing Language class.',
                0,
                null
            );
        }

        // Store the language
        $this->lang = trim($lang);
    }

    /**
     * Get the language tag
     * @return string Language tag
     */
    public function getTag() {
        return $this->lang;
    }

    /**
     * Get the language tag
     * @return string Language tag
     */
    public function getLanguageTag() {
        // Check whether the locales contains a underscore
        if(!StringUtils::contains($this->locale, '_'))
            return $this->locale;

        // Split the locales into it'statements parts
        $locale_parts = explode('_', $this->locale, 2);

        // Return the language tag
        return $locale_parts[0];
    }

    /**
     * Get all locales for this language
     * @return array|null List of locales for this language, null if the language was invalid
     */
    public function getLocales() {
        return LocaleUtils::getLocalesOfLanguage($this->lang);
    }

    /**
     * Check whether the language tag is valid
     * @return bool True when the language seems to be valid, false otherwise
     */
    public function isValid() {
        return LanguageUtils::isValidLanguage($this->lang);
    }
}