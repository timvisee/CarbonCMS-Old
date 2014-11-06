<?php

/**
 * page.php
 * Page controller file for Carbon CMS.
 * @author Tim Vis�e
 * @version 0.1
 * @website http://timvisee.com/
 * @copyright Copyright � Tim Vis�e 2012-2013, All rights reserved.
 */

namespace controller;

use controller\Controller;
use carbon\core\Database;
use carbon\core\Page as PageClass;

// Prevent direct requests to this file due to security reasons
defined('CARBON_ROOT') or die('Access denied!');

/**
 * Page class, Controller child
 * @package controller
 * @author Tim Vis�e
 */
class Page extends Controller {
    
    /**
     * @var Page $page Page
     */
    private $page;
    
    /**
     * Constructor
     * @param Database $db Database instance
     * @param Page $page Page instance
     */
    public function __construct(Database $db, PageClass $page) {
        // Run the Controller constructor
        parent::__construct($db);
        
        // Store the page
        $this->page = $page;
    }
    
    /**
     * Render the page
     */
    public function render() {
        // TODO: Render page header
        $this->getView()->renderHeader();
        
        // TODO: Use different way of page rendering
        // Render the page body
        echo $this->getModel()->getPage()->getPageBody();
        
        // TODO: Render page footer
        $this->getView()->renderFooter();
    }
    
    /**
     * Get the page
     * @return Page Page
     */
    public function getPage() {
        return $this->page;
    }
}