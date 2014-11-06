<?php

namespace carbon\core\filesystem;

// Prevent direct requests to this set_file due to security reasons
defined('CARBON_CORE_INIT') or die('Access denied!');

class FilesystemObjectArrayUtils {

    // TODO: Allow arrays of objects as parameters!
    public static function move($paths, $targets, $recursive = false) {
        // Convert $paths and $targets into a string array of paths, return the $default value if failed
        if(($paths = self::asPathArray($paths)) === null)
            return false;
        if(($targets = self::asPathArray($targets)) === null)
            return false;

        // Keep track of the number of moved files
        $moved = 0;

        // Count the number of paths and targets
        $pathsCount = sizeof($paths);
        $targetsCount = sizeof($targets);

        // Make sure there's only one target, or that the number of targets equals the number of paths
        if($pathsCount != $targetsCount && $targetsCount != 1)
            return false;

        // Validate all paths and targets
        if(!self::isValid($paths, true) || !self::isValid($targets, true))
            return false;

        // Loop through all the files which should be moved
        for($i = 0; $i < $pathsCount; $i++) {
            // Get the corresponding path and target
            $path = $paths[$i];
            $target = $targetsCount > 1 ? $targets[$i] : $targets[0];

            // Check whether the $path is a directory
            if(self::isDirectory($path)) {
                // Make sure recursive mode is enabled
                if(!$recursive)
                    return false;


            }


        }
    }
}
 