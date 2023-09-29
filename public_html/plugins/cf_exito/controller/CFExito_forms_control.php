<?php
if(!class_exists('CFExito_forms_control'))
{
    class CFExito_forms_control
    {
        function __construct($arr)
        {
            $this->loader=$arr['loader'];
        }

          function getAllForms($total_forms=false,$max_limit=false,$page=1)
        {
            global $mysqli;
            global $dbpref;
            $table= $dbpref.'ext_popup_form';
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
                $search=" and `form_name` like '%".$search."%'";
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
            //////////////////////////////
            //echo "select * from `".$table."` where 1".$search." order by ".$order_by.$limit;
            $user_id=$_SESSION['user' . get_option('site_token')]; 
            $access=$_SESSION['access' . get_option('site_token')]; 
            if($access=='admin')
            {
                $qry=$mysqli->query("select * from `".$table."` where 1".$search." order by ".$order_by.$limit);
            }
            else
            {
                $qry=$mysqli->query("select * from `".$table."` where `user_id`=".$user_id."".$search." order by ".$order_by.$limit);
            }
            
            $arr=[];
            if($qry->num_rows>0)
            {
                while($data = $qry->fetch_assoc() )
                {
                    $arr[]=$data;
                }   
             
            }
            return $arr;

        }

        function getUsersCount( $form_id=null )
        {
            global $mysqli;
            global $dbpref;
            $form_id= $mysqli->real_escape_string( $form_id );

            $table2=$dbpref."ext_popup_optins";
            $user_id=$_SESSION['user' . get_option('site_token')]; 
            $access=$_SESSION['access' . get_option('site_token')]; 
            if($access=='admin')
            {
                $user_counts=$mysqli->query("SELECT COUNT(*) AS `total_user` FROM `".$table2."` WHERE `form_id`=".$form_id);
            }
            else
            {
                $user_counts=$mysqli->query("SELECT COUNT(*) AS `total_user` FROM `".$table2."` WHERE `form_id`=".$form_id." and `user_id`=".$user_id);
            }
            
            $user_count=$user_counts->fetch_assoc();
        }

         function getFormsCount()
        {
            global $mysqli;
            global $dbpref;
            $table= $dbpref.'ext_popup_form';
            $user_id=$_SESSION['user' . get_option('site_token')]; 
            $access=$_SESSION['access' . get_option('site_token')]; 
            if($access=='admin')
            {
                $qry=$mysqli->query("select count(`id`) as `total_forms` from `".$table."`");
            }
            else
            {
                $qry=$mysqli->query("select count(`id`) as `total_forms` from `".$table."` where `user_id`=".$user_id);
            }
            
            $r=$qry->fetch_object();
            return $r->total_forms;
        }

        function getMiniForms($id=false)
        {
            global $mysqli;
            global $dbpref;
            $table=$dbpref.'ext_popup_form';

            $arr=array();

            $cond="";
            if($id)
            {
                $id=$mysqli->real_escape_string($id);
                $cond=" where `id`=".$id;
            }
            $user_id=$_SESSION['user' . get_option('site_token')]; 
            $access=$_SESSION['access' . get_option('site_token')]; 
            if($access=='admin')
            {
                $qry=$mysqli->query("select `id`, `form_name` from `".$table."`".$cond." order by `id` desc");
            }
            else
            {
                $qry=$mysqli->query("select `id`, `form_name` from `".$table."`".$cond." and `user_id`=".$user_id." order by `id` desc");
            }
            while($r=$qry->fetch_object())
            {
                $arr[$r->id]=$r->form_name;
            }

            return $arr;
        }
        function getValidInputs($id)
        {
            global $mysqli;
            global $dbpref;
            $id=$mysqli->real_escape_string($id);

            $arr=array();
            $table= $dbpref.'ext_popup_inputs';
            $user_id=$_SESSION['user' . get_option('site_token')]; 
            $access=$_SESSION['access' . get_option('site_token')]; 
            if($access=='admin')
            {
                $qry=$mysqli->query("select * from `".$table."` where `form_id`=".$id." order by `position` asc");
            }
            else
            {
                $qry=$mysqli->query("select * from `".$table."` where `form_id`=".$id." and `user_id`=".$user_id." order by `position` asc");
            }
            
            while($r=$qry->fetch_object())
            {
                array_push($arr,$r->name);
            }

            return $arr;
        }
        function allowProcessingInMainCF($form_id)
        {
            //allow processing in m
            global $mysqli;
            global $dbpref;
            $table=$dbpref.'ext_popup_form';
            $form_id=(int)$mysqli->real_escape_string($form_id);
        }
        function getFormSetup( $form_id = null,$setup_only=false )
        {
            global $mysqli;
            global $dbpref;
            $table=$dbpref.'ext_popup_form';
            $form_id = trim( $mysqli->real_escape_string( $form_id ) );

            $get=($setup_only)? '`form_setup`':'*';
            $user_id=$_SESSION['user' . get_option('site_token')]; 
            $access=$_SESSION['access' . get_option('site_token')]; 
            if($access=='admin')
             $r = $mysqli->query("SELECT ".$get." FROM `".$table."` WHERE `id`=".$form_id );
            else
                $r = $mysqli->query("SELECT ".$get." FROM `".$table."` WHERE `id`=".$form_id." and `user_id`=".$user_id );

             if( $r->num_rows > 0)
             {
                $data = $r->fetch_assoc();
                if($setup_only)
                {
                    return $data['form_setup'];
                }
                else{return $data;}
             }
             return 0;
        }

    function cfexitoGetFormInput( $form_id=null )
    {
        global $mysqli;
        global $dbpref;
        $table=$dbpref.'ext_popup_inputs';

        $form_id = trim($mysqli->real_escape_string( $form_id ));
        $user_id=$_SESSION['user' . get_option('site_token')]; 
        $access=$_SESSION['access' . get_option('site_token')]; 
        if($access=='admin')
        {
            $r = $mysqli->query("SELECT * FROM `".$table."` WHERE `form_id`=".$form_id." ORDER BY `position` ASC");
        }
        else
        {
            $r = $mysqli->query("SELECT * FROM `".$table."` WHERE `form_id`=".$form_id." and `user_id`=".$user_id." ORDER BY `position` ASC");
        }
        if( $returnOptions->num_rows > 0)
        {
            return $returnOptions;
        }
        return 0;
    }
    function loadGlobalForms($config_version=0)
    {
        global $mysqli;
        global $dbpref;
        $table=$dbpref.'ext_popup_form';
        $user_id=$_SESSION['user' . get_option('site_token')]; 
        $access=$_SESSION['access' . get_option('site_token')]; 
        if($access=='admin')
        {
            $qry=$mysqli->query("select `id` from `".$table."` where `is_global`=1");
        }
        else
        {
            $qry=$mysqli->query("select `id` from `".$table."` where `is_global`=1 and `user_id`=".$user_id);
        }
        
        while($r=$qry->fetch_object())
        {
            self::getFormUI($r->id, $config_version);
        }
    }
    function doControlCookieForSubscription($form_id,$doo='get')
    {
        //$doo should be 'get' or 'set'
        $setup=self::getFormSetup($form_id, true);

        if($setup)
        {
            if($doo=='get')
            {
                $setup=json_decode($setup);
                if(isset($setup->dont_display_after_subscription) && $setup->dont_display_after_subscription=='1')
                {
                    if(isset($_COOKIE['cfexito_form_subscribed_'.$form_id]))
                    {
                        return false;
                    }
                }
            }
            else if($doo=='set')
            {
                setcookie('cfexito_form_subscribed_'.$form_id,1,time()+86400*30);
            }
        }
        return true;
    }
    function initFormSubmit()
    {
        $cfexito_form_submit_err="";
        if(isset($_POST['cfexito_store_data']))
        {
            $optin_ob= $this->loader->load('optin_control');
            $err=$optin_ob->storeLeads();
            if(isset($err['status']) && $err['status']===0)
            {
                $cfexito_form_submit_err=$err['msg'];
            }
        }
        $GLOBALS['cfexito_form_submit_err']=$cfexito_form_submit_err;
    }
    function getFormUI($id= null, $config_version=0)
    {
        if(!self::doControlCookieForSubscription($id)){return;}
        $form_data=self::getFormSetup($id);
        if($form_data)
        {
            global $cfexito_form_submit_err;
            $show_err=$cfexito_form_submit_err;
            require plugin_dir_path( dirname(__FILE__,1) )."/views/popupForm.php";
        }
        else{echo "";}
    }
    public function getAjaxRequest( array $ajax_datas )
    {
            global $mysqli;

            $cus_input = [];
            $form_setup =[];
            
            foreach ( $ajax_datas as $key => $data ) {
                $data_expload = explode( "@", $key );
    
                if( $data_expload[0] == "custom" )
                {   
                    // $data = trim( htmlspecialchars( stripcslashes( $data ) ) );
                    $key = trim( htmlspecialchars( stripcslashes( $key ) ) );
                    $c_data=json_decode( $data, true );
                    $cus_input=$c_data;

                }else if( $key== "cfexito_header_content" )
                {
                    $header = $mysqli->real_escape_string( trim($ajax_datas[$key]) );
                }
                else if( $key== "cfexito_form_name" )
                {
                    $form_name= $mysqli->real_escape_string( trim(htmlspecialchars($ajax_datas[$key])) ); 
                }   
                else if( $key== "cfexito_footer_content" )
                {
                    $footer=$mysqli->real_escape_string(trim($ajax_datas[$key]));
                }
                else if($key== "cfexito_is_global")
                {
                    $is_global=(int)$mysqli->real_escape_string(trim($ajax_datas[$key]));
                }
                else if( $key== "cfexito_custom_css" )
                {
                    $form_css=$mysqli->real_escape_string( trim($ajax_datas[$key]) );
                }
                else if( $key== "cfexito" )
                {
                    $form_setup= $ajax_datas[$key] ;
                }       

            }      
            //$form_css="";

            if( $ajax_datas['cfexito_param'] == "save_popupForm"  )
            {
                $form_setup = $mysqli->real_escape_string(json_encode($form_setup));
                
                global $mysqli;
                global $dbpref;
                
                $table=$dbpref.'ext_popup_form';
                $user_id=$_SESSION['user' . get_option('site_token')]; 
                //$access=$_SESSION['access' . get_option('site_token')]; 
                $sql = "INSERT INTO `".$table."`(`form_name`,`header_text`, `footer_text`, `form_setup`,`form_css`,`is_global`,`user_id`) VALUES ('".$form_name."' ,'".$header."','".$footer."','".$form_setup."','".$form_css."',".$is_global.",".$user_id.")";
                $return_insert = $mysqli->query( $sql )?1:-1;
                if( $return_insert == 1 ){
                    $last_id = $mysqli->insert_id;
                    $position=1;

                    foreach ($cus_input as $key => $value) {
                        $this->cfexito_add_custom_input( $value, $last_id, $position );
                        $position++;
                    }
                    echo json_encode( array( "status" => 1 , "success" => "data addedd succefully","form_id"=>$last_id  ) );
                    die();
                }
            }else if( $ajax_datas['cfexito_param'] == "update_popupForm" ){
                $form_setup = json_encode($form_setup);
                
                global $mysqli;
                global $dbpref;

                $form_id   = $mysqli->real_escape_string($ajax_datas['cfexito_form_id']);
                $shortcode = "[popup_shortcode id=".$form_id."]";
                $table=$dbpref.'ext_popup_form';
                
                $sql="UPDATE `".$table."` SET `form_name`='".$form_name."',`header_text`='".$header."',`footer_text`='".$footer."',`form_setup`='".$form_setup."',`form_css`='".$form_css."',`is_global`=".$is_global." WHERE `id`=".$form_id;

                $return_insert = $mysqli->query( $sql )?1:-1;
                    if( $return_insert == 1 ){

                        $table1=$dbpref."ext_popup_inputs";
                        $return_delete=$mysqli->query("DELETE FROM `".$table1."` WHERE `form_id`=".$form_id);
                        /*
                        update_option( "cfexito_popop_show",$ajax_datas['cfexito_popop_where_show']."@".$form_id );
                        */
                        $position=1;
                        foreach ($cus_input as $key => $value) {
                            $this->cfexito_add_custom_input( $value, $form_id, $position );
                            $position++;
                        }
                        echo json_encode( array( "status" => 1 , "success" => "data updated succefully","form_id"=>$form_id  ) );
                            die();
                    }
            }elseif( $ajax_datas['cfexito_param'] == "delete_form" ){
                
                global $dbpref;

                $form_id   = $mysqli->real_escape_string($ajax_datas['id']);
                
                $table=$dbpref.'ext_popup_form';
                $sql3="DELETE FROM `".$table."` WHERE `id`=".$form_id;
                $form_delete = $mysqli->query($sql3)?1:-1;
                
                if($form_delete){
                    echo json_encode( array( "status" => 1 , "success" => "data deleted succefully" ));
                    die();
                }
            }
        }

        function cfexito_add_custom_input( array $custom = [], $form_id = "" , $position="" )
        {
            global $mysqli;
            global $dbpref;
            $table= $dbpref."ext_popup_inputs";
            $custom_place = $mysqli->real_escape_string(  trim(htmlspecialchars( $custom['placeholder']) )   );
            $custom_name = $mysqli->real_escape_string( trim(htmlspecialchars( $custom['name']) ) );
            $custom_type =$mysqli->real_escape_string( trim(htmlspecialchars( $custom['type']) )  );
            $custom_title = $mysqli->real_escape_string( trim(htmlspecialchars( $custom['title']) )  );
            $custom_postion = $mysqli->real_escape_string( trim(htmlspecialchars( $position ) ) );
            $custom_required = $mysqli->real_escape_string( trim(htmlspecialchars( $custom['required']) ) );

            $result = $mysqli->query( "INSERT INTO `".$table."`(`form_id`, `name`, `placeholder`, `type`,`title`, `position`, `required`) VALUES (".$form_id." ,'".$custom_name."' ,'".$custom_place."' ,'".$custom_type."' ,'".$custom_title."','".$custom_postion."' ,'".$custom_required."' )" );
           
        }
    }
}
?>