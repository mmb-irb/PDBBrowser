<?php
// load global vars and includes
include "globals.inc.php";
// Store input data in $_SESSION to reload initial form if necessary
$_SESSION['queryData'] = $_REQUEST;
// Selection of action to do
// 1. IdCode -> Results page
// 2. Sequence input -> runBlast
// 3. Other -> search on DB
//
// 1. Redirection to the requested entry if code selected
if ($_REQUEST['idCode']) {
    header('Location: getStruc.php?idCode=' . $_REQUEST['idCode']);
    // 2. Sequence input. If uploaded file, this takes preference
} elseif ($_FILES['seqFile']['name'] or $_REQUEST['seqQuery']) {
    if (($_FILES['seqFile']['tmp_name'])) {
        $_SESSION['queryData']['seqQuery'] = file_get_contents($_FILES['seqFile']['tmp_name']);
    }
    // Redirect to Blast if sequence, data is already stored in $_SESSION
    header('Location: runBlast.php');
} else {
    //  3. normal search, Bluiding SQL SELECT from the input form
    //     $ANDConds will contain all SQL conditions found in the form
    $ANDconds = ["True"]; // required to fulfill SQL syntax if form is empty
    //  Resolution, we consider only cases where user has input something
    if (($_REQUEST['minRes'] != '0.0') or ( $_REQUEST['maxRes'] != 'Inf')) {
        if ($_REQUEST['minRes'] != '0.0') {
            $ANDconds[] = "e.resolution >= " . $_REQUEST['minRes'];
        }
        if ($_REQUEST['maxRes'] != 'Inf') {
            $ANDconds[] = "e.resolution <= " . $_REQUEST['maxRes'];
        }
    }
    //     Compound type $ORconds holds options selected
    if (isset($_REQUEST['idCompType'])) { //should be isset as idCompType come from checkboxes
        $ORconds = [];
        foreach (array_keys($_REQUEST['idCompType']) as $k) {
            $ORconds[] = " e.idCompType = " . $k;
        }
        $ANDconds[] = "(" . join(" OR ", $ORconds) . ")";
    }
    //     Classe of experiment
    if (isset($_REQUEST['idExpClasse'])) {//should be isset as idExpClasse come from checkboxes
        $ORconds = [];
        foreach (array_keys($_REQUEST['idExpClasse']) as $k) {
            $ORconds[] = " et.idExpClasse = " . $k;
        }
        $ANDconds[] = "(" . join(" OR ", $ORconds) . ")";
    }
    //    text query, adapted to use fulltext indexes, $textFields is defined in globals.inc.php and
    // lists all text fields to be searched in.
    if ($_REQUEST['query']) {
        $ORconds = [];
        foreach (array_values($textFields) as $field) {
            $ORconds[] = "MATCH (" . $field . ") AGAINST ('" . $_REQUEST['query'] . "' "
                    . "IN BOOLEAN MODE)";
        }
        $ANDconds[] = "(" . join(" OR ", $ORconds) . ")";
    }
    //    text query without fulltext indexes
    //    if ($_REQUEST['query']){
    //        foreach (split (' ',$_REQUEST['query']) as $wd) {
    //            $ANDconds=array();
    //            foreach (array_values($textFields) as $field) {
    //                $ORconds[] = $field." like '%".$wd."%'";
    //            }
    //            $ANDconds[] = "(".join (" OR ", $ORconds).")";
    //        }
    //    }
    //    main SQL string, make sure that all tables are joint, and relationships included
    // SELECT columns FROM tables WHERE Conditions_from_relationships AND Conditions_from_query_Form
    $sql = "SELECT distinct e.idCode,e.header,e.compound,e.resolution,s.source,et.expType FROM 
        expType et, author_has_entry ae, author a, source s, entry_has_source es, entry e, sequence sq WHERE
        e.idExpType=et.idExpType AND
        ae.idCode=e.idCode and ae.idAuthor=a.idAuthor AND
        es.idCode=e.idCode and es.idsource=s.idSource AND
        e.idCode = sq.idCode AND
        " . join(" AND ", $ANDconds);
//    Ordering will be done by the DataTable element using JQuery, if not available can also be done from the SQL 
//    switch ($order) {
//        case 'idCode':
//        case 'header':
//        case 'compound':
//        case 'resolution':
//            $sql .= " ORDER BY e." . $order;
//            break;
//        case 'source':
//            $sql .= " ORDER BY s.source";
//            break;
//        case 'expType':
//            $sql .= " ORDER BY et.expType";
//            break;
//    }
    if (!isset($_REQUEST['nolimit'])) {
        $sql .= " LIMIT 5000"; // Just to avoid too long listings when testing
    }
#DEBUG
// print "<p>$sql</p>";
    //     DB query
    $rs = mysqli_query($mysqli,$sql) or print mysqli_error($mysqli);
    //     We check whether there are results to show
    if (!mysqli_num_rows($rs)) {
        print errorPage("Not Found", "No results found.");
    } else {
        //        Results table formated with DataTable
        print headerDBW("Search results");
        print "Num Hits: " . mysqli_num_rows($rs);
        ?>
        <table border="0" cellspacing="2" cellpadding="4" id="dataTable">
            <thead>
                <tr>
                    <th>idCode</th>
                    <th>Header</th>
                    <th>Compound</th>
                    <th>Resolution</th>
                    <th>Exp. Type</th>
                    <th>Source</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($rsF = mysqli_fetch_assoc($rs)) { ?>
                    <tr>
                        <td><a href="getStruc.php?idCode=<?php print $rsF['idCode'] ?>">
                                <?php print $rsF['idCode'] ?></a></td>
                        <td><?php print ucwords(strtolower($rsF['header'])) ?></td>
                        <td><?php print ucwords(strtolower($rsF['compound'])) ?></td>
                        <td><?php print $rsF['resolution'] ?></td>
                        <td><?php print $rsF['expType'] ?></td>
                        <td><?php print ucwords(strtolower($rsF['source'])) ?></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
        <p class="button"><a href="index.php?new=1">New Search</a></p>
        <script type="text/javascript">
        <!-- this activates the DataTable element when page is loaded-->
            $(document).ready(function () {
                $('#dataTable').DataTable();
            });
        </script>
        <?php
        print footerDBW();
    }
}
