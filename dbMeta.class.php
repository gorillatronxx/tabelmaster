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
    
      public function buildFormArray($array) {
        $v = NULL; 
    
        var_dump($array);
        
/*  array comes in keyed by col name has: 
 * 
 * Field  - this is the name of the form field
 * Type  - this is the DB data type  
 * Null  - NULL or not YES / NO
 * Key   - PRI (look for the primary here)  
 * Default - null / current time stamp
 * Extra   - auto_increment    
 *  
 * 
 * Need to produce an array like this : key is the col name as above 
 * 
 * FORM_LABEL - so user can see 
 * FORM_SIZE - se
 * FORM_MAXLENGTH - se
 * FORM_TYPE - hidden, text, time, text_area, bool
 * FORM_VALUE - to be filled in later? 
 * 
 */    
        foreach($array as $k => $v) { 
            
            // Run each  through some private functions 
            $array[$k]['FORM_LABEL']     = $this->make_form_label($array[$k]['Field']);            
            $array[$k]['FORM_SIZE']      = $this->make_form_size($array[$k]['Type']); 
            $array[$k]['FORM_MAXLENGTH'] = $this->make_form_size($array[$k]['Type']); 
            $array[$k]['FORM_TYPE']      = $this->make_form_type($array[$k]['Type']);
            
            // remove junk 
           $unset_array = ['Key','Default','Null','Field','Extra','Type']; 
           foreach ($unset_array as $x) {
               unset($array[$k][$x]); // unset, junk clean up    
           }
           ksort($array[$k]); // sort for fun 
        }
        
         var_dump($array); 
        
    return $array;     
    }

    private function make_form_type($str){
        if($str === 'PRI') {            
            return 'hidden';
        } else {
            return 'text'; 
        }
        
    }
    
    private function make_form_label($str) {
        $var = strtr($str,'_',' '); 
        $label = ucwords($var);         
    return $label;    
    }
    
    private function make_form_size($str) {
        $size = filter_var($str, FILTER_SANITIZE_NUMBER_INT); // only ints left
    return $size;
    }
    
    
    // Get all the columns in an array with info from DB SHOW 
    public function get_tabel_columns() {  
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
        $array = $this->get_tabel_columns();
        foreach($array as $x) {
            if($x['Key'] === 'PRI'){
                $str = $x['Field']; // from db output 
            } 
        }
    return $str; 
    }

    // Removes the pk (takes data array and pk string
    public function zap_pk_id() {
        $array = $this->get_tabel_columns();
        $pk = $this->get_pk(); 
        unset($array[$pk]);
    return $array;     
    }

    // Take the raw mysql column data, make a nice keyed array by col name
    private function nice_array($array) {
        $resultArr = array();
        foreach ($array as $value) {
            $resultArr[$value['Field']] = $value; // Field is db 
        }
        unset($array); // or $mainArr = $resultArr;
    return $resultArr; 
    }
    
    // Get the names only in array
    public function get_col_names() {
        $array = $this->get_tabel_columns(); 
        foreach($array as $k => $array) {
            $names[] = $k;
        }
    return $names;     
    }
    
    // Get the names only in array w/o pk
    public function get_col_names_no_pk() {
        $array = $this->get_tabel_columns();
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
