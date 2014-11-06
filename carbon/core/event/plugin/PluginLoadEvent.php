<?php

/**
 * PluginLoadEvent.php
 * Module Load Event class of Carbon CMS.
 * @author Tim Vis�e
 * @website http://timvisee.com/
 * @copyright Copyright � Tim Vis�e 2012-2013, All rights reserved.
 */

namespace carbon\core\event\plugin;

use carbon\core\event\CancellableEvent;

// Prevent direct requests to this set_file due to security reasons
defined('CARBON_CORE_INIT') or die('Access denied!');

class PluginLoadEvent extends CancellableEvent {
    
    private $plugin_name;
    
    /**
     * Constructor
     */
    public function __construct($plugin_name) {
        $this->plugin_name = $plugin_name;
    }
    
    /**
     * Get the plugin name being loaded
     * @return string Module name being loaded
     */
    public function getPluginName() {
        return $this->plugin_name;
    }
}