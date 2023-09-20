<?php
if(!class_exists('CFspinner_forms_control'))
{
    class CFspinner_forms_control
    {
        function __construct($arr)
        {
            $this->loader=$arr['loader'];
        }

         
      function getWheelui($id= null, $config_version=0)
      {
        $wheeldata=self::getWheelsetup($id);
        if($wheeldata)
        {
        
             require plugin_dir_path( dirname(__FILE__,1) )."/views/showwheel.php";
        }
        else{
          echo "";
        }
       
      }
      function getWheelsetup( $wheelid = null )
      {
          global $mysqli;
          global $dbpref;
           $table= $dbpref.'spinwheel_setting';
          $wheelid = trim( $mysqli->real_escape_string( $wheelid ) );

         
//  If(get_option('cfwheelid')== $wheelid)
//  {
           $r = $mysqli->query("SELECT * FROM `".$table."` WHERE `id`=".$wheelid );

           if( $r->num_rows > 0)
           {
              $data = $r->fetch_assoc();
              return $data;
           }
           else{
           return 0;
           }
      // }
    
    // else{
    //   return 0;
    // }

  }


    

      function cfspinnerGetFormInput( $wheelid=null )
      {
          global $mysqli;
          global $dbpref;
          $table=$dbpref.'spinner_popup_forminputs';
  
          $wheelid = trim($mysqli->real_escape_string( $wheelid ));
          $returnOptions = $mysqli->query("SELECT * FROM `".$table."` WHERE `cfspinwheelid`=".$wheelid." ORDER BY `position` ASC");
  
          if( $returnOptions->num_rows > 0)
          {
              return $returnOptions;
          }
          return 0;
      }
      public function getFormrequestdata($ajax_datas)
      {
        global $mysqli;
        global $dbpref;
        $table= $dbpref.'spinwheelusers';
        $time =date('Y-m-d H:i:s');
        $data=array();

        foreach($_POST as $index=>$val)
        {

          if($index==='name')
          {
              $name=$mysqli->real_escape_string($val);
              continue;
          }
          elseif($index==='email')
          {
              $email=$mysqli->real_escape_string($val);
              continue;

          }
          elseif($index==='action')
          {
            $action=$mysqli->real_escape_string($val);
              continue;

          }
          elseif($index==='cfspinner_nonce')
          {
            $nonce=$mysqli->real_escape_string($val);
              continue;
          }
          elseif($index==='cfspinner_wheelid')
          {
            $cfspinner_wheelid=$mysqli->real_escape_string($val);
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


          $sql = "SELECT `email` FROM `".$table."` WHERE `email` ='".$email."' AND `wheelid` = '".$cfspinner_wheelid."'" ;
        $result = $mysqli->query($sql);
        
          if ( $result->num_rows>0)
           {
            echo json_encode(array("statusCode"=>0));
            die();
          }
      else
      {
         $sql = "INSERT INTO  `".$table."`(`wheelid`, `name`, `email`, `exf`, `winprize`, `mailstatus`, `added_on`)VALUES ('".$cfspinner_wheelid."','".$name."','".$email."','".$data."','prize','0','".$time."')";
       $return_insert = $mysqli->query( $sql );
       if($return_insert)
       {
       echo json_encode(array("statusCode"=>1));
       die();


}
      }
    }


public function Storesetting($ajax)
{


  if( $ajax['cfspinner_param'] == "save_settingForm"  )
  {
  global $mysqli;
            global $dbpref;
  $cfspinnerwheel = trim( $mysqli->real_escape_string( $ajax['cfspinnerwheel'] ) );
  $cfspinnernum = trim( $mysqli->real_escape_string( $ajax['cfspinnernum'] ) );
  $cfspinslicefontsize = trim( $mysqli->real_escape_string( $ajax['cfspinslicefontsize'] ) );
  $cfspinfont = trim( $mysqli->real_escape_string( $ajax['cfspinfont'] ) );
  $cfspinfontstyle = trim( $mysqli->real_escape_string( $ajax['cfspinfontstyle'] ) );
  $cfspinwheeltype = trim( $mysqli->real_escape_string( $ajax['cfspinwheeltype'] ) );
  $cf_spinner_header_Content = trim( $mysqli->real_escape_string( $ajax['cf_spinner_header_Content'] ) );
  $cf_spinner_footer_Content = trim( $mysqli->real_escape_string( $ajax['cf_spinner_footer_Content'] ) );
  $cfspinner_theme = trim( $mysqli->real_escape_string( $ajax['cfspinner_theme'] ) );
   $cfspinnerwheelimgurl = trim($mysqli->real_escape_string($ajax['cfspinnerwheelimgurl']));
   $cfspinmailsub = trim($mysqli->real_escape_string($ajax['cfspinmailsub']));
   $cf_spinner_mailerbody = trim($mysqli->real_escape_string($ajax['cf_spinner_mailerbody']));
 $custominputs= $ajax['custominputs'] ;

        $cus_input = [];
        $c_data=json_decode( $custominputs, true );
        $cus_input=$c_data;
      
              $table=$dbpref.'spinwheel_setting';
            $time =date('Y-m-d H:i:s');       
            if($cfspinner_theme == "cfwheelcolbg")
            {
            $cfslicelabel = $_POST['cfslicelabel'];
            $cfslicelabelcolor = $_POST['cfslicelabelcolor'];
            $cfslicefontcolor = $_POST['cfslicefontcolor'];
            $cfspinnerwheelimgurl="blank";

           $new_arr=[];
           for($i=0;$i<count($cfslicelabelcolor);$i++)
           {
             $arr['cfslicelabel']      =$cfslicelabel[$i]; 
             $arr['cfslicelabelcolor'] =$cfslicelabelcolor[$i]; 
             $arr['cfslicefontcolor'] =$cfslicefontcolor[$i]; 
             $new_arr[]=$arr;
           }
             $jsonencodelabel  = json_encode($new_arr);
          }


          if($cfspinner_theme == "cfwheelimgbg")
          {
          $cfsliceimglabel = $_POST['cfsliceimglabel'];
          $cfsliceimgfontcolor = $_POST['cfsliceimgfontcolor'];
         
       
         $new_arr=[];
         for($i=0;$i<count($cfsliceimgfontcolor);$i++)
         {
           $arr['cfsliceimglabel']      =$cfsliceimglabel[$i]; 
           $arr['cfsliceimgfontcolor'] =$cfsliceimgfontcolor[$i]; 
          
           $new_arr[]=$arr;
         }
         $jsonencodelabel  = json_encode($new_arr);
        }



         

        $sql = "INSERT INTO  `".$table."`( `cfspinnerwheel`, `cfspinnernum`, `cfspinfont`, `cfspinfontstyle`, `cfspinslicefontsize`, `cfspinmainheader`, `cfspinmaifooter`, `cfspinwheeltype`, `cfspinner_theme`, `cfslicepricenames`, `cfspinnerbgimgurl`,`cfspinmailsub`,`cf_spinner_mailerbody`,`created_at`) VALUES ('".$cfspinnerwheel."','".$cfspinnernum."','".$cfspinfont."','".$cfspinfontstyle."','".$cfspinslicefontsize."','".$cf_spinner_header_Content."','".$cf_spinner_footer_Content."','".$cfspinwheeltype."','".$cfspinner_theme."','".$jsonencodelabel."','".$cfspinnerwheelimgurl."','".$cfspinmailsub."','".$cf_spinner_mailerbody."','".$time."')";

 $return_insert = $mysqli->query($sql)?1:0;
 if( $return_insert == 1 ){
  $last_id = $mysqli->insert_id;
  $position=1;
  foreach ($cus_input as $key => $value) {
 
    $this->cfspinner_add_custom_input( $value, $last_id, $position );
    $position++;
}
echo json_encode( array( "status" => 1 , "success" => "Data addedd succefully","wheelid"=>$last_id  ) );
die();

}


}else if( $ajax['cfspinner_param'] == "update_settingForm" ){

  global $mysqli;
  global $dbpref;

  $wheelid   = $mysqli->real_escape_string($ajax['cfspinner_wheel_id']);
  $cfspinnerwheeltheme   = $mysqli->real_escape_string($ajax['cfspinnerwheeltheme']);

  $table=$dbpref.'spinwheel_setting';

  //  print_r($ajax);

  $cfspinnerwheel = trim( $mysqli->real_escape_string( $ajax['cfspinnerwheel'] ) );
  $cfspinnernum = trim( $mysqli->real_escape_string( $ajax['cfspinnernum'] ) );
  $cfspinslicefontsize = trim( $mysqli->real_escape_string( $ajax['cfspinslicefontsize'] ) );
  $cfspinfont = trim( $mysqli->real_escape_string( $ajax['cfspinfont'] ) );
  $cfspinfontstyle = trim( $mysqli->real_escape_string( $ajax['cfspinfontstyle'] ) );
  $cfspinwheeltype = trim( $mysqli->real_escape_string( $ajax['cfspinwheeltype'] ) );
  $cf_spinner_header_Content = trim( $mysqli->real_escape_string( $ajax['cf_spinner_header_Content'] ) );
  $cf_spinner_footer_Content = trim( $mysqli->real_escape_string( $ajax['cf_spinner_footer_Content'] ) );
  $cfspinnerwheelimgurl = trim($mysqli->real_escape_string($ajax['cfspinnerwheelimgurll']));
  $cfspinmailsub = trim($mysqli->real_escape_string($ajax['cfspinmailsub']));
  $cf_spinner_mailerbody = trim($mysqli->real_escape_string($ajax['cf_spinner_mailerbody']));
 $custominputs= $ajax['custominputs'] ;
        $cus_input = [];
        $c_data=json_decode( $custominputs, true );
        $cus_input=$c_data;
      
              $table=$dbpref.'spinwheel_setting';
            $time =date('Y-m-d H:i:s');       
            if($cfspinnerwheeltheme == "cfwheelcolbg")
            {

            $cfslicelabel = $_POST['cfslicelabell'];
            $cfslicelabelcolor = $_POST['cfslicelabelcolorr'];
            $cfslicefontcolor = $_POST['cfslicefontcolorr'];
         
           $new_arr=[];
           for($i=0;$i<count($cfslicelabelcolor);$i++)
           {
             $arr['cfslicelabel']      =$cfslicelabel[$i]; 
             $arr['cfslicelabelcolor'] =$cfslicelabelcolor[$i]; 
             $arr['cfslicefontcolor'] =$cfslicefontcolor[$i]; 
             $new_arr[]=$arr;
           }
     $jsonencodelabel  = json_encode($new_arr);
          }


          if($cfspinnerwheeltheme == "cfwheelimgbg")
          {
          $cfsliceimglabel = $_POST['cfsliceimglabell'];
          $cfsliceimgfontcolor = $_POST['cfsliceimgfontcolorr'];
 
       
         $new_arr=[];
         for($i=0;$i<count($cfsliceimgfontcolor);$i++)
         {
           $arr['cfsliceimglabel']      =$cfsliceimglabel[$i]; 
           $arr['cfsliceimgfontcolor'] =$cfsliceimgfontcolor[$i]; 
  
           $new_arr[]=$arr;
         }
         $jsonencodelabel  = json_encode($new_arr);
        }
  
      $sql="UPDATE `".$table."` SET `cfspinnerwheel`='".$cfspinnerwheel."',`cfspinnernum`='".$cfspinnernum."',`cfspinslicefontsize`='".$cfspinslicefontsize."',`cfspinfont`='".$cfspinfont."',`cfspinfontstyle`='".$cfspinfontstyle."',`cfspinwheeltype`='".$cfspinwheeltype."',`cfspinmainheader`='".$cf_spinner_header_Content."' ,`cfspinmaifooter` ='".$cf_spinner_footer_Content."',`cfslicepricenames`='".$jsonencodelabel."',`cfspinmailsub`='".$cfspinmailsub."',`cf_spinner_mailerbody`='".$cf_spinner_mailerbody."'  WHERE `id`=".$wheelid;

  $return_insert = $mysqli->query( $sql )?1:0;
  if( $return_insert == 1 ){



    $table1=$dbpref."spinner_popup_forminputs";
    $return_delete=$mysqli->query("DELETE FROM `".$table1."` WHERE `cfspinwheelid`=".$wheelid);
 
    $position=1;
    foreach ($cus_input as $key => $value) {
        $this->cfspinner_add_custom_input( $value, $wheelid, $position );
        $position++;
    }
 
    echo json_encode( array( "status" => 1 , "success" => "data updated succefully","wheelid"=>$wheelid  ) );
    die();

  }

}

}


function cfspinner_add_custom_input( $custom , $wheel_id = "" , $position="" )
{
    global $mysqli;
    global $dbpref;
    $table= $dbpref."spinner_popup_forminputs";
    //  print_r($custom);
    

   $custom_place = $mysqli->real_escape_string(  trim(htmlspecialchars( $custom['placeholder']) )   );
    $custom_name = $mysqli->real_escape_string( trim(htmlspecialchars( $custom['name']) ) );
    $custom_type =$mysqli->real_escape_string( trim(htmlspecialchars( $custom['type']) )  );
    $custom_title = $mysqli->real_escape_string( trim(htmlspecialchars( $custom['title']) )  );
    $custom_postion = $mysqli->real_escape_string( trim(htmlspecialchars( $position ) ) );
    $custom_required = $mysqli->real_escape_string( trim(htmlspecialchars( $custom['required']) ) );
    $result = $mysqli->query( "INSERT INTO `".$table."`(`cfspinwheelid`, `name`, `placeholder`, `type`,`title`, `position`, `required`) VALUES (".$wheel_id." ,'".$custom_name."' ,'".$custom_place."' ,'".$custom_type."' ,'".$custom_title."','".$custom_postion."' ,'".$custom_required."' )" );
   
}

      public function updatePriceuser($ajax_price)
      {
        global $mysqli;
        global $dbpref;
        $table= $dbpref.'spinwheelusers';
        $cfspinerwinprize = trim( $mysqli->real_escape_string( $ajax_price['cfspinerwinprize'] ) );
        $getuserid="select id from ".$table." order by id desc";
        $result = $mysqli->query($getuserid);
        $user = $result->fetch_assoc();
        $getuserid = $user['id'];   
        $sql1 = "UPDATE `".$table."`  SET `winprize`='".$cfspinerwinprize."' WHERE `id`=".$getuserid;
        return $mysqli->query($sql1);
     
      }
      function getWheelsCount()
      {
          global $mysqli;
          global $dbpref;
          $table= $dbpref.'spinwheel_setting';

          $qry=$mysqli->query("select count(`id`) as `total_wheels` from `".$table."`");

          $r=$qry->fetch_object();
          return $r->total_wheels;
      }

    function getUserWheelcount($wheelid)

{ 
   global $mysqli;
  global $dbpref;
  $table= $dbpref.'spinwheelusers';
  $wheelid=$mysqli->real_escape_string($wheelid);
  $qry=$mysqli->query("select count(`id`) as `total_user` from `".$table."` where `wheelid`=".$wheelid);

  $r=$qry->fetch_object();
  return $r->total_user;

}




      function getAllWheels($total_wheels=false,$max_limit=false,$page=1)
      {
          global $mysqli;
          global $dbpref;
          $table= $dbpref.'spinwheel_setting';
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
              $search=" and `cfspinnerwheel` like '%".$search."%'";
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
          if($qry->num_rows>0)
          {
              while($data = $qry->fetch_assoc() )
              {
                  $arr[]=$data;
              }   
           
          }
          return $arr;

      }



      function getALLWheelusers($total_wheels=false,$max_limit=false,$page=1,$wheelid)
      {
          global $mysqli;
          global $dbpref;
          $table= $dbpref.'spinwheelusers';
          $page=$mysqli->real_escape_string($page);
          $wheelid=$mysqli->real_escape_string($wheelid);

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
              $search=" and `cfspinuseremail` like '%".$search."%'";
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
      
  $qry=$mysqli->query("select * from `".$table."` where 1 and `wheelid`=".$wheelid.$search." order by ".$order_by.$limit."");

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

      function getSaveSettings($id)
      {
        global $mysqli;
            global $dbpref;
            $table= $dbpref.'spinwheel_setting';
        $id=(int)$id;
         
         $result=$mysqli->query("select * from `".$table."` where `id`=".$id);
        while($row = $result->fetch_assoc()) {
         return $row;


        }
      }


      public function doExportToCSV()
      {
        global $mysqli;
       global $dbpref;
         if (isset($_POST['cfspinner_export_csv'])) {
      
         $wheelid =$mysqli->real_escape_string($_POST['cfspinner_export_csv']);
      
        $table=$dbpref."spinwheelusers";
      
      
          $sql="select * from `".$table."` where `wheelid`='".$wheelid."' order by `id` desc";
      
      
        $qry=$mysqli->query($sql);
      
        $csv_fields=array();
        $csv_fields[] = '#';
        $csv_fields[] = 'Name';
        $csv_fields[] = 'Email';
        $csv_fields[] = 'Prize Name';
      
      
        $output_filename = 'user.csv';
      $output_handle = @fopen( 'php://output', 'w' );
      
      header( 'Cache-Control: must-revalidate, post-check=0, pre-check=0' );
      header( 'Content-Description: File Transfer' );
      header( 'Content-type: text/csv' );
      header( 'Content-Disposition: attachment; filename=' . $output_filename );
      header( 'Expires: 0' );
      header( 'Pragma: public' );
      
      
      fputcsv( $output_handle, $csv_fields );
      
      
        if($qry->num_rows>0)
        {
          $count=0;
          while($r=$qry->fetch_assoc())
          {
            ++$count;
            $outputrray=array($count);
            array_push($outputrray,$r['name']);
            array_push($outputrray,$r['email']);
            array_push($outputrray,$r['winprize']);

      
          
            fputcsv( $output_handle,$outputrray);
          }
        }
      fclose( $output_handle );
      die();
      
      }
      
      }
      function sendmail()
{

    global $mysqli;
global	$dbpref;

	$table=$dbpref."spinwheelusers";
  $table1= $dbpref.'spinwheel_setting';
  
  if (isset($_POST['cfspinsendmail']))
   {
      
    $id =$mysqli->real_escape_string($_POST['cfspinsendmail']);

	$total_query=$mysqli->query("select * from `".$table."` where  `id`='".$id."'");
	if($r=$total_query->fetch_object())
	{
    $wheelid=$r->wheelid;
    $sqlquery=$mysqli->query("select * from `".$table1."` where  `id`='".$wheelid."'");
    if($row=$sqlquery->fetch_object())
    {
    $subject = $row->cfspinmailsub;
   $body=$row->cf_spinner_mailerbody;
    $prize = $r->winprize;
  

    // echo $prize;

      $body= str_ireplace("{winprize}",$prize,$body);
//     echo $body;
// exit;
    $data = array("mailer"=>php, "name"=>$r->name, "email"=>$r->email,'subject'=>$subject,'body'=>$body);
			 $mail=cf_mail($data);
	
       if($mail == '1')
       {
       global $mysqli;
       global $dbpref;
       $table= $dbpref.'spinwheelusers';
     
       $sql1 = "UPDATE `".$table."`  SET `mailstatus`='1' WHERE `id`=".$id;
       return $mysqli->query($sql1);
       }
   
	}
  }

}
return 1;

	}




  }
}