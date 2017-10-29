<?php

if(!function_exists('ClassLoader')){
    
    function ClassLoader($classname){
        include dirname(__FILE__).'/'.$classname.'.class';
    }
    spl_autoload_register('ClassLoader');
}

?>