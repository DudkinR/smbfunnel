<?php
if(!class_exists('CFquizo_optin_control'))
{
    class CFquizo_optin_control
    {
        function __construct($arr)
        {
            $this->loader=$arr['loader'];
        }
        function storeLeads()
        {
            if(isset($_POST['cfquiz_store_data']) && isset($_POST['cfquizo_quiz_id']))
            {
                $stat=array('status'=>0, 'msg'=>'');
                $quiz_id= cf_enc($_POST['cfquizo_quiz_id'], 'decrypt');
                if(!is_numeric($quiz_id))
                {
                    self::desolvePost();
                    $stat['msg']='Unable to verify resource quiz_id.';
                    return $stat;
                }
                
                if(!cf_verify_nonce($_POST['cfquizo_nonce'], 'cfquizo_nonce_'.$quiz_id))
                {
                    self::desolvePost();
                    $stat['msg']="Unable to verify resource nonce.";
                    return $stat;
                }

                global $mysqli;
                global $dbpref;
                $table= $dbpref.'cfquiz_popup_optins';
                $quiz_id=$mysqli->real_escape_string($quiz_id);
                $name='';
                $email='';
                $data=array();
                
                foreach($_POST as $index=>$val)
                {
                    if($index==='cfquiz_store_data') 
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
                $qry=$mysqli->query("select * from `".$table."` where `email`='".$email."' and `quiz_id`='".$quiz_id."'");
                        $manage_quiz_events=function($quiz_id){
                        $quiz_ob=$this->loader->load('quizs_control');
                        $quiz_ob->doControlCookieForSubscription($quiz_id,'set');
                        $setup=json_decode($quiz_ob->getquizSetup($quiz_id, true));
                        if(isset($setup->allow_process_in_cf) && $setup->allow_process_in_cf !=='1')
                        {
                            self::desolvePost();
                            if(isset($setup->redirect_url))
                            {
                                header('Location: '.$setup->redirect_url);
                                die();
                            }
                        }
                };

                if($qry!=null)
                {
                if($qry->num_rows<1)
                {
                    $in=$mysqli->query("insert into `".$table."` (`quiz_id`,`name`,`email`,`exf`,`url`,`ip`,`added_on`) values (".$quiz_id.",'".$name."','".$email."','".$data."','".$url."','".$ip."','".$date."')");
                
                    if($in)
                    {   
                        $stat['status']= 1; 
                        $manage_quiz_events($quiz_id);
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
                    $manage_quiz_events($quiz_id);
                }
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
            $table= $dbpref.'cfquiz_popup_optins';
            $lead_ids=$mysqli->real_escape_string($lead_ids);
            $mysqli->query("delete from `".$table."` where `id` in (".$lead_ids.")");
            return true;
        }
        function deleteResponse($lead_ids)
        {
            global $mysqli;
            global $dbpref;
            $table= $dbpref.'cfquiz_response';
            $lead_ids=$mysqli->real_escape_string($lead_ids);
            $mysqli->query("delete from `".$table."` where `id` in (".$lead_ids.")");
            return true;
        }
        function getLeadsCount($quiz_id)
        {
            global $mysqli;
            global $dbpref;
            $table= $dbpref.'cfquiz_response';
            $qry=$mysqli->query("select count(`id`) as `count_id` from `".$table."` where `quiz_id`=".$quiz_id."");
            if($qry!=null)
            {
            $r=$qry->fetch_object();
            return $r->count_id;
        }
        else
        {
            return 0;
        }
        }
        function getQuizName($quiz_id)
        {
            global $mysqli;
            global $dbpref;
            $table= $dbpref.'cfquiz_popup';
            $qry=$mysqli->query("select quiz_name from `".$table."` where `id`=".$quiz_id."");
            if($qry!=null)
            {
            $r=$qry->fetch_object();
            return $r->quiz_name;
        }
        else
        {
            return 0;
        }
        }

        function getLeads22($quiz_id,$max_limit=false,$page=1)
        {
            global $mysqli;
            global $dbpref;
            $table= $dbpref.'cfquiz_response';

            $quiz_id=$mysqli->real_escape_string($quiz_id);
            $page=$mysqli->real_escape_string($page);
            if(!$max_limit)
            {$max_limit=$mysqli->real_escape_string($max_limit);}

            $arr=array();
            $limit="";

            if($max_limit !==false && is_numeric($max_limit) && is_numeric($page))
            {
                
                $start=($page*$max_limit)-$max_limit;
                $limit =" limit ".$start.','.$max_limit;
            }

            $search="";

            if(isset($_POST['onpage_search']))
            {
                $search=trim($mysqli->real_escape_string($_POST['onpage_search']));
                $search=str_replace('_','[_]',$search);
                $search=str_replace('%','[%]',$search);
                $search=" and (`user_details` like '%".$search."%' or `quiz_response` like '%".$search."%')";
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

            $qry=$mysqli->query("select * from `".$table."` where `quiz_id`=".$quiz_id.$search." order by ".$order_by.$limit);
            if($qry!=null)
            {
               return $qry;
                $count=0;

                if($max_limit && $page)
                {
                    $count=($page*$max_limit)-$max_limit;
                }

            }
            else
            {
                $qry=array();
                return $qry;
            }
        }
        function doExportToCSV()
        {
            if(isset($_POST['cfquiz_export_csv']))
            {
                $quiz_controller=$this->loader->load('quizs_control');
                $data=self::getLeads22($_POST['cfquiz_export_csv']);
                $quiz_detail=$quiz_controller->getMiniquizs($_POST['cfquiz_export_csv']);
                if(!isset($quiz_detail[$_POST['cfquiz_export_csv']]))
                {return;}
                $filename=str_replace(' ','_',trim($quiz_detail[$_POST['cfquiz_export_csv']]));
                if(strlen($filename)<1)
                {
                    $filename .='cf_surveyor';
                }
                $filename .='.csv';
                // open raw memory as file so no temp files needed, you might run out of memory though
                $f = fopen('php://memory', 'w'); 
                $qry=$data;
                $creating_row_line="";
                $counter=0;   
                if($r=$qry->fetch_object())
                {    
                    $creating_row_line="";
                    $creating_row_line.="# ,";
                    $user_details=json_decode($r->user_details);
                    foreach($user_details as $key => $value) {
                       $creating_row_line.=ucfirst($key).",";
                    }
                    $creating_row_line.=  "Added on: "; 
                    $creating_row_line.="\n"; 
                    fwrite($f, $creating_row_line);
                }
                $counter=0; 
                $qry->data_seek(0);
                while($r=$qry->fetch_object())
                {    
                    $creating_row_line="";
                    $counter++;
                    $creating_row_line.=$counter.",";
                    $user_details=json_decode($r->user_details);
                    foreach($user_details as $key => $value) {
                       $creating_row_line.=$value.",";
                    }
                    $creating_row_line.=$r->added_on.","; 
                    $creating_row_line.="\n"; 
                    fwrite($f, $creating_row_line);
                }//while loop over here
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