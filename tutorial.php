<?php
ob_start();
include 'config.class.php';  // db stuff
include 'database.class.php'; // Include database class
include 'htmlhelper.class.php'; // html routines 

$table_name = TABLE_NAME; // from the config 
$sort_field = SORT_FIELD; // From the config: to sort the thml table 

$columns_names_and_types = NULL; 
$row = NULL;
$del_id = NULL; 

$f  = isset($_GET['f']) ? $_GET['f'] : "r"; // check for f 

$columns = get_sql_columns($table_name); // Gets all the column names in array
$id_field = get_pk($table_name);               // gets the primary key as str


// remove the id_field from the $colums array to make table fields 

foreach ($columns as $key => $value) {
    if($id_field === $value) {
        unset($columns[$key]); // remove the pk from the 
    } else {
        $table_fields[] = $value; // set table fields 
    }
}

/***

Need to work on the add form so that it it  auto or a temp late
 or 
 put it out on another page. So that it can be modified 

Add the html helper class to build the stuff???? 


***/


// Cases for flow
	switch($f) {
                case "af"  : add_form($table_name, $row); // ? takes no vars
			break;	
		case "r"   : show($table_name, $sort_field, $id_field); // show table DONE
			break;
		case "del" : del_row($table_name, $_GET[$id_field], $id_field); // del DONE
			break;

		case "add" : add_row($table_name, $table_fields, $_GET); // DONE
			break;  
		case "ud"  : update($table_name, $id_field, $_GET); // DONE
			break;  
		case "mod" : mod_pull_row($table_name,$id_field, $_GET[$id_field]); // DONE		
			break; 
	}			

/////// Functions 

// Gets the name of all the columns in the table         
function get_sql_columns($table_name) {    
    $database = new Database();
    $sql = NULL;
    $sql .= 'SHOW COLUMNS FROM ' . $table_name;
    $database->query($sql);
    $database->execute();
    $raw_column_data = $database->resultset();
  
   //var_dump($raw_column_data); exit; 
    
    
    foreach($raw_column_data as $outer_key => $array) {
            foreach($array as $inner_key => $value){
         
                if ($inner_key === 'Field'){
                    if (!(int)$inner_key) {
                        $column_names[] = $value;
                    }
                }
                
            }
    }
    return $column_names; // array  
}    // End function 
     
// Gets the name of all the columns in the table         
function get_sql_columns_with_size($table_name) {    
    $database = new Database();
    $sql = NULL;
    $sql .= 'SHOW COLUMNS FROM ' . $table_name;
    $database->query($sql);
    $database->execute();
    $raw_column_data = $database->resultset();
  
   //var_dump($raw_column_data);  
    
    foreach($raw_column_data as $outer_key => $array) {
        $column_names_and_types[] = array($array['Field'] , $array['Type']); 
    }     
    //var_dump($columns_names_and_types);
return $column_names_and_types; // array  
}    // End function 


// Function that gets the primary key from the database 
function get_pk($table_name) {
    $x = DB_NAME;
    $database = new Database();
    $sql = NULL;
    $sql .= "SELECT COLUMN_NAME FROM information_schema . COLUMNS ";
    $sql .= "WHERE (TABLE_SCHEMA = '$x') AND (TABLE_NAME = '$table_name') AND (COLUMN_KEY = 'PRI')";
    $database->query($sql); 
    $database->execute();
    $pk = $database->single(); 
    $id_field = $pk['COLUMN_NAME']; 
    return $id_field;
}


