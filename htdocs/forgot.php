<?php
require "../includes/lib/lib.php";
require_once "functions.php";

lib_cssuse("style.css");
lib_jsonload('$("#loginform input:first").focus()');

if ($_POST["email"]) {
	if (user_sendpassword($_POST["email"]))
		$msg = "Sprawdź pocztę";
	else
		$msg = "Błąd przy wysyłaniu email";
}

?>
<html>
<head>
	<title>Biblioteka | Przypomij hasło</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<?= lib_css() ?>
</head>
<body>

<div class="loginbox formbox">
	<fieldset>
		<legend>Przypomnij hasło</legend>

		<span class="errmsg"><?= $msg ?></span>

		<form method="POST" id="loginform">
		<table>
		<tr>
			<td>E-mail</td>
			<td><input type="text" name="email" /></td>
		</tr>
		<tr>
			<td></td>
			<td>
				<input type="submit" value="Przypomnij" />
			</td>
		</tr>
		</table>
		</form>

		<a href="index.php">Powrót</a>
	</fieldset>

	<span class="info">Wpisz adres e-mail z Twojego konta w Bibliotece i wciśnij "Przypomnij". Jeśli adres
	zostanie znaleziony w systemie, otrzymasz wiadomość e-mail z hasłem.</span>

</div>

<?= lib_js() ?>
</body>
</html>
