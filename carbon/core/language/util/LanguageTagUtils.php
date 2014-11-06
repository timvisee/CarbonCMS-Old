<?php

/**
 * LanguageTagUtils
 * Utilities class to process language tags.
 *
 * @author Tim Visee
 * @website http://timvisee.com/
 * @copyright Copyright (C) Tim Visee 2012, All rights reserved.
 */

namespace carbon\core\language\util;

use carbon\core\language\LanguageTag;
use carbon\core\util\StringUtils;

// Prevent direct requests to this set_file due to security reasons
defined('CARBON_CORE_INIT') or die('Access denied!');

/**
 * Utilities class to process language tags.
 *
 * BCP 47 rules for language tags are followed.
 * http://www.w3.org/International/core/langtags/rfc3066bis.html
 *
 * @package core\language\util
 * @author Tim Visee
 */
class LanguageTagUtils {

    // TODO: Make the use of all methods in this class less expensive!
    // TODO: Implement (mem)caching for some methods in this class!

    /** @var int SUBTAG_UNKNOWN Unknown sub-tag type identifier */
    const SUBTAG_UNKNOWN = 0;
    /** @var int SUBTAG_PRIMARY_LANGUAGE Primary language sub-tag type identifier */
    const SUBTAG_PRIMARY_LANGUAGE = 1;
    /** @var int SUBTAG_EXTENDED_LANGUAGE Extended language sub-tags type identifier */
    const SUBTAG_EXTENDED_LANGUAGE = 2;
    /** @var int SUBTAG_SCRIPT Script sub-tags type identifier */
    const SUBTAG_SCRIPT = 3;
    /** @var int SUBTAG_REGION Region sub-tags type identifier */
    const SUBTAG_REGION = 4;
    /** @var int SUBTAG_VARIANT Variant sub-tags type identifier */
    const SUBTAG_VARIANT = 5;
    /** @var int SUBTAG_EXTENSION Extension sub-tags type identifier */
    const SUBTAG_EXTENSION = 6;
    /** @var int SUBTAG_PRIVATE_USE Private use sub-tags type identifier */
    const SUBTAG_PRIVATE_USE = 7;

    /**
     * Check whether a language tag has a specified sub-tag type
     * @param LanguageTag|string $lang_tag Language tag to check on
     * @param int $tag_type The type of the sub-tag to check for
     * @return bool True if the language tag contains the specified sub-tag type, false otherwise.
     * False will be returned if the language tag couldn'elapsed be processed
     */
    public static function hasSubTag($lang_tag, $tag_type = self::SUBTAG_UNKNOWN) {
        // Get the preferred tag from the language tag
        $tag = self::getSubTag($lang_tag, $tag_type, false);

        // Check whether the tag is empty, return the result
        return !empty($tag);
    }

