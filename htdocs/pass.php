<?php
require "../includes/lib/lib.php";

$TITLE = "Zmień hasło";
require "header.php";

if ($_POST["pass"]) {
	$user = SQL::one(
		"SELECT password FROM users WHERE id='%d'",
		Session::get("uid"));

	if ($user && $user["password"] == $_POST["pass"]) {
		if ($_POST["pass1"] && $_POST["pass1"] == $_POST["pass2"]) {
			$count = SQL::run(
				"UPDATE users SET password='%s' WHERE id=%d",
				array($_POST["pass1"], Session::get("uid")));

			if ($count == 1)
				$msg = "Hasło zmienione";
		} else {
			$msg = "Powtórz nowe hasło poprawnie";
		}
	} else {
		$msg = "Złe hasło";
	}
}

?>

<div class="loginbox formbox">
	<fieldset>
		<legend>Zmień hasło</legend>

		<span class="errmsg"><?= $msg ?></span>

		<form method="POST">
		<table>
		<tr>
			<td>Obecne</td>
			<td><input type="password" name="pass" /></td>
		</tr>
		<tr>
			<td>Nowe</td>
			<td><input type="password" name="pass1" /></td>
		</tr>
		<tr>
			<td>Powtórz nowe</td>
			<td><input type="password" name="pass2" /></td>
		</tr>
		<tr>
			<td></td>
			<td>
				<input type="submit" value="Zmień" />
			</td>
		</tr>
		</table>
		</form>

		<a href="index.php">Powrót</a><br />
	</fieldset>
</div>

<?php require "footer.php" ?>
