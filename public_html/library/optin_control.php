<?php
class Optincontrol
{
	var $mysqli;
	var $dbpref;
	var $load;
	function __construct($arr)
	{
		$this->mysqli=$arr['mysqli'];
		$this->dbpref=$arr['dbpref'];
		$this->load=$arr['load'];
	}
	function getOptionForSpecifcPagesforZapier()
	{
		$mysqli=$this->mysqli;
		$pref=$this->dbpref;
		$table=$pref."quick_optins";
		$qry=$mysqli->query("select `a`.id,`a`.name,`a`.email,`a`.extras,`a`.ipaddr,(select `valid_inputs` from `".$pref."quick_pagefunnel` where `id`=`a`.pageid) as `zap_valid_inputs` from `".$table."` as `a` where `pageid` in (select `id` from `".$pref."quick_pagefunnel` where `settings` like '%\"zapier_enable\":true%') and `send_zap` not in('1') limit 100");
		$token=get_option('site_token');
		$arr=array();
		while($r=$qry->fetch_assoc())
		{
			$r['id'] +=125;
			$r['id'] =hash_hmac('sha1',$r['id'],$token);
			$valid_inputs=explode(",",$r['zap_valid_inputs']);
			$extras=(array)json_decode($r['extras']);

			$data_arr=array('id'=>$r['id']);
			foreach($valid_inputs as $valid_input)
			{
				if(in_array($valid_input,array('name','email')))
				{
					$data_arr[$valid_input]=$r[$valid_input];
				}
				elseif(isset($extras[$valid_input]))
				{
					$data_arr[$valid_input]=$extras[$valid_input];
				}
			}

			array_push($arr,json_encode($data_arr));
		}
		$up=$mysqli->query("update `".$table."` set `send_zap`='1' where `pageid` in (select `id` from `".$pref."quick_pagefunnel` where `settings` like '%\"zapier_enable\":true%') and `send_zap` not in('1') limit 100");

		return "[".implode(",",$arr)."]";
	}
	function visualOptisForFunnels($funnel_id='all',$pagecount=0)
	{
		$mysqli=$this->mysqli;
		$pref=$this->dbpref;
	$table=$pref."quick_optins";
	
	$limit=get_option('qfnl_max_records_per_page');

		$funnel_id=$mysqli->real_escape_string($funnel_id);
		$pagecount=$mysqli->real_escape_string($pagecount);

    $tempdatebetween_arr=dateBetween('addedon');
    $datebetween=$tempdatebetween_arr[1];
		$datebetween_all=$tempdatebetween_arr[0];


		$countsql="select count(`id`) as `totaloptins` from `".$table."`".$datebetween_all;
		$baseurl="";
		$extrafields=0;
		$total=0;
		if(is_numeric($funnel_id)||strpos($funnel_id,"page@")===0)
		{
    $pageidqry="";
    if(strpos($funnel_id,"page@")===0)
		{
			$pageidd=str_replace("page@","",$funnel_id);
			$pageidd=explode('@',$mysqli->real_escape_string($pageidd));

			$pageid=$pageidd[1];
			$funnel_id=$pageidd[0];

			if(!is_numeric($pageid))
			{
			$pagefunnelquery=$mysqli->query("select `id` from `".$pref."quick_pagefunnel` where `funnelid`='".$funnel_id."' and `filename`='".$pageid."'");
			if($pagefunnelquery->num_rows>0)
			{
				$pageidarrr=array();
				while($fidobb=$pagefunnelquery->fetch_object())
				{
					array_push($pageidarrr,"'".$fidobb->id."'");
				}
				$pageidqry=' and `pageid` in ('.implode(',',$pageidarrr).') ';
			}
			else {
				return 0;
			}
			}
			else {$pageidqry=" and `pageid`='".$pageid."'";}
		}
	}
		else
		{
		return 0;
		}

		$countsql="select count(`id`) as totaloptins from `".$table."` where funnelid='".$funnel_id."'".$pageidqry.$datebetween;

    $total_qry=$mysqli->query($countsql);
		if($total_qry)
		{
			if($res=$total_qry->fetch_object())
			{
				$total=$res->totaloptins;
			}
		}

		$order_by="`id` desc";
		if(isset($_GET['arrange_records_order']))
		{
		   $order_by=base64_decode($_GET['arrange_records_order']);
		}
		
		$search_qry="";
		if(isset($_POST["onpage_search"]) && strlen($_POST["onpage_search"])>0)
		{
			$_POST["onpage_search"]=$mysqli->real_escape_string($_POST["onpage_search"]);
			$search_qry=" and (`name` like '%".$_POST["onpage_search"]."%' or `email` like '%".$_POST["onpage_search"]."%' or `extras` like '%:\"%".$_POST["onpage_search"]."%\"%' or `ipaddr` like '%".$_POST["onpage_search"]."%')";
		}
		if($pagecount==0)
		{
		$sql="select * from `".$table."` where funnelid='".$funnel_id."'".$pageidqry.$datebetween.$search_qry." order by ".$order_by." limit ".$limit."";
		}
		else
		{
		$pagecount=($pagecount*$limit)-$limit;
		$sql="select * from `".$table."` where funnelid='".$funnel_id."'".$pageidqry.$datebetween.$search_qry." order by ".$order_by." limit ".$pagecount.", ".$limit."";
		}
		$funnel=$this->load->loadFunnel();
		$funnel_data=$funnel->getFunnel($funnel_id);
		$extrafields=$funnel_data->validinputs;
		$baseurl=$funnel_data->baseurl;
		if(strlen($pageidqry)>1)
		{
      if(is_numeric($pageid))
			{
			$pagevalidinputsquery=$mysqli->query("select `valid_inputs` from `".$pref."quick_pagefunnel` where `id`=".$pageid." limit 1");
		  }
			else
			{
			$pagevalidinputsquery=$mysqli->query("select `valid_inputs` from `".$pref."quick_pagefunnel` where `funnelid`='".$funnel_id."' and `filename`='".$pageid."' limit 1");
			}

			if($pagevalidinputsquery->num_rows>0)
			{
				$pagevalidinputsquery_ob=$pagevalidinputsquery->fetch_object();
				if(strlen($pagevalidinputsquery_ob->valid_inputs)>0)
				{
				$extrafields=	$pagevalidinputsquery_ob->valid_inputs;
				}
			}
		}
		if(strlen($extrafields)>0)
		{
			$extrafields=explode(',',$extrafields);
		}
		else
		{
			$extrafields=0;
		}

		$qry=$mysqli->query($sql);
		return array('leads'=>$qry,'total'=>$total,'extracols'=>$extrafields,'base_url'=>$baseurl);
	}