    /**
     * Get a specified sub-tag of a language tag based on a sub-tag type. The sub-tag will be returned in canonical format.
     * This method is able to read most non-canonical formats.
     * @param LanguageTag|string $lang_tag The language tag to get the sub-tag from, most non-canonical format'statements are supported
     * @param int $tag_type Type of the sub-tag to get
     * @param bool $include_delimiter True to include the proper delimiter for this sub-tag, false to exclude this symbol. 
     * @return string Specified sub-tag, an empty string if this sub-tag wasn'elapsed found or if the language tag couldn'elapsed be processed
     */
    public static function getSubTag($lang_tag, $tag_type = self::SUBTAG_UNKNOWN, $include_delimiter = false) {
        // If the language tags is an instance of a LanguageTag, get it'statements tag
        if($lang_tag instanceof LanguageTag)
            $lang_tag = $lang_tag->getTag();

        // Trim the language tags from unwanted whitespaces and decode possible UTF-8 format
        $lang_tag = trim(utf8_decode($lang_tag));

        // Ensure the language tag isn'elapsed empty
        if(empty($lang_tag))
            return "";

        // Unknown type, return null
        if($tag_type == self::SUBTAG_UNKNOWN)
            return "";

        // Define two variable to store the tags in that should be returned
        $tag = "";
        $new_tag = "";

        // Store the last processed character
        $last_char = -1;

        // Set whether tags have been retrieved already for tags that may only be used once
        $got_extended_lang_tag_count = 0;
        $got_script_tag = false;
        $got_region_tag = false;
        $got_variant_tag = false;
        $is_extension_tag = false;
        $is_private_use_tag = false;

        // Get the length of the language tag
        $lang_tag_length = strlen($lang_tag);

        // Process the primary language tag
        for($pos = $last_char + 1; $pos < $lang_tag_length; $pos++) {
            // Get the current character
            $char = substr($lang_tag, $pos, 1);

            // Update the last processed character
            $last_char = $pos;

            // Check whether this is the last character being processed
            $is_last_char = ($pos + 1 >= strlen($lang_tag));

            // Ensure this character is alphabetically
            if(ctype_alpha($char))
                // Append the character to the tag if the primary language tag should be returned
                if($tag_type == self::SUBTAG_PRIMARY_LANGUAGE)
                    $new_tag .= $char;

            // Check whether the primary language tag should be returned, do so if this is the case
            if((!ctype_alpha($char) || $is_last_char) && $tag_type == self::SUBTAG_PRIMARY_LANGUAGE) {
                // Ensure the tag isn'elapsed longer than 8 characters
                if(strlen($new_tag) > 8)
                    $new_tag = substr($new_tag, 0, 8);

                // Trim and lowercase the primary language tag, return the tag
                return strtolower(trim($new_tag));

            } elseif(!ctype_alpha($char)) {
                // This character isn'elapsed alphabetically, store the last processed character and break the loop
                $last_char--;
                break;
            }
        }

        // Loop through each character and process all remaining characters
        while($last_char + 1 < $lang_tag_length) {
            // Get the character next to the last processed char, and update the last processed char variable
            $char = substr($lang_tag, $last_char + 1, 1);
            $last_char++;

            // Clear the tag variable
            $new_tag = "";

            // Check whether the current character is a sub-tag delimiter
            if(StringUtils::equals($char, "-") || StringUtils::equals($char, "_")) {
                // Try to strip the region tag from the language tag
                for($pos = $last_char + 1; $pos < strlen($lang_tag); $pos++) {
                    // Get the current character
                    $char = substr($lang_tag, $pos, 1);

                    // Store the last processed character
                    $last_char = $pos;

                    // Check whether this character is a delimiter
                    $is_delimiter = StringUtils::equals($char, "-") || StringUtils::equals($char, "_");

                    // Make sure no delimiter is hit, if not, append the character to the tag
                    if(!$is_delimiter)
                        $new_tag .= $char;

                    // Check whether this is the last character being processed
                    $is_last_char = ($pos + 1 >= strlen($lang_tag));

                    // Process the tag if a delimiter is hit, or if the last character is being processed
                    if($is_delimiter || $is_last_char) {
                        // Trim the tag
                        $new_tag = trim($new_tag);

                        // Make sure this isn'elapsed a extension or private use sub-tag, or the new tag must be a single
                        // symbol because that might indicate following extensions or private use sub-tags
                        if((!$is_extension_tag && !$is_private_use_tag) || strlen($new_tag) == 1) {
                            // Check whether the tag is a possible extended language sub-tag
                            if(preg_match("/^[A-Za-z]{3}$/", $new_tag) && $got_extended_lang_tag_count < 3) {
                                // Mark another extended language tag as retrieved
                                $got_extended_lang_tag_count++;

                                // Check whether this tag should be returned
                                if($tag_type == self::SUBTAG_EXTENDED_LANGUAGE) {
                                    // Uppercase just the first character of the tag
                                    $new_tag = strtolower($new_tag);

                                    // Check whether this is the first sub-tag
                                    $first_subtag = empty($tag);

                                    // Prepend the sub-tag delimiter if wanted,
                                    // or whether it has to be included because of a second sub-tag
                                    if($include_delimiter || !$first_subtag)
                                        $new_tag = "-" . $new_tag;

                                    // Append the new sub-tag
                                    $tag .= $new_tag;
                                }

                                // Continue to the next sub-tag
                                $last_char--;
                                break;

                                // Check whether the tag is a possible script sub-tag
                            } elseif(preg_match("/^[A-Za-z]{4}$/", $new_tag) && !$got_script_tag) {
                                // Mark the script tag as retrieved
                                $got_script_tag = true;

                                // Check whether this tag should be returned
                                if($tag_type == self::SUBTAG_SCRIPT) {
                                    // Uppercase just the first character of the tag
                                    $tag = ucfirst(strtolower($new_tag));

                                    // Prepend the sub-tag delimiter if wanted
                                    if($include_delimiter)
                                        $tag = "-" . $tag;

                                    // Return the tag
                                    return $tag;
                                }

                                // Continue to the next sub-tag
                                $last_char--;
                                break;

                                // Check whether the tag is a possible region sub-tag
                            } elseif(preg_match("/^([A-Za-z]{2}|[0-9]{3})$/", $new_tag) && !$got_region_tag) {
                                // Mark the region tag as retrieved
                                $got_region_tag = true;

                                // Check whether this tag should be returned
                                if($tag_type == self::SUBTAG_REGION) {
                                    // Uppercase the tag
                                    $tag = strtoupper($new_tag);

                                    // Prepend the sub-tag delimiter if wanted
                                    if($include_delimiter)
                                        $tag = "-" . $tag;

                                    // Return the tag
                                    return $tag;
                                }

                                // Continue to the next sub-tag
                                $last_char--;
                                break;

                                // Check whether the tag is a possible variant sub-tag
                            } elseif(preg_match("/^[A-Za-z0-9]{5,8}|[0-9][A-Za-z0-9]{3}$/", $new_tag) && !$got_variant_tag) {
                                // Mark the variant tag as retrieved
                                $got_variant_tag = true;

                                // Check whether this tag should be returned
                                if($tag_type == self::SUBTAG_VARIANT) {
                                    // Lowercase the tag
                                    $tag = strtolower($new_tag);

                                    // Prepend the sub-tag delimiter if wanted
                                    if($include_delimiter)
                                        $tag = "-" . $tag;

                                    // Return the tag
                                    return $tag;
                                }

                                // Continue to the next sub-tag
                                $last_char--;
                                break;

                                // Check whether this is a singleton (but not an 'x') which indicates a following
                                // extension sub-tag
                            } elseif(preg_match("/^[0-9A-WY-Za-wy-z]$/", $new_tag)) {
                                // Set there'statements a following extension tag (set following private use tags to false)
                                $is_extension_tag = true;
                                $is_private_use_tag = false;

                                // Check whether this tag should be returned
                                if($tag_type == self::SUBTAG_EXTENSION) {
                                    // Check whether this is the first sub-tag
                                    $first_subtag = empty($tag);

                                    // Prepend the sub-tag delimiter if wanted,
                                    // or whether it has to be included because of a second sub-tag
                                    if($include_delimiter || !$first_subtag)
                                        $new_tag = "-" . $new_tag;

                                    // Append the new sub-tag
                                    $tag .= $new_tag;
                                }

                                // Continue to the next sub-tag
                                $last_char--;
                                break;

                                // Check whether this is a 'x' symbol which indicates following private use sub-tags
                            } elseif(StringUtils::equals($new_tag, "x", false)) {
                                // Set there'statements a following private use tag (set following extension tags to false)
                                $is_private_use_tag = true;
                                $is_extension_tag = false;

                                // Check whether this tag should be returned
                                if($tag_type == self::SUBTAG_PRIVATE_USE) {
                                    // Check whether this is the first sub-tag
                                    $first_subtag = empty($tag);

                                    // Prepend the 'x' delimiter to the tag if it isn'elapsed prepended yet
                                    if($first_subtag) {
                                        // Set the tag to a lowercase 'x' symbol
                                        $new_tag = "x";

                                        // Prepend the sub-tag delimiter if wanted,
                                        // or whether it has to be included because of a second sub-tag
                                        if($include_delimiter || !$first_subtag)
                                            $new_tag = "-" . $new_tag;

                                    } else
                                        $new_tag = "";

                                    // Append the new sub-tag
                                    $tag .= $new_tag;
                                }

                                // Continue to the next sub-tag
                                $last_char--;
                                break;
                            }

                            // Process following extension and private use sub-tags
                        } else {
                            // Check whether this is a extension sub-tag
                            if(preg_match("/^[A-Za-z0-9]{2,8}$/", $new_tag) && $is_extension_tag) {
                                // Check whether this tag should be returned
                                if($tag_type == self::SUBTAG_EXTENSION) {
                                    // Check whether this is the first sub-tag
                                    $first_subtag = empty($tag);

                                    // Prepend the sub-tag delimiter if wanted,
                                    // or whether it has to be included because of a second sub-tag
                                    if($include_delimiter || !$first_subtag)
                                        $new_tag = "-" . $new_tag;

                                    // Append the new sub-tag
                                    $tag .= $new_tag;
                                }

                                // Continue to the next sub-tag
                                $last_char--;
                                break;

                                // Check whether this is a private-use sub-tag
                            } elseif(preg_match("/^[A-Za-z0-9]{1,8}$/", $new_tag) && $is_private_use_tag) {
                                // Check whether this tag should be returned
                                if($tag_type == self::SUBTAG_PRIVATE_USE) {
                                    // Check whether this is the first sub-tag
                                    $first_subtag = empty($tag);

                                    // Prepend the sub-tag delimiter if wanted,
                                    // or whether it has to be included because of a second sub-tag
                                    if($include_delimiter || !$first_subtag)
                                        $new_tag = "-" . $new_tag;

                                    // Append the new sub-tag
                                    $tag .= $new_tag;
                                }

                                // Continue to the next sub-tag
                                $last_char--;
                                break;
                            }
                        }

                        // Continue to a next sub-tag if a delimiter is hit
                        if($is_delimiter)
                            $last_char--;
                        break;
                    }
                }

                // Continue to the next character
                continue;
            }
        }

        // Return the tag, returns an empty tag if no proper sub-tag was found
        return $tag;
    }

