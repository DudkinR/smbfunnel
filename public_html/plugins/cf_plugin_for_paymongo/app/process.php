<?php
namespace CFPAY_peyment_addon\paymongo;
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
        $qry=$mysqli->query("select * from `".$table."` where `method`='".$method."' order by `id` desc");
        
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
             
                if($data['method']=='paymongo')
                {
                    
                    $file=plugin_dir_path(__FILE__);
                     $file .="paymongo/paymongo_payment.php";
                    require_once($file);
                    $ob=new \CFPay_paymongo_payment();
                    return $ob->doPayment($data, $product, $call_back_url);
                }
          
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

        $qry=$mysqli->query("select count(`id`) as `count_id` from `".$table."` where `method`='".$method."'");
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
        $qry=$mysqli->query("select * from `".$table."` where `method`='".$method."'".$search." order by ".$order_by.$limit_str);

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
        $table= $dbpref."payment_methods";
        $id=$mysqli->real_escape_string($id);

        $qry=$mysqli->query("delete from `".$table."` where `id`=".$id."");
        return ($qry)? 1:0;
    }
    function doSaveUpdateSetup( $data )
    {
        global $mysqli;
        global $dbpref;
        $table= $dbpref."payment_methods";


        $id=$mysqli->real_escape_string($data['pespal_id']);
        $id=(int)$id;
        $title=$mysqli->real_escape_string($data['paymongo_title']);
        $public_key=$mysqli->real_escape_string($data['paymongo_public_key']);
        $secret_key=$mysqli->real_escape_string($data['paymongo_secret_key']);
        $tax=$mysqli->real_escape_string($data['paymongo_tax']);
        $type=$mysqli->real_escape_string($data['paymongo_type']);
        $method=$this->method;
        
        $credentials =json_encode( array( "public_key" => $public_key,"secret_key" => $secret_key, "type" => $type ) );
        // print_r($credentials);
        // exit;

        if( $id<1 )
        {
        
            $qry="insert into `".$table."` (`title`, `method`, `credentials`, `tax`, `createdon`) values ('".$title."','".$method."','".$credentials."','".$tax."','".date('Y-m-d H:i:s')."')";
        }
        else
        {
            $qry="update `".$table."` set `title`='".$title."', `method`='".$method."', `credentials`='".$credentials."', `tax`='".$tax."' where `id`=".$id."";
        }
        $return_status = ($mysqli->query($qry))? 1:0;
        if($return_status)
        {
            echo json_encode(array("status" =>1, "message"=>"Saved successfully") );
        }else{
            echo json_encode(array("status" =>0,  "message"=>"Unable to save the setup") );
        }
    }
    function registerAjaxRequest(){
        add_action('cf_ajax_cfpay_savepaymongo',function(){
            if( $_POST['paymongo_title'] == "" )
            {
                echo json_encode(array("status"=>0, "message"=>"Please provide data for Title."));
                die();
            }
            elseif($_POST['paymongo_public_key'] == ""){
                echo json_encode(array("status"=>0, "message"=>"Please provide data for Public Key."));
                die();
            }
            elseif($_POST['paymongo_secret_key'] == ""){
                echo json_encode(array("status"=>0, "message"=>"Please provide data for Secret Key."));
                die();
            }
            elseif($_POST['paymongo_type'] == ""){
                echo json_encode(array("status"=>0, "message"=>"Please select type"));
                die();
            }
            elseif( !is_numeric($_POST['paymongo_tax']) ){
                echo json_encode(array("status"=>0, "message"=>"Only Number Allowed"));
                die();
            }
            else{   
                echo self::doSaveUpdateSetup($_POST);
                die();
            }
        });
    }
}
?>