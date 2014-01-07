<?php

/**
 * CarbonConfigException.php
 *
 * Carbon CMS Config Exception class file
 *
 * @author Tim Visee
 * @website http://timvisee.com/
 * @copyright Copyright © Tim Visee 2012-2013, All rights reserved.
 */

namespace core\exception\config;

use core\exception\CarbonCoreException;

// Prevent users from accessing this file directly
defined('CARBON_ROOT') or die('Access denied!');

/**
 * CarbonConfigException class
 * @package core\exception
 * @author Tim Visee
 */
class CarbonConfigException extends CarbonCoreException { }