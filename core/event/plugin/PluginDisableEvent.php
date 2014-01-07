<?php

/**
 * PluginDisableEvent.php
 * Plugin Disable Event class of Carbon CMS.
 * @author Tim Vis�e
 * @website http://timvisee.com/
 * @copyright Copyright � Tim Vis�e 2012-2013, All rights reserved.
 */

namespace core\event\plugin;

use core\event\Event;
use core\plugin\Plugin;

// Prevent users from accessing this file directly
defined('CARBON_ROOT') or die('Access denied!');

class PluginDisableEvent extends Event {
    
    private $plugin;
    
    /**
     * Constructor
     */
    public function __construct(Plugin $plugin) {
        $this->plugin = $plugin;
    }
    
    /**
     * Get the plugin being disabled
     * @return Plugin Plugin being disabled
     */
    public function getPlugin() {
        return $this->plugin;
    }
}