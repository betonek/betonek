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


function has_only_keys($arr, $keys){
    throw new Exception("not implemented yet");
}
// INFO - works only in $p contains only following combinations of parameters:
// {title, author}
// {title, author_id}
// TODO: implement and use has_only_keys function
function rpc_item_add_final($p)
{
    if(array_key_exists("title", $p) AND array_key_exists("author_id", $p)){
        return res(add_item_author_id($p["title"], $p["author_id"]));
    } else if(array_key_exists("title", $p) AND array_key_exists("author", $p)){
        throw new Exception("Not supported");
    }
    throw new Exception("Not supported");
}

function rpc_author_search($p)
{
    return res(author_search($p["query"]));
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
