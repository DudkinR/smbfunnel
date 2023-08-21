<?php
class Membershipqfnl
{
	var $mysqli;
	var $dbpref;
	var $load;
	var $ip;
	var $base_dir;
	var $secure;
	function __construct($arr)
	{
		$this->mysqli = $arr['mysqli'];
		$this->dbpref = $arr['dbpref'];
		if (isset($arr['load'])) {
			$this->load = $arr['load'];
			$this->secure = $this->load->secure();
		}
		$this->ip = $arr['ip'];

		if (isset($arr['base_dir'])) {
			$this->base_dir = $arr['base_dir'];
		}
	}
	function get_gravatar_image($email = null, $size = 90)
	{
		$default = 'mp';
		$grav_url = "https://www.gravatar.com/avatar/" . md5(strtolower(trim($email))) . "?d=" . urlencode($default) . "&s=" . $size;
		return $grav_url;
	}
	function membrshipTemplatecreator($html, $data, $by = 'array')
	{
		//if from array $by=array
		$pregtestfordom = "/(cf-loop(?=((=['\"]members)+)))+/";
		if (preg_match($pregtestfordom, $html, $arr)) {
			$html = @cfLoopCreator('members', $html);
		}

		if ($by == 'array') {
			preg_match('/({members})+/', $html, $arr);

			if (is_array($arr)) {
				for ($i = 0; $i < count($arr); $i++) {
					$start = strpos($html, "{members}");
					$end = strpos($html, "{/members}");
					if (!$start || !$end) {
						continue;
					}
					$end = $end + 9;
					$find = substr($html, $start, ($end - $start + 1));

					$str = "";

					for ($j = 0; $j < count($data); $j++) {
						$str .= self::tempMembershipReplacerCb($find, $data[$j]);
					}
					$html = str_replace($find, $str, $html);
				}
			}

			if (isset($data[0])) {
				$html = self::tempMembershipReplacerCb($html, $data[0]);
			}
		}

		return $html;
	}
	function tempMembershipReplacerCb($str, $datas)
	{
		foreach ($datas as $index => $data) {

			if (!is_array($data)) {
				$str = str_replace("{member." . $index . "}", $data, $str);
			}
		}
		$str = str_replace("{members}", "", $str);
		$str = str_replace("{/members}", "", $str);

		return $str;
	}
	function hiddenAccountCreator($regpageid, $name, $email, $datas)
	{
		$mysqli = $this->mysqli;
		$pref = $this->dbpref;
		$page_table = $pref . "quick_pagefunnel";

		$regpageid = $mysqli->real_escape_string($regpageid);

		$qry = $mysqli->query("select `funnelid`,`title`,`filename` from `" . $page_table . "` where `category`='login' and `funnelid` in (select `funnelid` from `" . $page_table . "` where `id`=" . $regpageid . ")");

		$title = "";
		if ($qry->num_rows > 0) {
			$r = $qry->fetch_object();
			$funnel_ob = $this->load->loadFunnel();
			$funnel = $funnel_ob->getFunnel($r->funnelid, '`baseurl`,`name`');
			$login_url = $funnel->baseurl;
			$funnel_name = $funnel->name;
			$login_url .= "/" . $r->filename;
			$title = $r->title;

			$arr = array('qwertyuiopasdfghjklzxcvbnm', 'QWERTYUIOPADFGHJKLZXCVBNM', '1234567890', '@*-_^');
			shuffle($arr);
			$password = "";
			for ($i = 0; $i < count($arr); $i++) {
				$password .= substr(str_shuffle($arr[$i]), 0, mt_rand(2, 5));
			}

			if (isset($datas['membership'])) {
				unset($datas['membership']);
			}






			$create = self::createMember($r->funnelid, $regpageid, $name, $email, $password, $datas, '', 0, 0, 1, 1);
			if ($create) {
				$arr = array(
					'name' => $name,
					'email' => $email,
					'password' => $password,
					'login_url' => $login_url,
					'funnel_name' => $funnel_name,
					'page_title' => $title,
				);

				foreach ($datas as $index => $data) {
					if (is_array($data)) {
						foreach ($data as $dindex => $dval) {
							$data[$dindex] = $mysqli->real_escape_string($dval);
						}
						$arr[$index] = json_encode($data);
					} else {
						$arr[$index] = $mysqli->real_escape_string($data);
					}
				}
				return $arr;
			} else {
				return 0;
			}
		} else {
			return 0;
		}
	}
	function createMember($funnelid, $pageid, $name, $email, $password, $exf, $verifycode, $verify = 1, $update = 0, $autocreate = 0, $multiaccount = 0)
	{
		$plugin_loader = false;
		if (isset($GLOBALS['plugin_loader'])) {
			$plugin_loader = $GLOBALS['plugin_loader'];
		}

		if (filter_var($email, FILTER_VALIDATE_EMAIL) && (strlen($password) > 2 || $update > 0)) {
			//member registration
			$mysqli = $this->mysqli;
			$pref = $this->dbpref;
			$table = $this->dbpref . "quick_member";
			$name = htmlentities($mysqli->real_escape_string($name));
			$email = $mysqli->real_escape_string($email);
			$password = $mysqli->real_escape_string($password);
			$verifycode = $mysqli->real_escape_string($verifycode);
			$members = get_current_member($funnelid);
			$mid = $members['id'];

			$total = $this->getTotalCount();
			$site_token_for_dashboard = get_option('site_token');
			if ($_SESSION['user_plan_type' . $site_token_for_dashboard] == 2 && $total >= 10) {
				return 0;
			}

			$edatas = $exf;
			$data_to_provide_toplugins = array(
				'name' => $name,
				'email' => $email,
				'funnel_id' => $funnelid,
				'page_id' => $pageid,
			);
			foreach ($edatas as $index => $edata) {
				if (is_array($edata)) {
					foreach ($edata as $edata_index => $edata_val) {
						$edata[$edata_index] = htmlentities($mysqli->real_escape_string($edata_val));
					}
					$edata = json_encode($edata);
				} else {
					$edata = htmlentities($mysqli->real_escape_string($edata));
				}
				$edatas[$index] = $edata;
				if ($index == 'submit') {
					unset($edatas[$index]);
					continue;
				} elseif ($index == 'name') {
					unset($edatas[$index]);
				} elseif ($index == 'email') {
					unset($edatas[$index]);
				} elseif ($index == 'password') {
					unset($edatas[$index]);
				} elseif ($index == 'reenterpassword') {
					$repass = $edatas[$index];
					unset($edatas[$index]);
				}
			}

			if (is_array($edatas)) {
				$data_to_provide_toplugins['data'] = $edatas;
			}

			$exfjsn = $mysqli->real_escape_string(json_encode($edatas));
			$verified = 0;

			if ($update > 0) {
				$passworddata = "";
				if (strlen($password) > 4) {
					if ($password != $repass) {
						return get_option('pwd_mismatch_err');
					}
					$password = password_hash($password, PASSWORD_DEFAULT);
					$passworddata = ",`password`='" . $password . "'";
				}
				//`verifycode`
				$updated_verfication_code = time();
				$updated_verfication_code .= substr(str_shuffle('qwertyuiopsdfghjklzxcvbnm1234567890'), 0, 5);
				$check = $mysqli->query("SELECT `id` FROM `$table` WHERE `email`='$email' AND `id`!='$mid' AND `funnelid`='$funnelid'");
				if ($check->num_rows <= 0) {
					$u = $mysqli->query("UPDATE `$table` SET `name`='$name',`email`='$email',`verifycode`='$updated_verfication_code' " . $passworddata . ",`exf`='$exfjsn' where `id`=" . $update . "");
					if ($u) {
						if ($plugin_loader) {
							$data_to_provide_toplugins['id'] = $update;
							$plugin_loader->processMembership($data_to_provide_toplugins, 'update');
						}
						return 1;
					}
				} else {
					return get_option('already_email_err');
				}
			} else {
				$funnelob = $this->load->loadFunnel();
				$funneldata = $funnelob->getFunnel($funnelid);

				$validinputs = explode(",", $funneldata->validinputs);

				if (in_array('reenterpassword', $validinputs)) {
					if ($autocreate < 1) {
						if ($password != $repass) {
							return get_option('pwd_mismatch_err');
						}
					}
				}
				if (!$this->secure->isSecurePassword($password)) {
					return get_option('not_secure_password_alert');
				}

				if ($verify == 1) {
					$qry = $mysqli->query("select id from `" . $table . "` where verifycode='" . $verifycode . "' and verified not in('1') and funnelid='" . $funnelid . "' and pageid='" . $pageid . "'");
					if ($qry->num_rows > 0) {
						++$verified;
					}
				} else {
					++$verified;
				}
				if ($verified > 0) {
					$chk = $mysqli->query("select `id` from `" . $table . "` where email='" . $email . "' and funnelid='" . $funnelid . "'");
					if ($chk->num_rows < 1 || ($multiaccount == 1)) {
						if ($verify == 1) {
							$password = password_hash($password, PASSWORD_DEFAULT);
							$mysqli->query("update `" . $table . "` set `name`='" . $name . "',`email`='" . $email . "',`password`='" . $password . "',`verified`='1',`ip_created`='" . $this->ip . "',`ip_lastsignin`='N/A',`date_created`='" . time() . "',`date_lastsignin`='N/A',`exf`='" . $exfjsn . "' where `verifycode`='" . $verifycode . "'");

							$get_id_qry = $mysqli->query("select `id` from `" . $table . "` where `verifycode`='" . $verifycode . "' and `email`='" . $email . "'");
							if ($plugin_loader && $get_id_qry->num_rows > 0) {
								$get_id_qry_r = $get_id_qry->fetch_object();
								$data_to_provide_toplugins['id'] = $get_id_qry_r->id;
								$plugin_loader->processMembership($data_to_provide_toplugins, 'update');
							}
						} else {
							$verifycode = time();
							$verifycode .= substr(str_shuffle('qwertyuiopasdfghjklzxcvbnm1234567890'), 0, 5);
							$date = date('d-M-Y h:ia');
							$password = password_hash($password, PASSWORD_DEFAULT);
							$in = $mysqli->query("insert into `" . $table . "` (`funnelid`, `pageid`, `name`, `email`, `password`, `verified`, `verifycode`, `date_verifycodegen`, `ip_created`, `ip_lastsignin`, `date_created`, `date_lastsignin`,`valid`,`mailed`, `exf`) values ('" . $funnelid . "','" . $pageid . "','" . $name . "','" . $email . "','" . $password . "','1','" . $verifycode . "','','" . $this->ip . "','N/A','" . time() . "','N/A','1','0','" . $exfjsn . "')");
							if (!isset($GLOBALS['sales_membershiparray'])) {
								$GLOBALS['sales_membershiparray'] = array();
							}
							array_push($GLOBALS['sales_membershiparray'], $mysqli->insert_id);
							if ($multiaccount == 1) {
								$mysqli->query("update `" . $table . "` set `password`='" . $password . "' where `email`='" . $email . "' and `funnelid`='" . $funnelid . "'");
							}

							if ($plugin_loader && $in) {
								$data_to_provide_toplugins['id'] = $mysqli->insert_id;
								$plugin_loader->processMembership($data_to_provide_toplugins, 'add');
							}
						}
						return 1;/*Registered*/
					} else {
						return get_option('re_register_err');
					}
				} else {
					return get_option('un_auth_access_err');/*not verified*/
				}
			}
		} else {
			if (!(strlen($password) > 2 || $update > 0)) {
				return get_option('not_secure_password_alert');
			}
			return get_option('invalid_email_err');/*Invalid type email*/
		}
	}

