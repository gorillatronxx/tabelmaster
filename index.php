<?php
ob_start();

// make include once / or auto / or put them into lib

require_once 'config.class.php';     // db config stuff
require_once 'database.class.php';   // database pdo class
require_once 'dbMeta.class.php';     // database metadata helper
require_once 'crudWorker.class.php';  // builds sql statments for pdo
require_once 'htmlhelper.class.php'; // html routines 
require_once 'filterVars.class.php'; // filter supper globals

$tm         = new dbMeta();      // create db matadata object
$crudWorker = new crudWorker();   // create sql worker 
$fv         = new filterVars();  // 

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
    case "af"  : create_row_form($form_array_add); // cols for add no pk
        break;
    case "r"   : read_data($s); // read data table 
	break;
    case "del" : $crudWorker->delete_row($safe_get[PRIMARY_KEY]); // del DONE
        break;
    case "add" : $crudWorker->create_row($safe_get); // DONE
        break;  
    case "ud"  : $crudWorker->update_row($safe_get); // DONE
	break;  
    case "mod" : $crudWorker->get_update_row($safe_get[PRIMARY_KEY]); // DONE		
	break; 
}			

// this stuff is all output to screen 

// read the table data
function read_data($s) {
    $ht   = new htmlhelper(); 
    $crud = new crudWorker();
    echo $ht->startHTML(); 
    echo '<fieldset><legend>' . LEGEND_READ . ' - ' . ucfirst(TABLE_NAME) 
            . ' sorted by ' . $ht->labelMaker($s) .'</legend>';
    echo $crud->read($s); 
    echo '</fieldset>'; 
    echo $ht->endHTML(); 
}
    
// make the Modify form 
function mod_form($array){
    $ht = new htmlhelper();
    $f = 'ud';
    $legend = LEGEND_UPDATE . " - " . ucfirst(TABLE_NAME);
    echo $ht->startHTML();     
    echo $ht->BuildStartForm($legend); 
       foreach($array as $k => $v_array) {
           echo($ht->BuildFormInsert($v_array));
        } 
    echo $ht->BuildEndForm($f); 
    echo $ht->endHTML();     
}

// Make Add form
function create_row_form($array) { 
   $ht = new htmlhelper();  
   $f = 'add'; 
   $legend = LEGEND_CREATE . " - " . ucfirst(TABLE_NAME); 
   echo $ht->startHTML();    
   echo $ht->BuildStartForm($legend); 
    foreach($array as $k => $v_array) { // use values of array? 
        echo($ht->BuildFormInsert($v_array));          
    } 
   echo $ht->BuildEndForm($f);
   echo $ht->endHTML(); 
}

ob_end_flush();
