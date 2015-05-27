<?php
ob_start();
include 'config.class.php';  // db stuff
include 'database.class.php'; // Include database class
include 'htmlhelper.class.php'; // html routines 
include 'dbMeta.class.php'; // database meta data & help
include 'sqlWorker.class.php';

foreach($_GET as $k => $v) {
    $v = filter_input(INPUT_GET, '$v', FILTER_SANITIZE_STRING);
    echo $k .  "  " . $v .   "<BR>";
    echo "X = " .   $v . "<BR>"; 
    
    $safe_get = [$k => $v];      
}

echo "var dum of Safe get:<BR>";
var_dump($safe_get); 



// Do a filter thing with this // FIX THIS
$f   = isset($safe_get['f']) ? $safe_get['f'] : "r"; // check for f 

// Get the essential table data needed 
$tm   = new dbMeta(); 
$sqlWorker = new sqlWorker(); 

$cols = $tm->get_db_columns();  // array w/ all metadata 
$pk   = $tm->get_pk();          // str of the pk
$cols_no_pk = $tm->zap_pk_id(); // array w/o pk all meta data

define("PRIMARY_KEY", $pk); // make a constant and be done
     
        


$form_array_add =  $tm->buildFormArray($cols_no_pk); 

// Cases for flow
switch($f) {
    case "af"  : add_form($form_array_add); // ? takes no vars
        break;
    case "r"   : $sqlWorker->show(); // show table DONE
	break;
    case "del" : $sqlWorker->del_row($safe_get[PRIMARY_KEY]); // del DONE
        break;
    case "add" : $sqlWorker->add_row($safe_get); // DONE
        break;  
    case "ud"  : $sqlWorker->update($_GET); // DONE
	break;  
    case "mod" : $sqlWorker->mod_pull_row($_GET[PRIMARY_KEY]); // DONE		
	break; 
}			

/////// Functions 

function mod_form($array){
    $f = 'ud';
   echo BuildStartForm(); 
       foreach($array as $k => $v_array) {
           echo(BuildFormInsert($v_array));
        } 
   echo BuildEndForm($f);   
}


// Make Add form
function add_form($array) {
   $f = 'add'; 
   echo BuildStartForm(); 
        foreach($array as $k => $v_array) { // use values of array? 
        echo(BuildFormInsert($v_array));          
    } 
    echo BuildEndForm($f); 
}


// Move these out? 

function BuildStartForm() {
   $phpSelf = filter_input(INPUT_SERVER, 'PHP_SELF', FILTER_SANITIZE_URL);
   echo "<form action='$phpSelf?' method='get' enctype='text/plain' target='_parent'>";     
}

function BuildEndForm($f) {
    echo "<INPUT TYPE='hidden' NAME='f' VALUE='$f'>"; // this is coded in 
    echo "<BUTTON>Submit</BUTTON>"; 
    echo "</FORM>";  
}


// HTML Helper functions for forms
function BuildFormInsert($v) {
    $value = isset($v['VALUE']) ? $v['VALUE'] : '';        // value set or not
    $lable = ($v['TYPE'] === "HIDDEN") ? '' : $v['LABLE']; // hide HIDDEN type lable  
    // generate 
    $i = "<lable> $lable <INPUT TYPE='$v[TYPE]' NAME='$v[NAME]' VALUE='$value' SIZE='$v[SIZE]' MAXLENGTH='$v[SIZE]'></lable><BR>"; 
    return $i;  
}


// Preps the mod row data, puts it into form array for auto gen update form 
function mod_prep($row){
    $tm   = new dbMeta(); 
    $cols = $tm->get_db_columns(); // array w/ all metadata 
    $form_array_mod =  $tm->buildFormArray($cols); // make to nice form ready
    // insert the values to the form array for mod form
    foreach($row as $k => $v) {
        $form_array_mod[$k]['VALUE'] = $v;
    }
    mod_form($form_array_mod);  // returns array ready to be processed 
}

ob_end_flush();







