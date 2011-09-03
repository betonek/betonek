<?php
require_once "functions.php";

check_login();

/* load JavaScript, run B.init() on startup */
lib_jsuse("betonek.js");
lib_jsuse("lib/dump.js");
lib_jsuse("lib/rpc.js");
lib_jsuse("lib/jquery.tmpl.js");
lib_jsuse("lib/blockUI.js");
lib_jsuse("lib/raty.js");
lib_jsonload("B.init()");

/* set title */
if ($TITLE)
	$title = "Biblioteka | $TITLE";
else
	$title = "Biblioteka | Szukaj";

/* setup CSS */
lib_cssuse("style.css");
lib_cssuse("ui-lightness/jquery-ui-1.8.12.custom.css");

/* userbar menus */
$menu1 = array(
	"index.php"           => "Szukaj",
	"add.php"             => "Dodaj"
);

$menu2 = array(
	":email"             => Session::get("email"),
	"profile.php"        => "Ustawienia konta",
	"logout.php"         => "Wyloguj się"
);

?>
<html>
<head>
	<title><?= $title ?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<?= lib_css() ?>
</head>
<body>

<span id="templates">
<?= lib_tpl() ?>
</span>

<div id="userbar">
	<ul id="ub_nav">
		<?= draw_menu($menu1) ?>
	</ul>

	<ul id="ub_account">
		<?php /*if (is_admin()) echo "<li>Jesteś Adminem!</li>"*/ ?>
		<?= draw_menu($menu2, true) ?>
	</ul>
</div>
