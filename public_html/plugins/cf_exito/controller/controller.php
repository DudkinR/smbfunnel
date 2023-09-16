<?php
if(!class_exists('CFExito_controller'))
{
    class CFExito_controller
    {
        function __construct()
        {

        }
        function load($library)
        {
            $file=plugin_dir_path(__FILE__);
            $cls='CFExito_'.$library;
            $file .=$cls.'.php';
            require_once($file);
            $ob=new $cls(array('loader'=>$this));
            return $ob;
        }
    }
}
?>