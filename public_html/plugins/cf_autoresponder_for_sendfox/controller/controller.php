<?php
if(!class_exists('sendfox_controller'))
{
    class sendfox_controller
    {
        function __construct()
        {

        }
        function load($library)
        {
            $file=plugin_dir_path(__FILE__);
            $cls='CFSendfox_'.$library;
            $file .=$cls.'.php';
            require_once($file);
            $ob=new $cls(array('loader'=>$this));
            return $ob;
        }
    }
}
?>