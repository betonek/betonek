<?php
/*
 * Betonek configuration file
 */

/* set to false on production sites */
define('DEBUG', true);

/* application config */
define('CFG_TITLE', "Betonek");
define('CFG_MAILPREFIX', "[Betonek]");
define('CFG_ADMIN', "admin@domain.com");
define('CFG_URL', "http://localhost/betonek/");

/* MySQL database */
define('CFG_SQL_HOST', 'localhost');
define('CFG_SQL_USER', 'root');
define('CFG_SQL_PASS', FALSE);
define('CFG_SQL_DB',   'betonek');

/* SMTP account for sending mails */
define('CFG_MAIL_FROM', "admin@domain.com");
define('CFG_MAIL_HOST', "smtp.domain.com");
define('CFG_MAIL_USER', CFG_MAIL_FROM);
define('CFG_MAIL_PASS', "password");
