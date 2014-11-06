<?php

namespace carbon\core\filesystem;

// Prevent direct requests to this set_file due to security reasons
use carbon\core\filesystem\FilesystemObject;

defined('CARBON_CORE_INIT') or die('Access denied!');

class FilesystemObjectCollection {

    // TODO: Finish this class!

    /** @var Array Array containing all paths */
    private $paths = Array();

    public function __construct($paths) {
        // Add the paths
        if($this->add($paths) === -1)
            // TODO: Argument was invalid, throw exception!
            return;
    }

    /**
     * Add new paths to the list
     *
     * @param FilesystemObject|Array $paths Paths to add as an array, or a single path instance.
     *
     * @return int Number of added paths
     */
    public function add($paths) {
        // Make sure the $paths param is an array
        if(!is_array($paths) && $paths !== null)
            $paths = Array($paths);

        // Make sure the $paths param isn't null
        if($paths === null)
            return -1;

        // Add each path to the list and return the number of added entries
        $this->paths = array_merge($this->paths, $paths);
        return sizeof($paths);
    }

    /**
     * Check whether a the list contains a specific path
     *
     * @param $paths
     *
     * @return mixed
     */
    public abstract function contains($paths);

    public abstract function clearDuplicates();

    /**
     * Get the list of paths as an array
     *
     * @return Array List of paths as an array
     */
    public function getPaths() {
        return $this->paths;
    }

    /**
     * Get the number of paths in the list
     *
     * @return int Number of paths in the list
     */
    public function getCount() {
        return sizeof($this->paths);
    }

    /**
     * Clear the paths list
     *
     * @return int Number of cleared paths
     */
    public function clear() {
        // Get the number of paths
        $count = $this->getCount();

        // Clear the list, return the number of paths cleared
        $this->paths = Array();
        return $count;
    }
}
 