<?php
ob_start();
include 'config.class.php';  // db stuff
include 'database.class.php'; // Include database class
include 'htmlhelper.class.php'; // html routines 

include 'dbMeta.class.php'; 
$meta = new dbMeta(); 
$c = $meta->get_raw_columns();
var_dump($c); 



$sort_field = SORT_FIELD; // From the config: to sort the thml table 

$columns_names_and_types = NULL; 
$row = NULL;
$del_id = NULL; 

//Need new function that gets the sql_column data better 
//$columns = get_sql_columns_with_size($table_name); // Gets all the column names in array

$pk = get_pk(); // gets the primary key as str, from db query
$raw_columns = get_raw_columns(); 
$nice_array = nice_array($raw_columns); // transforms data in to new array
$nice_array = zap_pk_id($nice_array,$pk); // Removes the pk if needed
$pk2 = get_pk2($raw_columns); // get the pk in a new way

echo "reslts<BR>"; 
    //var_dump($raw_columns); 
echo "nice <BR>"; 
var_dump($nice_array); 
var_dump($pk);  // Using some crazy sql. 
var_dump($pk2); // Easier way to do it

//////////////////

// get length 
$data = array_pop($nice_array); 
echo 'Data:' .  $data['Type'] . "<BR>"; 
$foo = $data['Type']; 
$foo = substr($foo, -3, 2);
var_dump($foo); 


// See if we can get the pk out of the array
function get_pk2($array) {
    foreach($array as $x) {
        if($x['Key'] === 'PRI'){
            $str = $x['Field']; // from db output 
        } 
    }
return $str; 
}

// Removes the pk (takes data array and pk string
function zap_pk_id($array,$pk) {
    unset($array[$pk]);
return $array;     
}

// Take the raw mysql column data, make a nice keyed array by col name
function nice_array($array) {
    $resultArr = array();
    foreach ($array as $value) {
        $resultArr[$value['Field']] = $value; // Field is db 
    }
    unset($array); // or $mainArr = $resultArr;
return $resultArr; 
}

function get_raw_columns() { 
    $database = new Database();
    $sql = NULL;
    $sql .= 'SHOW COLUMNS FROM ' . TABLE_NAME;
    $database->query($sql);
    $database->execute();
    $data = $database->resultset();
return $data;
}

// Function that gets the primary key from the database 
function get_pk() {
    $x = DB_NAME;
    $table = TABLE_NAME;
    $database = new Database();
    $sql = NULL;
    $sql .= "SELECT COLUMN_NAME FROM information_schema . COLUMNS ";
    $sql .= "WHERE (TABLE_SCHEMA = '$x') AND (TABLE_NAME = '$table') AND (COLUMN_KEY = 'PRI')";
    $database->query($sql); 
    $database->execute();
    $pk = $database->single(); 
    $id_field = $pk['COLUMN_NAME']; 
    return $id_field;
}