<?php 
function formatNumber($number) {

    $cleared_number = str_replace(array('.', ','), ['', '.'], $number); 

    
    $formatted = (float)$cleared_number; 
    return $formatted;
    
}

function formatTR($number) {
    $formatted = number_format((float)$number, 2, ',', '.');        
    return $formatted;

}

?>