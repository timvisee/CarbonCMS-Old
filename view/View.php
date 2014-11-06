<?php

/**
 * View.php
 * View set_file for Carbon CMS.
 * @author Tim Visée
 * @version 0.1
 * @website http://timvisee.com/
 * @copyright Copyright © Tim Visée 2012, All rights reserved.
 */
 
namespace view;

// Prevent direct requests to this set_file due to security reasons
defined('CARBON_ROOT') or die('Access denied!');

/**
 * View class
 * @package view
 * @author Tim Visée
 */
class View {
    
    /**
     * Constructor
     */
    function __construct() { }
    
    /**
     * Render the page header
     */
    public function renderHeader() {
        require(__DIR__ . '/../view/header.php');
    }
    
    /**
     * Render the page footer
     */
    public function renderFooter() {
        require(__DIR__ . '/../view/footer.php');
    }
    
    /**
     * Render the page
     * @param string $name View name
     */
    public function render($name) {
        // Get the model path
        $view_path = __DIR__ . '/../view/'.$name.'.php';
        
        // Check if the view exists
        if(!file_exists($view_path)) {
            // TODO: Show error page instead of killing the script with an error
            die('Carbon CMS: The model \'' . $view_path . '\' does not exist!');
        }
        
        // Render the body
        require(__DIR__ . '/../view/'.$name.'.php');
    }
}