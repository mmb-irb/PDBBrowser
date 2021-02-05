<?php
/*
 * getStruc.php Shows data for a PDB entry
 */

// load global vars and includes
require "globals.inc.php";

//get data for the structure requested
$sql = "SELECT e.* from entry e where e.idCode='" . $_REQUEST['idCode'] . "'";
$rs = mysqli_query($mysqli, $sql) or print mysqli_error($mysqli);
if (!mysqli_num_rows($rs)) { //search is empty
    print errorPage('Not Found', 'The requested structure is not available');
    exit();
}
// Get Main entry
$data = mysqli_fetch_assoc($rs);
// new DB query to get authors
$data['auts'] = '';
$rsA = mysqli_query($mysqli, "SELECT * from author a, author_has_entry ae where ae.idCode='" . $data['idCode'] . "' and a.idAuthor = ae.idAuthor order by a.author") or print mysqli_error($mysqli);
if (mysqli_num_rows($rsA)) {
    $auts = [];
    while ($rsAF = mysqli_fetch_assoc($rsA)) {
        $auts[] = $rsAF['author'];
    }
    $data['auts'] = join(", ", $auts);
}
// new DB query to get sources
$rsA = mysqli_query($mysqli, "SELECT * from source s, entry_has_source es where es.idCode='" . $data['idCode'] . "' and s.idSource = es.idSource order by s.source") or print mysqli_error($mysqli);
$data['sources'] = '';
if (mysqli_num_rows($rsA)) {
    $sources = [];
    while ($rsAF = mysqli_fetch_assoc($rsA)) {
        $sources[] = $rsAF['source'];
    }
    $data['sources'] = join(", ", $sources);
}
// new DB query to get sequences, output in FASTA format
$data['sequences'] = '';
$rsA = mysqli_query($mysqli, "SELECT * from sequence s where s.idCode='" . $data['idCode'] . "' order by s.chain") or print mysqli_error($mysqli);
$sequences = [];
if (mysqli_num_rows($rsA)) {
    while ($sq = mysqli_fetch_assoc($rsA)) { 
        $sequences[] = "<pre>>". $sq['header']."\n".preg_replace("/(.{60})/", "$1\n", $sq['sequence'])."</pre>";
    }
    $data['sequences'] = join("\n", $sequences);
}
//end controller =======================================================================================================================
?>

<?= headerDBW($_REQUEST['idCode'])?>
<table class="table table-hover">
    <tbody>
        <tr>
            <td>PDB reference</td>
            <td><?= $data['idCode'] ?></td>
            <td rowspan="5">
                <a href="http://www.pdb.org/pdb/explore.do?structureId=<?= $data['idCode'] ?>">
                    <img src="http://mmb.pcb.ub.es/api/pdb/<?= strtolower($data['idCode']) ?>.png" border="0" width="250" ><br>
                        Link to Protein Data Bank</a>
            </td>
        </tr>
        <tr>
            <td>Header</td>
            <td><?= $data['header'] ?></td>
        </tr>
        <tr>
            <td>Title</td>
            <td><?= $data['compound'] ?></td>
        </tr>
        <tr>
            <td>Resolution</td>
            <td>
                <?php if ($data['resolution']) {?>
                    <?= $data['resolution'] ?>
                <?    } else {?>
                    N.D.
                <?php } ?>
            </td>
        </tr>
        <tr>
            <td>Ascession Date</td>
            <td><?= $data['ascessionDate'] ?></td>
        </tr>
        <tr>
            <!--$expTypeArray is generated in globals.inc.php-->
            <td>Experiment type</td> 
            <td colspan="2"><?= $expTypeArray[$data['idExpType']]['ExpType'] ?></td>
        </tr>
        <tr>
            <td>Authors</td>
            <td colspan="2"><?= $data['auts']?></td>
        </tr>
        <tr>
            <td>Source</td>
            <td colspan="2"><?= $data['sources'] ?></td>
        </tr>
        <tr>
            <td>Sequence(s)</td>
            <td colspan="2"><?= $data['sequences'] ?></td>
        </tr>
    </tbody>
</table>
<?= footerDBW();?>