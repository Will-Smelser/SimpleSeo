<?php
/**
 * Created by PhpStorm.
 * User: Will
 * Date: 12/10/13
 * Time: 6:50 PM
 */

$config = array();
$config['API_PROTOCOL'] = 'http';
$config['API_HOST'] = $_SERVER['HTTP_HOST'];
$config['API_PATH'] = '/api';
$config['API_CONTROLLERS'] = array('Body','Google','Head','Moz','Semrush','Server','Social','W3c');

return $config;