<?php
if (isset($_POST['check_by_zap']) && isset($_POST['cf_zap_auth'])) {
	$page = "callback_api";
}

/*Auths*/
if ($page == 'create_config') {
	$headers = $this->load->loadBootstrap(1);
	$headers .= $this->load->loadVue();
	$headers .= $this->load->loadScript('request');
	$headers .= $this->load->loadStyle('style');
	$headers .= $this->load->loadJSTranslator();
	$footer = $this->load->loadScript('auth_control');

	if (isset($_POST['generate_translation'])) {
		registerTranslation($_POST['generate_translation']);
		$footer .= "<script>
		authcreate.language_selected=true;
		authcreate.current_language=`" . $_POST['generate_translation'] . "`;
		</script>";
	}

	require_once($this->view_dir . "/create_config.php");
} elseif ($page == 'index') {
	header('Location: index.php?page=login');
} elseif ($page == "login") {

	$site_tokenforlogin = get_option('site_token');

	if (isset($_SESSION['user' . $site_tokenforlogin])) {
		header('Location: index.php?page=' . $_SESSION['first_page' . get_option('site_token')] . '');
	}
	$security = $this->load->secure();
	$security->manageRate(0);
	//echo $this->load->loadStyle('animate');
	$headers = $this->load->loadBootstrap();
	$headers .= $this->load->loadVue();
	$headers .= $this->load->loadScript('request');
	$headers .= $this->load->loadScript('visual_loader');
	$headers .= $this->load->loadStyle('visual-loader');
	$headers .= $this->load->loadStyle('style');
	$headers .= $this->load->loadJSTranslator();
	$footer = $this->load->loadScript('auth_control');
	require_once($this->view_dir . "/login.php");

	if (isset($_GET['autologin'])) {
		echo "<script>authcreate.autologin=1;</script>";
	}
} elseif ($page == "forgot_password") {
	$security = $this->load->secure();
	$security->manageRate(0);
	$headers = $this->load->loadBootstrap(1);
	$headers .= $this->load->loadVue();
	$headers .= $this->load->loadScript('request');
	$headers .= $this->load->loadScript('visual_loader');
	$headers .= $this->load->loadStyle('style');
	$headers .= $this->load->loadStyle('visual-loader');
	$headers .= $this->load->loadJSTranslator();
	$footer = $this->load->loadScript('auth_control');

	require_once($this->view_dir . "/forgot_password.php");
} elseif ($page == "logout") {
	session_destroy();
	if (isset($_COOKIE['qfnlreuser' . get_option('site_token')])) {
		setcookie('qfnlreuser' . get_option('site_token'), '', -1);
	}
	header('Location: index.php?page=login');
}