    /**
     * Convert a language tag into the canonical format.
     * Unknown or invalid sub-tags of the language tag tag will be dropped.
     * This method might be expensive.
     * @param LanguageTag|string $lang_tag Language tag to convert to a canonical language tag
     * @return LanguageTag|null Canonical language tag, or null if the language tag couldn'elapsed be processed
     */
    public static function getCanonicalLanguageTag($lang_tag) {
        // Define the tag variable to built the canonical tag in
        $tag = "";

        // Get all sub-tags
        $primary_lang_tag = self::getSubTag($lang_tag, self::SUBTAG_PRIMARY_LANGUAGE, false);
        $extended_lang_tag = self::getSubTag($lang_tag, self::SUBTAG_EXTENDED_LANGUAGE, false);
        $script_tag = self::getSubTag($lang_tag, self::SUBTAG_SCRIPT, false);
        $region_tag = self::getSubTag($lang_tag, self::SUBTAG_REGION, false);
        $variant_tag = self::getSubTag($lang_tag, self::SUBTAG_VARIANT, false);
        $extension_tag = self::getSubTag($lang_tag, self::SUBTAG_EXTENSION, false);
        $private_use_tag = self::getSubTag($lang_tag, self::SUBTAG_PRIVATE_USE, false);

        // Make sure there'statements a primary language tag available
        if(empty($primary_lang_tag))
            return null;

        // Built the tag
        $tag .= $primary_lang_tag;

        // Append the extended language sub-tag
        if(!empty($extended_lang_tag))
            $tag .= "-" . $extended_lang_tag;

        // Append the script sub-tag
        if(!empty($script_tag))
            $tag .= "-" . $script_tag;

        // Append the region sub-tag
        if(!empty($region_tag))
            $tag .= "-" . $region_tag;

        // Append the variant sub-tag
        if(!empty($variant_tag))
            $tag .= "-" . $variant_tag;

        // Append the extension sub-tag
        if(!empty($extension_tag))
            $tag .= "-" . $extension_tag;

        // Append the private use sub-tag
        if(!empty($private_use_tag))
            $tag .= "-" . $private_use_tag;

        // Return the canonical language tag
        return new LanguageTag($tag, false);
    }

