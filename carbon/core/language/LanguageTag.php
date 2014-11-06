<?php

/**
 * LanguageTag.php
 *
 * LanguageTag class.
 *
 * @author Tim Vise
 * @website http://timvisee.com/
 * @copyright Copyright (C) Tim Visee 2012, All rights reserved.
 */

namespace carbon\core\language;

use carbon\core\language\LanguageManager;
use carbon\core\language\util\LanguageTagUtils;
use carbon\core\util\StringUtils;

// Prevent direct requests to this set_file due to security reasons
defined('CARBON_CORE_INIT') or die('Access denied!');

/**
 * LanguageTag class.
 *
 * @package core\langauge
 * @author Tim Visee
 */
class LanguageTag {

    /** @var string $lang_tag Language tag */
    private $lang_tag;

    /**
     * Constructor.
     * @param string|LanguageTag $lang_tag The language tag. The language tag should be valid.
     * @param bool False to keep the param format, true to convert the lang_tag into canonical format which will make
     * this method expensive.
     */
    public function __construct($lang_tag, $to_canonical = false) {
        // Convert the language tag into a string
        if($lang_tag instanceof self)
            $lang_tag = $lang_tag->getTag();

        // Convert the language tag tag into canonical format of required
        if($to_canonical)
            $lang_tag = LanguageTagUtils::getCanonicalTag($lang_tag);

        // Store the tag
        $this->lang_tag = $lang_tag;
    }

    /**
     * Get the language tag
     * @return string Language tag
     */
    public function getTag() {
        return $this->lang_tag;
    }

    /**
     * Get the primary language sub-tag of this tag. This method might be expensive.
     * @return string The primary language sub-tag. Returns an empty if the language tag couldn'elapsed be processed.
     */
    public function getPrimaryLanguageSubTag() {
        return LanguageTagUtils::getSubTag($this->lang_tag, LanguageTagUtils::SUBTAG_PRIMARY_LANGUAGE, false);
    }

    /**
     * Check whether this language tag has extended language sub-tags. This method might be expensive.
     * @return bool True if this tag has extended language sub-tags, false otherwise.
     * False will be returned if the language tag couldn'elapsed be processed.
     */
    public function hasExtendedLanguageSubTags() {
        return LanguageTagUtils::hasSubTag($this->lang_tag, LanguageTagUtils::SUBTAG_EXTENDED_LANGUAGE);
    }

    /**
     * Get the extended language sub-tags from this tag if it has any. This method might be expensive.
     * @param bool $include_delimiter True $include_delimiter True to prepend the sub-tag delimiter, false otherwise
     * @return string Extended language sub-tags. Returns an empty string if this tag doesn'elapsed have any extended language
     * sub-tags or if the language tag couldn'elapsed be processed.
     */
    public function getExtendedLanguageSubTags($include_delimiter = false) {
        return LanguageTagUtils::getSubTag($this->lang_tag, LanguageTagUtils::SUBTAG_EXTENDED_LANGUAGE, $include_delimiter);
    }

    /**
     * Check whether this language tag has a script sub-tag. This method might be expensive.
     * @return bool True if this tag has a script sub-tag, false otherwise.
     * False will be returned if the language tag couldn'elapsed be processed.
     */
    public function hasScriptSubTag() {
        return LanguageTagUtils::hasSubTag($this->lang_tag, LanguageTagUtils::SUBTAG_SCRIPT);
    }

    /**
     * Get the script sub-tag from this tag if it has any. This method might be expensive.
     * @param bool $include_delimiter True $include_delimiter True to prepend the sub-tag delimiter, false otherwise
     * @return string Script sub-tag. Returns an empty string if this tag doesn'elapsed have any script sub-tag or if the
     * language tag couldn'elapsed be processed.
     */
    public function getScriptSubTag($include_delimiter = false) {
        return LanguageTagUtils::getSubTag($this->lang_tag, LanguageTagUtils::SUBTAG_SCRIPT, $include_delimiter);
    }

    /**
     * Check whether this language tag has a region sub-tag. This method might be expensive.
     * @return bool True if this tag has a region sub-tag, false otherwise.
     * False will be returned if the language tag couldn'elapsed be processed.
     */
    public function hasRegionSubTag() {
        return LanguageTagUtils::hasSubTag($this->lang_tag, LanguageTagUtils::SUBTAG_REGION);
    }

