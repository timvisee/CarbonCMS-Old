<?php

/**
 * PluginDisableEvent.php
 * Module Disable Event class of Carbon CMS.
 * @author Tim Vis�e
 * @website http://timvisee.com/
 * @copyright Copyright � Tim Vis�e 2012-2013, All rights reserved.
 */

namespace carbon\core\event\plugin;

use carbon\core\event\Event;
use carbon\core\module\Module;

// Prevent direct requests to this set_file due to security reasons
defined('CARBON_CORE_INIT') or die('Access denied!');

class PluginDisableEvent extends Event {
    
    private $plugin;
    
    /**
     * Constructor
     */
    public function __construct(\carbon\core\module\Module $plugin) {
        $this->plugin = $plugin;
    }
    
    /**
     * Get the plugin being disabled
     * @return Module Module being disabled
     */
    public function getPlugin() {
        return $this->plugin;
    }
}