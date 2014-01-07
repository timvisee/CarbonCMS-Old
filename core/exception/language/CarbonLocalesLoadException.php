<?php

/**
 * CarbonLocalesLoadException.php
 *
 * @author Tim Visee
 * @website http://timvisee.com/
 * @copyright Copyright © Tim Visee 2012-2013, All rights reserved.
 */

namespace core\exception\language;

use core\exception\language\CarbonLocaleException;

// Prevent users from accessing this file directly
defined('CARBON_ROOT') or die('Access denied!');

/**
 * CarbonLocalesLoadException class
 * @package core\exception
 * @author Tim Visee
 */
class CarbonLocalesLoadException extends CarbonLocaleException { }