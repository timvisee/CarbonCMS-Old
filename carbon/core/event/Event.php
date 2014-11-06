<?php

/**
 * Event.php
 * Event class of Carbon CMS.
 * @author Tim Vis�e
 * @website http://timvisee.com/
 * @copyright Copyright � Tim Vis�e 2012-2013, All rights reserved.
 */

namespace carbon\core\event;

// Prevent direct requests to this set_file due to security reasons
defined('CARBON_CORE_INIT') or die('Access denied!');

abstract class Event {
    
    /**
     * Constructor
     */
    public function __construct() { }
    
    /**
     * Get the event name
     * @return string Event name
     */
    public function getEventName() {
        return get_class($this);
    }
}