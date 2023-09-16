<?php
if(!class_exists('CFRespo_optin_control')) {
    class CFRespo_optin_control {
        function __construct( $arr ) {
            $this -> loader = $arr['loader'];
        }

        /*
        ================
        Get the data from the optin page.
        ================
        */
        function storeLeadsRespo() {
            if(isset($_POST['cfrespo_store_data']) && isset($_POST['cfrespo_form_id'])) {
                $stat=array(
                    'status' => 0, 
                    'msg'=>''
                );
                $form_id = cf_enc($_POST['cfrespo_form_id'], 'decrypt');


                if( !is_numeric($form_id) ) {
                    self::desolvePost();
                    $stat['msg'] = 'Unable to verify resource.';
                    return $stat;
                }
                
                if(!cf_verify_nonce($_POST['cfrespo_nonce'], 'cfrespo_nonce_'.$form_id)) {
                    self::desolvePost();
                    $stat['msg'] = 'Unable to verify nonce.';
                    return $stat;
                }

                global $mysqli;
                global $dbpref;
                $table = $dbpref.'respo_popup_optins';
                $form_id = $mysqli->real_escape_string($form_id);
                $name='';
                $email='';
                $data=array();

                foreach( $_POST as $index=>$val ) {
                    if( $index === 'cfrespo_store_data' ) { continue; }

                    elseif( $index === 'name' ) {
                        $name = $mysqli -> real_escape_string( $val );
                        continue;
                    }

                    elseif( $index === 'email' ) {
                        $email = $mysqli -> real_escape_string( $val );

                        if( !filter_var($email, FILTER_VALIDATE_EMAIL) ) {
                            self::desolvePost();
                            $stat['msg'] = 'Invalid email pattern';
                            return $stat;
                        }
                        continue;
                    }

                    $index = $mysqli -> real_escape_string( $index );
                    if( is_array($val) ) {
                        for( $i=0; $i<count($val); $i++ ) {
                            $val[$i] = $mysqli -> real_escape_string( $val[$i] );
                        }
                    }
                    else
                    {
                        $val = $mysqli -> real_escape_string( $val );
                    }
                    $data[$index] = $val;
                }

                $data = json_encode($data);
                $ip = getIP();
                $url = getProtocol();
                $url .= $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
                $date = date('Y-m-d H:i:s');

                $qry = $mysqli -> query( "SELECT * FROM `".$table."` WHERE `email`='".$email."' AND `form_id`='".$form_id."'" );

                $manage_form_events = function( $form_id ) {
                    $form_ob = $this -> loader -> load('forms_control');
                    $form_ob -> doControlCookieForSubscription( $form_id, 'set' );
                    $setup = $form_ob -> getFormSetup( $form_id );

                    if( isset( $setup['allow_process_in_cf'] ) && $setup['allow_process_in_cf'] !=='1' ) {
                        self::desolvePost();
                        if( isset($setup['redirect_url']) ) {
                            header('location: '.$setup['redirect_url']);
                            die();
                        }
                    }
                };

                if( $qry -> num_rows < 1 ) {
                    $in = $mysqli -> query( "INSERT INTO `".$table."` (`form_id`,`name`,`email`,`exf`,`url`,`ip`,`added_on`) VALUES (".$form_id.",'".$name."','".$email."','".$data."','".$url."','".$ip."','".$date."')" );
                
                    if( $in ) {
                        $stat['status']= 1; 
                        $manage_form_events( $form_id );
                    }

                    else {
                        self::desolvePost();
                        $stat['msg']='Something wrong';
                    }
                    return $stat;
                }

                else {
                    $stat['status']= 2;
                    $manage_form_events( $form_id );
                }
            }
            return false;   
        }

        function desolvePost() {
            $_SERVER['REQUEST_METHOD'] = 'GET';
            foreach( $_POST as $index => $val ) {
                unset( $_POST[$index] );
            }
        }

        function getLeads( $form_id ) {
            global $mysqli;
            global $dbpref;
            $table = $dbpref.'respo_popup_optins';

            $form_id = $mysqli -> real_escape_string( $form_id );
            $arr = array();
            $order_by="`id` desc";
            $qry=$mysqli->query("SELECT * FROM `".$table."` WHERE `form_id`=" .$form_id. " ORDER BY ".$order_by);

            if ( $qry -> num_rows > 0 ) {
                $form_control = $this -> loader -> load('forms_control');
                $valid_inputs = $form_control -> getValidInputs($form_id);

                $header = array('#');
                if( in_array('name', $valid_inputs) ) {
                    array_push( $header, 'Name' );
                    array_splice( $valid_inputs, array_search('name', $valid_inputs), 1 );
                }
    
                if(in_array('email', $valid_inputs)) {
                    array_push( $header, 'Email' );
                    array_splice( $valid_inputs, array_search('email', $valid_inputs), 1 );
                }
    
                $header_s = array_merge( $header, $valid_inputs );
                $header = array();
    
                for( $i=0; $i<count($header_s); $i++ ) {
                    array_push( $header, ucwords($header_s[$i]) );
                }
    
                array_push( $header, 'URL' );
                array_push( $header, 'IP' );
                array_push( $header, 'Added On' );
    
                $arr[0] = $header;
                $count=0;

                while($r=$qry->fetch_object()) {
                    ++$count;
                    $optin=array($count);

                    if(in_array('Name', $header))
                    {
                        array_push($optin, $r->name);
                    }
                    if(in_array('Email', $header))
                    {
                        array_push($optin, $r->email);
                    }
                    $exf=array();
                    $temp_exf=json_decode($r->exf);
                    if(is_object($temp_exf)) {$exf=(array)$temp_exf;}
                    
                    for($i=0; $i<count($valid_inputs); $i++)
                    {
                        $exf_data=(isset($exf[$valid_inputs[$i]]))? $exf[$valid_inputs[$i]]:'N/A';
                        array_push($optin, $exf_data);
                    }

                    array_push($optin, $r->url);
                    array_push($optin, $r->ip);
                    array_push($optin, date('d-M-Y h:ia',strtotime($r->added_on)));
                    $arr[$r->id]=$optin;
                }
            }

            return $arr;
        }

        function getLeadsCount( $form_id ) {
            global $mysqli;
            global $dbpref;
            $table= $dbpref.'respo_popup_optins';

            $qry = $mysqli -> query( "SELECT COUNT(`id`) AS `count_id` FROM `".$table."` WHERE `form_id`=".$form_id."" );

            $r = $qry -> fetch_object();
            return $r -> count_id;
        }

        function deleteLeads($lead_ids) {
            global $mysqli;
            global $dbpref;
            $table = $dbpref.'respo_popup_optins';
            $lead_ids = $mysqli -> real_escape_string($lead_ids);
            $mysqli->query("DELETE FROM `".$table."` WHERE `id` in (".$lead_ids.")");
            return true;
        }

        function doExportToCSV() {
            if(isset($_POST['cfpopup_export_csv'])) {
                $form_controller=$this->loader->load('forms_control');
                $data=self::getLeads($_POST['cfpopup_export_csv']);
                if(count($data)<1){return;}

                $form_detail=$form_controller -> getMiniForms($_POST['cfpopup_export_csv']);
                if(!isset($form_detail[$_POST['cfpopup_export_csv']])) {return;}

                $filename=str_replace(' ','_',trim($form_detail[$_POST['cfpopup_export_csv']]));
                if(strlen($filename)<1) {
                    $filename .='cf_popup';
                }
                $filename .='.csv';
                $f = fopen('php://memory', 'w');
                foreach ($data as $line) {
                    fputcsv($f, $line, ','); 
                }
                fseek($f, 0);
                header('Content-Type: application/csv');
                header('Content-Disposition: attachment; filename="'.$filename.'";');
                fpassthru($f);
                die();
            }
        }
    }
}
?>