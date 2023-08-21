<?php
if(!function_exists('cfsocialsharetable')){
    
    function cfsocialsharetable(){
        global $mysqli;
        global $dbpref;
        $table=$dbpref."cf_social_share";

        $q1="CREATE TABLE IF NOT EXISTS `".$table."` (
           `social_id` int(11) NOT NULL AUTO_INCREMENT,
           `network_name` text NOT NULL,
            primary key(`social_id`)
        )";
        $mysqli->query($q1);

        $table2=$dbpref."cf_social_setting";

        $q2="CREATE TABLE IF NOT EXISTS `".$table2."` (
          `icon_id` int(11) NOT NULL AUTO_INCREMENT,
          `display` text COLLATE utf8mb4_unicode_ci NOT NULL,
          `icon_shape` int(11) COLLATE utf8mb4_unicode_ci NOT NULL,
          `icon_color` text COLLATE utf8mb4_unicode_ci NOT NULL,
           primary key(`icon_id`)
        ) 
        ";
      $mysqli->query($q2);    

    $sql="INSERT INTO `".$table2."` ( `display`, `icon_shape`, `icon_color`) VALUES ('block', 0, '{\"icon_facebook\":\"#3B5998\",\"icon_twitter\":\"#55ACEE\",\"icon_instagram\":\"#FA55D4\",\"icon_youtube\":\"#BB0000\",\"icon_google\":\"#ED1710\",\"icon_pinterest\":\"#CB2027\",\"icon_linkedin\":\"#00AFF0\",\"icon_whatsapp\":\"#13F848\",\"icon_skype\":\"#00AFF0\",\"icon_tumblr\":\"#2C4762\",\"icon_yahoo\":\"#430297\",\"icon_reddit\":\"#FF5700\",\"icon_digg\":\"#2217FF\",\"icon_blogger\":\"#FF930D\",\"icon_buffer\":\"#FFFBFA\",\"icon_vkontakte\":\"#1F38B5\",\"icon_xing\":\"#11820A\",\"icon_telegram\":\"#7AF4FF\"}')";
     $mysqli->query($sql);
        
        
    }
}
?>
