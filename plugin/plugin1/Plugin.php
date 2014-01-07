<?php

/**
 * Plugin.php
 * Test Plugin for Carbon CMS.
 * @author Tim Vis�e
 * @website http://timvisee.com/
 * @copyright Copyright � Tim Vis�e 2012-2013, All rights reserved.
 */

use core\plugin\Plugin;

// Prevent users from accessing this file directly
defined('CARBON_ROOT') or die('Access denied!');

class TestClass extends Plugin {
    
    public function onLoad() {
        // echo $this->getPluginDisplayName() . ' loaded! :D<br />';
    }
    
    public function onEnable() {
        // echo $this->getPluginDisplayName() . ' enabled! :D<br />';
    }
    
    public function onDisable() {
        // echo $this->getPluginDisplayName() . ' disabled! :(<br />';
    }
}