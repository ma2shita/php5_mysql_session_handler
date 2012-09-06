php5_mysql_session_handler
==========================

PHP SESSION store to MySQL Handler for PHP5

SETUP:
------
1. create table

    SQL> CREATE TABLE `sessions` (
        `id` varchar(32) not null,
        `data` text,
        `updated` timestamp default current_timestamp on update current_timestamp,
        PRIMARY KEY (`id`)
     ) TYPE=MyISAM;

2. define add to startup or common.

	define('DB_HOST_MASTER', 'HOST');
	define('DB_USER_MASTER', 'USER');
	define('DB_PASS_MASTER', 'PASSWD');
	define('DB_MBLV_MASTER', 'DBNAME');

ENABLE:
-------

1. add `require_once 'session_store_mysql.php';` to startup or common. 
   (use this session store, immediate)
 
DISABLE:
--------

1. remove or comment-out `require_once 'session_store_mysql.php';` 
   (use default session store, immediate)

CLEANUP:
--------

1. `SQL> DROP TABLE \`sessions\`;`
2. `rm session_store_mysql.php`

ENV.:
-----

* PHP 5.3.3 or higher
* PHP mysql functions.

ETC
---

License: MIT License.

Author: ma2shita @ ma2shita.jp / 2012-09-06

[EoF]

