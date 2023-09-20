<?php
if(!function_exists('cfspinnerinstall'))
{
    function cfspinnerinstall($pref)
    {
    global $mysqli;
        global $dbpref;
 
         $input_table_query="CREATE TABLE IF NOT EXISTS `".$dbpref."spinwheel_setting` (
           `id` int(11) NOT NULL AUTO_INCREMENT,
 `cfspinnerwheel` text DEFAULT NULL,
 `cfspinnernum` text DEFAULT NULL,
 `cfspinfont`  text DEFAULT NULL,
 `cfspinfontstyle` text DEFAULT NULL,
 `cfspinslicefontsize` text DEFAULT NULL,
 `cfspinmainheader`text DEFAULT NULL,
 `cfspinmaifooter` text DEFAULT NULL,
 `cfspinwheeltype` text DEFAULT NULL,
 `cfspinner_theme` text  DEFAULT NULL,
 `cfslicepricenames` text DEFAULT NULL,
 `cfspinnerbgimgurl` text DEFAULT NULL,
 `cfspinmailsub` text DEFAULT NULL,
 `cf_spinner_mailerbody` text DEFAULT NULL,
 `created_at` varchar(500) DEFAULT NULL,
 PRIMARY KEY (`id`)
) ";
        $mysqli->query($input_table_query);


        $input_table_query1="CREATE TABLE IF NOT EXISTS `".$dbpref."spinner_popup_forminputs` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `cfspinwheelid` int(11) DEFAULT NULL,
            `name` varchar(500) DEFAULT NULL,
            `placeholder` varchar(500) DEFAULT NULL,
            `type` varchar(500) DEFAULT NULL,
            `title` varchar(500) DEFAULT NULL,
            `position` int(11) DEFAULT NULL,
            `required` int(11) DEFAULT NULL,
            `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
            PRIMARY KEY (`id`)
            )";
        $mysqli->query($input_table_query1);


        $sqlquery="CREATE TABLE IF NOT EXISTS `".$dbpref."spinwheelusers` (
           `id` bigint not null auto_increment,
            `wheelid` int(11) not null,
            `name` varchar(255) not null,
            `email` varchar(255) not null,
            `exf` text not null,
            `winprize` text not null,
            `mailstatus` text not null,
            `added_on` timestamp not null default current_timestamp(),
            primary key(`id`)
          
           ) ";



        $mysqli->query($sqlquery);

    }



    
}
?>