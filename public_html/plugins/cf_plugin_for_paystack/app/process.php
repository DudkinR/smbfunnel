<?php
namespace CFPAY_peyment_addon\paystack;
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

        $table=$dbpref."cfpay_addon_credentials_".$this->method;
        $user_id=$_SESSION['user' . get_option('site_token')]; 
        $access=$_SESSION['access' . get_option('site_token')]; 
        if($access=='admin')
        {
            $qry=$mysqli->query("select * from `".$table."` order by `id` desc");
        }
        else
        {
            $qry=$mysqli->query("select * from `".$table."` where `user_id`=".$user_id." order by `id` desc");
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
                'tax'=>((is_numeric($r->tax))? 0:$r->tax),
                'credentials'=>$r->credentials
            ),
            function($data, $product, $call_back_url){
                //here add your payment methods to process
                
                if($data['method']=='paystack')
                {
                    $file=plugin_dir_path(__FILE__);
                    $file .="paystack/Paystack_payment.php";
                    require_once($file);
                    $ob=new \CFPay_Paystack_payment();
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
        $table= $dbpref.'cfpay_addon_credentials_'.$this->method;
        $user_id=$_SESSION['user' . get_option('site_token')]; 
        $access=$_SESSION['access' . get_option('site_token')]; 
        if($access=='admin')
        {
            $qry=$mysqli->query("select count(`id`) as `count_id` from `".$table."`");
        }
        else
        {
            $qry=$mysqli->query("select count(`id`) as `count_id` from `".$table."` where `user_id`=".$user_id."");
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
        $table= $dbpref.'cfpay_addon_credentials_'.$this->method;
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
        if($access=='admin')
        {
            $qry=$mysqli->query("select * from `".$table."`".$search." order by ".$order_by.$limit_str);
        }
        else
        {
            $qry=$mysqli->query("select * from `".$table."` where `user_id`=".$user_id.$search." order by ".$order_by.$limit_str);
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
        $table= $dbpref.'cfpay_addon_credentials_'.$this->method;

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
        $table= $dbpref.'cfpay_addon_credentials_'.$this->method;
        $id=$mysqli->real_escape_string($id);

        $qry=$mysqli->query("delete from `".$table."` where `id`=".$id."");
        return ($qry)? 1:0;
    }
    function doSaveUpdateSetup($id,$title,$method,$credentials,$tax=0)
    {
        global $mysqli;
        global $dbpref;
        $table= $dbpref.'cfpay_addon_credentials_'.$this->method;

        $id=$mysqli->real_escape_string($id);
        $id=(int)$id;
        $title=$mysqli->real_escape_string($title);
        $method=$mysqli->real_escape_string($method);
        $credentials=$mysqli->real_escape_string($credentials);
        $tax=$mysqli->real_escape_string($tax);
        $user_id=$_SESSION['user' . get_option('site_token')]; 

        if($id<1)
        {
            $qry="insert into `".$table."` (`title`, `method`, `credentials`, `tax`, `added_on`,`user_id`) values ('".$title."','".$method."','".$credentials."','".$tax."','".date('Y-m-d H:i:s')."',".$user_id.")";
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