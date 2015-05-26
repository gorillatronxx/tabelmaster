<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of sqlWorker
 *
 * @author jms04747
 */

class sqlWorker {


    // Just show the data in a table sorted
    function show() {
        $database = new Database(); 
        $ht       = new HtmlHelper();
        // Build the sql piece by piece to put in $vars     
        $sql = "SELECT * FROM " . TABLE_NAME . " ORDER BY " . SORT_FIELD; 
        $database->query($sql);  
        $result = $database->resultset(); 
        /*** send to the linker  param 1 - data  --- param 2 - unique db row  ***/
        $rows = $ht->linker($result, PRIMARY_KEY); // Builds link on id field
        echo  $ht->array2table($rows); // Prints out a table 
        $this->add_button(); // adds a stupid button
        exit; 
    } // end function 
    
    function del_row($id) {
        $_GET['f'] = null; 
        $database = new Database();	
        $sql = NULL;
        $sql .= "DELETE FROM " . TABLE_NAME . " WHERE " . PRIMARY_KEY . " = " . $id; 		
        $database->query($sql); 	  
        $database->execute();
        $this->move_along(); // move to database class? 
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
        $insert_fields = implode(", " ,$table_fields);
        $insert_fields = "(" . $insert_fields . ")"; 

        // build bind fields 
        $array = NULL;     
        foreach($table_fields as $b) {
            $array .= ":". $b . ",";
        }

        $bind_fields = rtrim($array, $charlist = ',');
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
        $this->move_along();
    } // END Function   
    
    
    // Update row in DB
    function update($vars_get) {
        $database = new Database();
        $del_id = $vars_get[PRIMARY_KEY];

        // Need to automate this like (add row)
        unset($vars_get['f']); 
        unset($vars_get[PRIMARY_KEY]); 

        $list_of_keys = array_keys($vars_get);
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
        $this->move_along();
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
    
    // put in the helper 
    function move_along() {
        $phpSelf = filter_input(INPUT_SERVER, 'PHP_SELF', FILTER_SANITIZE_URL);
        header("location:$phpSelf") ;	
	exit; 	
    }

    function add_button() {
        $phpSelf = filter_input(INPUT_SERVER, 'PHP_SELF', FILTER_SANITIZE_URL);
	echo "<BR><A HREF='$phpSelf?f=af'>ADD</A>"; 
    }
    
   
} // End class
