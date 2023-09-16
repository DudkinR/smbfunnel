<?php
if(!class_exists('CFgiftdiscount_controller'))
{
  class CFgiftdiscount_controller
  {
    function __construct()
    {
      $this->load= $this;
      
    
    }
    function load($library)
    {
      global $app_variant;
      $students="customer";
      if( $app_variant == "shopfunnels" ){
        $students="Customer";
        
        }
        elseif( $app_variant == "cloudfunnels" ){
            $students="Member";
        
        }
        elseif( $app_variant == "coursefunnels" ){
            $students="Student";
        
        }
        
      $file=plugin_dir_path(__FILE__);
      $cls="CFDiscount_".$library;
      $file .=$cls.'.php';
      require_once($file);
      $ob=new $cls(array('loader'=>$this,"students"=>$students));
      return $ob;
    }
    function view($file, $data= array())
    {
        $dir= plugin_dir_path(__FILE__);
        $file = $dir."../views/".$file.'.php';
        require_once($file);
    }
  }
  
}
?>