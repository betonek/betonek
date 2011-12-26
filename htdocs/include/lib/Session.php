<?php

require_once "SQL.php";

class Session
{
	const datastruct = "
	CREATE TABLE IF NOT EXISTS `session` (
		`id`        varchar(64) NOT NULL,
		`data`      varchar(255),
		`timestamp` int(10) unsigned NOT NULL default '0',
		PRIMARY KEY (`id`)
	) ENGINE=MEMORY";

	public static function _open()
	{
		SQL::run(self::datastruct);
		return true;
	}

	public static function _close()
	{
		return true;
	}

	public static function _read($id)
	{
		$row = SQL::one(
			"SELECT `data` FROM `session` WHERE id = '%s'",
			$id);

		if (is_array($row))
			return $row["data"];
		else
			return "";
	}

	public static function _write($id, $data)
	{
		if (!$data)
			return self::_destroy($id);

		SQL::run(
			"REPLACE INTO `session` VALUES('%s', '%s', %d)",
			array($id, $data, time()));

		return true;
	}

	public static function _destroy($id)
	{
		SQL::run(
			"DELETE FROM `session` WHERE `id` = '%s'",
			$id);

		return true;
	}

	public static function _gc($max)
	{
		SQL::run(
			"DELETE FROM `session` WHERE `timestamp` < %d",
			time() - $max);

		return true;
	}

	/** Public functions **/
	public static function start()
	{
		session_set_save_handler(
			array('Session', '_open'),
			array('Session', '_close'),
			array('Session', '_read'),
			array('Session', '_write'),
			array('Session', '_destroy'),
			array('Session', '_gc'));
		session_set_cookie_params(0, "/", "", false, false);
		session_start();

		return true;
	}

	public static function get($var) { return base64_decode($_SESSION[$var]); }
	public static function set($var, $val) { $_SESSION[$var] = base64_encode($val); }
	public static function del($var) { unset($_SESSION[$var]); }
	public static function kill() { session_unset(); }
}
