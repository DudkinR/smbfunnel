<?php
class Integrations
{
    var $mysqli;
    var $pref;
    function __construct($arr)
    {
       // print_r($arr);
        $this->mysqli=$arr['mysqli'];
        $this->pref=$arr['dbpref'];
    }
    function getData($get="all",$page=1)
    {
        $mysqli=$this->mysqli;
        $table=$this->pref."qfnl_integrations";
        if(is_numeric($get))
        {
            $id=$mysqli->real_escape_string($get);
            $qry=$mysqli->query("select * from `".$table."` where `id`=".$id."");
            if($qry->num_rows>0)
            {
                return $qry->fetch_object();
            }
            else{return 0;}
        }
        elseif($get==="all")
        {
            if($page==1|| (! is_numeric($page)))
            {
                $page=0;
            }
            else
            {
                $page=($page*10)-10;
            }
            if(isset($_POST["onpage_search"]) && strlen($_POST['onpage_search'])>0)
            {
                $search_keywords=$mysqli->real_escape_string($_POST["onpage_search"]);
                $query_str="select * from `".$table."` where `title` like '%".$search_keywords."%' or `type` like '%".$search_keywords."%' or `data` like '%".$search_keywords."%' or `position` like '%".$search_keywords."%' order by `id` desc";
            }
            else
            {
                $timelimit_condition=1;
                $date_between=dateBetween('added_on');
                if(strlen($date_between[0])>1)
                 {
                     $timelimit_condition=$date_between[0];
                 }
                 $order_by="`id` desc";
                 if(isset($_GET['arrange_records_order']))
                 {
                    $order_by=base64_decode($_GET['arrange_records_order']);
                 }

                 $query_str="select * from `".$table."` where ".$timelimit_condition." order by ".$order_by." limit ".$page.",".get_option('qfnl_max_records_per_page')."";
            }
            return $mysqli->query($query_str);
        }
        elseif($get==="total")
        {
            $timelimit_condition=1;
            $date_between=dateBetween('added_on');
            if(strlen($date_between[0])>1)
             {
                 $timelimit_condition=$date_between[0];
             }

            $qry=$mysqli->query("select count(`id`) as `countid` from `".$table."` where ".$timelimit_condition."");
            if($r=$qry->fetch_object())
            {
                return $r->countid;
            }
            return 0;
        }
    }
}
?>