<?php
if(!class_exists('CFMenu_controller')) {
    class CFMenu_controller {
        function __construct() { }
        function load($library) {
            $file=plugin_dir_path(__FILE__);
            $cls='CFMenu_'.$library;
            $file .=$cls.'.php';
            require_once($file);
            $ob=new $cls(array('loader'=>$this));
            return $ob;
        }
    }
}
?>