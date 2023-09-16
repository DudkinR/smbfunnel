<?php
if(!class_exists('CFQuestion_controller')) {
    class CFQuestion_controller {
        function __construct() { }
        function load($library) {
            $file=plugin_dir_path(__FILE__);
            $cls='CFQuestion_'.$library;
            $file .=$cls.'.php';
            require_once($file);
            $ob=new $cls(array('loader'=>$this));
            return $ob;
        }
    }
}
?>