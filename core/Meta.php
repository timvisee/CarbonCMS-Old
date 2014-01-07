<?php

/**
 * Meta.php
 *
 * Meta class
 *
 * @author Tim Visee
 * @version 0.1
 * @website http://timvisee.com/
 * @copyright Copyright © Tim Visee 2012-2013, All rights reserved.
 */

namespace core;

// Prevent users from accessing this file directly
defined('CARBON_ROOT') or die('Access denied!');

/**
 * Meta class
 * @package core
 * @author Tim Visee
 */
class Meta {

    /** @var string Meta name attribute value */
    private $name = '';
    /** @var string Meta data attribute value */
    private $content = '';
    /** @var string Meta http-equiv attribute value */
    private $http_equiv = null;
    /** @var string Meta scheme attribute value */
    private $scheme = null;
    /** @var string Meta charset attribute value */
    private $charset = null;
    /** @var array Custom meta attributes */
    private $custom_attr = array();

    /**
     * Constructor
     * @param string $name Meta name
     * @param string $content Meta data
     * @throws \Exception Throws when either the $name or the $data param is not a string
     */
    public function __consruct($name, $content = '') {
        // Convert null to an empty string in the $data param
        if($content == null)
            $content = '';

        // Make sure the meta name and data are valid
        if(!self::isValidName($name))
            throw new \Exception("The meta name attribute value has to be a string");
        if(!self::isValidContent($content))
            throw new \Exception("The meta data attribute value has to be a string");

        // Store the meta name and data
        $this->name = $name;
        $this->content = $content;
    }

    /**
     * Get the meta name
     * @return string Meta name
     */
    public function getName() {
        return $this->name;
    }

    /**
     * Get the meta data
     * @return string Meta data
     */
    public function getContent() {
        return $this->content;
    }

    /**
     * Set the meta data
     * @param string $content Meta data
     * @throws \Exception Throws when the meta data is invalid
     */
    public function setContent($content) {
        // Convert null to an empty string in the $data param
        if($content == null)
            $content = '';

        // Make sure the meta data is valid
        if(!self::isValidContent($content))
            throw new \Exception("The meta data attribute value has to be a string");

        // Update the meta data
        $this->content = $content;
    }

    /**
     * Reset the meta data (this will set the meta data to an empty string)
     */
    public function resetContent() {
        // Reset the meta data
        $this->content = '';
    }

    /**
     * Get the meta http-equiv value
     * @return string Meta http-equiv value, returns null if the http-equiv attribute was not set
     */
    public function getHttpEquiv() {
        return $this->http_equiv;
    }

    /**
     * Set the meta http-equiv attribute
     * @param $http_equiv Meta http-equiv attribute value, null to reset the meta http-equiv attribute
     * @throws \Exception Throws when the http-equiv value is invalid
     */
    public function setHttpEquiv($http_equiv) {
        // Make sure the http-equiv value is valid
        if(!self::isValidHttpEquiv($http_equiv) && $http_equiv != null)
            throw new \Exception("The meta http-equiv attribute value value is invalid");

        // Update the meta http-equiv
        $this->http_equiv = $http_equiv;
    }

    /**
     * Reset (disable) the http-equiv attribute
     */
    public function resetHttpEquiv() {
        $this->http_equiv = null;
    }

    /**
     * Check if the http-equiv attribute is set
     * @return bool True if the http-equiv attribute was set
     */
    public function isHttpEquivSet() {
        return ($this->http_equiv != null);
    }

    /**
     * Get the meta scheme attribute value
     * @return string Meta scheme attribute value
     */
    public function getScheme() {
        return $this->scheme;
    }

    /**
     * Set the meta scheme
     * @param $scheme Meta scheme, null to reset the meta scheme attribute
     * @throws \Exception Throws when the meta scheme attribute value is invalid
     */
    public function setScheme($scheme) {
        // Make sure the meta scheme is valid
        if(!self::isValidScheme($scheme) && $scheme != null)
            throw new \Exception("The meta scheme attribute value is invalid");

        // Update the meta scheme
        $this->scheme = $scheme;
    }

    /**
     * Reset(disable) the meta scheme attribute
     */
    public function resetScheme() {
        $this->scheme = null;
    }

    /**
     * Check if the meta scheme attribute is set
     * @return bool True if set
     */
    public function isSchemeSet() {
        return ($this->scheme != null);
    }

    /**
     * Get the meta charset attribute value
     * @return string Meta charset attribute value, null if the meta charset attribute isn't set
     */
    public function getCharset() {
        return $this->charset;
    }

    /**
     * Set the meta charset attribute value
     * @param string $charset Meta charset attribute value, null to reset the meta charset attribute
     * @throws \Exception Throws when the meta charset attribute value is invalid
     */
    public function setCharset($charset) {
        // Check if the meta charset attribute value is valid
        if(!self::isValidCharset($charset) && $charset != null)
            throw new \Exception("The meta charset attribute value is invalid");

        // Update the meta charset attribute value
        $this->charset = $charset;
    }

    /**
     * Reset (disable) the meta charset attribute
     */
    public function resetCharset() {
        $this->charset = null;
    }

    /**
     * Checks whether the meta charset attribute is set
     * @return bool True if the meta charset attribute is set
     */
    public function isCharsetSet() {
        return ($this->charset != null);
    }

    /**
     * Check if a custom attribute is set
     * @param $attr Attribute to check
     * @return bool True if this attribute is set
     */
    public function isCustomAttribute($attr) {
        // Make sure the attribute is valid
        if(!self::isValidCustomAttribute($attr))
            return false;

        // Check if this is a custom attribute that has been set
        return array_key_exists($attr, $this->custom_attr);
    }

