<?php
require "./examples.php";

require "../includes/lib/lib.php";

require_once "functions.php";
check_login();

/*** RPC functions ***/

function rpc_search($p)
{
	return res(book_search($p));
}

/*********************/

function err($code, $msg)
{
	return array("error" => array(
		"code"      => $code,
		"message"   => $msg
	));
}

function res($result)
{
	return array("result" => $result);
}

/*********************/

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	$req = json_decode($HTTP_RAW_POST_DATA, true);
	$method = $req["method"];
	$params = $req["params"];
} else {
	$method = $_GET["method"];
	$params = $_GET;
}

$handler = "rpc_$method";

if (function_exists($handler)) {
	$out = $handler($params);
} else if (array_key_exists($method, $examples)) {
	$out = res($examples[$method]);
} else {
	$out = err(1, "Invalid method");
}

header("Content-Type: application/json-rpc");
echo json_encode($out);
