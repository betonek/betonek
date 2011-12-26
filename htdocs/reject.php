<?php
require "include/lib/lib.php";
require_once "functions.php";

check_login();

if (!$_GET["uid"])
	die("Brak numeru uid");

if (Session::get("email") != CFG_ADMIN)
	die("Brak uprawnień");

if (!user_reject($_GET["uid"]))
	die("Błąd przy odrzucaniu");

echo "Konto odrzucone";
