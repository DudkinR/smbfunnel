<?php
if(!class_exists('CFSOCIAL_controller')) {
    class CFSOCIAL_controller {
        function __construct() { }
        function load($library) {
            $file=plugin_dir_path(__FILE__);
            $cls='CF_Social_Share_'.$library;
            $file .=$cls.'.php';
            require_once($file);
            $ob=new $cls(array('loader'=>$this));
            return $ob;
        }
    }
}
?>