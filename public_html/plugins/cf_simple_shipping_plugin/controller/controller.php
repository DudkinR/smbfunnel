<?php
if(!class_exists('CF_Simple_Shipping_controller')) {
    class CF_Simple_Shipping_controller {
        function __construct() { }
        function load($library) {
            $file=plugin_dir_path(__FILE__);
            $cls='CF_Simple_Shipping_'.$library;
            $file .=$cls.'.php';
            require_once($file);
            $ob=new $cls(array('loader'=>$this));
            return $ob;
        }
    }
}
?>