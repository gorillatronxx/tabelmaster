<?php
ob_start();
include 'config.class.php';  // db stuff
include 'database.class.php'; // Include database class
include 'htmlhelper.class.php'; // html routines 
include 'dbMeta.class.php'; // database meta data & help
include 'sqlWorker.class.php';

// Do a filter thing with this // FIX THIS
$f   = isset($_GET['f']) ? $_GET['f'] : "r"; // check for f 
$row = isset($row) ? $row : $row = NULL; 


// Get the essential table data needed 
$tm   = new dbMeta(); 
$sqlWorker = new sqlWorker(); 


$cols = $tm->get_db_columns(); // array w/ all metadata 
$pk   = $tm->get_pk($cols);    // str of the pk
$cols_no_pk = $tm->zap_pk_id($cols, $pk); // array w/o pk all meta data
$col_names  = $tm->get_col_names($cols);  // array col names all
$col_names_no_pk = $tm->get_col_names($cols_no_pk); // array col names w/ no pk

define("PRIMARY_KEY", $pk); // make a constant and be done
        

// Cases for flow
switch($f) {
    case "af"  : add_form($row); // ? takes no vars
        break;	
    case "r"   : $sqlWorker->show(); // show table DONE
	break;
    case "del" : $sqlWorker->del_row($_GET[PRIMARY_KEY]); // del DONE
        break;
    case "add" : $sqlWorker->add_row($col_names_no_pk, $_GET); // DONE
        break;  
    case "ud"  : $sqlWorker->update($_GET); // DONE
	break;  
    case "mod" : $sqlWorker->mod_pull_row($_GET[PRIMARY_KEY]); // DONE		
	break; 
}			

/////// Functions 

// output form 
function add_form($row) {
    $html = build_form($row);
       echo $html;
       exit; 
} 


// build form and populate as needed 
function build_form($row) {
  // if we have a row with stuff in it  
	if(isset($row)) { 
		$f = "ud"; 
		$animal_id = $row['animal_id'];
		$animal_name = $row['animal_name'];
		$animal_type = $row['animal_type'];
	} 
	// else if there is nothing in it 
	else {
		$f = "add"; 	
		$animal_id = ''; 
		$animal_name = ''; 
		$animal_type = ''; 
	}	
        
	$html = ""; 
	$html .= "<form action='$_SERVER[PHP_SELF]?' method='get' enctype='text/plain' target='_parent'>";
	$html .= "	Animal Name: <INPUT TYPE='text' NAME='animal_name' VALUE='$animal_name' SIZE='20' MAXLENGTH='20'>";  
	
        $html .= "Animal Type: <INPUT TYPE='text' NAME='animal_type' VALUE='$animal_type' SIZE='20' MAXLENGTH='20'>";  
	
        $html .= "<INPUT TYPE='hidden' NAME='f' value='$f'>"; 
	$html .= "<INPUT TYPE='hidden' NAME='animal_id' value='$animal_id'>"; 
	$html .= "<BUTTON>Submit</BUTTON>	"; 
        $html .= "<BR>"; 
        //$html .= $p; 
        
        $html .= "</form> "; 
        
	return $html; 

} // End Function 





ob_end_flush();







