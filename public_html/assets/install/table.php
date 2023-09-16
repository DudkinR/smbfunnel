<?php
function createTable($conn,$pref)
{
	// Table for Users who have access to funnel admin
	$sql = "CREATE TABLE IF NOT EXISTS `".$pref."users` (
	`id` BIGINT NOT NULL AUTO_INCREMENT,
	`name` VARCHAR(255) NOT NULL,
	`email` VARCHAR(255) NOT NULL,
	`password` VARCHAR(255) NOT NULL,
	`verified` VARCHAR(255) NOT NULL,
	`verifycode` VARCHAR(255) NOT NULL,
	`date_verifycodegen` VARCHAR(255) NOT NULL,
	`ip_lastsignin` VARCHAR(255) NOT NULL,
	`ip_created` VARCHAR(255) NOT NULL,
	`date_created` VARCHAR(255) NOT NULL,
	`date_signin` VARCHAR(255) NOT NULL,
	`permission` TEXT NOT NULL,
	`profile_picture` TEXT NOT NULL,
	PRIMARY KEY(`id`)
	)";
	$create_one=$conn->query($sql);

	// Table for Funnel created in the website
	$sql0 = "CREATE TABLE IF NOT EXISTS `".$pref."quick_funnels` (
	`id` BIGINT NOT NULL AUTO_INCREMENT,
	`name` VARCHAR(255) NOT NULL,
	`flodername` text NOT NULL,
	`baseurl` text NOT NULL,
	`pagecount` VARCHAR(255) NOT NULL,
	`firstpage` VARCHAR(255) NOT NULL,
	`type` VARCHAR(255) NOT NULL,
	`labelhtml` text NOT NULL,
	`validinputs` text NOT NULL,
	`primarysmtp` varchar(255) NOT NULL,
	`date_created` VARCHAR(255) NOT NULL,
	`token` VARCHAR(255) NOT NULL, 
	PRIMARY KEY(id)
	)";
	$create_two=$conn->query($sql0);

	// Table for Templates
	$sql1 = "CREATE TABLE IF NOT EXISTS `".$pref."quick_templates` (
	`id` BIGINT NOT NULL AUTO_INCREMENT,
	`name` VARCHAR(255) NOT NULL,
	`image` VARCHAR(255) NOT NULL,
	`type` VARCHAR(255) NOT NULL,
	`popularity_score` VARCHAR(255) NOT NULL,
	`date_created` VARCHAR(255) NOT NULL,
	PRIMARY KEY(`id`)
	)";
	$create_three=$conn->query($sql1);

	// Table for PageFunnel
	//`selares` for autoresponders
	$sql2 = "CREATE TABLE IF NOT EXISTS `".$pref."quick_pagefunnel` (
	`id` BIGINT NOT NULL AUTO_INCREMENT,
	`funnelid` VARCHAR(255) NOT NULL,
	`name` VARCHAR(255) NOT NULL,
	`title` TEXT NOT NULL,
	`metadata` TEXT NOT NULL,
	`filename` VARCHAR(255) NOT NULL,
	`pageheader` TEXT NOT NULL,
	`pagefooter` TEXT NOT NULL,
	`type` VARCHAR(255) NOT NULL,
	`active_type` ENUM('NA', 'a','b') NOT NULL DEFAULT 'NA',
	`level` INT(11) NOT NULL,
	`category` VARCHAR(255) NOT NULL,
	`custom` VARCHAR(255) NOT NULL,
	`content` VARCHAR(255) NOT NULL,
	`viewcount` VARCHAR(255) NOT NULL,
	`bouncecount` VARCHAR(255) NOT NULL,
	`convertcount` VARCHAR(255) NOT NULL,
	`hasabtest` VARCHAR(255) NOT NULL,
	`contentbpage` VARCHAR(255) NOT NULL,
	`viewcoubtpage` VARCHAR(255) NOT NULL,
	`convertcountbpage` VARCHAR(255) NOT NULL,
	`bouncecountbpage` VARCHAR(255) NOT NULL,
	`downsellpage` VARCHAR(255) NOT NULL,
	`upsellpage` VARCHAR(255) NOT NULL,
	`selares` TEXT NOT NULL,
	`lists` TEXT NOT NULL,
	`product` VARCHAR(255) NOT NULL,
	`membership` TEXT NOT NULL,
	`paymentmethod` varchar(255) NOT NULL,
	`templateimg` TEXT NOT NULL,
	`valid_inputs` TEXT NOT NULL,
	`settings` TEXT NOT NULL,
	`date_created` VARCHAR(255) NOT NULL,
	`token` VARCHAR(255) NOT NULL,
	`varient` VARCHAR(255) unique NOT NULL,
	PRIMARY KEY(`id`)
	)";
	$create_four=$conn->query($sql2);

	//Table for Member
	$sql3 = "CREATE TABLE IF NOT EXISTS `".$pref."quick_member` (
	`id` BIGINT NOT NULL AUTO_INCREMENT,
	`funnelid` VARCHAR(255) NOT NULL,
	`pageid` VARCHAR(255) NOT NULL,
	`name` VARCHAR(255) NOT NULL,
	`email` VARCHAR(255) NOT NULL,
	`password` VARCHAR(255) NOT NULL,
	`verified` VARCHAR(255) NOT NULL,
	`verifycode` TEXT NOT NULL,
	`date_verifycodegen` VARCHAR(255) NOT NULL,
	`ip_created` VARCHAR(255) NOT NULL,
	`ip_lastsignin` VARCHAR(255) NOT NULL,
	`date_created` VARCHAR(255) NOT NULL,
	`date_lastsignin` VARCHAR(255) NOT NULL,
	`valid` varchar(255) NOT NULL,
	`exf` TEXT NOT NULL,
	PRIMARY KEY(`id`)
	)";
	$create_five=$conn->query($sql3);


	//Table for Membership
	$sql4 = "CREATE TABLE IF NOT EXISTS `".$pref."quick_membership` (
	`id` BIGINT NOT NULL AUTO_INCREMENT,
	`name` VARCHAR(255) NOT NULL,
	`filename` VARCHAR(255) NOT NULL,
	`content` VARCHAR(255) NOT NULL,
	`date_created` VARCHAR(255) NOT NULL,
	PRIMARY KEY(`id`)
	)";
	$create_six=$conn->query($sql4);

	$sql10 = "CREATE TABLE IF NOT EXISTS `".$pref."quick_autoresponders` (
	`id` BIGINT NOT NULL AUTO_INCREMENT,
	`autoresponder` VARCHAR(255) NOT NULL,
	`autoresponder_name` VARCHAR(255) NOT NULL,
	`autoresponder_detail` text NOT NULL,
	`exf` text NOT NULL,
	`date_created` VARCHAR(255) NOT NULL,
	PRIMARY KEY(`id`)
	)";
	$create_ten=$conn->query($sql10);

	$sql11="create table if not exists `".$pref."quick_optins` (".
	"`id` bigint NOT NULL AUTO_INCREMENT,".
	"`funnelid` varchar(255) NOT NULL,".
	"`pageid` varchar(255) NOT NULL,".
	"`name` varchar(255) NOT NULL,".
	"`email` varchar(255) NOT NULL,".
	"`extras` text NOT NULL,".
	"`ipaddr` text NOT NULL,".
	"`exf` text NOT NULL,".
	"`send_zap` varchar(255) NOT NULL,".
	"`addedon` varchar(255) NOT NULL,".
	"PRIMARY KEY(`id`));";
	$create_eleven=$conn->query($sql11);

	$sql12="create table if not exists `".$pref."options` (".
	"`id` bigint not null auto_increment,".
	"`option_name` text not null,".
	"`option_value` text not null,".
	"`createdon` varchar(255) not null,".
	"primary key(`id`));";
	$create_twelve=$conn->query($sql12);

	$sql13="create table if not exists `".$pref."quick_smtp_setting` (".
	"`id` bigint NOT NULL AUTO_INCREMENT,".
	"`title` varchar(255) NOT NULL,".
	"`hostname` varchar(255) NOT NULL,".
	"`port` varchar(255) NOT NULL,".
	"`encryption` varchar(255) NOT NULL,".
	"`fromname` varchar(255) NOT NULL,".
	"`fromemail` varchar(255) NOT NULL,".
	"`username` varchar(255) NOT NULL,".
	"`password` varchar(255) NOT NULL,".
	"`replyname` varchar(255) NOT NULL,".
	"`replyemail` varchar(255) NOT NULL,".
	"`created_at` varchar(255) NOT NULL,".
	"PRIMARY KEY(`id`));";
	$create_thirteen=$conn->query($sql13);

	$sql14="create table if not exists `".$pref."quick_list_records` (".
	"`id` bigint NOT NULL AUTO_INCREMENT,".
	"`title` varchar(255) NOT NULL,".
	"`creationdate` varchar(255) NOT NULL,".
	"PRIMARY KEY(`id`));";
	$create_forteen=$conn->query($sql14);

	$sql15="create table if not exists `".$pref."quick_email_lists` (".
	"`id` bigint NOT NULL AUTO_INCREMENT,".
	"`listid` varchar(255) NOT NULL,".
	"`name` varchar(255) NOT NULL,".
	"`email` varchar(255) NOT NULL,".
	"`exf` text NOT NULL,".
	"`ipaddr` text NOT NULL,".
	"`date` varchar(255) NOT NULL,".
	"PRIMARY KEY(`id`));";
	$create_fifteen=$conn->query($sql15);

	$sql16="create table if not exists `".$pref."all_sales`(
	`id` bigint not null auto_increment,
	`productid` varchar(255) not null,
	`paymentmethod` varchar(255) not null,
	`membership` varchar(255) not null,
	`payment_id` text not null,
	`shippingdetail` text not null,
	`shipped` varchar(255) not null,
	`funnelid` varchar(255) not null,
	`pageid` varchar(255) not null,
	`paymentdata` text not null,
	`parent` varchar(255) not null,
	`purchase_name` varchar(255) not null,
	`purchase_email` varchar(255) not null,
	`total_paid` varchar(255) not null,
	`step_payments` text not null,
	`valid` varchar(255) not null,
	`exf` text not null,
	`addedon` varchar(255) not null,
	primary key(`id`)
	);
	";
	$create_sixteen=$conn->query($sql16);

	$sql17="create table if not exists `".$pref."all_products`(
	`id` bigint not null auto_increment,
	`productid` varchar(255) not null,
	`title` text not null,
	`url` text not null,
	`description` text not null,
	`price` varchar(255) not null,
	`currency` varchar(255) not null,
	`shipping` varchar(255) not null,
	`subproducts` text not null,
	`opproducts` text not null,
	`tax` varchar(255) not null,
	`createdon` varchar(255) not null,
	primary key(`id`)
	);
	";
	$create_seventeen=$conn->query($sql17);

	$sql18="create table if not exists `".$pref."payment_methods`(
	`id` bigint not null auto_increment,
	`title` varchar(255) not null,
	`method` varchar(255) not null,
	`tax` varchar(255) not null,
	`credentials` text not null,
	`createdon` varchar(255) not null,
	primary key(`id`)
	);
	";
	$create_eighteen=$conn->query($sql18);


	$sql19 = "create table if not exists `".$pref."quick_sequence` (".
	"`id` bigint NOT NULL AUTO_INCREMENT,".
	"`title` text not null,".
	"`listid` text NOT NULL,".
	"`smtpid` text NOT NULL,".
	"`sentdata` text NOT NULL,".
	"`stimezone` text NOT NULL,".
	"`sequence` varchar(255) NOT NULL,".
	"`date` varchar(255) NOT NULL,".
	"`time` varchar(255) NOT NULL,".
	"`updated_on` varchar(255) NOT NULL,".
	"PRIMARY KEY(`id`))";
	$create_nineteen = $conn->query($sql19);

	$sql20="CREATE TABLE IF NOT EXISTS `".$pref."quick_subscription_mail_schedule` (".
				"`id` bigint NOT NULL AUTO_INCREMENT,".
				"`seqid` varchar(255) NOT NULL,".
				"`listid` varchar(255) NOT NULL,".
				"`smtpid` varchar(255) NOT NULL,".
				"`status` text NOT NULL,".
				"`sentdata` text NOT NULL,".
				"`extraemails` text NOT NULL,".
				"`sdate` varchar(255) NOT NULL,".
				"`stime` varchar(255) NOT NULL,".
				"`stimezone` varchar(255) NOT NULL,".
				"`stoken` varchar(255) NOT NULL,".
				"`date` varchar(255) NOT NULL,".
				"`time` varchar(255) NOT NULL,".
				"PRIMARY KEY(`id`))";
	$create_twenty = $conn->query($sql20);

	$sql21="CREATE TABLE IF NOT EXISTS `".$pref."quick_api_schedular_table` (".
						"`id` bigint NOT NULL AUTO_INCREMENT,".
						"`cburl` text NOT NULL,".
						"`stoken` varchar(255) NOT NULL,".
						"`stime` bigint NOT NULL,".
						"`status` varchar(255) NOT NULL,".
						"`ip` varchar(255) NOT NULL,".
						"`date` varchar(255) NOT NULL,".
						"`time` varchar(255) NOT NULL,".
						"PRIMARY KEY(`id`))";
	$create_twentyone = $conn->query($sql21);

	$sql22="create table if not exists `".$pref."site_visit_record` (".
	"`id` bigint not null auto_increment,".
	"`session_id` varchar(255) not null,".
	"`visit_ip` varchar(255) not null,".
	"`device` varchar(255) not null,".
	"`os` varchar(255) not null,".
	"`browser` varchar(255) not null,".
	"`location` varchar(255) not null,".
	"`visit_pageid` varchar(255) not null,".
	"`convert_pageid` varchar(255) not null,".
	"`convert_optinid` varchar(255) not null,".
	"`convert_count` varchar(255) not null,".
	"`visitedon` varchar(255) not null,".
	"`convertedon` varchar(255) not null,".
	"primary key(`id`));";
	$create_twentytwo=$conn->query($sql22);

	$sql23="create table if not exists `".$pref."email_links_visits`(
	`id` bigint not null auto_increment,
	`url` text not null,
	`sequence_id` varchar(255) not null,
	`email_token` varchar(255) not null,
	`url_token` varchar(255) not null,
	`visited` varchar(255) not null,
	`createdon` varchar(255) not null,
	primary key(`id`));";
	$create_twentythree=$conn->query($sql23);

	$sql24="create table if not exists `".$pref."gdpr_datas` (
		`id` bigint not null auto_increment,
		`ip` text not null,
		`action` varchar(255) not null,
		`funnel` varchar(255) not null,
		`label` varchar(255) not null,
		`type` varchar(255) not null,
		`data` text not null,
		`date` varchar(255) not null,
		primary key(`id`)
	);
		";
	$create_twentyfour=$conn->query($sql24);

	$sql25="create table if not exists `".$pref."qfnl_integrations` (
		`id` bigint not null auto_increment,
		`title` varchar(255) not null,
		`type` varchar(255) not null,
		`data` text not null,
		`position` varchar(255) not null,
		`added_on` varchar(255) not null,
		primary key(`id`)
		);
		";
	$create_twentyfive=$conn->query($sql25);

	//----For Plugins--------
	$sql26="create table if not exists `".$pref."qfnl_plugins`(
		`id` bigint not null auto_increment,
		`base_dir` varchar(255) not null,
		`destin_version` varchar(255) not null,
		`status` varchar(10) not null,
		`activated_on` datetime not null,
		primary key(`id`)
	)
	";
	$create_twentysix=$conn->query($sql26);

	$sql27="create table if not exists `".$pref."qfnl_funnel_meta`(
		`id` bigint not null auto_increment,
		`funnel_id` varchar(255) not null,
		`page_level` varchar(255) not null,
		`page_type` varchar(4) not null,
		`key` varchar(255) not null,
		`value` text not null,
		primary key(`id`)
		)
	";
	$create_twentyseven=$conn->query($sql27);

	//-------for plugins-------
	$sql28="create table if not exists `".$pref."qfnl_cod`(
		`id` bigint not null auto_increment,
		`sell_id` bigint not null,
		`status` int default 0,
		`buyer_email` varchar(255) not null,
		`signed_by` int not null,
		`last_ip` text not null,
		`added_on` datetime not null,
		`updated_on` datetime not null,
		primary key(`id`)
	)";
	$create_twentyeight= $conn->query($sql28);

	$sql29= "create table if not exists `".$pref."media`(
		`id` bigint not null auto_increment,
		`title` varchar(255) not null,
		`file` text not null,
		`type` varchar(255) not null,
		`file_type` varchar(255) not null,
		`size` varchar(255) not null,
		`description` text not null,
		`added_on` varchar(255) not null,
		`updated_on` varchar(255) not null,
		primary key(`id`)
		)
		";
	$create_twentynine= $conn->query($sql29);

	if($create_one && $create_two && $create_three && $create_four && $create_five && $create_six && $create_ten && $create_eleven && $create_twelve && $create_thirteen && $create_forteen && $create_fifteen && $create_sixteen && $create_seventeen && $create_eighteen && $create_nineteen && $create_twenty && $create_twentyone && $create_twentytwo && $create_twentythree && $create_twentyfour && $create_twentyfive && $create_twentysix && $create_twentyseven && $create_twentyeight && $create_twentynine)
	{return 1;}
	else
	{return 0;}
}

function deleteTable($con,$pref)
{

}
?>
