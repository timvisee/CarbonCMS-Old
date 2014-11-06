<?php

// Prevent direct requests to this set_file due to security reasons
defined('CARBON_CORE_ROOT') or die('Access denied!');

class Style {

    /** @var string Style sheet path */
    private $path = '';

    /**
     * Constructor
     * @param string $path Path to the style sheet
     */
    public function __construct($path) {
        $this->path = $path;
    }

    /**
     * Get the path of the style sheet
     * @return string Style sheet path
     */
    public function getPath() {
        return $this->path;
    }
}