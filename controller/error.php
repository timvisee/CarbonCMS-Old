<?php

/**
 * error.php
 * Error controll file for Carbon CMS.
 * @author Tim Vis�e
 * @website http://timvisee.com/
 * @copyright Copyright � Tim Vis�e 2012, All rights reserved.
 */

namespace controller;

use controller\Controller;
use core\Database;

// Prevent users from accessing this file directly
defined('CARBON_ROOT') or die('Access denied!');

class Error extends Controller {
    
    function __construct(Database $db) {
        parent::__construct($db);
    }
    
    public function index() {
        $this->view->render('error/index');
    }
}