<?php

// TODO: Create this class!

/**
 * StyleManager.php
 *
 * The StyleManager class manages the stylesheets being loaded for the website layout.
 *
 * @author Tim Visee
 * @version 0.1
 * @website http://timvisee.com/
 * @copyright Copyright (C) Tim Visee 2012-2013, All rights reserved.
 */

namespace core;

// Prevent users from accessing this file directly
defined('CARBON_ROOT') or die('Access denied!');

/**
 * Manages the stylesheets being loaded for the website layout.
 * @package core
 * @author Tim Visee
 */
class StyleManager {

    /** @var array $syle Array containing all the stylesheets */
    private $style = array();

    /**
     * Constructor
     */
    public function __construct() { }

    /**
     * Add a new style
     * @param Style $style Add a new style
     */
    public function addStyle($style) {
        // Make sure that the $style param is not null
        if($style == null)
            return;

        // TODO: Check for duplications!

        // Add the style
        array_push($this->style, $syle);
    }

    /**
     * Reset all the styles
     */
    public function resetStyles() {
        $this->style = array();
    }

    /**
     * Get the HTML code for al the stylesheets
     */
    public function getHTML() {
        // TODO: Generate and return the HTML for the style sheets
    }
}