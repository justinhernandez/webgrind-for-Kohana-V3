<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * Webgrind Controller
 *
 * @package			Webgrind
 * @author			Justin Hernandez <justin@transphorm.com>
 */
class Controller_WebGrind extends Controller {

	public function action_index()
	{
		if ( Kohana::$environment !== Kohana::PRODUCTION)
			require Kohana::find_file('vendor/webgrind', 'index');
		
		die();
	}
	
}