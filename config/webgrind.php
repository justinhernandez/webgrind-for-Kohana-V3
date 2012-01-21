<?php defined('SYSPATH') or die('No direct access allowed.');

return array(

	/**
	 * Copy the media files from vendor/webgrind/img, js, styles and copy the
	 * files into publicly visible media folders
	 */
	'styleDir' => '/assets/css/webgrind/',
	'jsDir' => '/assets/js/webgrind/',
	'imgDir' => '/assets/images/webgrind/',
	
	/**
	 * Base url 
	 */
	'baseUrl' => '/webgrind',
	
	/**
	 * gprof2dot.py path 
	 */
	'gprof2dotPath' => MODPATH . 'webgrind/vendor/webgrind/library/gprof2dot.py',
	
	/**
	 * Automatically check if a newer version of webgrind is available for download
	 */
	'checkVersion' => true,
	'hideWebgrindProfiles' => true,
	
	/**
	 * Writable dir for information storage.
	 * If empty, will use system tmp folder or xdebug tmp
	 */
	'storageDir' => '',
	'profilerDir' => '/tmp',
	
	/**
	 * Suffix for preprocessed files
	 */
	'preprocessedSuffix' => '.webgrind',
	
	'defaultTimezone' => 'Europe/Copenhagen',
	'dateFormat' => 'Y-m-d H:i:s',
	// 'percent', 'usec' or 'msec'
	'defaultCostformat' => 'percent',
	'defaultFunctionPercentage' => 90,
	'defaultHideInternalFunctions' => false,
	
	/**
	 * Path to python executable
	 */ 
	'pythonExecutable' => '/opt/local/bin/python',
	
	/**
	 * Path to graphviz dot executable
	 */	
	'dotExecutable' => '/opt/local/bin/dot',
		
	/**
	 * sprintf compatible format for generating links to source files. 
	 * %1$s will be replaced by the full path name of the file
	 * %2$d will be replaced by the linenumber
	 */
	'fileUrlFormat' => 'webgrind?op=fileviewer&file=%1$s#line%2$d', // Built in fileviewer
	//static $fileUrlFormat = 'txmt://open/?url=file://%1$s&line=%2$d'; // Textmate
	//static $fileUrlFormat = 'file://%1$s'; // ?

    /**
     * format of the trace drop down list                                                                                                                                                      
     * default is: invokeurl (tracefile_name) [tracefile_size]
     * the following options will be replaced:
     *   %i - invoked url
     *   %f - trace file name
     *   %s - size of trace file
     *   %m - modified time of file name (in dateFormat specified above)
     */
	'traceFileListFormat' => '%i (%f) [%s]',

);
