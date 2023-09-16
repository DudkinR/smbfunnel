<?php

if(!class_exists('CFProduct_review_setting'))
{
    class CFProduct_review_setting
    {
        var $pref="cfproduct_review_";
        function __construct($arr)
        {
          $this->loader=$arr['loader'];
        }

        /*
        Settings 
        */
        function settings( $data )
        {
            //Condition variable
            $vars1 = [ "showld",'showld','showp','shows','showsummary',"showavg",'showsumbox','aapproved','markasread'];
            $vars2 = ['rallowfu',"rfext",'rshowstar','rshowsummary'];
            global $mysqli;
            global $dbpref;
            $table = $dbpref."cfproduct_review_setting";
            $filter_data=['action','type'];
            $rsetting=[];
            $revsetting=[];
            $rtext=[];
            $revtext=[];
            $rstyle=[];
            $formstyle=[];

            if( isset( $data['rsetting'] ) )
            {
                foreach( $vars1 as $rs )
                {
                    if( isset( $data['rsetting'][$rs] ) )
                    {
                        $rsetting[$rs] = true;
                    }else{
                        $rsetting[$rs]=false;
                    }
                }
            }
            if( isset( $data['revsetting'] ) )
            {
                foreach( $vars2 as  $rs )
                {
                    if( isset( $data['revsetting'][$rs] ) )
                    {
                        $revsetting[$rs] = true;
                    }else{
                        $revsetting[$rs]=false;
                    }
                }
            }

            foreach( $data['rtext'] as $index => $dat  )
            {
                if( !in_array( $index, $filter_data ) )
                {
                    $rtext[$mysqli->real_escape_string( $index )] = $mysqli->real_escape_string( $dat );
                }
            }
            foreach( $data['rstyle'] as $index => $dat  )
            {
                if( !in_array( $index, $filter_data ) )
                {
                    $rstyle[$mysqli->real_escape_string( $index )] = $mysqli->real_escape_string( $dat );
                }
            }
            foreach( $data['formstyle'] as $index => $dat  )
            {
                if( !in_array( $index, $filter_data ) )
                {
                    $formstyle[$mysqli->real_escape_string( $index )] = $mysqli->real_escape_string( $dat );
                }
            }
            foreach( $data['revtext'] as $index => $dat  )
            {
                if( !in_array( $index, $filter_data ) )
                {
                    $revtext[$mysqli->real_escape_string( $index )] = $mysqli->real_escape_string( $dat );
                }
            }
            if( !isset( $revtext['rfextesnions']) )
            {
                $revtext['rfextesnions']="";
            }
            $email_subject = $mysqli->real_escape_string( $data['email_subject'] );
            $email_content = $mysqli->real_escape_string( $data['email_content'] );
            $setting = $mysqli->real_escape_string( json_encode( $rsetting ) );
            $text = $mysqli->real_escape_string( json_encode( $rtext ) );
            $style = $mysqli->real_escape_string( json_encode( $rstyle ) );
            $fstyle = $mysqli->real_escape_string( json_encode( $formstyle ) );
            $fsetting = $mysqli->real_escape_string( json_encode( $revsetting ) );
            $ftext = $mysqli->real_escape_string( json_encode( $revtext ) );
            $pr = $mysqli->query("SELECT * FROM `$table`");
            
            if( $pr->num_rows > 0 )
            {
                $mysqli->query("DELETE  FROM `$table`");
            }
            $mysqli->query("INSERT INTO `$table` (`email_subject`,`email_content`,`rsetting`,`rtext`,`rstyle`,`formsetting`,`formstyle`,`formtext`) VALUE ('".$email_subject."','".$email_content."','".$setting."','".$text."','".$style."','".$fsetting."','".$ftext."','".$fstyle."')");
            
            if( $mysqli->affected_rows > 0 )
            {
                return json_encode(array('status'=>1,'message'=>t('Saved changes')));
            }else{
                return json_encode(array('status'=>0,'message'=>t('There is someting wrong please refresh the page.')));
            }
        } 
        
        // Get setup setting
        function getSettings( $only=false )
        {
            global $mysqli;
            global $dbpref;
            $table = $dbpref."cfproduct_review_setting";
            if( $only ){
                $row = $mysqli->query("SELECT `email_content`,`email_subject`,`formtext` FROM `$table`");
            
            }else{
                $row = $mysqli->query("SELECT * FROM `$table`");
            }
            if( $row->num_rows > 0 )
            {
                $r = $row->fetch_assoc();
                return $r;
            }
            return false;          
        }

        // Reset setting
        function resetSettings()
        {
            global $mysqli;
            global $dbpref;
            $table = $dbpref."cfproduct_review_setting";
            $pr = $mysqli->query("SELECT * FROM `$table`");
            if( $pr->num_rows > 0 )
            {
                $mysqli->query("DELETE  FROM `$table`");
            }
            return json_encode(array('status'=>1,'message'=>t('Saved changes')));

        }

        //Add a review
        function addReview( $datas ){
            
            // Extract setting variable variable
            $data = $this->getSettings( );
            if($data)
            {
                foreach( $data as $index=> $dat )
                {
                    if( $index == "email_content" ||$index == "email_subject"  )
                    {
                        ${$index}=$dat;
                    }else{
                        foreach( json_decode($dat) as $in => $d )
                        {
                            if( $in == "customcss" || $in == "rcustomcss"  )
                            {
                                ${$in} = str_ireplace( "\\r\\n", "\r\n",$d );
                            }
                            ${$in} = $d;
                        }
                    }
                }  
            }
            else{
                $vars = [
                    /*********** Review variable ***********/ 
                    "showld"=>true,'showld'=>true,"aapproved"=>false,'markasread'=>false,'showp'=>true, 'shows'=>true,'showsummary'=>true,"showavg"=>true,'showsumbox'=>true,
                    
                    'headertext'=>'Customers Ratings And Reviews','ratingtext'=>'Average Rating','readmore'=>'Read More','readless'=>'Read Less','summaryletter'=>-1,'rateproducttext'=>'Rate Product','reviewstext'=>'Reviews',
                    'pnext'=>'Next &raquo;','pprev'=>'&laquo; Prev',
            
                    'starcolor'=>'ffc107','star5'=>'4CAF50','star4'=>'2196F3','star3'=>'00bcd4','star2'=>'ff9800','star1'=>'f44336','rprocolor'=>'000000','rprobackcolor'=>'transparent',
                    'readmorecolor'=>'007bff','summarycolor'=>'212529','summaryfize'=>'13','pageac'=>'4CAF50','pagehoc'=>'dddddd','pagecolor'=>'ffffff','avgratingcolor'=>'f1f1f1','rwidth'=>'80','boxposition'=>'c','customcss'=>'',
            
                    /********** Form setting == general setting ==  variable ***************/ 
                    'rallowfu'=>true,'rfsize'=>5,'rmaxfile'=>5,"rfext"=>true,'rshowstar'=>true,'rshowsummary'=>true,
                    
                    'rfextesnions'=>'.png, .jpg, .jpeg, .gif',"rboxposition"=>'c',"rallowall"=>"all",'rupload_text'=>'Upload File','rheadertext'=>'Rate this product','rlabeltext'=>'Enter Summary','rdeletebtn_text'=>'Delete','rsubbtn_text'=>'Submit Review','rsum_place'=>'Share details of your own experience at this place','rsum_length'=>2000,
                    
                    'rfrmwidth'=>'35','rhbackcolor'=>'ffffff','rhcolor'=>'000000','rstarcolor'=>'ffc700','rstarhover'=>'deb217','rstardefault'=>'cccccc','rlabelc'=>'000000','rfooterc'=>'fffffff','rsub_backcolor'=>'007bff',
                    'rcustomcss'=>'','rsub_color'=>'ffffff',
                    'email_content'=>'<p>Hi {name},</p><p>Thanks for the review.</p><p><strong>Please visit the below URL to verify yourself</strong>.</p><p>{verification_url}</p><p>Thanks</p>',
                    'email_subject'=>'Thanks for the review'
                ];
                //Parse all variable
                foreach($vars as $ind=> $var)
                {
                    ${$ind} = $var;
                }
            } 

            $dir     = CFPRODUCT_REV_PLUGIN_DIR_PATH."upload/";
            $dir_url = CFPRODUCT_REV_PLUGIN_URL."upload/";
            
            global $mysqli;
            global $dbpref;
            $table    = $dbpref."cfproduct_review_records";
            if( isset( $datas['rate'] ) )
            {
                $rating   = $mysqli->real_escape_string( trim( $datas['rate'] )); 
            }else{
                $rating = 1;
            }

            if( isset( $datas['summary'] ) )
            {
                $summary  = $mysqli->real_escape_string( trim( $datas['summary'] )); 
                
                if( strlen( $summary ) > $rsum_length ){
                    echo json_encode( array('status'=>0, 'message'=> t('Only ${1} character allowed',[$rsum_length])) );
                    die();
                }
            }else{
                $summary = '';
            }

            $pid      = $mysqli->real_escape_string( trim( $datas['pid'] ));
            $pid      = cf_enc($pid,'decrypt'); 
              
            // i have set this session on review_shortcode file on line no 110
            if( has_session('cfprrev_mid') )
            {
                $mdata =get_session('cfprrev_mid');
            }else{
                $mid="all";
            }
            $mid=$mdata['mid'];
            
            // Check the if admin enable option of adding reivew setting
            // anybody can add the review  
            // only logged in user can add the review
            
            if( $mid == "all" ){
                $name     = $mysqli->real_escape_string( trim( $datas['name'] ));
                $email    = $mysqli->real_escape_string( trim( $datas['email'] ));
            }else{

                $name     = $mysqli->real_escape_string($mdata['name']);
                $email    = $mysqli->real_escape_string($mdata['email']);
            }
            $email = filter_var( $email, FILTER_SANITIZE_EMAIL  );
            $name  = filter_var( $name, FILTER_SANITIZE_STRING );
            if( !filter_var( $email, FILTER_VALIDATE_EMAIL ) )
            {
                echo json_encode( array('status'=>0, 'message'=> t('Please enter valid email')) );
                die();
            }
            if( empty( $name ) )
            {
                echo json_encode( array('status'=>0, 'message'=> t('Please enter valid name')) );
                die();
            }
            if( isset( $_FILES['media'] ) )
            {
                $files    = $_FILES['media'];  
            }else{
                $files = [];
            }
            $pdata    = get_product($pid);
            $ptitle   = $mysqli->real_escape_string($pdata['title']);

            if(isset( $aapproved ) && $aapproved ) $approved = 1;
            else $approved = 0;

            if(isset( $markasread ) && $markasread ) $markread = 1;
            else $markread = 0;

            $medias   = [];
            $up=[];
            if( $rallowfu &&  count( $_FILES['media']['name'] ) > $rmaxfile )
            {
                echo json_encode( array('status'=>0, 'type'=>'file', 'message'=> "Sorry! You can upload only ".$rmaxfile." file(s)") );
                die();
            }elseif( count( $files ) > 0 ){

                foreach( $files['name'] as $in => $file )
                {
                    if( $files['size'][$in] > ((int)$rfsize*1000000) )
                    {
                        echo json_encode( array('status'=>0, 'type'=>'file', 'message'=> "Sorry! You can upload only ".$rmaxfile."MB per file size") );
                        die();
                    }
                    $media['name']     = $file;
                    $media['type']     = $files['type'][$in];
                    $media['tmp_name'] = $files['tmp_name'][$in];
                    $media['error']    = $files['error'][$in];
                    $media['size']     = $files['size'][$in];
                    $medias[]          = $media;   
                }
                if( $medias[0]['name'] )
                {
                    foreach( $medias as $in =>  $m )
                    {
                        $extension = pathinfo($m['name'],PATHINFO_EXTENSION);
                        $filename  = time().$in."_".$mid.".".$extension;
                        $up[]      = [ 'name'=> $dir_url.$filename, "ext"=>$extension, "type"=> $m['type'] ];
                        move_uploaded_file( $m['tmp_name'] , $dir.$filename );
                    }
                }
            }

            $time = date('Y-m-d H:i:s', time() );
            $upm  = $mysqli->real_escape_string( json_encode( $up ) ); 
            $do_action  = $mysqli->real_escape_string( $datas['do_action'] ); 
            if( $do_action=="add" )
            {
                $check = $mysqli->query("SELECT `id` FROM `$table` WHERE `email`='$email' AND `product_id`='$pid'");
                if($check->num_rows>0)
                {
                    echo json_encode( array('status'=>0, 'message'=> 'You are already given the review') );
                    die();
    
                }
                $sql  = "INSERT INTO `".$table."`(`product_id`, `product_title`, `name`, `email`, `rating`, `summary`, `media`, `readed`, `approved`, `added_on`) VALUES ('".$pid."','".$ptitle."','".$name."','".$email."','".$rating."','".$summary."','".$upm."','".$markread."','".$approved."','".$time."')";
                $mysqli->query($sql);
                if( isset( $datas['review_url'] ) )
                {
                    $lastid = cf_enc($mysqli->insert_id);
                    $revurl = $mysqli->real_escape_string( $datas['review_url'] );
                    $revur = parse_url($revurl);
                    if( isset( $revur['query'] ) )
                    {
                        $newu = $revur['scheme']."://".$revur['host'].$revur['path'];
                        $query = explode("&",$revur['query']);
                        $final=[];
                        foreach( $query as $i => $qr )
                        {
                            if( stristr($qr,"cfprorev_open_revmodal") )
                            {
                                unset($query[$i]);
                            }else{
                                $final[$i]=$qr;
                            }
                        }
                        if( count( $final ) )
                        {
                            $new_q = implode("&",$final);
                            $newrevurl = $newu."?".$new_q."&cfpro_rev_verify_email=".$lastid;
                        }else{
                            $newrevurl =  $newu."?cfpro_rev_verify_email=".$lastid;
                        }
                    }else{
                        $newrevurl = $revurl."?cfpro_rev_verify_email=".$lastid;
                    }
                    $email_data  = $this->replaceshortcode( $name, $email, $newrevurl, $email_subject, $email_content );
                    $edata=[
                        "",
                        "name"=>$name,
                        "email"=>$email,
                        "subject"=>$email_data['subject'],
                        "body"=>$email_data['body']
                    ];
                    try{
                        try{
                            cf_mail($edata);
                        }catch(Exception $e)
                        {
                            echo json_encode( array('status'=>0, 'message'=> 'Review not added please refresh the page') );
                            die();
                        }
                        $send_email="We have sent you a verification mail. Please check your email.";
                        if( $mysqli->affected_rows > 0)
                        {
                            $this->addAeverageRating($pid);
                            echo json_encode( array('status'=>1, 'action'=>'add', 'message'=>'added','email'=>$send_email ) );
                        }else{
                            echo json_encode( array('status'=>0, 'message'=> 'Review not added please refresh the page') );
                        }   
                    }catch( Exception $e ){
                        echo json_encode( array('status'=>0, 'message'=> 'Review not added. please contact site owner') );
                    }
                    die();
                }
                if( $mysqli->affected_rows > 0)
                {
                    $this->addAeverageRating($pid);
                    echo json_encode( array('status'=>1,'action'=>'add', 'message'=>'added','email'=>'no' ) );
                }else{
                    echo json_encode( array('status'=>0,  'message'=> 'Review not added please refresh the page') );
                }
                die();
            }
            
            if( $do_action == "update" )
            {
                $rid  = cf_enc( $mysqli->real_escape_string( $datas['rid'] ),'decrypt');
                $sq = $mysqli->query("SELECT `id` FROM `$table` WHERE `id`=$rid");
                if( $sq->num_rows > 0 )
                {
                    $sql  = "UPDATE `$table` SET `summary`='$summary',`rating`='$rating',`media`='$upm' WHERE `id`=$rid";
                    $mysqli->query( $sql ); 
                    echo json_encode( array('status'=>1, 'action'=>'update') );
                }else{
                    echo json_encode( array('status'=>0, 'action'=>'update' ) );
                }
            }
            
            die();
        }

        //Add Average Rating
        function addAeverageRating( $pid )
        {
            global $mysqli;
            global $dbpref;
            global $app_variant;
            $table1 = $dbpref."cfproduct_review_records";
            $table2 = $dbpref."all_products";
            $q2 = $mysqli->query("SELECT AVG(`rating`) as `average_rating` FROM `$table1` WHERE `product_id`='$pid'");
            if(  $mysqli->affected_rows > 0 && $q2->num_rows > 0 )
            {
                $query="";
                if( $app_variant == "shopfunnels" )
                {
                    $query=" OR `parent_product`='$pid'";

                }
                $r2 = $q2->fetch_object();
                $total_rating = $r2->average_rating;
                $mysqli->query("UPDATE `$table2` SET `average_rating`='$total_rating' WHERE `id`='$pid' $query");
                return true;
            }

        }
        //Replace Shortcode
        function replaceshortcode( $name,$email,$url,$subject,$content)
        {
            $sub="";
            $cont="";
            $shortcodes=['{name}','{email}','{verification_url}'];
            $shortcodes_v=['{name}'=>$name,'{email}'=>$email,'{verification_url}'=>$url];

            foreach($shortcodes as $shortcode)
            {
                $sub =$subject;
                if( stristr( $sub,$shortcode ) )
                {
                    $sub=str_ireplace( $shortcode, $shortcodes_v[$shortcode] , $sub );
                }
                $subject=$sub;
                $cont =$content;
                if( stristr( $cont,$shortcode ) )
                {
                    $cont=str_ireplace( $shortcode, $shortcodes_v[$shortcode] , $cont );
                }
                $content=$cont;
            }
            return [ 'subject'=>$sub,'body'=>$content ];
        }
         //To insert addLike into the database table.. 
        function addLike( $data )
        {
            global $mysqli;
            global $dbpref;
            $table = $dbpref."cfproduct_review_likes";
            $pid   = $mysqli->real_escape_string( trim( $data['pid'] ));
            $pid = cf_enc($pid,'decrypt');

            $rid   = $mysqli->real_escape_string( trim( $data['rid'] ));
            $rid = cf_enc($rid,'decrypt');
             if( has_session('cfprrev_mdata')===false )
             {
                echo json_encode( array( "status"=>0, "message"=>"Please login first") );
                die();
             }
             $mdata = get_session('cfprrev_mdata');
             $name  = $mdata['name'];
             $email = $mdata['email'];
             $mid   = $mdata['mid'];
            $liked_ex =  $this->checkLikeStatus( $mid,$pid,$rid,1 );
            if( $liked_ex == 1 )
            { 
                $sql  = "DELETE FROM `".$table."` WHERE `pid`= $pid AND `rid`= $rid AND `mid`=".$mid;
                $mysqli->query( $sql );
                if( $mysqli->affected_rows == 1 ){
                    $stl = $this->showTotalLikes($pid,$rid);
                    echo json_encode( array( "status"=> 1,'type' =>'remove', 'like'=>$stl['likes'], 'dislike'=> $stl['dislikes'], "message"=>"Removed Like Successfully") );
                    die();
                }
            }
            elseif( $liked_ex == -1 )
            { 
                $sql  = "UPDATE `".$table."` SET `rlike`='1' WHERE `pid`=$pid AND `rid`= $rid AND `mid`=$mid";
                $mysqli->query( $sql );
                if( $mysqli->affected_rows == 1 ){
                    $stl = $this->showTotalLikes($pid,$rid);
                    echo json_encode( array( "status"=> 1, 'type' =>'update', 'like'=>$stl['likes'], 'dislike'=> $stl['dislikes'], "message"=>"Removed Like  Successfully") );
                    die();
                }
            }
            else
            {
                $sql="INSERT INTO `$table`(`name`, `email`, `mid`, `pid`,`rid`, `rlike`) VALUES ('".$name."','".$email."','".$mid."','".$pid."','".$rid."',1)";
                $mysqli->query( $sql );
                if( $mysqli->affected_rows == 1 ){
                    $stl=$this->showTotalLikes($pid, $rid );
                    echo json_encode( array( "status"=>1, 'type' =>'add', 'like'=>$stl['likes'], 'dislike'=> $stl['dislikes'], "message"=>"Added Like Successfully") );
                    die();
                }
            }
        }

        //To insert addDisLike into the database table.. 
        function addDisLike( $data )
        {
            global $mysqli;
            global $dbpref;
            $table = $dbpref."cfproduct_review_likes";
            $pid   = $mysqli->real_escape_string( trim( $data['pid'] ));
            $pid = cf_enc($pid,'decrypt');

            $rid   = $mysqli->real_escape_string( trim( $data['rid'] ));
            $rid = cf_enc($rid,'decrypt');
             if( has_session('cfprrev_mdata')===false )
             {
                echo json_encode( array( "status"=>0, "message"=>"Please login first") );
                die();
             }
             $mdata = get_session('cfprrev_mdata');
             $name  = $mdata['name'];
             $email = $mdata['email'];
             $mid   = $mdata['mid'];

            $liked_ex =  $this->checkLikeStatus( $mid,$pid,$rid,-1 );
            if( $liked_ex == -1 )
            { 
                $sql  = "DELETE FROM `".$table."` WHERE `pid`= $pid AND `rid`= $rid AND `mid`=".$mid;
                $mysqli->query( $sql );
                if( $mysqli->affected_rows == 1 ){
                    $stl = $this->showTotalLikes($pid, $rid );
                    echo json_encode( array( "status"=> 1,'type' =>'remove', 'like'=>$stl['likes'], 'dislike'=> $stl['dislikes'], "message"=>"Removed Dislike Successfully") );
                    die();
                }
            }
            elseif( $liked_ex == 1 )
            { 
                $sql  = "UPDATE `".$table."` SET `rlike`=-1 WHERE `pid`=$pid AND `rid`= $rid AND  `mid`=$mid";
                $mysqli->query( $sql );
                if( $mysqli->affected_rows == 1 ){
                    $stl = $this->showTotalLikes($pid, $rid);
                    echo json_encode( array( "status"=> 1, 'type' =>'update', 'like'=>$stl['likes'], 'dislike'=> $stl['dislikes'], "message"=>"Added Dislike  Successfully") );
                    die();
                }
            }
            else
            {
                $sql="INSERT INTO `$table`(`name`, `email`, `mid`, `pid`,`rid`, `rlike`) VALUES ('".$name."','".$email."','".$mid."','".$pid."','".$rid."',-1)";
                $mysqli->query( $sql );
                if( $mysqli->affected_rows == 1 ){
                    $stl=$this->showTotalLikes($pid, $rid );
                    echo json_encode( array( "status"=>1, 'type' =>'add', 'like'=>$stl['likes'], 'dislike'=> $stl['dislikes'], "message"=>"Added Dislike Successfully") );
                    die();
                }
            }
        }//end of addDisLike function 
        
        //this function is used to check the status of like .. 
        function checkLikeStatus( $mid, $pid,$rid, $type=1 )
        {
            global $mysqli;
            global $dbpref;
            $table = $dbpref."cfproduct_review_likes";
            if( empty( $mid ) || $mid === false )
            {
                return false;
            }
            $sql = "SELECT `rlike` FROM `".$table."` WHERE `mid`=$mid AND `rid`=$rid AND  `pid`=".$pid;
            $result = $mysqli->query( $sql );
            if( $result->num_rows > 0 )
            {
                $data = $result->fetch_assoc();
                return $data['rlike'];
            }
            return false;
        }
        //this function is used to check the status of like .. 
        function checkLikeStatusF( $mid, $pid,$rid, $type=1 )
        {
            global $mysqli;
            global $dbpref;
            $table = $dbpref."cfproduct_review_likes";
            if( empty( $mid ) || $mid === false )
            {
                return false;
            }
            // for like
            if( $type==1 )
            {
                $sql = "SELECT `rlike` FROM `".$table."` WHERE `mid`=$mid AND `rid`=$rid AND `rlike` = 1 AND `pid`=".$pid;
            }
            //for dislike
            elseif( $type == -1 )
            {
                $sql = "SELECT `rlike` FROM `".$table."` WHERE `mid`=$mid AND `rid`=$rid AND  `rlike` = -1 AND `pid`=".$pid;
            }
            $result = $mysqli->query( $sql );
            if( $result->num_rows > 0 )
            {
                $data = $result->fetch_assoc();
                return $data['rlike'];
            }
            return false;
        }
        //Show total Likes
        function showTotalLikes( $pid, $rid )
        {
            global $mysqli;
            global $dbpref;
            $table = $dbpref."cfproduct_review_likes";
            $sql= "SELECT count(*) as likes FROM `".$table."` WHERE `pid` = $pid  AND `rid` = $rid  AND `rlike` = 1";
            $result  = $mysqli->query( $sql );
            if( $result->num_rows > 0 )
            {
                $r = $result->fetch_assoc();
                $dislike = $this->showTotalDisikes($pid ,$rid);
                return array('likes'=>$r['likes'],'dislikes'=>$dislike);
            }
        }
        //Show total Dislike
        function showTotalDisikes($pid,$rid)
        {
            global $mysqli;
            global $dbpref;
            $table = $dbpref."cfproduct_review_likes";
            $sql = "SELECT count(*) as dislikes FROM `".$table."` WHERE `pid` = $pid AND `rid` = $rid AND `rlike` = -1";
            $result  = $mysqli->query( $sql );
            if( $result->num_rows > 0 )
            {
                $r = $result->fetch_assoc();
                return $r['dislikes'];
            }
        }
        function deleteReview( $data )
        {
            global $mysqli;
            global $dbpref;
            $rid = cf_enc($mysqli->real_escape_string( $data['rid'] ),"decrypt");
            $table = $dbpref."cfproduct_review_records";
            $table1 = $dbpref."cfproduct_review_likes";
            
            $sql = "DELETE FROM `$table` WHERE `id` = $rid";
            $sql1 = "DELETE FROM `$table1` WHERE `rid` = $rid";
            $mysqli->query( $sql );
            if( $mysqli->affected_rows > 0 )
            {
                $mysqli->query( $sql1 );
                echo json_encode( array( "status"=>1, "message"=>"reveiw removed Successfully") );
            }else{
                echo json_encode( array( "status"=>0,  "message"=>"review not removed Successfully") );
            }


        }
    }
}
?>