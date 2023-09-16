<?php
session_start();
set_time_limit(0);
$current_base_dir=str_replace("\\","/",__DIR__);
require_once($current_base_dir."/library/esc_html.php");

if(isset($_GET["cfhttp"]))
{
	foreach($_GET as $cfhttp_data_index=>$cfhttp_data_val)
	{
		$_GET[$cfhttp_data_index]= js_html_entity_decode(base64_decode($cfhttp_data_val));
		$_REQUEST[$cfhttp_data_index]= js_html_entity_decode(base64_decode($cfhttp_data_val));
	}
}
if(isset($_POST["cfhttp"]))
{
	foreach($_POST as $cfhttp_data_index=>$cfhttp_data_val)
	{
		$_POST[$cfhttp_data_index]= js_html_entity_decode(base64_decode($cfhttp_data_val));
		$_REQUEST[$cfhttp_data_index]= js_html_entity_decode(base64_decode($cfhttp_data_val));
	}
}

require($current_base_dir."/gcp/gcp.php");

require_once($current_base_dir."/library/library.php");
$load=new Library();
$cf_product_code="mailenginepro";
if(is_file($GLOBALS["config_file"]))
{

require_once($GLOBALS["config_file"]);
require_once($current_base_dir."/library/options.php");


$load->setInfo('mysqli',$mysqli);
$load->setInfo('dbpref',$dbpref);
$load->setInfo('base_dir',str_replace("\\","/",__DIR__));
$userobforcheck=$load->loadUser();
}
$csrf_msg="Session timeout please refresh page and try again";

$security=$load->secure();


if(function_exists('get_option'))
{
$main_load=$load;
require_once($current_base_dir."/library/plugin_options.php");
}

//-------------config file generation-------------
if(isset($_POST['createsubdomain']))
{
		// create new subdomain
		$mysqli = $info['mysqli'];
		$dbpref = $info['dbpref'];
		$table = $dbpref . "subdomians";
		//clear text
		$name=$_POST['subdomain_name'];
		$url=$_POST['subdomain_url'];
		$type = $_POST['subdomain_type'];
		$user_id = $_SESSION['user' . get_option('site_token')];
		$sql_insert="
		INSERT INTO `".$table."`(`id`, `name`, `url`, `type`, `user_id`) VALUES
		(NULL,'$name','$url','$type','$user_id')";
		$mysqli->query($sql_insert);
		echo $sql_insert;


}
?>