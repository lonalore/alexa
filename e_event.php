<?php

/**
 * @file
 *
 */

if(!defined('e107_INIT'))
{
	exit;
}


/**
 * Class alexa_event.
 */
class alexa_event
{

	/**
	 * Configure functions/methods to run when specific e107 events are triggered.
	 *
	 * @return array
	 */
	function config()
	{
		$event = array();

		// After a plugin is installed.
		$event[] = array(
			'name'     => "admin_plugin_install",
			'function' => "updateAddonList",
		);

		// After a plugin is uninstalled.
		$event[] = array(
			'name'     => "admin_plugin_uninstall",
			'function' => "updateAddonList",
		);

		// After a plugin is upgraded.
		$event[] = array(
			'name'     => "admin_plugin_upgrade",
			'function' => "updateAddonList",
		);

		// Plugin information is updated.
		$event[] = array(
			'name'     => "admin_plugin_refresh",
			'function' => "updateAddonList",
		);

		return $event;

	}

	/**
	 * Callback function to update metatag addon list.
	 */
	function updateAddonList()
	{
		e107_require_once(e_PLUGIN . 'alexa/includes/alexa.class.php');

		$alexa = new Alexa();
		$alexa->updateAddonList();
	}

}
