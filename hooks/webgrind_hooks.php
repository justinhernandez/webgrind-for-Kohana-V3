<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * comments
 *
 * @package			package
 * @author			Justin Hernandez <justin@transphorm.com>
 * @copyright		2009
 */
class Webgrind_Hooks
{

	/**
	 * Detects XDEBUG_PROFILE shortcut as specified in config.
	 */
	public function detect_shortcut()
	{
		$shortcut = Kohana::config('webgrind.shortcut');
			
		// search url for shortcut
		if (preg_match("/(\?|&)$shortcut(&|=|$)/", Router::$query_string))
		{
			$url = str_replace($shortcut, 'XDEBUG_PROFILE', url::current(TRUE));
			url::redirect($url, 307);
		}
	}

	/**
	 * Appends a javascript redirect if to webgrind if XDEBUG_PROFILE is present.
	 */
	public function webgrind_redirect()
	{
		//  check for XDEBUG_PROFILE
		if (strpos(Router::$query_string, 'XDEBUG_PROFILE'))
		{
			$js = self::js_new_window();
			// check for body tag and insert js after that or if no body tag
			// then prepend it to view data.
			if ($pos = stripos(Event::$data, '<body>'))
			{
				$replace = substr(Event::$data, $pos, 6).$js;
				Event::$data = substr_replace(Event::$data, $replace, $pos, 6);
			}
			else
			{
				Event::$data = $js.Event::$data;
			}
		}
	}
	
	/**
	 * Check config and alert methods to disable webgrind if necessary.
	 *
	 * @param   param
	 * @return  return
	 */
	public static function init()
	{
		// check for production mode and check config if webgridn allowed.
		// disable if necessary
		$allow = ((IN_PRODUCTION) AND ( ! Kohana::config('webgrind.allow_production')))
				? FALSE
				: TRUE;
		
		// if $allow is true then call hooks method
		if ($allow) self::hooks();
	}
	
	/**
	 * All webgrind hooks are enabled in this method
	 */
	private static function hooks()
	{
		// check for shortcut in url
		Event::add('system.post_routing', array('Webgrind_Hooks', 'detect_shortcut'));
		// hook for javascript redirect
		Event::add('system.display', array('Webgrind_Hooks', 'webgrind_redirect'));
		// webgrind path constant
		define('WEBGRIND', url::site().str_replace(DOCROOT, '', MODPATH).'webgrind/');
	}
	
	/**
	 * Open up a new webgrind window using js
	 * 
	 * @return  string
	 */
	private static function js_new_window()
	{
		// autoload newest cache grind file
		$url = url::base().'webgrind?autoload_latest=1';
		
		// reuse webgrind window?
		if (Kohana::config('webgrind.reuse_window'))
		{
			$js = "<script type='text/javascript'>window.open('$url', 'webgrind');</script>";
		}
		else
		{
			$js = "<script type='text/javascript'>window.open('$url');</script>";
		}
		
		return $js;
	}
}

// call hook check method
Webgrind_Hooks::init();
