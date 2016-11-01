<?php

/**
 * @file
 * Main class for Alexa plugin.
 */


if(!defined('e107_INIT'))
{
	exit;
}

e107_require_once(e_PLUGIN . 'alexa/vendor/autoload.php');

use Alexa\Request\Certificate;
use Alexa\Request\Request;

/**
 * Class Alexa.
 */
class Alexa
{

	/**
	 * Application ID.
	 *
	 * @var mixed
	 */
	private $appID;

	/**
	 * Contains a list about plugins, which has e_alexa.php addon file.
	 *
	 * @var array
	 */
	private $addonList = array();

	/**
	 * Alexa constructor.
	 */
	public function __construct()
	{
		$prefs = e107::getPlugConfig('alexa')->getPref();
		$this->appID = varset($prefs['app_id'], '');
		$this->addonList = varset($prefs['addon_list'], array());
	}

	/**
	 * Helper function to get enabled plugins.
	 *
	 * @return array
	 *  Contains enabled plugins.
	 */
	public function getEnabledPlugins()
	{
		$sql = e107::getDb();

		$enabledPlugins = array();

		// Get list of enabled plugins.
		$sql->select("plugin", "*", "plugin_id !='' order by plugin_path ASC");
		while($row = $sql->fetch())
		{
			if($row['plugin_installflag'] == 1)
			{
				$enabledPlugins[] = $row['plugin_path'];
			}
		}

		return $enabledPlugins;
	}

	/**
	 * Update addon list.
	 */
	public function updateAddonList()
	{
		$fl = e107::getFile();

		$plugList = $fl->get_files(e_PLUGIN, "^plugin\.(php|xml)$", "standard", 1);
		$pluginList = array();
		$addonsList = array();

		// Remove Duplicates caused by having both plugin.php AND plugin.xml.
		foreach($plugList as $num => $val)
		{
			$key = basename($val['path']);
			$pluginList[$key] = $val;
		}

		foreach($pluginList as $p)
		{
			$p['path'] = substr(str_replace(e_PLUGIN, '', $p['path']), 0, -1);
			$plugin_path = $p['path'];

			if(is_readable(e_PLUGIN . $plugin_path . '/e_alexa.php'))
			{
				$addonsList[] = $plugin_path;
			}
		}

		e107::getPlugConfig('alexa')->set('addon_list', $addonsList)->save(false);
	}

	/**
	 * Performs the actual request handling for the Alexa endpoint.
	 */
	public function requestHandler()
	{
		// Return a failure response if not a POST request.
		if('POST' != $_SERVER['REQUEST_METHOD'])
		{
			if(E107_DEBUG_LEVEL > 0)
			{
				e107::getDebug()->log('Not a POST request.');
			}

			header("HTTP/1.0 405 Method Not Allowed");
			exit;
		}

		// Get raw request data.
		$data = file_get_contents("php://input");
		if(empty($data))
		{
			if(E107_DEBUG_LEVEL > 0)
			{
				e107::getDebug()->log('Empty data.');
			}

			header("HTTP/1.0 400 Bad Request");
			exit;
		}

		try
		{
			// Process the raw data into an Alexa Request.
			$request = $this->parseRequest($data);

			// Alexa Response.
			$response = null;

			$enabledPlugins = $this->getEnabledPlugins();

			// Invoke hooks until we get a Response.
			foreach($this->addonList as $plugin)
			{
				if(!in_array($plugin, $enabledPlugins))
				{
					continue;
				}

				$file = e_PLUGIN . $plugin . '/e_alexa.php';
				if(!is_readable($file))
				{
					continue;
				}

				e107_require_once($file);
				$addonClass = $plugin . '_alexa';

				if(!class_exists($addonClass))
				{
					continue;
				}

				$class = new $addonClass();

				if(!method_exists($class, 'config'))
				{
					continue;
				}

				$response = $class->config($request);

				// Only return the first response.
				if($response && !empty($response->outputSpeech))
				{
					break;
				}
			}

			// Output the response.
			if($response && !empty($response->outputSpeech))
			{
				header("HTTP/1.0 200 OK");
				e107::getAjax()->response($response->render());
			}
			else
			{
				if(E107_DEBUG_LEVEL > 0)
				{
					e107::getDebug()->log('No response handler.');
				}

				header("HTTP/1.0 500 Internal Server Error");
			}
		} catch(Exception $e)
		{
			if(E107_DEBUG_LEVEL > 0)
			{
				$message = $e->getMessage();
				e107::getDebug()->log($message);
			}

			header("HTTP/1.0 500 Internal Server Error");
		}

		exit;
	}

	/**
	 * Parses the raw request content into an Alexa Request object.
	 *
	 * @param string $data
	 *   The raw request content.
	 *
	 * @return \Alexa\Request\Request
	 *   The Alexa Request.
	 *
	 * @throws RuntimeException
	 * @throws InvalidArgumentException
	 */
	public function parseRequest($data)
	{
		$certchain = $_SERVER['HTTP_SIGNATURECERTCHAINURL'];
		$signature = $_SERVER['HTTP_SIGNATURE'];

		$certificate = new AlexaCachedCertificate($certchain, $signature);

		$request = new Request($data, $this->appID);
		$request->setCertificateDependency($certificate);

		return $request->fromData();
	}

}


/**
 * Extends \Alexa\Request\Certificate to add caching.
 *
 * Extends the default Amazon Alexa App library Certificate class to allow
 * e107-based caching of the downloaded Amazon certificate.
 */
class AlexaCachedCertificate extends Certificate
{

	/**
	 * {@inheritdoc}
	 */
	public function getCertificate()
	{
		$cid = 'alexa:certificate:' . $this->certificateUrl;

		$cache = e107::getCache();
		$cached = $cache->retrieve($cid, false, true, true);

		if($cached)
		{
			$certificate = unserialize(base64_decode($cached));
		}
		else
		{
			$certificate = $this->fetchCertificate();

			$cacheData = base64_encode(serialize($certificate));
			$cache->set($cid, $cacheData, true, false, true);
		}

		return $certificate;
	}

}
