<?php

/**
 * Event.php
 * Event class of Carbon CMS.
 * @author Tim Vis�e
 * @website http://timvisee.com/
 * @copyright Copyright � Tim Vis�e 2012-2013, All rights reserved.
 */

namespace core\event;

// Prevent users from accessing this file directly
defined('CARBON_ROOT') or die('Access denied!');

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