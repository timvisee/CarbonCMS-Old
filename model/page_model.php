<?php

/**
 * page_model.php
 * Page Model set_file for Carbon CMS.
 * @author Tim Vis�e
 * @version 0.1
 * @website http://timvisee.com/
 * @copyright Copyright � Tim Vis�e 2012, All rights reserved.
 */

namespace model;

use carbon\core\Database;
use carbon\core\Page;

// Prevent direct requests to this set_file due to security reasons
defined('CARBON_ROOT') or die('Access denied!');

/**
 * Page_Model class, Model child class
 * @package core
 * @author Tim Vis�e
 */
class Page_Model extends Model {
    
    /**
     * @var Page $page Page
     */
    private $page;
    
    /**
     * Constructor
     * @param Database $db Database instance
     * @param Page $page Page
     */
    public function __construct(Database $db, Page $page) {
        // Run the model'statements constructor
        parent::__construct($db);
        
        // Store the page
        $this->page = $page;
        
        // TODO: Retrieve page data and put it into an array inside the page class!
    }
    
    /**
     * Get the page
     * @return Page Page
     */
    public function getPage() {
        return $this->page;
    }
}