// Update row in DB
function update($table_name, $id_field, $vars_get) {
  $database = new Database();
	$del_id = $vars_get[$id_field];

	// Need to automate this like (add row)
	unset($vars_get['f']); 
	unset($vars_get[$id_field]); 

	$list_of_keys = array_keys($vars_get);
	$update_fields = NULL; 
  $x = NULL;  		
		foreach($list_of_keys as $f) {
			$x .= $f . " =:" . $f . ", "; 	
		}
	$x = rtrim($x, $charlist = ', ');
	$update_fields = $x; 

	$sql = NULL; 
		$sql .= "UPDATE " . $table_name . " SET "; 
		$sql .= $update_fields; 
		$sql .= " WHERE " . $id_field . " = " . $del_id; 
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
function mod_pull_row($table_name, $id_field, $id){
	$database = new Database();
	$sql = NULL; 
	$sql .= "SELECT * FROM "; 
	$sql .= $table_name; 
	$sql .= " WHERE " . $id_field . " = "; 
	$sql_bind = ":" . $id_field;
	$sql .= $sql_bind; 
	$database->query($sql);
  $database->bind($sql_bind,$id);
  $database->execute();
  $row = $database->single(); 	
	add_form($table_name, $row);	
} // END Function 


// Adds record to the DB
function add_row($table_name, $table_fields ,$vars_get) {
 
	// Clean the get vars with a white list so db and all are happy 
	$white_list = $table_fields; 
	$list_of_keys = array_keys($vars_get);
	$diff = array_diff($list_of_keys,$white_list);    // Move to OBJ or function  
		foreach ($diff as $d) {		
			unset($vars_get[$d]); 	
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
	$sql = "INSERT INTO "  . $table_name . " ". $insert_fields . " VALUES " . $bind_fields; 
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
function del_row($table_name,$id, $id_field) {
	$_GET['f'] = null; 
	$database = new Database();	
	$sql = NULL;
	$sql .= "DELETE FROM ";
	$sql .= $table_name; 
	$sql .= " WHERE " . $id_field . " = " . $id; 		
	$database->query($sql); 	  
	  //$database->query('DELETE FROM animals WHERE animal_id = :animal_id');
	  //$database->bind(':animal_id',$id_field); 
	$database->execute();
	move_along(); // move to database class? 
} // END Function 


// output form 
function add_form($table_name, $row) {
    // do some work for both forms 
    $pk = get_pk($table_name);
    $column_names_and_types = get_sql_columns_with_size($table_name); // hash 
    
    
    if(empty($row)) {
       echo "empty  row <BR>";
       echo "table name " . $table_name;    
    //   var_dump($column_names_and_types); 
       $html = build_form($column_names_and_types,$row);
       echo $html;
       exit; 
    } 
    // if we have row data     
    
    $html = build_form($column_names_and_types, $row);  
       echo $html;
       exit; 
}

// Just show the data in a table sorted
function show($table_name, $sort_field, $id_field) {
  $database = new Database(); 
  $ht       = new HtmlHelper();
	 	// Build the sql piece by piece to put in $vars    
		$sql = null; 
		$sql .= "SELECT * FROM ";
		$sql .= $table_name;
		$sql .= " ORDER BY "; 
		$sql .= $sort_field; 
	$database->query($sql);  
  $rows = $database->resultset(); 
  /*** send to the linker 
    param 1 - data 
    param 2 - unique db row
  ***/
  $rows = $ht->linker($rows, $id_field); // Builds link on id field
  echo  $ht->array2table($rows); // Prints out a table 
	add_button();
exit; 	
} // end function 


// build form and populate as needed 
function build_form($column_names_and_types, $row) {
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
                
                $p = 'foo';         
        
                var_dump($column_names_and_types); 
                var_dump($row);        
                
	}	
        $l = ''; 
        foreach ($column_names_and_types as $outer_key => $array) {
            foreach($array as $k => $v) { 
                //echo $outer_key . "<BR>"; 
                if($k === 0) {
                    if($v === 'animal_id') {
                        echo "Value is -> $v is and KEY is $k <BR><BR>"; 
 
                    }
                    
                    //echo "Build Form Value Stuff:" .  $k . "   " .  $v .  "<BR>";
                    $l .= "Lable: $v  <INPUT TYPE='text' NAME='$v' VALUE=''" ; 
                }
                if($k === 1) {
                    //echo "Build type Stuff:" . $k . "   " . $v . "<BR>";
                    $l .= "SIZE='20' MAXLENGHT='20'><BR>";
                }
                
            } 
            
        }
        echo $l; 
        
        
        
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







