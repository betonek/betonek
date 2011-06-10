<?php
require "./examples.php";

require "../includes/lib/lib.php";

require_once "functions.php";
check_login();

/*** RPC functions ***/

/* CRUD for items  */

/* read */
function rpc_search($p)
{
	return res(book_search($p));
}

function rpc_title_view($p)
{
	if ($p["item_id"]) {
		$title_id = title_get_id_by_item_id($p["item_id"]);

		if (!$title_id)
			return err(1, "title for given item_id not found");
	} else {
		$title_id = $p["title_id"];
	}

	$view = title_view($title_id);
	if (!$view)
		return err(2, "error in fetching title view - not found?");
	else
		return res($view);
}

function rpc_title_rate($p)
{
	return res(title_rate($p["title_id"], $p["mark"]));
}

function rpc_title_comment($p)
{
	return res(title_comment($p["title_id"], $p["comment"]));
}

function rpc_search_authors($p)
{
	return res(authors_search($p["query"]));
}

/* create */
function rpc_add_item_author($p)
{
    return res(add_item_author($p["title"], $p["author_name"]));
}

function rpc_add_item_author_id($p)
{
    return res(add_item_author_id($p["title"], $p["author_id"]));
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
