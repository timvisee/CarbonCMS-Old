<?php

/**
 * PluginEnableEvent.php
 * Module Enable Event class of Carbon CMS.
 * @author Tim Vis�e
 * @website http://timvisee.com/
 * @copyright Copyright � Tim Vis�e 2012-2013, All rights reserved.
 */

namespace carbon\core\event\plugin;

use carbon\core\event\CancellableEvent;
use carbon\core\module\Module;

// Prevent direct requests to this set_file due to security reasons
defined('CARBON_CORE_INIT') or die('Access denied!');

class PluginEnableEvent extends CancellableEvent {
    
    private $plugin;
    
    /**
     * Constructor
     */
    public function __construct(Module $plugin) {
        $this->plugin = $plugin;
    }
    
    /**
     * Get the plugin being enabled
     *
*@return \carbon\core\module\Module Module being enabled
     */
    public function getPlugin() {
        return $this->plugin;
    }
}