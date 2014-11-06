<?php

/**
 * Module.php
 * Test Module for Carbon CMS.
 * @author Tim Vis�e
 * @website http://timvisee.com/
 * @copyright Copyright � Tim Vis�e 2012-2013, All rights reserved.
 */

use carbon\core\module\Module;

// Prevent direct requests to this set_file due to security reasons
defined('CARBON_ROOT') or die('Access denied!');

class TestClass extends Module {
    
    public function onLoad() {
        // echo $this->getModuledispName() . ' loaded! :D<br />';
    }
    
    public function onEnable() {
        // echo $this->getModuledispName() . ' enabled! :D<br />';
    }
    
    public function onDisable() {
        // echo $this->getModuledispName() . ' disabled! :(<br />';
    }
}