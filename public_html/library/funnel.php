<?php

use Lullabot\AMP\AMP;

class Funnel
{
	var $mysqli;
	var $dbpref;
	var $load;
	var $ip;
	var $base_dir;
	var $template_url;

	var $do_route;
	var $routed_url;
	var $routed_dir;
	public $routed_dir_original;
	public $routed_url_original;
	public $tempdestinimgurl;

	function __construct($arr)
	{
		$this->mysqli = $arr['mysqli'];
		$this->dbpref = $arr['dbpref'];
		$this->load = $arr['load'];
		$this->ip = $arr['ip'];

		$this->do_route = (get_option('qfnl_router_mode') == '1') ? 1 : 0;
		$this->routed_url = "@@qfnl_install_url@@";
		$this->routed_dir = "@@qfnl_install_dir@@";
		$this->routed_url_original =  get_option('install_url') . "/public_funnels";

		if (isset($arr['base_dir'])) {
			$this->base_dir = $arr['base_dir'];
			$this->routed_dir_original = $this->base_dir . "/public_funnels";
		}
		//$this->template_url= "http://resource.Jeetforlife.in/templates/index.php";
		$this->template_url = "https://162.0.238.76/resources/index.php";
		// $this->template_url = "http://cloudfunnels.in/resources/index.php";
		// $this->template_url = "http://localhost/CloudFunnels_Templates/templates.php";
	}
	function createFunnel($url, $name, $type, $modify_index = 0,$cname)
	{
		//create funnel
		global $document_root;
		$site_token_for_dashboard=get_option('site_token');
		$total_funnels = $this->getTotalFunnel();
		if($cname==1)
		{			
		$this->insertSubdomain($name);
		}
			
		
		if($_SESSION['user_plan_type' . $site_token_for_dashboard]==2 && $total_funnels>=1 ) {
			return 0;
		}
		if (filter_var($url, FILTER_VALIDATE_URL)) {
			if ($this->do_route) {
				$dir = $this->routed_dir_original;
				$actualurl = str_replace(get_option('install_url'), $this->routed_url, $url);
				$url = $actualurl;
			} else {
				$dir = $document_root;
				$actualurl = $url;
			}

			if (substr($url, strlen($url) - 1, 1) == "/") {
				$url = rtrim($url, '/');
			}

			$path = str_replace("http://", "", $url);
			$path = str_replace("https://", "", $path);
			$path = str_replace($_SERVER['HTTP_HOST'], "", $path);
			$path = str_replace($this->routed_url, "", $path);

			if ($this->do_route) {
				$routed_path = $this->routed_dir . $path;
			} else {
				$routed_path = 0;
			}
			$path = $dir . $path;

			$mysqli = $this->mysqli;
			$pref = $this->dbpref;
			$table = $pref . "quick_funnels";
			$path_existance = $mysqli->real_escape_string($path);
			$check_existance = $mysqli->query("select `id`,`name` from `" . $table . "` where `flodername`='" . $path_existance . "'");

			if ($check_existance->num_rows < 1) {
				if ($modify_index) {
					if (is_file($path . "/index.html")) {
						unlink($path . "/index.html");
					}
					if (is_file($path . "/index.php")) {
						unlink($path . "/index.php");
					}
					if (cf_dir_exists($path . '/q-fnl-assets')) {
						self::delAllFilesWithDir($dir);
					}
				}
				if (!cf_dir_exists($path) || !is_file($path . "/index.php")) {
					if (self::makeStepPath($path)) {
						if (!cf_dir_exists($path . '/q-fnl-assets')) {
							mkdir($path . '/q-fnl-assets');
							mkdir($path . '/q-fnl-assets/js');
							mkdir($path . '/q-fnl-assets/img');
							mkdir($path . '/q-fnl-assets/css');
						}

						return self::insertFunnelBase($name, $actualurl, $path, $type, $routed_path,$cname);
					} else {
						return "Unable to create Dir";
					}
				} elseif ($path == $document_root && !is_file($path . "/index.php")) {
					if (!cf_dir_exists($path . '/q-fnl-assets')) {
						mkdir($path . '/q-fnl-assets');
						mkdir($path . '/q-fnl-assets/js');
						mkdir($path . '/q-fnl-assets/img');
						mkdir($path . '/q-fnl-assets/css');
						return self::insertFunnelBase($name, $actualurl, $path, $type, $routed_path,$cname);
					} else {
						return "Something is wrong, seems another funnel is already available on the same path";
					}
				} else {
					return "Something is wrong, seems another funnel is already available on the same path";
				}
			} else {
				$r = $check_existance->fetch_object();
				//return "Another Funnel <a href='index.php?page=create_funnel&id=".$r->id."'>".$r->name."</a> Already Created At The Same Location.";

				return t("another funnel ${1}${2}${3} already created at the same location.", array(
					"<a href='index.php?page=create_funnel&id=" . $r->id . "'>",
					$r->name,
					"</a>"
				));
			}
		} else {
			return "Invalid Type URL";
		}
	}
	function getTotalFunnel()
	{
		$mysqli = $this->mysqli;
		$pref = $this->dbpref;
		$table = $pref . "quick_funnels";
		$qry_str = $mysqli->query("SELECT COUNT(*) AS `total` from `".$table."`");
		$run = $qry_str->fetch_object();
		return $run->total;
	}
	function makeStepPath($path)
	{
		//function make path following by slashes
		global $document_root;
		$path = str_replace("\\", '/', $path);
		$root = $document_root;
		$path = explode('/', str_replace($root . "/", "", $path));
		$count = 0;
		for ($i = 0; $i < count($path); $i++) {
			if (strlen($path[$i]) > 0) {
				$root = $root . '/' . $path[$i];
				if (!cf_dir_exists($root)) {
					mkdir($root);
					++$count;
				} else {
					++$count;
				}
			}
		}
		return $count;
	}
	function readPage($funnelid, $label, $type)
	{
		$mysqli = $this->mysqli;
		$pref = $this->dbpref;
		$table = $pref . "quick_pagefunnel";
		$return = 'B4';
		
		$qry = $mysqli->query("select `date_created` from `" . $table . "` where `level`='" . $label . "' and `funnelid`='" . $funnelid . "' and `type`='" . $type . "'");

		if ($qry->num_rows > 0) {
			$data = $qry->fetch_object();
			$pageDate = $data->date_created;
			$compairDate = '23-07-2023';
			$pageDateFormatted = date('d-m-Y', $pageDate);
			$compairDateTimestamp = strtotime($compairDate);
			$compairDateTimestamp = date('d-m-Y', $compairDateTimestamp);
		    $pageDateFormatted = new DateTime($pageDateFormatted);
            $compairDateTimestamp = new DateTime($compairDateTimestamp);

			// Compare dates
			if ($pageDateFormatted < $compairDateTimestamp) {			
				$return = 'B4';
			} elseif ($pageDateFormatted > $compairDateTimestamp) {			    
				$return = 'B5';
			} else {			    
				$return = 'B5';
			}
		}
		return $return;
	}
	function insertSubdomain($name){
		$mysqli = $this->mysqli;
		$pref = $this->dbpref;	
		$trans_install_url =str_replace(["https://","www"],"",$_SERVER['HTTP_HOST']);
		$url=$name.".".$trans_install_url;
		$type = 0;
		$user_id = $_SESSION['user' . get_option('site_token')];
		$sql_insert="INSERT INTO `".$pref."subdomians`(`name`, `url`, `type`, `user_id`) VALUES ('".$name."','".$url."','".$type."','".$user_id."')";
		$mysqli->query($sql_insert); 
	}
	function insertFunnelBase($name, $url, $dir, $type, $routed_path = 0,$cname)
	{
		//echo $cname;
		$plugin_loader = false;
		if (isset($GLOBALS['plugin_loader'])) {
			$plugin_loader = $GLOBALS['plugin_loader'];
		}
		$mysqli = $this->mysqli;
		$pref = $this->dbpref;
		$date = time();
		if ($routed_path === 0) {
			$routed_path = $dir;
		}
		$url = rtrim($url, "/");
		$basedomain = explode("@@",$url);
		$domain=$name.".".$basedomain[1];
		$token = substr(str_shuffle('1234567890188595652'), 0, 10);
		$sql = "INSERT INTO `" . $pref . "quick_funnels` 
		(`name`, `flodername`,`baseurl`, `pagecount`, `firstpage`, `type`,`labelhtml`,`validinputs`,`primarysmtp`,`date_created`,`token`)
		 VALUES 
		 ('" . $name . "','" . $routed_path . "','" . $url . "','0','','" . $type . "','','firstname,lastname,name,email,password,reenterpassword,phone,remember_user,payment_method','phpmailer','" . $date . "','" . $token . "' )";
		$in = $mysqli->query($sql);
		if ($in) {
        if (isset($_SESSION['user' . get_option('site_token')])) {
            $user_id = $_SESSION['user' . get_option('site_token')];
            $funnel_id = $mysqli->insert_id;
        
            // Check if the combination of user_id and funnel_id already exists
            
            $qryn = $mysqli->query("SELECT * FROM `" . $pref . "user_funnel` WHERE `user_id`='" . $user_id . "' AND `funnel_id`='" . $funnel_id . "'"); 
        
            if ($qryn->num_rows > 0) {
                // Combination already exists, perform upgrade (update)
                $sqlf = "UPDATE `" . $pref . "user_funnel` SET ... WHERE `user_id`='" . $user_id . "' AND `funnel_id`='" . $funnel_id . "'";
            } else {
                // Combination doesn't exist, insert new record
                $sqlf = "INSERT INTO `" . $pref . "user_funnel` (`user_id`, `funnel_id`) VALUES ('" . $user_id . "', '" . $funnel_id . "')";
              }
            $inf = $mysqli->query($sqlf);

        }

			////
			$qry = $mysqli->query("select max(`id`) as `id` from `" . $pref . "quick_funnels`");
			if ($r = $qry->fetch_object()) {
				self::createIndex($dir, $r->id);
				if ($plugin_loader) {
					$plugin_loader->processFunnelCreateDelete($r->id, true);
				}
				self::createDefaultFunnelPages($r->id, array(
					'base_dir' => $dir,
					'routed_dir' => ($routed_path) ? $routed_path : $dir,
					'url' => $url,
					'is_routed' => $routed_path
				), $type);
				return $r->id;
			}
		} else {
			return 0;
		}
	}
	function hasFunnelInBase()
	{
		$mysqli = $this->mysqli;
		$pref = $this->dbpref;
		$table = $pref . "quick_funnels";
		$qry = $mysqli->query("select count(`id`) as `countid` from `" . $table . "` where `baseurl`='" . $this->routed_url . "'");
		$count = 0;
		if ($r = $qry->fetch_object()) {
			$count = $r->countid;
		}
		return $count;
	}
	function getFunnel($id, $select = "*", $type = "")
	{
		//get bse funnel detail
		$mysqli = $this->mysqli;
		$pref = $this->dbpref;
		$table = $pref . "quick_funnels";
		$id = $mysqli->real_escape_string($id);
		$type = $mysqli->real_escape_string($type);
		$user_id=$_SESSION['user' . get_option('site_token')]; 
		$access=$_SESSION['access' . get_option('site_token')];
		if($access=='admin')
		{
			if ($id === "-1") {
				$chk_type = "";
				if (strlen($type) > 0) {
					$chk_type = " where `type`='" . $type . "'";
				}
				$qry = $mysqli->query("select " . $select . " from `" . $table . "`" . $chk_type);
			} else {
				$qry = $mysqli->query("select " . $select . " from `" . $table . "` where `id`=" . $id . "");
			}
		}
		else
		{
			if ($id === "-1") {
				$chk_type = "";
				if (strlen($type) > 0) {
					$chk_type = " and `type`='" . $type . "'";
				}
				 $text_sql="select " . $select . " from `" . $table . "` as `a` 
				INNER JOIN `".$pref."user_funnel` as `b` on `a`.`id`=`b`.`funnel_id` 
				where `b`.`user_id`=".$user_id."
				" . $chk_type;
				$qry = $mysqli->query($text_sql);
			} else {
				$qry = $mysqli->query("select " . $select . " from `" . $table . "` where `id`=" . $id . "");
			}
		}
		$r = 0;

		if ($qry) {
			if ($id === "-1") {
				return $qry;
			}
			if ($res = $qry->fetch_object()) {
				$r = $res;
			}
		}
		return $r;
	}
	function fieldAttrChangeForPlugin()
	{
		/*$fields=array(
			'funnelid'=>'funnel_id',
			'filename'=>'file_name',
			'pageheader'=>'page_header',
			'pagefooter'=>'page_footer',
		);*/
	}
	function getPageBYId($id)
	{
		$mysqli = $this->mysqli;
		$pref = $this->dbpref;
		$table = $pref . "quick_pagefunnel";
		$id = $mysqli->real_escape_string($id);
		$qry = $mysqli->query("select * from `" . $table . "` where `id`=" . $id . "");
		if ($qry->num_rows > 0) {
			$r = $qry->fetch_assoc();
			$funnel = self::getFunnel($r['funnelid']);
			if ($funnel) {
				$url = $funnel->baseurl;
				$url = str_replace('@@qfnl_install_dir@@', get_option('install_url'), $url);

				$r['funnel_id'] = $r['funnelid'];
				unset($r['funnelid']);
				$r['url'] = $url . '/' . $r['filename'];
				return $r;
			}
		} else {
			return false;
		}
	}
	function fixPageDetailName(&$r)
	{
		//For plugin only
		if (isset($r['funnelid'])) {
			$r['funnel_id'] = $r['funnelid'];
			unset($r['funnelid']);
		}
		if (isset($r['metadata'])) {
			$data = json_decode($r['metadata']);
			if (is_object($data)) {
				$r['metadata'] = (array)$data;
			} else {
				$r['metadata'] = array();
			}
		}
		if (isset($r['filename'])) {
			$r['file_name'] = $r['filename'];
			unset($r['filename']);
		}
		if (isset($r['pageheader'])) {
			$r['page_header'] = $r['pageheader'];
			unset($r['pageheader']);
		}
		if (isset($r['pagefooter'])) {
			$r['page_footer'] = $r['pagefooter'];
			unset($r['pagefooter']);
		}
		if (isset($r['valid_inputs'])) {
			$r['valid_inputs'] = trim($r['valid_inputs']);

			$r['valid_inputs'] = (strlen($r['valid_inputs']) < 1) ? (array()) : (explode(',', trim($r['valid_inputs'])));
		}
		if (isset($r['settings'])) {
			$data = json_decode($r['settings']);
			$data = (is_object($data)) ? ((array)$data) : array();
			$r['settings'] = $data;
		}
		if (isset($r['content'])) {
			unset($r['content']);
		}
		if (isset($r['viewcount'])) {
			$r['view_count'] = $r['viewcount'];
			unset($r['viewcount']);
		}
		if (isset($r['bounce_count'])) {
			//$r['bounce_count']=$r['bouncecount'];
			unset($r['bouncecount']);
		}
		if (isset($r['convertcount'])) {
			//$r['convert_count']=$r['convertcount'];
			unset($r['convertcount']);
		}
		if (isset($r['hasabtest'])) {
			$r['has_ab_test'] = $r['hasabtest'];
			unset($r['hasabtest']);
		}
		if (isset($r['contentbpage'])) {
			unset($r['contentbpage']);
		}
		if (isset($r['viewcoubtpage'])) {
			unset($r['viewcoubtpage']);
		}
		if (isset($r['convertcountbpage'])) {
			unset($r['convertcountbpage']);
		}
		if (isset($r['bouncecountbpage'])) {
			unset($r['bouncecountbpages']);
		}
		if (isset($r['selares'])) {
			$r['selares'] = trim($r['selares']);
			$r['autoresponders'] = (strlen($r['selares']) < 1) ? (array()) : (explode('@', trim($r['selares'], '@')));
			unset($r['selares']);
		}
		if (isset($r['lists'])) {
			$r['lists'] = trim($r['lists']);
			$r['lists'] = (strlen($r['lists']) < 1) ? (array()) : (explode('@', trim($r['lists'], '@')));
		}
		if (isset($r['membership'])) {
			$r['membership'] = trim($r['membership']);
			$r['membership_registration_pages'] = (strlen($r['membership']) < 1) ? (array()) : (explode(',', trim($r['membership'], ',')));
			unset($r['membership']);
		}
		if (isset($r['token'])) {
			unset($r['token']);
		}
		if (isset($r['varient'])) {
			unset($r['varient']);
		}
		if (isset($r['templateimg'])) {
			unset($r['templateimg']);
		}
		/*if(isset())
		{

		}*/
		return $r;
	}
	function getPagesForFunnel($funnel_id)
	{
		//for plugin only
		$mysqli = $this->mysqli;
		$pref = $this->dbpref;
		$table = $pref . "quick_pagefunnel";

		$id = $mysqli->real_escape_string($funnel_id);

		$funnel = self::getFunnel($funnel_id);
		$arr = array();

		if ($funnel) {
			$url = $funnel->baseurl;
			$url = str_replace('@@qfnl_install_dir@@', get_option('install_url'), $url);
			$qry = $mysqli->query("SELECT * FROM `$table` WHERE `funnelid`='$id' AND `type`='a' ORDER BY `level` ASC");
			if ($qry->num_rows > 0) {
				while ($r = $qry->fetch_assoc()) {
					$r['funnel_id'] = $r['funnelid'];
					unset($r['funnelid']);
					$r['url'] = $url . '/' . $r['filename'];
					self::fixPageDetailName($r);
					array_push($arr, $r);
				}
			}
		}

		return $arr;
	}
	function renameFunnels($posts)
	{
		$mysqli = $this->mysqli;
		$pref = $this->dbpref;
		$table = $pref . "quick_funnels";
		$funnel_id = $mysqli->real_escape_string($posts['funnelid']);
		$funnelname = $mysqli->real_escape_string($posts['funnelname']);
		$dir = $this->getFunnel($funnel_id, '`flodername`');
		$dir = $dir->flodername;
		if ($this->do_route) {
			$dir = str_replace($this->routed_dir, $this->routed_dir_original, $dir);
		}
		$updateddir = $this->routed_dir_original . "/" . $funnelname;

		if (!cf_dir_exists($dir)) {
			die('Page not found, may be no template created.');
		}
		$newfunnelsname = str_ireplace(" ", "-", $funnelname);
		$udpate_dir = "@@qfnl_install_dir@@/" . $newfunnelsname;
		$udpate_url = "@@qfnl_install_url@@/" . $newfunnelsname;
		$is_page_exists = function () use ($mysqli, $table, $funnel_id, $udpate_dir) {
			$qry = $mysqli->query("SELECT `id` FROM `$table` WHERE `id`='$funnel_id' AND `flodername`='$udpate_dir'");
			if ($qry->num_rows > 0) {
				return true;
			}
			return false;
		};
		if ($is_page_exists()) {
			die('Page dir name already in use, try different.');
		}
		$done_rename = rename($dir, $updateddir);
		if ($done_rename) {

			$mysqli->query("UPDATE `$table` SET `name`='$funnelname', `flodername`='$udpate_dir',`baseurl`='$udpate_url' WHERE `id`='$funnel_id'");
		}
		return $done_rename;
	}
	function getPageFunnel($id, $type, $level = 1, $by = 'funnelid')
	{
		//get page funnel detail
		$mysqli = $this->mysqli;
		$pref = $this->dbpref;
		$table = $pref . "quick_pagefunnel";

		$id = $mysqli->real_escape_string($id);
		$level = $mysqli->real_escape_string($level);
		$type = $mysqli->real_escape_string($type);

		if ($by == 'funnelid') {
			$qry = $mysqli->query("select * from `" . $table . "` where `funnelid`='" . $id . "' and `level`='" . $level . "' and `type`='" . $type . "'");
		} else {
			$qry = $mysqli->query("select * from `" . $table . "` where `id`='" . $id . "'");
		}

		$r = 0;
		if ($qry) {
			if ($res = $qry->fetch_object()) {
				$r = $res;
			}
		}
		return $r;
	}
	function getPageHTML($funnelid)
	{
		//get funnel data by folder
		$mysqli = $this->mysqli;
		$pref = $this->dbpref;
		$funnelid = $mysqli->real_escape_string($funnelid);
		$table = $pref . "quick_pagefunnel";
		$qry = $mysqli->query("SELECT `name`,`category`, `filename`,`level`  FROM `$table` WHERE `funnelid`=$funnelid AND `type`='a'  ORDER BY `level`");
		$data = [];
		if ($qry->num_rows > 0) {
			while ($r = $qry->fetch_object()) {
				if (empty($r->title)) {
					$r->title = ucwords(str_replace("-", " ", $r->filename));
				}
				$data[] = $r;
			}
			return $data;
		}
		return false;
	}
	function getPageFunnelDataByFolder($funnelid, $folder, $ab, $sel = "*")
	{
		//get funnel data by folder
		$mysqli = $this->mysqli;
		$pref = $this->dbpref;
		$funnelid = $mysqli->real_escape_string($funnelid);
		$folder = $mysqli->real_escape_string($folder);
		$table = $pref . "quick_pagefunnel";
		$qry = $mysqli->query("select " . $sel . " from `" . $table . "` where funnelid=" . $funnelid . " and filename='" . $folder . "' and type='" . $ab . "'");
		if ($qry) {
			if ($r = $qry->fetch_object()) {
				return $r;
			} else {
				return 0;
			}
		} else {
			return 0;
		}
	}
	function getCountFunnelsInDB()
	{
		$mysqli = $this->mysqli;
		$pref = $this->dbpref;
		$table = $pref . "quick_pagefunnel";
		$qry = $mysqli->query("select count(`id`) as `count_id` from `" . $table . "`");
		return $qry->count_id;
	}
function getAllFunnelForView($from = 0, $type = "", $limit = null)
	{ 
		//get all funnel for view
		if ($limit === null || !is_numeric($limit)) {
			$limit = (int)get_option('qfnl_max_records_per_page');
		}
		$mysqli = $this->mysqli;
		$pref = $this->dbpref;
		$table = $pref . "quick_funnels";
		$page_table = $pref . "quick_pagefunnel";

		$from = $mysqli->real_escape_string($from);

		$arr = array('rows' => 0, 'total_rows' => 0);

		$totalrows = 0;

		$date_search_for_total = dateBetween('date_created');
		$user_id=$_SESSION['user' . get_option('site_token')];
			
		if($this->funnel_access_user($user_id))
		{
		echo "Admin<br>";
		if (strlen($type) > 2) {
			$sql_text="select count(`id`) as rowcount from `" . $table . "` where `type`='" . $type . "'" . $date_search_for_total[1];
			$totalrows_qry = $mysqli->query($sql_text);
		} else {
			if (strlen($date_search_for_total[0]) > 0) {
				$date_search_for_total[0] = " where" . $date_search_for_total[0];
			}
			$sql_text="select count(`id`) as rowcount from `" . $table . "`" . $date_search_for_total[0];
			$totalrows_qry = $mysqli->query($sql_text);
		}

		if ($totalrows_qry) {
			if ($r = $totalrows_qry->fetch_object()) {
				$totalrows = $r->rowcount;
			}
		}

		if ($from > 0) {
			$from = ($from * $limit) - $limit;
		}

		$ab_viewcount = "(select sum(`" . $page_table . "`.viewcount) from `" . $page_table . "` where `" . $page_table . "`.funnelid=`funnel_id`)";

		$ab_convertcount = "(select sum(`" . $page_table . "`.convertcount) from `" . $page_table . "` where `" . $page_table . "`.funnelid=`funnel_id`)";

		$ab_sumpages = "(select count(`" . $page_table . "`.id) from `" . $page_table . "` where `" . $page_table . "`.funnelid=`funnel_id` and `" . $page_table . "`.type='a')";

		$membership_countquery = "";
		if (isset($_GET['page']) && ($_GET['page'] = "membership_funnels")) {
			$membership_countquery = ",(select count(distinct(`email`)) from `" . $pref . "quick_member` where `funnelid`=`funnel_id` and `email` not in('',' ')) as `count_members`";
		}

		if (isset($_POST['onpage_search']) && strlen($_POST['onpage_search']) > 0) {
			$search_content = $mysqli->real_escape_string($_POST['onpage_search']);
			$searchinsidetype = "";
			if (strlen($type) > 0) {
				$searchinsidetype = " and `b`.type='" . $type . "'";
			}
			//`b`.type='".$type."' and
			$sql_text="select `a`.*,`b`.id as `funnel_id`,`b`.token as `funnel_token`,`b`.name as `funnel_name`, `b`.baseurl as `funnel_baseurl`,`b`.date_created as `funnelcreatedon`,`b`.type as `funnel_type`," . $ab_viewcount . " as `ab_viewcount`," . $ab_convertcount . " as `ab_convertcount`,(" . $ab_viewcount . "-" . $ab_convertcount . ") as `ab_bouncecount`," . $ab_sumpages . " as `sumpages`" . $membership_countquery . " from `" . $page_table . "` as `a` right join `" . $table . "` as `b` on `a`.funnelid=`b`.id where ((`a`.type='a' and `a`.level>0) or `a`.level is NULL)" . $searchinsidetype . " and (`b`.name like '%" . $search_content . "%' or `b`.flodername like '%" . $search_content . "%' or `b`.type like '%" . $search_content . "%' or `b`.baseurl like '%" . $search_content . "%' or `a`.name like '%" . $search_content . "%' or `a`.title like '%" . $search_content . "%' or `a`.filename like '%" . $search_content . "%' or `a`.category like '%" . $search_content . "%') and `a`.id in(select max(`id`) from `" . $page_table . "` where `type`='a' and `funnelid`=`b`.id) order by `b`.id desc";
			$qry = $mysqli->query($sql_text);

		} else {
			$date_search = dateBetween('date_created', 'b');
			$order_by = "`b`.id desc";

			if (isset($_GET['arrange_records_order'])) {
				$order_by = base64_decode($_GET['arrange_records_order']);
			}


			if (strlen($type) > 2) {
				$sql_text = "select `a`.*,`b`.id as `funnel_id`,`b`.token as `funnel_token`,`b`.name as `funnel_name`, `b`.baseurl as `funnel_baseurl`,`b`.date_created as `funnelcreatedon`,`b`.type as `funnel_type`," . $ab_viewcount . " as `ab_viewcount`," . $ab_convertcount . " as `ab_convertcount`,(" . $ab_viewcount . "-" . $ab_convertcount . ") as `ab_bouncecount`," . $ab_sumpages . " as `sumpages`" . $membership_countquery . " from `" . $page_table . "` as `a` right join `" . $table . "` as `b` on `a`.funnelid=`b`.id where `b`.type='" . $type . "' and ((`a`.id in (select max(`id`) from `" . $page_table . "` where `type`='a' and `funnelid`=`b`.id) " . $date_search[1] . ") or `a`.level is NULL)" . $date_search[1] . " order by " . $order_by . " limit " . $from . "," . $limit . "";
				$qry = $mysqli->query($sql_text);
			} else {
				$sql_text= "select a.*,`b`.id as `funnel_id`,`b`.token as `funnel_token`,`b`.name as `funnel_name`, `b`.baseurl as `funnel_baseurl`,`b`.date_created as `funnelcreatedon`,`b`.type as `funnel_type`," . $ab_viewcount . " as `ab_viewcount`," . $ab_convertcount . " as `ab_convertcount`,(" . $ab_viewcount . "-" . $ab_convertcount . ") as `ab_bouncecount`," . $ab_sumpages . " as `sumpages`" . $membership_countquery . " from `" . $page_table . "` as `a` right join `" . $table . "` as `b` on `a`.funnelid=`b`.id where (`a`.id in (select max(`id`) from `" . $page_table . "` where `type`='a' and `funnelid`=`b`.id) " . $date_search[1] . ") or `a`.level is NULL" . $date_search[1] . " order by " . $order_by . " limit " . $from . "," . $limit . "";
				$qry = $mysqli->query($sql_text);
			}
		}
		}
		else
		{
			echo " User <br>";
				if (strlen($type) > 2) {
			 $sql_text="SELECT COUNT(DISTINCT qf.id) AS rowcount
			FROM `" . $table . "` AS qf
			INNER JOIN smbf_user_funnel AS uf ON qf.id = uf.funnel_id AND uf.user_id =  '".$user_id."' AND  `type`='" . $type . "'" . $date_search_for_total[1];
		
			$totalrows_qry = $mysqli->query($sql_text);
		} else {
			if (strlen($date_search_for_total[0]) > 0) {
				$date_search_for_total[0] = " where" . $date_search_for_total[0];
			}
			$sql_text=$sql_text="SELECT COUNT(DISTINCT qf.id) AS rowcount
FROM `" . $table . "` AS qf
INNER JOIN smbf_user_funnel AS uf ON qf.id = uf.funnel_id AND uf.user_id = '".$user_id."'" . $date_search_for_total[0];
			$totalrows_qry = $mysqli->query($sql_text);
		}

		if ($totalrows_qry) {
			if ($r = $totalrows_qry->fetch_object()) {
				$totalrows = $r->rowcount;
			}
		}

		if ($from > 0) {
			$from = ($from * $limit) - $limit;
		}

		$ab_viewcount = "(select sum(`" . $page_table . "`.viewcount) from `" . $page_table . "` where `" . $page_table . "`.funnelid=`funnel_id`)";

		$ab_convertcount = "(select sum(`" . $page_table . "`.convertcount) from `" . $page_table . "` where `" . $page_table . "`.funnelid=`funnel_id`)";

		$ab_sumpages = "(select count(`" . $page_table . "`.id) from `" . $page_table . "` where `" . $page_table . "`.funnelid=`funnel_id` and `" . $page_table . "`.type='a')";

		$membership_countquery = "";
		if (isset($_GET['page']) && ($_GET['page'] = "membership_funnels")) {
			$membership_countquery = ",(select count(distinct(`email`)) from `" . $pref . "quick_member` where `funnelid`=`funnel_id` and `email` not in('',' ')) as `count_members`";
		}

		if (isset($_POST['onpage_search']) && strlen($_POST['onpage_search']) > 0) {
			$search_content = $mysqli->real_escape_string($_POST['onpage_search']);
			$searchinsidetype = "";
			if (strlen($type) > 0) {
				$searchinsidetype = " and `b`.type='" . $type . "'";
			}
			//`b`.type='".$type."' and
			$sql_text = "SELECT `a`.*, 
                `b`.id AS `funnel_id`,
                `b`.token AS `funnel_token`,
                `b`.name AS `funnel_name`,
                `b`.baseurl AS `funnel_baseurl`,
                `b`.date_created AS `funnelcreatedon`,
                `b`.type AS `funnel_type`,
                " . $ab_viewcount . " AS `ab_viewcount`,
                " . $ab_convertcount . " AS `ab_convertcount`,
                (" . $ab_viewcount . "-" . $ab_convertcount . ") AS `ab_bouncecount`,
                " . $ab_sumpages . " AS `sumpages`" . $membership_countquery . " 
             FROM `" . $page_table . "` AS `a` 
             RIGHT JOIN `" . $table . "` AS `b` 
             ON `a`.funnelid = `b`.id 
             LEFT JOIN `".$pref."user_funnel` AS `uf` 
             ON `b`.id = `uf`.funnel_id 
             WHERE ((`a`.type='a' AND `a`.level>0) OR `a`.level IS NULL)" . $searchinsidetype . " 
             AND (`b`.name LIKE '%" . $search_content . "%' OR `b`.flodername LIKE '%" . $search_content . "%' OR `b`.type LIKE '%" . $search_content . "%' OR `b`.baseurl LIKE '%" . $search_content . "%' OR `a`.name LIKE '%" . $search_content . "%' OR `a`.title LIKE '%" . $search_content . "%' OR `a`.filename LIKE '%" . $search_content . "%' OR `a`.category LIKE '%" . $search_content . "%') 
             AND `a`.id IN (SELECT MAX(`id`) 
                           FROM `" . $page_table . "` 
                           WHERE `type`='a' AND `funnelid`=`b`.id) 
             AND (`uf`.user_id = ".$user_id.")
             ORDER BY `b`.id DESC";
	$qry = $mysqli->query($sql_text);

		} else {
			$date_search = dateBetween('date_created', 'b');
			$order_by = "`b`.id desc";

			if (isset($_GET['arrange_records_order'])) {
				$order_by = base64_decode($_GET['arrange_records_order']);
			}


			if (strlen($type) > 2) {
			$sql_text = "SELECT `a`.*, 
                `b`.id AS `funnel_id`,
                `b`.token AS `funnel_token`,
                `b`.name AS `funnel_name`,
                `b`.baseurl AS `funnel_baseurl`,
                `b`.date_created AS `funnelcreatedon`,
                `b`.type AS `funnel_type`,
                " . $ab_viewcount . " AS `ab_viewcount`,
                " . $ab_convertcount . " AS `ab_convertcount`,
                (" . $ab_viewcount . "-" . $ab_convertcount . ") AS `ab_bouncecount`,
                " . $ab_sumpages . " AS `sumpages`" . $membership_countquery . " 
             FROM `" . $page_table . "` AS `a` 
             RIGHT JOIN `" . $table . "` AS `b` 
             ON `a`.funnelid = `b`.id 
             LEFT JOIN `".$pref."user_funnel` AS `uf` 
             ON `b`.id = `uf`.funnel_id 
             WHERE (`b`.type = '" . $type . "' AND (`a`.id IN (SELECT MAX(`id`) 
                                                           FROM `" . $page_table . "` 
                                                           WHERE `type`='a' AND `funnelid`=`b`.id) " . $date_search[1] . ") 
             OR `a`.level IS NULL" . $date_search[1] . ") 
             AND (`uf`.user_id = ".$user_id.")
             ORDER BY " . $order_by . " 
             LIMIT " . $from . "," . $limit . "";
	$qry = $mysqli->query($sql_text);
			} else {
			 $sql_text = "SELECT a.*,
                `b`.id AS `funnel_id`,
                `b`.token AS `funnel_token`,
                `b`.name AS `funnel_name`,
                `b`.baseurl AS `funnel_baseurl`,
                `b`.date_created AS `funnelcreatedon`,
                `b`.type AS `funnel_type`,
                " . $ab_viewcount . " AS `ab_viewcount`,
                " . $ab_convertcount . " AS `ab_convertcount`,
                (" . $ab_viewcount . "-" . $ab_convertcount . ") AS `ab_bouncecount`,
                " . $ab_sumpages . " AS `sumpages`" . $membership_countquery . " 			 
             FROM `" . $page_table . "` AS `a`
             RIGHT JOIN `" . $table . "` AS `b` 
             ON `a`.funnelid = `b`.id 
             LEFT JOIN `".$pref."user_funnel` AS `uf` 
             ON `b`.id = `uf`.funnel_id 
             WHERE ((`a`.id IN (SELECT MAX(`id`) 
                              FROM `" . $page_table . "` 
                              WHERE `type`='a' AND `funnelid`=`b`.id) " . $date_search[1] . ") 
             OR `a`.level IS NULL" . $date_search[1] . ") 
             AND (`uf`.user_id = ".$user_id.")
             ORDER BY " . $order_by . " 
             LIMIT " . $from . "," . $limit . "";

             $qry = $mysqli->query($sql_text);
			}
		}
		}

		$arr['rows'] = $qry;
		$arr['total_rows'] =	$totalrows;
	//	print_r($arr);
		return $arr;
	}
	function  funnel_access_user($user_id){
		$mysqli = $this->mysqli;
		$pref = $this->dbpref;
		$table = $pref . "user_funnel";
		$admin_type_table = $pref."administrator";
		// find user_id in administator table
	 	$sql_text="SELECT * FROM `$admin_type_table` WHERE `user_id`='$user_id'";
		$qry_type = $mysqli->query($sql_text);
		if($qry_type->num_rows >0){
			while ($r = $qry_type->fetch_object()) {
				if($r->type=='admin')
				{
					$_SESSION['access' . get_option('site_token')]='admin';
					return true;
				}
			}
		}
		else{
			$_SESSION['access' . get_option('site_token')]='user';
			return false;
			
		}
	}
	function totalSumCountsFunelPages($funnelid)
	{
		//sum counts
		$mysqli = $this->mysqli;
		$pref = $this->dbpref;
		$table = $pref . "quick_funnels";
		$page_table = $pref . "quick_pagefunnel";

		$qry = $mysqli->query("select (select count(`id`) from `" . $page_table . "` where funnelid='" . $funnelid . "' and type='a') as sumpages,sum(`viewcount`) as sumvisit,sum(`convertcount`) as sumconvertcount,sum(`bouncecount`) as sumbounce from `" . $page_table . "` where funnelid='" . $funnelid . "'");

		if ($qry) {
			if ($r = $qry->fetch_object()) {
				return $r;
			}
		}
	}
	function totalSumCountsFunel()
	{
		//sum counts
		$mysqli = $this->mysqli;
		$pref = $this->dbpref;
		$table = $pref . "quick_funnels";
		$qry = $mysqli->query("SELECT COUNT(*) AS 'total' FROM `$table`");
		$r = $qry->fetch_object();
		return $r->total;
	}
	function countIntegratedInOthers($funnel_id)
	{
		//count howmuch time used in another funnel
		$mysqli = $this->mysqli;
		$pref = $this->dbpref;
		$page_table = $pref . "quick_pagefunnel";
		$id = $funnel_id;
		$qry = $mysqli->query("select count(distinct(`funnelid`)) as `countid` from `" . $page_table . "` where `membership` like '" . $id . ",' or `membership` like '%," . $id . ",%' or `membership` like '%," . $id . "'");
		$count = 0;
		if ($r = $qry->fetch_object()) {
			$count = $r->countid;
		}
		return $count;
	}
	function deleteFunnel($id)
	{
		//delete funnel
		$plugin_loader = false;
		if (isset($GLOBALS['plugin_loader'])) {
			$plugin_loader = $GLOBALS['plugin_loader'];
		}
		global $document_root;
		$mysqli = $this->mysqli;
		$pref = $this->dbpref;
		$table = $pref . "quick_funnels";
		$page_table = $pref . "quick_pagefunnel";
		$id = $mysqli->real_escape_string($id);
		$data = self::getFunnel($id, '`id`,`flodername`');

		if ($data) {
			$folder = $data->flodername;
			if ($this->do_route) {
				$base_dir = $this->routed_dir_original;
				$folder = str_replace($this->routed_dir, $this->routed_dir_original, $folder);
			} else {
				$base_dir = str_replace('\\', '/', $document_root);
			}


			$base_query = $mysqli->query("select `filename` from `" . $page_table . "` where `funnelid`='" . $id . "'");

			while ($r = $base_query->fetch_object()) {
				self::delAllFilesWithDir($folder . "/" . $r->filename);
			}
			$index = $folder . "/index.php";
			if (cf_dir_exists($folder . "/q-fnl-assets")) {
				self::delAllFilesWithDir($folder . "/q-fnl-assets");
			}

			if (is_file($index) && filesize($index) > 0) {
				//cloud_funnels_no_conlict_index

				if ($this->do_route) {
					unlink($index);
				} else {
					$fp = fopen($index, 'r');
					$index_data = fread($fp, filesize($index));
					fclose($fp);

					if (strpos($index_data, "cloud_funnels_no_conlict_index") > 0) {
						if ($base_dir == $folder) {
							//$fpp=fopen($index,"w");
							cf_fwrite($index, "<h1>Page Not Found</h1>");
							//fclose($fpp);
						} else {
							$checkdirstat = scandir($folder);
							unlink($index);
							if (count($checkdirstat) === 3 && $checkdirstat[2] == "index.php") {
								rmdir($folder);
							}
						}
					}
				}
			}

			$mysqli->query('delete from `' . $page_table . '` where funnelid="' . $data->id . '"');
			$mysqli->query('delete from `' . $table . '` where `id`=' . $data->id . '');
			$mysqli->query('delete from `' . $pref . 'user_funnel` where `funnel_id`=' . $data->id . '');
			if ($plugin_loader) {
				$plugin_loader->processFunnelCreateDelete($data->id, false);
			}
		}
	}
	function delAllFilesWithDir($dir)
	{
		if (cf_dir_exists($dir)) {
			$objects = scandir($dir);
			foreach ($objects as $object) {
				if ($object != "." && $object != "..") {
					if (!is_file($dir . "/" . $object)) {
						self::delAllFilesWithDir($dir . "/" . $object);
					} else {
						unlink($dir . "/" . $object);
					}
				}
			}
			reset($objects);
			rmdir($dir);
		}
	}
	function initiateFunnelCloner($funnel, $new_category)
	{
		$mysqli = $this->mysqli;
		$pref = $this->dbpref;
		$table = $pref . "quick_funnels";
		$page_table = $pref . "quick_pagefunnel";
		$funnel = $mysqli->real_escape_string($funnel);
		$new_category = $mysqli->real_escape_string($new_category);

		$mysqli->query("update `" . $table . "` set `labelhtml`='' where `id`=" . $funnel . "");
		$mysqli->query("delete from `" . $page_table . "` where `funnelid`='" . $funnel . "'");
		$mysqli->query("update `" . $table . "` set `type`='" . $new_category . "' where `id`=" . $funnel . "");
		return 1;
	}
	function saveEditorData($funnel_id, $type, $lavel, $category, $data, $page = "index", $page_name = 'index', $screenshot = 1)
	{
		//save editors data
		//$screenshot=1 screenshot 0 no screenshot 2 only screenshot
		$plugin_loader = false;
		if (isset($GLOBALS['plugin_loader'])) {
			$plugin_loader = $GLOBALS['plugin_loader'];
		}
		$mysqli = $this->mysqli;
		$pref = $this->dbpref;

		$funnel_id = $mysqli->real_escape_string($funnel_id);
		$type = $mysqli->real_escape_string($type);
		$lavel = $mysqli->real_escape_string($lavel);
		$category = $mysqli->real_escape_string($category);
		$page = $mysqli->real_escape_string($page);
		$page_name = $mysqli->real_escape_string($page_name);

		$funneldata = self::getFunnel($funnel_id);
		//print_r($funneldata);
		if ($funneldata) {
			$table = $pref . "quick_pagefunnel";
			$chk_folder_existance = $mysqli->query("select `id` from `" . $table . "` where `funnelid`='" . $funnel_id . "' and `filename`='" . $page . "' and `level` not in(" . $lavel . ")");

			if ($chk_folder_existance->num_rows > 0) {
				die('Something wrong with the directory, please refresh and try again.');
			}

			$hasdata = self::getPageFunnel($funnel_id, $type, $lavel);
			if (!$hasdata) {
				$token = substr(str_shuffle('qwertyuiopasdfghjklzxcvbnm12346790'), 0, 5);
				$token .= time();
				$validinpututespage = $funneldata->validinputs;

				$metadata = json_encode(array('description' => '', 'icon' => '', 'keywords' => '', 'robots' => '', 'copyright' => '', 'DC_title' => ''));

				$settings = json_encode(array('cookie_notice' => 0, 'redirect_for_post' => 0, 'redirect_for_post_url' => '', 'snippet_integrations' => array(), 'page_cache' => 0, 'zapier_enable' => false, 'pabbly_enable' => false, 'active_amp' => false));

				$template_img = self::websiteToImgInit($page, $type);
				$template_img = $mysqli->real_escape_string($template_img);

				$pageinsert = $mysqli->query("INSERT INTO `" . $pref . "quick_pagefunnel`(`funnelid`, `name`, `title`,`metadata`, `filename`, `pageheader`, `pagefooter`, `type`, `level`,`category`,`custom`, `content`, `viewcount`, `bouncecount`, `convertcount`, `hasabtest`, `contentbpage`, `viewcoubtpage`, `convertcountbpage`, `bouncecountbpage`, `downsellpage`, `upsellpage`,`selares`,`lists`,`product`,`membership`,`paymentmethod`,`templateimg`,`valid_inputs`,`settings`,`date_created`, `token`,`varient`) VALUES ('$funnel_id','$page_name','$page_name','$metadata','$page','','','$type',' $lavel','$category','0','','0','0','0','0','','0','0','0','0','0','','0','','0','0','$template_img','$validinpututespage','$settings','" . time() . "','$token','$token')");

				$pageid = $mysqli->insert_id;

				$plugin_loader->processPageCreateDelete(array(
					'page_id' => $pageid,
					'funnel_id' => $funnel_id,
					'page_level' => $lavel,
					'ab_type' => $type
				), true);
			} else {
				$page = $hasdata->filename;
				$pageid = $hasdata->id;
			}

			if (in_array($category, array("register", "login", "membership", "forgotpassword"))) {
				$membership = $this->load->loadMember();
				$membership->setMembershipPage($pageid, $category);
			}

			$funnel_folder_name = $funneldata->flodername;
			if ($this->do_route) {
				$funnel_folder_name = str_replace($this->routed_dir, $this->routed_dir_original, $funnel_folder_name);

				if (is_array($data)) {
					foreach ($data as $data_index => $data_value) {
						$data[$data_index] = self::pageRouteEncode($data_value, $funnel_folder_name, $page);

						$data[$data_index] = str_replace($this->routed_dir_original, $this->routed_dir, $data_value);
					}
				}
			}
			if ($screenshot !== 2) {
				$varient_qry = $mysqli->query("select `varient` from `" . $pref . "quick_pagefunnel` where `funnelid`=" . $funnel_id . " and `filename`='" . $page . "' and `type`='" . $type . "'");

				if ($varient_qry->num_rows > 0) {
					$varient_r = $varient_qry->fetch_object();
					$varient = $varient_r->varient;

					$created = self::createTemplate($varient, $funnel_folder_name, $data['html'], $data['css'], $data['js'], $type, $funnel_id, $page);

					$plugin_loader->processPageContentChange($funnel_id, $pageid, $lavel, $type);
				}
			}
			if (in_array($screenshot, array(1, 2))) {
				$template_img = self::websiteToImgInit($page, $type);
				$template_img = $mysqli->real_escape_string($template_img);

				$mysqli->query("update `" . $pref . "quick_pagefunnel` set `templateimg`='" . $template_img . "' where `id`=" . $pageid . "");
			}
			if (isset($data['fontlink'])) {
				$fontlink = $data['fontlink'];
				$font_arr = [];

				$csslinks = $mysqli->query("select `csslink` from `" . $pref . "quick_pagefunnel`  WHERE `id`=" . $pageid . "");

				if ($csslinks->num_rows > 0) {
					$links_ar = $csslinks->fetch_object();
					$links_arr = $links_ar->csslink;

					if (!empty($links_arr)) {
						$decode_link = json_decode($links_arr);
						if (is_array($decode_link) &&  !in_array($fontlink, $decode_link)) {
							array_push($decode_link, $fontlink);
							$font_ar = $mysqli->real_escape_string(json_encode($decode_link));
						} else {
							$font_ar = $mysqli->real_escape_string(json_encode($decode_link));
						}
					} else {
						array_push($font_arr, $fontlink);
						$font_ar = $mysqli->real_escape_string(json_encode($font_arr));
					}
				}
				$mysqli->query("UPDATE `" . $pref . "quick_pagefunnel` SET `csslink`='" . $font_ar . "' WHERE `id`=" . $pageid . "");
			}
		} else {
			echo 0;
		}
	}
	function copyDir($from, $to)
	{
		$from = rtrim(str_replace("\\", "/", $from), "/");
		$to = rtrim(str_replace("\\", "/", $to), "/");

		if (cf_dir_exists($from)) {
			if (!cf_dir_exists($to)) {
				mkdir($to);
			}

			$from_open_ref = opendir($from);
			$to_open_ref = opendir($to);

			$files = scandir($from);

			foreach ($files as $file) {
				if ($file === '.' || $file === '..') {
					continue;
				}
				$from_file =	$from . '/' . $file;
				$to_file = $to . '/' . $file;

				if (is_dir($from_file)) {
					self::copyDir($from_file, $to_file);
				} else if (is_file($from_file)) {
					copy($from_file, $to_file);
				}
			}

			closedir($from_open_ref);
			closedir($to_open_ref);
		}
		return true;
	}
	function copyPageDir($from, $to, $temp)
	{
		$from = rtrim(str_replace("\\", "/", $from), "/");
		$to = rtrim(str_replace("\\", "/", $to), "/");

		if (cf_dir_exists($from)) {
			if (!cf_dir_exists($to)) {
				mkdir($to);
			}

			$from_open_ref = opendir($from);
			$to_open_ref = opendir($to);

			$files = scandir($from);

			foreach ($files as $file) {
				if ($file === '.' || $file === '..') {
					continue;
				}
				$from_file =	$from . '/' . $file;
				$ext = pathinfo($file, PATHINFO_EXTENSION);
				if (in_array(strtolower($ext), ['js', 'css', 'html'])) {
					$expl = explode(".", $file);
					$to_file = $to . '/' . $expl[0] . "." . $temp . "." . $ext;
				} else {
					$to_file = $to . '/' . $file;
				}

				if (is_dir($from_file)) {
					self::copyPageDir($from_file, $to_file, $temp);
				} else if (is_file($from_file)) {
					copy($from_file, $to_file);
				}
			}

			closedir($from_open_ref);
			closedir($to_open_ref);
		}
		return true;
	}
	function createDefaultFunnelPages($funnel_id, $funnel_detail, $funnels_type = 'sales')
	{
		global $document_root;
		$mysqli = $this->mysqli;
		$dbpref = $this->dbpref;

		$funnel_id = $mysqli->real_escape_string($funnel_id);

		$read_file = function ($file) {
			$data = "";
			if (file_exists($file)) {
				$size = filesize($file);
				if ($size > 0) {
					$fp = fopen($file, 'r');
					$data = fread($fp, $size);
					fclose($fp);
				}
			}
			return $data;
		};

		$template_control_button = function ($level, $page_data) use ($funnel_id, $funnel_detail, $document_root, $read_file) {
			// $page_data array('category'=>'sales', 'name'=>'Website home', 'url_slug'=>'home')
			// 👉 $funnel_detail array('base_dir'=> $dir,'routed_dir'=> ($routed_path)? $routed_path : $dir,'url'=> $url, 'is_routed'=> $routed_path)

			$funnel_base_dir = $funnel_detail['base_dir'];
			$funnel_routed_dir = $funnel_detail['routed_dir'];
			$funnel_url = $funnel_detail['url'];
			$funnel_is_routed = $funnel_detail['is_routed'];

			$page_name = $page_data['name'];
			$page_category = $page_data['category'];
			$page_url_slug = $page_data['url_slug'];

			$default_funnels_root = $document_root . '/library/funnel_dependencies/default_templates/' . $page_category;
			if (!cf_dir_exists($default_funnels_root)) {
				return "";
			}

			$page_url_full = $funnel_url . "/" . $page_url_slug;
			$html_page_lvl = '<div class="d-flex align-items-center mnubtn-cont"  v-on:mousedown="activeMenuBtnN(\$event)" v-on:contextmenu="showContext(true, \$event)" lbl="' . $level . '" category="' . $page_category . '" ready="1"><button  class="btn  btn-block mnubtn-n" style="opacity: 1 !important">' . $page_name . '</button><div class="d-flex mt-2"> <a href="' . $page_url_full . '" target="_blank"><i title="Visit Page" class="fas fa-external-link-alt"></i></a> </div></div>';
			$html_page_lvl = trim($html_page_lvl);

			$html = $read_file($default_funnels_root . '/index.html');
			$css = $read_file($default_funnels_root . '/@cf_asset/css/style.css');
			$js = "";

			$html = str_replace("@cf_asset/img", "@cf_asset/img/img-a", $html);
			$css = str_replace("@cf_asset/img", "@cf_asset/img/img-a", $css);
			$css = str_replace("../img", "@cf_asset/img/img-a", $css);

			$html = str_replace("@cf_asset", "@@qfnl_install_url@@/@@folder@@asset", $html);
			$css = str_replace("@cf_asset", "@@qfnl_install_url@@/@@folder@@asset", $css);

			$arr = array('html' => $html, 'css' => $css, 'js' => $js);

			ob_start();
			self::saveEditorData($funnel_id, 'a', $level, $page_category, $arr, $page_url_slug, $page_name);

			$page_save_stat = ob_end_clean();
			$settings_save_stat = self::updatePageFunnelSettings($funnel_id, $level, json_encode(array('page_folder' => $page_url_slug)), 1);

			if ($page_save_stat) {
				//copy images
				$form_img_dir = $default_funnels_root . '/@cf_asset/img';
				$to_img_dir = $funnel_base_dir . '/' . $page_url_slug . '/asset/img/img-a';
				self::copyDir($form_img_dir, $to_img_dir);
				return $html_page_lvl;
			} else {
				return "";
			}
		};
		if ($funnels_type == "membership") {
			$delault_pages = array(
				array('category' => 'register', 'name' => 'Registration page', 'url_slug' => 'registration-page'),
				array('category' => 'login', 'name' => 'Login Page', 'url_slug' => 'login-page'),
				array('category' => 'membership', 'name' => 'Membership Page', 'url_slug' => 'membership-page'),
				array('category' => 'forgotpassword', 'name' => 'Forgot Password Page', 'url_slug' => 'forgot-password-page'),
			);
		} elseif ($funnels_type == "webinar") {
			$delault_pages = array(
				array('category' => 'optin', 'name' => 'Optin', 'url_slug' => 'optin'),
				array('category' => 'thankyou', 'name' => 'Thankyou page', 'url_slug' => 'thankyou-page'),
			);
		} elseif ($funnels_type == "sales") {
			$delault_pages = array(
				array('category' => 'optin', 'name' => 'Optin', 'url_slug' => 'optin'),
				array('category' => 'sales', 'name' => 'Sale Page', 'url_slug' => 'salespage'),
				array('category' => 'orderform', 'name' => 'Order Form', 'url_slug' => 'order-form'),
				array('category' => 'confirm', 'name' => 'Confirmation Page', 'url_slug' => 'confirmation-page'),
				array('category' => 'cancel', 'name' => 'Cancelation Page', 'url_slug' => 'cancelation-page'),
			);
		} else {
			$delault_pages = [];
		}


		$page_lvl_html = "";
		for ($i = 0; $i < count($delault_pages); $i++) {
			$lvl = $i + 1;
			$page_lvl_html .=	$template_control_button($lvl, $delault_pages[$i]);
		}
		$page_lvl_html = $mysqli->real_escape_string($page_lvl_html);

		$qry_save_lbl_btns = $mysqli->query("UPDATE `" . $dbpref . "quick_funnels` SET `labelhtml`='" . $page_lvl_html . "' where `id`=$funnel_id");
	}

