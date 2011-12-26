<?php
require "include/lib/lib.php";
require_once "functions.php";

check_login();

if (!$_GET["uid"])
	die("Brak numeru uid");

$email = Session::get("email");
if ($email != CFG_ADMIN && $email != "admin")
	die("Brak uprawnień");

if (!user_activate($_GET["uid"]))
	die("Błąd przy aktywacji");

echo "Konto aktywowane";
