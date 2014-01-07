<?php

/**
 * StringUtils.php
 * StringUtils class for Carbon CMS.
 * String utilities class.
 * @author Tim Visee
 * @website http://timvisee.com/
 * @copyright Copyright © Tim Visee 2013, All rights reserved.
 */

namespace core\util;

// Prevent users from accessing this file directly
defined('CARBON_ROOT') or die('Access denied!');

/**
 * StringUtils class
 * @package core\util
 * @author Tim Visee
 */
class StringUtils {

    /**
     * Check whether two strings equal to each other
     * @param string $str1 First string
     * @param string $str2 Second string
     * @param bool $case_sensitive True to use case sensitivity, false otherwise (default: true)
     * @param bool $trim_whitespaces True to trim whitespaces first from the two strings (default: false)
     * @return bool True if the two strings match, false otherwise
     */
    public static function equals($str1, $str2, $case_sensitive = true, $trim_whitespaces = false) {
        // Trim all the unnecessary whitespaces from the two strings
        if($trim_whitespaces) {
            $str1 = trim($str1);
            $str2 = trim($str2);
        }

        // Check whether the strings are equal
        if($case_sensitive)
            return (strcmp($str1, $str2) == 0);
        else
            return (strcasecmp($str1, $str2) == 0);
    }

    /**
     * @param string $haystack Check if a string contains a sub string
     * @param string|array $needle Sub string, or array with a list of sub strings.
     * @param bool $case_sensitive False to check without case sensitivity (default: true)
     * @return bool True if the haystack contains the needle,
     * if the $needle is a array, true will be returned if the string contains any of the items
     */
    public static function contains($haystack, $needle, $case_sensitive = true) {
        // Create an array of the needle, if it's not an array already
        if(!is_array($needle))
            $needle = Array($needle);

        // Check for each needle, if it exists in the $haystack
        foreach($needle as $entry) {
            // Use case sensitivity or not, based on method arguments
            if($case_sensitive) {
                if(strpos($haystack, $entry) !== false)
                    return true;
            } else {
                if(stripos($haystack, $entry) !== false)
                    return true;
            }
        }

        // String doesn't contain this needle, return false
        return false;
    }

    /**
     * Check if a string starts with a substring
     * @param string $haystack String to check in
     * @param string $needle Sub String
     * @param bool $ignore_case Should the case be ignored (default: false)
     * @return bool True if the haystack starts with the needle
     */
    public static function startsWith($haystack, $needle, $ignore_case = false) {
        // Make sure the needle length is not longer than the haystack
        if(strlen($needle) > strlen($haystack))
            return false;

        // Compare the strings, check if it should be case sensitive
        if(!$ignore_case)
            return (substr($haystack, 0, strlen($needle)) == $needle);
        else
            return (strtolower(substr($haystack, 0, strlen($needle))) == strtolower($needle));
    }

    /**
     * Check if a string ends with a sub string
     * @param string $haystack String
     * @param string $needle Sub string
     * @param bool $ignore_case Should the case be ignored (default: false)
     * @return bool True if the haystack ends with the needle
     */
    public static function endsWith($haystack, $needle, $ignore_case = false) {
        // Make sure the needle length is not longer than the haystack
        if(strlen($needle) > strlen($haystack))
            return false;

        // Compare the strings, check if it should be case sensitive
        if(!$ignore_case)
            return (substr($haystack, -strlen($needle)) == $needle);
        else
            return (strtolower(substr($haystack, -strlen($needle))) == strtolower($needle));
    }
}