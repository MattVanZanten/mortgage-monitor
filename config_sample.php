<?php
//connect to db
error_reporting(-1);
ini_set('display_errors', 'On');
$dsn 		= 'mysql:dbname=;host=localhost';
$user 		= '';
$password 	= '';
$connect 	= new PDO($dsn, $user, $password, array(\PDO::MYSQL_ATTR_INIT_COMMAND =>  'SET NAMES utf8') );
date_default_timezone_set('America/Vancouver');
$to = 'email_address';