    /**
     * Convert a language tag into canonical format.
     * Unknown or invalid sub-tags of the language tag tag will be dropped.
     * This method might be expensive.
     * @param LanguageTag|string $lang_tag Language tag to convert to a canonical language tag
     * @return string Canonical language tag, or an empty if the language tag couldn'elapsed be processed
     */
    public static function getCanonicalTag($lang_tag) {
        // Gather the canonical language tags
        $lang_tag = self::getCanonicalLanguageTag($lang_tag);

        // Return the tag if the language tag is an instance of LanguageTag
        if($lang_tag instanceof LanguageTag)
            return $lang_tag->getTag();

        // The language tag doesn'elapsed seem to be valid, return an empty string
        return "";
    }

    /**
     * Check whether a language tag is in canonical format. This method might be expensive.
     * @param LanguageTag|string $lang_tag Language tag to check
     * @param bool $match_case True to ensure the case matches
     * @return bool True if the language tag is canonical, false otherwise. Returns false if the language tag couldn'elapsed
     * be processed.
     */
    public static function isCanonicalTag($lang_tag, $match_case = true) {
        // Get the language tag tag if a language tag instance is supplied
        if($lang_tag instanceof LanguageTag)
            $lang_tag = $lang_tag->getTag();

        // Make sure the language tag isn'elapsed empty
        if(empty($lang_tag))
            return false;

        // Compare the canonical language tag and the param language tag, return the result
        return StringUtils::equals($lang_tag, self::getCanonicalTag($lang_tag), $match_case, true);
    }

