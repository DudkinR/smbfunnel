<?php
if(!class_exists('CFAddStudent_setup'))
{
  class CFAddStudent_setup
  {
    var $pref="cfaddstudent_";
    var $student='';
    function __construct($arr)
    {
      global $app_variant;
			$app_variant = isset($app_variant)?$app_variant:"coursefunnels";
			if( $app_variant == "shopfunnels" ){
				$this->student = "Customer";
	
			}
			elseif( $app_variant == "cloudfunnels" ){
				$this->student = "Member";
	
			}
			elseif( $app_variant == "coursefunnels" ){
				$this->student = "Student";
	
			}
      $this->loader=$arr['loader'];
    }
    function text_to_avatar($txt){
      $colors=  ['#003366', '#005580', '#049560', '#e68a00', '#e62e00', '#e6005c', '#660066', '#800040', '#990099', '#008000', '#73264d'];
      shuffle($colors);
      $txt= strtoupper(trim($txt));
      $txt= preg_replace('/(\s){2,}/', ' ', $txt);
      $avatar= "";
      if(strlen($txt)>0) {
      $arr= array_slice(explode(" ", $txt), 0, 2);
      foreach($arr as $word){
          $avatar .= substr($word, 0, 1);
      }
      $color= $colors[mt_rand(0, 10)];
      $av_len= count($arr);
      $avatar = "<div style='background-color: ".$color."' cfs-studnet-store-avatar='true'><span>".$avatar."</span></div>";

      }
      return $avatar;
    }
    function getAllFunnels($funnel_id=false)
    {
      global $mysqli;
      global $dbpref;
      $table= $dbpref."quick_funnels";
      $user_id=$_SESSION['user' . get_option('site_token')];
      $access=$_SESSION['access' . get_option('site_token')];
      
      if($funnel_id)
      {
        $id = $mysqli->real_escape_string($funnel_id);
        $qry=$mysqli->query("select `id`,`name` from `$table` WHERE `type`='membership' AND `id`=$id");
      }else{
        $qry=$mysqli->query("select `id`,`name` from `$table` WHERE `type`='membership'");
      }
      $arr=array();

      //////////////////////////////

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

    function gedtAllStudents($total_forms=false,$max_limit=false,$page=1,$funnels_id)
    {
      global $mysqli;
      global $dbpref;
      $table= $dbpref."quick_member";
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
        $search=" and `name` like '%".$search."%' or `email` like '%".$search."%' or `ip_lastsignin` like '%".$search."%'";
      }
      $order_by="`id` desc";
      if(isset($_GET['arrange_records_order']))
      {
        $order_by=base64_decode($_GET['arrange_records_order']);
      }

      $date_between=dateBetween('date_created',null,true);

      if(strlen($date_between[0])>0)
      {
          $search .=$date_between[1];
      }
      if($funnels_id)
      {
        $search.=" and `funnelid`=$funnels_id";
      }
      //////////////////////////////
      $qry=$mysqli->query("select * from `".$table."` where 1".$search." order by ".$order_by.$limit);
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
    function getSingleStudent( $student_id )
    {
      global $mysqli;
      global $dbpref;
      $table= $dbpref."quick_member";
      $student_id=$mysqli->real_escape_string($student_id);
      $qry=$mysqli->query("select * from `".$table."` where `id`=$student_id");
      if($qry->num_rows>0)
      {
        while($data = $qry->fetch_assoc() )
        {
          return $data;
        }   
      }
      return [];
    }
    function getSingleStudentCourses($student_id)
    {    
    
      global $mysqli;
      global $dbpref;
      $array = [];
      $table = $dbpref . "quick_membership";
      $student_id = $mysqli->real_escape_string($student_id['id']);            
      $qry = $mysqli->query("select * from `" . $table . "` where `membership`='".$student_id."'");
      
      if ($qry->num_rows > 0) {
        while ($data_row = $qry->fetch_object()) {          
          array_push($array,$data_row);
          
        }
        
      }
      echo json_encode($array);
      die();
      
    }

    function getStudentsCount( $funnels_id=false )
    {

      global $mysqli;
      global $dbpref;
      $table= $dbpref.'quick_member';
      if( $funnels_id )
      {
        $fid=$mysqli->real_escape_string( $funnels_id );
        $qry=$mysqli->query("select count(`id`) as `total_setup` from `".$table."` WHERE `funnelid`=$fid");
      }else{
        $qry=$mysqli->query("select count(`id`) as `total_setup` from `".$table."`");

      }

      $r=$qry->fetch_object();
      return $r->total_setup;
    }
    function getAllFunnelsCounts()
    {
      global $mysqli;
      global $dbpref;
      $table= $dbpref.'quick_funnels';

      $qry=$mysqli->query("select count(`id`) as `total_setup` from `".$table."`");

      $r=$qry->fetch_object();
      return $r->total_setup;
    }
    function addStudentManually( $data ){

      global $mysqli;
      global $dbpref;

    	$table=$dbpref."quick_member";
      $name = trim($mysqli->real_escape_string($data['name']));
      $email = trim($mysqli->real_escape_string($data['email']));
      $funnel_id = $mysqli->real_escape_string($data['funnel_id']);     
      $pass = trim($mysqli->real_escape_string( $data['password']));
      $cpass =trim( $mysqli->real_escape_string($data['cpassword']));
      $valid =trim( $mysqli->real_escape_string($data['valid']));
      if(isset($data['select_courses']))
      {
        $all_courses = $data['select_courses'];
      }
      else{
        $all_courses = [];
      }


      $names = $data['input_name'];
      $values = $data['input_value'];
      $funnel_id=(int)$funnel_id;
      $page_id = $mysqli->real_escape_string($data['page_id']);
      $page_id = (int)$page_id;
      $pas_regex=base64_decode(get_option('secure_password_regex'));
      $pass_reges="/".$pas_regex."/";
      
      if( empty( $name) )
      {
        return json_encode(array('status'=>0,'message'=>'Please Enter Name!'));
      }
      if( empty( $email) )
      {
        return json_encode(array('status'=>0,'message'=>'Please Enter Email!'));
      }

      if(!filter_var($email, FILTER_VALIDATE_EMAIL))
      {
        return json_encode(array('status'=>0,'message'=>'Inavlid Email!'));
      }
      if(!filter_var($email, FILTER_VALIDATE_EMAIL))
      {
        return json_encode(array('status'=>0,'message'=>'Inavlid Email!'));
      }
      $extra=[];
      if( count( $names ) > 0 && ( count( $names ) == count( $values )  ) )
      {
        $len = count( $names );
        for( $i = 0; $i < $len ;  $i++)
        {
          if( !empty( $names[$i] ) ){
            $extra[$mysqli->real_escape_string( $names[$i] ) ] = $mysqli->real_escape_string( $values[$i] );
          }
        }
      }
      $e = json_encode( (object)$extra );
		  $password = password_hash( $pass, PASSWORD_DEFAULT );
		  $date_created = time();
		  $ip = getIP();
      
      if( $data['savestudents'] == "save" )
		  {
        if( empty( $pass) )
        {
          return json_encode(array('status'=>0,'message'=>'Please Enter Password!'));
        }
        if($pass != $cpass)
        {
          return json_encode(array('status'=>0,'message'=>'Password and confirm password should be same'));
        }

        if(!preg_match($pass_reges,$pass))
        {
          return json_encode(array('status'=>0,'message'=>'Please insert password with a minimum length of eight and combination of upper and lowercase characters, numbers and special characters'));
        }


        $check_email = "SELECT `id` FROM `".$table."` WHERE `email`='".$email."' AND `funnelid`=$funnel_id AND `pageid`=$page_id";
        $check = $mysqli->query( $check_email );
        if( $mysqli->affected_rows <= 0 )
        {
          $sql="INSERT INTO `".$table."` (`funnelid`, `pageid`, `name`, `email`, `password`, `verified`, `verifycode`, `date_verifycodegen`, `ip_created`, `ip_lastsignin`, `date_created`, `date_lastsignin`, `valid`, `exf`) 
          VALUES ($funnel_id,$page_id,'".$name."','".$email."','".$password."','custom','custom','custom','".$ip."','".$ip."','".$date_created."','','$valid','".$e."')";
          if( $mysqli->query($sql) )
          {
            $last_id = $mysqli->insert_id;
            $table2 = $dbpref . "quick_membership";
            if( is_array( $all_courses ) && count( $all_courses ) > 0 )
            {
              foreach ($all_courses as $val) {              
                $table3 = $dbpref . "all_products";
                $sql3 = mysqli_fetch_assoc($mysqli->query("SELECT `course_type` FROM `" . $table3 . "` WHERE `id` = " .  $mysqli->real_escape_string($val) . ""));             

                $sql4 = "INSERT INTO `" . $table2 . "` (`membership`, `funnelid`, `product`, `product_type`) VALUES ($last_id,$funnel_id,$val ,'" . $sql3['course_type'] . "')";
                $mysqli->query($sql4);
              }
            }
            return json_encode(array('status'=>1,'message'=>ucfirst($this->student).' added successfully','funnel_id'=>$funnel_id,'student_id'=>$last_id));
          }else{
            return json_encode(array('status'=>0,'message'=>'There is something wrong please refresh the page'));
          }

        }else{
          return json_encode(array('status'=>0,'message'=>ucfirst($this->student).' already available. Please use different unique email.'));
        }

      }else if( $data['savestudents'] == "update" )
      {

        $id=$mysqli->real_escape_string( $data['cfstudent_id'] );

        $sql1 = "SELECT `email` FROM `".$table."` WHERE `id`=$id";
        if( !empty( $pass ) )
        {
          if($pass != $cpass)
          {
            return json_encode(array('status'=>0,'message'=>'Password and confirm password should be same'));
          }
          if(!preg_match($pass_reges,$pass))
          {
            return json_encode(array('status'=>0,'message'=>'Please insert password with a minimum length of eight and combination of upper and lowercase characters, numbers and special characters'));
          }
        }
        $check = $mysqli->query( $sql1 );
        if( $check->num_rows > 0 )
        {
          $result = $check->fetch_assoc();
          $gemail = $result['email'];
          if( empty( $pass) )
          {
            $sql="UPDATE `".$table."` SET `name`='".$name."',`email`='".$email."',`valid`='$valid' , `exf`='".$e."' WHERE `id`=$id";
          }else{
            $sql="UPDATE `".$table."` SET `name`='".$name."',`email`='".$email."' ,`password`='".$password."', `exf`='".$e."' WHERE `id`=$id";
          }
          if( $gemail == $email )
          {
            if( $mysqli->query( $sql ) )
            {
              $table2 = $dbpref . "quick_membership";
              $sql5 = "DELETE FROM `" . $table2 . "` WHERE `membership` = $id";
              $mysqli->query($sql5); 
              if( is_array( $all_courses ) && count( $all_courses ) > 0 )
              {
                foreach ($all_courses as $val) {              
                  $table3 = $dbpref . "all_products";
                  $sql3 = mysqli_fetch_assoc($mysqli->query("SELECT `course_type` FROM `" . $table3 . "` WHERE `id` = " .  $mysqli->real_escape_string($val) . ""));      
    
                  $sql4 = "INSERT INTO `" . $table2 . "` (`membership`, `funnelid`, `product`, `product_type`) VALUES ($id,$funnel_id,$val ,'" . $sql3['course_type'] . "')";
                  $mysqli->query($sql4);
                }  
              }
              return json_encode(array('status'=>1,'message'=>ucfirst($this->student).' update successfully','funnel_id'=>$funnel_id,'student_id'=>$id));
            }else{
              return json_encode(array('status'=>0,'message'=>'There is something wrong please refresh the page'));
            }
          }else{

            $check_qry = "SELECT `id` FROM `".$table."` WHERE `email`='".$email."'";
            $check_email=$mysqli->query($check_qry);
            if( $check_email->num_rows <= 0 )
            {
              if( $mysqli->query( $sql ) )
              {
                return json_encode(array('status'=>1,'message'=>ucfirst($this->student).' updated successfully','funnel_id'=>$funnel_id,'student_id'=>$id));
              }else{
                return json_encode(array('status'=>0,'message'=>'There is something wrong please refresh the page'));
              }
            }else{
              return json_encode(array('status'=>0,'message'=>ucfirst($this->student).' already available. Please use different unique email.'));
            }
          }
        }else{
          return json_encode(array('status'=>0,'message'=>'Invalid '.ucfirst($this->student).'. Please refresh the page.'));
        }
        return json_encode(array('status'=>2,'message'=>'Invalid '.ucfirst($this->student).'. Please refresh the page.'));
      }
    }
    function addBulkStuden( $data )
    {
      global $mysqli;
      global $dbpref;

    	$table=$dbpref."quick_member";
      $funnel_id = $mysqli->real_escape_string( $data['funnel_id'] );   
      $page_id = $mysqli->real_escape_string( $data['page_id'] );
      $namef = (int)$mysqli->real_escape_string( $data['namef'] );
      $emailf = (int)$mysqli->real_escape_string( $data['emailf'] );
      $files = $_FILES['filess'];
      $fileName = $files["tmp_name"];
      $coursesf = $data['select_courses'];
    
      if ($files["size"] > 0) {
        
        $file = fopen($fileName, "r");
        $columnss=[];
        $headers=[];
        $i=0;
        while ( ( $column = fgetcsv( $file, 10000, "," ) ) !== FALSE) {
          if($i==0)
          {
            $headers=$column;
          }else{
            $columnss[]=(array)$column;
          }
          $i++;
        }
        if( count( $headers ) <= 1)
        {
          return json_encode(array('status'=>0,"message"=>"There is the name or email or both not available in the CSV file"));
        }

        if(count($columnss)==0)
        {
          return json_encode(array('status'=>0,"message"=>"Please add atleast one record in the CSV file and try again"));
        }

        foreach($columnss as $columns)
        {
          $extra=[];
          $name='';
          $email=false;

          
          foreach($columns as $j=> $col)
          {
            if( $j == $namef ){
              $name = $mysqli->real_escape_string($col);
            }else if( $j == $emailf ){
              $email =$mysqli->real_escape_string( $col);
              // Remove all illegal characters from email
              $email = filter_var($email, FILTER_SANITIZE_EMAIL);
              // Validate e-mail
              if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $email = $email;  
              }else{
                $email=false;
              } 
            }else{
              $extra[$mysqli->real_escape_string($headers[$j])] = $mysqli->real_escape_string($col);
            }
          }
          if($email)
          {
            $check_qry = "SELECT `id` FROM `$table` WHERE `email`='$email' AND `funnelid`=$funnel_id";
            $check_email=$mysqli->query($check_qry);
            if( $check_email->num_rows == 0 )
            {
              
              $date_created = time();
              $pass = substr($email,0,4);
              $pass = ucfirst($pass)."$1234";
              $password = password_hash( $pass, PASSWORD_DEFAULT );
		          $ip = getIP();
              $e = json_encode((object)$extra);

              $sql="INSERT INTO `".$table."` (`funnelid`, `pageid`, `name`, `email`, `password`, `verified`, `verifycode`, `date_verifycodegen`, `ip_created`, `ip_lastsignin`, `date_created`, `date_lastsignin`, `valid`, `exf`) 
              VALUES ($funnel_id,$page_id,'".$name."','".$email."','".$password."','custom','custom','custom','".$ip."','".$ip."','".$date_created."','','1','".$e."')";
              $mysqli->query($sql);
              $last_id = $mysqli->insert_id;
              $table2 = $dbpref . "quick_membership";
              if( is_array( $coursesf ) && count( $coursesf ) > 0 )
              {
                foreach ($coursesf as $val) {              
                  $table3 = $dbpref . "all_products";
                  $sql3 = mysqli_fetch_assoc($mysqli->query("SELECT `course_type` FROM `" . $table3 . "` WHERE `id` = " .  $mysqli->real_escape_string($val) . ""));             
    
                  $sql4 = "INSERT INTO `" . $table2 . "` (`membership`, `funnelid`, `product`, `product_type`) VALUES ($last_id,$funnel_id,$val ,'" . $sql3['course_type'] . "')";
                  $mysqli->query($sql4);
                }
              }  
            }
          }
        }
        return json_encode(array('status'=>1));
      }
      else{
        return json_encode(array('status'=>0,"message"=>"Please create header row for CSV file and try again"));
      }
    }

    function updatedelete($ajax,$param){
      global $mysqli;
      global $dbpref;
      $student_id   = $mysqli->real_escape_string($ajax['id']);
      $table=$dbpref."quick_member";
      if( $param == "delete" ){        
        $sql="DELETE FROM `".$table."` WHERE `id`=".$student_id;
          $form_delete = $mysqli->query($sql)?1:-1;
          if($form_delete){
            echo json_encode( array( "status" => 1 , "success" => ucfirst($this->student)." deleted succefully" ));
          die();
        }
      }
      elseif( $param == "cancel")
      {
        $update   =  $mysqli->real_escape_string($ajax['update']);
        $sql="UPDATE `$table` SET `valid`=$update  WHERE `id`=".$student_id;
        $form_update = $mysqli->query($sql)?1:-1;
        if( $form_update ){
          echo json_encode( array( "status" => 1 ));
        die();
        }
      }
    }
    function getPages( $ajax ){

      global $mysqli;

      $param=$mysqli->real_escape_string($ajax['cfaddstudent_param']);
      $funnel_id   = $mysqli->real_escape_string($ajax['funnel_id']);

      if( $param == "get_page" ){
        $pages = get_funnel_pages( $funnel_id );
        $data ='<option value="-1"> Select Page</option>';
        if(isset($pages) && $pages)
        {
          foreach(  $pages as $page ) 
          {
            $data .='<option value="'.$page['id'].'">'.$page['file_name'].'</option>';
          }  
          echo json_encode( array( "status" => 1 , "success" => $data ));
          die();
        
        }else{
          $data .='<option value="-1"> No Pages available</option>';

          echo json_encode( array( "status" => 1 , "success" => $data ));
          die();
        }
        
      }
    }
  }
}
?>