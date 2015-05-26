<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of dbMeta
 *
 * @author jms04747
 */
class dbMeta {

    // Get all the columns in an array with info use SHOW 
    public function get_db_columns() {  
        $database = new Database();
        $sql = NULL; 
        $sql .= 'SHOW COLUMNS FROM ' . TABLE_NAME;
        $database->query($sql);
        $database->execute();
        $data = $database->resultset();
        $na = $this->nice_array($data); // Kets the array with Field name
    return $na;
    }
    
    // Get the primary key for table         
    public function get_pk($array) {
        foreach($array as $x) {
            if($x['Key'] === 'PRI'){
                $str = $x['Field']; // from db output 
            } 
        }
    return $str; 
    }

    // Removes the pk (takes data array and pk string
    public function zap_pk_id($array,$pk) {
        unset($array[$pk]);
    return $array;     
    }

    // Take the raw mysql column data, make a nice keyed array by col name
    public function nice_array($array) {
        $resultArr = array();
        foreach ($array as $value) {
            $resultArr[$value['Field']] = $value; // Field is db 
        }
        unset($array); // or $mainArr = $resultArr;
    return $resultArr; 
    }
    // Get the names only in array
    public function get_col_names($array) {
        foreach($array as $k => $array) {
            $names[] = $k;
        }
    return $names;     
    }
    

}// End class 
