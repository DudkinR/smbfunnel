<?php
  if(!function_exists('cfproduct_question_install'))
  {
    function cfproduct_question_install()
    {
      global $mysqli;
      global $dbpref;
    
      
      $table=$dbpref."cfproduct_question_records";
      

      $q1="CREATE TABLE IF NOT EXISTS `".$table."` (
      `id` bigint(20) NOT NULL AUTO_INCREMENT,
      `product_id` varchar(255) NOT NULL,
      `product_title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
      `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
      `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
      `question` text COLLATE utf8mb4_unicode_ci NOT NULL,
      `answer` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
      `status` int(255) NOT NULL,
      `added_on` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
      `user_id` int(16) default 1,
       primary key(`id`)
      ) 
      ";
      $mysqli->query($q1);

      $table2=$dbpref."cfproduct_question_setting";
      

      $q2="CREATE TABLE IF NOT EXISTS `".$table2."` (
          `ui_id` int(11) NOT NULL AUTO_INCREMENT,
          `que_font` text COLLATE utf8mb4_unicode_ci NOT NULL,
          `ans_font` text COLLATE utf8mb4_unicode_ci NOT NULL,
          `que_tcolor` text COLLATE utf8mb4_unicode_ci NOT NULL,
          `ans_tcolor` text COLLATE utf8mb4_unicode_ci NOT NULL,
          `que_bg` text COLLATE utf8mb4_unicode_ci NOT NULL,
          `ans_bg` text COLLATE utf8mb4_unicode_ci NOT NULL,
          `user_id` int(16) default 1,
           primary key(`ui_id`)
        ) 
        ";
      $mysqli->query($q2);

      $sql="INSERT INTO `".$table2."`(`ui_id`,`que_font`, `ans_font`, `que_tcolor`, `ans_tcolor`, `que_bg`, `ans_bg`) VALUES
      (1, '16', '15', '#195391f5', '#1a19188f', '#FFFFFF', '#FFFFFF')";
      $mysqli->query($sql);


     
    }
  }
?>