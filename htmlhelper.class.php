<?php
class htmlhelper {

  public function array2table($array, $table = true)
  {
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

    if ($table) {
        return '<table>' . $tableHeader . $out . '</table>';
    } else {
        return $out;
    }
  } // end function 


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
  






} // end of class 
?>