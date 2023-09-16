<?php
if(!class_exists('CFaddstudent_controller'))
{
  class CFaddstudent_controller
  {
    function __construct()
    {
    }
    function load($library)
    {
      $file=plugin_dir_path(__FILE__);
      $cls='CFAddStudent_'.$library;
      $file .=$cls.'.php';
      require_once($file);
      $ob=new $cls(array('loader'=>$this));
      return $ob;
    }
  }
}
?>