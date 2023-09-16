<?php
if(!class_exists('CFGlobalAR_form_controller'))
{
    class CFGlobalAR_form_controller
    {
        function __construct( $arr )
        {
            $this -> loader = $arr['loader'];
        }

        /*
            Get Ajax Values and send to the related functions.
        */
        public function getAjaxRequest( $ajax_data )
        {
            global $mysqli;
            global $dbpref;
            $buttonHitValue = $ajax_data['cfglobalau_update_insert'];
            
            if($buttonHitValue == "CREATE" || $buttonHitValue == "UPDATE") echo $this->createData( $ajax_data, $mysqli, $dbpref );
            elseif ($buttonHitValue == "DELETE") echo  $this->deleteData( $ajax_data, $mysqli, $dbpref );
        }

        /*
            Insert the values.
        */
        function createData( $ajax_data, $mysqli, $dbpref )
        {
            global $mysqli;
            global $dbpref;
            $table= $dbpref.'quick_autoresponders';

            $form_title = $mysqli->real_escape_string(( !empty($ajax_data['cfglobal_title'])) ?$ajax_data['cfglobal_title']:'');
            $form_event = $mysqli->real_escape_string(( !empty($ajax_data['cfglobalau_update_insert'])) ? $ajax_data['cfglobalau_update_insert']:'');
            $install_url = $mysqli->real_escape_string(( !empty($ajax_data['cfglobal_ajax_insertUrl'])) ? $ajax_data['cfglobal_ajax_insertUrl']:'');
            $api_url = $mysqli->real_escape_string(( !empty($ajax_data['cfglobal_api_url'])) ?$ajax_data['cfglobal_api_url']:'');
            $id = $mysqli->real_escape_string(( !empty(cf_enc($ajax_data['cfglobalau_form_id'], "decrypt"))) ? cf_enc($ajax_data['cfglobalau_form_id'], "decrypt"):0);
            $form_methods = $mysqli->real_escape_string(( !empty($ajax_data['cfglobal_form_methods'])) ? $ajax_data['cfglobal_form_methods']:'');
            $authRequired = $mysqli->real_escape_string(( !empty($ajax_data['authRequired'])) ? $ajax_data['authRequired']:0);

            $body_data_format = $mysqli->real_escape_string($ajax_data['body_data_format']);

            $autoresponder_name = 'cfglobalautoresponder';
            $date = time();
            $custom_form_input = $this->custom_input($ajax_data, 'custom' );
            $custom_header_form_input = $this->custom_input($ajax_data, 'custom_header', 1 );
            
            if($authRequired)
            {
                $username = $mysqli->real_escape_string($ajax_data['cfglobal_username']);
                $password = $mysqli->real_escape_string($ajax_data['cfglobal_password']);
                $encode_data = $username.':'.$password;
                $header_str = "Basic ". base64_encode($encode_data);
            }
            else $header_str = "";

            $custom_header_form_input['body_data_format'] = $body_data_format;
            $custom_header_form_input['Authorization'] = $header_str;
            $custom_form_input_data = array();
            
            foreach($custom_form_input as $key=>$value)
            {
                $custom_form_input_data[$value['name']] = preg_replace('/\{[^}]+\}/', '', $value['title']);
            }
            
            $custom_form_input_data['api_url'] = $api_url;
            $custom_form_input_data['form_method'] = $form_methods;
            $custom_form_input_data['authRequired'] = $authRequired;
            $custom_form_input = array_filter($custom_form_input);

            if($autoresponder_name == 'cfglobalautoresponder')
            {
                $auto_ob= $this->loader->load('auto_controller');
                $tocheck_autores = json_decode($auto_ob->CFGlobalAU($custom_form_input_data, $custom_header_form_input));
            }

            if($ajax_data['hit_button'] != "test")
            {
                array_push($custom_form_input, array('api_url'=>$api_url, 'form_method'=>$form_methods));
                if($id<1)
                {
                    $sql_status=($mysqli->query("INSERT INTO `".$table."` (`autoresponder`, `autoresponder_name`, `autoresponder_detail`, `exf`, `date_created`) VALUES ('".$form_title."','".$autoresponder_name."','".json_encode($custom_header_form_input)."','".json_encode($custom_form_input)."','".$date."')"))?1:0;
                    $id_new = $mysqli->insert_id;
                }

                else $sql_status=($mysqli->query("UPDATE `".$table."` set `autoresponder`='".$form_title."',`autoresponder_name`='".$autoresponder_name."', `autoresponder_detail`='".json_encode($custom_header_form_input)."', `exf`='".json_encode($custom_form_input)."' where `id`='".$id."'"))?1:0;
                

                if($sql_status) $msg = 'API saved successfully';
                else $msg = 'Something went wrong';

                return json_encode(array(
                    'status' => $sql_status,
                    'msg'    => $msg,
                    'url'    => $install_url.(isset($id_new)?$id_new:0),
                    'action' => $ajax_data['cfglobalau_update_insert']
                ));
            }

            else return json_encode(array(
                'status' => $tocheck_autores->status,
                'msg' => $tocheck_autores->msg
            ));
        }

        /*
            Delete the values.
        */
        function deleteData( $ajax_data, $mysqli, $dbpref )
        {
            $table = $dbpref.'quick_autoresponders';
            $form_id = cf_enc($ajax_data['cfglobalau_form_id'], 'decrypt');
            $sql_status = ($mysqli->query( "DELETE FROM `".$table."` WHERE `id`='$form_id' " ))?1:0;
            if($sql_status) $msg = "Your form has been deleted";
            else $msg = "Something went wrong";
            return json_encode(array(
                'status' => $sql_status,
                'msg'    => $msg
            ));
        }

        function custom_input( $ajax_data, $match_data, $isNew=0 )
        {
            $cus_input = [];
            
            foreach ( $ajax_data as $key => $data )
            {
                $data_expload = explode( "@", $key );
                if( $data_expload[0] == $match_data )
                {
                    $key = trim( htmlspecialchars( stripcslashes( $key ) ) );
                    $c_data=json_decode( $data, true );
                    $cus_input=$c_data;
                }
            }
            if($isNew)
            {
                $new_arr = array();
                foreach($cus_input as $key=>$value)
                {
                    $new_arr[$value['name']] = $value['title'];
                }
                return $new_arr;
            }
            return $cus_input;
        }
    }
}