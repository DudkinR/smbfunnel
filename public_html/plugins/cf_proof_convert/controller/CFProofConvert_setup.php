<?php
if(!class_exists('CFProofConvert_setup'))
{
  class CFProofConvert_setup
  {
    var $pref="cfproof_convert_";
    function __construct($arr)
    {
      $this->loader=$arr['loader'];
    }

    function getAllSetups($total_forms=false,$max_limit=false,$page=1)
    {
      global $mysqli;
      global $dbpref;
      $table= $dbpref.$this->pref."setup";
      $page=$mysqli->real_escape_string($page);
      $user_id=$_SESSION['user' . get_option('site_token')];
      $access=$_SESSION['access' . get_option('site_token')];

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
        $search=" and `title` like '%".$search."%'";
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
      if($access=='admin')
      {
        $qry=$mysqli->query("select * from `".$table."` where 1".$search." order by ".$order_by.$limit);
      }
      else
      {
        $qry=$mysqli->query("select * from `".$table."` where `user_id`=".$user_id." ".$search." order by ".$order_by.$limit);
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

    function getTotalImpressions( $setup_id=null )
    {
      global $mysqli;
      global $dbpref;
      $setup_id= $mysqli->real_escape_string( $setup_id );

      $table=$dbpref.$this->pref."setup";
      $user_id=$_SESSION['user' . get_option('site_token')];
      $access=$_SESSION['access' . get_option('site_token')];
      if($access=='admin')
      {
        $qry=$mysqli->query("select `impressions` from `".$table."` where `id`=".$setup_id);
      }
      else
      {
        $qry=$mysqli->query("select `impressions` from `".$table."` where `id`=".$setup_id." and `user_id`=".$user_id);
      }     
      $total_impression=$impression->fetch_assoc();
      return $total_impression['impressions'];
    }

    function getSetupsCount()
    {
      global $mysqli;
      global $dbpref;
      $table= $dbpref.$this->pref.'setup';
      $user_id=$_SESSION['user' . get_option('site_token')];
      $access=$_SESSION['access' . get_option('site_token')];
      if($access=='admin')
      {
        $qry=$mysqli->query("select count(`id`) as `total_setup` from `".$table."`");
      }
      else
      {
        $qry=$mysqli->query("select count(`id`) as `total_setup` from `".$table."` where `user_id`=".$user_id);
      }

      $r=$qry->fetch_object();
      return $r->total_setup;
    }

    function getFormSetup( $setup_id = null )
    {
      global $mysqli;
      global $dbpref;
      $table=$dbpref.$this->pref.'setup';
      $r = $mysqli->query("SELECT `setup` FROM `".$table."` WHERE `id`=".$setup_id );

      if( $r->num_rows > 0)
      {
        $data = $r->fetch_assoc();
        return $data;
      }
      return 0;
    }

    function loadUserSide($config_version){
      require plugin_dir_path( dirname(__FILE__,1) )."/views/notification.php";
    }

    function loadAllSetup($limit=null,$config_version=0,$url=null)
    {
      global $mysqli;
      global $dbpref;
      $url=rtrim($url, '/\\');
      $table=$dbpref.$this->pref.'setup';
      $user_id=$_SESSION['user' . get_option('site_token')];
      $access=$_SESSION['access' . get_option('site_token')];
      if($access=='admin')
      {
        $qry=$mysqli->query("select * from `".$table."` order by `id` desc limit ".$limit.",1");
      }
      else
      {
        $qry=$mysqli->query("select * from `".$table."` where `user_id`=".$user_id." order by `id` desc limit ".$limit.",1");
      }
      if($qry->num_rows>0){
        $r=$qry->fetch_object(); 
        $f_setup=explode(",", $r->funnels);
        $setup_data=json_decode($r->setup);
        if(in_array("all", $f_setup)){
          return $r;
        }
        else{
          $all_funnels=get_funnels();
          $install_url=get_option('install_url');
          foreach ($f_setup as  $f_value) {
            if($f_value!="f" || $f_value!="all"){
              $pages = get_funnel_pages($f_value);
              foreach ($pages as $page){
                $page_url=str_ireplace("@@qfnl_install_url@@",$install_url, $page['url']);
                if($page_url==$url){
                  return $r;
                }                       
              }
            }
          }

          if(!empty($setup_data->page_url)){
          $specific_page_url = explode("\\r\\n",rtrim($mysqli->real_escape_string($setup_data->page_url),"\\r\\n") ); 
            foreach ($specific_page_url as $page_url) 
            {
              $page_url=rtrim($page_url, '/\\');
              if($page_url==$url){
                return $r;
              }
            }
          }
        }
      }else{
        return false;
      }
    }

    function get_all_count(){
      global $dbpref;
      global $mysqli;
      $table=$dbpref.$this->pref.'notification_data';
      $table1=$dbpref.$this->pref.'setup';
      $qry=$mysqli->query("SELECT * FROM `".$table1."`");
      $max_count=[];
      while ($r=$qry->fetch_assoc()) {
        $fake_data=json_decode($r['fake_data'],true);
        $fake_data =!empty($fake_data)?$fake_data:[];
        $setup_data=json_decode($r['setup']);

        $qry1=$mysqli->query("SELECT * FROM `".$table."` WHERE `product_id`=".$setup_data->product_id);
        while ($data=$qry1->fetch_assoc()) {
          // array_push($fake_data, $data);   
        }
        $max_count[]=count($fake_data);
      }
      // print_r($max_count);
      return $max_count;
    }

    function getProofConvertNotification($ajax=null){
      global $dbpref;
      global $mysqli;
      $table=$dbpref.$this->pref.'notification_data';
      $table1=$dbpref.$this->pref.'setup';
      $next=$mysqli->real_escape_string($ajax['next']);
      $data_count=$mysqli->real_escape_string($ajax['count']);
      $setup_count=$mysqli->real_escape_string($ajax['setup_count']);
      $new_next=$mysqli->real_escape_string($ajax['new_next']);
      $currnet_url=$mysqli->real_escape_string($ajax['url']);
      $config_version=$mysqli->real_escape_string($ajax['config_version']);
      $max_count = self::get_all_count();
      if( count($max_count)>=1 )
      {
        $max_count=max($max_count);
      } else{
        $max_count=0;
      }
      $total_setup=self::getSetupsCount();
      $setup_count=intval($setup_count);
      $scount=($setup_count >= $total_setup)?0:$setup_count;
      $r = self::loadAllSetup($scount,$config_version,$currnet_url);
      // if setup data not getting. then send null response with next setup id to get next setup data
      if(empty($r) || $r==false){
        $scount++;
        $next++;
        $ret_data=json_encode(array( "status"=>1, "count"=>$max_count,"setup_count"=>$scount,"totol_setup"=>$total_setup,"s"=>$next));
        echo $ret_data;
        die();
      }
      // setup css
      $setup_css = json_decode( $r->setup_css );
     
      self::addImpressions(array("setup_id"=>$r->id,"cs"=>"l"));
      $text_color=(!empty($setup_css->text_color))?$setup_css->text_color:"";
      $background_color= (!empty($color=$setup_css->background_color))?$setup_css->background_color:"";
      $name_color=(!empty($setup_css->name_color))?$setup_css->name_color:"";
      $address_color=(!empty($setup_css->address_color))?$setup_css->address_color:"";
      $product_title_color=(!empty($setup_css->product_title_color))?$setup_css->product_title_color:"";
      $product_link_color=(!empty($setup_css->product_link_color))?$setup_css->product_link_color:"";
      $times_color=(!empty($setup_css->product_link_color))?$setup_css->times_color:"";
      $custom_css=(!empty($setup_css->custom_css))?$setup_css->custom_css:"";
      
      // setup
      $setup = json_decode( $r->setup );
      $setup_id = $r->id;
      $redirect_url=( !empty($setup->redirect_url) ) ?$setup->redirect_url:"";
      $product_title=$setup->product_title;
      $product_id=$setup->product_id;
      $type =(!empty( $setup->message_type) ) ? $setup->message_type:"r";
      $position=( !empty($setup->position) ) ?$setup->position:"bl";
      $rotative=( !empty($setup->rotative) ) ?$setup->rotative:"yes";
      $theme=( !empty($setup->theme) ) ?$setup->theme:"theme_a";
      $delay_time=( !empty($setup->delay_time) ) ?$setup->delay_time:30;
      $showing_time=( !empty($setup->showing_time) ) ?$setup->showing_time:30;
      $link_text=( !empty($setup->link_text) ) ?$setup->link_text:"Buy Now";
      $add_css=plugin_dir_url(dirname(__FILE__,1))."assets/css/".$theme.".css?v=".$config_version;
        
        if($type=="r"){
          $qry1=$mysqli->query("SELECT * from `".$table."` ORDER BY `id` DESC");
        }
        elseif($type=="f"){
          $qry2=$mysqli->query("SELECT `fake_data` from `".$table1."` WHERE id=".$setup_id);
        }
        elseif($type=="b"){
          $qry1=$mysqli->query("SELECT * from `".$table."` ORDER BY `id` DESC");
          $qry2=$mysqli->query("SELECT `fake_data` from `".$table1."` WHERE id=".$setup_id);
        }else{
          $qry1=$mysqli->query("SELECT * from `".$table."` ORDER BY `id` DESC");
          $qry2=$mysqli->query("SELECT `fake_data` from `".$table1."` WHERE id=".$setup_id);
        }
        $all_datas=[];
        
        //if  proof convert message type is real and both then fetch data
        if($type=="r" || $type=="b"){
          while($qry1_data = $qry1->fetch_assoc())
            {
              $all_datas[]=$qry1_data;
            }
        }
        $send_data=[];
        foreach ($all_datas as $all_key => $all_data) {
          if(isset($all_data['id'])){
            unset($all_data['id']);
          }
          if($product_id==$all_data['product_id']){
            $send_data[]=$all_data;
          }
        }
        //if type is not equal to real then fetch only fake data
        if($type!="r"){
          $fake_data=$qry2->fetch_assoc();
          $all_fake_datas=json_decode($fake_data['fake_data'],true);
          foreach ($all_fake_datas as $f_data) {
              $f_data['time']="s";
            array_push($send_data, $f_data);
          }
        }
    
        if(count($send_data)<=0)
        {
          $scount++;
          $next++;
          $ret_data=json_encode(array( "status"=>1, "count"=>$max_count,"setup_count"=>$scount,"totol_setup"=>$total_setup,"s"=>$next));
          echo $ret_data;
          die();
          
        }
        // echo $min_count." ".$total_setup; 
         if($total_setup >0 ){
            $count=count($send_data);
            $next=intval($next);
            $next_data=($next>=$max_count)?0:$next;
            $new_next=($new_next>=$max_count)?0:$new_next;
            if($data_count!=$max_count){
            $next_data=0;   
            }
          
          $current_time=time();
          //if next value is greater than all notifcation data count then next==0;
          // if()
          // {
            if($next_data>=$count){
              if($new_next>=$count){
                $new_next=0;
              }
              $sdata=$send_data[$new_next];
              $new_next++;
            }else{
              $sdata=$send_data[$next_data];
            }
            $name=!empty($sdata['name'])?$sdata['name']:"Someone";
            $address=!empty($sdata['address'])?$sdata['address']:"Somewhere";
            $email=$sdata['email'];
            if($sdata['time']=="s"){
                $times_ago="Some times ago";
            }
            else{
                $add_time=!empty($sdata['time'])?$sdata['time']:time()-40;
                
                $m_time=$current_time-$add_time;
                $times_ago=null;
                if(is_numeric($m_time)){
                  $value = array(
                    "years" => 0, "days" => 0, "hours" => 0,
                    "minutes" => 0, "seconds" => 0,
                  );
                  if($m_time >= 31556926){
                    $value["years"] = floor($m_time/31556926);
                    $m_time = ($m_time%31556926);
                    if($value['years']==1){
                      $times_ago=$value['years']." year ago";
                    }else{
                      $times_ago=$value['years']." years ago";
                    }
                  }
                  elseif($m_time >= 86400){
                    $value["days"] = floor($m_time/86400);
                    $m_time = ($m_time%86400);
                    if($value['days']==1){
                      $times_ago=$value['days']." day ago";
                    }else{
                      $times_ago=$value['days']." days ago";
                    }
                  }
                  elseif($m_time >= 3600){
                    $value["hours"] = floor($m_time/3600);
                    $m_time = ($m_time%3600);
                    if($value['hours']==1){
                      $times_ago=$value['hours']." hour ago";
                    }else{
                      $times_ago=$value['hours']." hours ago";
                    }
                  }
                  elseif($m_time >= 60){
                    $value["minutes"] = floor($m_time/60);
                    $m_time = ($m_time%60);
                    if($value['minutes']==1){
                      $times_ago=$value['minutes']." minute ago";
                    }else{
                      $times_ago="Some times ago";
                    }
                  }
                  else{
                    $value['seconds']=$m_time;
                    if($value['seconds']==1){
                      $times_ago=$value['seconds']." second ago";
                    }else{
                      $times_ago=$value['seconds']." seconds ago";
                    } 
                  }      
                }
            }
            //proof convert content
            $notifications=$mysqli->real_escape_string($r->notification);
            $notifications=str_ireplace("{name}", "<strong class='cfproof_convert_name' >".$name."</strong>", $notifications);
            $notifications=str_ireplace("{address}", "<strong  class='cfproof_convert_address'>".$address."</strong>", $notifications);
            $notifications=str_ireplace("{product.title}", "<strong class='cfproof_convert_product_title' id='cfproof_convert_product_title' data-setup-id='".$r->id."' data-redirect_url='".$redirect_url."'>".$product_title."</strong></a>", $notifications);
            $notifications=str_ireplace("{times_ago}", "<strong class='cfproof_convert_times_ago'>".$times_ago."</strong>", $notifications);
            $notifications=str_ireplace("{product.link}", "<strong class='cfproof_convert_product_link'  id='cfproof_convert_link'>".$link_text."</strong>", $notifications);

            $notifications = explode("\\r\\n", rtrim( $notifications, "\\r\\n"));
              
            $notification_data="<div>".$notifications[0]."</div>";
            $notification_data.="<div>".$notifications[1]."</div>";
            $notification_data.="<div>".$notifications[2]."</div>";
            $custom_css=".cfproof_convert_chips{color: #".$text_color."; background-color: #".$background_color.";}
                .cfproof_convert_name{color: #".$name_color.";}
                .cfproof_convert_address{color: #".$address_color.";}
                .cfproof_convert_product_title{color: #".$product_title_color.";}
                .cfproof_convert_times_ago {color: #".$times_color.";}
                .cfproof_convert_product_link {color: #".$product_link_color.";}
                .cfproof_convert_times_ago {color: #".$times_color.";}";
            if(strlen($custom_css)>0)
            {
              $custom_css.=$custom_css;
              $custom_css=str_replace('.this-setup', '.cfproof_convert_chips', $custom_css);
            }

            $image=self::get_gravatar_image($email);
            $default = plugin_dir_url(dirname(__FILE__,1)).'assets/image/user.png';
            $output_data="";
            $output_data.= '
            <div class="row">
              <div class="col-sm-2 p-0 m-0">  
                <div class="cfproof_convert_temp_img">
                  <img src="'.$image.'" alt="Person">
                </div>
              </div>
              <div class="col-sm-10 p-0 m-0">
                <div class="cfproof_convert_text">
                  '.$notification_data.'
                </div>
              </div>
            </div>
            <span class="cfproof_convert_closebtn">&times;</span>';
            $prev=$scount;
            if($total_setup>1){
              if( $prev > 0 ){
              $next_data++;
              }
              elseif($prev==0){
                $next_data=$next_data;
              }
              }else{
                $next_data++;
              }
            $scount++;
            $ret_data=json_encode(array("status"=>1,"showing_time"=>(int)$showing_time,"delay_time"=>(int)$delay_time,"css_file"=>$add_css,"custom_css"=>$custom_css,"setup_count"=>$scount,"count"=>$max_count,"s"=>$next_data,"output"=>$output_data,"position"=>$position,"new_next"=>$new_next,"rotative"=>$rotative));
            echo $ret_data;
            die();
        } 
          else{
            $ret_data=json_encode(array("status"=>0));
            echo $ret_data;
            die();
        }
          
    }
    function get_gravatar_image($email=null){
      $email = $email;
      $default = 'mp';
      $size = 40;
      $grav_url = "https://www.gravatar.com/avatar/" . md5( strtolower( trim( $email ) ) ) . "?d=" . urlencode( $default ) . "&s=" . $size;
      return $grav_url;

    }
    function addImpressions($data=null)
    {
      global $mysqli;
      global $dbpref;
      $table= $dbpref.$this->pref."setup";
      $coming_sourse=$mysqli->real_escape_string( $data['cs'] );
      $setup_id=$mysqli->real_escape_string( $data['setup_id'] );
      if($coming_sourse=="a"){
        $qry="UPDATE `".$table."` SET `impressions`=`impressions`+1 WHERE `id`=".$setup_id;
        $r=$mysqli->query($qry)?1:-1;
        if($r){
          $data=self::getFormSetup($setup_id);
          $setup=json_decode($data['setup']);
           $dont_display =  (isset($setup->dont_display_after_click) && $setup->dont_display_after_click=='1')?1:0;
          echo json_encode(array("status"=>1,"dont_display"=>$dont_display));
        }      
      }elseif($coming_sourse=="l"){
          $qry="UPDATE `".$table."` SET `impressions`=`impressions`+1 WHERE `id`=".$setup_id;
          $mysqli->query($qry);      
      }
    }

    function getProofConvertSettingAjax( array $setup_datas )
    {
      global $mysqli;
      global $dbpref;
      $table=$dbpref.$this->pref."setup";
      $setup =[];
      $setup_css=[];
      $setup_fake_data=[];
      $user_id=$_SESSION['user' . get_option('site_token')];
      $access=$_SESSION['access' . get_option('site_token')];

      $title=$mysqli->real_escape_string(trim($setup_datas['cfproof_convert_title']));
      $param=$mysqli->real_escape_string(trim($setup_datas['cfproof_convert_param']));
      $notification=$mysqli->real_escape_string(trim($setup_datas['cfproof_convert_notification']));
      $fake_datas=json_decode($setup_datas['cfproof_convert_fake_data']);
      $funnels =( isset( $setup_datas['cfproof_convert_funnels'] ) )? implode(',', $setup_datas['cfproof_convert_funnels']):"";
      $funnels=$mysqli->real_escape_string($funnels);
      foreach ( $setup_datas['cfproof_convert'] as $setup_key => $setup_data ) {
        $setup[$setup_key]= $mysqli->real_escape_string($setup_data) ;
      }
      foreach ( $setup_datas['cfproof_convert_css'] as $setup_css_key => $setup_css_data ) {
        $setup_css[$setup_css_key]= $mysqli->real_escape_string($setup_css_data) ;
      }
      foreach ($fake_datas as $fake_keys => $fake_data) {
        $setup_fake_inner_data=[];
        foreach ($fake_data as $fake_key => $data) {
          $setup_fake_inner_data[$fake_key]=$mysqli->real_escape_string(trim($data));
          $setup_fake_inner_data['time']=time();
        }
        $setup_fake_data[$fake_keys]=$setup_fake_inner_data;
      }
      $impressions=count($setup_fake_data);
      if(isset($setup['product_id']) && !empty($setup['product_id']) )
      {
        $get_product=get_products($setup['product_id']);
        $setup['product_title']=$get_product[0]['title'];
      }else{
        $setup['product_title']='Product Title';

      }
      if($setup['message_type']=="r"){
        $add_fake = NULL;
      }else{
        $add_fake = json_encode($setup_fake_data);
      }
      $setup=json_encode($setup);
      $setup_css=json_encode($setup_css);
      if( $setup_datas['cfproof_convert_param'] == "save_setup"  )
      {
        $sql = "INSERT INTO `".$table."`(`title`, `setup`,`fake_data`,`setup_css`,`notification`, `funnels`,`impressions`,`user_id`) VALUES ('".$title."' ,'".$setup."','".$add_fake."','".$setup_css."','".$notification."','".$funnels."',".$impressions.",".$user_id.")";
        $return_insert = $mysqli->query( $sql )?1:-1;
                
        if( $return_insert == 1 ){
          $last_id = $mysqli->insert_id;
          echo json_encode( array( "status" => 1 , "success" => "data addedd succefully","setup_id"=>$last_id  ) );
          die();
        }
      }else if( $setup_datas['cfproof_convert_param'] == "update_setup" ){
        $setup_id   = $mysqli->real_escape_string($setup_datas['cfproof_convert_setup_id']);
        $sql="UPDATE `".$table."` SET `title`='".$title."',`setup`='".$setup."', `fake_data`='".$add_fake."', `setup_css`='".$setup_css."',`funnels`='".$funnels."',`notification`='".$notification."' WHERE `id`=".$setup_id;
        $return_insert = $mysqli->query( $sql )?1:-1;
        if( $return_insert == 1 ){
          echo json_encode( array( "status" => 1 , "success" => "data updated succefully","setup_id"=>$setup_id  ) );
          die();
        }
      }
    }
    function deleteSetup($ajax=null){
      global $mysqli;
      global $dbpref;
      $param=$mysqli->real_escape_string($ajax['cfproof_convert_param']);
      $setup_id   = $mysqli->real_escape_string($ajax['id']);
      if( $param == "delete_setup" ){        
        $table=$dbpref.$this->pref."setup";
        $sql="DELETE FROM `".$table."` WHERE `id`=".$setup_id;
          $form_delete = $mysqli->query($sql)?1:-1;
          if($form_delete){
            echo json_encode( array( "status" => 1 , "success" => "data deleted succefully" ));
          die();
        }
      }
    }
  }
}
?>