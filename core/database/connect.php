<?php
/* Connection parameters */
$database_connection = new StdClass();


$local = False;
$database_connection->server = 'localhost';
$database_connection->username = 'jrmholis_user';
$database_connection->password = 'jrmholis_user';
$database_connection->name = 'jrmholis_agent_circle';
// $database_connection->server = 'localhost';
// $database_connection->username = 'root';
// $database_connection->password = '';
// $database_connection->name = 'aero_field_team';
//$database_connection->server = '108.167.189.23';
//$database_connection->username = 'grohsfab_remote';
//$database_connection->password = 'X!==%G)i~VaB';
//$database_connection->name = 'grohsfab_phpmars';

/* Establishing the connection */
$database = new mysqli($database_connection->server, $database_connection->username, $database_connection->password, $database_connection->name);

/* Debugging */
if($database->connect_error) {
	die('The connection to the database failed ! Please read the documentation !');
}

/* Initiate the Database Class */
Database::$database = $database;

?>
