<?php

namespace carbon\core\database\query\builder;

// Prevent direct requests to this file due to security reasons
defined('CARBON_CORE_INIT') or die('Access denied!');

class DatabaseQueryBuilderSelect {

    private $from = Array();

    public function setFrom($from = Array()) {
        if(is_array($from)) {
            $this->from = $from;
            return;
        }

        if(is_string($from)) {
            $this->from = Array($from);
            return;
        }

        if($from === null)
            $this->from = Array();
    }

    public function getQuery() {
        $query_parts = Array('SELECT');

        $query_parts[] = '*';

        // Add the FROM part
        $query_parts[] = 'FROM';
        $query_parts[] = implode(', ', $this->from);

        return implode(' ', $query_parts);
    }
}
