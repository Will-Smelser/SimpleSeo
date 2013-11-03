<?php
/**
 * This is meant to be called by a cron job
 * to perform deletes on the token table
 * in database.
 */

$mysqlhost = null;
function loadConfig(){
	global $mysqlhost;
	return require_once '../config/main.php';
}

$config = loadConfig();

$db = $config['components']['db'];
$link = mysql_connect($mysqlhost,$db['username'],$db['password']);

if($link === false){
	echo "Database connection failed.";
	throw new Exception("Database connection failed.");
	exit;
}

if(!mysql_query('DELETE FROM simpleseoapi.tokens WHERE  expire < UNIX_TIMESTAMP()',$link))
	echo 'Delete unused tokens failed.'."\nERROR:\n".mysql_errno()."\n\n";
else
	echo 'Delete was a success'."\n\n";

mysql_close($link);
?>