    /**
     * Get the value of a custom attribute
     * @param string $attr Attribute to get the value from
     * @return string Attribute value, or null if the attribute is invalid or not set
     */
    public function getCustomAttribute($attr) {
        // Check if this attribute is set
        if(!$this->isCustomAttr($attr))
            return null;

        // Return the attribute value
        $this->custom_attr[$attr];
    }

    /**
     * Set a custom attribute
     * @param $attr Attribute to set
     * @param $val Attribute value
     * @throws \Exception Throws whether the attribute or the attribute value is invalid
     */
    public function setCustomAttribute($attr, $val) {
        // Make sure the attribute and the attribute value are valid
        if(!self::isValidCustomAttribute($attr))
            throw new \Exception("The attribute is invalid");
        if(!self::isValidCustomAttributeValue($val))
            throw new \Exception("The attribute value is invalid");

        // Set or reset the attribute
        if($val != null)
            $this->custom_attr[$attr] = $val;
        else
            $this->resetCustomAttr($attr);
    }

    /**
     * Reset (remove) a custom attribute
     * @param string $attr Attribute to reset (remove)
     */
    public function resetCustomAttribute($attr) {
        // Check if there's any custom attribute with this name
        if(!$this->isCustomAttr($attr))
            return;

        // Reset the attribute
        unset($this->custom_attr[$attr]);
    }

    /**
     * Get the list of custom attributes
     * @return array List of custom attributes
     */
    public function getCustomAttributes() {
        return $this->custom_attr;
    }

    /**
     * Reset the list of custom attributes
     */
    public function resetCustomAttributes() {
        $this->custom_attr = array();
    }

    /**
     * Get the HTML code for the meta tag
     * @return string HTML code of the meta tag
     */
    public function getHTML() {
        // Meta tag prefix
        $out = '<meta ';

        // Add the required name attribute
        $out .= 'name="' . $this->getName() . '" ';

        // Add the data attribute if the http-equiv attribute is not set
        if(!$this->isHttpEquivSet())
            $out .= 'data="' . $this->getContent() . '" ';
        else
            $out .= 'http-equiv="' . $this->getHttpEquiv() . '" ';

        // Add the scheme attribute if set
        if($this->isSchemeSet())
            $out .= 'scheme="' . $this->getScheme() . '" ';

        // Add the charset attribute if set
        if($this->isCharsetSet())
            $out .= 'charset="' . $this->getCharset() . '" ';

        // Add custom attributes to the code
        foreach($this->custom_attr as $attr => $val)
            $out .= $attr . '="' . $val . '" ';

        // Add the tag suffix to the output variable
        $out .= '/>';

        // Return the HTML code
        return $out;
    }

    /**
     * Checks whether a meta name is valid
     * @param mixed $name Meta name to check
     * @return bool True if the meta name is valid
     */
    public static function isValidName($name) {
        // Make sure the meta name is not null
        if($name == null)
            return false;

        // Make sure the meta name is a string
        if(!is_string($name))
            return false;

        // Trim the meta name
        $name = trim($name);

        // Make sure the meta name is not an empty string
        return (strlen($name) > 0);
    }

    /**
     * Check whether meta data is valid
     * @param mixed $content Meta data to check
     * @return bool True if the meta data is valid
     */
    public static function isValidContent($content) {
        // Make sure the meta data is not null
        if($content == null)
            return false;

        // Make sure the meta data is a string
        return is_string($content);
    }

    /**
     * Check whether meta http-equiv is valid
     * @param mixed $http_equiv Meta http-equiv to check
     * @return bool True if the meta http-equiv is valid
     */
    public static function isValidHttpEquiv($http_equiv) {
        // Make sure the meta http-equiv is not null
        if($http_equiv == null)
            return false;

        // Make sure the value is a string
        if(!is_string($http_equiv))
            return false;

        // Convert the value to lowercase
        $http_equiv = strtolower($http_equiv);

        // Define an array with the allowed attribute values
        $allowed_values = array('data-type', 'default-style', 'refresh');

        // Check if the attribute value is valid
        return array_key_exists($http_equiv, $allowed_values);
    }

    /**
     * Check whether meta scheme is valid
     * @param $scheme Meta scheme to check
     * @return bool True if the meta scheme is valid
     */
    public static function isValidScheme($scheme) {
        // Make sure the meta scheme is not null
        if($scheme == null)
            return false;

        // Make sure the meta scheme value is a string
        if(!is_string($scheme))
            return false;

        // Convert the value to lowercase
        $scheme = strtolower($scheme);

        // Define an array with the allowed attribute values
        $allowed_values = array('format', 'uri');

        // Check if the attribute value is valid
        return array_key_exists($scheme, $allowed_values);
    }

    /**
     * Check whether meta charset is valid
     * @param mixed $charset Meta charset to check
     * @return bool True if the meta charset is valid
     */
    public static function isValidCharset($charset) {
        // Make sure the meta charset is not null
        if($charset == null)
            return false;

        // Make sure the meta charset is a string
        return is_string($charset);
    }

    public static function isValidCustomAttribute($attr) {
        // Make sure the attribute is not null
        if($attr == null)
            return false;

        // TODO: May only contain chars and dashes, min length, max length etc..

        // Convert and trim the attribute to lowercase
        $attr = trim(strtolower($attr));

        // Make sure the attribute is allowed
        $non_allowed_attr = array('name', 'data', 'http-equiv', 'charset', 'scheme');
        if(array_key_exists($attr, $non_allowed_attr))
            return false;

        // Make sure the attribute is a string
        return is_string($attr);
    }

    public static function isValidCustomAttributeValue($val) {
        // Make sure the attribute is not null
        if($val == null)
            return false;

        // TODO: May not contain quotes and stuff

        // Make sure the attribute is a string
        return is_string($val);
    }
}