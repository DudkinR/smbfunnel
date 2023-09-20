<?php
if(!class_exists('CFScarcityJeet_controller'))
{
    class CFScarcityJeet_controller
    {
        function __construct()
        {

        }
        function load($library)
        {
            $file=plugin_dir_path(__FILE__);
            $cls='CFScarcityJeet_'.$library;
            $file .=$cls.'.php';
            require_once($file);
            $ob=new $cls(array('loader'=>$this));
            return $ob;
        }
    }
}
?>