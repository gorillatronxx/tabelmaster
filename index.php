<?php
ob_start();

// make include once / or auto / or put them into lib
// Make good docs of all methods 

include 'config.class.php';     // db config stuff
include 'database.class.php';   // database pdo class
include 'dbMeta.class.php';     // database metadata helper
include 'sqlWorker.class.php';  // builds sql statments for pdo
include 'htmlhelper.class.php'; // html routines 
include 'filterVars.class.php'; // filter supper globals

$tm        = new dbMeta();      // create db matadata object
$sqlWorker = new sqlWorker();   // create sql worker 
$fv        = new filterVars();  // 

$safe_get = $fv->SafeGet();     // get the filtered get 
$pk = $tm->get_pk();            // str of the pk
define("PRIMARY_KEY", $pk);     // make primary key konstant

$f = isset($safe_get['f']) ? $safe_get['f'] : "r"; // check for f 
$s = isset($safe_get['s']) ? $safe_get['s'] : SORT_FIELD; // sort field

// Condence move these inside class
$cols_no_pk = $tm->zap_pk_id(); // array w/o pk all meta data
$form_array_add =  $tm->buildFormArray($cols_no_pk); // columns for add form

// Cases 
switch($f) {
    case "af"  : add_form($form_array_add); // ? takes no vars
        break;
    case "r"   : show_data($s); // show table DONE
	break;
    case "del" : $sqlWorker->del_row($safe_get[PRIMARY_KEY]); // del DONE
        break;
    case "add" : $sqlWorker->add_row($safe_get); // DONE
        break;  
    case "ud"  : $sqlWorker->update($safe_get); // DONE
	break;  
    case "mod" : $sqlWorker->mod_pull_row($safe_get[PRIMARY_KEY]); // DONE		
	break; 
}			

// this stuff is all output to screen 

// show the table data
function show_data($s) {
    $ht = new htmlhelper(); 
    $sq = new sqlWorker;
    echo $ht->startHTML(); 
    echo '<fieldset><legend> Viewing - ' . ucfirst(TABLE_NAME) 
            . ' sorted by ' . $ht->labelMaker($s) .'</legend>';
    echo $sq->show($s); 
    echo '</fieldset>'; 
    echo $ht->endHTML(); 
}
    
// make the Modify form 
function mod_form($array){
    $ht = new htmlhelper();
    $f = 'ud';
    $legend = LEGEND_MODIFY . " - " . ucfirst(TABLE_NAME);
    echo $ht->startHTML();     
    echo $ht->BuildStartForm($legend); 
       foreach($array as $k => $v_array) {
           echo($ht->BuildFormInsert($v_array));
        } 
    echo $ht->BuildEndForm($f); 
    echo $ht->endHTML();     
}

// Make Add form
function add_form($array) { 
   $ht = new htmlhelper();  
   $f = 'add'; 
   $legend = LEGEND_ADD . " - " . ucfirst(TABLE_NAME); 
   echo $ht->startHTML();    
   echo $ht->BuildStartForm($legend); 
    foreach($array as $k => $v_array) { // use values of array? 
        echo($ht->BuildFormInsert($v_array));          
    } 
   echo $ht->BuildEndForm($f);
   echo $ht->endHTML(); 
}

ob_end_flush();
