<?php
if (!class_exists('CF_Social_share_form_controller')) {
    class CF_Social_share_form_controller
    {
        var $pref;

        function __construct($arr)
        {


            $this->loader = $arr['loader'];
        }
        function AddSocialData($data)
        {
            global $mysqli;
            global $dbpref;

            $table = $dbpref . "cf_social_share";

            // $nerwork_name = $mysqli->real_escape_string($data['network_name']);
            $network_name = $_POST['network_name'];

            $decodedata = (base64_decode($network_name[0]));
            // print_r($decodedata);  
            // exit;
            if (isset($_POST['network_name'])) {
                $query = ($mysqli->query("SELECT * FROM `$table` WHERE `network_name`='{$decodedata}'"));
                $da = mysqli_fetch_array($query);
                if (($da) > 0) {
                    echo "201";
                } else {
                    foreach ($network_name as $item) {

                        $item_data = base64_decode($item);

                        $sql_status = ($mysqli->query("INSERT INTO `$table` (`network_name`) VALUES('" . $item_data . "')")) ? 1 : 0;
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

        function updateSocialData($data)
        {
            global $mysqli;
            global $dbpref;
            $table = $dbpref . "cf_social_share";

            $form_id = $data['edit_id'];
            $network_update = $mysqli->real_escape_string($data['network_update']);
            $network_update_data = base64_decode($network_update);

            $sql_status = ($mysqli->query("UPDATE `$table` SET  `network_name`= '" . $network_update_data . "'WHERE  `social_id`='" . $form_id . "' ")) ? 1 : 0;
            if ($sql_status) $msg = "Form update successfully.";
            else $msg = "Something went wrong! Please try again";
            echo json_encode(array(
                'msg' => $msg,
                'status' => $sql_status
            ));
        }

        function DeteteData($data)
        {

            global $mysqli;
            global $dbpref;
            $table = $dbpref . "cf_social_share";
            $d_id = $data['del_id'];
            $sql_status = ($mysqli->query("DELETE FROM `$table`  WHERE  `social_id`='" . $d_id . "' ")) ? 1 : 0;
            if ($sql_status) $msg = "Form update successfully.";
            else $msg = "Something went wrong! Please try again";
            echo json_encode(array(
                'msg' => $msg,
                'status' => $sql_status
            ));
        }

        function previewData($fid)
        {
            global $mysqli;
            global $dbpref;
            $table = $dbpref . "cf_social_share";
            if (isset($_POST['checking_answer_btn'])) {
                $fid = $_POST['f_id'];
                $result_array = [];
                $sql = ($mysqli->query("SELECT * FROM `$table` where `social_id`='$fid'"));
                $num_rows = mysqli_num_rows($sql);
                if ($num_rows > 0) {
                    foreach ($sql as $rows) {
                        array_push($result_array, $rows);
                        header('content-type:application/json');
                        echo json_encode($result_array);
                    }
                } else {
                    echo $return = "No record found";
                }
            }
        }
    }
}
?>