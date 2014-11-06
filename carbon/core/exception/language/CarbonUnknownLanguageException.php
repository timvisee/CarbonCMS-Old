<?php

/**
 * CarbonUnknownLanguageException.php
 *
 * @author Tim Visee
 * @website http://timvisee.com/
 * @copyright Copyright © Tim Visee 2012-2013, All rights reserved.
 */

namespace carbon\core\exception\language;

use carbon\core\exception\language\CarbonLanguageException;

// Prevent direct requests to this set_file due to security reasons
defined('CARBON_CORE_INIT') or die('Access denied!');

/**
 * CarbonUnknownLanguageException class
 * @package core\exception
 * @author Tim Visee
 */
class CarbonUnknownLanguageException extends CarbonLanguageException { }