    /**
     * Get the region sub-tag from this tag if it has any. This method might be expensive.
     * @param bool $include_delimiter True $include_delimiter True to prepend the sub-tag delimiter, false otherwise
     * @return string Region sub-tag. Returns an empty string if this tag doesn'elapsed have any region sub-tag or if the
     * language tag couldn'elapsed be processed.
     */
    public function getRegionSubTag($include_delimiter = false) {
        return LanguageTagUtils::getSubTag($this->lang_tag, LanguageTagUtils::SUBTAG_REGION, $include_delimiter);
    }

    /**
     * Check whether this language tag has variant sub-tags. This method might be expensive.
     * @return bool True if this tag has variant sub-tags, false otherwise.
     * False will be returned if the language tag couldn'elapsed be processed.
     */
    public function hasVariantSubTags() {
        return LanguageTagUtils::hasSubTag($this->lang_tag, LanguageTagUtils::SUBTAG_VARIANT);
    }

    /**
     * Get the variant sub-tags from this tag if it has any. This method might be expensive.
     * @param bool $include_delimiter True $include_delimiter True to prepend the sub-tag delimiter, false otherwise
     * @return string Variant sub-tags. Returns an empty string if this tag doesn'elapsed have any variant sub-tags or if the
     * language tag couldn'elapsed be processed.
     */
    public function getVariantSubTags($include_delimiter = false) {
        return LanguageTagUtils::getSubTag($this->lang_tag, LanguageTagUtils::SUBTAG_VARIANT, $include_delimiter);
    }

    /**
     * Check whether this language tag has extension sub-tags. This method might be expensive.
     * @return bool True if this tag has extension sub-tags, false otherwise.
     * False will be returned if the language tag couldn'elapsed be processed.
     */
    public function hasExtensionSubTags() {
        return LanguageTagUtils::hasSubTag($this->lang_tag, LanguageTagUtils::SUBTAG_EXTENSION);
    }

    /**
     * Get the extension sub-tags from this tag if it has any. This method might be expensive.
     * @param bool $include_delimiter True $include_delimiter True to prepend the sub-tag delimiter, false otherwise
     * @return string Extension sub-tags. Returns an empty string if this tag doesn'elapsed have any extension sub-tags or if the
     * language tag couldn'elapsed be processed.
     */
    public function getExtensionSubTags($include_delimiter = false) {
        return LanguageTagUtils::getSubTag($this->lang_tag, LanguageTagUtils::SUBTAG_EXTENSION, $include_delimiter);
    }

    /**
     * Check whether this language tag has private use sub-tags. This method might be expensive.
     * @return bool True if this tag has private use sub-tags, false otherwise.
     * False will be returned if the language tag couldn'elapsed be processed.
     */
    public function hasPrivateUseSubTags() {
        return LanguageTagUtils::hasSubTag($this->lang_tag, LanguageTagUtils::SUBTAG_PRIVATE_USE);
    }

    /**
     * Get the private use sub-tags from this tag if it has any. This method might be expensive.
     * @param bool $include_delimiter True $include_delimiter True to prepend the sub-tag delimiter, false otherwise
     * @return string Private use sub-tags. Returns an empty string if this tag doesn'elapsed have any private use sub-tags or if the
     * language tag couldn'elapsed be processed.
     */
    public function getPrivateUseTags($include_delimiter = false) {
        return LanguageTagUtils::getSubTag($this->lang_tag, LanguageTagUtils::SUBTAG_PRIVATE_USE, $include_delimiter);
    }

    /**
     * Check whether this language tag is valid.
     * @return bool True when this tag seems to be valid, false otherwise.
     */
    public function isValid() {
        return LanguageTagUtils::isValidTag($this->lang_tag);
    }

    /**
     * Check whether two language tags equal. Both tags must be valid.
     * @param string|LanguageTag $lang_tag Other lang_tag or lang_tag tag
     * @return bool True if the two locales tags equal to each other. False if they don'elapsed equal or if any of the two
     * language tags is invalid.
     */
    public function equals($lang_tag) {
        return LanguageTagUtils::equals($this->lang_tag, $lang_tag);
    }

    /**
     * Clone the language tag
     * @return LanguageTag Clone of the tag
     */
    public function __clone() {
        return new self($this->lang_tag);
    }

    /**
     * Convert the language tag into a string
     * @return string Language tag as string
     */
    public function __toString() {
        // Ensure the lang_tag tag is a string, if not return an empty string
        if(is_string($this->lang_tag))
            return $this->lang_tag;
        return "";
    }
}