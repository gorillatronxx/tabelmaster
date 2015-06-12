<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of filterVars
 *
 * @author jms04747
 */
class filterVars {

    public function SafeGet() {
        $safe_get = '';
        foreach($_GET as $k => $v) {
            $x = filter_input(INPUT_GET, $k, FILTER_SANITIZE_STRING);
            $safe_get[$k] = $x; // array of safe get values      
        }
    return $safe_get; 
    }
    
    
    public function phpSelf() {
        $phpSelf = filter_input(INPUT_SERVER, 'PHP_SELF', FILTER_SANITIZE_URL);
    return $phpSelf; 
    }
}
