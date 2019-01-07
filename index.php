<?php
/*
 * index.php
 * main form
 */
# Loading global variables and DB connection
include "globals.inc.php";
//
// $_SESSION['queryData'] array holds data from previous forms, 
// if empty it should be initialized to avoid warnings, and set defaults
// also a ...?new=1 allows to clean it from the URL.
//

if (isset($_REQUEST['new']) or !isset($_SESSION['queryData'])) {
    $_SESSION['queryData'] = [
        'minRes' => '0.0',
        'maxRes' => 'Inf',
        'query' => ''
    ];
}
#
print headerDBW("PDB Browser rev. 2018");
#Main Form follows
?>
<form name="MainForm" action="search.php" method="POST" enctype="multipart/form-data">
    <div class="row" style="border-bottom: solid 1px">
        <div class="form-group">
            <label><b>PDB Id</b></label>
            <input type="text" name="idCode" value="<?php print $_SESSION['queryData']['idCode']?>" size="5" maxlength="4"/>
        </div>
    </div>
    <div class="row">
        <h3>Search fields</h3>
    </div>
    <div class="row">
        <div class="col-md-4">
            <div class="form-group">
                <label>Resolution</label>
                <p>                    
                    From <input type="text" name="minRes" value="<?php print $_SESSION['queryData']['minRes'] ?>" size="5">
                    to <input type="text" name="maxRes" value="<?php print $_SESSION['queryData']['maxRes'] ?>" size="5" >
                </p>
            </div>

        </div>
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="form-group">
                <label>Compound Type:</label>
                <div class="form-check">
                    <?php
                    /* 
                     * input options from $compTypeArray[] array
                     */
                    foreach (array_keys($compTypeArray) as $idCompType ) {?>
                        <input class="form-check-input" type="checkbox" 
                               name="idCompType[<?php print $idCompType ?>]" />  <?php print $compTypeArray[$idCompType]."\n" ?>                       
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-4">
            <div class="form-group">
                <label>Exp. Type:</label>
                <div class="form-check">
                    <?php
                    /* 
                     * input options from $expClasseArray
                     */
                    foreach (array_keys($expClasseArray) as $idExpClasse) { ?>
                        <input class="form-check-input" type="checkbox" name="idExpClasse[<?php print $rsF['idExpClasse'] ?>]" /> <?php print $expClasseArray[$idExpClasse] . "\n" ?>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>

    <div class="row" style="border-bottom: 1px solid">
        <div class="col-md-6">
            <label><h4> Text Search</h4></label>
            <div class="form-group">
                <input type="text" name="query" value="<?php print $_SESSION['queryData']['query'] ?>" style="width:100%" />
            </div>
        </div>
    </div>
    <div class="row" style="border-bottom: 1px solid">
        <div class="col-md-6">
            <label><h4>Sequence search</h4></label>
            <div class="form-group">
                <textarea class="form-control" name="seqQuery" rows="4" cols="60" style="width:100%"></textarea><br>
                Upload sequence file: <input type="file" name="seqFile" value="" width="50" style="width:100%"/>
            </div>
        </div>
    </div>
    <div class="row">
        <p>
    <button type='submit' class="btn btn-primary">Submit</button>
    <button type='reset' class="btn btn-primary">Reset</button>
    <button class="btn btn-primary" onclick="window.location.href='index.php=?new=1">New Search</button>
    </p>
    </div>
</form>
<?php
print footerDBW();

