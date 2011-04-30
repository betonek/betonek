<?php
require "../includes/lib/lib.php";

$TITLE = "Ustawienia konta";
require "header.php";

if ($_POST["email"]) {
	$user = SQL::one(
		"SELECT * FROM users WHERE email='%s' AND id!=%d",
		array($_POST["email"], Session::get("uid")));

	if ($user) {
		$msg = "Nie można zmienić e-mail, ktoś już używa tego adresu";
	} else {
		$count = SQL::run(
			"UPDATE users SET name='%s', surname='%s', email='%s' WHERE id=%d",
			array($_POST["name"], $_POST["surname"], $_POST["email"], Session::get("uid")));

		if ($count == 1) {
			$msg = "Dane zaktualizowane";

			Session::set("email", $_POST["email"]);
			Session::set("name", $_POST["name"]);
			Session::set("surname", $_POST["surname"]);
		}
	}
}

$name = Session::get("name");
$surname = Session::get("surname");
$email = Session::get("email");

?>

<div id="loginbox">
	<fieldset>
		<legend>Ustawienia konta</legend>

		<span class="errmsg"><?= $msg ?></span>

		<form method="POST">
		<table>
		<tr>
			<td>Imię</td>
			<td><input type="text" name="name" value="<?= $name ?>"/></td>
		</tr>
		<tr>
			<td>Nazwisko</td>
			<td><input type="text" name="surname" value="<?= $surname ?>"/></td>
		</tr>
		<tr>
			<td>E-mail</td>
			<td><input type="text" name="email" value="<?= $email ?>"/></td>
		</tr>
		<tr>
			<td></td>
			<td>
				<input type="submit" value="Zmień" />
			</td>
		</tr>
		</table>
		</form>

		<a href="pass.php">Zmień hasło</a><br />
		<a href="index.php">Powrót</a><br />
	</fieldset>

	<span class="info">
	Pamiętaj, że jeśli zmienisz e-mail, to musisz też podawać nowy adres przy logowaniu
	</span>
</div>

<?php require "footer.php" ?>
