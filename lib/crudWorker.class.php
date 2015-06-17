<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of sqlWorker  - this is a CRUD class that does the sql work for 
 * Create Read Update & Delete of table row data. 
 *
 * @author jms04747
 */

class crudWorker {

    // Need consructor $s , $id ect.. (work on this later)  
    //
    
    // Just read the data in a tabel sorted by $s 
    public function read($s) {
        $database = new Database(); 
        $ht       = new HtmlHelper();
        $meta     = new dbMeta();
        $sort_field = $s;    
        $cols = $meta->get_col_names(); //array of col names 
        
        // build a string that is used to pull a limited # of chars off each col
        foreach ($cols as $col) {
            $array[] = "SUBSTRING($col, 1," . LENGTH_TABEL_COL . ") AS $col"; // AS $col makes the return clean 
        }
        $cols_limited = implode(", ",$array); // smash it all together    
        
            // var_dump($cols_limited); 
        
        // Build the sql piece by piece to put in $vars     
        $sql = "SELECT $cols_limited FROM " . TABLE_NAME . " ORDER BY " . $sort_field; 
        $database->query($sql);  
        $result = $database->resultset(); 
        
        /*** send to the linker  param 1 - data  --- param 2 - unique db row  ***/
        $rows = $ht->linker($result, PRIMARY_KEY); // Builds link on id field  
        $table = $ht->array2table($rows); 
        return $table; 
    } // end function 
    
    
    // Deletes a row from the database 
     public function delete_row($id) { 
        $database = new Database();	
        $sql = NULL;
        $sql .= "DELETE FROM " . TABLE_NAME . " WHERE " . PRIMARY_KEY . " = " . $id; 		
        $database->query($sql); 	  
        $database->execute();
        $this->move_along(); // move to database class? 
    } // END Function 
   
    
  // Creates a record to the DB
    public function create_row($vars_get) {
        $tm = new dbMeta();  // manipulate the DB Show data
        $table_fields = $tm->get_col_names_no_pk();    // get col names for db insert
                
        // Clean the get vars with a white list so db and all are happy 
        $white_list   = $table_fields;          // 
        $list_of_keys = array_keys($vars_get);  // From the get 
                
        $diff = array_diff($list_of_keys,$white_list);    // compare arrays for insert  
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
    public function update_row($vars_get) {
        $database = new Database();
        $del_id = $vars_get[PRIMARY_KEY];

        // Need to automate this like (create row)
        unset($vars_get['action']);     // removes the action code 
        unset($vars_get[PRIMARY_KEY]);  // removes the pk 

        $array_keys = array_keys($vars_get);
        $x = NULL;  		
        foreach($array_keys as $f) {
            $x .= $f . " =:" . $f . ", "; 	
        }
        $update_fields = rtrim($x, $charlist = ', ');
        
        $sql = "UPDATE " . TABLE_NAME . " SET " . $update_fields; 
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
    public function get_update_row($id){
        $database = new Database();
        $sql = "SELECT * FROM " . TABLE_NAME . " WHERE " . PRIMARY_KEY . " = "; 
        $sql_bind = ":" . PRIMARY_KEY;
        $sql .= $sql_bind; 
        $database->query($sql);
        $database->bind($sql_bind,$id);
        $database->execute();
        $row = $database->single(); 	
        $this->mod_prep($row); // send the row to get preped
    } // END Function 


// Preps the mod row data, puts it into form array for auto gen update form 

    private function mod_prep($row){
        $meta   = new dbMeta(); 
        $cols = $meta->get_tabel_columns(); // array w/ all metadata 
        $form_array_mod =  $meta->buildFormArray($cols); // make to nice form ready
        // insert the values to the form array for mod form
        foreach($row as $k => $v) {
            $form_array_mod[$k]['VALUE'] = $v;
           }    
           
    update_form($form_array_mod);  // returns array ready to be processed 
    }
    
    
    // put in the helper 
    public function move_along() {
        $fv = new filterVars;
        $phpSelf = $fv->phpSelf();
        header("location:$phpSelf") ;		
    }    
   
} // End class
