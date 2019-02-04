<?php

/*
 * globals.inc.php
 * Global variables and settings
 */
// Base directories
// Automatic, taken from CGI variables.
$baseDir = dirname($_SERVER['SCRIPT_FILENAME']);
#$baseDir = '/home/dbw00/public_html/PDBBrowser';
$baseURL = dirname($_SERVER['SCRIPT_NAME']);

// Temporal dir, create if not exists, however Web server 
// may not have the appropriate permission to do so
$tmpDir = "$baseDir/tmp";
if (!file_exists($tmpDir)) {
    mkdir($tmpDir);
}
// Blast details, change to adapt to local settings
// Blast databases should be created using the appropriate programs.
$blastHome = "$baseDir/../../blast";
$blastDbsDir = "$blastHome/DBS";
$blastExe = "$blastHome/bin/blastp";
$blastDbs = array("SwissProt" => "sprot", "PDB" => "pdb");
$blastCmdLine = "$blastExe -db $blastDbsDir/" . $blastDbs['PDB'] . " -evalue 0.001 -max_target_seqs 100 -outfmt \"6 sseqid stitle evalue\"";

// Include directory
$incDir = "$baseDir/include";

// Load accessory routines
include_once "$incDir/bdconn.inc.php";
include_once "$incDir/libDBW.inc.php";

// Load predefined arrays
// Fulltext search fields
$textFields = Array('e.header', 'e.compound', 'a.author', 's.source', 'sq.header');

// Compounds
$rs = mysqli_query($mysqli, "SELECT * from comptype") or print mysqli_error($mysqli);
while ($rsF = mysqli_fetch_array($rs)) {
    $compTypeArray[$rsF['idCompType']] = $rsF['type'];
}

//expTypes
$rs = mysqli_query($mysqli,"SELECT * from expType") or print mysqli_error($mysqli);
while ($rsF = mysqli_fetch_array($rs)) {
    $expTypeArray[$rsF['idExpType']] = $rsF;
}
//expClasses
$rs = mysqli_query($mysqli,"SELECT * from expClasse") or print mysqli_error($mysqli);
while ($rsF = mysqli_fetch_array($rs)) {
    $expClasseArray[$rsF['idExpClasse']] = $rsF['expClasse'];
}
// Start session to store queries
session_start();
