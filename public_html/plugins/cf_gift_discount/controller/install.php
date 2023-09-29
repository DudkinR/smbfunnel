<?php
if(!function_exists('CFDiscount_Install'))
{
  function CFDiscount_Install()
  {
    global $mysqli;
    global $dbpref;
    $mysqli_version = $mysqli->server_version;
    $mysqli_version = (int)str_ireplace(".","",$mysqli_version);
    $loswer_version =  (int)"552";
    $charset='';

    if( $mysqli_version > $loswer_version )
    {
      $charset .= " ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
    }else{
      $charset .= " ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";

    }
    $table1=$dbpref."issue_gift";
    $table =$dbpref."gift_cards";
    $table2 =$dbpref."gift_cards_settings";
    //table for setup
    $form_table_query="CREATE TABLE IF NOT EXISTS `".$table."` (
      `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
      `discount_type` enum('giftcard','percentage') NOT NULL,
      `percentage` decimal(5,2) DEFAULT NULL,
      `apply_type` enum('all','custom') NOT NULL,
      `gift_code` varchar(255) NOT NULL,
      `products` text DEFAULT NULL,
      `initial_value` decimal(15,2) NOT NULL DEFAULT 0.00,
      `currency` varchar(255) NOT NULL DEFAULT 'USD',
      `remaining_value` decimal(15,2) NOT NULL DEFAULT 0.00,
      `member_id` bigint(20) DEFAULT NULL,
      `expiration_type` enum('no_expiration','set_expiration') NOT NULL,
      `expiration_date` date NOT NULL DEFAULT '2030-01-01',
      `redeem_no` int(11) NOT NULL DEFAULT 1,
      `status` enum('1','0') NOT NULL,
      `notes` text DEFAULT NULL,
      `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
      `updated_at` timestamp NOT NULL DEFAULT current_timestamp(),
      `user_id` bigint(16) DEFAULT 1,
      PRIMARY KEY (`id`),
      KEY `member_id` (`member_id`)
     ) $charset;   
    ";
     $mysqli->query($form_table_query);
    
    //table for optins
    $optin_table_query="CREATE TABLE IF NOT EXISTS `".$table1."` (
      `id` bigint(20) NOT NULL AUTO_INCREMENT,
      `giftcode` varchar(255) NOT NULL,
      `giftcard_id` bigint(20) DEFAULT NULL,
      `order_id` varchar(255) DEFAULT NULL,
      `comment` text DEFAULT NULL,
      `name` varchar(255) DEFAULT NULL,
      `email` varchar(255) DEFAULT NULL,
      `last_deduct_value` decimal(16,4) DEFAULT NULL,
      `type` enum('giftcard','discount') NOT NULL DEFAULT 'giftcard',
      `created_at` datetime NOT NULL,
      `user_id` bigint(20) DEFAULT 1,
      PRIMARY KEY (`id`)
       ) $charset;";
    $mysqli->query($optin_table_query);

      //table for optins
      $optin_table_query1="CREATE TABLE IF NOT EXISTS `".$table2."` (
        `giftcard` text DEFAULT NULL,
        `discount` text DEFAULT NULL,
        `gemail_subject` text DEFAULT NULL,
        `gemail_content` text DEFAULT NULL,
        `demail_subject` text DEFAULT NULL,
        `demail_content` text DEFAULT NULL,
        `user_id` bigint(20) DEFAULT 1
          ) $charset;";
      $mysqli->query($optin_table_query1);
      $user_id=$_SESSION['user' . get_option('site_token')]; 
      $qry1="INSERT INTO `".$table2."` (`giftcard`,`discount`,`gemail_subject`,`gemail_content`,`demail_subject`,`demail_content`,`user_id`)VALUE('{\"label_text\":\"Enter Gift Code\",\"button_text\":\"Apply\",\"button_bcolor\":\"023059\",\"button_color\":\"FFFFFF\",\"error_color\":\"FF0000\",\"result_bcolor\":\"CEECF2\",\"result_color\":\"777777\",\"customCSS\":\"\"}','{\"label_text\":\"Enter Discount Code\",\"button_text\":\"Apply\",\"button_bcolor\":\"023059\",\"button_color\":\"FFFFFF\",\"error_color\":\"FF0000\",\"result_bcolor\":\"CEECF2\",\"result_color\":\"777777\",\"customCSS\":\"\"}','Someone Sent {initial_value} {currency} Gift card','<p>Hi {name},</p>\r\n<p>Your {initial_value} {currency} gift card is active. Keep this email or write down your gift card number.</p>\r\n<p><strong>{giftcode}</strong></p>\r\n<p>If you did not raise the request please write to our support team.</p>\r\n<p>Thanks</p>','Someone Sent {percentage}% Discount Code','<p>Hi {name},</p>\r\n<p>Your {percentage} discount code is active. Keep this email or write down your discount code number.</p>\r\n<p><strong>{discount}</strong></p>\r\n<p>If you did not raise the request please write to our support team.</p>\r\n<p>Thanks</p>',".$user_id.")";
      $mysqli->query($qry1);
      
      $qry2 ="ALTER TABLE `$table` ADD `is_gift_product` ENUM('yes','no') NOT NULL DEFAULT 'no' AFTER `discount_type`";
      $mysqli->query($qry2);
    
    }
}
?>