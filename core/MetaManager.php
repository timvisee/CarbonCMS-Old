<?php

/**
 * MetaManager.php
 *
 * The MetaManager class manages all the meta tags for the website.
 *
 * @author Tim Visee
 * @website http://timvisee.com/
 * @copyright Copyright (C) Tim Visee 2012-2013, All rights reserved.
 */

namespace core;

// Prevent users from accessing this file directly
defined('CARBON_ROOT') or die('Access denied!');

/**
 * Manages all the meta tags for the website.
 * @package core
 * @author Tim Visee
 */
class MetaManager {

    /** @var array Array containing the meta tags */
    private $meta = array();

    /**
     * Constructor
     * @param array|Meta $meta (Optional) Meta object or array of Meta objects, null to use no default meta tags
     */
    public function __construct($meta = null) {
        // Store the meta from the $meta param if set
        if($meta != null) {
            // Check if the $meta param is an array already
            if(is_array($meta))
                $this->meta = $meta;
            else
                $this->meta = array($meta);
        }
    }

    /**
     * Get the array with meta tags
     * @return array Array of meta tags
     */
    public function getAllMeta() {
        return $this->meta;
    }

    /**
     * Get the amount of meta tags
     * @return int Amount of meta tags
     */
    public function getMetaCount() {
        return sizeof($this->meta);
    }

    /**
     * Get a meta tag
     * @param string $name Name of the meta tag to get
     * @param bool $case_sensitive (Optional) Set whether the name of the meta tag to search for is case sensitive, default false
     * @return Meta The meta tag or null if the meta tag could not be found
     */
    public function getMeta($name, $case_sensitive = false) {
        // Make sure the name of the meta tag is valid
        if(!Meta::isValidName($name))
            return null;

        // Try to get the meta tag to return
        foreach($this->meta as $entry) {
            if($case_sensitive) {
                if(strtolower($entry->getName()) == strtolower($name))
                    return $entry;
            } else {
                if($entry->getName() == $name)
                    return $entry;
            }
        }

        // No meta tag found, return null
        return null;
    }

    /**
     * Add a meta tag
     * @param Meta $meta The meta tag to add
     * @param bool $overwrite Set if already exsisting meta tags with the same name should be overwritten
     * @return bool False if the meta tag was not being added because an already existing meta tag with the same name
     *              may not be overwritten.
     * @throws \Exception Throws when the $meta param is not an instance of the Meta class
     */
    public function setMeta($meta, $overwrite = true) {
        // Make sure the meta is an instance of the Meta class
        if(!($meta instanceof Meta))
            throw new \Exception("The meta tag has to be an instance of the Meta object");

        // Check if old tags may be overwritten
        if($overwrite) {
            // Remove old tags with the same name
            $this->removeMeta($meta->getName(), true);
        } else {
            // Check if there's any tag with the same name, if so, return
            if($this->isMeta($meta->getName(), true))
                return false;
        }

        // Add the meta tag and return true
        array_push($this->meta, $meta);
        return true;
    }

    /**
     * Remove meta tag(s)
     * @param string $name The name of the meta tag(s) to remove
     * @param bool $case_sensitive (Optional) Set if the name is case sensitive or not, default false
     * @return int Amount of removed meta tags
     */
    public function removeMeta($name, $case_sensitive = false) {
        // Keep track of the amount of tags being removed
        $removed = 0;

        // Check for reach meta and unset the tag if the name equals
        foreach($this->meta as &$entry) {
            if($case_sensitive) {
                if(strtolower($entry->getName()) == strtolower($name)) {
                    unset($entry);
                    $removed++;
                }
            } else {
                if($entry->getName() == $name) {
                    unset($entry);
                    $removed++;
                }
            }
        }

        // Return the amount of removed tags
        return $removed;
    }

    /**
     * Remove all the meta tags
     * @return int Amount of removed meta tags
     */
    public function removeAllMeta() {
        // Get the amount of meta tags that is going to be removed
        $removed = sizeof($this->meta);

        // Remove all the meta tags
        $this->meta = array();

        // Return the amount of removed tags
        return $removed;
    }

    /**
     * Check whether a meta tag is set
     * @param string $name Name of the meta tag to check for
     * @param bool $case_sensitive (Optional) Set if the name check is case sensitive or not, false by default
     * @return bool True if any meta tag exists with this name
     */
    public function isMeta($name, $case_sensitive = false) {
        // Make sure the meta tag name is valid
        if(!Meta::isValidName($name))
            return false;

        // Check whether any meta tag exists with the specified name
        foreach($this->meta as $entry) {
            if($case_sensitive) {
                if(strtolower($entry->getName()) == strtolower($name))
                    return true;
            } else {
                if($entry->getName() == $name)
                    return true;
            }
        }
        return false;
    }

    /**
     * Get the HTML code for the meta tags
     * @return string HTML code for the meta tags
     */
    public function getHTML() {
        // Define the variable to output the HTML in
        $out = '';

        // Add the HTML code for each meta tag
        foreach($this->meta as $entry)
            $out .= $entry->getHTML() . PHP_EOL;

        // Return the HTML code
        return $out;
    }
}
