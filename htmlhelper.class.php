<?php

class htmlhelper {
    
    
  // Builds a table from rows of data    
  public function array2table($array, $table = true) {
    $out = '';
    foreach ($array as $key => $value) {
        if (is_array($value)) {

            /** if (!isset($tableHeader)) {        
                $tableHeader =
                    '<tr><th>' .
                    implode('</th><th>', array_keys($value)) .
                    '</th></tr>';
            } **/                  
            array_keys($value);

            
            
            $out .= "\n<tr>";
            $out .= $this->array2table($value, false);
            $out .= "</tr>\n";
            } else {
                
               // $value = $value . "xxx"; // right here
                
                $out .= "<td>$value</td>";
            }
        }
        // build it up 
        if ($table) {    
            $sort_buttons = $this->BuildSortButtons(); //         
            //$tableHeader = strtr($tableHeader,'_',' '); // remove unerscores
            //$x = strtoupper($tableHeader);              // Make all caps         
        return "\n<table>\n" . $sort_buttons . $out . "\n</table>\n";
        } else {
        return $out;
        }       
    } // end function 
  
  // builds (update & delte) links to the table 
    public function linker($rows, $id_field) {
        $fv = new filterVars;
        $phpSelf = $fv->phpSelf();    
        $row = '';  
        $c = count($rows); // get # of rows
        $c--;   // minus one to fix offset 
        foreach($rows as $row) {          
            // iterate throug and add a modify button 
	    for($x = 0; $x <= $c; $x++) {	    
	  	$column_u_id = $rows[$x][$id_field]; // use get u_id out of array   
                $rows[$x]['mod'] = "<A HREF='$phpSelf?action=uf&" . $id_field . "=$column_u_id'><button title='Update'> U </button></A>"; // build mod link 
		$rows[$x]['del'] = "<A HREF='$phpSelf?action=d&" . $id_field . "=$column_u_id'><button title='Delete'> D </button></A>"; // build del link	
	    }	
        }
    return $rows;    
    } // end of function 
  
  // HTML Stuff 
    
    // Builds the start of a form 
     function BuildStartForm($legend) {
       $fv = new filterVars;
       $phpSelf = $fv->phpSelf();  
       echo "<fieldset>";
       echo "<legend>$legend</legend>";
       echo "<form action='$phpSelf?' method='get' enctype='text/plain' target='_parent'>\n";     
    }

    // builds the form insert items in the form @rray
    // this can come from the create or update form 
    public function BuildFormInsert($array) {
        $out = ''; 
        // make the get safe     
        $fv = new filterVars(); 
        $safe_get = $fv->SafeGet(); 
        $action = $safe_get['action']; // set action 
        
        // If we have the create form 
        if ($action === 'crf') {
            $meta = new dbMeta(); 
            $pk = $meta->get_pk();
            unset($array[$pk]);  // remove pk field 
        }
        
        // Loop this through a function that build each form type 
        foreach ($array as $k => $v_array) {        
            $out .= $this->FormItems($v_array, $action); // send data and action code 
        }
        return $out; 
    } // END FUNCTION
 
    
    // This makes each form type item based on an array of values
    private function FormItems($x, $action) {
        $out = ''; 
        $type  = $x['FORM_TYPE']; // set all the values to someting sane 
        $name  = $x['FORM_NAME']; 
        $hide  = $x['FORM_HIDE'];
        $label = $x['FORM_LABEL'];
        $size  = $x['FORM_SIZE']; 
        $max   = $x['FORM_MAXLENGTH']; 
        $value = isset($x['VALUE']) ? $x['VALUE'] : ''; 
        $now = date('Y-m-d H:i:s'); 
       
        
        if ($hide === 'hidden') {
            $type = 'hidden'; 
        }
        
        if ($type === 'hidden') {
            $out .= "<input type='$type' name='$name' value='$value'>"; 
        }
        
        if ($type === 'text') {
            $out .= "<p>\n"; 
            $out .=  "<label for='$label'> $label </label><br>\n "; 
            $out .= "<input type='$type' name='$name' value='$value' size='$size' maxlength='$max' required>\n";
            $out .= "</p>\n"; 
        }
        
        // datetime row created 
        if ($type === 'datetime') {
            
            if($action === 'crf') {
                $value = $now; 
            }    
            if($action === 'uf') {
                $value = $value; 
            }
            $out .= "<input type='hidden' name='$name' value='$value'>\n";
        }
        
        // datetime row updated 
        if ($type === 'timestamp') {
            
            if($action === 'crf') {
                $value = '';
            }
            if($action === 'uf'){
                $value = $now; 
            }
            $out .= "<input type='hidden' name='$name' value='$value'>\n";
        }
        
        if ($type === 'textarea') {
            $out .= "<p>\n";
            $out .= "<label for='$label'> $label </label><br>\n"; 
            $out .= "<textarea rows='4' cols='50' name='$name'>$value</textarea>"; // set the rows and col in a global config (work on wrap) 
            $out .= "</p>\n"; 
        }
        
        
        // ADD SUPPORT FOR DATE (expires)
        //
        // ACTIVE / INACTIVE BOOL? 
        //
        // ADD SUPPORT FOR TEXT  / TEXTAREA   
        
        
        //var_dump($x); 
    return $out;     
    } // END FUNCTION 
    
    
    
    // Builds the end of a form 
    public function BuildEndForm($action) {
        echo "<INPUT TYPE='hidden' NAME='action' VALUE='$action'>"; // this is coded in 
        echo "<p class='submit'> <input type='submit' value='Submit' /></p>"; 
        echo "</fieldset>"; 
        echo "</FORM>";  
    } 

    // Make Create Row button used on the read tabel page 
    private function createButton() {
        $fv = new filterVars;
        $phpSelf = $fv->phpSelf();
	$addButton = "<A HREF='$phpSelf?action=crf'><button title='Create Row'>Create Row</button></A>"; 
        return $addButton; 
    }
   
    public function BuildSortButtons() {
        $md = new dbMeta();
        $fv = new filterVars(); 
        $array_names = $md->get_col_names();
        $phpSelf = $fv->phpSelf();
     
        $l = '<tr>';
        foreach ($array_names as $name) {
            $label = $this->labelMaker($name);
            $l .= "<th><a href='$phpSelf?s=$name'><button title='Sort by $label'>$label</button></A></th>\n  "; 
        }
        $l .= '<th colspan=2>';    
        $l .= $this->createButton(); 
        $l .= '</th></tr>'; 
        return $l; 
    }

    // makes table col name into lable text
    public function labelMaker($str) {
        $str_out = ucwords(\strtr($str,'_',' '));        
        return $str_out; 
    }
    
    // Builds the start of a HTML page
    public function startHTML() {
        $css = CSS; 
        // if we have a .css in the config.class 
        if(isset($css)) {
            $link = "  <link rel='stylesheet' href='" . CSS . "'>\n"; 
        }
        
        echo "<!DOCTYPE html>\n" 
        . "<html>\n" 
        . "<head>\n";
        echo $link; 
        echo "<meta charset='UTF-8'>\n"
        . "<title>tablemaster</title>\n"
        . "</head>\n"
        . "<body>\n";         
                    
    } 
    
    // Builds the end of an HTML page 
    public function endHTML() {
        echo "\n</body>\n"
        . "</html>"; 
    }
    
    
    
} // end of class 
