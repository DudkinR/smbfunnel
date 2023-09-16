<?php
if(!class_exists('CFquiz_controller'))
{
    class CFquiz_controller
    {
        function __construct()
        {

        }
        function load($library)
        {
            $file=plugin_dir_path(__FILE__);
            $cls='CFQuizo_'.$library;
            $file .=$cls.'.php';
            require_once($file);
            $ob=new $cls(array('loader'=>$this));
            return $ob;
        }
    }
}
?>