<?php

require("config.php");
require("grid_cell_connector.php");

$pdo = new PDO($dsn, $db_user, $db_pass);

$conn = new GridCellConnector($pdo, [ "prefix" => $db_prefix, "tick" => $db_tick ]);
//$conn->enable_log();
$conn->render();

?>