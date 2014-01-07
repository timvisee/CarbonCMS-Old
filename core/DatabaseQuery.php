<?php

/**
 * DatabaseQuery.php
 *
 * The DatabaseQuery class.
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
 * Database Query class
 * @package core
 * @author Tim Visee
 */
class DatabaseQuery {

    private $query = '';

    /**
     * Reset the current built query
     */
    public function resetQuery() {
        $this->query = '';
    }
}