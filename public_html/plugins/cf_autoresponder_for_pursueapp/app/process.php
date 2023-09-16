<?php
namespace CFAUTO_autoresponder_addon\pursueapp;
class Cfautores_processor
{
    var $pref;
    function __construct($arr)
    {
        $this->pref=$arr['pref'];
        self::loadMethods();
    }
    function loadMethods()
    {
        global $mysqli;
        global $dbpref;
        $autores= $mysqli->real_escape_string($this->autores);
        $table=$dbpref.'quick_autoresponders';


        $qry=$mysqli->query("SELECT * FROM `".$table."` WHERE `autoresponder_name`='".$autores."' ORDER  BY `id` DESC");

        if(!$qry || $qry->num_rows<1)
        {
            return;
        }
        
        while($r=$qry->fetch_object())
        {
            $id="cfautores".$this->autores."_".$r->id;
            
            $title=$r->autoresponder;
            $credentials =$r->autoresponder_detail;

            if($autores=='pursueapp')
            {
              register_autoresponder($id,$title,function($data,$arg2){
                $file=plugin_dir_path(__FILE__);
              $file .="pursueapp/pursueapp.php";
              require_once($file);

              $ob=new \CFAuto_pursueapp_autores();
              $tocheck=$ob->Pursueapp($arg2[0],$data['email'],$data['name']);
                },array($credentials));  
              } 
              else
              {
                  return false;
              } 
        }
        
    }
    function countSetups()
    {
        global $mysqli;
        global $dbpref;
        $table= $dbpref.'quick_autoresponders';
        $autores= $mysqli->real_escape_string($this->autores);
        $qry=$mysqli->query("select count(`id`) as `count_id` from `".$table."`  WHERE `autoresponder_name`='".$autores."'");
        if($qry->num_rows>0)
        {
            $r=$qry->fetch_object();
            return $r->count_id;
        }

        return 0;
    }
    function loadSetups($page=1)
    {
        //print_r($_GET);
        global $mysqli;
        global $dbpref;
        $table= $dbpref.'quick_autoresponders';
        $page=$mysqli->real_escape_string($page);
        $page=(int)$page;
        $page=($page<1)? 1:$page;
        $records_to_show=get_option('qfnl_max_records_per_page');
        $records_to_show=(int) $records_to_show;
        $page=($page*$records_to_show)-$records_to_show;

        $limit_str=" limit ".$page.",".$records_to_show;

        $search="";

        if(isset($_POST['onpage_search']))
        {
            $search=trim($mysqli->real_escape_string($_POST['onpage_search']));
            $search=str_replace('_','[_]',$search);
            $search=str_replace('%','[%]',$search);
            $search=" AND `autoresponder_name` like '%".$search."%' or `autoresponder` like '%".$search."%'";
        }
        $arr=array();

        $order_by="`id` desc";
        if(isset($_GET['arrange_records_order']))
        {
            $order_by=base64_decode($_GET['arrange_records_order']);
        }

        $date_between=dateBetween('added_on',null,true);

        if(strlen($date_between[0])>0)
        {
            if(strlen($search)>0)
            {
                $search .=$date_between[1];
            }
            else
            {
                $search =" AND ".$date_between[0];
            }
        }
        $autores= $mysqli->real_escape_string($this->autores);
        $qry=$mysqli->query("SELECT * FROM `".$table."`  WHERE `autoresponder_name`='".$autores."' AND 1 ".$search." ORDER BY ".$order_by.$limit_str);
        while($r=$qry->fetch_object())
        {
            array_push($arr,$r);
        }
        
        return $arr;
    }
    function getSetup($id)
    {
        global $mysqli;
        global $dbpref;
        $table= $dbpref.'quick_autoresponders';

        $id=$mysqli->real_escape_string($id);
        $qry=$mysqli->query("select * from `".$table."` where `id`=".$id."");
        if($qry->num_rows>0)
        {
            return $qry->fetch_object();
        }
        else
        {return false;}
    }
    function deleteSetup($id)
    {
        global $mysqli;
        global $dbpref;
        $table= $dbpref.'quick_autoresponders';
        $id=$mysqli->real_escape_string($id);
        $qry=$mysqli->query("delete from `".$table."` where `id`=".$id."");
        return ($qry)?1:0;
    }
    function doSaveUpdateSetup($id,$title,$autotype,$credentials_js,$email)
    {  
        global $mysqli;
        global $dbpref;
        $table= $dbpref.'quick_autoresponders';
        $id=$mysqli->real_escape_string($id);
        $id=(int)$id;
        $title = $mysqli->real_escape_string($title);
        $autotype = $mysqli->real_escape_string($autotype);
        $email = $mysqli->real_escape_string($email);
        $credentials_j= json_decode($credentials_js,true);
        $new_cred=[];
        foreach($credentials_j as $key => $cred)
        {
                $new_cred[$key]=$mysqli->real_escape_string( $cred );
        }

        $credentials= json_encode( $new_cred );
        $exf = json_encode(array("name"=>"test","email"=>$email));
        $date=time();
            if($autotype=='pursueapp')
            {
              $file=plugin_dir_path(__FILE__);
              $file .="pursueapp/pursueapp.php";
              require_once($file);
              $ob=new \CFAuto_pursueapp_autores();
              $tocheck_autores=$ob->Pursueapp($credentials,$email,'test');
            }
            if($tocheck_autores)
            {
                if($id<1)
                {
                  $sql="INSERT INTO `".$table."` (`autoresponder`, `autoresponder_name`, `autoresponder_detail`, `exf`, `date_created`) VALUES ('".$title."','".$autotype."','".$credentials."','".$exf."','".$date."')";
                }
                else
                {
                    $sql = "UPDATE `".$table."` set `autoresponder`='".$title."',`autoresponder_name`='".$autotype."',`autoresponder_detail`='".$credentials."',`exf`='".$exf."' where `id`='".$id."'";
                }
                return ($mysqli->query($sql))? 1:0;
            }
    }
    function registerAjaxRequest(){

        add_action('cf_ajax_cfautores_savcredentials'.$this->autores,function(){
          //  print_r($_POST);
        echo self::doSaveUpdateSetup($_POST['cfautores_id'],$_POST['cfautores_title'],$_POST['cfautores_method'],$_POST['cfautores_credentials'],$_POST['cfautores_email']);
            die();
        });
    }   
}
?>