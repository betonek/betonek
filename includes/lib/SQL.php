<?php

class SQL
{
	public static function connect()
	{
		mysql_connect(CFG_SQL_HOST, CFG_SQL_USER, CFG_SQL_PASS);
		mysql_select_db(CFG_SQL_DB);
		mysql_set_charset("utf8");
	}

	public static function query($query, $args = null)
	{
		$res = false;
		$todo = null;

		if ($args !== null) {
			if (is_array($args)) {
				foreach ($args as $key => $arg)
					$args[$key] = mysql_real_escape_string($arg);

				$todo = vsprintf($query, $args);
			} else {
				$todo = sprintf($query, mysql_real_escape_string($args));
			}
		}

		if ($todo === null)
			$todo = $query;

		$res = mysql_query($todo);

		if ($res === FALSE) {
			error_log("MySQL error: " . mysql_error() . " in '$query'");
		}

		return $res;
	}

	public static function fetch($res, $limit = -1)
	{
		$output = array();

		while (($row = mysql_fetch_assoc($res)) && $limit-- != 0)
			$output[] = $row;

		return $output;
	}

	public static function one($query, $args = null)
	{
		$output = array();

		$res = self::query($query, $args);

		if ($res === FALSE) {
			$output = false;
		} else {
			$output = mysql_fetch_assoc($res);
			mysql_free_result($res);
		}

		return $output;
	}

	public static function run($query, $args = null)
	{
		$output = array();

		$res = self::query($query, $args);

		if ($res === TRUE) {
			/* INSERT, DELETE or UPDATE */
			if (strtolower(substr($query, 0, 7)) == "insert ")
				$output = mysql_insert_id();
			else
				$output = mysql_affected_rows();
		} else if ($res !== FALSE) {
			$output = self::fetch($res);
			mysql_free_result($res);
		} else {
			$output = FALSE;
		}

		return $output;
	}
}
