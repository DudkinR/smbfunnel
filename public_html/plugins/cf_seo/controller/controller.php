<?php
if( !class_exists( "CFSEO_base_controller" ) )
{
    class CFSEO_base_controller
    {
        function __construct()
        {

        }
        function load( $library )
        {
            $file = plugin_dir_path(__FILE__);
            $cls  ='CFSEO_'.$library;
            $file .=$cls.'.php';
            require_once($file);
            $ob = new $cls(array('loader'=>$this));
            return $ob;
        }
    }
}