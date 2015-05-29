<?php

class htmlhelper {

  // Builds a table from rows of data   
  public function array2table($array, $table = true) {
    $out = '';
    foreach ($array as $key => $value) {
        if (is_array($value)) {
            if (!isset($tableHeader)) {
                $tableHeader =
                    '<th>' .
                    implode('</th><th>', array_keys($value)) .
                    '</th>';
            }
            array_keys($value);
            $out .= '<tr>';
            $out .= $this->array2table($value, false);
            $out .= '</tr>';
        } else {
            $out .= "<td>$value</td>";
        }
    }
    // build it up 
    if ($table) {
        $tableHeader = strtr($tableHeader,'_',' '); // remove unerscores
        $x = strtoupper($tableHeader);              // Make all caps 
        return '<table>' . $x . $out . '</table>';
    } else {
        return $out;
    }
  } // end function 

  // builds (mod & del) links to the table 
  public function linker($rows, $id_field) {
    $row = '';  
    $c = count($rows); // get # of rows
    $c--; // minus one to fix offset 
      foreach($rows as $row) {          
          
        // iterate throug and add a modify button 
	    for($x = 0; $x <= $c; $x++) {	    
	  	  $column_u_id = $rows[$x][$id_field]; // use get u_id out of array   
  		  $rows[$x]['mod'] = "<A HREF='$_SERVER[PHP_SELF]?f=mod&" . $id_field . "=$column_u_id'>Mod</A>"; // build mod link 

		  $rows[$x]['del'] = "<A HREF='$_SERVER[PHP_SELF]?f=del&" . $id_field . "=$column_u_id'>Del</A>"; // build del link	
	    }	
    }
    return $rows; 
  } // end of function 
  
  // Put more html classes here
     function BuildStartForm() {
       $phpSelf = filter_input(INPUT_SERVER, 'PHP_SELF', FILTER_SANITIZE_URL);
       echo "<form action='$phpSelf?' method='get' enctype='text/plain' target='_parent'>";     
    }
 
    function BuildFormInsert($v) {
        $value = isset($v['VALUE']) ? $v['VALUE'] : '';        // value set or not
        $lable = ($v['TYPE'] === "HIDDEN") ? '' : $v['LABLE']; // hide HIDDEN type lable  
        // generate 
        $i = "<lable> $lable "
                . "<INPUT TYPE='$v[TYPE]'NAME='$v[NAME]' VALUE='$value' SIZE='$v[SIZE]' MAXLENGTH='$v[SIZE]'>"
                . "</lable><BR>"; 
    return $i;  
    }
    
    function BuildEndForm($f) {
        echo "<INPUT TYPE='hidden' NAME='f' VALUE='$f'>"; // this is coded in 
        echo "<BUTTON>Submit</BUTTON>"; 
        echo "</FORM>";  
    } 
  
 
} // end of class 
