<?php
ob_start();
include 'config.class.php';  // db stuff
include 'database.class.php'; // Include database class
include 'htmlhelper.class.php'; // html routines 
include 'dbMeta.class.php'; // database meta data & help

// Do a filter thing with this // FIX THIS
$f  = isset($_GET['f']) ? $_GET['f'] : "r"; // check for f 

// Get the essential table data needed 
$tm   = new dbMeta(); 
$cols = $tm->get_db_columns(); // array w/ all metadata 
$pk   = $tm->get_pk($cols);    // str of the pk
$cols_no_pk = $tm->zap_pk_id($cols, $pk); // array w/o pk all meta data
$col_names  = $tm->get_col_names($cols);  // array col names all
$col_names_no_pk = $tm->get_col_names($cols_no_pk); // array col names w/ no pk

define("PRIMARY_KEY", $pk); // make a constant and be done
        
//var_dump($cols, $tm); 
//var_dump($pk); 
//var_dump($cols_no_pk);
//var_dump($col_names); 
//var_dump($col_names_no_pk); 
//$columns = get_sql_columns(); // Gets all the column names in array

// Cases for flow
switch($f) {
    case "af"  : add_form($row); // ? takes no vars
        break;	
    case "r"   : show(); // show table DONE
	break;
    case "del" : del_row($_GET[PRIMARY_KEY]); // del DONE
        break;
    case "add" : add_row($col_names_no_pk, $_GET); // DONE
        break;  
    case "ud"  : update($_GET); // DONE
	break;  
    case "mod" : mod_pull_row($_GET[PRIMARY_KEY]); // DONE		
	break; 
}			

/////// Functions 

// Update row in DB
function update($vars_get) {
    $database = new Database();
    $del_id = $vars_get[PRIMARY_KEY];

    // Need to automate this like (add row)
    unset($vars_get['f']); 
    unset($vars_get[PRIMARY_KEY]); 

    $list_of_keys = array_keys($vars_get);
    $update_fields = NULL; 
    $x = NULL;  		
    foreach($list_of_keys as $f) {
        $x .= $f . " =:" . $f . ", "; 	
    }
    $update_fields = rtrim($x, $charlist = ', ');

    $sql = NULL; 
    $sql .= "UPDATE " . TABLE_NAME . " SET " . $update_fields; 
    $sql .= " WHERE " . PRIMARY_KEY . " = " . $del_id; 
    $database->query($sql);
    // Run the bind statement in a loop 
    foreach($vars_get as $k => $v) {		
        $k = ":" . $k; // add the :			
	$database->bind($k, $v); 	
    }
    $database->execute();
    move_along();
}	

// Pull one row to modify send to form 
function mod_pull_row($id){
    $database = new Database();
    $sql = NULL; 
    $sql .= "SELECT * FROM " . TABLE_NAME . " WHERE " . PRIMARY_KEY . " = "; 
    $sql_bind = ":" . PRIMARY_KEY;
    $sql .= $sql_bind; 
    $database->query($sql);
    $database->bind($sql_bind,$id);
    $database->execute();
    $row = $database->single(); 	
    add_form($row);	
} // END Function 

// Adds record to the DB
function add_row($table_fields ,$vars_get) {
 
    // Clean the get vars with a white list so db and all are happy 
    $white_list   = $table_fields;          // 
    $list_of_keys = array_keys($vars_get);  // From the get 
    $diff = array_diff($list_of_keys,$white_list);    // Move to OBJ or function  
    foreach ($diff as $d) {		
	unset($vars_get[$d]); // ace the different	
    }	 
    $database = new Database();
    // build insert fields 
    $insert_fields = NULL; 
    $insert_fields = implode(", " ,$table_fields);
    $insert_fields = "(" . $insert_fields . ")"; 

    // build bind fields 
    $bind_fields = NULL; 
    foreach($table_fields as $bind_field) {
        $bind_fields .= ":". $bind_field . ",";
    }
    $bind_fields = rtrim($bind_fields, $charlist = ',');
    $bind_fields = "(" . $bind_fields . ")"; 
	
    // build the SQL
    $sql = "INSERT INTO "  . TABLE_NAME . " ". $insert_fields . " VALUES " . $bind_fields; 
    $database->query($sql); 
	
    // Run the bind statement in a loop 
    foreach($vars_get as $k => $v) {
        $k = ":" . $k; // add the :
	$database->bind($k, $v); 	
    }
    $database->execute(); 
    move_along();
} // END Function 

// Deletes one row 
function del_row($id) {
    $_GET['f'] = null; 
    $database = new Database();	
    $sql = NULL;
    $sql .= "DELETE FROM " . TABLE_NAME . " WHERE " . PRIMARY_KEY . " = " . $id; 		
    $database->query($sql); 	  
    $database->execute();
    move_along(); // move to database class? 
} // END Function 


// output form 
function add_form($row) {
    $html = build_form($row);
       echo $html;
       exit; 
} 


// Just show the data in a table sorted
function show() {
    $database = new Database(); 
    $ht       = new HtmlHelper();
    // Build the sql piece by piece to put in $vars    
    $sql = null; 
    $sql .= "SELECT * FROM " . TABLE_NAME . " ORDER BY " . SORT_FIELD; 
    $database->query($sql);  
    $result = $database->resultset(); 
    /*** send to the linker  param 1 - data  --- param 2 - unique db row  ***/
    $rows = $ht->linker($result, PRIMARY_KEY); // Builds link on id field
    echo  $ht->array2table($rows); // Prints out a table 
    add_button(); // adds a stupid button
    exit; 	
} // end function 


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
        $html .= $p; 
        
        $html .= "</form> "; 
        
	return $html; 

} // End Function 

// put in the helper 
function move_along() {
	header("location:$_SERVER[PHP_SELF]") ;	
	exit; 	
}

function add_button() {
	echo "<BR><A HREF='$_SERVER[PHP_SELF]?f=af'>ADD</A>"; 
}



ob_end_flush();







