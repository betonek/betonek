<?php
require "../includes/lib/lib.php";

lib_cssuse("style.css");

/* login handling */
require "lib/SHA256.php";
lib_jsuse("lib/sha256.js");
lib_jsuse("login.js");

/* setup crypt salt */
if (Session::get("salt_time") < time() - 3600) {
	Session::set("salt_time", time());

	srand(time());
	Session::set("salt", rand(97485, 1987967896));
}

$salt = intval(Session::get("salt"));
lib_jsonload("BL.init($salt)");

/* handle login attempt */
if ($_POST["email"]) {
	$user = SQL::one(
		"SELECT * FROM users WHERE email='%s' AND active=1",
		$_POST["email"]);

	if ($user && $_POST["crypted"] === sha256("$salt" . $user["password"])) {
		Session::set("uid", $user["id"]);
		Session::set("email", $user["email"]);
		Session::set("name", $user["name"]);
		Session::set("surname", $user["surname"]);
	} else {
		$msg = "Nieudane logowanie";
	}
}

if (Session::get("uid")) {
	$back_url = Session::get("back_url");
	if (!$back_url)
		$back_url = "index.php";

	header("Location: $back_url");
	exit(0);
}

?>
<html>
<head>
	<title>Biblioteka | Zaloguj się</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<?= lib_css() ?>
</head>
<body>

<div id="loginbox">
	<fieldset>
		<legend>Zaloguj się</legend>

		<span class="errmsg"><?= $msg ?></span>

		<form method="POST" id="loginform">
		<table>
		<tr>
			<td>E-mail</td>
			<td><input type="text" name="email" /></td>
		</tr>
		<tr>
			<td>Hasło</td>
			<td>
				<input type="password" name="pass" />
				<input type="hidden" name="crypted" />
			</td>
		</tr>
		<tr>
			<td></td>
			<td>
				<input type="submit" value="Zaloguj" />
			</td>
		</tr>
		</table>
		</form>

		<a href="newlogin.php">Utwórz nowe konto</a><br />
		<a href="forgot.php">Przypomnij hasło</a>
	</fieldset>

	<span class="info">Aby uzyskać dostęp do Biblioteki podaj swój adres e-mail i hasło. Jeśli nie masz
	konta, kliknij w link "Utwórz nowe konto"</span>

</div>

<?= lib_js() ?>
</body>
</html>
