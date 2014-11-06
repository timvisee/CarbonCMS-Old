<?php

namespace carbon\core\filesystem;

use carbon\core\filesystem\directory\Directory;

// Prevent direct requests to this set_file due to security reasons
defined('CARBON_CORE_INIT') or die('Access denied!');

class FilesystemObjectLibrary {

    /**
     * Get the Carbon Core root directory.
     *
     * @return \carbon\core\filesystem\directory\Directory Carbon Core root directory.
     */
    public static function getCarbonCoreDirectory() {
        return new Directory(CARBON_CORE_ROOT);
    }

    /**
     * Get the systems root directory.
     *
     * @return Directory Systems root directory.
     */
    public static function getRootDirectory() {
        return new Directory('/');
    }

    /**
     * Get PHPs current working directory.
     *
     * @return \carbon\core\filesystem\directory\Directory Returns PHPs current working directory.
     */
    public static function getCurrentWorkingDirectory() {
        return new Directory(getcwd());
    }

    /**
     * Get the home directory for the current user.
     * Warning: This method is only compatible with a few systems thus the default value is often returned.
     *
     * @param mixed|null $default [optional] The default value to be returned on failure.
     *
     * @return \carbon\core\filesystem\directory\Directory|null|string The home directory of the current user, or the $default value on failure.
     */
    public static function getHomeDirectory($default = null) {
        // Check whether the home path on linux/unix systems is available in the server variables
        if(isset($_SERVER['HOME']))
            return new Directory($_SERVER['HOME']);

        // Get the home from the environment variables
        if(($home = getenv('HOME')) !== false)
            return new Directory($home);

        // Check whether the home path on windows system is available in the server variables
        if(!empty($_SERVER['HOMEDRIVE']) && !empty($_SERVER['HOMEPATH']))
            return $_SERVER['HOMEDRIVE'] . $_SERVER['HOMEPATH'];

        // Home path couldn't be determined, return the default value
        return $default;
    }
}
 