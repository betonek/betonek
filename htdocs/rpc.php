<?php
require "../includes/lib/lib.php";

require_once "functions.php";
check_login();

/*** RPC functions ***/

/* CRUD for items  */

/* read */
function rpc_search($p)
{
	return res(book_search($p["query"]));
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

$req = json_decode($HTTP_RAW_POST_DATA, true);
$fname = "rpc_$req[method]";
if (function_exists($fname))
	$out = $fname($req["params"]);
else
	$out = err(1, "Invalid method");

header("Content-Type: application/json-rpc");
echo json_encode($out);
