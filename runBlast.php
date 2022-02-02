<?php
/*
 * runBlast.php
 * Sequence search using Blast
 */
// Loading global variables and DB connection
require "globals.inc.php";

// Take data from $_SESSION, loaded in search.php, if empty back to front page
if (!isset($_SESSION['queryData']) or ! $_SESSION['queryData']['seqQuery']) {
    header('Location: index.php');
}

// prepare FASTA file
// Identify file format, ">" as first char indicates FASTA
$p = strpos($_SESSION['queryData']['seqQuery'], '>');
if (!$p and ( $p !== 0)) { // strpos returns False if not found and "0" if first character in the string
    // When is not already FASTA, add header + new line
    $_SESSION['queryData']['seqQuery'] = "> User provided sequence\n" . $_SESSION['queryData']['seqQuery'];
}

// Set temporary file name to a unique value to protect from concurrent runs
$tempFile = $tmpDir . "/" . uniqId('pdb');

// Open temporary file and store query FASTA
$ff = fopen($tempFile . ".query.fasta", 'wt');
fwrite($ff, $_SESSION['queryData']['seqQuery']);
fclose($ff);

// execute Blast, command line prefix set in globals.inc.php
$cmd = $blastCmdLine . " -query " . $tempFile . ".query.fasta -out " . $tempFile . ".blast.out"; 
// DEBUG print command line
#print "$cmd\n";
exec($cmd);

// Read results file and parse hits onto $result[]
$result = file($tempFile . ".blast.out");
if (!count($result)) {
    print errorPage("Not Found", 'No results found. <p class="button" ><a href="index.php?new=1">New Search</a></p>');
    exit();
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
// end controller section ======================================================================================
?>

<!-- Results table -->
<?= headerDBW("Search results")?>

<p>Num Hits: <?= count($result) ?> </p>
<table border="0" cellspacing="2" cellpadding="4" id="blastTable">
    <thead>
        <tr>
            <th>idCode</th>
            <th>Type</th>
            <th>Header</th>
            <th>Compound</th>
            <th>E. value</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach (array_values($records) as $rr) { ?>
            <tr>
                <td><a href="getStruc.php?idCode=<?= $rr['idCode'] ?>"><?= $rr['idCode']?>_<?= $rr['sub']?></a></td>
                <td><?= $rr['tip'] ?></td>	
                <td><?= $rr['desc'] ?></td>
                <td><?= $rr['compound']?></td>
                <td><?= $rr['ev'] ?></td>
            </tr>
        <?php } ?>
    </tbody>
</table>

<p class="button"><a href="index.php?new=1">New Search</a></p>
<?= footerDBW()?>
<!-- DataTable activation-->

<script type="text/javascript">
    $(document).ready(function () {
        $('#blastTable').DataTable();
    });
</script>
