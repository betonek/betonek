<?php
require "../includes/lib/lib.php";
require_once "functions.php";

lib_cssuse("style.css");
lib_jsonload('$("#loginform input:first").focus()');

if ($_POST["email"]) {
	$msg = "";

	if (user_exists($_POST["email"])) {
		$msg = "Takie konto już istnieje";
	} else if ($_POST["pass"] != $_POST["pass2"]) {
		$msg = "Hasła nie zgadzają się";
	} else {
		user_add($_POST["email"], $_POST["name"], $_POST["surname"], $_POST["pass"], $msg);
	}
}

?>
<html>
<head>
	<title>Biblioteka | Utwórz nowe konto</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<?= lib_css() ?>
</head>
<body>

<div class="loginbox formbox">
	<fieldset>
		<legend>Utwórz nowe konto</legend>

		<span class="errmsg"><?= $msg ?></span>

		<form method="POST" id="loginform">
		<table>
		<tr>
			<td>Imię</td>
			<td><input type="text" name="name" /></td>
		</tr>
		<tr>
			<td>Nazwisko</td>
			<td><input type="text" name="surname" /></td>
		</tr>
		<tr>
			<td>E-mail</td>
			<td><input type="text" name="email" /></td>
		</tr>
		<tr>
			<td>Hasło</td>
			<td><input type="password" name="pass" /></td>
		</tr>
		<tr>
			<td>Powtórz hasło</td>
			<td><input type="password" name="pass2" /></td>
		</tr>
		<tr>
			<td></td>
			<td>
				<input type="submit" value="Utwórz" />
			</td>
		</tr>
		</table>
		</form>

		<a href="index.php">Powrót</a><br />
	</fieldset>

	<span class="info">
	Wypełnij wszystkie pola i kliknij "Utwórz". Gdy system zaakceptuje zgłoszenie poczekaj, aż
	Administrator aktywuje Twoje konto. Otrzymasz wtedy wiadomość e-mail z powiadomieniem.
	</span>

</div>

<?= lib_js() ?>
</body>
</html>
