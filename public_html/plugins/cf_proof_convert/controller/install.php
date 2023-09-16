<?php
if(!function_exists('cfProofConvertInstal'))
{
  function cfProofConvertInstall($pref)
  {
    global $mysqli;
    global $dbpref;
    $table=$dbpref.$pref."setup";
    $table1=$dbpref.$pref."notification_data";
    //table for setup
    $form_table_query="CREATE TABLE IF NOT EXISTS `".$table."` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `title` varchar(500) NOT NULL,
      `setup` text DEFAULT NULL,
      `fake_data` text DEFAULT NULL,
      `setup_css` text DEFAULT NULL,
      `notification` text DEFAULT NULL,
      `funnels` text DEFAULT NULL,
      `impressions` int DEFAULT NULL,
      `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
      `updated_at` timestamp NOT NULL DEFAULT current_timestamp(),
      PRIMARY KEY (`id`)
    )";
     $mysqli->query($form_table_query);
    
    //table for optins
    $optin_table_query="CREATE TABLE IF NOT EXISTS `".$table1."` (
      `id` bigint not null auto_increment,
      `name` varchar(500) DEFAULT NULL,
      `email` varchar(500) DEFAULT NULL,
      `address` text DEFAULT NULL,
      `product_id` int(9) DEFAULT NULL,
      `time` text DEFAULT NULL,
      `added_on` timestamp NOT NULL DEFAULT current_timestamp(),
      primary key(`id`)
    )";
    $mysqli->query($optin_table_query);
  }
}
?>