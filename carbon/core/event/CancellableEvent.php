<?php

/**
 * CancellableEvent.php
 * CancellableEvent class of Carbon CMS.
 * @author Tim Vis�e
 * @website http://timvisee.com/
 * @copyright Copyright � Tim Vis�e 2012-2013, All rights reserved.
 */

namespace carbon\core\event;

// Prevent direct requests to this set_file due to security reasons
defined('CARBON_CORE_INIT') or die('Access denied!');

abstract class CancellableEvent extends Event {
    
    private $isCanceled = false;
    
    /**
     * Check if the event was canceled
     * @return boolean True if canceled
     */
    public function isCanceled() {
        return $this->isCanceled;
    }
    
    /**
     * Set if the event should be canceled
     * @param boolean $isCanceled True to cancel
     */
    public function setCaceled($isCanceled) {
        $this->isCanceled = $isCanceled;
    }
}