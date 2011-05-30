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
/* TODO:
 * search authors and merge results somehow into $titles
 * support types param
 * support user_id param
 * support author_id param
 * support empty query
 */
function book_search($req)
{
	/* sanitize the query */
	$query = str_replace(array("\"", "'", "<", ">"), "", $req["query"]);

	if ($req["engine"] == "simple") {
		$titles = SQL::run(
			"SELECT
				type, title, name AS author, titles.id AS title_id, author_id
			FROM
				titles
				LEFT JOIN authors ON titles.author_id = authors.id
			WHERE
				title LIKE '%s%%';",
			$query);
	} else { /* == full */
		$titles = SQL::run(
			"SELECT
				type, title, name AS author, titles.id AS title_id, author_id
			FROM
				titles
				LEFT JOIN authors ON titles.author_id = authors.id
			WHERE
				MATCH(title) AGAINST('%s' WITH QUERY EXPANSION);",
			$query);
	}

	/*$authors = SQL::run(
		"SELECT
			id AS author_id, name AS author_name
		FROM
			authors
		WHERE
			MATCH(name) AGAINST('%s' IN BOOLEAN MODE);",
		$query);*/

	return array(
		"query"   => $query,
		"titles"  => $titles
	);
}

/** Get title id by item
 * @param item_id       item id
 * @return integer      title id
 * @retval 0            item not found
 */
function title_get_id_by_item_id($item_id)
{
	$owner = SQL::one("SELECT title_id FROM owners WHERE id=%u", $item_id);

	if ($owner)
		return intval($owner["title_id"]);
	else
		return 0;
}

/** Get title by its id or item id
 * @param title_id               title id
 * @return array                 see RPC API
 * @retval FALSE                 not found
 */
function title_view($title_id)
{
	/* get basic title view info */
	$view = SQL::one("
		SELECT
			type, title, name AS author, titles.id AS title_id, author_id
		FROM
			titles
			LEFT JOIN authors ON titles.author_id = authors.id
		WHERE
			titles.id = %u;", $title_id);

	if (!$view)
		return FALSE;

	/* get owners and items */
	$items = SQL::run("
		SELECT
			owners.id AS item_id, user_id, CONCAT(users.name, ' ', users.surname) AS user, users.email AS user_email
		FROM
			owners
			LEFT JOIN users ON owners.user_id = users.id
		WHERE title_id = %u;", $title_id);
	$view["owners"] = $items;

	/* determine is_owner */
	$view["is_owner"] = false;
	$login_uid = Session::get("uid");

	foreach ($items as $item) {
		if ($item["user_id"] == $login_uid) {
			$view["is_owner"] = true;
			break;
		}
	}

	/* get average mark */
	$avgmark = SQL::one("SELECT AVG(mark) AS m FROM ratings WHERE title_id = %u;", $title_id);
	$view["average_mark"] = floatval($avgmark["m"]);

	/* get comments */
	$comments = SQL::run("
		SELECT
			comment, user_id, CONCAT(users.name, ' ', users.surname) AS user, users.email AS user_email
		FROM
			comments
			LEFT JOIN users ON comments.user_id = users.id
		WHERE title_id = %u;", $title_id);
	$view["comments"] = $comments;

	return $view;
}

/** Submit title rate on behalf of the user logged in
 * @param title_id        title id
 * @param mark            title mark: integer 0-10; if -1, delete user rating
 */
function title_rate($title_id, $mark)
{
	/* sanitize */
	$mark = intval($mark);
	if ($mark < -1)
		$mark = 0;
	else if ($mark > 10)
		$mark = 10;

	if ($mark == -1) {
		SQL::run(
			"DELETE FROM ratings WHERE title_id=%u AND user_id=%u;",
			array($title_id, Session::get("uid")));
	} else {
		SQL::run(
			"INSERT INTO
				ratings (title_id, user_id, mark)
			VALUES
				(%u, %u, %u)
			ON DUPLICATE KEY UPDATE
				mark=VALUES(mark)",
			array($title_id, Session::get("uid"), $mark));
	}

	return array(
		"title_id"    => $title_id,
		"mark"        => $mark
	);
}
