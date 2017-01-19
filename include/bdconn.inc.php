<?php

/*
 * bdconn.inc.php
 * DB Connection
 */
$host = "localhost";
$dbname = "pdb";
$user = "dbw00";
$password = "master00";
($conn = mysql_connect($host, $user, $password)) or die(mysql_error());
($id = mysql_select_db($dbname)) or die(mysql_error());