<?php

/*
 * globals.inc.php
 * Global cariables and settings
 */
// Base directories
#$baseDir = $_SERVER['DOCUMENT_ROOT'].'/PDBBrowser';
$baseDir = '/home/dbw00/public_html/PDBBrowser';
$baseURL = dirname($_SERVER['SCRIPT_NAME']);

// Temporal dir, create if not exists
$tmpDir = "$baseDir/tmp";
if (!file_exists($tmpDir)) {
    mkdir($tmpDir);
}
// Blast details, change to adapt to local settings
$blastHome = "/home/dbw00/blast";
$blastDbsDir = "$blastHome/dbs";
$blastExe = "$blastHome/bin/blastall";
$blastDbs = array("SwissProt" => "sprot", "PDB" => "pdb_seqres.txt");
$blastCmdLine = "$blastExe -d $blastDbsDir/" . $blastDbs['PDB'] . " -p blastp  -e 0.001 -v 100 -b 0 ";

// Include directory
$incDir = "$baseDir/include";

// Load accessory routines
include_once "$incDir/bdconn.inc.php";
include_once "$incDir/libDBW.inc.php";

// Load predefined arrays
// Fulltext search fields
$textFields = Array('e.header', 'e.compound', 'a.author', 's.source', 'sq.header');

// Compounds
$rs = mysql_query("SELECT * from comptype where type like '%nuc%'") or print mysql_error();
while ($rsF = mysql_fetch_array($rs)) {
    $compTypeArray[$rsF['idCompType']] = $rsF;
}

//expTypes
$rs = mysql_query("SELECT * from expType") or print mysql_error();
while ($rsF = mysql_fetch_array($rs)) {
    $expTypeArray[$rsF['idExpType']] = $rsF;
}
//expClasses
$rs = mysql_query("SELECT * from expClasse") or print mysql_error();
while ($rsF = mysql_fetch_array($rs)) {
    $expClasseArray[$rsF['idExpClasse']] = $rsF;
}
// Start session to store queries
session_start();
