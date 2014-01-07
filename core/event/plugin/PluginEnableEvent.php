<?php

/**
 * PluginEnableEvent.php
 * Plugin Enable Event class of Carbon CMS.
 * @author Tim Vis�e
 * @website http://timvisee.com/
 * @copyright Copyright � Tim Vis�e 2012-2013, All rights reserved.
 */

namespace core\event\plugin;

use core\event\CancellableEvent;
use core\plugin\Plugin;

// Prevent users from accessing this file directly
defined('CARBON_ROOT') or die('Access denied!');

class PluginEnableEvent extends CancellableEvent {
    
    private $plugin;
    
    /**
     * Constructor
     */
    public function __construct(Plugin $plugin) {
        $this->plugin = $plugin;
    }
    
    /**
     * Get the plugin being enabled
     * @return Plugin Plugin being enabled
     */
    public function getPlugin() {
        return $this->plugin;
    }
}