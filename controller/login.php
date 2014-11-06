<?php

/**
 * login.php
 * Login controller file for Carbon CMS.
 * @author Tim Visée
 * @website http://timvisee.com/
 * @copyright Copyright © Tim Visée 2012-2013, All rights reserved.
 */

namespace controller;

use controller\Controller;
use carbon\core\Database;
use model\Login_Model;

// Prevent direct requests to this file due to security reasons
defined('CARBON_ROOT') or die('Access denied!');

class Login extends Controller {

    public function index() {
        $this->view->render('login/index');
    }

    function run() {
        $this->model->run();
    }
}