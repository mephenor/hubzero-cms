<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

/*
|--------------------------------------------------------------------------
| Define directories
|--------------------------------------------------------------------------
|
| First thing we need to do is set some constants for the app's directory
| and the path to the parent directory containing the app and core.
|
*/

// Set some needed defines
define('PATH_APP', __DIR__);
define('PATH_ROOT', dirname(__DIR__));
define('DS', DIRECTORY_SEPARATOR);

// Load the webroot application
require_once PATH_ROOT . DS . 'index.php';
