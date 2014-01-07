<?php

/**
 * index.php
 *
 * This file handles all page requests on the website in the Carbon CMS directory.
 * This file does a version check, sets the CARBON_ROOT constant, initializes the AutoLoader and start's the bootstrap.
 *
 * @author Tim Visee
 * @version 0.1
 * @website http://timvisee.com/
 * @copyright Copyright (C) Tim Visee 2012-2013, All rights reserved.
 */

// Check whether the server's PHP version is supported or not
if(version_compare(phpversion(), '5.3.1', '<')) {
    // PHP version the server is running is not supported, show an error message
    // TODO: Show proper error message
    die('This server is running PHP '.phpversion().', the required PHP version to run Carbon CMS is PHP 5.3.1 or higher,
            please install PHP 5.3.1 or higher on your server!');
}

// Define a named constant with the root directory of the Carbon installation
define('CARBON_ROOT', __DIR__);

// Import and initialize the AutoLoader
require(CARBON_ROOT . DIRECTORY_SEPARATOR . 'core/AutoLoader.php');
use core\AutoLoader;
AutoLoader::init();

// Import the Bootstrap class
use core\Bootstrap;

// Construct and initialize the Bootstrap
$bootstrap = new Bootstrap(true);

// Stop the Bootstrap
$bootstrap->shutdown();