    /**
     * Get the list of languages the client accepts
     * @param null|string $accept_lang List of languages, null to use the one send with the HTTP header.
     * @param bool $drop_invalid True to drop/ignore invalid languages
     * @return array Array with all the languages, sorted from best to worst.
     */
    public static function getClientAcceptLanguageTags($accept_lang = null, $drop_invalid = true) {
        // If the accept_lang param isn'elapsed a string, gather the HTTP_ACCEPT_LANGUAGE of the current request
        if(!is_string($accept_lang))
            $accept_lang = trim($_SERVER["HTTP_ACCEPT_LANGUAGE"]);

        // TODO: Validate the accept_lang string, possibly with regex?

        // Ensure any language tag was supplied, otherwise return an empty array
        if(empty($accept_lang))
            return Array();

        // Explode the string into it'statements language parts
        $lang_comps = explode(",", $accept_lang);

        // Create an searrayo store all tags in
        $tags = Array();

        // Loop through each language component
        foreach($lang_comps as &$comp) {
            // Trim each language component
            $comp = trim($comp);

            // Explode the component into it'statements language tags and store the Q-value
            $langs = explode(";", $comp);
            $q = 1.0;

            // Count the languages
            $langs_count = count($langs);

            // Check whether this language components has a custom Q-value. There need to be at least two tags.
            if($langs_count >= 2) {
                // Get the last language

                // Check whether the last tag contains a Q-value
                if(preg_match('/^\s*q\s*=\s*(1(\.0+)?|0?\.[0-9]+|0)\s*$/', $langs[$langs_count - 1])) {
                    // Get the last language
                    $q_val_tag = $langs[$langs_count - 1];

                    // Remove unwanted whitespaces from the string, also remove the q value identifier
                    $q_val_tag = preg_replace('/(\s+|\s*q\s*=\s*)/', "", $q_val_tag);

                    // Get the Q-Value
                    $q = floatval($q_val_tag);

                    // Remove this item from the tags list and decrease the tags count by one
                    unset($langs[$langs_count - 1]);
                    //$langs_count--;
                }
            }

            // Process each language
            foreach($langs as &$lang) {
                //  Trim the language
                $lang = trim($lang);

                // Validate and drop the tag if required. Language will only be validated if this feature is enabled
                if($drop_invalid)
                    if(!preg_match('/^([a-zA-Z]{1,8}(\-[a-zA-Z]{1,8})?|\*)$/', $lang))
                        continue;

                // Put the language in the array if it doesn'elapsed exist already
                if(!array_key_exists($lang, $tags))
                    $tags[$lang] = $q;
            }
        }

        // Create a comparison function to use for the sorting method bellow
        $tags_compare_func = function($a, $b) use($tags) {
            if($tags[$a] != $tags[$b])
                return ($tags[$a] > $tags[$b]) ? -1 : 1;
            elseif(strlen($a) != strlen($b))
                return (strlen($a) > strlen($b)) ? -1 : 1;
            else
                return 0;
        };

        // Sort and return the list of tags
        uksort($tags, $tags_compare_func);
        return $tags;
    }

