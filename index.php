<?php
/*
 * index.php
 * main form
 */
# Loading global variables and DB connection
include "globals.inc.php";
// $_SESSION['queryData'] array holds data from previous forms, 
// if empty it should be initialized to avoid warnings, and set defaults
// also a ...?new=1 allow to clean it from the URL.
if (isset($_REQUEST['new']) or ! isset($_SESSION['queryData'])) {
    $_SESSION['queryData'] = array(
        'minRes' => '0.0',
        'maxRes' => 'Inf',
        'query' => ''
    );
}
#
print headerDBW("PDB Browser rev. 2017");
#Main Form follows
?>
<form name="MainForm" action="search.php" method="POST" enctype="multipart/form-data">
    <table border="0" cellspacing="2" cellpadding="4" align="center">
        <tbody>
            <tr>
                <td><b>PDB Id</b></td>
                <td><input type="text" name="idCode" value="" size="5" maxlength="4"/></td>
            </tr>
            <tr>
                <td colspan="2">&nbsp;</td>
            </tr>
            <tr>
                <td colspan="2"><b>Search fields</b></td>
            </tr>
            <tr>
                <td>Resolution:</td>
                <td>
                    From <input type="text" name="minRes" value="<?php print $_SESSION['queryData']['minRes'] ?>" size="5">
                    to <input type="text" name="maxRes" value="<?php print $_SESSION['queryData']['maxRes'] ?>" size="5" />
                </td>
            </tr>
            <tr>
                <td>Compound Type:</td>
                <td>
                    <?php
                    /* We obtain the possible fields from the comptype table, 
                     * alternatively this could be done in globals.inc.php and stored as $compTypeArray
                     * Then here we will use $compTypeArray instead
                     *
                     *  foreach (array_values($compTypeArray) as $c) { 
                     *   <input type="checkbox" name="idCompType[<?php print $c ?>]" /> <?php print $c['type'] ?>-->
                     * } 
                     * 
                     * Note that input names build idCompType[] array
                     */
                    $rs = mysql_query("SELECT * from comptype");
                    while ($rsF = mysql_fetch_array($rs)) {
                        ?>
                        <input class="form-check-input" type="checkbox" name="idCompType[<?php echo $rsF['idCompType'] ?>]" /> <?php echo $rsF['type'] . " \n" ?>
                    <?php } ?>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label>Exp. Type:</label>
                <div class="form-check">
                    <?php
                    /* We obtain the possible fields from the expClasse table,
                     * that is a condensed version of expType, alternatively this could be done in globals.inc.php
                     * as compType
                     * input names build idExpClasse[] array
                     */
                    $rs = mysql_query("SELECT * from expClasse  order by ExpClasse");
                    while ($rsF = mysql_fetch_array($rs)) {
                        ?>
                        <input class="form-check-input" type="checkbox" name="idExpClasse[<?php echo $rsF['idExpClasse'] ?>]" /> <?php echo $rsF['expClasse'] . "\n" ?>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <label><h4> Text Search</h4></label>
            <div class="form-group">
                <input type="text" name="query1" value="<?php print $_SESSION['queryData']['query1'] ?>" size="60" />
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <label><h4>Sequence search</h4></label>
            <div class="form-group">
                    <textarea class="form-control" name="seqQuery" rows="4" cols="60"></textarea><br>
                    Upload sequence file: <input type="file" name="seqFile" value="" width="50" />
            </div>
        </div>
    </div>
    <button type='submit' class="btn btn-primary">Submit</button>
    <button type='reset' class="btn btn-primary">Reset</button>
    <p class="btn btn-primary"><a href="index.php?new=1">New Search</a></p>
</form>
<?php 
print footerDBW();
