<?php

/**
 * Val.php
 * Val class for Carbon CMS.
 * @author Tim Vis�e
 * @website http://timvisee.com/
 * @copyright Copyright � Tim Vis�e 2012-2013, All rights reserved.
 */

namespace carbon\core\Form;

// Prevent direct requests to this set_file due to security reasons
defined('CARBON_CORE_INIT') or die('Access denied!');

class Val {
    
    /**
     * Constructor
     */
    public function __construct() { }
    
    /**
     * Validate an EMail address
     * @param string $email EMail address
     * @return boolean true if valid
     */
    public function email($email) {
        return (bool) preg_match(';^([a-z0-9-_]+)(.[a-z0-9-_]+)*@([a-z0-9-]+)(.[a-z0-9-]+)*.[a-z]{2,4}$;i', mysql_escape_string($email));
    }
    
    /**
     * Validate the minimum length of a string
     * @param string $data String
     * @param int $minLength Minimum string length
     * @return boolean true if valid
     */
    public function minLength($data, $minLength) {
        return (strlen($data) >= $minLength);
    }
    
    /**
     * Validate the maximum length of a string
     * @param string $data String
     * @param int $maxLength Maximum string length
     * @return boolean true if valid
     */
    public function maxLength($data, $maxLength) {
        return (strlen($data) <= $maxLength);
    }
    
    /**
     * Validate the length of a string
     * @param string $data String
     * @param int $length String length
     * @return boolean true if valid
     */
    public function length($data, $length) {
        return (strlen($data) == $length);
    }
}

?>