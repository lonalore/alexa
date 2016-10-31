<?php

/**
 * @file
 * Simple mod-rewrite module.
 */

if(!defined('e107_INIT'))
{
	exit;
}


/**
 * Class alexa_url.
 */
class alexa_url
{

	/**
	 * Provides information about mod-rewrite rules.
	 *
	 * @return array $config
	 */
	function config()
	{
		$config = array();

		// Alexa endpoint, with query parameters.
		$config['endpoint/?'] = array(
			// Matched against url, and if true, redirected to 'redirect' below.
			'regex'    => '^alexa/endpoint/?\?(.*)$',
			// File-path of what to load when the regex returns true.
			'redirect' => '{e_PLUGIN}alexa/endpoint.php?$1',
		);

		// Alexa endpoint.
		$config['endpoint'] = array(
			// Matched against url, and if true, redirected to 'redirect' below.
			'regex'    => '^alexa/endpoint/?(.*)$',
			// Used by e107::url(); to create a url from the db table.
			'sef'      => 'alexa/endpoint$',
			// File-path of what to load when the regex returns true.
			'redirect' => '{e_PLUGIN}alexa/endpoint.php',
		);

		return $config;
	}

}
