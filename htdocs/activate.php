<?php
require "../includes/lib/lib.php";
require_once "functions.php";

check_login();

if (!$_GET["uid"])
	die("Brak numeru uid");

if (Session::get("email") != CFG_ADMIN)
	die("Brak uprawnień");

if (!user_activate($_GET["uid"]))
	die("Błąd przy aktywacji");

echo "Konto aktywowane";
