<?php
class ComposeMail
{
    var $mysqli;
	var $dbpref;
	var $load;
	var $ip;
	var $sequence_ob;
    function __construct($arr)
    {
        $this->mysqli=$arr['mysqli'];
		$this->dbpref=$arr['dbpref'];
		$this->load=$arr['load'];
		$this->ip=$arr['ip'];
		$this->sequence_ob=$this->load->loadSequence();
	}
	function init($title,$smtps,$lists,$custom_emails,$sentdata,$extra_setup)
	{
		$smtps=array_unique(explode(",",$smtps));

		for($i=0;$i<count($smtps);$i++)
		{
			if(!is_numeric($smtps[$i])&& !(trim($smtps[$i])=='php'))
			{
				unset($smtps[$i]);
			}
		}
		$smtps=array_values($smtps);

		if(count($smtps)<1)
		{
			return false;
		}

		$lists=array_unique(explode(",",$lists));

		$list_data=array();
		$list_ob=$this->load->createlist();
		
		$smtp_pointer=0;
		for($i=0;$i<count($lists);$i++)
		{
			if(is_numeric($lists[$i]))
			{
				$get_list_data=$list_ob->getLeadsFromLists($lists[$i]);
				if($get_list_data)
				{
					while($data=$get_list_data->fetch_object())
					{
						//name email exf smtp
						if(!isset($smtps[$smtp_pointer]))
						{
							$smtp_pointer=0;
						}
						array_push($list_data,base64_encode(json_encode(array(
							$data->name,
							$data->email,
							$data->exf,
							$smtps[$smtp_pointer],
							$data->listid
						))));

						++$smtp_pointer;
						
					}
				}
			}
		}

		$custom_emails=array_unique(explode(',',$custom_emails));
		for($i=0;$i<count($custom_emails);$i++)
		{
			if(filter_var($custom_emails[$i],FILTER_VALIDATE_EMAIL))
			{
				if(!isset($smtps[$smtp_pointer]))
				{
					$smtp_pointer=0;
				}

				array_push($list_data,base64_encode(json_encode(array(
					"",
					$custom_emails[$i],
					"{}",
					$smtps[$smtp_pointer],
					0
				))));

				++$smtp_pointer;
			}
		}
		if(count($list_data))
		{
			$in=$this->sequence_ob->createSequenceForCompose($title,"@".implode('@',$lists)."@",implode('@',$smtps),$sentdata,$extra_setup);

			if($in)
			{
				return array("data"=>$list_data,"token"=>base64_encode($in));
			}
			else
			{
				return 0;
			}
		}
		else
		{
			return 0;
		}
	}

	function compose($compose_data,$token)
	{
		$compose_datas=(array)json_decode($compose_data);
		$sent_count=0;
		foreach($compose_datas as $data)
		{
			$data=(array)json_decode(base64_decode($data));
			$exf=(array)$data[2];
			foreach($exf as $index=>$exf_data)
			{
				if(is_object($exf_data))
				{
					$exf[$index]=(array)$exf_data;
				}
			}

			if($this->sequence_ob->composeOrScheduleSubscriptionMail($data[4],$data[1],$data[0],$exf,base64_decode($token),array('smtp'=>$data[3])))
			{
				++$sent_count;
			}
		}
		return $sent_count;
	}
	function getCompose($id)
    {
		$mysqli=$this->mysqli;
		$pref=$this->dbpref;

		$table=$pref."quick_sequence";
		$id=$mysqli->real_escape_string($id);
		$qry=$mysqli->query("select * from `".$table."` where `id`=".$id."");
		if($qry->num_rows>0 && $r=$qry->fetch_object())
		{
			return $r;
		}
		else
		{
			return 0;
		}
	}
}
?>