	function deleteOptin($id,$by='id')
	{
		//delete optin
		//$by may be id or funnelid or pageid
		$mysqli=$this->mysqli;
		$pref=$this->dbpref;
		$id=$mysqli->real_escape_string($id);
		if(is_numeric($id))
		{
			if($by !='id')
			{
				$by="'".$id."'";
			}
		}
        $table=$pref."quick_optins";
		$mysqli->query("delete from `".$table."` where `".$by."`=".$id."");
	}
	function optinToCsv($funnelid,$pageid)
	{
		//optin to csv
		$mysqli=$this->mysqli;
		$pref=$this->dbpref;
    $table=$pref."quick_optins";
		$page_table=$pref.'quick_pagefunnel';
		$funnel_ob=$this->load->loadFunnel();
    $ids=array();
    $input_fields="";
		$filename="";

		$funnelid=$mysqli->real_escape_string($funnelid);
		$pageid=$mysqli->real_escape_string($pageid);

    if(is_numeric($pageid))
		{
		$getpagedataqry=$mysqli->query("select `valid_inputs`,`id`,`filename` from `".$page_table."` where `level` in(select `level` from `".$page_table."` where `id`='".$pageid."') and `funnelid`='".$funnelid."'");
	  }
		else {
$getpagedataqry=$mysqli->query("select `valid_inputs`,`id`,`filename` from `".$page_table."` where `funnelid`='".$funnelid."' and `filename`='".$pageid."'");
		}

		if($getpagedataqry->num_rows)
		{
     while($r=$getpagedataqry->fetch_object())
		 {
			 array_push($ids,"'".$r->id."'");
			 $input_fields=$r->valid_inputs;
			 $filename=$r->filename;
		 }
		}

		if(strlen($input_fields)<1)
		{
		$getfetfunnel=$funnel_ob->getFunnel($funnelid);
		if($getfetfunnel)
		{
		$input_fields=$getfetfunnel->validinputs;
		}
		}
		$input_fields=explode(",",$input_fields);

		$optinqry=$mysqli->query("select * from `".$table."` where `funnelid`='".$funnelid."' and `pageid` in (".implode(',',$ids).")");

		//headers
		$csvheaders=array("#");
		$csvheaders=array_merge($csvheaders,$input_fields,array('ip','date'));

		$output_filename = $filename.'.csv';
		$output_handle = @fopen( 'php://output', 'w' );

		header( 'Cache-Control: must-revalidate, post-check=0, pre-check=0' );
		header( 'Content-Description: File Transfer' );
		header( 'Content-type: text/csv' );
		header( 'Content-Disposition: attachment; filename=' . $output_filename );
		header( 'Expires: 0' );
		header( 'Pragma: public' );
		fputcsv( $output_handle, $csvheaders);

		if($optinqry->num_rows>0)
		{
			$count=0;
			while($r=$optinqry->fetch_assoc())
			{
				++$count;
				$outputarr=array($count);
				$exfarr=(array)json_decode($r['extras']);

				if(isset($r['addedon']))
				{
				$r['addedon']=date('d-M-Y h:ia',$r['addedon']);
				}

				for($i=1;$i<count($csvheaders);$i++)
				{
					if($csvheaders[$i]=="ip"){$csvheaders[$i]="ipaddr";}
					if($csvheaders[$i]=="date"){$csvheaders[$i]="addedon";}

					if(isset($r[$csvheaders[$i]]))
					{
					array_push($outputarr,$r[$csvheaders[$i]]);
					}
					elseif(isset($exfarr[$csvheaders[$i]]))
					{
					array_push($outputarr,$exfarr[$csvheaders[$i]]);
					}
					else
					{
					array_push($outputarr,"");
					}
				}
				fputcsv( $output_handle,$outputarr);
			}

		}

fclose( $output_handle );
die();
	}
}
?>
