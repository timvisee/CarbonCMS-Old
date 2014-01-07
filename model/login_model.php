<?php

/**
 * login_model.php
 * Login model class for Carbon CMS.
 * @author Tim Visée
 * @website http://timvisee.com/
 * @copyright Copyright © Tim Visée 2012-2013, All rights reserved.
 */

namespace model;

use core\Database;

// Prevent users from accessing this file directly
defined('CARBON_ROOT') or die('Access denied!');

class Login_Model extends Model {
    
    public function run() {
        // TODO: Rewrite this bellow, table prefixes
        $query = $this->db->prepared("SELECT `user_id` FROM `carbon_users` WHERE username = :username AND password = :password");
        $query->execute(array(
                ':username' => $_POST['username'],
                ':password' => md5($_POST['password'])
        ));
        $data = $query->fetchAll();
        print_r($data);
    }
}