	function deleteOlderFunnelFiles($dir, $type, $ab = 'a', $only_cache = false)
	{
		//delete all html css js before creating new
		$dir = rtrim($dir, "/");
		$files = scandir($dir);
		switch ($type) {
			case "html": {
					$start = "template_" . $ab;
					$cache = "cache_" . $ab;
					$amp = "amp_" . $ab;
					break;
				}
			case "css": {
					$start = "style_" . $ab;
					$cache = "cache_" . $ab;
					$amp = "amp_" . $ab;
					break;
				}
			case "js": {
					$start = "script_" . $ab;
					$cache = "cache_" . $ab;
					$amp = "amp_" . $ab;
					break;
				}
			default: {
					$start = false;
				}
		}
		if ($start === false) {
			return;
		}
		$reg = ($only_cache) ? "/((" . $cache . "|" . $amp . ")+\.[0-9a-zA-Z]*\." . $type . ")$/" : "/((" . $start . "|" . $cache . "|" . $amp . ")+\.[0-9]*\." . $type . ")$/";

		foreach ($files as $file) {
			$filee = $dir . "/" . $file;
			if (preg_match($reg, $file) && is_file($filee)) {
				unlink($filee);
			}
		}
	}
	function decodeHTMLSpecialChars($str)
	{
		/*$str=html_entity_decode(mb_convert_encoding(stripslashes($str), "HTML-ENTITIES", 'UTF-8'));*/

		$str = html_entity_decode($str, ENT_QUOTES, "UTF-8");


		$ch_arr = array(
			"&#128;" => "",
			"&#129;" => " ",
			"&#130;" => "",
			"&#131;" => "ƒ",
			"&#132;" => "",
			"&#133;" => "",
			"&#134;" => "",
			"&#135;" => "‡",
			"&#136;" => "",
			"&#137;" => "‰",
			"&#138;" => "Š",
			"&#139;" => "‹",
			"&#140;" => "Œ",
			"&#141;" => " ",
			"&#142;" => "",
			"&#143;" => " ",
			"&#144;" => " ",
			"&#145;" => "",
			"&#146;" => "’",
			"&#147;" => "",
			"&#148;" => "”",
			"&#149;" => "•",
			"&#150;" => "–",
			"&#151;" => "—",
			"&#152;" => "˜",
			"&#153;" => "™",
			"&#154;" => "š",
			"&#155;" => "›",
			"&#156;" => "œ",
			"&#157;" => " ",
			"&#158;" => "ž",
			"&#159;" => "Ÿ",
		);

		foreach ($ch_arr as $index => $val) {
			$str = str_replace($index, $val, $str);
		}

		$str = str_replace("Â", "", $str);
		$str = str_replace("€", "'", $str);
		$str = str_replace("â€œ", '"', $str);
		$str = str_replace('â“', '-', $str);
		$str = str_replace('€', '"', $str);
		$str = str_replace('Ã¢€', '’', $str);
		$str = str_replace('Ã¢', '–', $str);

		return $str;
	}
	function pageRouteEncode($str, $funnel_folder, $page = '', $encode = true)
	{
		$original = $this->routed_url_original;
		$temp = $this->routed_url;
		global $document_root;
		$install_url = get_option('install_url');

		$funnel_folder = rtrim(trim(str_replace('\\', '/', $funnel_folder)), '/');
		$funnel_folder = str_replace('@@qfnl_install_dir@@/', '', $funnel_folder);
		$funnel_folder = str_replace('@@qfnl_install_dir@@', '', $funnel_folder);

		$funnel_folder = str_replace($document_root . '/public_funnels', '', str_replace($document_root . '/public_funnels/', '', $funnel_folder));

		$funnel_folder = str_replace($document_root, '', str_replace($document_root . '/', '', $funnel_folder));


		$funnel_folder = str_replace($install_url . '/public_funnels', '', str_replace($install_url . '/public_funnels/', '', $funnel_folder));

		$funnel_folder = str_replace($install_url, '', str_replace($install_url . '/', '', $funnel_folder));

		$funnel_folder = trim($funnel_folder, '/');

		$page_folder = $funnel_folder;
		$page_folder_temp = "@@folder@@";

		if (strlen($page_folder) > 0) {
			$page_folder .= '/';
			//$page_folder_temp .='/';

			if (strlen($page) > 0) {
				$page_folder .= $page . '/';
			}
		}

		if ($encode) {
			$str = str_replace($original, $temp, $str);

			$str = str_replace($temp . '/' . $page_folder, $temp . '/' . $page_folder_temp, $str);
		} else {
			$str = str_replace($temp, $original, $str);

			$str = str_replace($original . '/' . $page_folder_temp, $original . '/' . $page_folder, $str);
		}
		return $str;
	}
	function pageRouteDecode($str, $funnel_folder, $page = '')
	{
		return self::pageRouteEncode($str, $funnel_folder, $page, false);
	}
	function createTemplate($varient, $dir, $html, $css, $js, $type, $funnel_id, $page = "index")
	{
		$create_stat = false;
		if (cf_dir_exists($dir)) {
			if ($this->do_route) {

				$html = self::pageRouteEncode($html, $dir, $page);
				//$html=self::decodeHTMLSpecialChars($html);
				$css = self::pageRouteEncode($css, $dir, $page);
				$js = self::pageRouteEncode($js, $dir, $page);
			}
			if (strlen(trim($html)) < 1) {
				die(0);
			}
			echo 1;

			$subname = $page;
			$page = $dir . "/" . $page;

			$dirarr = array($page, $page . '/asset', $page . '/asset/img', $page . '/asset/img/img-' . $type, $page . '/asset/js', $page . '/asset/css');

			for ($i = 0; $i < count($dirarr); $i++) {
				if (!cf_dir_exists($dirarr[$i])) {
					mkdir($dirarr[$i]);
				}
			}

			//$index = fopen($page."/index.php", "w") or die("Unable to open file!");

			$requiredir = __DIR__ . '/library.php';

			$requiredir = str_replace("\\", '/', $requiredir);

			$configfile = str_replace("library/library.php", "config.php", $requiredir);

			if ($this->do_route) {
				$required_files = "";
			} else {
				$required_files = "session_start();require_once('" . $requiredir . "');
					require_once('" . $configfile . "');\$ob=new Library();";
			}

			$content = "<?php
				" . $required_files . "
				\$ob->setInfo('mysqli',\$mysqli);
		        \$ob->setInfo('dbpref',\$dbpref);
				\$ob->setInfo('load',\$ob);
				\$GLOBALS['ob']=\$ob;
				\$dir=__DIR__;
				\$funnel=\$ob->loadFunnel();
                \$funnel->userIndexContent(" . $funnel_id . ",\$dir,'" . $varient . "');
				?>";

			$create_stat = cf_fwrite($page . "/index.php", $content);
			//fclose($index);

			//$html=self::minifyHTML($html);
			$html = str_replace("@dbquote@", "", $html);

			if ($type == "a") {
				//$template_a = fopen($page."/template_a.html", "w") or die("Unable to open file!");
				$create_stat = cf_fwrite($page . "/template_a." . $varient . ".html", $html);
				//fclose($template_a);
			} else if ($type == "b") {
				//$template_b = fopen($page."/template_b.html", "w") or die("Unable to open file!");
				$create_stat = cf_fwrite($page . "/template_b." . $varient . ".html", $html);
				//fclose($template_b);
			}

			$cssfile = $page . '/asset/css/style_' . $type . '.' . $varient . '.css';
			//$file=fopen($cssfile,"w");
			//$css=$this->createQuoteForCSSUrls($css);
			require_once("vendor/autoload.php");
			//$css_minifier = new Minify\CSS($css);
			//$css=$css_minifier->minify();

			$css = str_replace("@dbquote@", '"', $css);

			cf_fwrite($cssfile, $css);
			//fclose($file);

			if (strlen($js) > 0) {
				$jsfile = $page . '/asset/js/script_' . $type . '.' . $varient . '.js';
				//$file=fopen($jsfile,"w");
				cf_fwrite($jsfile, $js);
				//fclose($file);
			}
		} else {
			return 0;
		}
		return $create_stat;
	}
	function minifyHTML($buffer)
	{
		$search = array(
			'/\>[^\S ]+/s',     // strip whitespaces after tags, except space
			'/[^\S ]+\</s',     // strip whitespaces before tags, except space
			'/(\s)+/s',         // shorten multiple whitespace sequences
			'/<!--(.|\s)*?-->/' // Remove HTML comments
		);
		$replace = array(
			'>',
			'<',
			'\\1',
			''
		);
		$buffer = preg_replace($search, $replace, $buffer);
		return $buffer;
	}
	function forceEndSlash($dir)
	{
		$dir = rtrim(str_replace("\\", "/", $dir));
		$dir_arr = explode("/", $dir);
		$currenturl = getProtocol();
		$currenturl .= $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
		$url = parse_url($currenturl);
		$doredirect = false;

		if (get_option('force_https_funnels_pages') == '1' && $url['scheme'] == 'http') {
			$install_url = get_option('install_url');
			if (strpos($install_url, "https://") !== false) {
				$doredirect = true;
				$url['scheme'] = 'https';
				$redirect_url = $url['scheme'] . "://" . $url['host'];
				if (isset($url['path'])) {
					$redirect_url .= $url['path'];
				}
				if (isset($url['query'])) {
					$redirect_url .= '?' . $url['query'];
				}
			}
		}
		if (isset($url['path'])) {
			$path = explode("/", trim($url['path']));
			if ($path[count($path) - 1] == $dir_arr[count($dir_arr) - 1]) {
				$url['path'] = $url['path'] . '/';
				$redirect_url = $url['scheme'] . "://" . $url['host'];
				$redirect_url .= $url['path'];
				if (isset($url['query'])) {
					$redirect_url .= '?' . $url['query'];
				}
				$doredirect = true;
			}
		}
		if ($doredirect) {
			header('Location: ' . $redirect_url);
			die();
		}
	}
	function userIndexContent($funnel_id, $dir, $varient = 0)
	{
		//echo "<h1>Test</h1>";
		//user sight ab index content create----view for users
		self::forceEndSlash($dir);
		$ob = $GLOBALS['ob'];
		//auth
		$current_dir = str_replace("\\", "/", $dir);

		$current_funnel_dir = explode('/', rtrim($current_dir, '/'));
		array_pop($current_funnel_dir);
		$current_funnel_dir = implode('/', $current_funnel_dir);

		$funnel = $ob->loadFunnel();


		if (isset($_GET['gmailloadtemplate'])) {
			$screenshot_seperator = explode("seperator", $_GET['gmailloadtemplate']);
			$_GET['gmailloadtemplate'] = $screenshot_seperator[0];
			if (isset($screenshot_seperator[1])) {
				$_GET["googlescreenshottoken"] = $screenshot_seperator[1];
			}
		}
		//--plugin global variables funnel id
		$GLOBALS['the_funnel_id'] = 0;
		$GLOBALS['the_page_level'] = 0;
		$GLOBALS['the_page_type'] = 'a';

		if ($funnel_id) {
			$GLOBALS['the_funnel_id'] = $funnel_id;
		}


		$thisfunneldata = $funnel->goNext($current_dir, $funnel_id);
		if (isset($thisfunneldata['varient'])) {
			$varient = $thisfunneldata['varient'];
		}
		$process_amp = 0;
		if (isset($_POST['qfnl-amp-submit'])) {
			$process_amp = 1;
			unset($_POST['qfnl-amp-submit']);
		}

		if (!is_array($thisfunneldata)) {
			die('<h1>404 Page Not Found</h1>');
		}



		//--plugin global variables page level
		if (isset($thisfunneldata['label'])) {
			$GLOBALS['the_page_level'] = $thisfunneldata['label'];
		}


		$currentpagesetting_ob = 0;
		if (isset($thisfunneldata['settings'])) {
			$currentpagesetting_ob = json_decode($thisfunneldata['settings']);
		}

		$_SESSION['current_funnel_label' . get_option('site_token')] = array($funnel_id, $thisfunneldata['label']);

		if (isset($_GET['process_oto']) || isset($_GET['exit_oto'])) {
			$process_product_oto = (isset($_GET['process_oto'])) ? $_GET['process_oto'] : '';
			$processed_oto = self::processOTO($process_product_oto, $funnel_id, $thisfunneldata['current_folder'], $thisfunneldata['label'], $thisfunneldata['category']);

			if ($processed_oto !== false) {
				die();
			}
		}
		if (isset($_GET['oto_remove'])) {
			self::removeProductInCheckOutPage($_GET['oto_remove']);
			goto exit_post;
		}
		if (isset($_POST)) {
			if (isset($_POST['confirm_checkout'])) {
				self::confirmCheckOut($funnel_id, $thisfunneldata['current_folder'], $thisfunneldata['label'], $thisfunneldata['category']);
			}
			if (isset($_POST['oto_remove'])) {
				self::removeProductInCheckOutPage($_POST['oto_remove']);
				goto exit_post;
			}
			if ($_SERVER['REQUEST_METHOD'] == "POST") {
				$doredirect = $funnel->leadsStoreFromSavedFunnels($funnel_id, $thisfunneldata['current_folder'], $_SESSION['currentab' . get_option('site_token')], $_POST);
				//redirect if redirection is on
				//$redirection_url_after_post="";
				if (isset($thisfunneldata['settings'])) {
					$redirectionsetting_ob = $currentpagesetting_ob;
					if (isset($redirectionsetting_ob->redirect_for_post) && $redirectionsetting_ob->redirect_for_post) {
						$redirectionurlforpost = $redirectionsetting_ob->redirect_for_post_url;
						if (filter_var($redirectionurlforpost, FILTER_VALIDATE_URL)) {
							$redirection_url_after_post = $redirectionurlforpost;
							//header('Location: '.$redirectionurlforpost.'');
						}
					}
				}
				if ($thisfunneldata['next_url'] && (!isset($redirection_url_after_post))) {
					if ($doredirect == 1) {
						$redirection_url_after_post = $thisfunneldata['next_url'] . "/";
						//header('Location: '.$thisfunneldata['next_url'].'/');
					}
				}
				if (isset($redirection_url_after_post) && !($process_amp)) {
					@header("Location: " . $redirection_url_after_post);
					echo "<script>window.location='" . $redirection_url_after_post . "'</script>";
					die();
				} elseif ($process_amp) {
					$process_amp_currentdomain = getProtocol();
					$process_amp_currentdomain .= $_SERVER['HTTP_HOST'];
					header("Content-type: application/json");
					header("Access-Control-Allow-Credentials: true");
					header("Access-Control-Allow-Origin: *.ampproject.org");
					header("AMP-Access-Control-Allow-Source-Origin: " . $process_amp_currentdomain);
					if ($doredirect !== 1) {
						header("HTTP/1.0 412 Precondition Failed", true, 412);
						echo json_encode(array('amp_validation_error' => $doredirect));
						die();
					} elseif (isset($redirection_url_after_post)) {
						header("AMP-Redirect-To: " . $redirection_url_after_post);
						header("Access-Control-Expose-Headers: AMP-Redirect-To, AMP-Access-Control-Allow-Source-Origin");
						die();
					}
				}
				//$process_amp

			}
		}
		exit_post:
		ob_start();
		$ab = 1;
		$cssab = 'a';

		if ($thisfunneldata['has_ab'] && $thisfunneldata['b_varient'] && is_file($current_dir . '/template_b.' . $thisfunneldata['b_varient'] . '.html')) {
			$ab = mt_rand(1, 2);
			if ($thisfunneldata['active_type'] !== 'NA') {
				$ab = ($thisfunneldata['active_type'] === 'a') ? 1 : 2;
			}
		}
		if (isset($_GET['gmailloadtemplate'])) {
			$ab = ($_GET['gmailloadtemplate'] == 'a') ? 1 : 2;
		}
		if ($ab == 2) {
			$varient = $thisfunneldata['b_varient'];
			$cssab = 'b';
		}

		//--plugin global variables page type
		$GLOBALS['the_page_type'] = $cssab;

		$thisfunneldata['selected_template'] = $cssab;
		$thisfunneldata['funnel_id'] = $funnel_id;

		$_SESSION['currentab' . get_option('site_token')] = $cssab;
		if ($thisfunneldata['viewed'] == 1 && !(isset($_GET['gmailloadtemplate']))) {
			$funnel->storeViews($funnel_id, $thisfunneldata['current_folder'], $cssab);
		}


		$loadcachedfile = 0;
		$create_cache = 0;
		$create_amp = 0;
		if (is_object($currentpagesetting_ob) && isset($currentpagesetting_ob->page_cache) && ($currentpagesetting_ob->page_cache) && !(isset($_GET['gmailloadtemplate']))) {
			$create_cache = 1;
			if (isset($currentpagesetting_ob->active_amp) && $currentpagesetting_ob->active_amp) {
				$create_amp = 1;
			}
			if (isset($_GET['qfnl_amp'])) {
				$cache_ab_file = ($ab == 1) ? 'amp_a.' . $varient . '.html' : 'amp_b.' . $varient . '.html';
				$cache_ab_file = $current_dir . "/" . $cache_ab_file;
				if (is_file($cache_ab_file)) {
					$detectedampfile = 1;
				}
			}

			if (!isset($detectedampfile)) {
				$cache_ab_file = ($ab == 1) ? 'cache_a.' . $varient . '.html' : 'cache_b.' . $varient . '.html';
				$cache_ab_file = $current_dir . "/" . $cache_ab_file;
			}

			if (is_file($cache_ab_file)) {
				//{validation_error}
				//ob_end_clean();
				require_once($cache_ab_file);
				$cacheddata = ob_get_contents();

				if (isset($doredirect) && ($doredirect !== 1)) {
					$cacheddata = str_replace("{validation_error}", $doredirect, $cacheddata);
				} else {
					$cacheddata = str_replace("{validation_error}", "", $cacheddata);
				}
				ob_end_clean();
				echo $cacheddata;
				ob_flush();
				flush();
				++$loadcachedfile;
			}
		}

		//echo $current_dir;

		if ($loadcachedfile < 1) {
			if ($ab == 1) {
				if (is_file($current_dir . '/template_a.' . $varient . '.html')) {
					require_once($current_dir . '/template_a.' . $varient . '.html');
				}
			}
			if ($ab == 2) {
				if (is_file($current_dir . '/template_b.' . $varient . '.html')) {
					require_once($current_dir . '/template_b.' . $varient . '.html');
				} else {
					require_once($current_dir . '/template_a.' . $varient . '.html');
				}
			}
			$content = ob_get_contents();
			ob_end_clean();

			$css_js_routedurl = "";
			$css_js_routeddir = "";
			if ($this->do_route) {
				$content = self::pageRouteDecode($content, $current_funnel_dir, $thisfunneldata['current_folder']);
				$temp_current_dir_for_route = $current_dir;
				$temp_current_dir_route = str_replace($this->routed_dir_original, "", $temp_current_dir_for_route);
				$css_js_routedurl = $this->routed_url_original . $temp_current_dir_route;
				$css_js_routeddir = $temp_current_dir_for_route;

				$route_required_script = get_option('install_url') . "/index.php?page=load_static_scripts&script=" . base64_encode($css_js_routedurl . "/asset/js/script_" . $cssab . "." . $varient . ".js") . "&create_cache=" . $create_cache . "&script_type=type.js";
				$route_required_style = get_option('install_url') . '/index.php?page=load_static_scripts&script=' . base64_encode($css_js_routedurl . "/asset/css/style_" . $cssab . "." . $varient . ".css") . '&create_cache=' . $create_cache . '&script_type=type.css';
			}

			$gdprcookieconsentdata = self::createCookieConsentBox($thisfunneldata['settings'], $funnel_id . '_' . $thisfunneldata['label']);
			$content .= $gdprcookieconsentdata;

			$content = str_replace("?page=data_requests", "?page=data_requests&gdpr_token=" . $funnel_id, $content);

			$content .= self::popweredByUs();

			if (strpos($content, '</head>') < 1) {
				//<meta name="viewport" content="width=device-width, initial-scale=1">
				$content = '
					<!doctype html>
					<html>
					<head>
					</head>
					<body>
					' . $content . '
					<script>
						AOS.init();
					</script>
					</body>
					</html>';
			}
			//$_SESSION['currentab'.get_option('site_token')]=$cssab;


			if ($this->do_route) {
				$jslink = "<script src='" . $route_required_script . "'></script>";
				$csslink = "<link rel='stylesheet' href='" . $route_required_style . "'>";
			} else {
				$jslink = "<script src='asset/js/script_" . $cssab . "." . $varient . ".js'></script>";
				$csslink = '<link rel="stylesheet" href="asset/css/style_' . $cssab . '.' . $varient . '.css">';
			}


			$reg = "/(\"|')+(.*(fa-[a-z]+)+)/";
			//preg_match($reg,$content);
			$cache_fontawesome = "";
			if (preg_match($reg, $content)) {
				$csslink .= "<link rel='stylesheet' href='" . get_option('install_url') . "/assets/fontawesome/css/all.css'>";

				$cache_fontawesome = "<link rel='stylesheet' href='" . get_option('install_url') . "/assets/fontawesome/css/all.css'>";
			}
			$main_url = get_option("install_url");

			$jslink .= '<script src="' . $main_url . '/assets/js/vv/libs/aos/aos.js"></script>
			<link rel="stylesheet" href="' . $main_url . '/assets/js/vv/libs/aos/aos.css" />
			<noscript>
				<style type="text/css">
					[data-aos] {
						opacity: 1 !important;
						transform: translate(0) scale(1) !important;
					}
				</style>
				</noscript>';
			$metacontents = self::createHeaderMeta($thisfunneldata['title'], $thisfunneldata['metadata']);

			$content = str_replace('</head>', $metacontents . "</head>", $content);
			$content = str_replace('</head>', $thisfunneldata['header'] . "</head>", $content);
			$content = str_replace('</body>', $thisfunneldata['footer'] . "</body>", $content);
			$content = str_replace('</head>', $csslink . $jslink . '</head>', $content);
			$content = self::addIntegrations($thisfunneldata, $content);
			if ($thisfunneldata['is_membership'] == "forgotpassword") {
				$content = self::fpwdManage($content);
			}

			if (isset($_SESSION['qfnl_membership_' . get_option('site_token') . '_' . $funnel_id])) {

				$logouturl_inmembership = getProtocol();
				$logouturl_inmembership .= str_replace("//", "/", $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] . "/?logout=1");

				$content = str_replace('{logout_url}', $logouturl_inmembership, $content);
				//%7Blogout_url%7D
				$content = str_replace('%7Blogout_url%7D', $logouturl_inmembership, $content);
				$content = str_replace('{logout_link}', '<a href="' . $logouturl_inmembership . '">logout</a>', $content);

				if (is_array($_SESSION['qfnl_membership_' . get_option('site_token') . '_' . $funnel_id])) {
					foreach ($_SESSION['qfnl_membership_' . get_option('site_token') . '_' . $funnel_id] as $index => $data) {
						if ($index == "session_product_ids") {
							$sales_ob = $ob->loadSell();
							$content = $sales_ob->productTemplatecreator($content, $data);
							continue;
						}
						if ($index == "email") {
							$member_ob = $this->load->loadMember();
							$member_image = $member_ob->get_gravatar_image($data);
							$content = str_replace('{student_image}', $member_image, $content);
						}
						$content = str_replace('{' . $index . '}', $data, $content);
					}
				}
			}
			if (isset($thisfunneldata['category']) && isset($_SESSION['current_payment_cofirmation' . get_option('site_token')])) {
				$sessiondataforsales = $_SESSION['current_payment_cofirmation' . get_option('site_token')];
				if ($thisfunneldata['category'] == 'orderform' || $thisfunneldata['category'] == 'confirm') {
					if (isset($sessiondataforsales['product_ids'])) {
						$sales_ob = $ob->loadSell();
						$content = $sales_ob->productTemplatecreator($content, $sessiondataforsales['product_ids']);
					}
					if (isset($sessiondataforsales['membership'])) {
						$member_ob = $this->load->loadMember();
						$content = $member_ob->membrshipTemplatecreator($content, $sessiondataforsales['membership']);
					}
				}
				$content = arrayIndexToStr($content, $sessiondataforsales);
			}
			if (isset($thisfunneldata['category']) && $thisfunneldata['category'] === 'checkout') {
				//checkoutpage content create
				$content = self::checkOutContentGenerate($content);
			}
			$login_url = self::getPageUrl($funnel_id, 'login');
			$register_url = self::getPageUrl($funnel_id, 'register');
			$forgot_url = self::getPageUrl($funnel_id, 'forgotpassword');

			$content = str_replace('{register_url}', $register_url, $content);
			$content = str_replace('%7Bregister_url%7D', $register_url, $content);
			$content = str_replace('{login_url}', $login_url, $content);
			$content = str_replace('%7Blogin_url%7D', $login_url, $content);
			$content = str_replace('{forgot_url}', $forgot_url, $content);
			$content = str_replace('%7Bforgot_url%7D', $forgot_url, $content);

			$cached_content = $content;
			if (isset($doredirect)) {
				if ($doredirect === 1) {
					$content = str_replace('{validation_error}', '', $content);
				} else {
					$content = str_replace('{validation_error}', $doredirect, $content);
				}
			} else {
				$content = str_replace('{validation_error}', '', $content);
			}
			if (isset($_GET['gmailloadtemplate'])) {

				$gscrrn_show_css_file = $current_dir . "/asset/css/style_" . $cssab . "." . $varient . ".css";

				$gscrrn_show_css_file_content = "";
				if (is_file($gscrrn_show_css_file)) {
					if (filesize($gscrrn_show_css_file) > 0) {
						$gscrrn_show_css_file_fp = fopen($gscrrn_show_css_file, "r");
						$gscrrn_show_css_file_content = fread($gscrrn_show_css_file_fp, filesize($gscrrn_show_css_file));
						fclose($gscrrn_show_css_file_fp);
					}
				}
				if ($this->do_route) {
					$gscrrn_show_css_file_content = self::pageRouteDecode($gscrrn_show_css_file_content, $current_funnel_dir, $thisfunneldata['current_folder']);
				}
				//$content=str_replace("</body>","<style>".$gscrrn_show_css_file_content."</style></body>",$content);
			}
			@ob_start();
			$get_bootstrap = $this->load->loadBootstrap(true);
			$content = str_replace("<head>", "<head>" . $get_bootstrap, $content);
			echo $content;

			ob_flush();
			flush();

			//cache create
			if ($create_cache && !(isset($_GET['gmailloadtemplate']))) {
				$amp_currenturl = parse_url($_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);

				//print_r($amp_currenturl);

				$amp_current_url_original = getProtocol();
				$amp_current_url_original .= $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

				if (isset($amp_currenturl['path'])) {
					$amp_currenturl['path'] .= "/";
					$amp_currenturl['path'] = str_replace("//", "/", $amp_currenturl['path']);
					$amp_current_url_original = getProtocol();
					$amp_current_url_original .= $amp_currenturl['path'];
				}

				$cachecontent = $cached_content;

				if ($this->do_route) {
					$cachecssjscontent = $metacontents . "<script src='asset/js/cache_" . $cssab . "." . $varient . ".js'></script><link rel='stylesheet' href='asset/css/cache_" . $cssab . "." . $varient . ".css'></link>" . $cache_fontawesome;

					$amp_css = '<style amp-custom>
						@import-amp-css@</style>
						';

					$amp_js = "<amp-script layout='container' src='" . $amp_current_url_original . "asset/js/cache_" . $cssab . "." . $varient . ".js'>";

					$hold_amp_content = $cachecontent;
					$cachecontent = str_replace($csslink . $jslink, $cachecssjscontent, $cachecontent);
					if ($create_amp) {
						$amp_content = str_replace($csslink . $jslink, $amp_css, $hold_amp_content);
						//$cachecontent=str_replace("<body>","<body>".$amp_js,$cachecontent);
					}
				} elseif ($create_amp) {
					$amp_css = '<style amp-custom>
						@import-amp-css@</style>
						';

					$amp_js = "<amp-script layout='container' src='" . $amp_current_url_original . "asset/js/cache_" . $cssab . "." . $varient . ".js'>";

					$amp_content = str_replace($csslink . $jslink, $amp_css, $cachecontent);
					//$cachecontent=str_replace("<body>","<body>".$amp_js,$cachecontent);
				}

				if ($create_amp) {

					$amp_content_css = "";
					$amp_content_css_file = $current_dir . "/asset/css/style_" . $cssab . "." . $varient . ".css";
					$import_css_include = "";


					if (is_file($amp_content_css_file)) {
						//require_once("vendor/autoload.php");
						//$minifier = new Minify\CSS($amp_content_css_file);
						//$amp_content_css=$minifier->minify();
						$amp_content_css_fp = fopen($amp_content_css_file, "r");
						$amp_content_css = fread($amp_content_css_fp, filesize($amp_content_css_file));
						fclose($amp_content_css_fp);

						if ($this->do_route) {
							$amp_content_css = self::pageRouteDecode($amp_content_css, $current_funnel_dir, $thisfunneldata['current_folder']);
						}
						$amp_content_css = preg_replace("/(\s)*(\!important)+/", "", $amp_content_css);

						$amp_css_import_rule = "/(@import)+(.(?!(;)))*[\)\s\"\']*(;){1}/";
						preg_match_all($amp_css_import_rule, $amp_content_css, $importcss_arr);

						if (isset($importcss_arr[0]) && is_array($importcss_arr[0])) {
							for ($i = 0; $i < count($importcss_arr[0]); $i++) {
								$import_links = preg_replace("/((@import)|(url\s|url|\s)|(;)|(\"|')|[()])*/", "", $importcss_arr[0][$i]);
								if (filter_var($import_links, FILTER_VALIDATE_URL)) {
									$import_css_include .= '<link rel="stylesheet" href="' . $import_links . '">';
								}
							}
							$amp_content_css = preg_replace($amp_css_import_rule, "", $amp_content_css);
						}
					}
					//@import-amp-css@
					$amp_content = str_replace("@import-amp-css@</style>", "@import-amp-css@</style>" . $import_css_include, $amp_content);
					$amp_content = str_replace("@import-amp-css@", $amp_content_css, $amp_content);
					$amp_content = self::createAMP($amp_content, $varient, $amp_current_url_original, $amp_js);
					self::createOrReadAmp($dir, $varient, $amp_content, "a", "create");
					$cachecontent = str_replace("<head>", '<head>
						<link rel="amphtml" href="' . $amp_current_url_original . '?qfnl_amp=amp.' . $varient . '.html">', $cachecontent);
				}

				$get_bootstrap = $this->load->loadBootstrap(true);
				$cachecontent = str_replace("<head>", "<head>" . $get_bootstrap, $cachecontent);
				self::createOrReadCache($dir, $varient, $cachecontent, $cssab, "create");
			}
		}
	}
	function getPageUrl($funnel = '0', $page = 'login')
	{
		$mysqli = $this->mysqli;
		$pref = $this->dbpref;
		$table = $pref . "quick_pagefunnel";
		$qry  = $mysqli->query("SELECT `filename` FROM `$table` WHERE `funnelid`='$funnel' AND `category`='$page'");
		if ($qry->num_rows > 0 && $row = $qry->fetch_object()) {
			$baseurl = self::getFunnel($funnel, "`baseurl`");
			$url = $baseurl->baseurl . '/' . $row->filename;
			return str_replace("@@qfnl_install_url@@", get_option("install_url"), $url);
		} else {
			return "#";
		}
	}
	function createOrReadAmp($dir, $varient, $content = "", $type = "a", $do = "create")
	{
		//$do=create/read
		$file = $dir . "/amp_" . $type . "." . $varient . ".html";
		if ($do == "create" && !is_file($file)) {
			//$fp=fopen($file,"w");
			cf_fwrite($file, $content);
			//fclose($fp);
		} else {
			if (is_file($file)) {
				$fp = fopen($file, "r");
				$content = fread($fp, filesize($file));
				fclose($fp);
				return $content;
			} else {
				return 0;
			}
		}
	}
	function createOrReadCache($dir, $varient, $content = "", $type = "a", $do = "create")
	{
		//$do=create/read
		$file = $dir . "/cache_" . $type . "." . $varient . ".html";
		if ($do == "create" && !is_file($file)) {
			//$fp=fopen($file,"w");
			cf_fwrite($file, $content);
			//fclose($fp);
		} else {
			if (is_file($file)) {
				$fp = fopen($file, "r");
				$content = fread($fp, filesize($file));
				fclose($fp);
				return $content;
			} else {
				return 0;
			}
		}
	}
	function createAMP($html, $varient, $url, $amp_js)
	{
		require_once("vendor/autoload.php");
		ini_set("allow_url_fopen", "On");

		$html = str_replace("<html>", "<html amp>", $html);

		$canonical_tag = '<link rel="canonical" href="' . $url . '">';

		$html = str_replace('<meta name="viewport"', $canonical_tag . '
	<meta name="viewport"', $html);

		$amp_boilerplate = "<style amp-boilerplate>body{-webkit-animation:-amp-start 8s steps(1,end) 0s 1 normal both;-moz-animation:-amp-start 8s steps(1,end) 0s 1 normal both;-ms-animation:-amp-start 8s steps(1,end) 0s 1 normal both;animation:-amp-start 8s steps(1,end) 0s 1 normal both}@-webkit-keyframes -amp-start{from{visibility:hidden}to{visibility:visible}}@-moz-keyframes -amp-start{from{visibility:hidden}to{visibility:visible}}@-ms-keyframes -amp-start{from{visibility:hidden}to{visibility:visible}}@-o-keyframes -amp-start{from{visibility:hidden}to{visibility:visible}}@keyframes -amp-start{from{visibility:hidden}to{visibility:visible}}</style><noscript><style amp-boilerplate>body{-webkit-animation:none;-moz-animation:none;-ms-animation:none;animation:none}</style></noscript>";

		//<script async custom-element='amp-script' src='https://cdn.ampproject.org/v0/amp-script-0.1.js'></script>

		$html = str_replace("</head>", "<script async src='https://cdn.ampproject.org/v0.js'></script><script async custom-element='amp-youtube' src='https://cdn.ampproject.org/v0/amp-youtube-0.1.js'></script><script async custom-element='amp-form' src='https://cdn.ampproject.org/v0/amp-form-0.1.js'></script><script async custom-template='amp-mustache' src='https://cdn.ampproject.org/v0/amp-mustache-0.2.js'></script><script async custom-element='amp-vimeo' src='https://cdn.ampproject.org/v0/amp-vimeo-0.1.js'></script>
	<script async custom-element='amp-iframe' src='https://cdn.ampproject.org/v0/amp-iframe-0.1.js'></script>
	" . $amp_boilerplate . "
	</head>
	", $html);

		$html = str_replace("<form", "<!--qfnl-amp--<form", $html);
		$html = str_replace("</form>", '<input type="hidden" name="qfnl-amp-submit"></form>qfnlamp-->', $html);


		//echo "-----------".$html."---------------";
		$start = strpos($html, "<body>") + 6;
		$len = (strpos($html, "</body>")) - $start;
		$body = substr($html, $start, $len);

		$amp = new AMP();
		$amp->loadHtml($body);
		$amp_html = $amp->convertToAmpHtml();

		$amp_html = self::ampify_img($amp_html);

		//print($amp->warningsHumanText());

		$html = str_replace($body, $amp_html, $html);

		$html = preg_replace("/((<!--qfnl-amp--)|(qfnlamp-->))+/", "", $html);

		$html = preg_replace("/action=((\"\")|(''))/", 'action-xhr="' . $url . '" target="_top"', $html);
		$html = str_replace("{validation_error}", '<div submit-error>
    <template type="amp-mustache">{{amp_validation_error}}</template>
  </div>', $html);

		//$html=str_replace("<body>","<body>".$amp_js,$html);
		//$html=str_replace("</body>","</amp-script></body>",$html);

		return $html;
	}
	function ampify_img($html)
	{
		preg_match_all("#<img(.*?)\\/?>#", $html, $img_matches);
		foreach ($img_matches[1] as $key => $img_tag) {
			preg_match_all('/(alt|src|width|height|class)=["\'](.*?)["\']/i', $img_tag, $attribute_matches);
			$attributes = array_combine($attribute_matches[1], $attribute_matches[2]);
			if (!array_key_exists('width', $attributes) || !array_key_exists('height', $attributes)) {
				if (array_key_exists('src', $attributes)) {
					list($width, $height) = self::getImgsize($attributes['src']);
					$attributes['width'] = $width;
					$attributes['height'] = $height;
				}
			}
			$amp_tag = '<amp-img ';
			foreach ($attributes as $attribute => $val) {
				$amp_tag .= $attribute . '="' . $val . '" ';
			}
			$amp_tag .= 'layout="responsive"';
			$amp_tag .= '>';
			$amp_tag .= '</amp-img>';
			$html = str_replace($img_matches[0][$key], $amp_tag, $html);
		}
		return $html;
	}
	function getImgsize($url, $referer = '')
	{
		$headers = array('Range: bytes=0-131072');
		if (!empty($referer)) {
			array_push($headers, 'Referer: ' . $referer);
		}

		// Get remote image
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		$data = curl_exec($ch);
		$http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		$curl_errno = curl_errno($ch);
		curl_close($ch);

		// Get network stauts
		if ($http_status != 200) {
			echo 'HTTP Status[' . $http_status . '] Errno [' . $curl_errno . ']';
			return [0, 0];
		}

		// Process image
		$image = @imagecreatefromstring($data);

		if (!is_bool($image)) {
			$dims = [imagesx($image), imagesy($image)];
		} else {
			$dims = [0, 0];
		}

		@imagedestroy($image);

		return $dims;
	}

	function cssJsScriptView($url, $cache = 0)
	{
		$file = str_replace($this->routed_url_original, $this->routed_dir_original, $url);
		$data = "";

		$file_info = pathinfo($file);

		if (is_file($file)  && in_array($file_info['extension'], array('css', 'js')) && filesize($file) > 0) {
			$fp = fopen($file, 'r');
			$data = fread($fp, filesize($file));
			fclose($fp);

			$file_path = rtrim(str_replace('\\', '/', dirname($file)), '/');
			$file_path_arr = explode('/asset/', $file_path);
			$page_path = '';

			if (count($file_path_arr) > 1) {
				array_pop($file_path_arr);
				$file_path = implode('/asset/', $file_path_arr);
				$file_path_arr = explode('/', $file_path);
				$page_path = $file_path_arr[count($file_path_arr) - 1];
				array_pop($file_path_arr);
				$file_path = implode('/', $file_path_arr);
			}

			$data = self::pageRouteDecode($data, $file_path, $page_path);
			$data = str_replace($this->routed_dir, $this->routed_dir_original, $data);

			if ($cache == 1) {
				$cache_file = $file_info['dirname'];
				$file_basename = preg_replace("/(script|style)+/", 'cache', $file_info['basename']);
				$cache_file .= "/" . $file_basename;
				if (!is_file($cache_file)) {
					//$fp=fopen($cache_file,"w");
					cf_fwrite($cache_file, $data);
					//fclose($fp);
				}
			}
		}
		return $data;
	}
	function addIntegrations($page_settings, $html)
	{
		$page_data = $page_settings;
		$page_settings = json_decode($page_settings['settings']);

		$html = self::addAutoLinking($html);
		$html = self::addCoundownTimerScript($html);
		$html = self::addBsCollapseHandler($html);
		if (!empty($page_data['csslink'])) {
			$csslinks =  json_decode($page_data['csslink'], true);
			$csslinks = implode("\n", $csslinks);
			$html = str_replace("</head>", $csslinks . "</head>", $html);
		}
		if (isset($GLOBALS['plugin_loader'])) {
			$plugin_ob = $GLOBALS['plugin_loader'];
			$plugin_header_contents = $plugin_ob->attachToContent('cf_head', $page_data);
			$plugin_footer_contents = $plugin_ob->attachToContent('cf_footer', $page_data);

			if ($plugin_header_contents !== null) {
				$html = str_replace("</head>", $plugin_header_contents . "</head>", $html);
			}
			if ($plugin_footer_contents !== null) {
				$html = str_replace("</body>", $plugin_footer_contents . "</body>", $html);
			}

			$html = $plugin_ob->processFilter('the_content', $html, $page_data);

			$html = $plugin_ob->doShortcode(false, $html);
		}

		return $html;
	}
	function addBsCollapseHandler($html)
	{
		$script = '
		<script>
			(function($){
				$(`[data_keep_area_visible]`).each(function(){
					let stat= $(this).attr("data_keep_area_visible");
					if(stat===false|| stat==="false")
					{
						$(this).css("display", "none");
					}
				});

				$(`[data-bs-toggle-collapse-controller]`).each(function(){
					try
					{
						$(this).on("click", function(){
							let el= $(this);
							let target= $(el.attr("data-bs-target"));
							$(target).slideToggle();
						})
					}catch(err){ console.log(err); }
				});
				$(`form`).each(function(){
					let val = addScriptFornonce();
					let hid = `<input  type="hidden" name="nonce" value="${val}" />`;
					$(this).prepend(hid);
				});
			})(jQuery)

		function addScriptFornonce()
		{
			return "' . cf_create_nonce('form_verify') . '";
		}
		</script>
		
		';

		$html = $html . $script;
		return $html;
	}
	function addCoundownTimerScript($html, $script_only = false)
	{
		$script = '
		<script>
		function cfCountDownTimer(){
			try
			{	let doc=document.querySelectorAll(`div[data-countdown-timer="1"]`);
				doc=doc[doc.length-1];
				let nostart=doc.querySelectorAll(`div[idd="nostart"]`)[0];
				let exp=doc.querySelectorAll(`div[idd="exp"]`)[0];
				let timer=doc.querySelectorAll(`div[idd="noexp"]`);
				let tempShow=function(cond)
				{
					if(cond===1)
					{
						nostart.style.display="none";
						exp.style.display="none";
						timer.forEach((docc)=>{ docc.style.display="block"; });
					}
					else if(cond===2)
					{
						nostart.style.display="block";
						exp.style.display="none";
						timer.forEach((docc)=>{ docc.style.display="none"; });
					}
					else
					{
						nostart.style.display="none";
						exp.style.display="block";
						timer.forEach((docc)=>{ docc.style.display="none"; });
					}
				}
				let showTime=function(selector,time){
					try
					{	time=time.toString();
						time=(time.length<2)? "0"+time:time;
						doc.querySelectorAll(`div[idd="${selector}"]`)[0].innerHTML=time;
					}catch(err){console.log(err);}
				};
				let verify=function(){
				let start=new Date(doc.getAttribute("data-countdown-min")).getTime();
				let destin=new Date(doc.getAttribute("data-countdown-max")).getTime();
				let current=new Date(new Date(Date.now()).toUTCString()).getTime();
				if(destin<current)
				{
					tempShow(0);
				}
				else if(current<start)
				{
					tempShow(2);
				}
				else
				{	
					tempShow(1);
					let diff=Math.round((destin-current)/1000);
					let diff_days=Math.floor(diff/(24*60*60));
					let diff_hours=Math.floor((diff%(24*60*60))/3600);
					let diff_mins=Math.floor(((diff%(24*60*60))%3600)/60);
					let diff_sec=Math.floor(((diff%(24*60*60))%3600)%60);
					if(doc.getAttribute("data-start-timer")==\'true\')
					{
						showTime(\'d\',diff_days);
						showTime(\'h\',diff_hours);
						showTime(\'m\',diff_mins);
						showTime(\'s\',diff_sec);
					}
				}
					setTimeout(verify,200);
				};
				verify();
			}
			catch(err){console.log(err);}
		};
		</script>';
		if ($script_only) {
			return $script;
		}
		if (strpos($html, 'data-countdown-timer') !== false) {
			$html = str_replace("</head>", $script . "</head>", $html);
		}
		return $html;
	}
	function addAutoLinking($html)
	{
		if (strpos($html, "data-autolink") !== false) {
			$str = "<script>
			function cfAutoLinkAdder(doc)
			{
				try{
					if(doc.getAttribute('data-autolink'))
					{
						let current=new window.URL(window.location.href);
						if(current.searchParams.get('loadalltemplatedata_get') && current.searchParams.get('loadalltemplatedata_get')==='1')
						{
							return;
						}
						let url='';
						if(doc.getAttribute('data-autolink-oto'))
						{
							let type=doc.getAttribute('data-autolink-oto');
							let product=(doc.getAttribute('data-autolink-oto-product'))? doc.getAttribute('data-autolink-oto-product'):'';
							url=current;
							if(type==='1')
							{
								url.searchParams.append('process_oto',product);
								try
								{
									let next=doc.getAttribute('data-autolink-oto-next');
									if(next && next.trim().length>2)
									{
										next=btoa(next.trim());
										url.searchParams.append('go_next_oto',next);
									}
								}catch(err){console.log(err);}
							}
							else if(type==='2')
							{
								url.searchParams.append('exit_oto',1);
							}
							else if(type==='-1')
							{
								url.searchParams.append('oto_remove',product);
							}
							url=url.href;
						}
						else
						{
							url=doc.getAttribute('data-autolink-url');
						}
						let target='';
						if(doc.getAttribute('data-autolink-target'))
						{
							target=doc.getAttribute('data-autolink-target');
							window.open(url,target);
						}
						else
						{
							window.location=url;
						}
					}
				}catch(err){console.log(err);}
			}
			(function(){
				let docs=document.querySelectorAll(`[data-autolink='1']`);
				docs.forEach(doc=>{
					doc.style.cursor='pointer';
					doc.addEventListener(`click`,function(){
						cfAutoLinkAdder(this);
					});
				});
			})();
			</script>";
			$html = $html . $str;
		}
		return $html;
	}
	function createHeaderMeta($title, $metadata)
	{
		//header meta create
		$data = '<meta charset="utf-8" />';
		if (strlen($title) > 0) {
			$data .= "<title>" . $title . "</title>";
		}
		$data .= '<meta name="viewport" content="width=device-width,minimum-scale=1,initial-scale=1">';
		if (strlen($metadata) > 0) {
			$metadata = (array)json_decode($metadata);
			foreach ($metadata as $index => $value) { //<link rel="shortcut icon" type="image/x-icon" href="icon.ico">
				if (strlen($value) < 1) {
					continue;
				}
				if ($index == "icon") {
					if (strpos($value, "ico") > 0) {
						$data .= '<link rel="shortcut icon" type="image/x-icon" href="' . $value . '">';
					} else {
						$data .= '<link rel="icon" href="' . $value . '" sizes="16x16">';
					}
				} elseif ($index == "DC_title") {
					$data .= "<meta name='DC.index' content='" . $value . "' />";
				} else {
					$data .= "<meta name='" . $index . "' content='" . $value . "' />";;
				}
			}
		}
		return $data;
	}
	function popweredByUs()
	{
		//branding
		$pr = $this->load->isPlusUser();

		return ($pr) ? '' : '<iframe scrolling="no" style="bottom:0px;right:0px;position:fixed;height:45px;width:235px;border:0px;padding:0px;overflow:hidden;z-index:9999999" src="' . get_option('install_url') . '/index.php?page=show_cf_narand"></iframe>';
	}
	function createCookieConsentBox($settings, $identifier)
	{
		//create cookie consent box
		$settings = json_decode($settings);
		if (isset($settings->cookie_notice)) {
			$gdpr_ob = $this->load->loadGdpr();
			$cookiebox = $gdpr_ob->showCookieScript($settings->cookie_notice, $identifier);
			return $cookiebox;
		} else {
			return "";
		}
	}
	function fpwdManage($content)
	{
		//forgot password template manage

		if (!isset($_POST['email']) && !isset($_GET['fpwd_token'])) {
			//show email template
			$content = self::fpwdReplacer("{update_password}", "{/update_password}", $content);
			$content = self::fpwdReplacer("{confirmation_message}", "{/confirmation_message}", $content);
			$content = str_replace("{insert_email}", "", $content);
			$content = str_replace("{/insert_email}", "", $content);
		} elseif (isset($_GET['fpwd_token'])) {
			//show new password field
			$content = self::fpwdReplacer("{insert_email}", "{/insert_email}", $content);
			$content = self::fpwdReplacer("{confirmation_message}", "{/confirmation_message}", $content);

			$content = str_replace("{update_password}", "", $content);
			$content = str_replace("{/update_password}", "", $content);
		} elseif (isset($_POST['email'])) {
			$content = self::fpwdReplacer("{insert_email}", "{/insert_email}", $content);
			$content = self::fpwdReplacer("{update_password}", "{/update_password}", $content);

			$content = str_replace("{confirmation_message}", "", $content);
			$content = str_replace("{/confirmation_message}", "", $content);
		}
		return $content;
	}
	function fpwdReplacer($start, $end, $content)
	{
		//forgot password template replacer
		preg_match_all("/(" . $start . ")+/", $content, $arr);
		for ($i = 0; $i < count($arr[0]); $i++) {
			preg_match_all("/(" . $start . ")+/", $content, $arrr);

			if (count($arrr[0]) < 1) {
				continue;
			}

			$count = (strpos($content, $end) + strlen($end)) - strpos($content, $start);
			$data = substr($content, strpos($content, $start), $count);
			$content = str_replace($data, "", $content);
		}
		return $content;
	}

	function changeLabel($id, $change_ob, $btnhtml = "")
	{
		//change page label
		$plugin_loader = false;
		if (isset($GLOBALS['plugin_loader'])) {
			$plugin_loader = $GLOBALS['plugin_loader'];
		}
		$mysqli = $this->mysqli;
		$pref = $this->dbpref;
		$table = $pref . "quick_pagefunnel";
		$id = $mysqli->real_escape_string($id);
		//$current=$mysqli->real_escape_string($current);
		//$rplc=$mysqli->real_escape_string($rplc);
		$token = time();

		$plugin_changes_array = array();
		foreach ($change_ob as $current => $rplc) {
			$current = $mysqli->real_escape_string($current);
			$rplc = $mysqli->real_escape_string($rplc);
			$plugin_changes_array[$current] = $rplc;
			$u1 = $mysqli->query("update `" . $table . "` set level='" . $rplc . "',token='" . $token . "' where funnelid='" . $id . "' and level='" . $current . "' and `token` not in ('" . $token . "')");
		}

		//   if($u1)
		//   {
		//   	$u1=$mysqli->query("update `".$table."` set level='".$current."',token='".$token."' where funnelid='".$id."' and level='".$rplc."' and token not in('".$token."')");
		//   }

		$funneltable = $pref . "quick_funnels";
		//mnubtn"
		$btnhtml = str_replace('mnubtn"', 'mnubtn" v-on:mousedown="activeMenuBtn($event)" v-on:contextmenu="showContext(true, $event)"', $btnhtml);

		$mysqli->query("update `" . $funneltable . "` set `labelhtml`='" . $btnhtml . "' where id=" . $id . "");

		if ($plugin_loader) {
			$plugin_loader->processPageLevelChange($id, $plugin_changes_array);
		}
		return 1;
	}
	function appEngineRenameDir($source, $dest, $permissions = 0755)
	{
		// Check for symlinks
		if (is_link($source)) {
			return symlink(readlink($source), $dest);
		}

		// Simple copy for a file
		if (is_file($source)) {
			return copy($source, $dest);
		}

		// Make destination directory
		if (!cf_dir_exists($dest)) {
			mkdir($dest, $permissions);
		}

		// Loop through the folder
		$dir = dir($source);
		while (false !== $entry = $dir->read()) {
			// Skip pointers
			if ($entry == '.' || $entry == '..') {
				continue;
			}

			// Deep copy directories
			self::appEngineRenameDir("$source/$entry", "$dest/$entry", $permissions);
		}

		// Clean up
		$dir->close();
		self::delAllFilesWithDir($source);
		return true;
	}
	function clearCache($funnel_id, $label)
	{
		//clear page cache
	}
	function mysqliEsc($data)
	{
		$mysqli = $this->mysqli;
		$data = $mysqli->real_escape_string($data);
		return $data;
	}
	function updatePageFunnelSettings($funnel_id, $label, $data, $clear_cache_only = 0)
	{
		//change page data
		//for editor changes cache will be cleared and for that set $clear_cache_only=1 and in $data param send {page_folder:name} only
		$plugin_loader = false;
		if (isset($GLOBALS['plugin_loader'])) {
			$plugin_loader = $GLOBALS['plugin_loader'];
		}
		$mysqli = $this->mysqli;
		$pref = $this->dbpref;
		$table = $pref . "quick_pagefunnel";

		$label = $this->mysqliEsc($label);
		$data = json_decode($data);

		$hasfunnel = self::getPageFunnel($funnel_id, 'a', $label);

		if ($hasfunnel !== 0) {
			$varient = $hasfunnel->varient;
			$dir = $this->getFunnel($funnel_id, '`flodername`');
			$dir = $dir->flodername;
			if ($this->do_route) {
				$dir = str_replace($this->routed_dir, $this->routed_dir_original, $dir);
			}
			$data->page_folder = $this->mysqliEsc($data->page_folder);
			$updateddir = $dir . "/" . $data->page_folder;
			$dir .= "/" . $hasfunnel->filename;

			if ($clear_cache_only === 0) {
				if ($dir == $updateddir) {
					$done_rename = 1;
				} else if (isset($data->force_rename) && ((int) $data->force_rename === 1)) {
					if (!cf_dir_exists($dir)) {
						die('Page not found, may be no template created.');
					}

					$renamed_folder = $data->page_folder;
					$is_page_exists = function () use ($mysqli, $table, $funnel_id, $label, $renamed_folder) {
						$qry = $mysqli->query("select `id` from `" . $table . "` where `funnelid`='" . $funnel_id . "' and `filename`='" . $renamed_folder . "' and `level` not in (" . $label . ")");

						if ($qry->num_rows > 0) {
							return true;
						}
						return false;
					};

					if ($is_page_exists()) {
						die('Page dir name already in use, try different.');
					}

					$done_rename = rename($dir, $updateddir);
				}
			}
			if ((isset($done_rename) && $done_rename) || $clear_cache_only === 1) {
				//delete cache	
				$cachefile_arr = array("html" => $updateddir, "js" => $updateddir . "/asset/js/", "css" => $updateddir . "/asset/css");
				foreach ($cachefile_arr as $indv_cache_file_index => $indv_cache_file_val) {
					$indv_cache_file_val = str_replace("//", '/', $indv_cache_file_val);

					self::deleteOlderFunnelFiles($indv_cache_file_val, $indv_cache_file_index, 'a', true);
					self::deleteOlderFunnelFiles($indv_cache_file_val, $indv_cache_file_index, 'b', true);
				}
				if ($clear_cache_only === 1) {
					return 1;
				}
				//echo $data->autoresponders;

				$inp_metadata = $this->mysqliEsc($data->metadata);


				$template_img_a = self::websiteToImgInit($data->page_folder, 'a');
				$template_img_b = self::websiteToImgInit($data->page_folder, 'b');

				$update_img_a = $mysqli->query("update `" . $table . "` set `templateimg`='" . $this->mysqliEsc($template_img_a) . "' where `level`='" . $this->mysqliEsc($label) . "' and `type`='a' and `funnelid`='" . $this->mysqliEsc($funnel_id) . "'");

				$update_img_b = $mysqli->query("update `" . $table . "` set `templateimg`='" . $this->mysqliEsc($template_img_b) . "' where `level`='" . $this->mysqliEsc($label) . "' and `type`='b' and `funnelid`='" . $this->mysqliEsc($funnel_id) . "'");

				$u = $mysqli->query("update `" . $table . "` set title='" . $this->mysqliEsc($data->page_title) . "',metadata='" . $inp_metadata . "',filename='" . $data->page_folder . "',pageheader='" . $this->mysqliEsc($data->header_scripts) . "',pagefooter='" . $this->mysqliEsc($data->footer_scripts) . "',active_type='" . $this->mysqliEsc($data->active_type) . "',hasabtest='" . $this->mysqliEsc($data->has_ab) . "',selares='" . $this->mysqliEsc($data->autoresponders) . "',lists='" . $this->mysqliEsc($data->lists) . "',product='" . $this->mysqliEsc($data->product) . "',category='" . $this->mysqliEsc($data->page_category) . "',membership='" . $this->mysqliEsc($data->membership_pages) . "',paymentmethod='" . $this->mysqliEsc($data->payment_method) . "',valid_inputs='" . $this->mysqliEsc($data->valid_inputs_page) . "',`settings`='" . $this->mysqliEsc($data->page_settings) . "'  where funnelid='" . $this->mysqliEsc($funnel_id) . "' and level='" . $this->mysqliEsc($label) . "'");

				$mysqli->query("update `" . $pref . "quick_funnels` set validinputs='" . $this->mysqliEsc($data->valid_inputs) . "',primarysmtp='" . $this->mysqliEsc($data->smtps) . "' where id='" . $this->mysqliEsc($funnel_id) . "'");

				if ($u) {
					$pageid = $mysqli->query("select `id` from `" . $table . "` where funnelid='" . $funnel_id . "' and level='" . $label . "'");
					$pageid = $pageid->fetch_object();
					$membership = $this->load->loadMember();

					if (in_array($data->page_category, array("register", "login", "membership", "forgotpassword"))) {
						$membership->deleteMembershipPage($pageid->id);
						$membership->setMembershipPage($pageid->id, $data->page_category);
						if ($data->page_category == "register") {
							if ($data->vrified_membership) {
								$membership->verifiedMembershipPages($pageid->id);
							} else {
								$membership->notVerifiedMembership($pageid->id);
							}
						}
					} else {
						$membership->deleteMembershipPage($pageid->id);
					}
					$plugin_loader->processPageSetupChange($funnel_id, $label);
					return 1;
				}
			} else {
				return "Unable to rename file";
			}
		} else {
			return "No template found for this page";
		}
	}
	function createIndex($basedir, $id)
	{
		//create base index file
		if ($this->do_route) {
			$required_files = "";
		} else {
			$dir = __DIR__;
			$dir = str_replace("\\", '/', $dir);
			$configfile = str_replace("library", "config.php", $dir);
			$required_files = "require_once('" . $configfile . "');
		  require_once('" . $dir . "/library.php');";
		}

		// $fp=fopen($basedir.'/index.php','w');
		$data = "<?php
	  " . $required_files . "
	  \$ob=new Library();
	  //auth cloud_funnels_no_conlict_index
	  \$ob->setInfo('mysqli',\$mysqli);
	  \$ob->setInfo('dbpref',\$dbpref);
	  \$funnel=\$ob->loadFunnel();
	  \$funnel_id=" . $id . ";
	  \$redirectto=\$funnel->goNext(__DIR__,\$funnel_id,'init');
	  if(\$redirectto===0)
	  {
		  \$ob->loadFourHunderdFour();
	  }
	  else
	  {
	  \$curren_loaded_url=str_replace('//','/',\$_SERVER['HTTP_HOST'].\$_SERVER['REQUEST_URI'].'/'.\$redirectto);
	  \$redirectto=getProtocol();
	  \$redirectto .=\$curren_loaded_url;	
      header('Location:'.\$redirectto.'/');
	  }
	  ?>";
		cf_fwrite($basedir . '/index.php', $data);
		//fclose($fp);
	}

	function readContent($funnelid, $label, $type)
	{
		//read page content including CSS
		$base_dir = self::getFunnel($funnelid, "`flodername`,`baseurl`,`validinputs`");
		$dir = str_replace('\\', '/', $base_dir->flodername);
		$cache_dir = $dir;

		if ($this->do_route) {
			$dir = str_replace($this->routed_dir, $this->routed_dir_original, $dir);
		}
		$folder = self::getPageFunnel($funnelid, $type, $label);
		$filee = 0;
		$file_css = 0;
		$file_js = 0;
		$csslink = '';
		if ($folder) {

			//Font Links
			if (!empty($folder->csslink)) {
				$csslinks =  json_decode($folder->csslink, true);
				$csslink = implode("\n", $csslinks);
			}
			$varient = $folder->varient;
			$folder = $folder->filename;
			$dir = $dir . '/' . $folder;
			$filee = $dir . '/template_' . $type . '.' . $varient . '.html';
			$file_css = $dir . '/asset/css/style_' . $type . '.' . $varient . '.css';
			$file_js = $dir . '/asset/js/script_' . $type . '.' . $varient . '.js';
		} else {
			$varient = "";
			$folder = "@folder@";
			$dir = $dir . '/' . $folder;
			$filee = $dir . '/template_' . $type . '.' . $varient . '.html';
			$file_css = $dir . '/asset/css/style_' . $type . '.' . $varient . '.css';
			$file_js = $dir . '/asset/js/script_' . $type . '.' . $varient . '.js';
		}
		$html = "";
		$css = "";
		$js = "";

		if (is_file($filee)) { //read html
			$fphtml = fopen($filee, 'r');
			if (filesize($filee) > 0) {
				$html = fread($fphtml, filesize($filee));
			}
		}
		if (is_file($file_css)) { //read css
			$fpcss = fopen($file_css, 'r');
			if (filesize($file_css) > 0) {
				$css = fread($fpcss, filesize($file_css));
			}
		}

		if (is_file($file_js)) {
			$fjs = fopen($file_js, 'r');
			if (filesize($file_js) > 0) {
				$js = fread($fjs, filesize($file_js));
			}
		}

		$arr = array();
		$arr['html'] = $html;
		$arr['css'] = $css;
		$arr['js'] = $js;
		$arr['link'] = $csslink;
		$arr['img_dir'] = $dir . '/asset/img';
		$arr['img_url'] = $base_dir->baseurl . '/' . $folder . '/asset/img';

		if ($this->do_route) {
			foreach ($arr as $arr_index => $arr_data) {
				$arr[$arr_index] = str_replace($this->routed_dir, $this->routed_dir_original, $arr_data);

				$arr[$arr_index] = self::pageRouteDecode($arr_data, $cache_dir, $folder);
			}
		}

		$inputs = explode(',', $base_dir->validinputs);
		array_push($inputs, 'name');
		array_push($inputs, 'email');
		array_push($inputs, 'firstname');
		array_push($inputs, 'lastname');
		array_push($inputs, 'phone');
		array_push($inputs, 'optional_products[]');
		array_push($inputs, 'confirm_checkout');
		array_push($inputs, 'oto_remove');
		array_push($inputs, 'submit');
		$inputs = implode(',', array_unique($inputs));
		$arr['input_names'] = explode(',', $inputs);

		return $arr;
	}
	function removeProductInCheckOutPage($product_id)
	{
		//input name oto_remove
		$orderform_session_name = 'order_form_data' . get_option('site_token');
		$orderform_session = $orderform_session_name;

		if (isset($_SESSION[$orderform_session])) {
			$order_data = $_SESSION[$orderform_session];
			if (isset($order_data['optional_products']) && is_array($order_data['optional_products'])) {
				$product = trim($product_id);
				$index = array_search($product, $order_data['optional_products']);
				if ($index !== false) {
					array_splice($order_data['optional_products'], $index, 1);
					$_SESSION[$orderform_session_name] = $order_data;
				}
			}
		}

		$url = getProtocol();
		$url .= trim($_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'], '/');
		$url = str_replace("oto_remove=", "oto_removed=", $url);
		@header('Location: ' . $url);
		echo "<script>window.location=`" . $url . "`;</script>";
		die();
	}
	function confirmCheckOut($funnel_id, $folder, $lbl, $category = 'checkout')
	{
		//confirm_checkout confirm checkout
		$mysqli = $this->mysqli;
		$orderform_session_name = 'order_form_data' . get_option('site_token');
		$orderform_session = $orderform_session_name;
		if (isset($_SESSION[$orderform_session]) && $category === 'checkout') {
			$orderform_session = $_SESSION[$orderform_session];
			$funnel_id = $mysqli->real_escape_string($funnel_id);
			$folder = $mysqli->real_escape_string($folder);
			$lbl = $mysqli->real_escape_string($lbl);
			$ab = (isset($_SESSION['currentab' . get_option('site_token')])) ? $_SESSION['currentab' . get_option('site_token')] : 'a';

			self::leadsStoreFromSavedFunnels($funnel_id, $folder, $ab, $orderform_session['data']);

			$url = get_option('install_url');
			$url .= '/index.php?page=do_payment';
			header('Location: ' . $url);
			echo "<script>window.location=`" . $url . "`;</script>";
			die();
		}
	}
	function checkOutContentGenerate($content)
	{
		$orderform_session_name = 'order_form_data' . get_option('site_token');
		$orderform_session = $orderform_session_name;
		if (isset($_SESSION[$orderform_session])) {
			$orderform_session = $_SESSION[$orderform_session];
			$saleob = $this->load->loadSell();
			$data = $saleob->checkOutDetailcreate($orderform_session['payment_method'], $orderform_session['product_id'], $orderform_session['optional_products']);
			$main_products = $data['main_products'];

			if (is_array($main_products)) {
				for ($i = 0; $i < count($main_products); $i++) {
					$main_products[$i] = "'" . $main_products[$i] . "'";
				}
				$mainproducts_str = implode(",", $main_products);
				$js = "<script>
			(function(){
				let products=[" . $mainproducts_str . "];
				let docs=document.querySelectorAll(`[data-autolink-oto='-1']`);
				docs.forEach(doc=>{
					try
					{
						let val=doc.getAttribute(`data-autolink-oto-product`);
						if(products.indexOf(val)>-1)
						{
							doc.onclick=function(){ alert(`Main Product(s) can not be removed`); return false;}
							doc.disabled=true;
						}
					}catch(err){console.log(err);}
				});
			})();
			</script>
			";

				$content = $content . $js;
			}

			unset($data['main_products']);

			$content = $saleob->productTemplatecreator($content, $data['all_products']);
			$content = arrayIndexToStr($content, $data);
		}
		return $content;
	}
	function processOTO($product, $funnel_id, $folder, $lbl, $category = 'oto')
	{
		//oto & one click upsell
		$orderform_session_name = 'order_form_data' . get_option('site_token');
		$orderform_session = $orderform_session_name;
		if (isset($_SESSION[$orderform_session]) && $category === 'oto') { //optional_products
			$orderform_session = $_SESSION[$orderform_session];
			if (isset($_GET['exit_oto'])) {
				goto exit_oto;
			}

			if (!(isset($orderform_session['optional_products']) && is_array($orderform_session['optional_products']))) {
				return false;
			} elseif (strlen(trim($product)) > 0) {
				array_push($orderform_session['optional_products'], trim($product));
				$_SESSION[$orderform_session_name]['optional_products'] = array_unique($orderform_session['optional_products']);
			} else {
				return false;
			}

			$mysqli = $this->mysqli;
			$pref = $this->dbpref;
			$table = $pref . "quick_pagefunnel";

			$funnel_id = $mysqli->real_escape_string($funnel_id);
			$folder = $mysqli->real_escape_string($folder);
			$lbl = $mysqli->real_escape_string($lbl);

			$ab = (isset($_SESSION['currentab' . get_option('site_token')])) ? $_SESSION['currentab' . get_option('site_token')] : 'a';




			self::leadsStoreFromSavedFunnels($funnel_id, $folder, $ab, $orderform_session['data']);

			if (isset($_GET['go_next_oto'])) {
				$next = base64_decode($_GET['go_next_oto']);
				if (filter_var($next, FILTER_VALIDATE_URL)) {
					@header('Location: ' . $next);
					echo "<script>window.location=`" . $next . "`;</script>";
					die();
				}
			}

			exit_oto:
			$url = $orderform_session['checkout_url'];
			@header("Location: " . $url);
			echo "<script>window.location=`" . $url . "`;</script>";
			die();

			/*$qry=$mysqli->query("select `filename`,`category` from `".$table."` where `funnelid`='".$funnel_id."' and `level`>".$lbl." and category='oto'");

		if($qry->num_rows>0)
		{

		}*/
		}
		return false;
	}

	function isOrderform($funnelid, $folder, $type, $data, $redirect = 1)
	{
		oto_chain:
		$oto_chain = 'oto_chain' . get_option('site_token');
		if (!isset($_SESSION[$oto_chain])) {
			if (isset($_SESSION['order_form_data' . get_option('site_token')]) && !(isset($_POST['confirm_checkout']))) {
				unset($_SESSION['order_form_data' . get_option('site_token')]);
			}
		}

		$mysqli = $this->mysqli;
		$pref = $this->dbpref;
		$table = $pref . "quick_pagefunnel";

		$funnelid = $mysqli->real_escape_string($funnelid);
		$folder = $mysqli->real_escape_string($folder);
		$type = $mysqli->real_escape_string($type);

		$randnum = mt_rand(0, 10);

		$orderby = "";
		if (($randnum % 2) == 0) {
			$orderby = " order by `id` desc";
		}

		$qry = $mysqli->query("select `id`,`level`,`type`,`category`,`product`,`paymentmethod`,`membership`,`lists` from `" . $table . "` where `filename`='" . $folder . "' and `funnelid`='" . $funnelid . "' and `type`='" . $type . "'" . $orderby . "");

		if ($qry->num_rows > 0) {
			$data = $qry->fetch_object();
			$pageid = $data->id;

			$funnel_smtp = self::getFunnel($funnelid, 'primarysmtp');
			$funnel_smtp = $funnel_smtp->primarysmtp;

			$excp_array = array('oto', 'checkout', 'orderform');
			if (isset($_SESSION[$oto_chain]) && array_search($data->category, $excp_array) === false) {
				unset($_SESSION[$oto_chain]);
				goto oto_chain;
			}

			if (!isset($_SESSION[$oto_chain]) && isset($_SESSION['order_form_data' . get_option('site_token')]) && (isset($_POST['confirm_checkout'])) && ($data->category !== 'checkout')) {
				unset($_SESSION['order_form_data' . get_option('site_token')]);
			}

			if ($data->category == "orderform") {
				$qryconfirmationpage = $mysqli->query("select `filename` from `" . $table . "` where `funnelid`=" . $funnelid . " and category='confirm' and `level`>" . $data->level . "");

				$qrycancelationpage = $mysqli->query("select `filename` from `" . $table . "` where `funnelid`=" . $funnelid . " and category='cancel' and `level`>" . $data->level . "");

				$has_checkout = false;
				$qrycheckoutpage = $mysqli->query("select `filename` from `" . $table . "` where `funnelid`=" . $funnelid . " and `category`='checkout' and `level`>" . $data->level . "");

				$has_oto = false;
				$qry_oto = $mysqli->query("select `id` from `" . $table . "` where `funnelid`=" . $funnelid . " and `category`='oto' and `level`>" . $data->level . "");

				if ($qry_oto->num_rows > 0) {
					$has_oto = true;
				}

				$funnelbase = self::getFunnel($funnelid);

				$baseurl = $funnelbase->baseurl;

				$page1 = $baseurl;
				$page2 = $baseurl;
				$page4 = $baseurl;

				if ($qryconfirmationpage->num_rows > 0) {
					$coget = $qryconfirmationpage->fetch_object();
					$page1 .= "/" . $coget->filename;
				}

				if ($qrycancelationpage->num_rows > 0) {
					$coget = $qrycancelationpage->fetch_object();
					$page2 .= "/" . $coget->filename;
				}

				if ($qrycheckoutpage->num_rows > 0) {
					$has_checkout = true;
					$coget = $qrycheckoutpage->fetch_object();
					$page4 .= '/' . $coget->filename;
				}

				$optional_products = array();
				if (isset($_POST['optional_products'])) {
					$optional_products = $_POST['optional_products'];
				}

				if ($has_checkout) {
					$url = $page4;
					$url = str_replace($this->routed_url, get_option('install_url'), $url);
				} else {
					$url = get_option('install_url');
					$url .= "/index.php?page=do_payment";
					$page4 = $url;
				}

				if ($this->do_route) {
					$page1 = str_replace($this->routed_url, get_option('install_url'), $page1);
					$page2 = str_replace($this->routed_url, get_option('install_url'), $page2);
					$page4 = str_replace($this->routed_url, get_option('install_url'), $page4);
				}

				$sandbox_session_name = "paypal_use_sandbox_" . get_option('site_token');
				if (isset($_SESSION[$sandbox_session_name])) {
					unset($_SESSION[$sandbox_session_name]);
				}

				$arr = array(
					'funnel_id' => $funnelid,
					'page_id' => $pageid,
					'folder' => $folder,
					'ab_type' => $data->type,
					'product_id' => $data->product,
					'lists' => $data->lists,
					'optional_products' => $optional_products,
					'payment_method' => $data->paymentmethod,
					'membership' => $data->membership,
					'smtp' => $funnel_smtp,
					'confirmation_url' => $page1,
					'cancel_url' => $page2,
					'checkout_url' => $page4,
					'data' => $_POST,
				);
				$_SESSION['order_form_data' . get_option('site_token')] = $arr;
				if ($has_oto) {
					$_SESSION[$oto_chain] = 1;
					return 2;
				}
				if ($redirect == 1) {
					header("Location: " . $url);
				}
				return 1;
			}
		} else {
			return 0;
		}
	}

	function goNext($currentdir, $funnelid, $doo = 'page_init')
	{
		//go to next label
		$mysqli = $this->mysqli;
		$pref = $this->dbpref;
		$table = $pref . "quick_pagefunnel";
		if ($doo == "init") {
			$qry = $mysqli->query("select `filename` from `" . $table . "` where funnelid=" . $funnelid . " and type='a' order by `level`");
			if ($qry) {
				$got_base_page = false;
				$temp_current_dir = str_replace("\\", "/", $currentdir);
				while ($r = $qry->fetch_object()) {
					if (is_file($temp_current_dir . "/" . $r->filename . "/index.php")) {
						$got_base_page = true;
						return $r->filename;
					}
				}
				if (!$got_base_page) {
					return 0;
				}
			} else {
				return 0;
			}
		} else {
			$baseurl = self::getFunnel($funnelid, "`baseurl`,`validinputs`");
			$currentdir = str_replace("\\", '/', $currentdir);
			$arr = explode('/', $currentdir);
			$currentfolder = $arr[((count($arr)) - 1)];
			$qry = $mysqli->query("select `id`,`title`,`metadata`,`level`,`hasabtest`,`csslink`,`pageheader`,`pagefooter`, `active_type`,`settings`,`category`,`varient`,(select `varient` from `" . $table . "` where funnelid=" . $funnelid . " and filename='" . $currentfolder . "' and `type`='b') as `b_varient` from `" . $table . "` where funnelid=" . $funnelid . " and filename='" . $currentfolder . "' and `type`='a'");
			$lvl = 0;
			if ($qry) {
				if ($r = $qry->fetch_object()) {
					$membership = $this->load->loadMember();
					//echo $membership->isMembershipPage($r->id);

					// $routed_logout=(strpos($_SERVER['REQUEST_URI'],'logout=1')>0)? 1:0;

					if (isset($_GET['logout'])) {
						if (isset($_SESSION['qfnl_membership_' . get_option('site_token') . '_' . $funnelid . ''])) {
							unset($_SESSION['qfnl_membership_' . get_option('site_token') . '_' . $funnelid . '']);
							unset($_SESSION['qfnl_gdpr_member' . get_option('site_token')]);
							$cookie_name = 'cookie_user_' . $funnelid . "_" . get_option('site_token');
							setcookie('cookie_user_' . $funnelid . "_" . get_option('site_token'), '', -1, '/');
							//echo  $cookie_name;
						}
					}
					$ismembership = $membership->isMembershipPage($r->id);

					$membership_page_screenshot = (isset($_GET['googlescreenshottoken']) && ($_GET['googlescreenshottoken'] == get_option('google_screenshot_token'))) ? true : false;

					if ($ismembership === "membership" && !$membership_page_screenshot) {
						if (!isset($_SESSION['qfnl_membership_' . get_option('site_token') . '_' . $funnelid . ''])) {
							$regqry = $mysqli->query("select `filename` from `" . $table . "` where funnelid=" . $funnelid . " and category='login'");
							if ($regqry->num_rows > 0) {
								$regqryob = $regqry->fetch_object();
								$regurl = $baseurl->baseurl . '/' . $regqryob->filename;
								if ($this->do_route) {
									$regurl = str_replace($this->routed_url, get_option('install_url'), $regurl);
								}
								$membership->remindPage($funnelid);
								header('Location: ' . $regurl . '');
							}
						} else {
							$membership->executeRemindedPage($funnelid);
						}
					}
					if ($ismembership && in_array($ismembership, array('login', 'register', 'forgotpassword'))) {
						if ($ismembership === 'login' && isset($_GET['after_login_action'])) {
							$after_login_action = base64_decode($_GET['after_login_action']);
							$membership->remindPage($funnelid, $after_login_action);

							if (isset($_GET['reset_on_register']) && ((int)$_GET['reset_on_register'] === 1)) {
								$_SESSION['qfnl_membership_reset_on_register_' . get_option('site_token') . '_' . $funnelid . ''] = 1;
							}
						}

						$tryrelogin = 0;
						lbtryrelogin:
						if (isset($_SESSION['qfnl_membership_' . get_option('site_token') . '_' . $funnelid . '']) && !$membership_page_screenshot) {
							$regqry = $mysqli->query("select `filename` from `" . $table . "` where funnelid=" . $funnelid . " and category='membership' order by level asc");
							if ($regqry->num_rows > 0) {
								$regqryob = $regqry->fetch_object();
								$regurl = $baseurl->baseurl . '/' . $regqryob->filename;
								if ($this->do_route) {
									$regurl = str_replace($this->routed_url, get_option('install_url'), $regurl);
								}
								header('Location: ' . $regurl . '');
							}
						} elseif (isset($_COOKIE['cookie_user_' . $funnelid . "_" . get_option('site_token')]) && $tryrelogin < 1) {
							$temp_cookie_user = explode("_br_", $_COOKIE['cookie_user_' . $funnelid . "_" . get_option('site_token')]);
							$cookieuserlogin = $membership->memberLogin($funnelid, $r->id, $temp_cookie_user[0], $temp_cookie_user[1], 1);
							if ($cookieuserlogin) {
								$_SESSION['qfnl_membership_' . get_option('site_token') . '_' . $funnelid . ''] = $cookieuserlogin;
								$_SESSION['qfnl_gdpr_member' . get_option('site_token')] = $cookieuserlogin;
								++$tryrelogin;
								goto lbtryrelogin;
							}
						}
					}

					$lvl = (int)$r->level;

					$viewsessionname = "qfnlv" . $funnelid . $lvl . "iewpage" . date('d-M-Y');
					$_SESSION['qfnl_view_visit_cookie_id' . get_option('site_token')] = $viewsessionname;

					$viewed = 0;
					if (!isset($_COOKIE[$viewsessionname]) && !isset($_GET['gmailloadtemplate'])) {
						setcookie($viewsessionname, '1', time() + 86400);
						self::addToVisitTable("visit", $viewsessionname, $r->id);
						++$viewed;
					}
					// ++$lvl;

					$b_varient = ($r->b_varient !== null) ?  $r->b_varient : false;

					$arr = array('next_url' => 0, 'has_ab' => $r->hasabtest, 'csslink' => $r->csslink, 'active_type' => $r->active_type, 'current_folder' => $currentfolder, 'viewed' => $viewed, 'is_membership' => $ismembership, 'title' => $r->title, 'metadata' => $r->metadata, 'header' => $r->pageheader, 'footer' => $r->pagefooter, 'settings' => $r->settings, 'label' => $r->level, 'category' => $r->category, 'varient' => $r->varient, 'b_varient' => $b_varient);

					$qry2 = $mysqli->query("select `filename` from `" . $table . "` where `funnelid`=" . $funnelid . " and `level`>" . $lvl . " order by `level` asc limit 1");
					if ($qry2) {
						if ($r2 = $qry2->fetch_object()) {
							$arr['next_url'] = $baseurl->baseurl . '/' . $r2->filename;
							if ($this->do_route) {
								$arr['next_url'] = str_replace($this->routed_url, get_option('install_url'), $arr['next_url']);
							}
							return $arr;
						} else {
							return $arr;
						}
					} else {
						return $arr;
					}
				} else {
					return 0;
				}
			} else {
				return 0;
			}
		}
	}
	function addToVisitTable($store = "visit", $token = 0, $pageid = 0, $convertoptinid = 0)
	{
		//add data to view or convert table to convert $store=convert
		$mysqli = $this->mysqli;
		$pref = $this->dbpref;
		$table = $pref . "site_visit_record";

		$user_os = getOS();
		$user_location = getLocation($this->ip);
		$user_location = (isset($user_location['country'])) ? $user_location['country'] : "Could Not Detect";
		$user_browser   = getBrowser();
		$user_device = getDevice();

		if ($store == "visit") {
			$mysqli->query("insert into `" . $table . "` (`session_id`, `visit_ip`,`device`,`os`,`browser`,`location`,`visit_pageid`, `convert_pageid`, `convert_optinid`,`convert_count`, `visitedon`, `convertedon`) values ('" . $token . "','" . $this->ip . "','" . $user_device . "','" . $user_os . "','" . $user_browser . "','" . $user_location . "','" . $pageid . "','" . $pageid . "','0','0','" . time() . "','" . time() . "')");
		} elseif ($store == "convert") {
			$mysqli->query("update `" . $table . "` set `convert_optinid`=concat(`convert_optinid`,'," . $convertoptinid . "'),`convert_count`=`convert_count`+1,`convertedon`='" . time() . "' where `session_id`='" . $token . "' and `visit_pageid`='" . $pageid . "'");
		}
	}
	function storeViews($funnel_id, $folder, $ab = 'a')
	{
		//function to store views
		$mysqli = $this->mysqli;
		$pref = $this->dbpref;
		$table = $pref . "quick_pagefunnel";
		$mysqli->query("update `" . $table . "` set viewcount=viewcount+1 where funnelid='" . $funnel_id . "' and filename='" . $folder . "' and type='" . $ab . "'");
	}
	function uploadAssets($filearr, $dir, $baseurl = "", $type = 'image')
	{
		if (!cf_dir_exists($dir)) {
			self::makeStepPath($dir);
		}
		$target_dir = $dir . "/";

		lbl:
		$filearr["name"] = str_replace(" ", "_", $filearr["name"]);
		$target_filename = time() . basename($filearr["name"]);

		$target_file = $target_dir . $target_filename;

		$uploaded = 0;

		if (!is_file($target_file)) {
			move_uploaded_file($filearr["tmp_name"], $target_file);
			++$uploaded;
		} else {
			goto lbl;
		}
		if ($uploaded > 0) {
			$url = $baseurl . '/' . $target_filename;
			$arr = array(
				'name' => $filearr["name"],
				'type' => $type,
				'src' => $url,
				'height' => 250,
				'width' => 350
			);
			return  $arr;
		} else {
			return 0;
		}
	}

	function leadsStoreFromSavedFunnels($funnel_id, $folder, $ab = 'a', $posts = array(), $orderformcheck = 1)
	{
		//leads store
		$mysqli = $this->mysqli;
		$pref = $this->dbpref;

		$optintable = $pref . 'quick_optins';
		$page_table = $pref . "quick_pagefunnel";

		$plugins_ob = false;
		if (isset($GLOBALS['plugin_loader'])) {
			$plugins_ob = $GLOBALS['plugin_loader'];
		}

		$total = $this->getOptinTotal();
		$site_token_for_dashboard = get_option('site_token');
		if ($_SESSION['user_plan_type' . $site_token_for_dashboard] == 2 && $total >= 10) {
			return 0;
		}

		if (count($posts) > 0 || $_SERVER['REQUEST_METHOD'] == "POST") {
			if ($orderformcheck) {
				/*redirect for order form*/
				$is_orderform = self::isOrderform($funnel_id, $folder, $ab, $posts);
				if ($is_orderform === 1) {
					return 0;
				} else if ($is_orderform === 2) {
					goto direct_process;
				}
			}
			//return 0;

			$storeleads = 1;
			$pageid = self::getPageFunnelDataByFolder($funnel_id, $folder, $ab);
			$membership = $this->load->loadMember();
			$ismembershippage = $membership->isMembershipPage($pageid->id);
			if ($ismembershippage !== 0) {
				$storeleads = 0;

				if ($ismembershippage == "register") {
					$storeleads = 1;
					$verifycode = "";
					if (isset($_GET['invitecode'])) {
						$verifycode = $_GET['invitecode'];
					}

					$required_name_member = (isset($posts['name'])) ? $posts['name'] : "";

					$regstat = $membership->createMember($funnel_id, $pageid->id, $required_name_member, $posts['email'], $posts['password'], $posts, $verifycode, $membership->isVerifiedMembershipPage($pageid->id));

					if ($regstat != 1) {
						$storeleads = 0;
					} else {
						if (isset($_SESSION['qfnl_membership_reset_on_register_' . get_option('site_token') . '_' . $funnel_id . ''])) {
							unset($_SESSION['qfnl_membership_reset_on_register_' . get_option('site_token') . '_' . $funnel_id . '']);
							$membership->executeRemindedPage($funnel_id, true);
						}
					}
				} elseif ($ismembershippage == "login") {
					$logstat = $membership->memberLogin($funnel_id, $pageid->id, $posts['email'], $posts['password']);
					if (is_array($logstat)) {
						$_SESSION['qfnl_membership_' . get_option('site_token') . '_' . $funnel_id . ''] = $logstat;
						$_SESSION['qfnl_gdpr_member' . get_option('site_token')] = $logstat;

						$membership->executeRemindedPage($funnel_id);
					}
				} elseif ($ismembershippage == "forgotpassword") {
					$fpwdstat = 1;
					if (isset($_POST['email'])) {
						$fpwdstat = $membership->createForgotPasswordOtpLink($_POST['email'], $funnel_id, $folder, "otp");
					} elseif (isset($_GET['fpwd_token'])) {
						//echo "-test-";
						$fpwdstat = $membership->createForgotPasswordOtpLink("", $funnel_id, $folder, 0);
					}
					return $fpwdstat;
				} elseif ($ismembershippage == "membership") {
					$fpwdstat = 1;
					$members = get_current_member($funnel_id);
					$update = $members['id'];
					if (isset($_POST['memberupdate'])) {
						$required_name_member = (isset($posts['name'])) ? $posts['name'] : "";
						$required_email_member = (isset($posts['email'])) ? $posts['email'] : "";
						$verifycode = "";
						if (isset($_GET['invitecode'])) {
							$verifycode = $_GET['invitecode'];
						}
						$regstat = $membership->createMember($funnel_id, $pageid->id, $required_name_member, $required_email_member, '', $posts, $verifycode, $membership->isVerifiedMembershipPage($pageid->id), $update);
						if ($regstat == 1) {
							$logstat = $membership->memberUpdate($funnel_id, $posts['email']);
							if (is_array($logstat)) {
								$_SESSION['qfnl_membership_' . get_option('site_token') . '_' . $funnel_id . ''] = $logstat;
								$_SESSION['qfnl_gdpr_member' . get_option('site_token')] = $logstat;
								$membership->executeRemindedPage($funnel_id);
							}
						}
					} else if (isset($_POST['passwordupdate'])) {
						$oldpassword = (isset($posts['password'])) ? $posts['oldpassword'] : "";
						$password = (isset($posts['password'])) ? $posts['password'] : "";
						$repass = (isset($posts['cpassword'])) ? $posts['cpassword'] : "";
						$verifycode = "";
						$passworddata = "";
						if (strlen($password) > 4 &&  strlen($repass)  > 4) {

							if ($password != $repass) {
								return get_option('pwd_mismatch_err');
							}
							$table = $this->dbpref . "quick_member";
							$get_member = $mysqli->query("SELECT `password` FROM `$table` WHERE `id`=" . $update . "");
							$member = $get_member->fetch_assoc();
							$oldpassword_db = $member['password'];

							if (password_verify($oldpassword, $oldpassword_db)) {

								$password = password_hash($password, PASSWORD_DEFAULT);
								$passworddata = " `password`= '$password' ";
								$updated_verfication_code = time();
								$updated_verfication_code .= substr(str_shuffle('qwertyuiopsdfghjklzxcvbnm1234567890'), 0, 5);
								$u = $mysqli->query("UPDATE `$table` SET $passworddata WHERE `id`=" . $update . "");
							}
						} else {
							return get_option('not_secure_password_alert');
						}
					}
				}
			}

			$datas = $posts;

			$name = "";
			$email = "";
			$password = "";
			foreach ($datas as $index => $data) {
				if (is_array($data) || is_object($data)) {
					if (is_array($data)) {
						foreach ($data as $data_index => $data_val) {
							$data[$data_index] = htmlentities($mysqli->real_escape_string($data_val));
						}
					}
					$data = json_encode($data);
				} else {
					$data = $mysqli->real_escape_string($data);
					$datas[$index] = htmlentities($data);
				}
				if ($index == 'submit') {
					unset($datas[$index]);
					continue;
				} elseif ($index == 'name') {
					$name = $data;
					unset($datas[$index]);
				} elseif ($index == 'email') {
					$email = $data;
					unset($datas[$index]);
				} elseif ($index == 'password') {
					$password = $data;
					unset($datas[$index]);
				} elseif ($index == 'reenterpassword') {
					unset($datas[$index]);
				} elseif ($index == 'rpassword') {
					unset($datas[$index]);
				} elseif ($index == 'product_ids') {
					unset($datas[$index]);
				}
			}
			$exfjsn = $mysqli->real_escape_string(json_encode($datas));


			if ($pageid) {

				if (count($posts) > 0) {
					if ($storeleads == 1) {
						if (strlen($name) > 0 || strlen($email) > 2 || strlen($exfjsn) > 2) {
							$checkpresentornot = $mysqli->query("select `id` from `" . $optintable . "` where `email`='" . $email . "' and `funnelid`='" . $funnel_id . "'");
							if ($checkpresentornot->num_rows < 1) {

								$mysqli->query("INSERT INTO `" . $optintable . "`(`funnelid`, `pageid`, `name`, `email`, `extras`, `ipaddr`, `exf`,`send_zap`,`send_pab`,`addedon`) VALUES ('" . $funnel_id . "','" . $pageid->id . "','" . $name . "','" . $email . "','" . $exfjsn . "','" . $this->ip . "','','0','0','" . time() . "')");
								$lastoptininsertid = $mysqli->insert_id;
								if (get_option('pabbly_post_rows') !== null) {
									update_option('pabbly_post_rows', 1);
								} else {
									add_option('pabbly_post_rows', 1);
								}
								self::apiCallToWebhook();
							} else {
								if (get_option('pabbly_post_rows') !== null) {
									update_option('pabbly_post_rows', 0);
								} else {
									add_option('pabbly_post_rows', 0);
								}
							}
						}

						if (isset($_SESSION['qfnl_view_visit_cookie_id' . get_option('site_token')])) {
							if ($_SESSION['qfnl_view_visit_cookie_id' . get_option('site_token')] == "qfnlv" . $funnel_id . $pageid->level . "iewpage" . date('d-M-Y')) {
								if (!isset($lastoptininsertid)) {
									$lastoptininsertid = "@";
								}
								self::addToVisitTable("convert", $_SESSION['qfnl_view_visit_cookie_id' . get_option('site_token')], $pageid->id, $lastoptininsertid);
							}
						}

						$mysqli->query("update `" . $page_table . "` set convertcount=convertcount+1 where id=" . $pageid->id . "");
						if (strlen($pageid->selares) > 0) { //add to autoresponder
							$autoresponders = explode('@', $pageid->selares);
							foreach ($autoresponders as $autoresponder) {
								if ($plugins_ob && strlen(trim($autoresponder)) > 0) {
									$plugins_ob->processAutoResponders($autoresponder, $name, $email, $datas);
								}
							}
						}
						//add to membership
						$membershipdata = array();
						if (strlen($pageid->membership) > 0) {
							if (strlen($password) == 0) {
								$membershiparr = array();
								$tempmembershiparr = explode(',', $pageid->membership);

								for ($i = 0; $i < count($tempmembershiparr); $i++) {
									if (is_numeric($tempmembershiparr[$i])) {
										array_push($membershiparr, $tempmembershiparr[$i]);
									}
								}
								if (count($membershiparr) > 0) {
									$member_ob = $this->load->loadMember();

									for ($i = 0; $i < count($membershiparr); $i++) {
										if ($membershiparr[$i] < 1) {
											continue;
										}
										$membershipdatasarr = $member_ob->hiddenAccountCreator($membershiparr[$i], $name, $email, $datas);

										if (is_array($membershipdatasarr)) {
											array_push($membershipdata, $membershipdatasarr);
										}
									}
								}
							}
						}
						//add to lists
						$lists_arr = explode('@', $pageid->lists);
						//print_r($lists_arr);
						if (count($lists_arr) > 0) {
							$list_ob = $this->load->createList();
							$dataforsequenceandlist = $datas;
							// $getleadsfromorder = $mysqli->query("SELECT * FROM `" . $pref . "all_sales` WHERE `purchase_email`='" . $posts['email'] . "'");
							if ($pageid->category === 'register') {
								$membershipdata = self::setRegisterPageData($funnel_id, $membershipdata, $posts);
								$posts['product_ids'] = array($pageid->product);
							}

							$addmulti = 0;
							if (isset($posts['product_ids'])) {
								$addmulti = 1;
								$dataforsequenceandlist['products'] = $posts['product_ids'];
							}

							if (count($membershipdata) > 0) {
								$dataforsequenceandlist['membership'] = $membershipdata;
							}

							for ($i = 0; $i < count($lists_arr); $i++) {
								if (!is_numeric($lists_arr[$i])) {
									continue;
								}
								$list_ob->addToList($lists_arr[$i], $name, $email, $dataforsequenceandlist, $addmulti);
							}
						}

						//return 0;

					}
				}
			}
		}

		if (isset($regstat)) {
			return $regstat;
		}
		if (isset($logstat)) {
			if (!is_array($logstat)) {
				return $logstat;
			}
		}
		direct_process:
		return 1;
	}

	function getOptinTotal()
	{
		$mysqli = $this->mysqli;
		$pref = $this->dbpref;
		$optintable = $pref . 'quick_optins';
		$run = $mysqli->query("SELECT COUNT(*) as `total` FROM `$optintable`");
		$result = $run->fetch_object();
		return $result->total;
	}
	function apiCallToWebhook()
	{
		$api_url = get_option('install_url') . '/index.php?page=callback_api&action=pabbly_api_callback';
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $api_url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, array('send_to_webhook' => get_option('site_token')));
		$res = curl_exec($ch);
		curl_close($ch);
	}
	function setRegisterPageData($funnel_id, $membershipdata, $posts)
	{
		$membershipdata['payment_id'] = 'NA';

		$funnel_page = get_funnel_pages($funnel_id);
		$login_url = "";
		foreach ($funnel_page as $key => $value) {
			if ($value['category'] === "login") {
				$login_url = str_replace('@@qfnl_install_url@@', get_option('install_url'), $value['url']);
				break;
			}
		}
		$membershipdata['login_url'] = $login_url;
		$membershipdata['email'] = $posts['email'];
		$membershipdata['password'] = $posts['password'];
		$membershipdata['payer_name'] = $posts['name'];
		$membershipdata['payer_email'] = $posts['email'];
		$membershipdata['total_paid'] = 0;
		return array($membershipdata);
	}
	function delLabel($funnel_id, $label)
	{
		$plugin_loader = false;
		if (isset($GLOBALS['plugin_loader'])) {
			$plugin_loader = $GLOBALS['plugin_loader'];
		}
		global $document_root;
		$mysqli = $this->mysqli;
		$pref = $this->dbpref;
		$funnel_id = $mysqli->real_escape_string($funnel_id);
		$label = $mysqli->real_escape_string($label);
		$table = $pref . "quick_pagefunnel";

		$funneldata = self::getFunnel($funnel_id);
		//print_r($funneldata);
		$ab = 'a';
		if ($funneldata) {
			lbl:
			$count = 0;
			$hasdata = self::getPageFunnel($funnel_id, $ab, $label);
			if ($hasdata) {
				$base_dir = str_replace('\\', '/', $document_root);
				$folder = str_replace('\\', '/', $funneldata->flodername . '/' . $hasdata->filename);
				$folder = str_replace($this->routed_dir, $this->routed_dir_original, $folder);
				self::delAllFilesWithDir($folder);
			}
			$mysqli->query("delete from `" . $table . "` where funnelid='" . $funnel_id . "' and level='" . $label . "'");
			//$mysqli->query("update `".$table."` set level=level-1 where funnelid='".$funnel_id."' and level>".$label."");
			/*
				'page_id'=>$pageid,
					'funnel_id'=>$funnel_id,
					'page_level'=>$lavel,
				*/
			$plugin_loader->processPageCreateDelete(array(
				'funnel_id' => $funnel_id,
				'page_level' => $label,
			), false);
		}
	}
	function copyLabel($funnel_id, $label)
	{
		$plugin_loader = false;
		if (isset($GLOBALS['plugin_loader'])) {
			$plugin_loader = $GLOBALS['plugin_loader'];
		}
		global $document_root;
		$mysqli = $this->mysqli;
		$pref = $this->dbpref;
		$funnel_id = $mysqli->real_escape_string($funnel_id);
		$label = $mysqli->real_escape_string($label);
		$table = $pref . "quick_pagefunnel";
		$page_slug = 'page-1';
		$funneldata = self::getFunnel($funnel_id);
		$ab = 'a';
		if ($funneldata) {

			// echo $page_slug;
			$hasdata = self::getPageFunnel($funnel_id, $ab, $label);

			$token = substr(str_shuffle('qwertyuiopasdfghjklzxcvbnm12346790'), 0, 5);
			$token .= time();

			if ($hasdata) {
				$count = 1;
				checkpageagain:
				$base_dir = str_replace('\\', '/', $document_root);

				$page_slug = $hasdata->filename . "-" . $count;
				$folder   = str_replace('\\', '/', $funneldata->flodername . '/' . $hasdata->filename);
				$folder   = str_replace($this->routed_dir, $this->routed_dir_original, $folder);
				if (self::checkPageName($page_slug, $funnel_id) === false) {
					$new_loc  = $folder . "-" . $count;
				} else {
					$count++;
					goto checkpageagain;
				}
				self::copyPageDir($folder, $new_loc, $token);
			}
			$new_level = self::getNewLevel($funnel_id);

			$template_img = self::websiteToImgInit($page_slug, $hasdata->type);
			$template_img = $mysqli->real_escape_string($template_img);
			$validinpututespage = $funneldata->validinputs;

			$pageinsert = $mysqli->query("INSERT INTO `" . $pref . "quick_pagefunnel`(`funnelid`, `name`, `title`,`metadata`, `filename`, `pageheader`, `pagefooter`, `type`, `level`,`category`,`custom`, `content`, `viewcount`, `bouncecount`, `convertcount`, `hasabtest`, `contentbpage`, `viewcoubtpage`, `convertcountbpage`, `bouncecountbpage`, `downsellpage`, `upsellpage`,`selares`,`lists`,`product`,`membership`,`paymentmethod`,`templateimg`,`valid_inputs`,`settings`, `date_created`, `token`,`varient`) VALUES ('" . $funnel_id . "','','','" . $hasdata->metadata . "','" . $page_slug . "','','','" . $hasdata->type . "','" . $new_level . "','" . $hasdata->category . "','0','','0','0','0','0','0','0','0','0','0','0','','0','0','0','0','" . $template_img . "','" . $validinpututespage . "','" . $hasdata->settings . "', '" . time() . "','" . $token . "','" . $token . "')");
			$pageid = $mysqli->insert_id;
			$plugin_loader->processPageCreateDelete(array(
				'page_id' => $pageid,
				'funnel_id' => $funnel_id,
				'page_level' => $new_level,
				'ab_type' => $hasdata->type
			), true);

			self::updatePageStep($funnel_id, $new_level, $hasdata->category, $page_slug);
			self::saveEditorData($funnel_id, $hasdata->type, $new_level, $hasdata->category, false, $page_slug, $page_slug, 2);

			$labelname = ucwords(str_replace("-", " ", $page_slug));
			echo json_encode(array("page_name" => $labelname, "category" => $hasdata->category));

			exit;
		}
	}
	function getNewLevel($funnel_id)
	{
		$mysqli = $this->mysqli;
		$pref = $this->dbpref;
		$level = 0;
		$table = $pref . "quick_pagefunnel";
		$row = $mysqli->query("SELECT `level` FROM `$table` WHERE `funnelid`='$funnel_id' ORDER BY `level` DESC LIMIT 1");
		if ($row->num_rows > 0) {
			$r = $row->fetch_object();
			$level = $r->level;
		}
		return (int)$level + 1;
	}
	function updatePageStep($funnel_id, $new_level, $category, $page_name)
	{
		$mysqli = $this->mysqli;
		$pref = $this->dbpref;
		$table = $pref . "quick_funnels";
		$row = $mysqli->query("SELECT `labelhtml` FROM `$table` WHERE `id`='$funnel_id'");
		if ($row->num_rows > 0) {
			$r = $row->fetch_object();
			$level = $r->labelhtml;
			$html_page_lvl = '<div class="d-flex align-items-center mnubtn-cont"  v-on:mousedown="activeMenuBtnN(\$event)" v-on:contextmenu="showContext(true, \$event)" lbl="' . $new_level . '" category="' . $category . '" ready="1"><button  class="btn  btn-block mnubtn-n" style="opacity: 1 !important">' . $page_name . '</button><div class="d-flex mt-2"><i v-on:click="visit()" title="Visit Page" class="fas fa-external-link-alt"></i></div></div>';
			$level .= $html_page_lvl;
			$level = $mysqli->real_escape_string($level);
			$mysqli->query("UPDATE `$table` SET `labelhtml`='$level' WHERE `id`='$funnel_id'");
		}
	}
	// check page name is availabel or not
	function checkPageName($filename, $funnel_id)
	{
		$mysqli = $this->mysqli;
		$pref = $this->dbpref;
		$table = $pref . "quick_pagefunnel";
		$row = $mysqli->query("SELECT `id` FROM `$table` WHERE `filename`='$filename' AND `funnelid`=$funnel_id");
		if ($row->num_rows > 0) {
			return true;
		} else {
			return false;
		}
	}
	function getTemplateinstallationfile()
	{
		$temp_dir =  $this->base_dir . "/public-assets/temp";
		return $temp_dir;
	}
	function installTemplate($id, $do = "download", $data = "")
	{
		set_time_limit(0);
		$do_clone = (filter_var($id, FILTER_VALIDATE_URL)) ? true : false;

		$temp_dir = self::getTemplateinstallationfile();

		$saved_temp_file =	get_option('temp_filename_template');

		if ($do == "download") {
			if (cf_dir_exists($temp_dir)) {
				self::delAllFilesWithDir($temp_dir);
			}

			if (!$do_clone) {
				$url = $this->template_url . "?download=" . $id;

				$iteration = 0;
				lbl:
				$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, $url);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
				curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
				curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
				curl_setopt($ch, CURLOPT_HTTPHEADER, array(
					"Host: cloudfunnels.in"
				));

				$res = curl_exec($ch);
				curl_close($ch);
			} else {
				$site_cloner = $this->load->cloneURL();
				if ($site_cloner->init($id, "get_session_site")) {
					$cloned_content = $site_cloner->sessionSite("get");
					$do = "save";
				} else {
					return 0;
				}
			}

			if (!$do_clone) {
				mkdir($temp_dir);
				cf_fwrite($temp_dir . '/' . $saved_temp_file . '.txt', $res);
			}
		}
		if ($do == 'save') {
			if (strpos($id, 'cloudfunnels.in/storage/templates/') !== false) {
				return 0;
			}
			$is_path_of_different_page = function () use ($data) {
				$mysqli = $this->mysqli;
				$dbpref = $this->dbpref;
				$table = $dbpref . 'quick_pagefunnel';
				$funnel_id = $mysqli->real_escape_string($data['funnel_id']);
				$lavel = $mysqli->real_escape_string($data['lavel']);
				$page = $mysqli->real_escape_string($data['page']);

				$chk_folder_existance = $mysqli->query("select `id` from `" . $table . "` where `funnelid`='" . $funnel_id . "' and `filename`='" . $page . "' and `level` not in(" . $lavel . ")");

				if ($chk_folder_existance->num_rows > 0) {
					die('Something wrong with the directory, please refresh and try again.');
				}
			};
			$is_path_of_different_page();

			$funnel = self::getFunnel($data['funnel_id']);
			$folder = $funnel->flodername;


			if ($this->do_route) {
				$folder = str_replace($this->routed_dir, $this->routed_dir_original, $folder);
			}


			$folder .= "/" . $data['page'];
			$url = $funnel->baseurl;
			$url .= "/" . $data['page'];

			$imgdir = $folder . "/asset/img/img-" . $data['type'];

			$imgdir_url = $url . "/asset/img/img-" . $data['type'];

			if (cf_dir_exists($imgdir)) {
				self::delAllFilesWithDir($imgdir);
			}
			$content = array();

			if (!is_file($temp_dir . "/" . $saved_temp_file . ".txt")) {
				die('Something Error Occurred During Template Installation');
			}
			$fp = fopen($temp_dir . "/" . $saved_temp_file . ".txt", "r");
			$temp_content = json_decode(fread($fp, filesize($temp_dir . "/" . $saved_temp_file . ".txt")));
			fclose($fp);


			$temp_content_forindex = (array)$temp_content;
			$content['html'] = base64_decode($temp_content_forindex['index.html']);

			$dom = new DOMDocument();
			libxml_use_internal_errors(true);
			$dom->loadHTML('<?xml encoding="utf-8" ?>' . $content['html']);
			$imgs = $dom->getElementsByTagName("img");
			foreach ($imgs as $img) {
				$src = $img->getAttribute("src");
				if (!filter_var($src, FILTER_VALIDATE_URL)) {
					if (strpos($src, "data:") === false) {
						$src = explode("/", $src);
						$lastindex = count($src);
						$lastindex = $lastindex - 1;
						$src = $src[$lastindex];
						$src = $imgdir_url . '/' . $src;
						$img->setAttribute('src', $src);
					}
				}
			}
			$content['html'] = $dom->saveHTML();

			if ((strpos($content['html'], "<body>") > 0) && (strpos($content['html'], "</body>") > 0)) {
				$content['html'] = substr($content['html'], (strpos($content['html'], "<body>") + 6), (strpos($content['html'], "</body>") - (strpos($content['html'], "<body>") + 6)));

				if ($do_clone) {
					$this->tempdestinimgurl = $imgdir_url;
					$content['html'] = str_replace("@qfnlcloneimglink@", $this->tempdestinimgurl . "/", $content['html']);
				}
			}

			$content['html'] = str_replace("%7B", "{", $content['html']);
			$content['html'] = str_replace("%7D", "}", $content['html']);


			$content['css'] = "";

			if (isset($temp_content->asset->css)) {
				$csscontents = (array)$temp_content->asset->css;
				foreach ($csscontents as $csscontents_index => $csscontents_data) {
					$csscontents_data = base64_decode($csscontents_data);

					if (strpos($csscontents_index, '.css') < 1) {
						continue;
					}
					if (strlen($csscontents_data) > 0) {
						$cssdata = $csscontents_data;

						if ($csscontents_index == "style.css") {
							$this->tempdestinimgurl = $imgdir_url;
							if ($do_clone) {
								$cssdata = str_replace("@qfnlcloneimglink@", $this->tempdestinimgurl . "/", $cssdata);
							} else {
								$cssdata = self::replaceUrlsInCSSLinks($cssdata);
							}
						}
						$content['css'] .= "/*" . $csscontents_index . "*/" . PHP_EOL;
						$content['css'] .= $cssdata;
					}
				}
			}
			$content['js'] = "";

			if (isset($temp_content->asset->js)) {
				$jsfiles = (array)$temp_content->asset->js;
				foreach ($jsfiles as $jsfiles_index => $jsfiles_content) {
					$content['js'] .= "/*" . $jsfiles_index . "*/" . PHP_EOL;
					if (strpos($jsfiles_index, '.js') < 1) {
						continue;
					}
					if (strlen($jsfiles_content) > 0) {
						$content['js'] .= base64_decode($jsfiles_content);
					}
				}
			}
			$content["html"] = self::decodeHTMLSpecialChars(str_replace("---@qfnl-img-link@---", "", $content["html"]));
			$content["css"] = str_replace("---@qfnl-img-link@---", "", $content["css"]);
			$content["js"] = str_replace("---@qfnl-img-link@---", "", $content["js"]);


			$reg_js_cleaner = "/(<script)+(.(?!(<script)))*(<\/script>)+/i";
			$content["html"] = preg_replace($reg_js_cleaner, "", $content["html"]);



			$content["html"] .= "<!--CF_SAVE_PERMITTED-->";

			self::saveEditorData($data['funnel_id'], $data['type'], $data['lavel'], $data['category'], $content, $data['page'], $data['page'], 0);

			if (isset($temp_content->asset->img)) {
				$allimages = (array)$temp_content->asset->img;
				foreach ($allimages as $allimages_index => $allimages_data) {
					$imgdata = base64_decode($allimages_data);
					cf_fwrite($imgdir . "/" . $allimages_index, $imgdata);
				}
			}

			self::delAllFilesWithDir($temp_dir);
			$zipinstallationdir = self::getZipTemplateDownloadLocation();
			self::delAllFilesWithDir($zipinstallationdir);
		}
	}
	function createQuoteForCSSUrls($content)
	{
		$cssurlptrn = "/(url\()+(.)*(\))+/";
		preg_match_all($cssurlptrn, $content, $arr);
		if (isset($arr[0]) && is_array($arr[0])) {
			$urls = $arr[0];
			for ($i = 0; $i < count($urls); $i++) {
				$url = $urls[$i];
				$backup = $url;
				$url = str_replace("url(", "", $url);
				$url = str_replace(")", "", $url);
				$url = str_replace("'", "", $url);
				$url = str_replace("\"", "", $url);
				$url = trim($url);
				$new_url = 'url(@dbquote@' . $url . '@dbquote@)';
				$content = str_replace($backup, $new_url, $content);
			}
		}
		return $content;
	}
	function replaceUrlsInCSSLinks($content)
	{ //img url replacer


		$cssurlptrn = "/(url\()+(.)*(\))+/";
		preg_match_all($cssurlptrn, $content, $match_arr);
		if (isset($match_arr[0]) && is_array($match_arr)) {
			$data_arr = $match_arr[0];
			for ($i = 0; $i < count($data_arr); $i++) {
				$url = $data_arr[$i];
				$backup = $url;
				$url = str_replace("url(", "", $url);
				$url = str_replace(")", "", $url);
				$url = str_replace("'", "", $url);
				$url = str_replace("\"", "", $url);

				if (strpos($url, "data:") !== false) {
					//return $backup;
					continue;
				}

				if (!filter_var($url, FILTER_VALIDATE_URL) || !(strpos($url, $this->routed_url) === false)) {
					$filearr = explode("/", $url);
					$imgurl = $this->tempdestinimgurl . "/";
					$content = str_replace($backup, "url(@dbquote@" . $imgurl . $filearr[count($filearr) - 1] . "@dbquote@)", $content);
				}
			}
		}


		return $content;
	}
	function showTemplates($type = "all", $abtype = "a", $search = "")
	{
		//show all templates
		$ssn = get_option('site_token');
		$ssn .= "_cache_downloadable_templates";
		load_template:
		if (!isset($_SESSION[$ssn])) {
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $this->template_url . "?view_templates=1");
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($ch, CURLOPT_HTTPHEADER, array(
				"Host: cloudfunnels.in"
			));
			$res = curl_exec($ch);
			curl_close($ch);
			$jsntemplates = $res;
			$data = json_decode($res);
			if (count($data) > 0) {
				$_SESSION[$ssn] = $jsntemplates;
			}
		} else {
			$data = json_decode(stripslashes($_SESSION[$ssn]));
		}

		if (json_last_error() && isset($_SESSION[$ssn])) {
			unset($_SESSION[$ssn]);
			goto load_template;
		}


		$div = "";
		$search_arr = array();
		if (strlen($search) > 3) {
			$search = strtolower($search);
			$search_arr = explode(" ", $search);
			$search_arr = array_merge(array($search), $search_arr);
		}

		$tagsearch = false;
		lbl:
		for ($i = 0; $i < count($data); $i++) {
			if (count($search_arr) < 1) {
				if (($type != "all") && ($data[$i]->type != $type)) {
					continue;
				}
			} else {
				$template_name = strtolower($data[$i]->name);
				if ($tagsearch) {
					$names_arr = array_merge(array($template_name), explode(" ", $template_name));
					$tags_arr = explode(',', strtolower($data[$i]->tags));
					$tags_arr = array_unique(array_merge($names_arr, $tags_arr));
				} else {
					$tags_arr = array($template_name);
				}
				$gotsearch = 0;
				foreach ($tags_arr as $tag) {
					if (in_array($tag, $search_arr)) {
						++$gotsearch;
						break;
					}
				}
				if ($gotsearch < 1) {
					continue;
				}
			}


			$required_pro = false;
			if (isset($data[$i]->pro) && $data[$i]->pro) {
				$required_pro = true;
			}

			$pr = $this->load->isPlusUser();
			$downloadable_id = ($pr || (isset($data[$i]->free) && $data[$i]->free && !$required_pro)) ? $data[$i]->template_index : "`NA`";

			global $pro_upgrade_url;

			$upgrade_btn = "<img src='assets/img/upgrade-btn.png' alt='Upgrade to Pro' style='max-height: 80%;'>";
			//<i class='fas fa-cart-plus'></i>

			$pro_required_text = "";
			if ($required_pro && !$pr) {
				$pro_required_text = "&nbsp;&nbsp;<a href='" . $pro_upgrade_url . "' class='badge badge-warning' target='_BLANK'>PRO</a>&nbsp;";
			}

			$icon_template_download = (is_numeric($downloadable_id)) ? "<i class='fas fa-chevron-circle-down'></i>" : "<a href='" . $pro_upgrade_url . "' target='_BLANK' data-bs-toggle='tooltip' title='Upgrade to pro'>" . $upgrade_btn . "</a>";
			$div .= "<div class='col-sm-4' style='display:inline-block;'><div class='card pnl'><div class='card-header'>" . $data[$i]->name . $pro_required_text . "</div><div style='padding:0px;' class='card-body' onmouseover='qfnlDownloadSpecificTemplate(this," . $downloadable_id . ",\"" . $abtype . "\")'>
		  <div class='masktemplateimg' style='background-image:url(\"" . $data[$i]->img . "\");background-size:cover;'></div> 
		  <a href='" . $data[$i]->preview_url . "' target='_BLANK' style='text-decoration:none;' data-bs-toggle='tooltip' title='Preview Template'><i class='fas fa-eye template-preview-icon'></i></a>
		 <div class='rounddownload'><h1>" . $icon_template_download . "</h1></div>
		  </div>
		  </div></div>";
		}
		if (!$tagsearch && strlen($div) < 5) {
			$tagsearch = true;
			goto lbl;
		}
		return $div;
	}
	function websiteToImg($siteURL)
	{
		//$siteURL="https://mechmarketers.com/cloudtest/test2/registration-page/?gmailloadtemplate=aseperatorbraek";

		/*
		$ch=curl_init();
	  	curl_setopt($ch,CURLOPT_URL,"https://www.googleapis.com/pagespeedonline/v5/runPagespeed?url=".$siteURL."&strategy=desktop");
	  	curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
	  	$googlePagespeedData=curl_exec($ch);
	  	curl_close($ch);
	  	$googlePagespeedData = json_decode($googlePagespeedData, true);

	   	$screenshot="";
	   	if(isset($googlePagespeedData['lighthouseResult']['audits']['final-screenshot']['details']['data']))
		{
			$screenshot = $googlePagespeedData['lighthouseResult']['audits']['final-screenshot']['details']['data'];
			$screenshot = str_replace(array('_','-'),array('/','+'),$screenshot);
		}
		*/

		if (get_option('disable_page_preview')) {
			update_option('google_screenshot_token', substr(str_shuffle('rgjkerbkgrtnl42353rtg3v43333AJK'), 1, 5));
			return "data:image/png;base64, ";
		}

		$ch = curl_init();
		//$api="https://capture-dot-cloudfunnels.appspot.com/create";
		$api = "http://104.236.35.191/create";
		//$api="http://localhost:3000/create";
		curl_setopt($ch, CURLOPT_URL, $api);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(array('target_url' => $siteURL)));
		curl_setopt($ch, CURLOPT_HTTPHEADER, array("content-type:application/json"));
		$data = json_decode(curl_exec($ch));
		$screenshot = "";
		if (isset($data->data)) {
			$screenshot = $data->data;
		}


		$screenshot = str_replace(array('_', '-'), array('/', '+'), $screenshot);
		update_option('google_screenshot_token', substr(str_shuffle('rgjkerbkgrtnl42353rtg3v43333AJK'), 1, 5));
		return "data:image/png;base64, " . $screenshot;
	}
	function websiteToImgInit($page, $type)
	{
		//init template 
		//gmailloadtemplate
		if (!get_option('google_screenshot_token')) {
			$token = substr(str_shuffle('abawrjkdbhfhfke33468efwdtdfbjbbfkkfrfhkhjjhh'), 0, 5);
			add_option('google_screenshot_token', $token);
		} else {
			$token = get_option('google_screenshot_token');
		}

		return $page . "/?gmailloadtemplate=" . $type . "seperator" . $token;
	}
	function createFunnelToken($id, $token)
	{
		$data = get_option('install_url');
		$data .= "/index.php?page=api_request@@cfbrk@@";
		$data .= cf_enc($id . '@' . $token);
		return base64_encode($data);
	}
	function decryptFunnelToken($token)
	{
		$token = explode('@', cf_enc($token, 'decrypt'));
		if (is_array($token) && count($token) === 2) {
			return array('id' => $token[0], 'token' => $token[1]);
		} else {
			return false;
		}
	}
	function getZipTemplateDownloadLocation()
	{
		return  $this->base_dir . "/public-assets/temp_site";
	}
	function uploadTeamplateZipAndGetURL($zip_file)
	{
		$temp_dir = self::getZipTemplateDownloadLocation();

		if (cf_dir_exists($temp_dir)) {
			self::delAllFilesWithDir($temp_dir);
		}
		mkdir($temp_dir);
		$f_token = time();
		$temp_dir .= '/' . $f_token;
		mkdir($temp_dir);

		$unique_key = time();
		$file = $temp_dir . '/site' . $unique_key . '.zip';

		$zip = new ZipArchive();
		move_uploaded_file($zip_file, $file);
		$zip->open($file);
		$zip->extractTo($temp_dir);
		$url = get_option('install_url');
		$url .= "/public-assets/temp_site/" . $f_token;
		$zip->close();

		$chk_count = 0;
		lbl:
		$index = $temp_dir . "/index.html";

		if ($chk_count > 1) {
			return t("All content of your template should be in base or inside a folder and it should contain \"index.html\"");
		}

		if (!file_exists($index)) {
			$dir_arr = scandir($temp_dir);
			if (count($dir_arr) === 4) {
				$dir = str_replace("/", "", $dir_arr[2]);
				$dir = str_replace("\\", "", $dir);
				$temp_dir .= '/' . $dir;
				$url .= "/" . str_replace(" ", "%20", $dir);
				++$chk_count;
				goto lbl;
			} else {
				return "All content of your template should be in base or inside a folder and it should contain \"index.html\"";
			}
		} else {
			$url .= "/index.html";
		}

		//unlink($file);
		return $url;
	}
	function getBlocks()
	{
		$base_dir = $this->base_dir;
		$blocks_file = $base_dir . '/assets/custom_blocks/blocks-custom.json';
		$dir = $base_dir . '/assets/custom_blocks/files';

		if (is_file($blocks_file)) {
			$blocks = array();
			$fp = fopen($blocks_file, 'r');
			$data = fread($fp, filesize($blocks_file));
			fclose($fp);
			$data = (array) json_decode($data);

			if (is_array($data) && count($data) > 0) {
				foreach ($data as $index => $file) {
					$file = $dir . '/' . $file;
					if (file_exists($file)) {
						ob_start();
						require_once($file);
						$data = ob_get_contents();
						ob_end_clean();
						$blocks[$index] = json_decode($data);
					}
				}
			}
			$blocks = array_reverse($blocks);
			return $blocks;
		} else {
			return json_encode(array([]));
		}
	}
	function delTemplateBlock($id)
	{
		$base_dir = $this->base_dir;
		$file = "";
		$blocks_file = $base_dir . '/assets/custom_blocks/blocks-custom.json';
		$dir = $base_dir . '/assets/custom_blocks/files';

		$fp = fopen($blocks_file, 'r');
		$blocks = fread($fp, filesize($blocks_file));
		fclose($fp);

		$blocks = (array) json_decode($blocks);
		if (is_array($blocks) && isset($blocks[$id])) {
			$file = $blocks[$id];
			unset($blocks[$id]);

			$fp = fopen($blocks_file, 'w');
			fwrite($fp, json_encode($blocks));
			fclose($fp);
		}

		if (file_exists($dir . '/' . $file)) {
			unlink($dir . '/' . $file);
		}
		return 1;
	}
	function addTemplateBlock($block)
	{
		$base_dir = $this->base_dir;
		$main_dir = $base_dir . '/assets/custom_blocks';
		$main_file = $main_dir . '/blocks-custom.json';
		$blocks = array();

		if (!cf_dir_exists($main_dir)) {
			mkdir($main_dir);
		}
		if (file_exists($main_file)) {
			$fp = fopen($main_file, 'r');
			$b = fread($fp, filesize($main_file));
			fclose($fp);
			$b = (array) json_decode($b);

			if (count($b) > 0) {
				$blocks = $b;
			}
		}

		$block = json_decode($block);

		if (isset($block->id)) {
			$id = $block->id;
			$dir = $main_dir . '/files';
			$file = $dir . '/' . $block->id . '.json';
			if (!cf_dir_exists($dir)) {
				mkdir($dir);
			}
			$blocks[$id] = $block->id . '.json';

			$fp = fopen($file, 'w');
			fwrite($fp, json_encode($block));
			fclose($fp);

			$fp = fopen($main_file, 'w');
			fwrite($fp, json_encode($blocks));
			fclose($fp);
		}

		return true;
	}
}
