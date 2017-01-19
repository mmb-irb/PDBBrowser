<?php
/*
 * getStruc.php Shows data for a PDB entry
 */
// load global vars and includes
include "globals.inc.php";
//get data for the structure requested
$sql = "SELECT e.* from entry e where e.idCode='" . $_REQUEST['idCode'] . "'";
$rs = mysql_query($sql) or print mysql_error();
if (!mysql_num_rows($rs)) { //search is empty
    print errorPage('Not Found', 'The requested structure is not available');
} else {
    $data = mysql_fetch_array($rs);
    print headerDBW($_REQUEST['idCode']);
    ?>
    <table border="0" cellspacing="2" cellpadding="4">
        <tbody>
            <tr>
                <td>PDB reference</td>
                <td><?php print $data['idCode'] ?></td>
                <td rowspan="5">
                    <a href="http://www.pdb.org/pdb/explore.do?structureId=<?php print $data['idCode'] ?>">
                        <img src="http://www.pdb.org/pdb/images/<?php print strtolower($data['idCode']) ?>_bio_r_250.jpg" border="0" width="250" height="250"><br>
                        Link to Protein Data Bank</a>
                </td>
            </tr>
            <tr>
                <td>Header</td>
                <td><?php print $data['header'] ?></td>
            </tr>
            <tr>
                <td>Title</td>
                <td><?php print $data['compound'] ?></td>
            </tr>
            <tr>
                <td>Resolution</td>
                <td>
                    <?php
                    if ($data['resolution'])
                        print $data['resolution'];
                    else
                        print "N.D.";
                    ?>
                </td>
            </tr>
            <tr>
                <td>Ascession Date</td>
                <td><?php print $data['ascessionDate'] ?></td>
            </tr>
            <tr>
                <?php // $expTypeArray is generated in globals.inc.php?>
                <td>Experiment type</td> 
                <td colspan="2"><?php print $expTypeArray[$data['idExpType']]['ExpType'] ?></td>
            </tr>
            <tr>
                <td>Authors</td>
                <td colspan="2">
                    <?php
                    // new DB query to get authors
                    $rsA = mysql_query("SELECT * from author a, author_has_entry ae where ae.idCode='" . $data['idCode'] . "' and a.idAuthor = ae.idAuthor order by a.author") or print mysql_error();
                    if (mysql_num_rows($rsA)) {
                        $auts = array();
                        while ($rsAF = mysql_fetch_array($rsA))
                            $auts[] = $rsAF['author'];
                        print join(", ", $auts);
                    }
                    ?>
                </td>
            </tr>
            <tr>
                <td>Source</td>
                <td colspan="2">
                    <?php
                    // new DB query to get sources
                    $rsA = mysql_query("SELECT * from source s, entry_has_source es where es.idCode='" . $data['idCode'] . "' and s.idSource = es.idSource order by s.source") or print mysql_error();
                    if (mysql_num_rows($rsA)) {
                        $sources = array();
                        while ($rsAF = mysql_fetch_array($rsA))
                            $sources[] = $rsAF['source'];
                        print join(", ", $sources);
                    }
                    ?>
                </td>
            </tr>
            <tr>
                <td>Sequence(s)</td>
                <td colspan="2">
                    <?php
                    // new DB query to get sequences, output in FASTA format
                    $rsA = mysql_query("SELECT * from sequence s where s.idCode='" . $data['idCode'] . "' order by s.chain") or print mysql_error();
                    if (mysql_num_rows($rsA)) {
                        while ($sq = mysql_fetch_array($rsA)) {
                            print "<pre>" . $sq['header'] . "\n" . preg_replace("/(.{60})/", "$1\n", $sq['sequence']) . "</pre>";
                        }
                    }
                    ?>
                </td>
            </tr>
        </tbody>
    </table>
    <?php
    print footerDBW();
}