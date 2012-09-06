<?php
/* PHP SESSION store to MySQL Handler
 *
 * SETUP:
 *   1. create table
 *        SQL> CREATE TABLE `sessions` (
 *          `id` varchar(32) not null,
 *          `data` text,
 *          `updated` timestamp default current_timestamp on update current_timestamp,
 *          PRIMARY KEY (`id`)
 *        ) TYPE=MyISAM;
 *   2. define add to startup or common.
 *        define('DB_HOST_MASTER', 'HOST');
 *        define('DB_USER_MASTER', 'USER');
 *        define('DB_PASS_MASTER', 'PASSWD');
 *        define('DB_MBLV_MASTER', 'DBNAME');
 *
 * ENABLE:
 *   1. `require_once 'session_store_mysql.php';` -> add to startup or common.
 *      (use this session store, immediate)
 *
 * DISABLE:
 *   1. `require_once 'session_store_mysql.php';` -> commentOut or remove
 *      (use default session store, immediate)
 *
 * CLEANUP:
 *   1. SQL> DROP TABLE `sessions`;
 *   2. rm session_mysql.php
 *
 * ENV.:
 *   * PHP 5.3.3 or higher
 *   * PHP mysql functions.
 *
 * License: MIT License.
 * Author: ma2shita @ ma2shita.jp / 2012-09-06
 */

class Session_Store
{
	private $db;

	function open($path, $name)
	{
		if ($this->db = mysql_connect(DB_HOST_MASTER, DB_USER_MASTER, DB_PASS_MASTER)) {
			return mysql_select_db(DB_MBLV_MASTER, $this->db);
		}
		return false;
	}

	function close()
	{
		return mysql_close($this->db);
	}

	function read($id)
	{
		$s = sprintf("SELECT data FROM sessions WHERE id = '%s'", mysql_real_escape_string($id, $this->db));
		$rs = mysql_query($s, $this->db);
		if ($r = mysql_fetch_assoc($rs)) {
			return $r['data'];
		}
		return '';
	}

	function write($id, $data)
	{
		$s = sprintf("REPLACE INTO sessions (id, data) VALUES ('%s', '%s')", mysql_real_escape_string($id, $this->db), mysql_real_escape_string($data, $this->db));
		mysql_query($s, $this->db);
		if (mysql_affected_rows($this->db)) {
			return true;
		}
		return false;
	}

	function destroy($id)
	{
		$s = sprintf("DELETE FROM sessions WHERE id = '%s'", mysql_real_escape_string($id, $this->db));
		mysql_query($s, $this->db);
		return mysql_affected_rows($this->db);
	}

	function gc($gc_maxlifetime)
	{
		$s = sprintf("DELETE FROM sessions WHERE (updated + INTERVAL + %s SECOND) < NOW()", mysql_real_escape_string($gc_maxlifetime, $this->db));
		mysql_query($s, $this->db);
		$cnt = mysql_affected_rows($this->db);
		return $cnt;
	}
}

/* recommend ini setting */
/*
 * ini_set('session.name', 'APNAME');
 * ini_set('session.auto_start', false); // use `session_start()` for performance.
 * ini_set('session.serialize_handler', 'php');
 * ini_set('session.gc_probability', 1);
 * ini_set('session.gc_divisor', 1000); // gc_probability/gc_divisor = 1/1000(0.1%) => gc running
 * ini_set('session.gc_maxlifetime', 1800); //30min
 * ini_set('session.hash_function', 1); //hash=SHA-1(160bits)
 */
$_session_store = new Session_Store();
session_set_save_handler(array(&$_session_store, "open"),
                         array(&$_session_store, "close"),
                         array(&$_session_store, "read"),
                         array(&$_session_store, "write"),
                         array(&$_session_store, "destroy"),
                         array(&$_session_store, "gc"));
register_shutdown_function('session_write_close'); 
// EoF
