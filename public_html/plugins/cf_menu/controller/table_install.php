<?php
if(!function_exists('cfcreative_install_table')){
    function cfcreative_install_table(){
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
        $table = $dbpref.'cfmenu';
        $qry="CREATE TABLE IF NOT EXISTS `".$table."` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `form_name` varchar(255) NOT NULL,
            `form_desc` varchar(255) NOT NULL,
            `custom_url` text NOT NULL,
            `dropndown` text NOT NULL,
            `logo_details` text NOT NULL,
            `manage_styles` text NOT NULL,
            `extra_buttons` text NOT NULL,
            `choose_theme` varchar(255) NOT NULL,
            `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
            `updated_at` timestamp NOT NULL DEFAULT current_timestamp(),
            PRIMARY KEY (`id`)
            ) $charset";
        $mysqli->query($qry);
    }
}
?>