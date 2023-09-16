<?php
if(!class_exists('CFProofConvert_controller'))
{
  class CFProofConvert_controller
  {
    function __construct()
    {
    }
    function load($library)
    {
      $file=plugin_dir_path(__FILE__);
      $cls='CFProofConvert_'.$library;
      $file .=$cls.'.php';
      require_once($file);
      $ob=new $cls(array('loader'=>$this));
      return $ob;
    }
  }
}
?>