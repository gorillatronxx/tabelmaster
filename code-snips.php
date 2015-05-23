/*** This prints a name list w/out knowing names ****/
foreach($rows as $row) {
	echo " ---- <BR>"; 
	foreach($row as $key => $value) {
		echo "$key - $value <BR>"; 
	}

}	


// this works easy but we have to know names 
foreach($rows as $row) {
	echo $row['animal_id'] . ' - ';
	echo $row['animal_type']. ' - ';
	echo $row['animal_name'] . '<br>';
}