<?php
namespace CFAUTO_autoresponder_addon\sendinblue;
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
        $user_id=$_SESSION['user' . get_option('site_token')];
        $access=$_SESSION['access' . get_option('site_token')];

        if($access == 'admin'){
            $qry=$mysqli->query("select * from `".$table."` WHERE `autoresponder_name`='".$autores."' order by `id` desc");
        }
        else{
            $qry=$mysqli->query("select * from `".$table."` WHERE `autoresponder_name`='".$autores."' AND `user_id`='".$user_id."' order by `id` desc");
        }

        if(!$qry || $qry->num_rows<1)
        {
            return;
        }
        
        while($r=$qry->fetch_object())
        {
            $id="cfautores".$this->autores."_".$r->id;
            
            $title=$r->autoresponder;
            $credentials =$r->autoresponder_detail;

            if($autores=='sendinblue')
            {
              register_autoresponder($id,$title,function($data,$arg2){
                $file=plugin_dir_path(__FILE__);
              $file .="sendinblue/sendinblue.php";
              require_once($file);

              $ob=new \CFAuto_sendinblue_autores();
              $tocheck=$ob->Sendinblue($arg2[0],$data);
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
        $user_id=$_SESSION['user' . get_option('site_token')];
        $access=$_SESSION['access' . get_option('site_token')];
        if($access == 'admin'){
            $qry=$mysqli->query("select count(`id`) as `count_id` from `".$table."`  WHERE `autoresponder_name`='".$autores."'");
        }
        else{
            $qry=$mysqli->query("select count(`id`) as `count_id` from `".$table."`  WHERE `autoresponder_name`='".$autores."' AND `user_id`='".$user_id."'");
        }
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
        $user_id=$_SESSION['user' . get_option('site_token')];
        $access=$_SESSION['access' . get_option('site_token')];
        $limit_str=" limit ".$page.",".$records_to_show;

        $search="";

        if(isset($_POST['onpage_search']))
        {
            $search=trim($mysqli->real_escape_string($_POST['onpage_search']));
            $search=str_replace('_','[_]',$search);
            $search=str_replace('%','[%]',$search);
            $search=" AND  `autoresponder` like '%".$search."%'";
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
        if($access == 'admin'){
            $qry=$mysqli->query("SELECT * FROM `".$table."`  WHERE `autoresponder_name`='".$autores."' ".$search." ORDER BY ".$order_by.$limit_str);
        }
        else{
            $qry=$mysqli->query("SELECT * FROM `".$table."`  WHERE `autoresponder_name`='".$autores."' AND `user_id`='".$user_id."' ".$search." ORDER BY ".$order_by.$limit_str);
        }
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
        $listid = (int)$credentials_j['listid'];
        $new_cred=[];
        $user_id=$_SESSION['user' . get_option('site_token')];
        //$access=$_SESSION['access' . get_option('site_token')];
        foreach($credentials_j as $key => $cred)
        {
                $new_cred[$key]=$mysqli->real_escape_string( $cred );
        }

        $credentials= json_encode( $new_cred );
        $exf = json_encode(array("firstname"=>"Test","lastname"=>"Test","email"=>$email,"listid"=>$listid));
        $date=time();
            if($autotype=='sendinblue')
            {
              $file=plugin_dir_path(__FILE__);
              $file .="sendinblue/sendinblue.php";
              require_once($file);
              $ob=new \CFAuto_sendinblue_autores();
              $tocheck_autores=$ob->Sendinblue($credentials,json_decode($exf, true));
            }
            if($tocheck_autores)
            {
                if($id<1)
                {
                    $sql="INSERT INTO `".$table."` (`autoresponder`, `autoresponder_name`, `autoresponder_detail`, `exf`, `date_created`,`user_id`) VALUES ('".$title."','".$autotype."','".$credentials."','".$exf."','".$date."','".$user_id."')";
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
        echo self::doSaveUpdateSetup($_POST['cfautores_id'],$_POST['cfautores_title'],$_POST['cfautores_method'],$_POST['cfautores_credentials'],$_POST['cfautores_email']);
            die();
        });
    }   
}
?>