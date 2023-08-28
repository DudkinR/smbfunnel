<?php
class Smtpcontrol
{
	var $mysqli;   
	var $dbpref;  
	function __construct($arr)
	{
		$this->mysqli=$arr['mysqli'];
		$this->dbpref=$arr['dbpref'];
	}
	function getSMTP($id,$all=0)
	{
		//get list
		$mysqli=$this->mysqli;
		$pref=$this->dbpref;
		$table=$pref."quick_smtp_setting";
		$id=$mysqli->real_escape_string($id);
		$user_id = $_SESSION['user' . get_option('site_token')];
		$access = $_SESSION['access' . get_option('site_token')];
		if($access == 'admin')
		{
			$where_data = " where 1";
			$add_user = " ";
		}
		else
		{
			$where_data = " where user_id = '".$user_id."'";
			$add_user = " and user_id = '".$user_id."'";
		}
		
		if($all==1)
		{
			$qry=$mysqli->query("select * from `".$table."` ".$where_data." order by id desc");
			if($qry->num_rows>0)
			{
				return $qry;
			}
			else
			{
				return 0;
			}
		}
		else
		{
			$qry=$mysqli->query("select * from `".$table."` where `id`=".$id." ".$add_user." limit 1");
			if($qry->num_rows>0)
			{
				return $qry->fetch_object(); 
			}
			else
			{
				return 0;
			}
		}
	}
	function pluginGetSMTPs($data)
	{
	   $mysqli=$this->mysqli;
	   $dbpref=$this->dbpref;
	   $table=$dbpref.'quick_smtp_setting';
	   $arr=array();
	   $qry=$mysqli->query("select * from `".$table."`".$data);
	   while($r=$qry->fetch_assoc())
	   {
		 $r['user_name']=$r['username'];
		 unset($r['username']);
		 $r['host_name']=$r['hostname'];
		 unset($r['hostname']);
		 $r['from_name']=$r['fromname'];
		 unset($r['fromname']);
		 $r['from_email']=$r['fromemail'];
		 unset($r['fromemail']);
		 $r['reply_name']=$r['replyname'];
		 unset($r['replyname']);
		 $r['reply_email']=$r['replyemail'];
		 unset($r['replyemail']);
		 array_push($arr,$r);
	   }
	   return $arr;
	}
}

?>