//with login
elseif ($page == 'no_permission') {
	$GLOBALS['inside_administration_page'] = false;
	$header = $this->load->loadBootstrap();
	$page_description = "";
	$createoredit = "No Permission";
	$header .= $this->load->loadStyle('style');
	$this->load->view($createoredit, $header, $page . ".php", "", array('page_description' => $page_description));
} elseif ($page == 'all_funnels') {
	$header = $this->load->loadBootstrap();
	$header .= $this->load->loadVue();
	$header .= $this->load->loadScript('request');
	$header .= $this->load->loadScript('visual_loader');
	$header .= $this->load->loadStyle('visual-loader');
	$header .= $this->load->loadStyle('style');
	$header .= "<script src='assets/js/vv/js/popper.min.js'></script>";
	//$header .=$this->load->loadStyle('animate');
	$funnel = $this->load->loadFunnel();
	$page_description = "Create, edit, manage your funnels and sites";
	$createoredit = "Funnels and Sites";

	$tutorial_link = "https://cloudfunnels.in/membership/members#tutorials_funnel";
	$this->load->view($createoredit, $header, $page . ".php", "", array('page_description' => $page_description, 'funnel' => $funnel, 'tutorial_link' => $tutorial_link));
} elseif ($page == 'membership_funnels') {
	$header = $this->load->loadBootstrap();
	$header .= $this->load->loadVue();
	$header .= $this->load->loadScript('request');
	$header .= $this->load->loadScript('visual_loader');
	$header .= $this->load->loadStyle('visual-loader');
	$header .= $this->load->loadStyle('style');
	//$header .=$this->load->loadStyle('animate');
	$funnel = $this->load->loadFunnel();
	$page_description = "Manage your membership areas";
	$createoredit = "Members";
	$tutorial_link = "https://cloudfunnels.in/membership/members#tutorials_membership";
	$this->load->view($createoredit, $header, $page . ".php", "", array('page_description' => $page_description, 'funnel' => $funnel, 'tutorial_link' => $tutorial_link));
} elseif ($page == 'optins') {
	$header = $this->load->loadBootstrap();
	$header .= $this->load->loadVue();
	$header .= $this->load->loadScript('request');
	$header .= $this->load->loadScript('visual_loader');
	$header .= $this->load->loadStyle('visual-loader');
	$header .= $this->load->loadStyle('style');
	//$header .=$this->load->loadStyle('animate');
	$funnel = $this->load->loadFunnel();
	$page_description = "Manage optins collected from funnel(s)";
	$createoredit = "Optins";
	$optinsob = $this->load->loadOptin();
	$tutorial_link = "https://cloudfunnels.in/membership/members#tutorials_funnel";
	$this->load->view($createoredit, $header, $page . ".php", "", array('page_description' => $page_description, 'funnel' => $funnel, 'optinob' => $optinsob, 'tutorial_link' => $tutorial_link));
} elseif ($page == 'members') {
	$header = $this->load->loadBootstrap();
	$header .= $this->load->loadVue();
	$header .= $this->load->loadScript('request');
	$header .= $this->load->loadScript('visual_loader');
	$header .= $this->load->loadStyle('visual-loader');
	$header .= $this->load->loadStyle('style');
	//$header .=$this->load->loadStyle('animate');
	$funnel = $this->load->loadFunnel();
	$page_description = "Manage your members";
	$createoredit = "Members";
	$optinsob = $this->load->loadMember();
	$tutorial_link = "https://cloudfunnels.in/membership/members#tutorials_membership";
	$this->load->view($createoredit, $header, $page . ".php", "", array('page_description' => $page_description, 'funnel' => $funnel, 'optinob' => $optinsob, 'tutorial_link' => $tutorial_link));
} elseif ($page == 'create_funnel') {
	$header = $this->load->loadBootstrap();
	$header .= $this->load->loadVue();
	$header .= $this->load->loadScript('request');
	$header .= $this->load->loadScript('jszip.min');
	$header .= $this->load->loadScript('jszip_utils');
	$header .= $this->load->loadScript('visual_loader');
	$header .= $this->load->loadStyle('visual-loader');
	$header .= $this->load->loadStyle('style');
	$header .= $this->load->loadStyle('animate');
	$header .= $this->load->loadScript('html2canvas.min');
	$footer = $this->load->loadScript('funnel_control');
	require_once('common_templates.php');
	$labelbuttons = "";
	$pages = false;
	$flodername = "";
	$createoredit = "<span id='uniquefunnelheader'>Create Funnel</span>";

	$smtps_ob = $this->load->loadSMTP();
	$smtps = $smtps_ob->getSMTP('', 1);

	$lists_ob = $this->load->createlist();
	$lists = $lists_ob->getList('', 1);

	$products_ob = $this->load->loadSell();
	$products = $products_ob->getProductIdTitle();

	$membership_ob = $this->load->loadMember();

	$registrationpages = $membership_ob->getAllMembershipRegistrationPages();

	$protocol = $this->load->getProtocol();

	$integrations = $this->load->loadIntegrations();
	$funnel_data_ob = $this->load->loadFunnel();
	$total_funnels = $funnel_data_ob->totalSumCountsFunel();

	if (isset($_GET['id'])) {
		$funnel_data = $funnel_data_ob->getFunnel($_GET['id']);
		$pages = $funnel_data_ob->getPageHTML($_GET['id']);
		if ($funnel_data) {
			$createoredit = "<span id='uniquefunnelheader'>Edit Funnel</span>";
			$labelbuttons = $funnel_data->labelhtml;

			$funnel_data->baseurl = str_replace($funnel_data_ob->routed_url, get_option('install_url'), $funnel_data->baseurl);

			$footer .= "<script>
		funnel.funnel_name='" . $funnel_data->name . "';
		funnel.funnel_url='" . $funnel_data->baseurl . "';
		funnel.funnel_type='" . $funnel_data->type . "';
		funnel.current_funnel=" . $funnel_data->id . ";
		funnel.current_step='step_2';
	    funnel.common_inputs_for_current_funnel='" . $funnel_data->validinputs . "';
		setTimeout(function(){
			try
			{
		funnel.headerAdder();
		var thisfirstbtn=document.querySelectorAll('button[lbl=\"1\"]')[0];
		thisfirstbtn.dispatchEvent(new Event('mousedown'));
		thisfirstbtn.dispatchEvent(new Event('mouseup'));
		funnel.movable_element=0;
		funnel.createIndicator();
		funnel.headerAdder();
			}
			catch(err)
			{
				console.log(err.message);
			}
		},2000);
		settingDivOpenClose();
		</script>";
		}
	}
	$page_description = "Create, edit and manage your funnels and sites";
	$tutorial_link = "https://cloudfunnels.in/membership/members#tutorials_funnel";
	$this->load->view($createoredit, $header, $page . ".php", $footer, array('labelbtn' => $labelbuttons, 'pages' => $pages, 'page_description' => $page_description, 'smtps' => $smtps, 'lists' => $lists, 'products' => $products, 'registrationpages' => $registrationpages, 'protocol' => $protocol, 'integrations' => $integrations, 'tutorial_link' => $tutorial_link, "flodername" => $flodername, 'total_funnels' => $total_funnels));
	if (isset($_COOKIE['templateedited'])) {
		unset($_COOKIE['templateedited']);
	}
} elseif ($page == 'page_builder') {
	$funnelob = $this->load->loadFunnel();
	if (isset($_GET['fid'])) {
		$content = $funnelob->readContent($_GET['fid'], $_GET['lbl'], $_GET['abtype']);
		$bootstrapFlag = $funnelob->readPage($_GET['fid'], $_GET['lbl'], $_GET['abtype']);

		$this_page_data = $funnelob->getPageFunnel($_GET['fid'], $_GET['abtype'], $_GET['lbl']);
		if ($this_page_data) {
			$_GET['folder'] = $this_page_data->filename;
		}
	}

	$header = $this->load->loadJSTranslator();
	$header .= $this->load->loadScript('request');

	$footer = $this->load->loadMediaBox();
	if ($bootstrapFlag == 'B4')
		require_once($this->view_dir . "/editor.php");
	else
		require_once($this->view_dir . "/editor2.php");
} elseif ($page == 'autores_dashboard') {
	$header = $this->load->loadBootstrap();
	$header .= $this->load->loadVue();
	$header .= $this->load->loadScript('request');
	$header .= $this->load->loadScript('visual_loader');
	$header .= $this->load->loadStyle('visual-loader');
	$header .= $this->load->loadStyle('style');
	$header .= $this->load->loadStyle('animate');
	$footer = $this->load->loadScript('auto_responders');

	$page_description = "Configure autoresponders";

	$tutorial_link = "https://cloudfunnels.in/membership/members#tutorials_autoresponder";

	$this->load->view("Autoresponders", $header, $page . ".php", $footer, array('page_description' => $page_description, 'tutorial_link' => $tutorial_link));
} elseif ($page == 'autores_records') {
	$header = $this->load->loadBootstrap();
	$header .= $this->load->loadVue();
	$header .= $this->load->loadScript('request');
	$header .= $this->load->loadScript('visual_loader');
	$header .= $this->load->loadStyle('visual-loader');
	$header .= $this->load->loadStyle('style');
	//$header .=$this->load->loadStyle('animate');
	// $footer =$this->load->loadScript('auto_responders');

	$page_description = "Saved autoresponders";

	$tutorial_link = "https://cloudfunnels.in/membership/members#tutorials_autoresponder";
	$this->load->view("Autoresponder Setups", $header, $page . ".php", "", array('page_description' => $page_description, 'tutorial_link' => $tutorial_link));
} elseif ($page == 'smtp_create') {

	$header = $this->load->loadBootstrap();
	$header .= $this->load->loadVue();
	$header .= $this->load->loadScript('request');
	$header .= $this->load->loadScript('visual_loader');
	$header .= $this->load->loadStyle('visual-loader');
	$header .= $this->load->loadStyle('style');
	//$header .=$this->load->loadStyle('animate');
	$smtp    = $this->load->loadSMTPData();

	if (isset($_POST['save'])) {
		if (isset($_POST['save']) && !isset($_GET['id'])) {
			$title = $_POST['title'];
			$fromemail = $_POST['fromemail'];
			$hostname = $_POST['hostname'];
			$port = $_POST['port'];
			$username = $_POST['username'];
			$password = $_POST['password'];
			$encryption = $_POST['encryption'];
			$fromname = $_POST['fromname'];
			$replyname = $_POST['replyname'];
			$replyemail = $_POST['replyemail'];
			$smtp->insert($title, $fromemail, $hostname, $port, $username, $password, $encryption, $fromname, $replyname, $replyemail);
			$lastid = $smtp->getLastId();
			echo "<script>window.location='index.php?page=smtp_table';</script>";
		}

		if (isset($_POST['save']) && isset($_GET['id'])) {
			$id = $_GET['id'];
			$title = $_POST['title'];
			$fromemail = $_POST['fromemail'];
			$hostname = $_POST['hostname'];
			$port = $_POST['port'];
			$username = $_POST['username'];
			$password = $_POST['password'];
			$encryption = $_POST['encryption'];
			$fromname = $_POST['fromname'];
			$replyname = $_POST['replyname'];
			$replyemail = $_POST['replyemail'];
			$smtp->edit($id, $title, $fromemail, $hostname, $port, $username, $password, $encryption, $fromname, $replyname, $replyemail);
		}
	}
	$page_description = "Create, edit and Manage SMTP settings";

	$tutorial_link = "https://cloudfunnels.in/membership/members#tutorials_smtp";
	$this->load->view("SMTP", $header, $page . ".php", "", array('page_description' => $page_description, 'smtp' => $smtp, 'tutorial_link' => $tutorial_link));
} elseif ($page == 'smtp_table') {
	$header = $this->load->loadBootstrap();
	$header .= $this->load->loadVue();
	$header .= $this->load->loadScript('request');
	$header .= $this->load->loadScript('visual_loader');
	$header .= $this->load->loadStyle('visual-loader');
	$header .= $this->load->loadStyle('style');
	//$header .=$this->load->loadStyle('animate');

	$page_description = "Create, edit and manage SMTP settings";

	$tutorial_link = "https://cloudfunnels.in/membership/members#tutorials_smtp";
	$this->load->view("SMTP Records", $header, $page . ".php", "", array('page_description' => $page_description, 'tutorial_link' => $tutorial_link));
} elseif ($page == 'createlist') {
	$header = $this->load->loadBootstrap();
	$header .= $this->load->loadVue();
	$header .= $this->load->loadScript('request');
	$header .= $this->load->loadScript('visual_loader');
	$header .= $this->load->loadStyle('visual-loader');
	$header .= $this->load->loadStyle('style');
	//$header .=$this->load->loadStyle('animate');
	$footer = $this->load->loadScript('list');

	$list = $this->load->createlist();
	$total = $list->getTotalCount();
	$total_lists = $list->totalList();

	$page_description = "Create, edit and manage list";

	$tutorial_link = "https://cloudfunnels.in/membership/members#tutorials_list";
	$this->load->view("Lists", $header, $page . ".php", $footer, array('page_description' => $page_description, 'tutorial_link' => $tutorial_link, 'total_leads' => $total, 'total_lists' => $total_lists));
} elseif ($page == 'listrecords') {
	$header = $this->load->loadBootstrap();
	$header .= $this->load->loadVue();
	$header .= $this->load->loadScript('request');
	$header .= $this->load->loadScript('visual_loader');
	$header .= $this->load->loadStyle('visual-loader');
	$header .= $this->load->loadStyle('style');
	//$header .=$this->load->loadStyle('animate');
	//$footer =$this->load->loadScript('list');
	$footer = "";
	$page_description = "Create, edit and manage and export all your lists";

	$tutorial_link = "https://cloudfunnels.in/membership/members#tutorials_list";
	$this->load->view("Lists", $header, $page . ".php", $footer, array('page_description' => $page_description, 'tutorial_link' => $tutorial_link));
} elseif ($page == "plugins") {
	$header = $this->load->loadBootstrap();
	$header .= $this->load->loadVue();
	$header .= $this->load->loadScript('request');
	$header .= $this->load->loadScript('visual_loader');
	$header .= $this->load->loadStyle('visual-loader');
	$header .= $this->load->loadStyle('style');
	//$header .=$this->load->loadStyle('animate');
	//$footer =$this->load->loadScript('list');
	$footer = $this->load->loadScript('plugins_control');
	$footer .= "<script>plugin_control.cf_version=`" . get_option('qfnl_current_version') . "`;</script>";

	if (isset($_GET['ins_remote_plugin'])) {
		$footer .= "<script>plugin_control.doInstallRemotePlugin(`" . $_GET['ins_remote_plugin'] . "`);</script>";
	}

	$page_description = "Create, edit and manage your plugins";

	$plugins_ob = $this->load->loadPlugins();

	$tutorial_link = "https://cloudfunnels.in/membership/members#tutorials_plugins";
	$this->load->view("Plugins", $header, $page . ".php", $footer, array('page_description' => $page_description, 'tutorial_link' => $tutorial_link, 'plugins_ob' => $plugins_ob));
} elseif ($page == "products") {
	$header = $this->load->loadBootstrap();
	$header .= $this->load->loadVue();
	$header .= $this->load->loadScript('request');
	$header .= $this->load->loadStyle('style');
	//$header .=$this->load->loadStyle('animate');
	$sell = $this->load->loadSell();
	$page_description = "Create, edit and manage your products.";
	$createoredit = "Products";
	$footer = $this->load->loadScript('products');
	$tutorial_link = "https://cloudfunnels.in/membership/members#tutorials_sales";
	$this->load->view($createoredit, $header, $page . ".php", $footer, array('page_description' => $page_description, 'sell' => $sell, 'tutorial_link' => $tutorial_link));
} elseif ($page == 'cod_manage') {
	if (isset($_GET['sell_id'])) {
		$sell_id = cf_enc($_GET['sell_id'], 'decode');

		$header = $this->load->loadBootstrap(1);
		$header .= $this->load->loadVue();
		$header .= $this->load->loadScript('request');
		$header .= $this->load->loadScript('visual_loader');
		$header .= $this->load->loadStyle('visual-loader');
		$header .= $this->load->loadStyle('style');

		$sale_ob = $this->load->loadSell();

		$page_description = "See and manage COD detail";

		$tutorial_link = "https://cloudfunnels.in/membership/members#tutorials_sales";
		$this->load->view("Product Sales", $header, $page . ".php", '', array('page_description' => $page_description, 'sales_ob' => $sale_ob, 'tutorial_link' => $tutorial_link));
	}
} elseif ($page == 'sales') {
	$header = $this->load->loadBootstrap(1);
	$header .= $this->load->loadVue();
	$header .= $this->load->loadScript('request');
	$header .= $this->load->loadScript('visual_loader');
	$header .= $this->load->loadStyle('visual-loader');
	$header .= $this->load->loadStyle('style');
	//$header .=$this->load->loadStyle('animate');

	$sale_ob = $this->load->loadSell();

	$page_description = "See and manage all your sales.";
	$tutorial_link = "https://cloudfunnels.in/membership/members#tutorials_sales";
	$this->load->view("Product Sales", $header, $page . ".php", '', array('page_description' => $page_description, 'sales_ob' => $sale_ob, 'tutorial_link' => $tutorial_link));
} elseif ($page == 'media') {
	$header = $this->load->loadBootstrap();
	$header .= $this->load->loadVue();
	$header .= $this->load->loadScript('vuex');
	$header .= $this->load->loadScript('request');
	$header .= $this->load->loadStyle('style');

	$footer = $this->load->loadScript('media_storage');
	$footer .= $this->load->loadScript('media_components/app');
	$footer .= $this->load->loadScript('media_components/plupload.full.min');
	$footer .= $this->load->loadScript('media');

	$page_description = "Manage all your media files";

	$tutorial_link = "";

	$this->load->view("Media", $header, $page . ".php", $footer, array(
		'page_description' => $page_description,
		'list_ob' => $this->load->createList(),
		'smtp_ob' => $this->load->loadSMTP(),
		'compose_ob' => $this->load->loadMailComposer(),
		'tutorial_link' => $tutorial_link
	));
} elseif ($page == 'compose_mail') {
	$header = $this->load->loadBootstrap();
	$header .= $this->load->loadVue();
	$header .= $this->load->loadScript('vuex');
	//$header .=$this->load->loadScript('tinymce/tinymce.min');
	$header .= $this->load->loadScript('request');
	$header .= $this->load->loadScript('visual_loader');
	$header .= $this->load->loadStyle('visual-loader');
	$header .= $this->load->loadStyle('style');
	//$header .=$this->load->loadStyle('animate');
	$footer = $this->load->loadScript('compose_mail');
	$footer .= $this->load->loadMediaBox();

	$page_description = "Compose mail to your subscribers";

	$tutorial_link = "https://cloudfunnels.in/membership/members#tutorials_compose_mail";

	$this->load->view("Compose Mail", $header, $page . ".php", $footer, array(
		'page_description' => $page_description,
		'list_ob' => $this->load->createList(),
		'smtp_ob' => $this->load->loadSMTP(),
		'compose_ob' => $this->load->loadMailComposer(),
		'tutorial_link' => $tutorial_link
	));
} elseif ($page == 'email_sequence' || $page == 'sequence') {
	$page = "sequence";
	$header = $this->load->loadBootstrap();
	$header .= $this->load->loadVue();
	//$header .=$this->load->loadScript('tinymce/tinymce.min');
	$header .= $this->load->loadScript('request');
	$header .= $this->load->loadScript('visual_loader');
	$header .= $this->load->loadStyle('visual-loader');
	$header .= $this->load->loadStyle('style');
	//$header .=$this->load->loadStyle('animate');
	$footer = $this->load->loadScript('sequence');
	$footer .= $this->load->loadMediaBox();

	$page_description = "Create edit and manage email sequence";

	$tutorial_link = "https://cloudfunnels.in/membership/members#tutorials_sequence";
	$this->load->view("Sequence", $header, $page . ".php", $footer, array('page_description' => $page_description, 'tutorial_link' => $tutorial_link));
} elseif ($page == 'sequence_records') {
	$header = $this->load->loadBootstrap();
	$header .= $this->load->loadVue();
	$header .= $this->load->loadScript('request');
	$header .= $this->load->loadScript('visual_loader');
	$header .= $this->load->loadStyle('visual-loader');
	$header .= $this->load->loadStyle('style');
	//$header .=$this->load->loadStyle('animate');
	// $footer =$this->load->loadScript('auto_responders');

	$page_description = "Create, edit and manage all your emails";

	$tutorial_link = "https://cloudfunnels.in/membership/members#tutorials_sequence";

	$this->load->view("Emails Records", $header, $page . ".php", "", array('page_description' => $page_description, 'tutorial_link' => $tutorial_link));
} elseif ($page == 'sequences') {
	$header = $this->load->loadBootstrap();
	$header .= $this->load->loadVue();
	$header .= $this->load->loadScript('request');
	$header .= $this->load->loadScript('visual_loader');
	$header .= $this->load->loadStyle('visual-loader');
	$header .= $this->load->loadStyle('style');
	//$header .=$this->load->loadStyle('animate');
	// $footer =$this->load->loadScript('auto_responders');

	$page_description = "Create, edit and manage all your sequences";

	$tutorial_link = "https://cloudfunnels.in/membership/members#tutorials_sequence";

	$this->load->view("Sequence Records", $header, $page . ".php", "", array('page_description' => $page_description, 'tutorial_link' => $tutorial_link));
} elseif ($page == 'createmultiuser') {
	$header = $this->load->loadBootstrap();
	$header .= $this->load->loadVue();
	$header .= $this->load->loadScript('request');
	$header .= $this->load->loadScript('visual_loader');
	$header .= $this->load->loadStyle('visual-loader');
	$header .= $this->load->loadStyle('style');
	//$header .=$this->load->loadStyle('animate');
	$multiuser = $this->load->loadmultiuser();
	if (isset($_POST['save'])) {
		if (isset($_POST['save']) && !isset($_GET['id'])) {

			$name = $_POST['name'];
			$email = $_POST['email'];
			$password = $_POST['password'];
			$permission = (isset($_POST['permission'])) ? implode(',', $_POST['permission']) : "";

			// $fileToUpload=$_POST['fileToUpload'];
			if ($multiuser->addUser($name, $email, $password, $permission, $_POST['current_user_current_pass'])) {
				$lastid = $multiuser->getLastId();
				echo "<script>window.location='index.php?page=multiuser_table';</script>";
			} else {
				$GLOBALS['user_addorupdate_err'] = 1;
			}
		}

		if (isset($_POST['save']) && isset($_GET['id'])) {
			$id = $_GET['id'];
			$name = $_POST['name'];
			$email = $_POST['email'];
			$password = $_POST['password'];
			if(isset($_POST['role'])&&$_POST['role']=="admin")
			{
				$role="admin";
			}
			else
			{
				$role="user";
			}
			$permission = (isset($_POST['permission'])) ? implode(',', $_POST['permission']) : "";
			if (!$multiuser->editUser($id, $name, $email, $password, $permission, $_POST['current_user_current_pass'],$role)) {
				$GLOBALS['user_addorupdate_err'] = 1;
			}
		}
	}
	$page_description = "Create, edit and manage user details";

	$tutorial_link = "https://cloudfunnels.in/membership/members#tutorials_user";
	$this->load->view("Users", $header, $page . ".php", "", array('page_description' => $page_description, 'multiuser' => $multiuser, 'tutorial_link' => $tutorial_link));
} elseif ($page == 'multiuser_table') {
	$header = $this->load->loadBootstrap();
	$header .= $this->load->loadVue();
	$header .= $this->load->loadScript('request');
	$header .= $this->load->loadScript('visual_loader');
	$header .= $this->load->loadStyle('visual-loader');
	$header .= $this->load->loadStyle('style');
	//$header .=$this->load->loadStyle('animate');
	$multiuser = $this->load->loadmultiuser();
	$page_description = "Create,edit and manage user details";
	$createoredit = "Users";

	$tutorial_link = "https://cloudfunnels.in/membership/members#tutorials_user";
	$this->load->view($createoredit, $header, $page . ".php", "", array('page_description' => $page_description, 'multiuser' => $multiuser, 'tutorial_link' => $tutorial_link));
} elseif ($page == "do_payment" || $page == "do_payment_execute") {
	if ($page == "do_payment_execute") {
		$_GET['page'] = 'do_payment';
		$_GET['execute'] = 1;
	}

	if ((isset($_SESSION['order_form_data' . get_option('site_token')])) || (isset($_GET['qfnl_is_ipn']))) {
		if (isset($_GET['qfnl_is_ipn'])) {
			$funnel_ob = $this->load->loadFunnel();
			$exists = $funnel_ob->isOrderform($_GET['qfunnel_id'], $_GET['qfolder'], 'a', $_POST, 0);

			if (!$exists || (get_option('ipn_token') != $_GET['qfnl_is_ipn'])) {
				goto lbl;
			}
		}

		$data = $_SESSION['order_form_data' . get_option('site_token')];
		$sell = $this->load->loadSell();
		if (isset($data['data']['payment_method']) && !empty($data['data']['payment_method'])) {
			$payment_method = $data['data']['payment_method'];
		} else {
			$payment_method = $data['payment_method'];
		}
		$sell->doPayment($data['funnel_id'], $data['folder'], $data['page_id'], $data['ab_type'], $data['product_id'], $payment_method, $data['membership'], $data['lists'], $data['optional_products'], $data['confirmation_url'], $data['cancel_url'], $data['data']);
	}
	lbl:
	exit;
} elseif (($page == 'schedule_api') || ($page == 'schedule_api_runserver')) {
	if ($page == 'schedule_api_runserver') {
		$_GET['runserver'] = 1;
	}
	$schedule_api_ob = $this->load->loadScheduler();
	$schedule_api_ob->reqControl();
} elseif ($page == 'mail_send') {
	$this->load->loadCbMailer();
} elseif ($page == "mail_track") {
	header('content-type: image/png');
	readfile($this->base_dir . '/assets/img/mail.png');
	$sequence_ob = $this->load->loadSequence();
	$sequence_ob->sequenceTrack(base64_decode($_GET['token']), base64_decode($_GET['card']));
} elseif ($page == "export_csv") {
	if (isset($_GET['type'])) {
		$type = $_GET['type'];
		if ($type == "list") {
			$listob = $this->load->createlist();
			$listob->exportToCsv($_POST['listid']);
		}
	}
	if (isset($_POST['optinto_csv'])) {
		$optin_ob = $this->load->loadOptin();
		$funnelpage = explode("@", str_replace("page@", "", $_POST['optinto_csv']));
		$optin_ob->optinToCsv($funnelpage[0], $funnelpage[1]);
	}
	if (isset($_POST["membersto_csv"])) {
		$member_ob = $this->load->loadMember();
		$member_ob->exportMemberToCsv($_POST["membersto_csv"]);
	}
	if (isset($_POST['salesto_csv'])) {
		$sell_ob = $this->load->loadSell();
		$sell_ob->exportToCSV($_POST['salesto_csv']);
	}
} elseif ($page == "download") {
	if (isset($_GET['type']) && $_GET['type'] == "data_request" && isset($_POST['gdpr_req_id'])) {
		$gdpr_ob = $this->load->loadGdpr();
		$data = $gdpr_ob->downloadGdprData($_POST['gdpr_req_id'], 1);
		if ($data === 0) {
			$data = "No Data Found";
		}
		header('content-type: text/html');
		header('content-disposition: attachment; filename=data_request.html');
		echo $data;
	}
} elseif ($page == 'dashboard') {
	$header = $this->load->loadBootstrap();
	$header .= $this->load->loadVue();
	$header .= $this->load->loadScript('request');
	$header .= $this->load->loadScript('visual_loader');
	$header .= $this->load->loadStyle('visual-loader');
	$header .= $this->load->loadStyle('style');
	//$header .=$this->load->loadStyle('animate');
	$header .= $this->load->loadChartJs();

	$page_description = "Start here";

	$this->load->view("Dashboard", $header, $page . ".php", "", array('page_description' => $page_description, 'load' => $this->load));
} elseif ($page == 'new_admin') {
	$header = $this->load->loadBootstrap();
	$header .= $this->load->loadScript('request');
	$header .= $this->load->loadScript('visual_loader');
	$header .= $this->load->loadStyle('visual-loader');
	$header .= $this->load->loadStyle('style');
	$header .=$this->load->loadStyle('animate');
	$header .= $this->load->loadChartJs();
	$page_description = "Admin new page";

	$this->load->view("Page super Admin", $header, $page . ".php", "", array('page_description' => $page_description, 'load' => $this->load));
}elseif ($page == 'settings') {

	$header = $this->load->loadBootstrap();
	$header .= $this->load->loadVue();
	$header .= $this->load->loadScript('request');
	$header .= $this->load->loadScript('visual_loader');
	$header .= $this->load->loadStyle('visual-loader');
	$header .= $this->load->loadStyle('style');

	$footer = $this->load->loadMediaBox();

	//$header .=$this->load->loadStyle('animate');
	$page_description = "Configure your app.";
	$createoredit = "Settings";
	$setting_ob = $this->load->loadSettings();

	if (isset($_POST['save_settings'])) {
		$setting_ob->saveSettings();
	}
	$tutorial_link = "https://cloudfunnels.in/membership/members#tutorials_settings";
	$this->load->view($createoredit, $header, $page . ".php", $footer, array('page_description' => $page_description, 'tutorial_link' => $tutorial_link));
} elseif ($page == "app_guide") {
	$header = $this->load->loadBootstrap();
	$header .= $this->load->loadScript('prism');
	$header .= $this->load->loadStyle('prism');
	$header .= $this->load->loadStyle('style');
	$page_description = "Guidance to maintain and setup the app.";
	$createoredit = "Help";
	$this->load->view($createoredit, $header, $page . ".php", "", array('page_description' => $page_description));
} elseif ($page == 'sentemailsdetails') {
	$header = $this->load->loadBootstrap();
	$header .= $this->load->loadVue();
	$header .= $this->load->loadScript('request');
	$header .= $this->load->loadScript('visual_loader');
	$header .= $this->load->loadStyle('visual-loader');
	$header .= $this->load->loadStyle('style');
	//$header .=$this->load->loadStyle('animate');

	$page_description = "View your complete mail sending details";

	$tutorial_link = "https://cloudfunnels.in/membership/members#tutorials_mail";

	$this->load->view("Mailing History", $header, $page . ".php", "", array('page_description' => $page_description, 'tutorial_link' => $tutorial_link));
} elseif ($page == 'do_unsubscribe') {
	echo $this->load->loadBootstrap(1);
	//echo $this->load->loadStyle('animate');
	echo $this->load->loadVue();
	echo $this->load->loadScript('request');
	echo $this->load->loadStyle('style');
	$this->load->loadUnsubscribePage();
} elseif ($page == "do_redirect") {
	if ((isset($_GET['qfnlemlcard'])) && (isset($_GET['qfnldetectcard']))) {
		$sequence_ob = $this->load->loadSequence();
		$sequence_ob->storeLinksVisits($_GET['qfnldetectcard'], $_GET['qfnlemlcard']);
	}
} elseif ($page == 'gdpr') {

	$header = $this->load->loadBootstrap();
	$header .= $this->load->loadVue();
	$header .= $this->load->loadScript('request');
	$header .= $this->load->loadScript('visual_loader');
	$header .= $this->load->loadStyle('visual-loader');
	$header .= $this->load->loadStyle('style');
	//$header .=$this->load->loadStyle('animate');
	$page_description = "Setup and manage GDPR requirements and requests.";
	$createoredit = "GDPR Settings";
	$cookie_ob = $this->load->loadGdpr();

	if (isset($_POST['save_settings'])) {
		$cookie_ob->saveSettings();
	}
	$footer = $this->load->loadMediaBox();

	$tutorial_link = "https://cloudfunnels.in/membership/members#tutorials_gdpr";
	$this->load->view($createoredit, $header, $page . ".php", $footer, array('page_description' => $page_description, 'tutorial_link' => $tutorial_link));
} elseif ($page == 'analysis') {

	$header = $this->load->loadBootstrap();
	$header .= $this->load->loadVue();
	$header .= $this->load->loadScript('request');
	$header .= $this->load->loadScript('visual_loader');
	$header .= $this->load->loadStyle('visual-loader');
	$header .= $this->load->loadStyle('style');
	//$header .=$this->load->loadStyle('animate');
	$page_description = "Make Analysis Of Your Funnels";
	$createoredit = "Analysis Of Your Funnels";

	$tutorial_link = "https://cloudfunnels.in/membership/members#tutorials_analysis";

	$this->load->view($createoredit, $header, $page . ".php", "", array('page_description' => $page_description, 'tutorial_link' => $tutorial_link));
} elseif ($page == "api_request") {
	if (isset($_GET['cf_cookie'])) {
		header('content-type:text/javascript');
		$gdpr_ob = $this->load->loadGdpr();
		echo $gdpr_ob->gdprJsScript($_GET['cf_cookie']);
	} elseif (isset($_GET['cf_load_cookie'])) {
		$gdpr_ob = $this->load->loadGdpr();
		echo $gdpr_ob->displayCookie(1, $_GET['cf_load_cookie']);
	} elseif (isset($_GET['mail_api_auth'])) {
		echo hash_hmac('sha1', get_option('site_token'), $_GET['mail_api_auth']);
	} elseif (isset($_POST['copy_funnel'])) {
		$token = $_POST['copy_funnel'];
		$clone_ob = $this->load->loadFunnelCloner();
		$data = $clone_ob->createMap($token);
		echo $data;
		die();
	}
} elseif ($page == "ajax") {
	//ajax request for integrated apps
	if (isset($_REQUEST['action'])) {
		$plugin_loader = $GLOBALS['plugin_loader'];
		$plugin_loader->processAjax($_REQUEST['action']);
	}
	die();
} elseif ($page == "callback_api") {
	//handle api requests for integrated apps
	if (isset($_REQUEST['action'])) {
		$plugin_loader = $GLOBALS['plugin_loader'];
		$plugin_loader->processApi($_REQUEST['action']);
	}
	die();
} elseif ($page === "data_requests") {
	$header = $this->load->loadBootstrap();
	$header .= $this->load->loadStyle('style');
	$err = 0;
	$secure_ob = $this->load->secure();
	if (isset($_POST['submitgdprrequest'])) {
		if (isset($_POST['csrf_token']) && ($secure_ob->matchToken($_POST['csrf_token']))) {
			$gdpr_ob = $this->load->loadGdpr();
			$err = $gdpr_ob->storeDataRequests($_POST['name'], $_POST['email'], $_POST['description'], $_POST['data_type']);
		} else {
			$err = "Please Refresh The Page And Try Again.";
		}
	}
	$csrf_token = $secure_ob->setToken();

	require_once($this->view_dir . "/" . $page . ".php");
	die();
	//$this->load->view("GDPR Data Access",$header,$page.".php","",array('page_description'=>"",'error'=>$err,'csrf_token'=>$csrf_token));
} elseif ($page == "load_static_scripts") {
	$create_cache = (isset($_GET['create_cache']) && $_GET['create_cache'] == '1') ? 1 : 0;
	if (isset($_GET['script_type'])) {
		$type = $_GET['script_type'];
		if ($type == "type.js") {
			header('content-type: application/javascript');
		} elseif ($type == "type.css") {
			header("content-type: text/css");
		}
	}
	$script = base64_decode($_GET['script']);
	$funnel_ob = $this->load->loadFunnel();
	echo $funnel_ob->cssJsScriptView($script, $create_cache);
} elseif ($page == "mmbr_lgout") {
	echo "<script>window.location='http://cloudfunnels.in';</script>";
} elseif ($page == "show_cf_narand") {
	//$install_url=get_option('install_url');

	$logo = (function_exists("getBsSixtyFourLogos")) ? getBsSixtyFourLogos("logo") : "assets/theme-assets/assets/images/logo.png";

	$logo_text = (function_exists("getBsSixtyFourLogos")) ? getBsSixtyFourLogos("logo-text") : "assets/theme-assets/assets/images/logo-text.png";

	$class = substr(str_shuffle("asdfghjklwertyuioxcvbnm"), 0, 5);
	echo "<a target='_PARENT' href='http://getcloudfunnels.in' style='text-decoration:none;'><div class='" . $class . "'>
	<div class='img'><img src='" . $logo . "'/></div>
	<div class='txt'><span>Powered&nbsp;By&nbsp;</span> <span><img src='" . $logo_text . "'/></span></div>
	</div></a>";
	echo "<style>
	." . $class . "
	{
		display:flex;
		flex-direction:row;
		justify-content:flex-end;
		height:28px !important;
		background-color:rgb(0, 51, 102, 0.9);
		width:100%;
		padding:5px;
		border-radius:5px 0px 0px 0px;
	}
	." . $class . " div
	{
		border:1px transparent;
		max-height:100% !important;
	}
	." . $class . " div.img
	{
		flex-grow:1;
	}
	." . $class . " div.txt
	{
		flex-grow:3;
		display:flex;
		flex-direction:row;
		align-items:stretch;
	}
	." . $class . " div.txt span:nth-child(1)
	{
		color:white;
		margin-top:auto;
		margin-bottom:auto;
		margin-right:2px;
		opacity:0.5;
		font-size:14px;
	}
	." . $class . " div.txt span:nth-child(2)
	{
		margin-top:auto;
		margin-bottom:auto;
	}
	." . $class . " div.txt img
	{
		max-height:14px;
		animation: brandani;
		animation-duration: 1s;
		animation-delay: 1s;
		animation-iteration-count: 2;
		animation-timing-function:ease;
	}
	@keyframes brandani
	{
		from {max-height:8px;} 
		to{max-height:14px;}
	}
	." . $class . " div.img img
	{
		max-height:25px !important;
		max-width:100%;
		vertical-align:middle;
		margin-right:4px;
	}
	</style>";
} elseif ($page == "install_update_dependencies") {
	set_time_limit(0);
	global $current_app_version;
	if (isset($current_app_version)) {
		$autoupdater = $this->load->loadAutoUpdater();
		$added = $autoupdater->installDependecies($current_app_version);
		if (isset($_GET['after_update_redirect'])) {
			$url = $_GET['after_update_redirect'];

			if (filter_var($url, FILTER_VALIDATE_URL)) {
				header('Location: ' . $url);
			} elseif (filter_var(base64_decode($url), FILTER_VALIDATE_URL)) {
				$url = base64_decode($url);
				header('Location: ' . $url);
			}
		} else {
			if (!$added) {
				echo "Try again";
			} else {
				echo "done";
			}
		}
	} else {
		echo "Version missing";
	}
}// page== subdomians
elseif($page=="subdomains" || $page=="new_subdomain")
{

	$header = $this->load->loadBootstrap();
	$header .= $this->load->loadStyle('style');
	$footer ="";
	$page_description = "Create, edit and manage subdomains";
	$createoredit = "Subdomains";
	$tutorial_link = "";
	$this->load->view($createoredit, $header, $page . ".php", $footer, array('page_description' => $page_description, 'tutorial_link' => $tutorial_link));
}
else {
	//manage plugins
	$has_a_integrated_page = false;
	if (isset($GLOBALS['plugin_loader'])) {
		$plugin_loader = $GLOBALS['plugin_loader'];
		$header = $this->load->loadBootstrap();
		$header .= $this->load->loadVue();
		$header .= $this->load->loadStyle('style');
		$createoredit = "";
		$page_description = "";
		$tutorial_link = "";
		$plugin_data = $plugin_loader->processAdminMenu($page);
		if ($plugin_data) {
			$has_a_integrated_page = true;
			if (isset($plugin_data[0]['page_title'])) {
				$createoredit = $plugin_data[0]['page_title'] . " ";
			}
			$this->load->view($createoredit, $header, "plugin_view.php", "", array('page_description' => $page_description, 'tutorial_link' => $tutorial_link), $plugin_data);
		}
	}

	if (!$has_a_integrated_page) {
		header('Location: index.php?page=no_permission');
		die();
	}
}
