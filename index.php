<?php
ob_start();

// make include once / or auto / or put them into lib

require_once 'config.class.php';      // db config stuff
require_once 'database.class.php';    // database pdo class
require_once 'dbMeta.class.php';      // database metadata helper
require_once 'crudWorker.class.php';  // builds sql statments for pdo
require_once 'htmlhelper.class.php';  // html routines 
require_once 'filterVars.class.php';  // filter supper globals

$meta   = new dbMeta();       // create matadata object of table 
$pk     = $meta->get_pk();            // str of the pk

$crudWorker = new crudWorker();   // create sql worker 

$filter   = new filterVars();   // filter vars 
$safe_get = $filter->SafeGet();     // get the filtered get 

define("PRIMARY_KEY", $pk);     // make primary key konstant

$action = isset($safe_get['action']) ? $safe_get['action'] : "r"; // check for f 

$s = isset($safe_get['s']) ? $safe_get['s'] : SORT_FIELD; // sort field


// Cases for the CRUD or forms
switch($action) {
    // Create row 
    case "c" : $crudWorker->create_row($safe_get); // param = safe get array  
        break;
    // Read database
    case "r"   : read_data($s); // param = sort string
	break;
    // Update row 
    case "u"  : $crudWorker->update_row($safe_get); // param = safe get array 
	break;
    // Delete row 
    case "d" : $crudWorker->delete_row($safe_get[PRIMARY_KEY]); // param = pk string
        break;
    // Create form 
    case "crf" : create_form(); 
        break;
    // Update form 
    case "uf" : $crudWorker->get_update_row($safe_get[PRIMARY_KEY]); // param = id string		
	break; 
}			

// this stuff is all output to screen (page views built) 
   
// make the Modify form 
function mod_form($array){
    $ht = new htmlhelper();
    $action = 'u';
    $legend = LEGEND_UPDATE . " - " . ucfirst(TABLE_NAME);
    echo $ht->startHTML();     
    echo $ht->BuildStartForm($legend); 
       foreach($array as $k => $v_array) {
           echo($ht->BuildFormInsert($v_array));
        } 
    echo $ht->BuildEndForm($action); 
    echo $ht->endHTML();     
}



// Make Add form
function create_form() { 
   $action = 'c'; 
   $legend = LEGEND_CREATE . " - " . ucfirst(TABLE_NAME); 
   $ht     = new htmlhelper();
   $meta   = new dbMeta(); 
   
            //$cols_no_pk  = $meta->zap_pk_id(); // array w/o pk all meta data

   $cols = $meta->get_tabel_columns(); // get the table cols from TABLE SHOW 
   $array = $meta->buildFormArray($cols); // columns for add form
   
   echo $ht->startHTML();    
   echo $ht->BuildStartForm($legend); 
    foreach($array as $k => $v_array) { // use values of array? 
        echo($ht->BuildFormInsert($v_array));          
    } 
   echo $ht->BuildEndForm($action);
   echo $ht->endHTML(); 
}





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



ob_end_flush();
