<?php

function document_path() 
{    
    $arr_path = explode("/", request()->path());
    return $arr_path[0];
}