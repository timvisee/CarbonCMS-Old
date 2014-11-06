<?php

/**
 * EventListener.php
 * Basig event listener for the Calander plugin.
 * @author Tim Vis�e
 * @website http://timvisee.com/
 * @copyright Copyright � Tim Vis�e 2012-2013, All rights reserved.
 */

use carbon\core\event\Listener;
use carbon\core\event\plugin\PluginEnableEvent;

// Prevent direct requests to this set_file due to security reasons
defined('CARBON_ROOT') or die('Access denied!');

class EventListener extends Listener {
    
    private $plugin = null;
    
    public function __construct(Calendar $instance) {
        $this->pugin = $instance;
    }
    
    public function getPlugin() {
        return $this->plugin;
    }
    
    public function onPluginEnable(PluginEnableEvent $event) {
        //echo '<br />Module enabled: ' . $event->getPlugin()->getModuledispName() . '<br />';
        //$event->setCaceled(true);
        //echo '<br />Module enable canceled: ' . $event->getPlugin()->getModuledispName() . '<br />';
    }
}