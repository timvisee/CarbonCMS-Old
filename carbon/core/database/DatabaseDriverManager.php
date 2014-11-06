<?php

namespace carbon\core\database;

use carbon\core\filesystem\directory\Directory;
use carbon\core\filesystem\directory\DirectoryScanner;

// Prevent direct requests to this file due to security reasons
defined('CARBON_CORE_INIT') or die('Access denied!');

abstract class DatabaseDriverManager {

    /** @var \carbon\core\filesystem\directory\Directory Defines the root directory of all database drivers. */
    private $driverRoot;

    /**
     * Constructor
     *
     * @param \carbon\core\filesystem\directory\Directory|null $driverRoot [optional] Driver root directory as a directory instance or
     * the path of the root directory as a string. The default root directory will be used if no directory was supplied.
     */
    public function __construct($driverRoot = null) {
        // Set the driver root
        $this->setDriverRoot($driverRoot);

        // Refresh the available drivers
        $this->refresh();
    }

    /**
     * Refresh the list of available database drivers on the system.
     */
    public function refresh() {
        // TODO: Implement some form of caching!
        // Automatically register namespaces for drivers with different namespace structures

        // Make sure the driver directory is valid, if not return null
        if(!$this->driverRoot->isDirectory())
            return null;

        // Construct a directory scanner which will be used to list all available drivers
        $ds = new DirectoryScanner($this->driverRoot);
        $dirs = $ds->readAll(, null);

        // TODO: Check each directory for valid drivers!
        // TODO: Store the result
        // TODO: Return some status!
    }

    /**
     * Get the driver root directory.
     *
     * @return Directory Driver root directory.
     */
    public function getDriverRoot() {
        return $this->driverRoot;
    }

    /**
     * Set the driver root directory.
     *
     * @param null $driverRoot [optional] Driver root directory path.
     */
    public function setDriverRoot($driverRoot = null) {
        // Check whether the default driver root should be used
        if($driverRoot === null || !is_string($driverRoot))
            $driverRoot = __DIR__ . "/driver";

        // Set the driver root
        $this->driverRoot = $driverRoot;
    }

    /**
     * Get the list of available drivers. The driver list will be refreshed automatically as it's needed.
     *
     * @return Array|null A list of available database drivers, or null on failure.
     */
    public function getDrivers() {
        // TODO: Return a list of available drives
    }


    /**
     * Get the default root directory for database drivers.
     *
     * @return Directory The default root directory for database drivers.
     */
    public static function getDefaultDriverRootDirectory() {
        return new Directory(__DIR__ . '/driver');
    }
}