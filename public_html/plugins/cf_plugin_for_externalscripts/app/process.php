<?php

namespace CFINT_integration_addon\integration;

class Cfint_processor
{

    var $mysqli;
    var $pref;
    function __construct($arr)
    {
        // print_r($arr);
        $this->pref = $arr['pref'];
        self::addIntegration();
    }

    function addIntegration()
    {

        add_filter("the_content", function ($content, $settings) {

            $int_ids = array();
            //   echo $content;
            $page_data = $settings;
            //print_r($page_data);
            $page_settings = json_decode($settings['settings']);
            if (isset($page_settings->snippet_integrations)) {
                if (is_array($page_settings->snippet_integrations)) {
                    $int_ids = $page_settings->snippet_integrations;
                }
            }

            if (count($int_ids) > 0) {
                // $int_ob=$this->load->loadIntegrations();
                return self::integrationViewer($int_ids, $content);
            } else {
                return $content;
            }
            return $content;
        });
    }



    function storeIntegrations($id = 0, $title='', $pos = "footer", $data = '', $type = '', $do = "insert")
    {
        global $mysqli;
        global $dbpref;
        $title = $mysqli->real_escape_string($title);
        $type = $mysqli->real_escape_string($type);
        $data = $mysqli->real_escape_string($data);
        $pos = $mysqli->real_escape_string($pos);
        $id = $mysqli->real_escape_string($id);
        $do = $mysqli->real_escape_string($do);


        $table = $dbpref . "qfnl_integrations";
        $add = 0;
        $user_id=$_SESSION['user' . get_option('site_token')]; 
        
        if ($do == "insert") {
            if ($mysqli->query("insert into `" . $table . "` (`title`,`type`,`data`,`position`,`added_on`,`user_id`) values ('" . $title . "','" . $type . "','" . $data . "','" . $pos . "','" . time() . "','".$user_id."')")) {
                $add = $mysqli->insert_id;
            }
        } else {
            if ($mysqli->query("update `" . $table . "` set `title`='" . $title . "',`type`='" . $type . "',`data`='" . $data . "',`position`='" . $pos . "' where `id`=" . $id . "")) {
                $add = $id;
            }
        }

        return ($add) ? 1 : 0;
    }
    function delIntegration($id)
    {
        global  $mysqli;
        global $dbpref;
        $table = $dbpref . "qfnl_integrations";
        $id = $mysqli->real_escape_string($id);
        $mysqli->query("delete from `" . $table . "` where `id`=" . $id . "");
        return 1;
    }
    function getData($get = "all", $page = 1)
    {
        global  $mysqli;
        global $dbpref;
        $table = $dbpref . "qfnl_integrations";
        $user_id=$_SESSION['user' . get_option('site_token')]; 
        $access=$_SESSION['access' . get_option('site_token')]; 
        if (is_numeric($get)) {
            $id = $mysqli->real_escape_string($get);
            $qry = $mysqli->query("select * from `" . $table . "` where `id`=" . $id . "");
            if ($qry->num_rows > 0) {
                // print_r($qry->fetch_object());
                return json_encode($qry->fetch_object());
            } else {
                return 0;
            }
        } elseif ($get === "all") {
            if ($page == 1 || (!is_numeric($page))) {
                $page = 0;
            } else {
                $page = ($page * 10) - 10;
            }
            if (isset($_POST["onpage_search"]) && strlen($_POST['onpage_search']) > 0) {
                $search_keywords = $mysqli->real_escape_string($_POST["onpage_search"]);
                if($access=='admin')
                {
                    $query_str = "select * from `" . $table . "` where `title` like '%" . $search_keywords . "%' or `type` like '%" . $search_keywords . "%' or `data` like '%" . $search_keywords . "%' or `position` like '%" . $search_keywords . "%' order by `id` desc";
                }
                else
                {
                    $query_str = "select * from `" . $table . "` where `title` like '%" . $search_keywords . "%' or `type` like '%" . $search_keywords . "%' or `data` like '%" . $search_keywords . "%' or `position` like '%" . $search_keywords . "%' and `user_id`=".$user_id." order by `id` desc";
                }
            } else {
                $timelimit_condition = 1;
                $date_between = dateBetween('added_on');
                if (strlen($date_between[0]) > 1) {
                    $timelimit_condition = $date_between[0];
                }
                $order_by = "`id` desc";
                if (isset($_GET['arrange_records_order'])) {
                    $order_by = base64_decode($_GET['arrange_records_order']);
                }
                if($access=='admin')
                {
                    $query_str = "select * from `" . $table . "` where " . $timelimit_condition . " order by " . $order_by . " limit " . $page . "," . get_option('qfnl_max_records_per_page') . "";
                }
                else
                {
                    $query_str = "select * from `" . $table . "` where " . $timelimit_condition . " and `user_id`=".$user_id." order by " . $order_by . " limit " . $page . "," . get_option('qfnl_max_records_per_page') . "";
                }
                
            }

            return $mysqli->query($query_str);
        } elseif ($get === "total") {
            $timelimit_condition = 1;
            $date_between = dateBetween('added_on');
            if (strlen($date_between[0]) > 1) {
                $timelimit_condition = $date_between[0];
            }
            if($access=='admin')
            {
                $qry = $mysqli->query("select count(`id`) as `countid` from `" . $table . "` where " . $timelimit_condition . "");
            }
            else
            {
                $qry = $mysqli->query("select count(`id`) as `countid` from `" . $table . "` where " . $timelimit_condition . " and `user_id`=".$user_id."");
            }
            if ($r = $qry->fetch_object()) {
                return $r->countid;
            }
            return 0;
        }
    }
    function integrationViewer($int_ids, $content)
    {
        if (is_array($int_ids)) {
            $header = "";
            $footer = "";
            // print_r($int_ids);
            for ($i = 0; $i < count($int_ids); $i++) {
                //print_r($content);
                $data = self::getData($int_ids[$i]);
                $data = json_decode($data);
                if ($data !== 0) {
                    $content1 = $data->data;

                    if ($data->type == "messenger" || $data->type == "skype") {
                        if (strpos($content1, "</script>") < 1) { //echo $data->type;
                            if ($data->type == "messenger") {
                                $content1 = '<!-- Load Facebook SDK for JavaScript -->
                            <div id="fb-root"></div>
                            <script>
                              window.fbAsyncInit = function() {
                                FB.init({
                                  xfbml            : true,
                                  version          : \'v4.0\'
                                });
                              };
                      
                              (function(d, s, id) {
                              var js, fjs = d.getElementsByTagName(s)[0];
                              if (d.getElementById(id)) return;
                              js = d.createElement(s); js.id = id;
                              js.src = \'https://connect.facebook.net/en_US/sdk/xfbml.customerchat.js\';
                              fjs.parentNode.insertBefore(js, fjs);
                            }(document, \'script\', \'facebook-jssdk\'));</script>
                      
                            <!-- Your customer chat code -->
                            <div class="fb-customerchat"
                              attribution=setup_tool
                              page_id="' . $content1 . '"
                        theme_color="#13cf13">
                            </div>';
                            } else {
                                $content1 = '<span class="skype-button bubble " data-contact-id="' . $content1 . '"></span>
                            <script src="https://swc.cdn.skype.com/sdk/v1/sdk.min.js"></script>';
                            }
                        }
                    }
                    if ($data->position == "header") {
                        $header .= $content1;
                    } else {
                        $footer .= $content1;
                    }
                }
            }
            $content = str_replace("</head>", $header . "</head>", $content);
            $content = str_replace("</body>", $footer . "</body>", $content);
            // return $html.$content;
            return $content;
        } else {
            return $content;
        }
    }
    function countOccurranceInPagetable($id)
    {
        global  $mysqli;
        global $dbpref;
        $table = $dbpref . "quick_pagefunnel";
        $id = $mysqli->real_escape_string($id);
        $user_id=$_SESSION['user' . get_option('site_token')]; 
        $access=$_SESSION['access' . get_option('site_token')]; 
        if($access=='admin')
        {
            $qry = $mysqli->query("select count(distinct(`funnelid`)) as `countid` from `" . $table . "` where `settings` like '%\"snippet_integrations\":[%\"" . $id . "\"%]%'");
        }
        else
        {
            $qry = $mysqli->query("select count(distinct(`funnelid`)) as `countid` from `" . $table . "` where `settings` like '%\"snippet_integrations\":[%\"" . $id . "\"%]%'");
        }
        
        $count = 0;
        if ($r = $qry->fetch_object()) {
            $count = $r->countid;
        }
        return $count;
    }

    function registerAjaxRequest()
    {

        add_action('cf_ajax_cfint_savcredentials', function () {
            echo self::storeIntegrations($_POST['cfint_id'], $_POST['cfint_title'], $_POST['cfint_position'], $_POST['cfint_data'], $_POST['cfint_type'], $_POST['cfint_do']);
            die();
        });
    }


    function registereditAjaxRequest()
    {

        add_action('cf_ajax_cfint_updatecredentials', function () {
            echo self::getData($_POST['cfeditid']);
            die();
        });
    }
}
