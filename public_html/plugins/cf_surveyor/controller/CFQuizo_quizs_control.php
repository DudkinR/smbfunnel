<?php
if(!class_exists('CFQuizo_quizs_control'))
{
    class CFQuizo_quizs_control
    {
        function __construct($arr)
        {
            $this->loader=$arr['loader'];
        }

          function getAllquizs($total_quizs=false,$max_limit=false,$page=1)
          {
            global $mysqli;
            global $dbpref;
            $table= $dbpref.'cfquiz_popup';
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
    
            $search="";

            if(isset($_POST['onpage_search']))
            {
                $search=trim($mysqli->real_escape_string($_POST['onpage_search']));
                $search=str_replace('_','[_]',$search);
                $search=str_replace('%','[%]',$search);
                $search=" and `quiz_name` like '%".$search."%'";
            }

            $order_by="`id` desc";
            if(isset($_GET['arrange_records_order']))
            {
                $order_by=base64_decode($_GET['arrange_records_order']);
            }

            $date_between=dateBetween('created_at',null,true);

            if(strlen($date_between[0])>0)
            {
                $search .=$date_between[1];
            }

            $qry=$mysqli->query("select * from `".$table."` where 1".$search." order by ".$order_by.$limit);

            $arr=[];
            if($qry==null)
            {
                return 0;
            }
            else
            {
            if($qry->num_rows>0)
            {
                while($data = $qry->fetch_assoc() )
                {
                    $arr[]=$data;
                }   
             
            }
            return $arr;
            }

        }
        function getUsersCount( $quiz_id=null )
        {
            global $mysqli;
            global $dbpref;
            $quiz_id= $mysqli->real_escape_string( $quiz_id );

            $table2=$dbpref."cfquiz_popup_optins";

            $user_counts=$mysqli->query("SELECT COUNT(*) AS `total_user` FROM `".$table2."` WHERE `quiz_id`=".$quiz_id);
            
            $user_count=$user_counts->fetch_assoc();
        }
        function getquizsCount()
        {
            global $mysqli;
            global $dbpref;
            $table= $dbpref.'cfquiz_popup';

            $qry=$mysqli->query("select count(`id`) as `total_quizs` from `".$table."`");
            
            if($qry==null)
            {
                return 0;
            }
            else
            {
            $r=$qry->fetch_object();
            return $r->total_quizs;
            }
        }
        function getMiniquizs($id=false)
        {
            global $mysqli;
            global $dbpref;
            $table=$dbpref.'cfquiz_popup';

            $arr=array();

            $cond="";
            if($id)
            {
                $id=$mysqli->real_escape_string($id);
                $cond=" where `id`=".$id;
            }
            $qry=$mysqli->query("select `id`, `quiz_name` from `".$table."`".$cond." order by `id` desc");
            if($qry==null)
            {
                return 0;
            }   
            else 
            {
            while($r=$qry->fetch_object())
            {
                $arr[$r->id]=$r->quiz_name;
            }
            return $arr;
            }
        }
        function getValidInputs($id)
        {
            global $mysqli;
            global $dbpref;
            $id=$mysqli->real_escape_string($id);

            $arr=array();
            $table= $dbpref.'cfquiz_popup_inputs';
            $qry=$mysqli->query("select distinct(`name`) from `".$table."` where `quiz_id`=".$id."");

            while($r=$qry->fetch_object())
            {
                array_push($arr,$r->name);
            }

            return $arr;
        }
        function allowProcessingInMainCF($quiz_id)
        {
            global $mysqli;
            global $dbpref;
            $table=$dbpref.'cfquiz_popup';
            $quiz_id=(int)$mysqli->real_escape_string($quiz_id);
        }
        function getquizsetup( $quiz_id = null,$setup_only=false )
        {
            global $mysqli;
            global $dbpref;
            $table=$dbpref.'cfquiz_popup';
            $quiz_id = trim( $mysqli->real_escape_string( $quiz_id ) );

            $get=($setup_only)? '`quiz_setup`':'*';

             $r = $mysqli->query("SELECT ".$get." FROM `".$table."` WHERE `id`=".$quiz_id );

             if( $r->num_rows > 0)
             {
                $data = $r->fetch_assoc();
                if($setup_only)
                {
                    return $data['quiz_setup'];
                }
                else{return $data;}
             }
             return 0;
        }
    function cfquizoGetquizInput( $quiz_id=null )
    {
        global $mysqli;
        global $dbpref;
        $table=$dbpref.'cfquiz_popup_inputs';

        $quiz_id = trim($mysqli->real_escape_string( $quiz_id ));
        $returnOptions = $mysqli->query("SELECT * FROM `".$table."` WHERE `quiz_id`=".$quiz_id." ORDER BY `position` ASC");

        if( $returnOptions->num_rows > 0)
        {
            return $returnOptions;
        }
        return 0;
    }
    function loadGlobalquizs($config_version=0)
    {
        global $mysqli;
        global $dbpref;
        $table=$dbpref.'cfquiz_popup';
        $qry=$mysqli->query("select `id` from `".$table."` where `is_global`=1");
        while($r=$qry->fetch_object())
        {
            self::getquizUI($r->id, $config_version);
        }
    }
    function doControlCookieForSubscription($quiz_id,$doo='get')
    {
        
        $setup=self::getquizsetup($quiz_id, true);

        if($setup)
        {
            if($doo=='get')
            {
                $setup=json_decode($setup);
                if(isset($setup->dont_display_after_subscription) && $setup->dont_display_after_subscription=='1')
                {
                    if(isset($_COOKIE['cfquiz_quiz_subscribed_'.$quiz_id]))
                    {
                        return false;
                    }
                }
            }
            else if($doo=='set')
            {
                setcookie('cfquiz_quiz_subscribed_'.$quiz_id,1,time()+86400*30);
            }
        }
        return true;
    }
    function initquizsubmit()
    {
        $cfquiz_quiz_submit_err="";
        if(isset($_POST['cfquiz_store_data']))
        {
            $optin_ob= $this->loader->load('optin_control');
            $err=$optin_ob->storeLeads();
            if(isset($err['status']) && $err['status']===0)
            {
                $cfquiz_quiz_submit_err=$err['msg'];
            }
            $optin_ob->desolvePost();
            $_SESSION["switch_quiz"] = "switch_to_questions";
        }
        $GLOBALS['cfquiz_quiz_submit_err']=$cfquiz_quiz_submit_err;

    }
    function initquizsubmit22()
    {
        $cfquiz_quiz_submit_err="";
        if(isset($_POST['submit_button']))
        {
      $optin_ob= $this->loader->load('optin_control');
      $return_insert=$this->insert_quiz_response();
      if( $return_insert == 1 ){
        $redirect_url=$_POST['redirect_url'];
        $allow_process_in_cf=$_POST['allow_process_in_cf'];
      if($redirect_url!="" && $allow_process_in_cf==0)
      {
        $optin_ob->desolvePost();
        header('Location: '.$redirect_url); 
      }
      
      }
      else 
      {
      echo "<script>alert('Not Submitted - Some Error');</script>";
      }

}
    }
    function getquizUI($id= null, $config_version=0)
    {
        if(!self::doControlCookieForSubscription($id)){return;}
        $quiz_data=self::getquizsetup($id);
        if($quiz_data)
        {
            global $cfquiz_quiz_submit_err;
            $show_err=$cfquiz_quiz_submit_err;
            
            $_SESSION["quiz_id_fromshortcode"] = $id;
            $quiz_setup= json_decode($quiz_data['quiz_setup']);
            if($quiz_setup->cfquizo_is_popup==0)
            require plugin_dir_path( dirname(__FILE__,1) )."/views/view_questions.php";
            if($quiz_setup->cfquizo_is_popup==1)    
            require plugin_dir_path( dirname(__FILE__,1) )."/views/popupquiz.php";
            
            
        }
        else echo "";
    }
    public function getAjaxRequest( array $ajax_datas )
    {
            global $mysqli;

            $cus_input = [];
            $quiz_setup =[];
            
            foreach ( $ajax_datas as $key => $data ) {
                $data_expload = explode( "@", $key );
    
                if( $data_expload[0] == "custom" )
                {   
                    
                    $key = trim( htmlspecialchars( stripcslashes( $key ) ) );
                    $c_data=json_decode( $data, true );
                    $cus_input=$c_data;

                }else if( $key== "cfquizo_header_content" )
                {
                    $header = $mysqli->real_escape_string( trim($ajax_datas[$key]) );
                }
                else if( $key== "cfquizo_quiz_name" )
                {
                    $quiz_name= $mysqli->real_escape_string( trim(htmlspecialchars($ajax_datas[$key])) ); 
                }
              
                else if( $key== "cfquizo_footer_content" )
                {
                    $footer=$mysqli->real_escape_string(trim($ajax_datas[$key]));
                }
                else if($key== "cfquizo_is_global")
                {
                    $is_global=(int)$mysqli->real_escape_string(trim($ajax_datas[$key]));
                }
                else if( $key== "cfquizo_custom_css" )
                {
                    $quiz_css=$mysqli->real_escape_string( trim($ajax_datas[$key]) );
                }
                else if( $key== "cfquizo" )
                {
                    $quiz_setup= $ajax_datas[$key] ;
                }       

            }      
            
            if( $ajax_datas['cfquizo_param'] == "save_popupquiz"  )
            {
                
                $quiz_setup = $mysqli->real_escape_string(json_encode($quiz_setup));
                
                global $mysqli;
                global $dbpref;
                
                $table=$dbpref.'cfquiz_popup';
                
                $sql = "INSERT INTO `".$table."`(`quiz_name`,`header_text`, `footer_text`, `quiz_setup`,`quiz_css`,`is_global`) VALUES ('".$quiz_name."' ,'".$header."','".$footer."','".$quiz_setup."','".$quiz_css."',".$is_global.")";
                
                $return_insert = $mysqli->query( $sql )?1:-1;
                
                if( $return_insert == 1 ){
                    $last_id = $mysqli->insert_id;
                    $position=1;

                    foreach ($cus_input as $key => $value) {
                        $this->cfquizo_add_custom_input( $value, $last_id, $position );
                        $position++;
                    }
                    echo json_encode( array( "status" => 1 , "success" => "Data Added Successfully","quiz_id"=>$last_id  ) );
                    die();
                }
            }else if( $ajax_datas['cfquizo_param'] == "update_popupquiz" ){
                $quiz_setup = json_encode($quiz_setup);
                
                global $mysqli;
                global $dbpref;

                $quiz_id   = $mysqli->real_escape_string($ajax_datas['cfquizo_quiz_id']);
                $shortcode = "[popup_shortcode id=".$quiz_id."]";
                $table=$dbpref.'cfquiz_popup';
                
                $sql="UPDATE `".$table."` SET `quiz_name`='".$quiz_name."',`header_text`='".$header."',`footer_text`='".$footer."',`quiz_setup`='".$quiz_setup."',`quiz_css`='".$quiz_css."',`is_global`=".$is_global." WHERE `id`=".$quiz_id;

                $return_insert = $mysqli->query( $sql )?1:-1;
                    if( $return_insert == 1 ){

                        $table1=$dbpref."cfquiz_popup_inputs";
                        $return_delete=$mysqli->query("DELETE FROM `".$table1."` WHERE `quiz_id`=".$quiz_id);
                        $position=1;
                        foreach ($cus_input as $key => $value) {
                            $this->cfquizo_add_custom_input( $value, $quiz_id, $position );
                            $position++;
                        }
                        echo json_encode( array( "status" => 1 , "success" => "Data Updated Successfully","quiz_id"=>$quiz_id  ) );
                            die();
                    }
            }elseif( $ajax_datas['cfquizo_param'] == "delete_quiz" ){
              
                global $dbpref;

                $quiz_id   = $mysqli->real_escape_string($ajax_datas['id']);
                
                $table=$dbpref.'cfquiz_popup';

                $sql3="DELETE FROM `".$table."` WHERE `id`=".$quiz_id;
                
                $quiz_delete = $mysqli->query($sql3)?1:-1;
                
                if($quiz_delete){
                    echo json_encode( array( "status" => 1 , "success" => "Data Deleted Successfully" ));
                    die();
                }
            }
        }


        public function getUsersideAjaxRequest( array $ajax_datas )
        {
        }
        function cfquizo_add_custom_input( array $custom = [], $quiz_id = "" , $position="" )
        {
            global $mysqli;
            global $dbpref;
            $table= $dbpref."cfquiz_popup_inputs";
            $custom_place = $mysqli->real_escape_string(  trim(htmlspecialchars( $custom['placeholder']) )   );
            $custom_name = $mysqli->real_escape_string( trim(htmlspecialchars( $custom['name']) ) );
            $custom_type =$mysqli->real_escape_string( trim(htmlspecialchars( $custom['type']) )  );
            $custom_title = $mysqli->real_escape_string( trim(htmlspecialchars( $custom['title']) )  );
            $custom_postion = $mysqli->real_escape_string( trim(htmlspecialchars( $position ) ) );
            $custom_required = $mysqli->real_escape_string( trim(htmlspecialchars( $custom['required']) ) );

            $result = $mysqli->query( "INSERT INTO `".$table."`(`quiz_id`, `name`, `placeholder`, `type`,`title`, `position`, `required`) VALUES (".$quiz_id." ,'".$custom_name."' ,'".$custom_place."' ,'".$custom_type."' ,'".$custom_title."','".$custom_postion."' ,'".$custom_required."' )" );
        }


      //It will insert quiz response and user details into the database
      function insert_quiz_response()
      {
      foreach ($_POST as $key => $values) {  
       $c=explode("@", $key);
       if($c[0]=="user"){
        $user_details[$c[1]]=$values;
       }
       if($c[0]=="opt"){
        $quizresponse[$c[1]]=base64_encode($values);
       }
      }
      $quizresponse=json_encode($quizresponse);
      $user_details=json_encode($user_details);
      $quiz_id=$_SESSION["quiz_id_fromshortcode"];
      global $mysqli;
      global $dbpref;
      $date=date('Y-m-d H:i:s');
      $table=$dbpref.'cfquiz_response';
      $quiz_id=$mysqli->real_escape_string($quiz_id);
      $quizresponse=$mysqli->real_escape_string($quizresponse);
      $user_details=$mysqli->real_escape_string($user_details);
      $quiz_id=(int)$quiz_id;
      $date=$mysqli->real_escape_string($date);
      $sql = "INSERT INTO `".$table."`(`quiz_id`, `user_details`, `quiz_response`, `added_on`) VALUES (".$quiz_id." ,'".$user_details."','".$quizresponse."','".$date."')";
      $return_insert = $mysqli->query( $sql )?1:-1;



      
      return $return_insert;
      }

      //to show questions for users in quiz 
      function select_question_ui()
      {
            global $mysqli;
            global $dbpref;
            $table=$dbpref.'cfquiz_questions2';
            $cond="";
            if(isset($_SESSION["quiz_id_fromshortcode"]))
            {
                $quiz_id=$_SESSION["quiz_id_fromshortcode"];
            }
            else
            {
                echo "<script>alert('Quiz id is missing');</script>";
            }
            if($quiz_id)
            {
                $quiz_id=$mysqli->real_escape_string($quiz_id);
                $cond=" where `quiz_id`=".$quiz_id;
            }  
            $qry=$mysqli->query("SELECT `id`, `quiz_id`, `question_pos`, `question`, `options`, `added_on` FROM `".$table."`".$cond." order by `id` desc");
            return $qry;
      }



    }
}
?>