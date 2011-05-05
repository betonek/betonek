<?php

require_once "Mail.php";

/** Send email
 * @param $subject       email subject
 * @param $body          body
 * @param $to            recipient
 * @param $from          sender
 * @retval TRUE          success
 * @retval FALSE         failure */
function send_email($subject, $body, $to = CFG_ADMIN, $from = CFG_MAIL_FROM)
{
	$smtp = Mail::factory('smtp', array(
		"host"       => CFG_MAIL_HOST,
		"auth"       => TRUE,
		"username"   => CFG_MAIL_USER,
		"password"   => CFG_MAIL_PASS
	));

	$res = $smtp->send($to, array(
		"Date"         => date("r"),
		"From"         => "Biblioteka <$from>",
		"To"           => $to,
		"Subject"      => CFG_MAILPREFIX . " " . $subject,
		"Content-Type" => "text/plain; charset=UTF-8",
		),
		$body);

	return ($res === TRUE);
}

/***********************************************/

function user_exists($email)
{
	return (SQL::one("SELECT id FROM users WHERE email='%s'", $email) !== FALSE);
}

function user_add($email, $name, $surname, $pass, &$msg)
{
	/* add to db */
	$uid = SQL::run(
		"INSERT INTO users SET email='%s', name='%s', surname='%s', password='%s'",
		array($email, $name, $surname, $pass));

	if ($uid === FALSE) {
		$msg = "Błąd bazy danych przy tworzeniu konta";
		return false;
	}

	/* send email */
	$res = send_email(
		"Nowe konto",
		"Imię i nazwisko: $name $surname\n" .
		"E-mail: $email\n" .
		"Aktywuj: " . CFG_URL . "activate.php?uid=$uid\n" .
		"Odrzuć: " . CFG_URL . "reject.php?uid=$uid\n");

	if (!$res) {
		$msg = "Błąd przy wysyłaniu wiadomości. Napisz email na adres " . CFG_ADMIN;
		return false;
	}

	$msg = "Konto utworzone. Poczekaj na e-mail z informacją o aktywacji.";
	return true;
}

function user_activate($uid)
{
	$user = SQL::one("SELECT * FROM users WHERE id=%d", $uid);
	if ($user === FALSE)
		return false;

	$res = SQL::run("UPDATE users SET active=1 WHERE id=%d", $uid);
	if ($res === FALSE)
		return FALSE;

	send_email(
		"Aktywacja konta",
		"Twoje konto zostało aktywowane: " . CFG_URL,
		$user["email"]);

	return true;
}

function user_reject($uid)
{
	$res = SQL::run("DELETE FROM users WHERE id=%d", $uid);
	if ($res === FALSE)
		return FALSE;

	return true;
}

/** Send password to the user
 * @param  $email         users email
 * @retval TRUE           email sent or no such user
 * @retval FALSE          error while sending email */
function user_sendpassword($email)
{
	$user = SQL::one("SELECT password FROM users WHERE email='%s'", $email);
	if ($user === FALSE)
		return TRUE;

	return send_email(
		"Przypomnienie",
		"Twoje hasło to: $user[password]",
		$email);
}

function check_login()
{
	if (Session::get("uid"))
		return true;

	Session::set("back_url", $_SERVER["REQUEST_URI"]);
	header("Location: login.php");
	exit(0);
}

function is_admin()
{
	return (Session::get("email") == CFG_ADMIN);
}

/***********************************************/

function draw_menu($menu, $addsep = false)
{
	$i = 0;
	$str = "";
	$cur = basename($_SERVER["REQUEST_URI"]);

	foreach ($menu as $url => $descr) {
		if ($addsep && $i++ > 0)
			$str .=  "<li class=\"uba_I\"></li>\n";

		$islink = ($url[0] != ":");
		$iscur = (!$islink || $url == $cur);

		if ($iscur)
			$str .= '<li class="ub_selected">';
		else
			$str .= '<li>';

		if ($islink)
			$str .= "<a href=\"$url\">$descr</a>";
		else
			$str .= $descr;

		$str .=  "</li>\n";
	}

	return $str;
}

/***********************************************/
function book_search($query)
{
	/* sanitize the query */
	$query = str_replace(array("\"", "'", "<", ">"), "", $query);

	/* search */
	$found = SQL::run(
		"SELECT * FROM books
		WHERE MATCH(title, author) AGAINST('%s' WITH QUERY EXPANSION)",
		$query);

	return array(
		"query" => $query,
		"found" => $found
	);
}
