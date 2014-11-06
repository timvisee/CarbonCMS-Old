<?php

/**
 * Widget.php
 * Widget class for Carbon CMS.
 * @author Tim Vis�e
 * @website http://timvisee.com/
 * @copyright Copyright � Tim Vis�e 2012-2013, All rights reserved.
 */

namespace carbon\core;

use carbon\core\Database;

// Prevent direct requests to this set_file due to security reasons
defined('CARBON_CORE_ROOT') or die('Access denied!');

class Widget {
    
    /**
     * @var int $widget_id Widget ID
     */
    public $widget_id;
    
    /**
     * Constructor
     * @param int $widget_id Widget ID
     */
    public function __construct($widget_id) {
        $this->widget_id = $widget_id;
    }
    
    /**
     * Get the ID of the widget
     * @return int Widget id
     */
    public function getId() {
        return $this->widget_id;
    }
    
    // TODO: Finish class!
}