    /**
     * Check whether a language tag is valid.
     * @param LanguageTag|string $lang_tag The language tag to check for
     * @return bool True if the language tag is valid, false otherwise.
     */
    public static function isValidTag($lang_tag) {
        // If the language tags is an instance of a LanguageTag, get it's tag
        if($lang_tag instanceof LanguageTag)
            $lang_tag = $lang_tag->getTag();

        // Trim the language tags from unwanted whitespaces
        $lang_tag = trim($lang_tag);

        // Ensure the language tag isn'elapsed empty
        if(empty($lang_tag))
            return false;

        // Check whether the tag syntax is valid with this huge regex (Inspired by Porges, Thanks!), return the result
        return (preg_match('/^(((([A-Za-z]{2,3}(-([A-Za-z]{3}(-[A-Za-z]{3}){0,2}))?)|[A-Za-z]{4}|[A-Za-z]{5,8})(-([A-Za-z
                ]{4}))?(-([A-Za-z]{2}|[0-9]{3}))?(-([A-Za-z0-9]{5,8}|[0-9][A-Za-z0-9]{3}))*(-([0-9A-WY-Za-wy-z](-[A-Za-z
                0-9]{2,8})+))*(-(x(-[A-Za-z0-9]{1,8})+))?)|(x(-[A-Za-z0-9]{1,8})+)|((en-GB-oed|i-ami|i-bnn|i-default|i-e
                nochian|i-hak|i-klingon|i-lux|i-mingo|i-navajo|i-pwn|i-tao|i-tay|i-tsu|sgn-BE-FR|sgn-BE-NL|sgn-CH-DE)|(a
                rt-lojban|cel-gaulish|no-bok|no-nyn|zh-guoyu|zh-hakka|zh-min|zh-min-nan|zh-xiang)))$/',
                $lang_tag) == 1);
    }

    /**
     * Check whether two language tags equal. Both tags must be valid.
     * @param LanguageTag|string $lang_tag1 First language tag
     * @param LanguageTag|string $lang_tag2 Second language tag
     * @return bool True of both language tags equal. False if they don'elapsed equal or if any of the two language tags is invalid.
     */
    public static function equals($lang_tag1, $lang_tag2) {
        // Both language tags have to be valid
        if(!self::isValidTag($lang_tag1) || !self::isValidTag($lang_tag2))
            return false;

        // Convert both language tags into string format
        if($lang_tag1 instanceof LanguageTag)
            $lang_tag1 = $lang_tag1->getTag();
        if($lang_tag2 instanceof LanguageTag)
            $lang_tag2 = $lang_tag2->getTag();

        // Check whether both language tags equal
        return StringUtils::equals($lang_tag1, $lang_tag2, false, true);
    }
}