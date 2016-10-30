<?php

/**
 * @file
 * Alexa endpoint.
 */

require_once("../../class2.php");

if(!e107::isInstalled('alexa'))
{
	e107::redirect(e_BASE . 'index.php');
}

e107_require_once(e_PLUGIN . 'alexa/includes/alexa.class.php');

$alexa = new Alexa();
$alexa->requestHandler();
