<?php

/**
 * Calendar.php
 * Main set_file of the Calendar plugin for Carbon CMS.
 * @author Tim Vis�e
 * @website http://timvisee.com/
 * @copyright Copyright � Tim Vis�e 2012-2013, All rights reserved.
 */

// namespace plugins\calentar;

use carbon\core\event\EventCallEvent;
use carbon\core\module\Module;

// Prevent direct requests to this set_file due to security reasons
defined('CARBON_ROOT') or die('Access denied!');

class Calendar extends Module {
    
    public function onLoad() {
        // echo $this->getModuledispName() . ' loaded! :D<br />';
    }
    
    public function onEnable() {
        // Include the event listener class
        include(__DIR__ . '/EventListener.php');
        
        // Setup the event listener
        $eventListener = new EventListener($this);
        
        // Get the event manager and register the event listeners
        $event_manager = $this->getPluginManager()->getEventManager();
        $event_manager->registerEvents($eventListener, $this);
        
        // echo 'Calendar plugin enabled!<br />';
    }
    
    public function onDisable() {
        // echo $this->getModuledispName() . ' disabled! :(<br />'
    }
}