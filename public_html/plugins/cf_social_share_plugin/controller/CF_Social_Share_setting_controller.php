<?php
if (!class_exists('CF_Social_share_setting_controller')) {
    class CF_Social_share_setting_controller
    {
        var $pref;
    
        function __construct($arr)
        {
            
            
            $this->loader = $arr['loader'];
            
            
        }
             
      
        function addSocialSetting($data)
        {
            global $mysqli;
            global $dbpref;
            $table = $dbpref . "cf_social_setting";

            $icon_id=$mysqli->real_escape_string($data['icon_id']);

            $display= $mysqli->real_escape_string($data['cf_display']);
            $icon_shape= $mysqli->real_escape_string($data['cf_shape']);

            $facebook= $mysqli->real_escape_string($data['icon_facebook']);
            $twitter= $mysqli->real_escape_string($data['icon_twitter']);
            $instagram= $mysqli->real_escape_string($data['icon_instagram']);
            $youtube= $mysqli->real_escape_string($data['icon_youtube']);
            $google= $mysqli->real_escape_string($data['icon_google']);
            $pinterest= $mysqli->real_escape_string($data['icon_pinterest']);
            $linkedin= $mysqli->real_escape_string($data['icon_linkedin']);
            $whatsapp= $mysqli->real_escape_string($data['icon_whatsapp']);
            $skype= $mysqli->real_escape_string($data['icon_skype']);
            $tumblr= $mysqli->real_escape_string($data['icon_tumblr']);
            $yahoo= $mysqli->real_escape_string($data['icon_yahoo']);
            $reddit= $mysqli->real_escape_string($data['icon_reddit']);
            $digg= $mysqli->real_escape_string($data['icon_digg']);
            $blogger= $mysqli->real_escape_string($data['icon_blogger']);
            $buffer= $mysqli->real_escape_string($data['icon_buffer']);
            $vkontakte= $mysqli->real_escape_string($data['icon_vkontakte']);
            $xing= $mysqli->real_escape_string($data['icon_xing']);
            $telegram= $mysqli->real_escape_string($data['icon_telegram']);   
   

            $cf_icon_color=json_encode( array("icon_facebook" => $facebook,"icon_twitter"=>$twitter,
            "icon_instagram" => $instagram ,"icon_youtube"=>$youtube,"icon_google"=>$google,"icon_pinterest"=>$pinterest,
            "icon_linkedin"=>$linkedin,"icon_whatsapp"=>$whatsapp,"icon_skype"=>$skype,"icon_tumblr"=>$tumblr,
            "icon_yahoo"=>$yahoo,"icon_reddit"=>$reddit,"icon_digg"=>$digg,"icon_blogger"=>$blogger,
            "icon_buffer"=>$buffer,"icon_vkontakte"=>$vkontakte,"icon_xing"=>$xing,"icon_telegram"=>$telegram));

            $update_insert=$mysqli->real_escape_string($data['update_insert']);

            if ($update_insert == 'create') {


            $sql_status = ($mysqli->query("INSERT INTO `$table`(`display`,`icon_shape`,icon_color) VALUES ('" .$display . "','" .$icon_shape . "','" .$cf_icon_color . "')")) ? 1 : 0;
            $insert_id = $mysqli->insert_id;
                }
            else{
                $sql_status = ($mysqli->query("UPDATE `" . $table . "` SET `display`='" . $display ."', `icon_shape`='" . $icon_shape ."',`icon_color`='" . $cf_icon_color ."' WHERE `icon_id`='" . $icon_id . "'")) ? 1 : 0;
                $insert_id = $icon_id;

            }
            
            if ($sql_status) $msg = "Form update successfully.";
            else $msg = "Something went wrong! Please try again";
            echo json_encode(array(
                'msg' => $msg,
                'status' => $sql_status
            ));
        }
        



    }
}
