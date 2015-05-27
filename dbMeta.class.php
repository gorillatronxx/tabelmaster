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
    
    /*** This takes in some data and formats it all nice to build forms.
      Makes array like this keyed with NAME
      'SIZE' => string '25' (length=2)
      'NAME' => string 'animal_type' (length=11)
      'VLAUE' => string '' (length=0)
      'MAXLENGTH' => string '25' (length=2)
      'TYPE' => string 'TEXT' (length=4)
    ***/ 
      function buildFormArray($array) {
        $v = NULL; 
        foreach($array as $k => $v) {         
            $size = \substr($array[$k]['Type'], -3, 2); // pull numbers size
            $array[$k]['SIZE'] = $size; 
            $array[$k]['NAME'] = $array[$k]['Field']; 
            $array[$k]['VLAUE'] = '';
            $array[$k]['MAXLENGTH'] = $size; 
            $lable = str_replace(, '_', ' ');
            $array[$k]['LABLE'] = $lable;
            
            // make lable also correct case
            // placeholder & autofocus attributes
            
            if($array[$k]['Key'] === 'PRI') { 
                $array[$k]['TYPE'] = 'HIDDEN';
            } else {
                $array[$k]['TYPE'] = 'TEXT'; 
            } // and add CHAR, TEXT, TINYTEXT, MEDIUMTEXT, LONGTEXT (searhc TEXT)  

           $unset_array = ['Key','Default','Null','Field','Extra','Type']; 
           foreach ($unset_array as $x) {
               unset($array[$k][$x]);   
           }
           ksort($array[$k]); // sort for fun 
        }
    return $array;     
    }
    
   
    
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
