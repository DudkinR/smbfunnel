<?php
if (!class_exists('CFQuestion_form_control')) {
    class CFQuestion_form_control
    {
        function __construct($arr)
        {
            $this->loader = $arr['loader'];
        }

        function getAllSetups($total_setups = false, $max_limit = false, $page = 1, $product_id)
        {
            global $mysqli;
            global $dbpref;
            $table = $dbpref . "cfproduct_question_records";
            $page = $mysqli->real_escape_string($page);
            if (!$max_limit) {
                $max_limit = $mysqli->real_escape_string($max_limit);
            }

            $arr = array();
            $limit = "";

            if ( $max_limit !== false && is_numeric( $max_limit ) && is_numeric( $page ) ) {
                $page = ($page * $max_limit) - $max_limit;
                $limit = " limit " . $page . ',' . $max_limit;
            }
            $search = "";
            if ( isset( $_POST['onpage_search'] ) ) {

                $search = trim($mysqli->real_escape_string($_POST['onpage_search']));
                $search = str_replace('_', '[_]', $search);
                $search = str_replace('%', '[%]', $search);
                // filter data with page name and page url and date
                $search = " AND `name` LIKE '%" . $search . "%' OR `email` LIKE '%" . $search . "%'  OR `added_on` LIKE '%" . $search . "%'";
            }
            $order_by = "`id` DESC";
            if (isset($_GET['arrange_records_order'])) {
                $order_by = base64_decode($_GET['arrange_records_order']);
            }
            $date_between = dateBetween('added_on', null, true);
            if (strlen($date_between[0]) > 0) {
                $search .= $date_between[1];
            }
            if( $product_id ){            
                $search .=" AND `product_id`=$product_id";
            }
            $qry = $mysqli->query("SELECT * FROM `$table` WHERE 1 " . $search . " ORDER BY " . $order_by . $limit);
            $arr = [];
            if ($qry->num_rows > 0) {
                while ( $data = $qry->fetch_assoc() ) {
                    $arr[] = $data;
                }
            }
            return $arr;
        }
        function getProdcuts()
        {
            global $mysqli;
            global $dbpref;
            $table = $dbpref . 'all_products';
            $sql   = "SELECT `id`,`title` FROM `$table` WHERE `parent_product`=0";
            $qry   = $mysqli->query( $sql );
            $arr   = [];
            if( $qry->num_rows > 0 ) {
                while ( $data = $qry->fetch_assoc() ) {
                    $arr[] = $data;
                }
            }
            return $arr;
        }
        function getProductId($pid){
            global $mysqli;
            global $dbpref;
            $table = $dbpref . 'all_products';
            $sql = "SELECT `id`,`title` FROM `$table` WHERE `productid`='$pid'";
            $qry = $mysqli->query($sql);
            if( $qry->num_rows > 0 && $data = $qry->fetch_assoc() )
            {
                return $data;
            }
            return false;
        }

        function getSetupsCount()
        {
            global $mysqli;
            global $dbpref;
            $table = $dbpref . "cfproduct_question_records";
            $qry = $mysqli->query("select count(`id`) as `total_setup` from `" . $table . "`");
            if ($qry->num_rows > 0) {
                $r = $qry->fetch_object();
                return $r->total_setup;
            }
        }

        function getFormUI($prdid, $v, $funnel_id )
        {
            global $mysqli;
            global $dbpref;
            $table = $dbpref . 'cfproduct_question_records';
            $table2 = $dbpref . 'cfproduct_question_setting';
            $data = self::getProductId($prdid);
            if($data)
            {
                $pro_title  = $data['title'];
                $pid = $data['id'];
                // counting total number of posts
                $count_result = $mysqli->query("SELECT count(*) as 'allcount' FROM  `$table`  WHERE product_id='$pid' ");
                $count_fetch =  $count_result->fetch_assoc();
                $postCount = $count_fetch['allcount'];
                $limit=5;
                $sql = $mysqli->query("SELECT * FROM `$table` WHERE `product_id`='$pid' ORDER BY `id` desc LIMIT 0," .$limit);        
                $sql2 = $mysqli->query("SELECT * FROM  `$table2`");
                
                require CFQUESTION_PLUGIN_DIR_PATH . "/view/shortcode.php";
            }
        }

        function insertFormData($post_data)
        {

            global $mysqli;
            global $dbpref;
            $table = $dbpref . "cfproduct_question_records";

            $product_id = $mysqli->real_escape_string($post_data['product_id']);

            $product_title = $mysqli->real_escape_string($post_data['product_title']);

            $name = $mysqli->real_escape_string(strip_tags($post_data['name']));
            $email = $mysqli->real_escape_string(strip_tags($post_data['email']));
            $question = $mysqli->real_escape_string(strip_tags($post_data['question']));
            $status = "0";
            $date = date("y-m-d h:m:s");
            $sql_status = ($mysqli->query("INSERT INTO `" . $table . "` (`product_id`,`product_title`,`name`, `email`, `question`,`status`,`added_on`) VALUES ('" . $product_id . "','" . $product_title . "','" . $name . "','" . $email . "','" . $question . "','" . $status . "' ,'" . $date . "' )")) ? 1 : 0;

            if ($sql_status) $msg = "Form saved successfully.";
            else $msg = "Smething went wrong! Please try again";
            echo json_encode(array(
                'msg' => $msg,
                'status' => $sql_status
            ));
            
        }
        function insertAnswer($data)
        {
            global $mysqli;
            global $dbpref;
            $table = $dbpref . "cfproduct_question_records";
            $form_id = $data['answer_id'];
            $answer = $mysqli->real_escape_string($data['answer']);
            $status = "1";
            $sql_status = ($mysqli->query("UPDATE `$table` SET  `answer`= '" . $answer . "', `status`='" . $status . "'  WHERE  `id`='" . $form_id . "' ")) ? 1 : 0;
            if ($sql_status) $msg = "Form update successfully.";
            else $msg = "Smething went wrong! Please try again";
            echo json_encode(array(
                'msg' => $msg,
                'status' => $sql_status
            ));
        }   

        function updateAnswer($data)
        {
            global $mysqli;
            global $dbpref;
            $table = $dbpref . "cfproduct_question_records";

            $form_id = $data['edit_id'];
            $answer = $mysqli->real_escape_string($data['edit_answer']);

            $status = "1";
            $sql_status = ($mysqli->query("UPDATE `$table` SET  `answer`= '" . $answer . "', `status`='" . $status . "'  WHERE  `id`='" . $form_id . "' ")) ? 1 : 0;
            if ($sql_status) $msg = "Form update successfully.";
            else $msg = "Smething went wrong! Please try again";
            echo json_encode(array(
                'msg' => $msg,
                'status' => $sql_status
            ));
        }

        function deleteData($data)
        {

            global $mysqli;
            global $dbpref;
            $table = $dbpref . "cfproduct_question_records";
            $d_id = $data['del_id'];
            $sql_status = ($mysqli->query("DELETE FROM `$table`  WHERE  `id`='" . $d_id . "' ")) ? 1 : 0;
            if ($sql_status) $msg = "Form update successfully.";
            else $msg = "Smething went wrong! Please try again";
            echo json_encode(array(
                'msg' => $msg,
                'status' => $sql_status
            ));
        }

        function previewData($fid)
        {
            global $mysqli;
            global $dbpref;
            $table = $dbpref . "cfproduct_question_records";
            if (isset($_POST['checking_answer_btn'])) {
                $fid = $_POST['f_id'];
                $result_array = [];
                $sql = ($mysqli->query("SELECT * FROM `$table` where `id`='$fid'"));
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
        function loadMoreQuestions($data)
        {
            global $mysqli;
            global $dbpref;
            $table = $dbpref . "cfproduct_question_records";
            $table2 = $dbpref . 'cfproduct_question_setting';
            $pid = $data['product_id'];
            if (isset($_POST['row'])) {
              
                $start = $_POST['row'];
                $limit = 5;
                $html = '';
                $sql = $mysqli->query("SELECT * FROM `$table` WHERE `product_id`='$pid' ORDER BY `id` desc LIMIT $start,$limit");
                $sql2 = $mysqli->query("SELECT * FROM  `" . $table2 . "`  ");
                if( $sql2->num_rows > 0 && $settings = $sql2->fetch_assoc(  ) )
                {
                    $que_font   = $settings['que_font'];
                    $ans_font   = $settings['ans_font'];
                    $que_tcolor = $settings['que_tcolor'];
                    $ans_tcolor = $settings['ans_tcolor'];
                    $que_bg     = $settings['que_bg']; 
                    $ans_bg     = $settings['ans_bg'];
                
                }else{
                    $que_font   = 16;
                    $ans_font   = 15;
                    $que_tcolor = '#195391f5';
                    $ans_tcolor = '#1a19188f';
                    $que_bg     = '#FFFFFF'; 
                    $ans_bg     = '#FFFFFF';
                }

                if ( $sql->num_rows > 0 ) {
                    while ( $row = $sql->fetch_array( ) ) {
                        if ($row['status'] == 1) {
                            $html .= '
                            <div id="cfquestion_qandadiv">
                            <div class="cfquestion_question col-sm-8">
                              <div class="d-flex">
                                <div class="cfquestion_question_q">
                                  <span style="font-weight: bold;">Question:</span>
                                </div>
                                <div class="cfquestion_question_qd">
                                  <p><span style="font-weight:500;color:'.$que_tcolor.';font-size:'.$que_font.'px;background-color:'.$que_bg.'">'.$row["question"].'</span></p>
                                </div>
                              </div>
                            </div>
                            <div class="cfquestion_answer col-sm-8">
                              <div class="d-flex">
                                <div class="cfquestion_question_a">
                                  <span style="font-weight: bold;">Answer:</span>
                                </div>
                                <div class="cfquestion_question_ans">
                                  <p><span style="font-weight:500;color:'.$ans_tcolor.';font-size:'.$ans_font.'px;background-color:'.$ans_bg.';">'.$row["answer"].'</span></p>
                                </div>
                              </div>
                            </div>
                          </div>';
                        }
                    }
                    echo $html;
                }
                return false;
            }
        }
    }
}
