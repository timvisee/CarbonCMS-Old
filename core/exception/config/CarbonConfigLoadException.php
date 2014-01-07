<?php

/**
 * CarbonConfigLoadingException.php
 *
 * Carbon CMS Config Load Exception class file
 *
 * @author Tim Visee
 * @website http://timvisee.com/
 * @copyright Copyright © Tim Visee 2012-2013, All rights reserved.
 */

namespace core\exception\config;

use core\exception\config\CarbonConfigException;

// Prevent users from accessing this file directly
defined('CARBON_ROOT') or die('Access denied!');

/**
 * CarbonConfigLoadException class
 * @package core\exception
 * @author Tim Visee
 */
class CarbonConfigLoadException extends CarbonConfigException { }