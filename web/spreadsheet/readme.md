DHTMLX SpreadSheet ver. 2.1
===========================
License
--------

This software is allowed to use:

- in GPL project for free
- in non-GPL project you need to obtain Commercial or Enteprise License (both include official technical support)

Please contact sales@dhtmlx.com for details.

System Requirements:
--------------------------
- on the client side: IE6+, FF, Chrome, Safari, Opera
- on the server side: PHP 5.x - 7.x, MySQL or MsSQL 

How to Start
-------------
Before start working with SpreadSheet, you need to apply necessary configuration settings. For this you need:

- Download the **SpreadSheet** package and unzip its content to the [YOUR APPLICATION ROOT] folder.
- Choose the suitable dump file for a database in the root folder of the package:
	- *dump_mssql.sql* - if you use MSSQL
    - *dump_mysql.sql* - if you use MySQL
- Open the *codebase/php/config.php* file and specify mandatory database server settings, depending on the type of the used database:
	- **Data source name** - the string with a data structure to establish connection to the database;
	- **User** - the username used to access the database;
	- **Password** - the password used by the username to access the database.<br>Other settings are optional.

~~~php
<?php

// MySQL
$dsn = "mysql:dbname=ssheet;host=localhost;port=3306";
$db_user = "root";
$db_pass = "1";
$db_tick = "`";

// or for SQLSRV
// $dsn = "sqlsrv:Server=localhost;Database=ssheet";
// $db_user = "ssheet";
// $db_pass = "1";
// $db_tick = "\"";

$db_prefix = "dhx_";
$username = "admin";
$password = "qwert";

?>
~~~

After configuration is finished, you can run any sample from the folder **spreadsheet/samples**.

If you'd like to add SpreadSheet on your own page, move on to one of the [initialization variants](https://docs.dhtmlx.com/spreadsheet__init.html).
