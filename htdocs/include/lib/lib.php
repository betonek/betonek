<?php
/*
 * Generic library providing support for writing web applications
 */

/* cd to includes/ */
chdir(dirname(dirname(__FILE__)));
require "config.php";

/* set logging level */
if (DEBUG) {
	error_reporting(E_ALL & ~E_NOTICE);
} else {
	error_reporting(E_PARSE);
}

/*********************************/

/* set our encoding ASAP */
header("Content-Type: text/html; charset=utf-8");

/* check if in SSL */
if (!$_SERVER["HTTPS"] || $_SERVER["HTTPS"] == "off")
	define(SSL, false);
else
	define(SSL, true);

/*********************************/

/* load libs */
require_once "SQL.php";
require_once "Session.php";

/*********************************/

SQL::connect();
Session::start();

/*********************************/

$GLOBALS["STYLES"] = array();
$GLOBALS["SCRIPTS"] = array();
$GLOBALS["FOOTERS"] = array();
$GLOBALS["TEMPLATES"] = array();

/*********************************/

lib_jsuse("lib/jquery.js");
lib_jsuse("lib/jquery-ui.js");
lib_jsuse("lib/jnotifica.js");
lib_jsuse("lib/json2.js");

/*********************************/

/* yuck */
function lib_js()
{
	$str = "";

	if (isset($GLOBALS["SCRIPTS"]))
		foreach ($GLOBALS["SCRIPTS"] as $script => $ignore)
			$str .= "<script type=\"text/javascript\" src=\"$script\"></script>\n";

	$str .= '<script type="text/javascript">' . "\n";
	$str .= '<!--' . "\n";
	$str .= '$(document).ready(function() {' . "\n";

	$str .= "if (typeof main === \"function\") main();\n";

	if (isset($GLOBALS["ONLOAD"]))
		foreach ($GLOBALS["ONLOAD"] as $todo)
			$str .= $todo . ";\n";

	$str .= '});' . "\n";
	$str .= '//-->' . "\n";
	$str .= '</script>' . "\n";

	if (isset($GLOBALS["FOOTERS"]))
		foreach ($GLOBALS["FOOTERS"] as $footer)
			$str .= $footer;

	return $str;
}

function lib_css()
{
	$str = "";
	foreach ($GLOBALS["STYLES"] as $style => $ignore)
		$str .= "<link rel=\"stylesheet\" type=\"text/css\" media=\"all\" href=\"$style\" />\n";
	return $str;
}

function lib_tpl()
{
	foreach ($GLOBALS["TEMPLATES"] as $tpl => $ignore)
		$str .= file_get_contents($tpl) . "\n";

	return $str;
}

function lib_jsuse($path)
{
	$GLOBALS["SCRIPTS"]["js/$path"] = true;

	if (file_exists("../js/$path.html"))
		$GLOBALS["TEMPLATES"]["../js/$path.html"] = true;

	if (file_exists("../js/$path.css"))
		$GLOBALS["STYLES"]["js/$path.css"] = true;
}

function lib_jsonload($what, $tag = false)
{
	if (!$tag) $tag = $what;
	$GLOBALS["ONLOAD"][$tag] = $what;
}

function lib_cssuse($path)
{
	$GLOBALS["STYLES"]["css/$path"] = true;
}

function lib_notify($msg, $timeout = 5000)
{
	lib_jsonload("$.jnotifica('$msg', {
		timeout: $timeout,
		msgCss: { fontSize: '15px', fontWeight: 'bold', textAlign: 'center' },
		close: { text: 'X', css: { color: '#fff', fontSize: '15px', position: 'absolute', top: 5, right: 10, cursor: 'pointer' }},
	})");
}
