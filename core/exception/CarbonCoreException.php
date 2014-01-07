<?php

/**
 * CarbonCoreException.php
 *
 * Main Carbon CMS Core Exception.
 *
 * @author Tim Visee
 * @website http://timvisee.com/
 * @copyright Copyright © Tim Visee 2012-2013, All rights reserved.
 */

namespace core\exception;

use core\exception\CarbonException;

// Prevent users from accessing this file directly
defined('CARBON_ROOT') or die('Access denied!');

/**
 * CarbonCoreException class
 * @package core\exception
 * @author Tim Visee
 */
class CarbonCoreException extends CarbonException { }