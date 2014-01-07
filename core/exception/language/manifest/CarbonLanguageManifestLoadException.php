<?php

/**
 * CarbonLanguageManifestLoadException.php
 *
 * @author Tim Visee
 * @website http://timvisee.com/
 * @copyright Copyright © Tim Visee 2012-2013, All rights reserved.
 */

namespace core\exception\language\manifest;

use core\exception\language\manifest\CarbonLanguageManifestException;
use core\exception\language\manifest;

// Prevent users from accessing this file directly
defined('CARBON_ROOT') or die('Access denied!');

/**
 * CarbonLanguageManifestLoadException class
 * @package core\exception
 * @author Tim Visee
 */
class CarbonLanguageManifestLoadException extends manifest\CarbonLanguageManifestException { }