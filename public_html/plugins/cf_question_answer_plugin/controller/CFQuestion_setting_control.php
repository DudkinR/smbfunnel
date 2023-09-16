<?php
if (!class_exists('CFQuestion_setting_control')) {
    class CFQuestion_setting_control
    {
        function __construct($arr)
        {
            $this->loader = $arr['loader'];
        }

               
        function getStyleUI($data)

       {
            global $mysqli;
            global $dbpref;
            $table = $dbpref ."cfproduct_question_setting";
            $value="";
            $uid=$mysqli->real_escape_string($data['ui_id']);
            $que_font = $mysqli->real_escape_string($data['que_font']);
            $ans_font = $mysqli->real_escape_string($data['ans_font']);
            $que_tcolor = $mysqli->real_escape_string($data['que_tcolor']);
            $ans_tcolor = $mysqli->real_escape_string($data['ans_tcolor']);
            $que_bg = $mysqli->real_escape_string($data['que_bg']);
            $ans_bg = $mysqli->real_escape_string($data['ans_bg']);
            $update_insert=$mysqli->real_escape_string($data['update_insert']);

            if ($update_insert == 'create') {

            $sql_status = ($mysqli->query("INSERT INTO `$table`(`que_font`, `ans_font`, `que_tcolor`, `ans_tcolor`, `que_bg`, `ans_bg`) VALUES ('" . $que_font . "','" . $ans_font . "','" . $que_tcolor . "','" . $ans_tcolor ."','" . $que_bg ."' ,'" . $ans_bg ."' )") ) ? 1 : 0;
            $insert_id = $mysqli->insert_id;
            }
            
            else {
                $sql_status = ($mysqli->query("UPDATE `" . $table . "` SET  `que_font`='" . $que_font . "',`ans_font`='" . $ans_font . "',`que_tcolor`='" . $que_tcolor . "',`ans_tcolor`='" . $ans_tcolor ."',`que_bg`='" . $que_bg ."',`ans_bg`='" . $ans_bg ."' WHERE `ui_id`='" . $uid . "'")) ? 1 : 0;
                $insert_id = $uid;
            }
            if ($sql_status) $msg = "Form saved successfully.";
            else $msg = "Smething went wrong! Please try again";
            echo json_encode(array(
                'msg' => $msg,
                'status' => $sql_status
            ));
              
            
         
         
         
         
         
     }

    }
}

?>