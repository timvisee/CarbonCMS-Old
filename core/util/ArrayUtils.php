<?php

/**
 * ArrayUtils.php
 * ArrayUtils class for Carbon CMS.
 * Array utilities class.
 * @author Tim Visee
 * @website http://timvisee.com/
 * @copyright Copyright © Tim Visee 2013, All rights reserved.
 */

namespace core\util;

// Prevent users from accessing this file directly
defined('CARBON_ROOT') or die('Access denied!');

/**
 * ArrayUtils class
 * @package core\util
 * @author Tim Visee
 */
class ArrayUtils {

    /**
     * Copy an array
     * @param $arr Array to copy
     * @return array Copy of the array
     */
    public static function copyArray($arr) {
        return array_merge(Array(), $arr);
    }

    /**
     * Checks whether an array is associative.
     * @param array $arr The array to check
     * @return bool True if the array is associative, false otherwise. May return false if it's not a valid array.
     */
    public static function isAssoc($arr) {
        if(!is_array($arr) || empty($arr))
            return false;

        $i = 0;
        foreach(array_keys($arr) as $k) {
            if($k !== $i)
                return true;

            $i++;
        }
        return false;
    }
}