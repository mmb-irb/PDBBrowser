<?php

/*
 * bdconn.inc.php
 * DB Connection
 */
$host = "localhost";
$dbname = "pdb";
$user = "dbw00";
$password = "dbw2018";
($mysqli = mysqli_connect($host, $user, $password)) or die(mysqli_error());
mysqli_select_db($mysqli, $dbname) or die(mysqli_error($mysqli));
