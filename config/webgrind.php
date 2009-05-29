<?php defined('SYSPATH') or die('No direct script access.');

/* WEBGRIND OPTIONS */

/**
 * Shortcut for XDEBUG_PROFILE. i.e. http://localhost/?XDP
 *
 * DEFAULT: 'XDP'
 */

$config['shortcut'] = 'XDP';

/**
 * Allow webgrind in production mode?
 *
 * DEFAULT: FALSE
 */

$config['allow_production'] = FALSE;

/**
 * Reuse the same window when profiling. Will not open multiple webgrind windows.
 *
 * DEFAULT: TRUE
 */

$config['reuse_window'] = TRUE;


/* WEBGRIND VENDOR OPTIONS */


$config['checkVersion'] = false;
$config['hideWebgrindProfiles'] = true;

/**
* Writable dir for information storage.
* If empty, will use system tmp folder or xdebug tmp
*/
$config['storageDir'] = '';
$config['profilerDir'] = '/tmp';

/**
* Suffix for preprocessed files
*/
$config['preprocessedSuffix'] = '.webgrind';
$config['defaultTimezone'] = 'Europe/Copenhagen';
$config['dateFormat'] = 'Y-m-d H:i:s';
$config['defaultCostformat'] = 'percent'; // 'percent', 'usec' or 'msec'
$config['defaultFunctionPercentage'] = 90;
$config['defaultHideInternalFunctions'] = false;

/**
* sprintf compatible format for generating links to source files. 
* %1$s will be replaced by the full path name of the file
* %2$d will be replaced by the linenumber
*/
$config['fileUrlFormat'] = webgrind::url().'?op=fileviewer&file=%1$s&line=%2$d'; // Built in fileviewer
