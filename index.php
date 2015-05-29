<?php
ob_start();

// make include once / or auto / or put them into lib
// Make good docs of all methods 

include 'config.class.php';     // db config stuff
include 'database.class.php';   // database pdo class
include 'dbMeta.class.php';     // database metadata helper
include 'sqlWorker.class.php';  // builds sql statments for pdo
include 'htmlhelper.class.php'; // html routines 

$tm        = new dbMeta();      // create db matadata object
$sqlWorker = new sqlWorker();   // create sql worker 


// Filter get and return as another array
foreach($_GET as $k => $v) {
    $x = filter_input(INPUT_GET, $k, FILTER_SANITIZE_STRING);
    $safe_get[$k] = $x; // array of safe get values      
}


// Set a defauult for f
$f  = isset($safe_get['f']) ? $safe_get['f'] : "r"; // check for f 
$pk = $tm->get_pk();          // str of the pk

define("PRIMARY_KEY", $pk); // make primary key konstant
     
// Condence move these inside class
$cols_no_pk = $tm->zap_pk_id(); // array w/o pk all meta data
$form_array_add =  $tm->buildFormArray($cols_no_pk); // columns for add form

// Cases 
switch($f) {
    case "af"  : add_form($form_array_add); // ? takes no vars
        break;
    case "r"   : $sqlWorker->show(); // show table DONE
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

/////// Functions 
// make the Modify form 
function mod_form($array){
    $ht = new htmlhelper();
    $f = 'ud';
    echo $ht->BuildStartForm(); 
       foreach($array as $k => $v_array) {
           echo($ht->BuildFormInsert($v_array));
        } 
    echo $ht->BuildEndForm($f);   
}


// Make Add form
function add_form($array) {
   $ht = new htmlhelper();  
   $f = 'add'; 
   echo $ht->BuildStartForm(); 
    foreach($array as $k => $v_array) { // use values of array? 
        echo($ht->BuildFormInsert($v_array));          
    } 
   echo $ht->BuildEndForm($f); 
}


ob_end_flush();







