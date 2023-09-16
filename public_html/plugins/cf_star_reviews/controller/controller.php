<?php
if(!class_exists('CFProduct_review_controller'))
{
  class CFProduct_review_controller
  {
    function __construct()
    {
    }
    function load($library)
    {
      $file=plugin_dir_path(__FILE__);
      $cls='CFProduct_review_'.$library;
      $file .=$cls.'.php';
      require_once($file);
      $ob=new $cls(array('loader'=>$this));
      return $ob;
    }
  }
}
?>