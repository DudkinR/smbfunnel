<?php
if (!function_exists('cfshippingcreative_install_table')) {
  function cfshippingcreative_install_table()
  {
    global $mysqli;
    global $dbpref;
    $mysqli_version = $mysqli->server_version;
    $mysqli_version = (int)str_ireplace(".", "", $mysqli_version);
    $loswer_version =  (int)"552";
    $charset = '';

    if ($mysqli_version > $loswer_version) {
      $charset .= " ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
    } else {
      $charset .= " ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";
    }
    //Shipping Options
    $table1 = $dbpref . 'shipping_options';

    $qry = "CREATE TABLE IF NOT EXISTS `" . $table1 . "` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `name` varchar(255) NOT NULL,
            `cost` varchar(15) NOT NULL,            
            `funnelid` varchar(15) NOT NULL,
            `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
            `updated_at` timestamp NOT NULL DEFAULT current_timestamp(),
            PRIMARY KEY (`id`)
            ) $charset";
    $mysqli->query($qry);

    $table2 = $dbpref . 'mail_templates';
    $qry2 = "CREATE TABLE IF NOT EXISTS `" . $table2 . "` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `template_name` varchar(255) NOT NULL,
      `subject` varchar(355) NOT NULL,
      `content` text NOT NULL,
      `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
      `updated_at` timestamp NOT NULL DEFAULT current_timestamp(),
      PRIMARY KEY (`id`)
      ) $charset";
    $mysqli->query($qry2);

   

    $table6 = $dbpref . 'ship_orders';
    $qry6 = "CREATE TABLE IF NOT EXISTS `" . $table6 . "` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `order_id` varchar(255) NOT NULL,              
                `user_name` varchar(255) NOT NULL,              
                `contact` varchar(255) NOT NULL,
                `amount` varchar(255) NOT NULL,
                `currency` varchar(55) NOT NULL, 
                `products` text NOT NULL,
                `shippingdata` text NOT NULL,
                `payment_method` varchar(255) NOT NULL,
                `payment_id` varchar(255) NOT NULL,         
                `tracking_number` varchar(55) NOT NULL,  
                `carrier_service` varchar(155) NOT NULL,  
                `carrier_url` varchar(255) NOT NULL, 
                `shipment_method` varchar(255) NOT NULL,                 
                `funnelid` varchar(55) NOT NULL,     
                `status` varchar(55) NOT NULL,  
                `ship_date` datetime NULL DEFAULT NULL,
                `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
                `updated_at` timestamp NOT NULL DEFAULT current_timestamp(),
                PRIMARY KEY (`id`)
                ) $charset";
    $mysqli->query($qry6);
  }
}
