<?php
/*
 * PDBBrowser api
 */
require "globals.inc.php";

$op = array_keys($_REQUEST)[0];

switch ($op) {
    case "glob":
        $data = ['compType' => $compTypeArray, 'expType' => $expTypeArray, 'expClasse' => $expClasseArray];
        break;
    case "blast":
        $data = blast($_REQUEST['blast']);
        break;
    case "search":
        $data = search($_REQUEST['search']);
        break;
    default:
        $data = show($_REQUEST['show']);
        break;
}

header ("Content-type: application/json");
header ("Access-Control-Allow-Origin:*");
print json_encode($data, JSON_PRETTY_PRINT);

//=========================================================================================================================
function search($searchInput) {
    global $textFields, $mysqli;

    $request = json_decode($searchInput, $assoc=True);


    $ANDconds = ["True"]; // required to fulfill SQL syntax if form is empty
    //  Resolution, we consider only cases where user has input something
    if (isset($request['minRes']) and $request['minRes'] != '0.0') {
            $ANDconds[] = "e.resolution >= " . $request['minRes'];
    }
    if (isset($request['maxRes']) and $request['maxRes'] != 'Inf') {
            $ANDconds[] = "e.resolution <= " . $request['maxRes'];
    }
    // Compound type $ORconds holds options selected
    if ($request['checkedComps']) { 
        $ORconds = [];
        foreach ($request['checkedComps'] as $k) {
            $ORconds[] = " e.idCompType = " . $k;
        }
        $ANDconds[] = "(" . join(" OR ", $ORconds) . ")";
    }
    // Classe of experiment
    if ($request['checkedExp']) {
        $ORconds = [];
        foreach ($request['checkedExp'] as $k) {
            $ORconds[] = " et.idExpClasse = " . $k;
        }
        $ANDconds[] = "(" . join(" OR ", $ORconds) . ")";
    }
    //  text query, adapted to use fulltext indexes, $textFields is defined in globals.inc.php and
    //  lists all text fields to be searched in.
    if ($request['query']) {
        $ORconds = [];
        foreach (array_values($textFields) as $field) {
            $ORconds[] = "MATCH (" . $field . ") AGAINST ('" . $request['query'] . "' "
                    . "IN BOOLEAN MODE)";
        }
        $ANDconds[] = "(" . join(" OR ", $ORconds) . ")";
    }
    //  main SQL string, make sure that all tables are joint, and relationships included
    // SELECT columns FROM tables WHERE Conditions_from_relationships AND Conditions_from_query_Form
    $sql = "SELECT distinct e.idCode,e.header,e.compound,e.resolution,s.source,et.expType FROM 
        expType et, author_has_entry ae, author a, source s, entry_has_source es, entry e, sequence sq WHERE
        e.idExpType=et.idExpType AND
        ae.idCode=e.idCode and ae.idAuthor=a.idAuthor AND
        es.idCode=e.idCode and es.idsource=s.idSource AND
        e.idCode = sq.idCode AND
        " . join(" AND ", $ANDconds);

    if (!isset($request['nolimit'])) {
        $sql .= " LIMIT 5000"; // Just to avoid too long listings when testing
    }
    #DEBUG
//    print "<p>$sql</p>";
//    exit();
    // DB query
    $rs = mysqli_query($mysqli,$sql) or print mysqli_error($mysqli);

    //     We check whether there are results to show
    if (!mysqli_num_rows($rs)) {
        return ['error'=> 404, 'message' => "No results found."];
    }
    $results = [];
    while ($rsF = mysqli_fetch_assoc($rs)) { 
        $results[] = $rsF;
    }
    return $results;
}
function blast($sequence) {
    global $tmpDir, $blastCmdLine, $mysqli;
    $tempFile = $GLOBALS['tmpDir'] . "/" . uniqId('pdb');


    // check format
    $p = strpos($sequence, ">"); 
    if (!$p and ($p !== 0)) {
        $sequence = "> User uploaded sequence\n".$sequence;
    }
    // Open temporary file and store query FASTA
    $ff = fopen($tempFile . ".query.fasta", 'wt');
    fwrite($ff, $sequence);
    fclose($ff);
    
    // execute Blast, command line prefix set in globals.inc.php
    $cmd = $blastCmdLine . " -query " . $tempFile . ".query.fasta -out " . $tempFile . ".blast.out"; 
    // DEBUG print command line
    #print "$cmd\n";
    exec($cmd);
    
    // Read results file and parse hits onto $result[]
    $result = file($tempFile . ".blast.out");
    if (!$result or !count($result)) {
        return ['error' => ['errorId'=>404, 'message'=> 'No results found.']];
    }
    $records = [];
    foreach (array_values($result) as $rr) { 
        if (strlen($rr) > 1) {
            $data = explode ("\t",$rr);
            preg_match('/(....)_(.) mol:([^ ]*) length:([0-9]*) *(.*)/', $data[1], $hits);
            list ($r, $idCode, $sub, $tip, $l, $desc)= $hits;
            // get compound from entry table
            $sql = "SELECT compound from entry WHERE idCode = '$idCode'";
            $rs = mysqli_query($mysqli,$sql) or print mysqli_error($mysqli);
            $rsf = mysqli_fetch_assoc($rs);
            $records[] = ['idCode'=> $idCode, 'sub' => $sub, 'tip' => $tip, 'desc' => $desc, 'compound' => $rsf['compound'], 'ev' => $data[2]];
        }
    }
    
    // Cleaning temporary files
    if (file_exists($tempFile . ".query.fasta")) {
        unlink($tempFile . ".query.fasta");
    }
    if (file_exists($tempFile . ".blast.out")) {
        unlink($tempFile . ".blast.out");
    }

    return ['hits' => $records];
}

