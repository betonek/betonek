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
	/* find currently selected menu position */
	$urls = array_keys($menu);
	$cur = basename($_SERVER["REQUEST_URI"]);

	$selected = "";
	foreach ($urls as $url) {
		if ($url == $cur)
			$selected = $url;
	}
	if (!$selected)
		$selected = $urls[0];

	/* draw menu */
	$i = 0;
	$str = "";
	foreach ($menu as $url => $descr) {
		if ($addsep && $i++ > 0)
			$str .=  "<li class=\"uba_I\"></li>\n";

		$islink = ($url[0] != ":");
		$issel = (!$islink || $url == $selected);

		if ($issel)
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
/** Parameter value sanitizer */
function _safeparam($string)
{
	return str_replace(array("\"", "'", "<", ">", "(", ")"), "", $string);
}

/** Sort titles according to decreasing relevance */
function _book_search_sort($t1, $t2)
{
	if ($t1["relevance"] == $t2["relevance"])
		return 0;
	else if ($t1["relevance"] < $t2["relevance"])
		return 1;
	else
		return -1;
}

/** Book search */
function book_search($req)
{
	$qadd = array();
	$qargs = array();
	$empty = false;

	/* sanitize the query */
	$query = trim(_safeparam($req["query"]));

	/* support empty query */
	if (!$query) {
		$empty = true;
	} else {
		/* analyze the query - find keywords longer than 2 characters */
		$ks = explode(' ', str_replace(array(",", ".", "-"), "", $query));
		$keywords = array();
		foreach ($ks as $k) {
			if (strlen($k) > 2)
				$keywords[] = $k;
		}

		$qadd[]  = "LOWER(CONCAT(authors.name, ' ', title)) REGEXP '(^| )(%s)( |,|$)'";
		$qargs[] = join('|', $keywords);
	}

	/* support "type" param */
	if ($req["type"]) {
		$type = _safeparam($req["type"]);

		$qadd[]  = "type='%s'";
		$qargs[] = $type;
	}

	/* support "owner" flag */
	if ($req["owner"]) {
		/* fetch all titles owned by uid */
		$owned_sql = SQL::run("SELECT title_id FROM owners WHERE user_id=%d", Session::get("uid"));

		/* rewrite */
		$owned = array();
		foreach ($owned_sql as $t)
			$owned[] = $t["title_id"];

		if (count($owned) == 0)
			$owned[] = 0; /* return empty set */

		$qadd[]  = "titles.id IN (%s)";
		$qargs[] = join(',', $owned);
	}

	/* support "author" param */
	if ($req["author"]) {
		$author = _safeparam($req["author"]);

		$a = SQL::one("SELECT id FROM authors WHERE name='%s';", $author);

		$qadd[]  = "author_id=%d";
		$qargs[] = intval($a["id"]);
	}

	/* ask the database */
	$titles = SQL::run(
		sprintf(
			"SELECT
				type, title, name AS author, titles.id AS title_id, author_id
			FROM
				titles
				LEFT JOIN authors ON titles.author_id = authors.id
			%s
			LIMIT 1000;",
			count($qadd) ? "WHERE " . join(' AND ', $qadd) : ""
		), $qargs);

	/* count keyword occurances for each db result */
	if (!$empty) {
		foreach ($titles as $i => $title) {
			/* concatenate author and title, without interpunction etc. */
			$s = strtolower(str_replace(array(",", ".", "-"), "", "$title[author] $title[title]"));

			/* compare against the query */
			$intersect = array_intersect($keywords, explode(' ', $s));

			/* store number of keyword occurances */
			$r = count($intersect);
			if ($r > 0)
				$titles[$i]["relevance"] = $r;
			else
				unset($titles[$i]);
		}

		/* sort according to relevance */
		usort($titles, "_book_search_sort");
	}

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

	/* get user mark */
	$rating = SQL::one("SELECT mark FROM ratings WHERE title_id = %u AND user_id = %u;",
		array($title_id, $login_uid));
	$view["mark"] = intval($rating["mark"]);

	/* get average mark */
	$avgmark = SQL::one("SELECT AVG(mark) AS m FROM ratings WHERE title_id = %u;", $title_id);
	$view["average_mark"] = floatval($avgmark["m"]);

	/* get comments */
	$comments = SQL::run("
		SELECT
			comment, date, user_id, CONCAT(users.name, ' ', users.surname) AS user, users.email AS user_email
		FROM
			comments
			LEFT JOIN users ON comments.user_id = users.id
		WHERE title_id = %u
		ORDER BY date ASC;", $title_id);
	$view["comments"] = $comments;

	return $view;
}

/** Submit title rate on behalf of the user logged in
 * @param title_id        title id
 * @param mark            title mark: integer 1-5; if 0, delete user rating
 */
function title_rate($title_id, $mark)
{
	/* sanitize */
	$mark = intval($mark);
	if ($mark < 1)
		$mark = 0;
	else if ($mark > 5)
		$mark = 5;

	if ($mark == 0) {
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

	/* get average mark */
	$avgmark = SQL::one("SELECT AVG(mark) AS m FROM ratings WHERE title_id = %u;", $title_id);

	return array(
		"title_id"      => $title_id,
		"mark"          => $mark,
		"average_mark"  => floatval($avgmark["m"])
	);
}

/** Submit title comment on behalf of the user logged in
 * @param title_id        title id
 * @param comment         comment; if empty, delete user comment from database
 */
function title_comment($title_id, $comment)
{
	$comment = trim(strip_tags($comment));

	if (!$comment) {
		SQL::run(
			"DELETE FROM comments WHERE title_id=%u AND user_id=%u;",
			array($title_id, Session::get("uid")));
	} else {
		SQL::run(
			"INSERT INTO
				comments (title_id, user_id, comment, date)
			VALUES
				(%u, %u, '%s', NOW())
			ON DUPLICATE KEY UPDATE
				comment = VALUES(comment)",
			array($title_id, Session::get("uid"), $comment));
	}

	return array(
		"title_id"    => $title_id,
		"comment"     => $comment
	);
}

/** Add new title
 * @param title          title name
 * @param type           title type
 * @param author_id      author id
 * @return title id
 */
function title_add($title, $type, $author_id)
{
	$title = _safeparam($title);

	/* SQL::run() returns last insert id for insert queries */
	return SQL::run(
		"INSERT INTO titles SET
			title='%s',
			type='%s',
			author_id=%u",
		array($title, $type, $author_id));
}

/** Add new item to users library
 * @param title_id      title id
 * @return owner id
 */
function item_add($title_id)
{
	$d = array(intval($title_id), Session::get("uid"));

	/* check if the user already has this title in his items */
	$o = SQL::one("
		SELECT id FROM owners
		WHERE title_id=%d AND user_id=%d",
		$d);

	if ($o)
		return $o["id"];

	/* SQL::run() returns last insert id for insert queries */
	return SQL::run(
		"INSERT INTO owners SET
		title_id=%u, user_id=%u",
		$d);
}

/** Deletes item from users library
 * @param title_id        title id
 * @return title_id
 * @retval 0              item not found in users library
 */
function item_del($title_id)
{
	$title_id = intval($title_id);

	/* remove */
	SQL::run("DELETE FROM owners WHERE title_id=%u AND user_id=%d",
		array($title_id, Session::get("uid")));

	return $title_id;
}

/** Add new author
 * @param name     author name
 * @return author id
 */
function author_add($name)
{
	$name = _safeparam($name);

	/* SQL::run() returns last insert id for insert queries */
	return SQL::run("INSERT INTO authors SET name='%s'", $name);
}

/** Search author by name
 * @param query    beginning of author name; if null, return all
 */
function author_search($query)
{
	$query = _safeparam($query);

	$authors = SQL::run(
		"SELECT DISTINCT
			id AS author_id, name AS author
		FROM
			authors
		WHERE name LIKE '%s%%'", $query);

	return array(
		"query"   => $query,
		"authors" => $authors
	);
}

/** Fetch all titles of given author
 * @param author_id   author id
 */
function author_titles($author_id, $type)
{
	$author_id = intval($author_id);

	if ($type)
		$addq = "AND type='%s' ";

	$titles = SQL::run(
		"SELECT DISTINCT
			id AS title_id, title, type
		FROM
			titles
		WHERE
			author_id=%d
			$addq",
		array($author_id, $type));

	return array(
		"author_id" => $author_id,
		"type"      => $type,
		"titles"    => $titles
	);
}

/** Get author id by title id
 * @param title_id      title id
 * @return integer      author id
 * @retval 0            item not found
 */
function author_get_id_by_title_id($title_id)
{
	$title = SQL::one("SELECT author_id FROM titles WHERE id=%u", $title_id);

	if ($title)
		return intval($title["author_id"]);
	else
		return 0;
}
