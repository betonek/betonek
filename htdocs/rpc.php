<?php
require "./examples.php";

require "../includes/lib/lib.php";

require_once "functions.php";

/* used for rpc testing */
if (!defined('CFG_RPC_NOAUTH'))
	check_login();

/*** RPC functions ***/

function rpc_search($p)
{
	return res(book_search($p));
}

function rpc_title_view($p)
{
	if ($p["item_id"]) {
		$title_id = title_get_id_by_item_id($p["item_id"]);

		if (!$title_id)
			return err(2, "title for given item_id not found");
	} else {
		$title_id = $p["title_id"];
	}

	$view = title_view($title_id);
	if (!$view)
		return err(3, "error in fetching title view - not found?");
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

function rpc_item_add_final($p)
{
	$item_id = 0;
	$title_id = intval($p["title_id"]);
	$author_id = intval($p["author_id"]);

	if ($title_id > 0) {
		$item_id = item_add($title_id);
		$author_id = author_get_id_by_title_id($title_id);
	} else if ($p["title"] && $p["type"]) {
		if ($author_id) {
			$title_id = title_add($p["title"], $p["type"], $author_id);
			$item_id = item_add($title_id);
		} else if ($p["author"]) {
			$author_id = author_add($p["author"]);
			$title_id = title_add($p["title"], $p["type"], $author_id);
			$item_id = item_add($title_id);
		} else {
			return err(4, "title and type given, but no author information");
		}
	} else {
		return err(5, "no valid title information given");
	}

	return res(array(
		"item_id"   => $item_id,
		"title_id"  => $title_id,
		"author_id" => $author_id
	));
}

function rpc_author_search($p)
{
	return res(author_search($p["query"]));
}

function rpc_item_del($p)
{
	return res(array(
		"title_id" => item_del($p["title_id"])
	));
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
	$out = err(1, "Invalid method: $method");
}

header("Content-Type: application/json-rpc");
echo json_encode($out);
