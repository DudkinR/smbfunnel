<?php
if(!class_exists('CFSpinner_controller'))
{
    class CFSpinner_controller
    {
        function __construct()
        {

        }
        function load($library)
        {
            $file=plugin_dir_path(__FILE__);
            $cls='CFspinner_'.$library;
            $file .=$cls.'.php';
            require_once($file);
            $ob=new $cls(array('loader'=>$this));
            return $ob;
        }
    }
}
?>