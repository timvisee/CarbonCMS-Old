<?php

/**
 * CarbonRegistryException.php
 *
 * Carbon CMS Registry Exception class file
 *
 * @author Tim Visee
 * @website http://timvisee.com/
 * @copyright Copyright © Tim Visee 2012-2013, All rights reserved.
 */

namespace core\exception\registry;

use core\exception\CarbonCoreException;

// Prevent users from accessing this file directly
defined('CARBON_ROOT') or die('Access denied!');

/**
 * CarbonRegistryException class
 * @package core\exception
 * @author Tim Visee
 */
class CarbonRegistryException extends CarbonCoreException { }