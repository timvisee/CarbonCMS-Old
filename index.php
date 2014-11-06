<?php

echo '<pre>CARBON CMS v0.1 Pre Alpha<br />-------------------------<br /><br />';

/**
 * index.php
 *
 * This file handles all page requests to the website in the current directory.
 * This file initializes Carbon CMS and starts the bootstrap.
 *
 * @author Tim Visee
 * @version 0.1
 * @website http://timvisee.com/
 * @copyright Copyright (C) Tim Visee 2012-2014. All rights reserved.
 */

// Define some constants
/** Defines the root directory for the website */
define('CARBON_SITE_ROOT', __DIR__);

// Load and initialize Carbon CMS
require(CARBON_SITE_ROOT . '/carbon/cms/init.php');

// Load, construct and initialize the Bootstrap
use carbon\cms\Bootstrap;
$bootstrap = new Bootstrap(true);

// Stop the Bootstrap
$bootstrap->shutdown();