	function getTotalCount()
	{
		$mysqli = $this->mysqli;
		$table = $this->dbpref . "quick_member";
		$qry_str = $mysqli->query("SELECT COUNT(*) AS `total` FROM `$table`");
		$run = $qry_str->fetch_object();
		return $run->total;
	}

	function getMemberDetailForPlugins($data)
	{
		//this will be used only in plugin
		$mysqli = $this->mysqli;
		$pref = $this->dbpref;
		$table = $this->dbpref . "quick_member";
		$qry_str = "select * from `" . $table . "`" . $data;
		$arr = array();
		$qry = $mysqli->query($qry_str);
		if ($qry->num_rows > 0) {
			while ($r = $qry->fetch_assoc()) {
				foreach ($r as $index => $val) {
					$do_unset = false;
					if ($index == 'funnelid') {
						$r['funnel_id'] = $val;
						$do_unset = true;
					} else if ($index == 'pageid') {
						$r['page_id'] = $val;
						$do_unset = true;
					} else if ($index == 'verifycode') {
						$r['verify_code'] = $val;
						$do_unset = true;
					}
					if ($do_unset) {
						unset($r[$index]);
					}

					if ($index == 'password') {
						unset($r['password']);
					}
				}
				array_push($arr, $r);
			}
		}
		return $arr;
	}
	function memberLogin($funnelid, $pageid, $email, $password, $cookieuser = 0)
	{
		//member login
		$mysqli = $this->mysqli;
		$pref = $this->dbpref;
		$table = $this->dbpref . "quick_member";

		$sale_table = $pref . "all_sales";

		$email = $mysqli->real_escape_string($email);
		$password = $mysqli->real_escape_string($password);

		$page = self::isMembershipPage($pageid);
		if ($page == "login") {
			$qry = $mysqli->query("select * from `" . $table . "` where `email`='" . $email . "' and `funnelid`='" . $funnelid . "' order by `id` desc limit 1");

			if ($qry->num_rows > 0) {
				$data = $qry->fetch_object();
				if ($data->valid != '1') {
					return get_option('qfnl_membership_cancelation_message');
				}
				$auth_cookie_user = 0;
				if ($cookieuser > 0) {
					if (hash_hmac('sha1', $data->verifycode, get_option('site_token')) == $password) {
						$auth_cookie_user = 1;
					}
				}
				if (password_verify($password, $data->password) || $auth_cookie_user > 0) {
					$exfs = json_decode($data->exf);
					//$err=json_last_error_msg();	
					$arr = array();
					$arr['name'] = $data->name;
					$name_arr = explode(" ", $arr['name']);
					if (!isset($exfs->first_name)) {
						$arr['first_name'] = $name_arr[0];
					}
					if (!isset($exfs->last_name)) {

						$arr['last_name'] = "";
						if (count($name_arr) > 1) {
							$arr['last_name'] = $name_arr[count($name_arr) - 1];
						}
					}
					$arr['email'] = $data->email;
					$arr['funnel_id'] = $data->funnelid;
					$arr['date'] = '';
					if (strlen($data->date_created) > 1) {
						$arr['date'] = date('d-M-Y h:ia', $data->date_created);
					}

					$products = array();

					$mysqli->query("update `" . $table . "` set `ip_lastsignin`='" . $this->ip . "',`date_lastsignin`='" . time() . "' where `id`='" . $data->id . "'");


					// $chk_qry="select `id`,`productid` from `".$sale_table."` where `membership` like '".$data->id.",%' or `membership` like '%,".$data->id.",%' or `membership` like '%,".$data->id."' and `payment_id`='".$exfs['payment_id']."'";

					$sell_members = array();
					$get_all = $mysqli->query("select `id` from `" . $table . "` where `email`='" . $email . "' and `funnelid`='" . $funnelid . "'");

					while ($r_get_all = $get_all->fetch_object()) {
						$search_str = "`membership`='" . $r_get_all->id . "' or `membership` like '" . $r_get_all->id . ",%' or `membership` like '%," . $r_get_all->id . ",%' or `membership` like '%," . $r_get_all->id . "'";
						array_push($sell_members, $search_str);
					}

					$sell_members = implode(" or ", $sell_members);

					$saledata = $mysqli->query("select distinct(`productid`) from `" . $sale_table . "` where (" . $sell_members . ")");

					if ($saledata->num_rows > 0) {
						while ($productid = $saledata->fetch_object()) {
							array_push($products, $productid->productid);
						}
					}

					$arr['session_product_ids'] = $products;

					foreach ($exfs as $index => $exf) {
						$arr[$index] = $exf;
					}
					if (isset($_POST['remember_user'])) {
						$cookie_data = $email . "_br_" . hash_hmac('sha1', $data->verifycode, get_option('site_token'));

						setcookie('cookie_user_' . $funnelid . "_" . get_option('site_token'), $cookie_data, time() + 86400 * 14, "/");
					}
					return $arr;
				} else {
					return get_option('invalid_login_credntials_err');
				}
			} else {
				return get_option('usr_does_not_exist_err');
			}
		}
	}
	function memberUpdate($funnelid, $email)
	{
		//member login
		$mysqli = $this->mysqli;
		$pref = $this->dbpref;
		$table = $this->dbpref . "quick_member";

		$sale_table = $pref . "all_sales";

		$email = $mysqli->real_escape_string($email);
		$qry = $mysqli->query("select * from `" . $table . "` where `email`='" . $email . "' and `funnelid`='" . $funnelid . "' order by `id` desc limit 1");

		if ($qry->num_rows > 0) {
			$data = $qry->fetch_object();
			$exfs = json_decode($data->exf);
			$arr = array();
			$arr['name'] = $data->name;
			$name_arr = explode(" ", $arr['name']);
			if (!isset($exfs->first_name)) {
				$arr['first_name'] = $name_arr[0];
			}
			if (!isset($exfs->last_name)) {

				$arr['last_name'] = "";
				if (count($name_arr) > 1) {
					$arr['last_name'] = $name_arr[count($name_arr) - 1];
				}
			}
			$arr['email'] = $data->email;
			$arr['funnel_id'] = $data->funnelid;
			$arr['date'] = '';
			if (strlen($data->date_created) > 1) {
				$arr['date'] = date('d-M-Y h:ia', $data->date_created);
			}

			$products = array();

			$mysqli->query("update `" . $table . "` set `ip_lastsignin`='" . $this->ip . "',`date_lastsignin`='" . time() . "' where `id`='" . $data->id . "'");


			// $chk_qry="select `id`,`productid` from `".$sale_table."` where `membership` like '".$data->id.",%' or `membership` like '%,".$data->id.",%' or `membership` like '%,".$data->id."' and `payment_id`='".$exfs['payment_id']."'";

			$sell_members = array();
			$get_all = $mysqli->query("select `id` from `" . $table . "` where `email`='" . $email . "' and `funnelid`='" . $funnelid . "'");

			while ($r_get_all = $get_all->fetch_object()) {
				$search_str = "`membership`='" . $r_get_all->id . "' or `membership` like '" . $r_get_all->id . ",%' or `membership` like '%," . $r_get_all->id . ",%' or `membership` like '%," . $r_get_all->id . "'";
				array_push($sell_members, $search_str);
			}

			$sell_members = implode(" or ", $sell_members);

			$saledata = $mysqli->query("select distinct(`productid`) from `" . $sale_table . "` where (" . $sell_members . ")");

			if ($saledata->num_rows > 0) {
				while ($productid = $saledata->fetch_object()) {
					array_push($products, $productid->productid);
				}
			}

			$arr['session_product_ids'] = $products;

			foreach ($exfs as $index => $exf) {
				$arr[$index] = $exf;
			}
			if (isset($_POST['remember_user'])) {
				$cookie_data = $email . "_br_" . hash_hmac('sha1', $data->verifycode, get_option('site_token'));

				setcookie('cookie_user_' . $funnelid . "_" . get_option('site_token'), $cookie_data, time() + 86400 * 14, "/");
			}
			return $arr;
		} else {
			return get_option('usr_does_not_exist_err');
		}
	}
	function getProductAccessForaMember($funnel_id, $email, $active_only = false)
	{
		//Get product access for a member & specific funnel id
		$mysqli = $this->mysqli;
		$products = array();
		$sell_members = array();
		$table = $this->dbpref . "quick_member";
		$sale_table = $this->dbpref . "all_sales";

		$get_all = $mysqli->query("select `id` from `" . $table . "` where `email`='" . $email . "' and `funnelid`='" . $funnel_id . "'");

		while ($r_get_all = $get_all->fetch_object()) {
			$search_str = "`membership`='" . $r_get_all->id . "' or `membership` like '" . $r_get_all->id . ",%' or `membership` like '%," . $r_get_all->id . ",%' or `membership` like '%," . $r_get_all->id . "'";
			array_push($sell_members, $search_str);
		}

		$sell_members = implode(" or ", $sell_members);

		$active_query = "";
		if ($active_only) {
			$active_query = " and `valid`='1'";
		}

		if (strlen(trim($sell_members)) > 0) {
			$saledata = $mysqli->query("select distinct(`productid`) from `" . $sale_table . "` where (" . $sell_members . ")" . $active_query);

			if ($saledata->num_rows > 0) {
				while ($productid = $saledata->fetch_object()) {
					array_push($products, $productid->productid);
				}
			}
		}

		return $products;
	}
	function isMember($funnelid, $email)
	{
		$mysqli = $this->mysqli;
		$pref = $this->dbpref;
		$table = $this->dbpref . "quick_member";

		$funnelid = $mysqli->real_escape_string($funnelid);
		$email = $mysqli->real_escape_string($email);

		$qry = $mysqli->query("select * from `" . $table . "` where `funnelid`='" . $funnelid . "' and`email`='" . $email . "'");

		if ($qry) {
			if ($qry->num_rows > 0) {
				return $qry->fetch_object();
			} else {
				return 0;
			}
		} else {
			return 0;
		}
	}

