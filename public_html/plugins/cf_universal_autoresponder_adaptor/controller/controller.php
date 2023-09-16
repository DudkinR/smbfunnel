<?php
if(!class_exists('CF_Global_AutoResponder_controller'))
{
    class CF_Global_AutoResponder_controller 
    {
        function __construct() { }

        function load($library) 
        {
            $file=plugin_dir_path(__FILE__);
            $cls='CFGlobalAR_'.$library;
            $file .=$cls.'.php';
            require_once($file);
            $ob=new $cls(array('loader'=>$this));
            return $ob;
        }
    }
}
?>