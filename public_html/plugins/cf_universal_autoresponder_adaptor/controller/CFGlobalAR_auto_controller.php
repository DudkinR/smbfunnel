<?php
if(!class_exists('CFGlobalAR_auto_controller'))
{
    class CFGlobalAR_auto_controller
    {
        function __construct( $arr )
        {
            $this -> loader = $arr['loader'];
        }

        function CFGlobalAU($custom_form_input, $custom_header_input)
        {
            $post_fields = "";
            $i=0;
            if(count($custom_form_input) == 2) $loop = false;
            else $loop = true;

            $new_arr = array();
            foreach($custom_header_input as $key=>$value)
            {
                if($key == 'body_data_format') $body_data_format = $value;
                elseif($key == 'Authorization')
                {
                    if($value=="") break;
                    else $new_arr[] = $key.': '.$value;
                }
                else $new_arr[] = $key.': '.$value;
            }

            $json_post_fields= array();
            foreach($custom_form_input as $key=>$value)
            {
                ++$i;
                if($key == 'api_url')
                {
                    $i=0;
                    $api_url = $value;
                    unset($custom_form_input[$key]);
                }
                if($key == 'form_method')
                {
                    $i=0;
                    $form_method = $value;
                    unset($custom_form_input[$key]);
                }
                if($key == 'authRequired')
                {
                    $i=0;
                    $authRequired = $value;
                    unset($custom_form_input[$key]);
                }
                if($loop)
                {
                    if($i>0)
                    {
                        if($body_data_format == 1)
                        {
                            if(filter_var($value, FILTER_VALIDATE_EMAIL)) $value = urlencode($value);
                            if($i>1) $post_fields .= "&";
                            $post_fields .= $key . "=" .$value;
                        }
                        else $json_post_fields[$key] = $value;
                    }
                }
            }

            if($body_data_format == 0) $post_fields = json_encode($json_post_fields);

            $curl=curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => $api_url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => $form_method,
                CURLOPT_POSTFIELDS => $post_fields,
                CURLOPT_HTTPHEADER => $new_arr,
            ));

            $response = curl_exec($curl);
            $err = curl_error($curl);
            $http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            curl_close($curl);

            if($err) $msg= "Error #:".$err;
            else $msg = $response;

            return json_encode(array(
                'status' => $http_code,
                'msg' => $msg
            ));
        }
    }
}