function show($idCode) {
    global $mysqli;
    $data = [];
    $sql = "SELECT e.* from entry e where e.idCode='$idCode'";
    $rs = mysqli_query($mysqli, $sql) or print mysqli_error($mysqli);
    if (!mysqli_num_rows($rs)) { //search is empty
        return ['error'=> 404, 'message' => 'The requested structure is not available'];
    }
    // Get Main entry
    $data = mysqli_fetch_assoc($rs);
    // new DB query to get authors
    $data['auts'] = '';
    $rsA = mysqli_query($mysqli, "SELECT * from author a, author_has_entry ae where ae.idCode='" . $data['idCode'] . "' and a.idAuthor = ae.idAuthor order by a.author") or print mysqli_error($mysqli);
    if (mysqli_num_rows($rsA)) {
        $data['auts'] = [];
        while ($rsAF = mysqli_fetch_assoc($rsA)) {
            $data['auts'][] = $rsAF['author'];
        }
    }
    // new DB query to get sources
    $rsA = mysqli_query($mysqli, "SELECT * from source s, entry_has_source es where es.idCode='" . $data['idCode'] . "' and s.idSource = es.idSource order by s.source") or print mysqli_error($mysqli);
    $data['sources'] = '';
    if (mysqli_num_rows($rsA)) {
        $data['sources'] = [];
        while ($rsAF = mysqli_fetch_assoc($rsA)) {
            $data['sources'][] = $rsAF['source'];
        }
    }
    // new DB query to get sequences, output in FASTA format
    $data['sequences'] = [];
    $data['sequencesFASTA'] = [];
    $rsA = mysqli_query($mysqli, "SELECT * from sequence s where s.idCode='" . $data['idCode'] . "' order by s.chain") or print mysqli_error($mysqli);
    if (mysqli_num_rows($rsA)) {
        while ($sq = mysqli_fetch_assoc($rsA)) { 
            $data['sequences'][] = $sq;
            $data['sequencesFASTA'][] = ">".$sq['header']."\n".preg_replace("/(.{60})/", "$1\n", $sq['sequence'])."\n";
        }
    }
    return $data;
}
