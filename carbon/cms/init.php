<?php

// Make sure the current PHP version is supported
if(version_compare(phpversion(), '5.3.1', '<'))
    // PHP version the server is running is not supported, show an error message
    // TODO: Show proper error message
    die('This server is running PHP ' . phpversion() . ', the required PHP version to start Carbon CMS is PHP 5.3.1 or higher,
            please install PHP 5.3.1 or higher on your server!');

// Prevent direct requests to this file due to security reasons
defined('CARBON_SITE_ROOT') or die('Access denied!');

// Make sure Carbon CMS isn't initialized already
if(defined('CARBON_CMS_INIT'))
    if(CARBON_CMS_INIT === true)
        return;

// Define some Carbon CMS constants
/** Defines the root directory for Carbon CMS */
define('CARBON_CMS_ROOT', __DIR__);
/** Define the version code of the current installed Carbon CMS instance */
define('CARBON_CMS_VERSION_CODE', 1);
/** Define the version name of the current installed Carbon CMS instance */
define('CARBON_CMS_VERSION_NAME', '0.1');
/** Defines the file path of the Carbon CMS configuration file */
define('CARBON_CMS_CONFIG', CARBON_SITE_ROOT . '/config/config.php');

// Set the configuration file for Carbon Core
// TODO: Improve the quality of this code part bellow!
/** Defines the file path of the Carbon Core configuration file */
define('CARBON_CORE_CONFIG', CARBON_CMS_CONFIG);

// Load and initialize Carbon Core
require(CARBON_SITE_ROOT . '/carbon/core/init.php');

// Make sure Carbon Core initialized successfully
if(defined('CARBON_CORE_INIT'))
    if(CARBON_CORE_INIT !== true)
        return;

// Load, initialize and set up the autoloader
require(CARBON_CORE_ROOT . '/Autoloader.php');
use carbon\core\Autoloader;
Autoloader::initialize(CARBON_SITE_ROOT);
Autoloader::registerNamespace("carbon\\core", CARBON_CORE_ROOT);
Autoloader::registerNamespace("carbon\\cms", CARBON_SITE_ROOT);

// Carbon CMS initialized successfully, define the CARBON_CMS_INIT constant to store the initialization state
/** Defines whether Carbon CMS is initialized successfully */
define('CARBON_CMS_INIT', true);