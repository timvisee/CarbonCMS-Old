<?php

/**
 * CarbonLanguageManifestException.php
 *
 * @author Tim Visee
 * @website http://timvisee.com/
 * @copyright Copyright © Tim Visee 2012-2013, All rights reserved.
 */

namespace core\exception\language\manifest;

use core\exception\language\CarbonLanguageException;

// Prevent users from accessing this file directly
defined('CARBON_ROOT') or die('Access denied!');

/**
 * CarbonLanguageManifestException class
 * @package core\exception
 * @author Tim Visee
 */
class CarbonLanguageManifestException extends CarbonLanguageException {

    private $manifest_file;

    /**
     * Constructor
     * @param string $message [optional] Exception message
     * @param int $code [optional] Exception code
     * @param \Exception $previous [optional] Previous chained exception
     * @param string|array|null $solutions [optional] $solution String or array with possible solutions
     * @param string|null $manifest_file Path to the manifest file which couldn't be loaded
     */
    public function __construct($message = "", $code = 0, \Exception $previous = null, $solutions = null, $manifest_file = null) {
        // Store the manifest file
        $this->manifest_file = $manifest_file;

        // Construct the parent
        parent::__construct($message, $code, $previous, $solutions);
    }

    /**
     * Get the path to the manifest file that couldn't be loaded
     * @return null|string Path to the manifest file, might return null if no file was set.
     */
    public function getManifestFile() {
        return $this->manifest_file;
    }
}