<?php
if(!function_exists('cfrecaptchaDoInstall'))
{
    function cfrecaptchaDoInstall()
    {
        global $mysqli;
        global $dbpref;
      
        
        $table=$dbpref."google_recaptcha";
        
  
        $q1="CREATE TABLE IF NOT EXISTS `".$table."` (
            `id` bigint(20)  NOT NULL AUTO_INCREMENT,
            `g_title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
            `g_version` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
            `credentials` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
            `createdon` timestamp(6) NOT NULL DEFAULT current_timestamp(6) ON UPDATE current_timestamp(6),
             primary key(`id`)
        ) 
        ";
        $mysqli->query($q1);
    }
}
?>