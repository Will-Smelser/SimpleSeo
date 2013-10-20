<?php
/**
 * This is meant to be called by a cron job
 * to perform deletes on the token table
 * in database.
 */

function loadConfig(){
	return require_once '../config/main.php';
}

$config = loadConfig();

$db = $config['components']['db'];
$link = mysql_connect($mysqlhost,$db['username'],$db['password']);

if(!mysql_query('DELETE FROM simpleseoapi.ipfilter WHERE  created < ADDDATE(NOW(),INTERVAL 24 HOUR)',$link))
	echo 'Delete old ip filters failed.'."\nERROR:\n".mysql_errno()."\n\n";
else
	echo 'Delete was a success'."\n\n";

mysql_close($link);
?>