<?php
  if(!function_exists('cfproduct_review_install'))
  {
    function cfproduct_review_install()
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
      
      $table=$dbpref."cfproduct_review_records";
      $table1=$dbpref."cfproduct_review_setting";
      $table2=$dbpref."cfproduct_review_likes";

      $q1="CREATE TABLE IF NOT EXISTS `".$table."` (
        `id` bigint(20) NOT NULL AUTO_INCREMENT,
        `product_id` varchar(255) NOT NULL,
        `product_title` text NOT NULL,
        `name` varchar(255) NOT NULL,
        `email` varchar(255) NOT NULL,
        `rating` int(2) NOT NULL DEFAULT 0,
        `summary` text NOT NULL,
        `media` text DEFAULT NULL,
        `readed` int(1) NOT NULL DEFAULT 0,
        `approved` int(3) NOT NULL DEFAULT 1,
        `added_on` datetime NOT NULL,
        PRIMARY KEY (`id`)
      ) $charset";
      $mysqli->query($q1);

      $q2="CREATE TABLE IF NOT EXISTS `".$table1."` (
        `email_content` text DEFAULT NULL,
        `email_subject` text DEFAULT NULL,
        `rsetting` text DEFAULT NULL,
        `rtext` text DEFAULT NULL,
        `rstyle` text DEFAULT NULL,
        `formsetting` text DEFAULT NULL,
        `formstyle` text DEFAULT NULL,
        `formtext` text DEFAULT NULL
      ) $charset";
      $mysqli->query($q2);
      
      $q3="CREATE TABLE IF NOT EXISTS `".$table2."` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `name` varchar(255) NOT NULL,
        `email` varchar(255) NOT NULL,
        `mid` bigint(20) NOT NULL,
        `pid` bigint(20) NOT NULL,
        `rid` bigint(20) NOT NULL,
        `rlike` int(11) NOT NULL DEFAULT 0,
        PRIMARY KEY (`id`)
      ) $charset";
      $mysqli->query($q3);
      
      $sql="INSERT INTO `".$table1."` (`email_subject`,`email_content`,`rsetting`,`rtext`,`rstyle`,`formsetting`,`formstyle`,`formtext`) VALUE ('Thanks for the review','<p>Hi {name},</p>\r\n<p>Thanks for the review.</p>\r\n<p><strong>Please visit the below URL to verify yourself</strong>.</p>\r\n<p>{verification_url}</p>\r\n<p>Thanks</p>','{\"showld\":true,\"showp\":true,\"shows\":true,\"showsummary\":true,\"showavg\":true,\"showsumbox\":true,\"aapproved\":false,\"markasread\":false}','{\"boxposition\":\"c\",\"headertext\":\"Customers Ratings And Reviews\",\"ratingtext\":\"Average Rating\",\"readmore\":\"Read More\",\"readless\":\"Read Less\",\"summaryletter\":\"-1\",\"rateproducttext\":\"Rate Product\",\"reviewstext\":\"Reviews\",\"pnext\":\"Next \\&#187;\",\"pprev\":\"\\&#171; Prev\"}','{\"rwidth\":\"80\",\"avgratingcolor\":\"F1F1F1\",\"starcolor\":\"FFC107\",\"star5\":\"4CAF50\",\"star4\":\"2196F3\",\"star3\":\"00BCD4\",\"star2\":\"FF9800\",\"star1\":\"F44336\",\"rprocolor\":\"000000\",\"rprobackcolor\":\"FFFFFF\",\"readmorecolor\":\"007BFF\",\"summarycolor\":\"212529\",\"summaryfize\":\"13\",\"pageac\":\"4CAF50\",\"pagehoc\":\"DDDDDD\",\"pagecolor\":\"FFFFFF\",\"customcss\":\"\"}','{\"rallowfu\":true,\"rfext\":true,\"rshowstar\":true,\"rshowsummary\":true}','{\"rallowall\":\"all\",\"rfsize\":\"5\",\"rmaxfile\":\"5\",\"rfextesnions\":\".png, .jpg, .jpeg, .gif\",\"rboxposition\":\"c\",\"rheadertext\":\"Rate this product\",\"rlabeltext\":\"Enter Summary\",\"rsum_place\":\"Share details of your own experience at this place\",\"rsum_length\":\"2000\",\"rupload_text\":\"Upload File\",\"rsubbtn_text\":\"Submit Review\",\"rdeletebtn_text\":\"Delete\"}','{\"rfrmwidth\":\"35\",\"rhbackcolor\":\"FFFFFF\",\"rhcolor\":\"000000\",\"rstarcolor\":\"FFC700\",\"rstarhover\":\"DEB217\",\"rstardefault\":\"CCCCCC\",\"rlabelc\":\"000000\",\"rfooterc\":\"FFFFFF\",\"rsub_backcolor\":\"007BFF\",\"rsub_color\":\"FFFFFF\",\"rcustomcss\":\"\"}')";
      $mysqli->query($sql);
    }
  }
?>