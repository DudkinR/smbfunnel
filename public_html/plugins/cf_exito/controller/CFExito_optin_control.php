<?php
if(!class_exists('CFExito_optin_control'))
{
    class CFExito_optin_control
    {
        function __construct($arr)
        {
            $this->loader=$arr['loader'];
        }
        function storeLeads()
        {
            if(isset($_POST['cfexito_store_data']) && isset($_POST['cfexito_form_id']))
            {
                $stat=array('status'=>0, 'msg'=>'');
                $form_id= cf_enc($_POST['cfexito_form_id'], 'decrypt');
                if(!is_numeric($form_id))
                {
                    self::desolvePost();
                    $stat['msg']='Unable to verify resource.';
                    return $stat;
                }
                
                if(!cf_verify_nonce($_POST['cfexito_nonce'], 'cfexito_nonce_'.$form_id))
                {
                    self::desolvePost();
                    $stat['msg']="Unable to verify resource.";
                    return $stat;
                }

                global $mysqli;
                global $dbpref;
                $table= $dbpref.'ext_popup_optins';
                $form_id=$mysqli->real_escape_string($form_id);
                $name='';
                $email='';
                $data=array();

                foreach($_POST as $index=>$val)
                {
                    if($index==='cfexito_store_data')
                    {continue;}
                    elseif($index==='name')
                    {
                        $name=$mysqli->real_escape_string($val);
                        continue;
                    }
                    elseif($index==='email')
                    {
                        $email=$mysqli->real_escape_string($val);
                        if(!filter_var($email, FILTER_VALIDATE_EMAIL))
                        {
                            self::desolvePost();
                            $stat['msg']='Invalid email pattern';
                            return $stat;
                        }
                        continue;
                    }

                    $index=$mysqli->real_escape_string($index);
                    if(is_array($val))
                    {
                        for($i=0;$i<count($val);$i++)
                        {
                            $val[$i]=$mysqli->real_escape_string($val[$i]);
                        }
                    }
                    else
                    {
                        $val=$mysqli->real_escape_string($val);
                    }
                    $data[$index]=$val;
                }

                $data=json_encode($data);
                $ip=getIP();
                $url=getProtocol();
                $url.=$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
                $date=date('Y-m-d H:i:s');

                $qry=$mysqli->query("select * from `".$table."` where `email`='".$email."' and `form_id`='".$form_id."'");

                $manage_form_events=function($form_id){
                        $form_ob=$this->loader->load('forms_control');
                        $form_ob->doControlCookieForSubscription($form_id,'set');
                        $setup=json_decode($form_ob->getFormSetup($form_id, true));
                        if(isset($setup->allow_process_in_cf) && $setup->allow_process_in_cf !=='1')
                        {
                            self::desolvePost();
                            if(isset($setup->redirect_url))
                            {
                                header('Location: '.$setup->redirect_url);
                                echo "<script>window.location=`".$setup->redirect_url."`;</script>";
                                die();
                            }
                        }
                };

                if($qry->num_rows<1)
                {
                    $in=$mysqli->query("insert into `".$table."` (`form_id`,`name`,`email`,`exf`,`url`,`ip`,`added_on`) values (".$form_id.",'".$name."','".$email."','".$data."','".$url."','".$ip."','".$date."')");
                
                    if($in)
                    {
                        $stat['status']= 1; 
                        $manage_form_events($form_id);
                    }
                    else
                    {
                        self::desolvePost();
                        $stat['msg']='Something wrong';
                    }
                    return $stat;
                }
                else
                {
                    $stat['status']= 2;
                    $manage_form_events($form_id);
                }
            }
            return false;   
        }
        function desolvePost()
        {
            $_SERVER['REQUEST_METHOD']='GET';
            foreach($_POST as $index=>$val)
            {
                unset($_POST[$index]);
            }
        }
        function deleteLeads($lead_ids)
        {
            global $mysqli;
            global $dbpref;
            $table= $dbpref.'ext_popup_optins';
            $lead_ids=$mysqli->real_escape_string($lead_ids);
            $mysqli->query("delete from `".$table."` where `id` in (".$lead_ids.")");
            return true;
        }
        function getLeadsCount($form_id)
        {
            global $mysqli;
            global $dbpref;
            $table= $dbpref.'ext_popup_optins';

            $qry=$mysqli->query("select count(`id`) as `count_id` from `".$table."` where `form_id`=".$form_id."");

            $r=$qry->fetch_object();
            return $r->count_id;
        }
        function getLeads($form_id,$max_limit=false,$page=1)
        {
            global $mysqli;
            global $dbpref;
            $table= $dbpref.'ext_popup_optins';

            $form_id=$mysqli->real_escape_string($form_id);
            $page=$mysqli->real_escape_string($page);
            if(!$max_limit)
            {$max_limit=$mysqli->real_escape_string($max_limit);}

            $arr=array();
            $limit="";

            if($max_limit !==false && is_numeric($max_limit) && is_numeric($page))
            {
                $page=($page*$max_limit)-$max_limit;
                $limit =" limit ".$page.','.$max_limit;
            }
    
            /////////////////////////////
            $search="";

            if(isset($_POST['onpage_search']))
            {
                $search=trim($mysqli->real_escape_string($_POST['onpage_search']));
                $search=str_replace('_','[_]',$search);
                $search=str_replace('%','[%]',$search);
                $search=" and (`name` like '%".$search."%' or `email` like '%".$search."%' or `exf` like '%".$search."%' or `url` like '%".$search."%' or `ip` like '%".$search."%')";
            }

            $order_by="`id` desc";
            if(isset($_GET['arrange_records_order']))
            {
                $order_by=base64_decode($_GET['arrange_records_order']);
            }

            $date_between=dateBetween('added_on',null,true);

            if(strlen($date_between[0])>0)
            {
                $search .=$date_between[1];
            }
            //////////////////////////////
            $qry=$mysqli->query("select * from `".$table."` where `form_id`=".$form_id.$search." order by ".$order_by.$limit);
            
            if($qry->num_rows>0)
            {
                $form_control=$this->loader->load('forms_control');
                $valid_inputs=$form_control->getValidInputs($form_id);

                $header=array('#');
                if(in_array('name', $valid_inputs))
                {
                    array_push($header, 'Name');
                    array_splice($valid_inputs, array_search('name',$valid_inputs), 1);
                }
                if(in_array('email', $valid_inputs))
                {
                    array_push($header, 'Email');
                    array_splice($valid_inputs,array_search('email',$valid_inputs),1);
                }
                $header_s=array_merge($header, $valid_inputs);

                $header=array();
                for($i=0; $i<count($header_s); $i++)
                {
                    array_push($header, ucwords($header_s[$i]) );
                }

                array_push($header, 'URL');
                array_push($header, 'IP');
                array_push($header, 'Added On');

                $arr[0]=$header;
                $count=0;

                if($max_limit && $page)
                {
                    $count=($page*$max_limit)-$max_limit;
                }

                while($r=$qry->fetch_object())
                {
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

                   // $arr[$r->form_id]=array();
                   $exf=array();
                   $temp_exf=json_decode($r->exf);
                   if(is_object($temp_exf))
                   {$exf=(array)$temp_exf;}
                   
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
        function doExportToCSV()
        {
            if(isset($_POST['cfexito_export_csv']))
            {
                $form_controller=$this->loader->load('forms_control');
                $data=self::getLeads($_POST['cfexito_export_csv']);
                if(count($data)<1){return;}

                $form_detail=$form_controller->getMiniForms($_POST['cfexito_export_csv']);
                if(!isset($form_detail[$_POST['cfexito_export_csv']]))
                {return;}

                $filename=str_replace(' ','_',trim($form_detail[$_POST['cfexito_export_csv']]));
                if(strlen($filename)<1)
                {
                    $filename .='cf_exito';
                }
                $filename .='.csv';
                // open raw memory as file so no temp files needed, you might run out of memory though
                $f = fopen('php://memory', 'w'); 
                // loop over the input array
                foreach ($data as $line) { 
                    // generate csv lines from the inner arrays
                    fputcsv($f, $line, ','); 
                }
                // reset the file pointer to the start of the file
                fseek($f, 0);
                // tell the browser it's going to be a csv file
                header('Content-Type: application/csv');
                // tell the browser we want to save it instead of displaying it
                header('Content-Disposition: attachment; filename="'.$filename.'";');
                // make php send the generated csv lines to the browser
                fpassthru($f);
                die();
            }
        }
    }
}
?>