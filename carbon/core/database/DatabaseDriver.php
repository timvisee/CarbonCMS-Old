<?php

/**
 * Created by PhpStorm.
 * User: Tim
 * Date: 5-8-14
 * Time: 15:21
 */

namespace carbon\core\database;

use carbon\core\filesystem\FilesystemObject;

class DatabaseDriver {

    private $data = Array();

    public function __constructor($id = null, $name = null, $namespace = null, $version_code = null, $version_name = null) {

    }

    public static function loadFromDirectory($dir) {
        // Make sure the $dir param isn't null
        if($dir === null)
            return null;

        // Convert the $dir param into a File instance if possible, return null otherwise
        if(!$dir instanceof FilesystemObject) {
            if(!is_string($dir))
                return null;
            $dir = new FilesystemObject($dir);
        }

        // Get the driver file, and make sure this file exists
        $settingsFile = new FilesystemObject($dir, 'driver.ini');
        if(!$settingsFile->isFile())
            return null;

        // Read the settings file
        $settingsArr = parse_ini_file($settingsFile->getPath(), true);

        echo '<pre>';
        print_r($settingsArr);
        echo '</pre>';
        die();
    }
}
