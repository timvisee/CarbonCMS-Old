<?php

/**
 * Form.php
 * Form class for Carbon CMS.
 * @author Tim Vis�e
 * @website http://timvisee.com/
 * @copyright Copyright � Tim Vis�e 2012-2013, All rights reserved.
 */

// TODO: FINISH CLASS!

namespace core;

use core\Form\Val;

// Prevent users from accessing this file directly
defined('CARBON_ROOT') or die('Access denied!');

/**
 * Steps:
 * - Fill out a form
 * - POST to PHP
 * - Sanatize
 * - Validate data
 * - Return data
 * - Write to Database
 */

class Form {
    
    /**
     * @var array $_postData Stores the posted data
     */
    private $_postData = array();
    
    /**
     * @var object $_val The validator object
     */
    private $_val = array();
    
    /**
     * Constructor
     */
    public function __construct() {
        $this->_val = new Val();
    }
    
    /**
     * Get values from $_POST
     * @param string $fieldName Field name
     * @return Form this
     */
    public function post($fieldName) {
        // Add the variable to the $_postData array
        if(isset($_POST[$fieldName]))
            $this->_postData[$fieldName] = $_POST[$fieldName];
        else
            $this->_postData[$fieldName] = null;
        
        return $this;
    }
    
    /**
     * Return posted data
     * @param string $fieldName Field name
     * @return mixed String, array or null
     */
    public function fetch($fieldName = null) {
        // Fetch all data, or a single field
        if($fieldName == null || trim($fieldName) == '')
            return $this->_postData;
        else
            if(isset($this->_postData[$fieldName]))
                return $this->_postData[$fieldName];
            else
                return null;
    }
    
    /**
     * Validate variables
     * @param string $fieldName Field name to check
     * @param string $validatorType Validator type
     * @param mixed $arg Validator arguments
     * @return boolean true if valid
     */
    public function val($fieldName, $validatorType, $arg = null) {
        // Get the post item
        $postItem = $this->_postData[$fieldName];
        
        // Validate the post item
        return $this->_val->{$validatorType}($postItem, $arg);
    }
}

?>