	function createForgotPasswordOtpLink($email, $funnelid, $folder, $do = "otp")
	{
		//forgot password link send
		if (isset($_SESSION['fpwdemail' . get_option('site_token')]) && isset($_GET['fpwd_token'])) {
			$email = $_SESSION['fpwdemail' . get_option('site_token')];
		}

		if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
			$mysqli = $this->mysqli;
			$pref = $this->dbpref;
			$table = $this->dbpref . "quick_member";
			$funnel_ob = $this->load->loadFunnel();
			$funnel = $funnel_ob->getFunnel($funnelid);
			$member = self::isMember($funnelid, $email);
			if ($member) {
				if ($do === "otp") {
					$otp = time();
					$otp .= substr(str_shuffle('qweriopasdfghjklzxcvbnmQWERTYUIOPASDFGHJKLZXCVBNM1234567890@-_+'), 1, 10);
					$mysqli->query("update `" . $table . "` set `verifycode`='" . $otp . "' where `funnelid`='" . $funnelid . "' and `email`='" . $email . "'");

					$mailob = $this->load->loadSequence();

					$reseturl = $funnel->baseurl . "/" . $folder . "/?fpwd_token=" . $otp;
					if ($funnel_ob->do_route) {
						$reseturl = str_replace($funnel_ob->routed_url, get_option('install_url'), $reseturl);
					}

					$fpwdemail = get_option('members_fpwd_mail');
					$fpwdemail = str_replace("{link}", $reseturl, $fpwdemail);
					$fpwdemail = explode("@fpwdemlbrk@", $fpwdemail);

					$email_body = "";
					$email_title = $fpwdemail[0];
					if (isset($fpwdemail[1])) {
						$email_body = $fpwdemail[1];
						$email_body = stripslashes($email_body);
					}

					$smtp_to_use_for_mailsending = (strlen($funnel->primarysmtp) > 0) ? $funnel->primarysmtp : get_option('default_smtp');

					$otp_sent = $mailob->sendMail($smtp_to_use_for_mailsending, $member->name, $member->email, $email_title, $email_body, "");

					if (!$otp_sent) {
						if (isset($_POST['email'])) {
							unset($_POST['email']);
						}
						return get_option('snd_email_err');
					}

					$_SESSION['fpwdemail' . get_option('site_token')] = $email;

					return 1;
				} elseif ($_GET['fpwd_token']) {
					$token = $mysqli->real_escape_string($_GET['fpwd_token']);
					if (isset($_POST['password'])) {
						if (!$this->secure->isSecurePassword($_POST['password'])) {
							return get_option('not_secure_password_alert');
						}
						if (isset($_POST['reenterpassword'])) {
							if ($_POST['password'] != $_POST['reenterpassword']) {
								return get_option('pwd_mismatch_err');
							}
						}

						$password = $mysqli->real_escape_string($_POST['password']);
						$password = password_hash($password, PASSWORD_DEFAULT);

						$qry = $mysqli->query("select `id` from `" . $table . "` where `funnelid`='" . $funnelid . "' and `email`='" . $email . "' and `verifycode`='" . $token . "'");
						$up = 0;
						if ($qry->num_rows > 0) {
							$mysqli->query("update `" . $table . "` set `password`='" . $password . "',`verifycode`='" . str_shuffle($token) . "@expired' where `funnelid`='" . $funnelid . "' and `email`='" . $email . "' and `verifycode`='" . $token . "'");
							++$up;
						}

						if ($up) {
							$qry = $mysqli->query("select `filename` from `" . $pref . "quick_pagefunnel` where `category`='login' and `funnelid`='" . $funnelid . "'");
							if ($qry) {
								if ($qry->num_rows > 0) {
									$result = $qry->fetch_object();
									$loginurl = $funnel->baseurl . "/" . $result->filename;
									$loginurl = str_replace($funnel_ob->routed_url, get_option('install_url'), $loginurl);
									header('Location: ' . $loginurl);
								} else {
									return "<font color='green'>Password Updated Successfully.</font>";
								}
							}

							return 1;
						} else {
							$protocol = $this->load->getProtocol();
							$bsurlforforgot = $protocol . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

							$bsurlforforgot = str_replace("fpwd_token", "fpwd_token_invalid", $bsurlforforgot);

							$fpwd_auth_error = str_replace('{link}', "<a href='" . $bsurlforforgot . "'>", get_option('fpwd_auth_error'));
							$fpwd_auth_error = str_replace('{/link}', "</a>", get_option('fpwd_auth_error'));

							return $fpwd_auth_error;
						}
					}
				}
			} else {
				if (isset($_POST['email'])) {
					unset($_POST['email']);
				}
				return get_option('usr_does_not_exist_err');
			}
		} else {
			return get_option('invalid_email_err');
		}
	}

	function varifiedMembersipLinkReplacer($content)
	{
		$reg = "/(@-cf-verified-){1}(\d)+(-member-@){1}/";
		$content = preg_replace_callback($reg, function ($chk) {
			if (isset($chk[0])) {
				$chk[0] = str_replace("@-cf-verified-", "", $chk[0]);
				$chk[0] = str_replace("-member-@", "", $chk[0]);
				$chk[0] = (int)$chk[0];
				$url = self::createMembeshipRegLink($chk[0], 0, "");
				return $url;
			}
		}, $content);
		return $content;
	}
	function createMembeshipRegLink($funnelid, $pageid = 0, $url = '')
	{
		//verified membership link create
		$mysqli = $this->mysqli;
		$pref = $this->dbpref;
		$table = $this->dbpref . "quick_member";
		$otp = time();
		$otp .= substr(str_shuffle('qweriopasdfghjklzxcvbnmQWERTYUIOPASDFGHJKLZXCVBNM1234567890@'), 1, 10);

		if ($pageid == 0) {
			$chk = $mysqli->query("select `id` from `" . $pref . "quick_pagefunnel` where `funnelid`='" . $funnelid . "' and `category`='register'");
			if ($r = $chk->fetch_object()) {
				$pageid = $r->id;
			}
		}

		if (is_numeric($pageid) && $pageid > 0) {
			//`funnelid`, `pageid`, `name`, `email`, `password`, `verified`, `verifycode`, `date_verifycodegen`, `ip_created`, `ip_lastsignin`, `date_created`, `date_lastsignin`,`valid`, `exf`
			$in = $mysqli->query("insert into `" . $table . "` (`funnelid`,`pageid`,`name`, `email`, `password`, `verified`, `verifycode`, `date_verifycodegen`, `ip_created`, `ip_lastsignin`, `date_created`, `date_lastsignin`,`valid`,`exf`) values ('" . $funnelid . "','" . $pageid . "','','','','0','" . $otp . "','','','','','','1','')");
		}

		$url .= "?invitecode=" . $otp;
		return $url;
	}
	function getAllMembershipRegistrationPages()
	{
		$mysqli = $this->mysqli;
		$pref = $this->dbpref;
		$table = $this->dbpref . "quick_pagefunnel";
		$arr = array();
		$qry = $mysqli->query("select id,title,funnelid,filename from `" . $table . "` where category='register' and type='a'");
		if ($qry->num_rows > 0) {
			while ($r = $qry->fetch_object()) {
				$funnel_ob = $this->load->loadFunnel();
				$funnel = $funnel_ob->getFunnel($r->funnelid);
				$url = $funnel->baseurl . "/" . $r->filename;
				$title = $funnel->name;
				if (strlen($r->filename) > 0) {
					$title .= "> " . $r->filename;
				}
				$arr[$r->id] = "<a href='" . $url . "' target='_BLANK'>" . $title . "</a>";
			}
		}
		return $arr;
	}
	function isMembershipPage($pageid)
	{
		//check and get whether membership page or not
		$pageid = (int)$pageid;
		$reg_pages = array();
		$login_pages = array();
		$memberarea_pages = array();
		$forgotpassword_pages = array();

		if (strlen(get_option('register_pages')) > 0) {
			$reg_pages = explode('@', get_option('register_pages'));
		}

		if (strlen(get_option('login_pages')) > 0) {
			$login_pages = explode('@', get_option('login_pages'));
		}

		if (strlen(get_option('forgotpassword_pages')) > 0) {
			$forgotpassword_pages = explode('@', get_option('forgotpassword_pages'));
		}

		if (strlen(get_option('membership_pages')) > 0) {
			$memberarea_pages = explode('@', get_option('membership_pages'));
		}

		if (in_array($pageid, $reg_pages)) {
			return "register";
		} elseif (in_array($pageid, $login_pages)) {
			return "login";
		} elseif (in_array($pageid, $memberarea_pages)) {
			return 'membership';
		} elseif (in_array($pageid, $forgotpassword_pages)) {
			return 'forgotpassword';
		} else {
			return 0;
		}
	}
	function setMembershipPage($pageid, $type)
	{
		//add membership page
		$pageid = (int)$pageid;

		if (self::isMembershipPage($pageid) !== 0) {
			self::deleteMembershipPage($pageid);
		}
		$type .= "_pages";
		$pages = array();
		if (strlen(get_option($type)) > 0) {
			$pages = explode('@', get_option($type));
		}
		if (!in_array($pageid, $pages)) {
			array_push($pages, $pageid);
		}
		update_option($type, implode('@', $pages));
	}
	function deleteMembershipPage($pageid)
	{
		$pageid = (int)$pageid;
		$page = self::isMembershipPage($pageid);
		if ($page !== 0) {
			$type = $page . "_pages";
			$pages = explode('@', get_option($type));
			if (in_array($pageid, $pages)) {
				unset($pages[array_search($pageid, $pages)]);
				//array_push($pages,$pageid);
			}
			update_option($type, implode('@', $pages));
		}
	}
	function verifiedMembershipPages($pageid)
	{
		$pageid = (int)$pageid;
		//add as verified membership page
		$pages = array();
		if (strlen(get_option("verified_membersip")) > 0) {
			$pages = explode('@', get_option("verified_membersip"));
		}
		if (!in_array($pageid, $pages)) {
			array_push($pages, $pageid);
		}
		update_option("verified_membersip", implode('@', $pages));
	}
	function notVerifiedMembership($pageid)
	{
		//delete from verified membership page link
		$pageid = (int)$pageid;
		$page = self::isVerifiedMembershipPage($pageid);
		if ($page) {
			$pages = array();
			$pages = explode('@', get_option("verified_membersip"));
			if (in_array($pageid, $pages)) {
				unset($pages[array_search($pageid, $pages)]);
			}
			update_option("verified_membersip", implode('@', $pages));
		}
	}
	function isVerifiedMembershipPage($pageid)
	{
		$pageid = (int)$pageid;
		if (self::isMembershipPage($pageid) === 0) {
			return 0;
		} else {
			$pages = array();
			if (strlen(get_option("verified_membersip")) > 0) {
				$pages = explode('@', get_option("verified_membersip"));
			}
			if (in_array($pageid, $pages)) {
				return 1;
			} else {
				return 0;
			}
		}
	}

	function visualOptisForFunnels($funnel_id = 'all', $pagecount = 0, $search = "")
	{
		$mysqli = $this->mysqli;
		$pref = $this->dbpref;
		$table = $pref . "quick_member";

		$tempdatebetween_arr = dateBetween('date_created');
		$datebetween = $tempdatebetween_arr[1];
		$datebetween_all = $tempdatebetween_arr[0];

		$countsql = "select count(distinct(`email`)) as totaloptins from `" . $table . "` where `email` not in('',' ')" . $datebetween;

		$baseurl = "";
		$extrafields = 0;
		$total = 0;
		if (is_numeric($funnel_id)) {
			$countsql = "select count(distinct(`email`)) as totaloptins from `" . $table . "` where funnelid='" . $funnel_id . "' and `email` not in ('',' ')" . $datebetween;

			$total_qry = $mysqli->query($countsql);
			if ($total_qry) {
				if ($res = $total_qry->fetch_object()) {
					$total = $res->totaloptins;
				}
			}

			$order_by = "`id` desc";
			if (isset($_GET['arrange_records_order'])) {
				$order_by = base64_decode($_GET['arrange_records_order']);
			}

			$max_qry = "and `id` in (select max(`id`) from `" . $table . "` where `funnelid`=" . $funnel_id . " group by `email`)";

			if ($pagecount == 0) {
				$sql = "select * from `" . $table . "` where funnelid='" . $funnel_id . "' " . $max_qry . " and `email` not in('',' ')" . $datebetween . " order by " . $order_by . " limit " . get_option('qfnl_max_records_per_page') . "";
			} else {
				$pagecount = ($pagecount * get_option('qfnl_max_records_per_page')) - get_option('qfnl_max_records_per_page');

				$sql = "select * from `" . $table . "` where funnelid='" . $funnel_id . "' " . $max_qry . " and `email` not in('',' ')" . $datebetween . " order by " . $order_by . " limit " . $pagecount . ", " . get_option('qfnl_max_records_per_page') . "";
			}
			$funnel = $this->load->loadFunnel();
			$funnel_data = $funnel->getFunnel($funnel_id);
			$extrafields = $funnel_data->validinputs;
			$baseurl = $funnel_data->baseurl;
			if (strlen($extrafields) > 0) {
				$extrafields = explode(',', $extrafields);
			} else {
				$extrafields = 0;
			}
		}
		if (strlen($search) > 0 || isset($_POST["searchmember"])) {
			if (isset($_POST["searchmember"])) {
				$search = $_POST["searchmember"];
			}
			$search = $mysqli->real_escape_string($search);
			$sql = "select * from `" . $table . "` where funnelid='" . $funnel_id . "' " . $max_qry . " and (name like '%" . $search . "%' or email like '%" . $search . "%' or exf like '%:\"%" . $search . "%\"%' or `ip_created` like '%" . $search . "%' or `ip_lastsignin` like '%" . $search . "%') and `email` not in('',' ') order by id desc";
		}

		$qry = $mysqli->query($sql);
		return array('leads' => $qry, 'total' => $total, 'extracols' => $extrafields, 'base_url' => $baseurl);
	}
	function deleteMemberByFunnel($email, $funnel_id)
	{
		$mysqli = $this->mysqli;
		$pref = $this->dbpref;
		$email = $mysqli->real_escape_string($email);
		$funnel_id = $mysqli->real_escape_string($funnel_id);
		$table = $pref . "quick_member";
		$mysqli->query("delete from `" . $table . "` where `email`='" . $email . "' and `funnelid`='" . $funnel_id . "'");
	}
	function deleteOptin($id, $by = 'id')
	{
		//delete optin
		//$by may be id or funnelid or pageid

		$plugin_ob = false;
		if (isset($GLOBALS['plugin_loader'])) {
			$plugin_ob = $GLOBALS['plugin_loader'];
		}

		$mysqli = $this->mysqli;
		$pref = $this->dbpref;
		$id = $mysqli->real_escape_string($id);
		if (is_numeric($id)) {
			if ($by != 'id') {
				$by = "'" . $id . "'";
			}
		}

		$table = $pref . "quick_member";
		$mysqli->query("delete from `" . $table . "` where `" . $by . "`=" . $id . "");
		if ($plugin_ob) {
			$plugin_ob->processMembership($id, 'delete');
		}
	}
	function remindPage($funnel_id, $url = null)
	{
		$token = get_option('site_token');
		$str = "membership_page_remind_" . $token . $funnel_id;
		$protocol = getProtocol();

		if (!isset($_GET['logout'])) {
			$_SESSION[$str] = ($url !== null) ? $url : $protocol . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
		} elseif (isset($_SESSION[$str])) {
			unset($_SESSION[$str]);
		}
	}
	function executeRemindedPage($funnel_id, $reset_only = false)
	{
		$token = get_option('site_token');
		$str = "membership_page_remind_" . $token . $funnel_id;

		if (isset($_SESSION[$str])) {
			$url = $_SESSION[$str];
			unset($_SESSION[$str]);
			if (!$reset_only && filter_var($url, FILTER_VALIDATE_URL)) {
				header('Location: ' . $url);
				echo "window.location= `" . $url . "`";
				die();
			}
		}
	}
	function exportMemberToCsv($funnelid)
	{
		//export members to csv
		$mysqli = $this->mysqli;
		$pref = $this->dbpref;
		$table = $pref . "quick_member";
		$page_table = $pref . 'quick_pagefunnel';
		$funnel_ob = $this->load->loadFunnel();
		$input_fields = "";
		$filename = "";

		$funnelid = $mysqli->real_escape_string($funnelid);

		$getpagedataqry = $mysqli->query("select `valid_inputs`,`filename` from `" . $page_table . "` where `funnelid`='" . $funnelid . "' and `category`='register'");

		if ($getpagedataqry->num_rows) {
			while ($r = $getpagedataqry->fetch_object()) {
				$input_fields = $r->valid_inputs;
				$filename = $r->filename;
			}
		}

		if (strlen($input_fields) < 1) {
			$getfetfunnel = $funnel_ob->getFunnel($funnelid);
			if ($getfetfunnel) {
				$input_fields = $getfetfunnel->validinputs;
			}
		}
		$input_fields = explode(",", $input_fields);

		$optinqry = $mysqli->query("select * from `" . $table . "` where `funnelid`='" . $funnelid . "' and `email` not in('',' ') order by id desc");

		//headers
		$csvheaders = array("#");
		$csvheaders = array_merge($csvheaders, $input_fields, array('Register_IP', 'Last_Assigned_IP', 'Added_On', 'Last_Activity'));

		$output_filename = $filename . '.csv';
		$output_handle = @fopen('php://output', 'w');

		header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		header('Content-Description: File Transfer');
		header('Content-type: text/csv');
		header('Content-Disposition: attachment; filename=' . $output_filename);
		header('Expires: 0');
		header('Pragma: public');
		fputcsv($output_handle, $csvheaders);

		if ($optinqry->num_rows > 0) {
			$count = 0;
			while ($r = $optinqry->fetch_assoc()) {
				++$count;
				$outputarr = array($count);
				$exfarr = (array)json_decode($r['exf']);

				if (is_numeric($r["date_lastsignin"])) {
					$r["date_lastsignin"] = date('d-M-Y h:ia', $r["date_lastsignin"]);
				}
				$r["date_created"] = date('d-M-Y h:ia', $r["date_created"]);
				$r['password'] = "***";

				for ($i = 1; $i < count($csvheaders); $i++) {
					//array('Register_IP','Last_Assigned_IP','Added_On','Last_Activity')
					if ($csvheaders[$i] == "Register_IP") {
						$csvheaders[$i] = "ip_created";
					}
					if ($csvheaders[$i] == "Last_Assigned_IP") {
						$csvheaders[$i] = "ip_lastsignin";
					}
					if ($csvheaders[$i] == "Added_On") {
						$csvheaders[$i] = "date_created";
					}
					if ($csvheaders[$i] == "Last_Activity") {
						$csvheaders[$i] = "date_lastsignin";
					}

					if (isset($r[$csvheaders[$i]])) {
						array_push($outputarr, $r[$csvheaders[$i]]);
					} elseif (isset($exfarr[$csvheaders[$i]])) {
						array_push($outputarr, $exfarr[$csvheaders[$i]]);
					} else {
						array_push($outputarr, "");
					}
				}
				fputcsv($output_handle, $outputarr);
			}
		}

		fclose($output_handle);
		die();
	}
}
