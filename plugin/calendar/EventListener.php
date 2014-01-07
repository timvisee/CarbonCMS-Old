<?php

/**
 * EventListener.php
 * Basig event listener for the Calander plugin.
 * @author Tim Vis�e
 * @website http://timvisee.com/
 * @copyright Copyright � Tim Vis�e 2012-2013, All rights reserved.
 */

use core\event\Listener;
use core\event\plugin\PluginEnableEvent;

// Prevent users from accessing this file directly
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
        //echo '<br />Plugin enabled: ' . $event->getPlugin()->getPluginDisplayName() . '<br />';
        //$event->setCaceled(true);
        //echo '<br />Plugin enable canceled: ' . $event->getPlugin()->getPluginDisplayName() . '<br />';
    }
}