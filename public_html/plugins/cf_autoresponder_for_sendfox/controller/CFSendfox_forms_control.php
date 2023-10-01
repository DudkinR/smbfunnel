<?php
if (!class_exists('CFSendfox_forms_control')) {
    class CFSendfox_forms_control
    {
        function __construct()
        {
            // $this->loader=$arr['loader'];
            self::loadMethods();
        }

        public  function loadMethods()
        {
            global $mysqli;
            global $dbpref;
            $table = $dbpref . 'cfsendfox_autoresponders';
            $qry = $mysqli->query("select * from `" . $table . "` order by `id` desc");
            if (!$qry || $qry->num_rows < 1) {
                return;
            }

            while ($r = $qry->fetch_object()) {
                $id = "cfautoressend_" . $r->id;
                $title = $r->title;
                $method = $r->type;
                $listidd = $r->listid;
                $apikeyy = $r->apikey;


                $credential_arr = array($apikeyy, $listidd);

                if ($method == 'sendfox') {


                    register_autoresponder($id, $title, function ($data, $arg2) {

                        $tocheck = self::Addtolist($arg2['0']['0'], $arg2['0']['1'], $data['name'], $data['email']);
                    }, array($credential_arr));
                } else {
                    return false;
                }
            }
        }


        function getSaveSettings($id)
        {
            global $mysqli;
            global $dbpref;
            $table = $dbpref . 'cfsendfox_autoresponders';
            $id = (int)$id;

            $result = $mysqli->query("select * from `" . $table . "` where `id`=" . $id);
            while ($row = $result->fetch_assoc()) {
                return $row;
            }
        }



        public function getAjaxRequest($ajax_datas)
        {

            // print_r($ajax_datas);
            global $mysqli;
            global $dbpref;
            $name = "";
            $table = $dbpref . 'cfsendfox_autoresponders';
            $title = $mysqli->real_escape_string(trim($ajax_datas['title']));
            $apikey = $mysqli->real_escape_string(trim($ajax_datas['apikey']));
            $listid = $mysqli->real_escape_string(trim($ajax_datas['listid']));
            $email = $mysqli->real_escape_string(trim($ajax_datas['email']));
            $autotype = $mysqli->real_escape_string(trim($ajax_datas['autotype']));
            $id = $mysqli->real_escape_string(trim($ajax_datas['id']));
            $date = date('Y-m-d H:i:s');
            if ($autotype == 'sendfox') {
                $returnresult =  self::Addtolist($apikey, $listid, $name, $email);
     
                if ($returnresult == 200) {

                    if ($id > 0) {
                        $sql = "UPDATE `" . $table . "` SET `title`='" . $title . "',`listid`='" . $listid . "',`apikey`='" . $apikey . "',`email`='" . $email . "' WHERE `id`=" . $id;
                    } else {
                        $sql = "INSERT INTO `" . $table . "`(`title`, `type`, `email`, `listid`, `apikey`, `date_created`) VALUES ('" . $title . "','" . $autotype . "','" . $email . "','" . $listid . "','" . $apikey . "','" . $date . "')";
                    }
                    $return_status = ($mysqli->query($sql)) ? 1 : 0;


                    if ($return_status) {
                        echo json_encode(array("status" => 1, "message" => "Saved successfully"));
                        die();
                    } else {
                        echo json_encode(array("status" => 0,  "message" => "Unable to save the setup"));
                        die();
                    }
                } else {
                    echo json_encode(array("status" => 0,  "message" => "Unable to save the setup"));
                    die();
                }
            }
        }



        function getAutoresponderCount()
        {
            global $mysqli;
            global $dbpref;
            $table = $dbpref . 'cfsendfox_autoresponders';

            $qry = $mysqli->query("select count(`id`) as `total_autoresponders` from `" . $table . "`");

            $r = $qry->fetch_object();
            return $r->total_autoresponders;
        }

        function getAllautores($total_autoresponders = false, $max_limit = false, $page = 1)
        {
            global $mysqli;
            global $dbpref;
            $table = $dbpref . 'cfsendfox_autoresponders';
            $page = $mysqli->real_escape_string($page);
            if (!$max_limit) {
                $max_limit = $mysqli->real_escape_string($max_limit);
            }

            $arr = array();
            $limit = "";

            if ($max_limit !== false && is_numeric($max_limit) && is_numeric($page)) {
                $page = ($page * $max_limit) - $max_limit;
                $limit = " limit " . $page . ',' . $max_limit;
            }


            $search = "";

            if (isset($_POST['onpage_search'])) {
                $search = trim($mysqli->real_escape_string($_POST['onpage_search']));
                $search = str_replace('_', '[_]', $search);
                $search = str_replace('%', '[%]', $search);
                $search = " and `title` like '%" . $search . "%'";
            }

            $order_by = "`id` desc";
            if (isset($_GET['arrange_records_order'])) {
                $order_by = base64_decode($_GET['arrange_records_order']);
            }

            $date_between = dateBetween('date_created', null, true);

            if (strlen($date_between[0]) > 0) {
                $search .= $date_between[1];
            }


            $qry = $mysqli->query("select * from `" . $table . "` where 1" . $search . " order by " . $order_by . $limit);

            $arr = [];
            if ($qry->num_rows > 0) {
                while ($data = $qry->fetch_assoc()) {
                    $arr[] = $data;
                }
            }
            return $arr;
        }



        function Addtolist($apikey, $listid, $name, $email)
        {

            if (filter_var($email, FILTER_VALIDATE_EMAIL)) {

                $curl = curl_init();

                $aurl = "https://api.sendfox.com/contacts?email=" . $email . "&lists=" . $listid . "&first_name=" . $name;
                curl_setopt_array($curl, array(
                    CURLOPT_URL => $aurl,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => '',
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => 'POST',
                    CURLOPT_HTTPHEADER => array(
                        "Authorization: Bearer $apikey"
                    ),
                ));

                $response = curl_exec($curl);
                $status_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
                // print_r($status_code);
                curl_close($curl);
                if ($status_code == 200) {
                    return $status_code;
                } else {
                    return 0;
                }
            } else {
                return 0;
            }
        }
    }
}
