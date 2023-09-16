<?php
if (!class_exists('CFrecaptcha_form_controller')) {
class CFrecaptcha_form_controller
{
   var $pref;
    
    function __construct($arr)
    {
        
        // $this->pref=$arr['pref'];
        
        $this->loader = $arr['loader'];
        
        
    }
   
    function countSetups()
    {
        
        global $mysqli;
        global $dbpref;
        $table= $dbpref.'google_recaptcha';
       
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
        $table= $dbpref.'google_recaptcha';
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
            $search=" and (`g_title` like '%".$search."%'`credentials` like '%".$search."%')";
        }

        $arr=array();

        $order_by="`id` desc";
        if(isset($_GET['arrange_records_order']))
        {
            $order_by=base64_decode($_GET['arrange_records_order']);
        }

        $date_between=dateBetween('createdon',null,true);

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
        
        $qry=$mysqli->query("select * from `".$table."` ".$search." order by ".$order_by.$limit_str);

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
        $table= $dbpref.'google_recaptcha';

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
        // print_r($id);
        // exit;
        global $mysqli;
        global $dbpref;
        $table= $dbpref.'google_recaptcha';
        $id=$mysqli->real_escape_string($id);

        $qry=$mysqli->query("delete from `".$table."` where `id`=".$id."");
        return ($qry)? 1:0;
    }
    function doSaveUpdateSetup( $data )
    {
        global $mysqli;
        global $dbpref;
        $table= $dbpref.'google_recaptcha';


        $id=$mysqli->real_escape_string($data['pespal_id']);
        $id=(int)$id;
        $title=$mysqli->real_escape_string($data['google_recaptcha_title']);
        $version=$mysqli->real_escape_string($data['google_recaptcha_version']);
        $site_key=$mysqli->real_escape_string($data['google_recaptcha_site_key']);
        $secret_key=$mysqli->real_escape_string($data['google_recaptcha_secret_key']);
        date_default_timezone_set('Asia/Kolkata');
        $dateTime = date("Y-m-d H:i:s"); 
        
        
        $credentials =json_encode( array( "site_key" => $site_key,"secret_key" => $secret_key ) );

        // print_r($credentials);
        // exit;

        if( $id<1 )
        {
        
            $qry="insert into `".$table."` (`g_title`, `g_version`, `credentials`, `createdon`) values ('".$title."','".$version."','".$credentials."','".$dateTime."')";
        }
        else
        {
            $qry="update `".$table."` set  `g_title`='".$title."',`g_version`='".$version."', `credentials`='".$credentials."' where `id`=".$id."";
        }
        $return_status = ($mysqli->query($qry))? 1:0;
        if($return_status)
        {
            echo json_encode(array("status" =>1, "message"=>"Saved successfully") );
        }else{
            echo json_encode(array("status" =>0,  "message"=>"Unable to save the setup") );
        }
    }
    
}
}
?>