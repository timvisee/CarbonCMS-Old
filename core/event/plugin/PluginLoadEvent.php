<?php

/**
 * PluginLoadEvent.php
 * Plugin Load Event class of Carbon CMS.
 * @author Tim Vis�e
 * @website http://timvisee.com/
 * @copyright Copyright � Tim Vis�e 2012-2013, All rights reserved.
 */

namespace core\event\plugin;

use core\event\CancellableEvent;

// Prevent users from accessing this file directly
defined('CARBON_ROOT') or die('Access denied!');

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
     * @return string Plugin name being loaded
     */
    public function getPluginName() {
        return $this->plugin_name;
    }
}