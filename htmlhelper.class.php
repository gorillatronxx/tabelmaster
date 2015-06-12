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

    // builds the form insert items in the form 
    function BuildFormInsert($v) {
    
        //var_dump($v); 
        
        $value = isset($v['VALUE']) ? $v['VALUE'] : '';        // value set or not

        // figure out type for form 
        $type = ($v['TYPE'] === "HIDDEN") ? 'hidden' : 'visable'; 
        
        // make hidden or text type 
        if ($type === 'hidden') {
           $i = "<INPUT TYPE='$type' NAME='$v[NAME]' VALUE='$value'>\n";            
            
        } else {
            $i = "<P>\n";
            $i .= "<label for='$v[NAME]'>" . $v['LABEL'] ."</label><BR>\n";
            $i .= "<INPUT TYPE='$v[TYPE]'NAME='$v[NAME]' VALUE='$value' SIZE='$v[SIZE]' MAXLENGTH='$v[SIZE]' required>\n";
            $i .= "</P>\n"; 
        }
        // Need to add textarea type
        return $i;  
    }
    
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
