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
            $array[$k]['SIZE'] = $size; // push SIZE
            $array[$k]['NAME'] = $array[$k]['Field']; // push NAME
            $array[$k]['MAXLENGTH'] = $size;  // push MAXLENGTH
            $lable = strtr($array[$k]['Field'], '_',' '); // translate _ to ' ' 
            $array[$k]['LABLE'] = ucwords($lable); // capitalize words, push LABLE
            
            // placeholder & autofocus attributes
            // also make id for css (later) 
            
            // Work on this later for various data types
            if($array[$k]['Key'] === 'PRI') { 
                $array[$k]['TYPE'] = 'HIDDEN';
            } else {
                $array[$k]['TYPE'] = 'TEXT'; 
            } // and add CHAR, TEXT, TINYTEXT, MEDIUMTEXT, LONGTEXT (searhc TEXT)  

           $unset_array = ['Key','Default','Null','Field','Extra','Type']; 
           foreach ($unset_array as $x) {
               unset($array[$k][$x]); // unset, junk clean up    
           }
           ksort($array[$k]); // sort for fun 
        }
    return $array;     
    }
      
    // Get all the columns in an array with info from DB SHOW 
    public function get_db_columns() {  
        $database = new Database();
        //$sql = NULL; 
        $sql = 'SHOW COLUMNS FROM ' . TABLE_NAME;
        $database->query($sql);
        $database->execute();
        $data = $database->resultset();
        $array = $this->nice_array($data); // Kets the array with Field name
    return $array;
    }
    
    // Get the primary key for table         
    public function get_pk() {
        $array = $this->get_db_columns();
        foreach($array as $x) {
            if($x['Key'] === 'PRI'){
                $str = $x['Field']; // from db output 
            } 
        }
    return $str; 
    }

    // Removes the pk (takes data array and pk string
    public function zap_pk_id() {
        $array = $this->get_db_columns();
        $pk = $this->get_pk(); 
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
    public function get_col_names() {
        $array = $this->get_db_columns(); 
        foreach($array as $k => $array) {
            $names[] = $k;
        }
    return $names;     
    }
    
    // Get the names only in array w/o pk
    public function get_col_names_no_pk() {
        $array = $this->get_db_columns();
        $pk = $this->get_pk(); 
        foreach($array as $v => $array) {
            if($v === $pk) {              // remove value = pk
                unset($v); 
            } else {
                $names[] = $v;
            }   
        }
    return $names;     
    }
    
    
    

}// End class 
