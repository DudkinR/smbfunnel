<?php
if(!class_exists('CFrecaptcha_controller')) {
    class CFrecaptcha_controller {
        function __construct() {            
         }
        function load($library) {
            
            $file=plugin_dir_path(__FILE__);
            $cls='CFrecaptcha_'.$library;
            $file .=$cls.'.php';            
            require($file);
            $ob=new $cls(array('loader'=>$this));            
            return $ob;
        }
    }
}
?>
