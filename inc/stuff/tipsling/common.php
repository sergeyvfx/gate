<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

function gate_array_column($array, $column_key, $index_key = null){
    $result=array();
    
    if ($index_key == null) {
        foreach($array as $value){
            $result[] = $value[$column_key];
        }
    }
    else {
        foreach($array as $value){
            $result[$value[$index_key]] = $value[$column_key];
        }
    } 
    return $result;
}
?>
