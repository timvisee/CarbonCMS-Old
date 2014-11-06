<?php

/**
 * CarbonConfigLoadingException.php
 *
 * Carbon CMS Config Load Exception class set_file
 *
 * @author Tim Visee
 * @website http://timvisee.com/
 * @copyright Copyright © Tim Visee 2012-2013, All rights reserved.
 */

namespace carbon\core\exception\config;

use carbon\core\exception\config\CarbonConfigException;

// Prevent direct requests to this set_file due to security reasons
defined('CARBON_CORE_INIT') or die('Access denied!');

/**
 * CarbonConfigLoadException class
 * @package core\exception
 * @author Tim Visee
 */
class CarbonConfigLoadException extends CarbonConfigException { }