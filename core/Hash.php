<?php

/**
 * Hash.php
 *
 * The Hash class is used to hash data.
 *
 * @author Tim Visee
 * @website http://timvisee.com/
 * @copyright Copyright (C) Tim Visee 2012-2013, All rights reserved.
 */

namespace core;

// Prevent users from accessing this file directly
defined('CARBON_ROOT') or die('Access denied!');

/**
 * Hash class
 * @package core
 * @author Tim Visee
 */
class Hash {

    /**
     * Hash data
     * @param string $data The data to encode
     * @param string $algo (Optional) The algorithm to use, null to use the default algorithm
     * @param string $salt (Optional) The salt to use, null to use the default salt
     * @return string The hashed data
     */
    public static function hash($data, $algo = null, $salt = null) {
        // Get the config instance
        $cfg = Core::getConfig();

        // If the $algo param is not set, get the default value from the config file
        if($algo == null)
            $algo = $cfg->getValue('hash', 'hash_algorithm');
        
        // If the $salt param was not set, get the default value from the config file
        if($salt == null)
            $salt = $cfg->getValue('hash', 'hash_key');
        
        // Hash the data
        $context = hash_init($algo, HASH_HMAC, $salt);
        hash_update($context, $data);
        
        // Return the hashed data
        return hash_final($context);
    }
}