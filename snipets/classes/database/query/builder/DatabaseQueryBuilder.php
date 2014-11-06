<?php

namespace carbon\core\database\query\builder;

// Prevent direct requests to this file due to security reasons
defined('CARBON_CORE_INIT') or die('Access denied!');

abstract class DatabaseQueryBuilder {

    abstract public function resetQuery();

}