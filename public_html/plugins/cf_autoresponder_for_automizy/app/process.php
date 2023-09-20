<?php
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
        $table=$dbpref.'cfplugin_autoresponders';

        $qry=$mysqli->query("select * from `".$table."` order by `id` desc");
        
        if(!$qry || $qry->num_rows<1)
        {
            return;
        }
        
        while($r=$qry->fetch_object())
        {
            $id="cfautores_".$r->id;
            $title=$r->autoresponder;
            $method=$r->autoresponder_name;
            $jsonarr = json_decode($r->autoresponder_detail);
            //print_r($jsonarr);
            $japikey = $jsonarr->api_token;
            $jlistid = $jsonarr->list_id;
            $credential_arr=array($japikey,$jlistid);

            if($method=='automizy')
            {
              register_autoresponder($id,$title,function($data,$arg2){  
              $file=plugin_dir_path(__FILE__);
              $file .="automizy/automizy.php";
              require_once($file);
              $ob=new Cfautomizy();
              $tocheck=$ob->automizy($arg2['0']['0'],$arg2['0']['1'],$data['name'],$data['email']);
                },array($credential_arr));  
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
        $table= $dbpref.'cfplugin_autoresponders';

        $qry=$mysqli->query("select count(`id`) as `count_id` from `".$table."`");
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
        $table= $dbpref.'cfplugin_autoresponders';
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
            $search=" where `title` like '%".$search."%' or `method` like '%".$search."%' or `credentials` like '%".$search."%'";
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
                $search =" where".$date_between[0];
            }
        }
        //echo "select * from `".$table."`".$search." order by ".$order_by.$limit_str;
        $qry=$mysqli->query("select * from `".$table."`".$search." order by ".$order_by.$limit_str);

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
        $table= $dbpref.'cfplugin_autoresponders';

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
        $table= $dbpref.'cfplugin_autoresponders';
        $id=$mysqli->real_escape_string($id);
        $qry=$mysqli->query("delete from `".$table."` where `id`=".$id."");
        return ($qry)?1:0;
    }
    function doSaveUpdateSetup($id,$title,$autotype,$jsonencode,$email)
    {  
        global $mysqli;
        global $dbpref;
        $table= $dbpref.'cfplugin_autoresponders';
        $id=$mysqli->real_escape_string($id);
        $id=(int)$id;
        $title = $mysqli->real_escape_string($title);
        $date=time();
        $jsonarr = json_decode($jsonencode);
            $japikey = $jsonarr->api_token;
            $jlistid = $jsonarr->list_id;
            if($autotype=='automizy')
            {
              $file=plugin_dir_path(__FILE__);
              $file .="automizy/automizy.php";
              require_once($file);
              $ob=new Cfautomizy();
              $tocheck_automizy=$ob->automizy($japikey,$jlistid,$title,$email);
            }
            if($tocheck_automizy)
            {
                if($id<1)
                {
                    $sql="INSERT INTO `".$table."` (`autoresponder`, `autoresponder_name`, `autoresponder_detail`, `exf`, `date_created`) VALUES ('".$title."','".$autotype."','".$jsonencode."','','".$date."')";
                }
                else
                {
                    $sql = "UPDATE `".$table."` set `autoresponder`='".$title."',`autoresponder_name`='".$autotype."',`autoresponder_detail`='".$jsonencode."' where `id`='".$id."'";
                }
                return ($mysqli->query($sql))? 1:0;
            }
    }
    function registerAjaxRequest(){
        add_action('cf_ajax_cfautores_savcredentials',function(){
        echo self::doSaveUpdateSetup($_POST['cfautores_id'],$_POST['cfautores_title'],$_POST['cfautores_method'],$_POST['cfautores_credentials'],$_POST['cfautores_email']);
            die();
        });
    }   
}
?>