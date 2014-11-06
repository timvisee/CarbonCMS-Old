<?php

// Make sure the current PHP version is supported
if(version_compare(phpversion(), '5.3.1', '<'))
    // PHP version the server is running is not supported, show an error message
    // TODO: Show proper error message
    die('This server is running PHP ' . phpversion() . ', the required PHP version to start Carbon Core is PHP 5.3.1 or higher,
            please install PHP 5.3.1 or higher on your server!');

// Prevent direct requests to this file due to security reasons
defined('CARBON_SITE_ROOT') or die('Access denied!');

// Make sure Carbon Core isn't initialized already
if(defined('CARBON_CORE_INIT'))
    if(CARBON_CORE_INIT === true)
        return;

// Define some Carbon Core constants
/** Defines the root directory for Carbon Core */
define('CARBON_CORE_ROOT', __DIR__);
/** Define the version code of the current installed Carbon Core instance */
define('CARBON_CORE_VERSION_CODE', 1);
/** Define the version name of the current installed Carbon Core instance */
define('CARBON_CORE_VERSION_NAME', '0.1');

// Carbon Core initialized successfully, define the CARBON_CORE_INIT constant to store the initialization state
/** Defines whether Carbon Core is initialized successfully */
define('CARBON_CORE_INIT', true);