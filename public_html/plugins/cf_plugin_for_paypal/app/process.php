<?php
namespace CFPAY_peyment_addon\paypal;
class Cfpay_processor
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

        $method= $mysqli->real_escape_string($this->method);

        $table=$dbpref."payment_methods";
        $user_id=$_SESSION['user' . get_option('site_token')]; 
        $access=$_SESSION['access' . get_option('site_token')]; 
        if($access=='admin')
        {
            $qry=$mysqli->query("select * from `".$table."` where `method`='".$method."' order by `id` desc");
        }
        else
        {
            $qry=$mysqli->query("select * from `".$table."` where `method`='".$method."' and `user_id`=".$user_id." order by `id` desc");
        }
        if(!$qry || $qry->num_rows<1)
        {
            return;
        }
        
        while($r=$qry->fetch_object())
        {
            $id="cfpay_".$this->method."_".$r->id;
            register_payment_method($id,
            array(
                'title'=>$r->title,
                'method'=>$r->method,
                'tax'=>((is_numeric($r->tax))? $r->tax:0),
                'credentials'=>$r->credentials
            ),
            function($data, $product, $call_back_url){
                //here add your payment methods to process
                
                if($data['method']=='paypal')
                {
                    $file=plugin_dir_path(__FILE__);
                    $file .="paypal/Paypal_payment.php";
                    require_once($file);
                    $ob=new \CFPay_Paypal_payment();
                    return $ob->doPayment($data, $product, $call_back_url);
                }
                /*
                else if($data['method']=='flutterwave')
                {
                    $file=plugin_dir_path(__FILE__);
                    $file .="flutterwave/Flutterwave_payment.php";
                    require_once($file);
                    $ob=new CFPay_Flutterwave_payment();
                    return $ob->doPayment($data, $product, $call_back_url);
                }*/
                else
                {
                    return false;
                }
            }
            );
        }
    }
    function countSetups()
    {
        global $mysqli;
        global $dbpref;
        $table= $dbpref.'payment_methods';

        $method= $mysqli->real_escape_string($this->method);
        $user_id=$_SESSION['user' . get_option('site_token')]; 
        $access=$_SESSION['access' . get_option('site_token')]; 
        if($access=='admin')
        {
            $qry=$mysqli->query("select count(`id`) as `count_id` from `".$table."` where `method`='".$method."'");
        }
        else
        {
            $qry=$mysqli->query("select count(`id`) as `count_id` from `".$table."` where `method`='".$method."' and `user_id`=".$user_id."");
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
        $table= $dbpref.'payment_methods';
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
            $search=" and (`title` like '%".$search."%' or `method` like '%".$search."%' or `credentials` like '%".$search."%')";
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
        $method= $mysqli->real_escape_string($this->method);
        //echo "select * from `".$table."`".$search." order by ".$order_by.$limit_str;
        $user_id=$_SESSION['user' . get_option('site_token')]; 
        $access=$_SESSION['access' . get_option('site_token')]; 
        if($access=='admin')
        {
            $qry=$mysqli->query("select * from `".$table."` where `method`='".$method."'".$search." order by ".$order_by.$limit_str);
        }
        else
        {
            $qry=$mysqli->query("select * from `".$table."` where `method`='".$method."' and `user_id`=".$user_id."".$search." order by ".$order_by.$limit_str);
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
        $table= $dbpref.'payment_methods';

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
        $table= $dbpref.'payment_methods';
        $id=$mysqli->real_escape_string($id);

        $qry=$mysqli->query("delete from `".$table."` where `id`=".$id."");
        return ($qry)? 1:0;
    }
    function doSaveUpdateSetup($id,$title,$method,$credentials,$tax=0)
    {
        global $mysqli;
        global $dbpref;
        $table= $dbpref.'payment_methods';

        $id=$mysqli->real_escape_string($id);
        $id=(int)$id;
        $title=$mysqli->real_escape_string($title);
        $method=$mysqli->real_escape_string($method);
        $credentials=$mysqli->real_escape_string($credentials);
        $tax=$mysqli->real_escape_string($tax);
        $user_id=$_SESSION['user' . get_option('site_token')]; 
      //  $access=$_SESSION['access' . get_option('site_token')]; 
        if($id<1)
        {
            $qry="insert into `".$table."` (`title`, `method`, `credentials`, `tax`, `createdon`,`user_id`) values ('".$title."','".$method."','".$credentials."','".$tax."','".date('Y-m-d H:i:s')."',".$user_id.")";
        }
        else
        {
            $qry="update `".$table."` set `title`='".$title."', `method`='".$method."', `credentials`='".$credentials."', `tax`='".$tax."' where `id`=".$id."";
        }

        return ($mysqli->query($qry))? 1:0;
    }
    function registerAjaxRequest(){
        add_action('cf_ajax_cfpay_savcredentials_'.$this->method,function(){
        echo self::doSaveUpdateSetup($_POST['cfpay_payment_id'],$_POST['cfpay_payment_title'],$_POST['cfpay_payment_method'],$_POST['cfpay_payment_credentials'],$_POST['cfpay_payment_tax']);
            die();
        });
    }
}
?>