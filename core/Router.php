<?php

/**
 * Router.php
 *
 * The Router class routes all the page requests into the right controller.
 *
 * @author Tim Visee
 * @website http://timvisee.com/
 * @copyright Copyright (C) Tim Visee 2012-2013, All rights reserved.
 */

namespace core;

use controller\Page;

// Prevent users from accessing this file directly
defined('CARBON_ROOT') or die('Access denied!');

/**
 * Routes all the page requests into the right controller.
 * @package core
 * @author Tim Visee
 */
class Router {

    /** @var string String containing the path */
    private $path = '';
    /** @var array Array containing the path parts separated*/
    private $path_arr = Array();
    
    /**
     * Construct and initialize the router
     */
    public function __construct() {
		// Initialize
        $this->init();
    }

    /**
     * Initialize the Router
     */
    public function init() {
        // Get the path info
        if(array_key_exists('ORIG_PATH_INFO', $_SERVER))
            $path = $_SERVER['ORIG_PATH_INFO'];
        else if(array_key_exists('PATH_INFO', $_SERVER))
            $path = $_SERVER['PATH_INFO'];
        else
            $path = '';

        // Filter unwanted stuff from the url
        $path = filter_var($path, FILTER_SANITIZE_URL);

        // Lowercase path info
        // TODO: Shouldn't be removed?
        $path = strtolower($path);

        // Remove any slashes on the beginning or end of the path info string
        // TODO: Only remove slash in front, links with page// could cause problems
        $path = trim($path, '/');

        // Store the path
        $this->path = $path;

        // Generate array of path info string
        $this->path_arr = explode('/', $path);
    }
    
    /**
     * Get the path as array
     * @return array Path array
     */
    public function getPathArray() {
        return $this->path_arr;
    }
    
    /**
     * Get the path as string
     * @param boolean $start_with_separator Should the path start with a file separator
     * @param boolean $end_with_separator Should the path end with a file separator
     * @return string Path
     */
    public function getPath($start_with_separator = false, $end_with_separator = false) {
        return (($start_with_separator) ? DIRECTORY_SEPARATOR : '') . $this->path . (($end_with_separator) ? DIRECTORY_SEPARATOR : '');
    }

    /**
     * Route a page request to the right controller
     */
    public function route() {
        // Set up and initialize the page manager, then set the page manager instance in the Core class
        $page_man = new PageManager(Core::getCache(), Core::getDatabase());
        Core::setPageManager($page_man);

        // Get the page path and path array
        $page_path = $this->getPath();
        $page_path_arr = $this->getPathArray();



        // TODO: Check all the stuff bellow

        // Should the admin controller be loaded
        if(sizeof($this->getPathArray()) >= 1) {
            if(strtolower($page_path_arr[0]) == 'admin') {
                // TODO: Route through the admin controller here...
                die('Load admin controller and show admin page...!');
            }
        }

        // TODO: Check for redirections, and other page types

        // Make sure the path requested is leading to any page
        if(!$page_man->isPage($page_path)) {
            // There was no page found at the current path, return an error
            // TODO: Show a custom error page here
            echo '404 - Page not found!';
            die();
        }

        // Get the page instance of the page to loadLocalesList
        $page = $page_man->getPage($page_path);

        // TODO: Temp code to loadLocalesList the right controller and render the page
        // Construct and initialize the page controller
        $controller = new Page(Core::getDatabase(), $page);
        $controller->loadModel('Page');
        $controller->render();
    }
}