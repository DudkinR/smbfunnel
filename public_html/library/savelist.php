<?php
class CreateList
{
	var $mysqli;
	var $dbpref;
	var $ip;
	var $load;
	function __construct($arr)
	{
		$this->mysqli = $arr['mysqli'];
		$this->dbpref = $arr['dbpref'];
		$this->ip = $arr['ip'];
		$this->load = $arr['load'];
	}

	function saveList()
	{
		$mysqli = $this->mysqli;
		$pref = $this->dbpref;
		$user_id = $_SESSION['user' . get_option('site_token')];
		$access = $_SESSION['access' . get_option('site_token')];
		if ($access == 'admin') {
			$where_data = " where 1";
			$add_user = " ";
		} else {
			$where_data = " where user_id = '" . $user_id . "'";
			$add_user = " and user_id = '" . $user_id . "'";
		}
		$ipaddress = $this->ip;

		if ($_POST['createlist'] == 1) {
			$listname = $_POST['listname'];
			$date = time();
			$listname = $mysqli->real_escape_string($listname);
			$insertlistname = "insert into `" . $pref . "quick_list_records` (`title`,`creationdate`,`user_id`) VALUES('" . $listname . "','" . $date . "','".$user_id."')";
			$res = $mysqli->query($insertlistname);
			//$idd = $fetcharray['id'];
			if ($res) {
				return $mysqli->insert_id;
			} else {
				return 0;
			}
		} elseif ($_POST['createlist'] == 2) {
			$name = $_POST['username'];
			$email = $_POST['email'];
			$listid = $_POST['listid'];
			$date = time();
			$updateuser = $_POST['updateuser'];
			$pagecount = $_POST['pagecount'];

			$name = $mysqli->real_escape_string($name);
			$email = $mysqli->real_escape_string($email);
			$listid = $mysqli->real_escape_string($listid);
			$ipaddress = $mysqli->real_escape_string($ipaddress);
			$updateuser = $mysqli->real_escape_string($updateuser);
			$pagecount = $mysqli->real_escape_string($pagecount);

			// echo $updateuser;
			if ($updateuser > 0) {
				$checkexist = "select * from `" . $pref . "quick_email_lists` where listid='" . $listid . "' and email='" . $email . "' and `id` not in(" . $updateuser . ") ".$add_user."";
				$selres = $mysqli->query($checkexist);
				$rows = $selres->num_rows;
				// echo $rows;
				if ($rows < 1) {
					$update = "UPDATE `" . $pref . "quick_email_lists` SET name='" . $name . "',email='" . $email . "' where id=" . $updateuser . "";
					$result = $mysqli->query($update);
					return 1;
				} else {
					return 0;
				}
			} else {
				$checkexist = "select * from `" . $pref . "quick_email_lists` where listid='" . $listid . "' and email='" . $email . "' ".$add_user."";
				$selres = $mysqli->query($checkexist);
				$rows = $selres->num_rows;
				// echo $rows;
				if ($rows == 0) {
					//$insertall = "insert into `".$pref."quick_email_lists` (`listid`, `name`, `email`, `exf`, `ipaddr`, `date`) values('".$listid."','".$name."','".$email."','','".$ipaddress."','".$date."')";
					//$result = $mysqli->query($insertall);
					self::addToList($listid, $name, $email, array());
					return 1;
				} else {
					return 0;
				}
			}

			if ($result == 1) {
				$record_per_page = 10;
				$page = '';
				if ($pagecount != 0) {
					$pagecount = $pagecount;
				} else {
					$pagecount = 1;
				}

				$start_from = ($pagecount - 1) * $record_per_page;

				$query = "SELECT * FROM `" . $pref . "quick_email_lists` where listid=" . $listid . " ".$add_user." order by id DESC LIMIT " . $start_from . ", " . $record_per_page . "";
				// echo $query;
				$result = $mysqli->query($query);
				if ($pagecount > 1) {
					$count = ($pagecount * 10) - 10;
				} else {
					$count = 0;
				}
			} else {
				return 0;
			}
		} elseif ($_POST["createlist"] == 3) {
			$nameemail = $_POST['giventext'];
			$listid = $_POST['listid'];


			$query = "SELECT * FROM `" . $pref . "quick_email_lists` where listid=" . $listid . " ".$add_user." and (name like '%" . $nameemail . "%' or email like '%" . $nameemail . "%')";
			// echo $query;
			$result = $mysqli->query($query);
			if ($result->num_rows >= 1) {
				$count = 0;
				while ($row = $result->fetch_assoc()) {

					$count++;

					$disabled = "";
					if (strlen($row['exf']) < 1) {
						$disabled = "disabled";
					}

					$action = "<button style='margin-right:10px;' class='btn unstyled-button' onclick='editRecord(" . $row['id'] . ")'><i class='fas fa-edit text-primary'></i></button><form style='display:inline;' action='' method='post'><button class='btn unstyled-button' name='listdeleterec' value=" . $row['id'] . "><i class='fas fa-trash text-danger'></i></span></button></form>";

					echo "<tr><td>" . $count . "</td><td id='listusername" . $row['id'] . "'>" . $row['name'] . "</td><td id='listuseremail" . $row['id'] . "'>" . $row['email'] . "</td><td>" . $row['ipaddr'] . "</td><td><button class='btn unstyled-button' " . $disabled . " onclick=viewExfData(" . $row['id'] . ")><i class='fas fa-eye'></i></button></td><td>" . date('d-M-Y h:ia', $row['date']) . "</td><td>" . $action . "</td></tr>";
				}
			} else {
				echo "<tr><td colspan=10></td></tr>";
			}
		} elseif ($_POST['createlist'] == 4) {
			//`".$pref."quick_list_records
			$_POST['updated_listname'] = $mysqli->real_escape_string($_POST['updated_listname']);
			$_POST['listid'] = $mysqli->real_escape_string($_POST['listid']);
			$mysqli->query("update `" . $pref . "quick_list_records` set `title`='" . $_POST['updated_listname'] . "' where `id`=" . $_POST['listid'] . "");
			return 1;
		} else {
			return 0;
		}
		//return;
	}
	function getList($id, $all = 0)
	{
		//get list
		$mysqli = $this->mysqli;
		$pref = $this->dbpref;
		$table = $pref . "quick_list_records";
		$id = $mysqli->real_escape_string($id);
		$user_id = $_SESSION['user' . get_option('site_token')];
		$access = $_SESSION['access' . get_option('site_token')];
		if ($access == 'admin') {
			$where_data = " where 1";
			$add_user = " ";
		} else {
			$where_data = " where user_id = '" . $user_id . "'";
			$add_user = " and user_id = '" . $user_id . "'";
		}
		if ($all == 1) {
			$qry = $mysqli->query("select * from `" . $table . "` ".$where_data." order by id desc");
			if ($qry->num_rows > 0) {
				return $qry;
			} else {
				return 0;
			}
		} else {
			$qry = $mysqli->query("select * from `" . $table . "` where  `id`=" . $id . " ".$add_user." limit 1");
			if ($qry->num_rows > 0) {
				return $qry->fetch_object();
			} else {
				return 0;
			}
		}
	}
	function addToList($listid, $name, $email, $extra = array(), $multisequence = 0, $run_sequence = true)
	{
		//add to list
		$total = $this->getTotalCount();
		$site_token_for_dashboard = get_option('site_token');
		$user_id = $_SESSION['user' . get_option('site_token')];
		$access = $_SESSION['access' . get_option('site_token')];
		if($access == 'admin'){
			$where_add = " ";
		}
		else{
			$where_add = " and user_id = '".$user_id."'";
		}

		if ($_SESSION['user_plan_type' . $site_token_for_dashboard] == 2 && $total >= 50) {
			return 1;
		}

		if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
			$mysqli = $this->mysqli;
			$pref = $this->dbpref;
			$table = $pref . "quick_email_lists";
			$sequencedata = $extra;

			$extraarr = array();
			foreach ($extra as $index => $data) {
				if (is_object($data) || is_array($data) || $index == 'name' || $index == 'email') {
					unset($extra[$index]);
				} else {
					$extra[$index] = $mysqli->real_escape_string($data);
				}
			}
			if (is_array($extra)) {
				$extraarr = $extra;
			}

			$qry = $mysqli->query("select `id` from `" . $table . "` where `listid`='" . $listid . "' and `email`='" . $email . "' ".$where_add." ");
			if ($qry->num_rows < 1) {
				$in = "insert into `" . $table . "` (`listid`, `name`, `email`, `exf`, `ipaddr`, `date`,`user_id`) values('" . $listid . "','" . $name . "','" . $email . "','" . json_encode($extraarr) . "','" . $this->ip . "','" . time() . "','".$user_id."')";

				$ins = $mysqli->query($in);
				$ins_id = $mysqli->insert_id;
			}
			if ($run_sequence) {
				if ($multisequence == 1 || $qry->num_rows < 1) {
					$sequence_ob = $this->load->loadSequence();
					$sequence_ob->composeOrScheduleSubscriptionMail($listid, $email, $name, $sequencedata);
				}
			}
			if (isset($ins) && $ins) {
				return $ins_id;
			}
		} else {
			return 0;
		}
	}
	function getTotalCount()
	{
		$mysqli = $this->mysqli;
		$pref = $this->dbpref;
		$user_id = $_SESSION['user' . get_option('site_token')];
		$access = $_SESSION['access' . get_option('site_token')];
		if($access == 'admin'){
			$where_add = " ";
		}
		else{
			$where_add = " where user_id = '".$user_id."'";
		}
		$table = $pref . "quick_email_lists";
		$qry_str = $mysqli->query("SELECT COUNT(*) AS `total` FROM `$table` ".$where_add."");
		$run = $qry_str->fetch_object();
		return $run->total;
	}
	function totalList()
	{
		$mysqli = $this->mysqli;
		$pref = $this->dbpref;
		$user_id = $_SESSION['user' . get_option('site_token')];
		$access = $_SESSION['access' . get_option('site_token')];
		if($access == 'admin'){
			$where_add = " ";
		}
		else{
			$where_add = " where user_id = '".$user_id."'";
		}
		$table = $pref . "quick_list_records";
		$qry_str = $mysqli->query("SELECT COUNT(*) AS `total` FROM `$table` " . $where_add . "");
		$run = $qry_str->fetch_object();
		return $run->total;
	}
	function pluginUpdateListUser($id, $data = array())
	{
		$mysqli = $this->mysqli;
		$pref = $this->dbpref;
		$table = $pref . "quick_email_lists";
		$id = $mysqli->real_escape_string($id);
		if (!is_array($data)) {
			return false;
		}
		if (isset($data['email']) && !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
			unset($data['email']);
		}
		foreach ($data as $index => $val) {
			if ($index == 'exf' && is_array($val)) {
				$exfs = array();
				foreach ($val as $exf_index => $exf_val) {
					if (is_array($exf_val) || is_object($exf_val) || $exf_index == 'name' || $exf_index == 'email') {
						continue;
					}
					$exf_index = $mysqli->real_escape_string($exf_index);
					$exf_val = $mysqli->real_escape_string($exf_val);
					$exfs[$exf_index] = $exf_val;
				}
				$exfs = json_encode($exfs);
				$data['exf'] = $exfs;
			} else {
				$data[$index] = $mysqli->real_escape_string($val);
			}
		}

		if (is_numeric($id) && ($id) > 0) {

			$updates = array();
			foreach ($data as $index => $val) {
				array_push($updates, "`" . $index . "`='" . $val . "'");
			}

			$updates = implode(',', $updates);

			$qry_str = "update `" . $table . "` set " . $updates . " where `id`=" . $id;

			return $mysqli->query($qry_str);
		} else {
			return 0;
		}
	}
	function pluginDeleteSubscriber($id)
	{
		$mysqli = $this->mysqli;
		$pref = $this->dbpref;
		$table = $pref . "quick_email_lists";
		$id = $mysqli->real_escape_string($id);
		return $mysqli->query("delete from `" . $table . "` where `id`='" . $id . "'");
	}
	function pluginGetSubscriber($id, $email = false)
	{
		$mysqli = $this->mysqli;
		$pref = $this->dbpref;
		$table = $pref . "quick_email_lists";
		$user_id = $_SESSION['user' . get_option('site_token')];
		$access = $_SESSION['access' . get_option('site_token')];
		if($access == 'admin'){
			$where_add = " ";
		}
		else{
			$where_add = " and user_id = '".$user_id."'";
		}
		$id = $mysqli->real_escape_string($id);
		if (!$email) {
			$qry = $mysqli->query("select * from `" . $table . "` where `id`=" . $id . " ".$where_add." limit 1");
		} else {
			$qry = $mysqli->query("select * from `" . $table . "` where `listid`='" . $id . "' and `email`='" . $email . "' ".$where_add." ");
		}
		if ($qry->num_rows > 0) {
			$r = $qry->fetch_assoc();
			$r['list_id'] = $r['listid'];
			unset($r['listid']);
			$exf = array();
			$get_exf = json_decode($r['exf']);
			if (is_object($get_exf)) {
				$exf = (array)$get_exf;
			}
			unset($r['exf']);
			$r['additional_data'] = $exf;
			$r['ip'] = $r['ipaddr'];
			unset($r['ipaddr']);
			return $r;
		} else {
			return false;
		}
	}
	function getLeadsFromLists($id)
	{
		$mysqli = $this->mysqli;
		$pref = $this->dbpref;
		$table = $pref . "quick_email_lists";
		$user_id = $_SESSION['user' . get_option('site_token')];
		$access = $_SESSION['access' . get_option('site_token')];
		if($access == 'admin'){
			$where_add = " ";
		}
		else{
			$where_add = " and user_id = '".$user_id."'";
		}

		$id = $mysqli->real_escape_string($id);

		$qry = $mysqli->query("select * from `" . $table . "` where `listid`='" . $id . "' ".$where_add)." ";

		if ($qry->num_rows > 0) {
			return $qry;
		} else {
			return 0;
		}
	}
	function showExtraData($id)
	{
		$mysqli = $this->mysqli;
		$pref = $this->dbpref;
		$table = $pref . "quick_email_lists";
		$user_id = $_SESSION['user' . get_option('site_token')];
		$access = $_SESSION['access' . get_option('site_token')];
		if($access == 'admin'){
			$where_add = " ";
		}
		else{
			$where_add = " and user_id = '".$user_id."'";
		}
		$id = $mysqli->real_escape_string($id);
		$exf = $mysqli->query("select `exf` from `" . $table . "` where `id`='" . $id . "'" . $where_add . " ");

		if ($exf->num_rows > 0) {
			$r = $exf->fetch_object();
			$data = $r->exf;

			if (strlen($data) > 0) {
				$datas = (array)json_decode($data);

				$table = "<div class='table-responsive'><table class='table table-striped'><thead><tr>@head@</tr></thead><tbody><tr>@body@</tr></tbody></table></div>";

				$head = "";
				$body = "";
				foreach ($datas as $index => $data) {
					$head .= "<td>" . $index . "</td>";
					$body .= "<td>" . $data . "</td>";
				}

				$table = str_replace('@head@', $head, $table);
				$table = str_replace('@body@', $body, $table);

				return $table;
			}
		}

		return "";
	}
	function exportToCsv($listid)
	{
		$mysqli = $this->mysqli;
		$pref = $this->dbpref;
		$table = $pref . 'quick_email_lists';
		$user_id = $_SESSION['user' . get_option('site_token')];
		$access = $_SESSION['access' . get_option('site_token')];
		if($access == 'admin'){
			$where_add = " ";
		}
		else{
			$where_add = " and user_id = '".$user_id."'";
		}
		$listid = $mysqli->real_escape_string($listid);

		// Build your query	

		$select = "SELECT * FROM `" . $table . "` WHERE listid=" . $listid . " ".$where_add;

		$MyQuery = $mysqli->query($select);


		// Process report request
		if (!$MyQuery) {
			die("The following error was found");
		} else {
			// Prepare our csv download

			// Set header row values
			$csv_fields = array();
			$csv_fields[] = '#';
			$csv_fields[] = 'List ID';
			$csv_fields[] = 'Name';
			$csv_fields[] = 'Email';
			$csv_fields[] = 'IP Address';
			$csv_fields[] = 'Date';
			$csv_fields[] = 'Extra Fields';


			$output_filename = 'listemails.csv';
			$output_handle = @fopen('php://output', 'w');

			header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
			header('Content-Description: File Transfer');
			header('Content-type: text/csv');
			header('Content-Disposition: attachment; filename=' . $output_filename);
			header('Expires: 0');
			header('Pragma: public');

			// Insert header row
			fputcsv($output_handle, $csv_fields);

			// Parse results to csv format
			while ($Result = $MyQuery->fetch_assoc()) {
				// print_r($Result);
				$exfarray = explode('@jsnbrk@', $Result['exf']);
				$newexfarray = array();

				for ($i = 0; $i < count($exfarray); $i++) {
					// echo $i;
					// print_r($exfarray[$i]);
					$stripstr = strstr($exfarray[$i], '}', true);
					$heading = json_decode($stripstr . "}");
					// print_r($heading);
					if (strlen($stripstr) > 2) {
						$newexfarray[$i] = $stripstr . "}";
					} else {
						$newexfarray['$i'] = "";
					}
				}
				$Result['date'] = date('d-M-Y h:ia', $Result['date']);
				$leadArray = (array) $Result; // Cast the Object to an array
				// Add row to file

				unset($leadArray['exf']);

				$leadArray = array_merge($leadArray, $newexfarray);

				fputcsv($output_handle, $leadArray);
			}

			// Close output file stream
			fclose($output_handle);

			die();
		}
	}
}
