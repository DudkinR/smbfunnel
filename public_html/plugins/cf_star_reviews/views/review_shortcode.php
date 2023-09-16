<?php
$random = bin2hex(random_bytes(5));
$cur_url = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
$cur_ur = parse_url($cur_url);
if( isset( $cur_ur['query'] ) )
{
    $newrevurl = $cur_url."&cfprorev_open_revmodal=".$random;
    $newrevurl1 = $cur_url."&cfprorev_open_revlike=".$random;
    $newrevurl2 = $cur_url."&cfprorev_do_login=".$random;
}else{
    $newrevurl = $cur_url."?cfprorev_open_revmodal=".$random;
    $newrevurl1 = $cur_url."?cfprorev_open_revlike=".$random;
    $newrevurl2 = $cur_url."?cfprorev_do_login=".$random;
}

$login_url = get_login_action_url( $funnel_id,$newrevurl,true );
$login_url2 = get_login_action_url( $funnel_id,$newrevurl2,true );

/*Setting data*/
if( $settings )
{
    // Extract all variable
    foreach($settings as $index=> $data)
    {
        if( $index == "email_content" ||$index == "email_subject"  )
        {
            ${$index}=$data;
        }else{
            foreach( json_decode($data) as $in => $d )
            {
                if( $in == "customcss" || $in == "rcustomcss"  )
                {
                    ${$in} = str_ireplace( "\\r\\n", "\r\n",$d );
                }
                ${$in} = $d;
            }
        }
        
    }   
}else{

    $vars = [
        /*********** Review variable ***********/ 
        "showld"=>true,'showld'=>true,"aapproved"=>false,'markasread'=>false,'showp'=>true, 'shows'=>true,'showsummary'=>true,"showavg"=>true,'showsumbox'=>true,
        
        'headertext'=>'Customers Ratings And Reviews','ratingtext'=>'Average Rating','readmore'=>'Read More','readless'=>'Read Less','summaryletter'=>-1,'rateproducttext'=>'Rate Product','reviewstext'=>'Reviews',
        'pnext'=>'Next &#187;','pprev'=>'&#171; Prev',

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

$newx = str_ireplace(".","",$rfextesnions);
$starcolor=strtoupper($starcolor);
if( $boxposition == "c" ) $boxpos = "margin:auto;";
elseif( $boxposition == "l" ) $boxpos = "margin-right:auto;";
elseif( $boxposition == "r" ) $boxpos = "margin-left:auto;";

if( $rboxposition == "c" ) $rboxpos = "margin:auto;";
elseif( $rboxposition == "r" ) $rboxpos = "margin-right:0;";
elseif( $rboxposition == "l" ) $rboxpos = "margin-left:0;";

if( isset( $_GET['cfpro_rev_verify_email'] ) )
{
    $lastid = cf_enc( $mysqli->real_escape_string($_GET['cfpro_rev_verify_email']) , 'decrypt' );
    $mysqli->query( "UPDATE `".$table."` SET `approved`=1 WHERE `id`=".$lastid );
}
if( is_member_loggedin($funnel_id) )
{
    
    $mdata  = get_current_member($funnel_id);
    $midd   = $mdata['id'];
    $memail  = $mdata['email'];
    if( empty( $mdata['name'] ) )
    {
        $exf = json_decode( $mdata['exf'] );
        if( isset( $exf->firstname ) )
        {
            $mname  = $exf->firstname;
            if( isset( $exf->lastname ) )
            {
                $mname   .= " ".$exf->lastname;
            }
        }
    }else{
        $mname   = $mdata['name'];
    }
    set_session('cfprrev_mdata',array('mid'=>$midd,'email'=>$memail,'name'=>$mname));
}else{
    $midd  = '';
    $memail = '';
    $mname  = '';
}

// check rating between half and full
if     ( $avg_rating > 4.5 && $avg_rating < 5) $new_rating = 4.5;
elseif ( $avg_rating > 3.5 && $avg_rating < 4) $new_rating = 3.5;
elseif ( $avg_rating > 2.5 && $avg_rating < 3) $new_rating = 2.5;
elseif ( $avg_rating > 1.5 && $avg_rating < 2) $new_rating = 1.5;
elseif ( $avg_rating > 0.5 && $avg_rating < 1) $new_rating = 0.5;
else    $new_rating=$avg_rating;


if( $rallowall=="all" || $rallowall=="allver" ){ 
    set_session('cfprrev_mid',array('mid'=>'all','email'=>''));
}else{
    set_session('cfprrev_mid',array('mid'=>$midd,'email'=>$memail,'name'=>$mname));
}
?>
<style>

    .cfpro-rev-pagination a.active {
    background-color: <?="#".$pageac?>;
    color: <?="#".$pagecolor?>;
    border: 1px solid <?="#".$pageac?>;
    }
    .cfpro-rev-bar-container{
        background-color: <?="#".$avgratingcolor ?>;
    }
    .cfpro-rev-all-rating-con{
        <?=$boxpos?>
    }  
    @media  ( min-width:576px ) {
        .cfpro-review-rating .modal-dialog{
        <?=$rboxpos?>
        max-width: <?=$rfrmwidth."% !important;"; ?>
    }  
    }
    .cfpro-rev-rating-c > input:checked ~ label {
    color:<?="#".$rstarcolor?>;    
    }
    .cfpro-rev-rating-c:not(:checked) > label:hover,
    .cfpro-rev-rating-c:not(:checked) > label:hover ~ label {
        color: <?="#".$rstarhover?>;  
    }
    .cfpro-rev-rating-c > input:checked + label:hover,
    .cfpro-rev-rating-c > input:checked + label:hover ~ label,
    .cfpro-rev-rating-c > input:checked ~ label:hover,
    .cfpro-rev-rating-c > input:checked ~ label:hover ~ label,
    .cfpro-rev-rating-c > label:hover ~ input:checked ~ label {
        color: <?="#".$rstarhover?>;
    }   
    .cfpro-rev-rating-c:not(:checked) > label {
     color:<?="#".$rstardefault?>;
    }
    
    @media screen and (min-width:1000px)
    {
        .cfpro-rev-all-rating-con{
            max-width:<?=$rwidth."%"; ?>;
            <?=$boxpos?>
        }     
    }

.cfpro-rev-pagination a:hover:not(.active) {background-color: <?="#".$pagehoc?>;}
</style>
<style>
    <?php
    echo str_ireplace(".this-form",".cfprov-rev-cus",str_ireplace("\\r\\n","",$customcss));
    ?>
     <?php
    echo str_ireplace(".this-form",".cfprov-rev-fcus",str_ireplace("\\r\\n","",$rcustomcss));
    ?>
</style>
<div class="cfpro-rev-all-rating-con py-3 w-100" id="customer_review_and_ratings">
    <input type="hidden" id="cfpr-rev-product--id-<?=$id?>" value="<?=$id?>">
    <?php if( is_member_loggedin( $funnel_id ) ) { ?>
        <input type="hidden" id="cfpro-members-login" value="true">
    <?php } else { ?>
        <input type="hidden" id="cfpro-members-login" value="false">
    <?php } ?>
    <?php if($showavg) { ?>
    <p class="cfpro-rev-rating-head"><?=$headertext; ?></p>
    <div class="d-flex">
        <div class="cfpro-rev-ave-rating" tabindex="-1">
            <div class="cfpro-rev-ave-rat px-2">
                <p class="pt-1 pb-0 mb-0 cfpro-rev-ave-text"><?=$ratingtext; ?></p>
                <div class="cfpro-rev-ave-rat-int"><?=$avg_rating;   ?></div>
                <span class="cfpro-rev-my-rating"></span>
                <script>
                $(".cfpro-rev-my-rating").starRating({
                initialRating: <?=$new_rating ?>,
                disableAfterRate: false,
                starShape: 'rounded',
                readOnly:true,
                disableAfterRate:false,
                ratedColor:'slategray',
                activeColor: '<?= "#".$starcolor; ?>',
                useGradient: "<?=( trim($starcolor) != "FFC107") ? false : true; ?>"
                });
                </script>
                <div class="cfpro-rev-total-reve"><?=$totalreviewer; ?> <?=$reviewstext; ?></div>
            </div>
        </div>
        <div class="cfpro-rev-all-rating px-2">
            <div class="w-100">
                <div class="d-flex">
                    <div class="cfpro-rev-left-aver col-" >
                        <div >5</div>
                    </div>
                    <div class="cfpro-rev-ave-middle col-">
                        <div class="cfpro-rev-bar-container">
                            <div class="cfpro-rev-bar-5 cfpro-rev-barss" style="width:<?=$fiveStarRatingPercent ?>;background-color: <?="#".$star5 ?> !important"></div>
                        </div>
                    </div>
                </div>
                <div class="d-flex">
                    <div class="cfpro-rev-left-aver col-" >
                        <div >4</div>
                    </div>
                    <div class="cfpro-rev-ave-middle col-">
                        <div class="cfpro-rev-bar-container">
                            <div class="cfpro-rev-bar-4 cfpro-rev-barss" style="width:<?=$fourStarRatingPercent ?>;background-color: <?="#".$star4 ?> !important"></div>
                        </div>
                    </div>
                </div>
                <div class="d-flex">
                    <div class="cfpro-rev-left-aver col-" >
                        <div >3</div>
                    </div>
                    <div class="cfpro-rev-ave-middle col-">
                        <div class="cfpro-rev-bar-container">
                            <div class="cfpro-rev-bar-3 cfpro-rev-barss" style="width:<?=$threeStarRatingPercent ?>;background-color: <?="#".$star3 ?> !important"></div>
                        </div>
                    </div>
                </div>
                <div class="d-flex">
                    <div class="cfpro-rev-left-aver col-" >
                        <div >2</div>
                    </div>
                    <div class="cfpro-rev-ave-middle col-">
                        <div class="cfpro-rev-bar-container">
                            <div class="cfpro-rev-bar-2 cfpro-rev-barss" style="width:<?=$twoStarRatingPercent ?>;background-color: <?="#".$star2 ?> !important"></div>
                        </div>
                    </div>
                </div>
                <div class="d-flex">
                    <div class="cfpro-rev-left-aver col-" >
                        <div >1</div>
                    </div>
                    <div class="cfpro-rev-ave-middle col-">
                        <div class="cfpro-rev-bar-container">
                            <div class="cfpro-rev-bar-1 cfpro-rev-barss" style="width:<?=$oneStarRatingPercent ?>;background-color: <?="#".$star1 ?> !important"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <hr>
    <?php } ?>
    <div class="cfpro-rev-reviews-container">
        <div class="cfpro-rev-rating-head cfpro-rev-rating-head-f text-end" tabindex="-1">
        <?php
         if(is_member_loggedin( $funnel_id ) || $rallowall=="all"  || $rallowall=="allver"  ){  ?>
            <button data-openId="<?=$random ?>" class="btn border bg-transparent font-weight-bold cfpro-rev-modal-login-myBtn" style="background-color: <?="#".$rprobackcolor ?> !important ;color: <?="#".$rprocolor ?> !important;"> <?=$rateproducttext ?></button>
        <?php } else{ ?>
            <button data-openId="<?=$random ?>" data-url='<?=$login_url?>' class="btn border bg-transparent font-weight-bold cfpro-rev-send-to-login" style="background-color: <?="#".$rprobackcolor ?> !important ;color: <?="#".$rprocolor ?> !important;"> <?=$rateproducttext ?></button>
        <?php } ?>
        </div>
        <?php if($showsumbox) { ?>
            <div class="px-2 ">
                <?php
                if( $row ->num_rows > 0 )
                {
                    $rev = 0;
                    while( $r = $row_all->fetch_assoc() )
                    {
                            $rev++;
                            $name   = $r['name'];
                            $rid    = $r['id'];
                            $email  = $r['email'];
                            $rating = $r['rating'];
                            $review = nl2br($r['summary']);
                            $media  = $r['media'];
                            $mediac = json_decode( $media , true );
                            $mcount = count( $mediac );
                            $image  = self::get_gravatar_image($email);
                            $date   = date("d-M-Y H:i",strtotime($r['added_on']));
                            ?>
                            <div class="d-flex" id="cfpro-rev-r-container-<?= cf_enc($rid); ?>">
                            <div class="cfpro-rev-r-i py-2 pt-3">
                                <div class="cfpro-rev-img"> 
                                    <img src="<?= $image; ?>" classs="img-thumbnail">
                                </div>
                            </div>
                            <div class="cfpro-rev-r-summery pl-2 w-100" >
                                <div class="d-flex justify-content-between" >
                                   <span class="cfpro-rev-r-name"> <span><?=$name; ?></span><time class="pl-1"><?= $date; ?></time> </span>
                                   <?php
                                    if(is_member_loggedin( $funnel_id ) && $email== $memail ){  ?>
                                   <span><i class='fas cfpro-rev-edit-reivew fa-ellipsis-v' data-openId="<?=$rid ?>"></i></span>
                                  <?php } else{ ?>
                                   <span><i class='fas cfpro-rev-edit-reivew fa-ellipsis-v' data-url="<?= $login_url2; ?>" data-openId="<?=$rid ?>"></i></span>
                                  <?php } ?>
                                </div>
                                <?php if($shows){ ?>
                                <div class="cfpro-rev-rating-star">
                                    <span class="cfpro-rev-my-rating"></span> 
                                    <script>
                                    $(".cfpro-rev-my-rating").starRating({
                                    initialRating: <?= $rating; ?>,
                                    disableAfterRate: false,
                                    starShape: 'rounded',
                                    readOnly:true,
                                    disableAfterRate:false,
                                    activeColor:"slategray",
                                    ratedColor:'slategray',
                                    });
                                    </script>
                                </div>
                                <?php }?>
                                <?php if($showsummary){ ?>
                                <div class="cfpro-rev-summery cfpro-rev-comment-text-f" style="color:<?="#".$summarycolor?>;font-size:<?=$summaryfize; ?>px" >
                                   <?php 
                                        $string_length = strlen( $review );
    
                                        if( $string_length > $summaryletter && $summaryletter!="-1")
                                        {
                                            $substr1 = substr( $review, 0 , $summaryletter );
                                            $substr2 = substr( $review, $summaryletter,$string_length-$summaryletter );
                                            ?>
                                            <span class="cfpro-rev-comment-first-text"><?= $substr1; ?><span class="cfpro-rev-comment-dot-f">...</span> </span><a href="javascript:void(0)" class="cfpro-rev-read-more-review-f" data-readmore="<?=$readmore?>" data-readless="<?=$readless?>" style="color:<?="#".$readmorecolor;?>"><?=$readmore;?></a>
                                            <span class="cfpro-rev-comment-second-text-f"><?php echo $substr2; ?></span>
                                            <?php
                                        }else{
                                        echo $review;
                                        }
                                    ?>  
                                </div>
                                <?php }
                                if( $media && count( $mediac ) > 0 ) {
                                    $k=1;
                                    ?>
                                    <div class="d-flex mt-2">
                                       <?php for( $j = 0; $j< $mcount; $j++ )
                                        if( $j <= 2 )
                                        {
                                            $k=$j+1;
                                            if( stristr( $mediac[$j]['type'], 'image' ) ){
                                                ?><div  data-unid="<?=$rev ?>"  data-medid="<?=$k?>" data-openId="<?=$random ?>" class="cfPrRevMedia cfPrRevMediaoOModal me-1" style="background-image: url('<?=$mediac[$j]['name']?>'); "></div>
                                            <?php
                                            }
                                            elseif( stristr($mediac[$j]['type'],'video') )
                                            {
                                                echo '<div  data-unid="'.$rev.'"  data-medid="'.$k.'" data-openId="'.$random.'" class="cfPrRevMedia cfPrRevMediaoOModal" > <i class="fas cfPrRevMediaoOModal fa-video" data-openId="'.$random.'"  data-medid="'.$k.'" data-unid="'.$rev.'" ></i></div>';
                                            }
                                            elseif( stristr($mediac[$j]['type'],'audio') ){
                                                echo '<div  data-unid="'.$rev.'" data-openId="'.$random.'" data-medid="'.$k.'" class="cfPrRevMedia cfPrRevMediaoOModal" ><i class="fas cfPrRevMediaoOModal fa-file-audio" data-openId="'.$random.'"  data-medid="'.$k.'" data-unid="'.$rev.'" ></i></div>';
                                            }
                                        }else{
                                            $k=$j+1;
                                            if( stristr($mediac[$j]['type'], 'image' ) ){
                                                ?><div  data-unid="<?=$rev ?>"  data-medid="<?=$k?>" data-openId="<?=$random ?>" class="cfPrRevMedia cfPrRevMediaoOModal me-1" style="background-image: url('<?=$mediac[$j]['name']?>'); "><div  data-unid="<?=$rev ?>"  data-medid="<?=$k?>" data-openId="<?=$random ?>" class="cfPrRevMediaover cfPrRevMediaoOModal"><span data-openId="<?=$random ?>"   data-medid="<?=$k?>" data-unid="<?=$rev ?>" class="cfPrRevMediaoOModal">+<?=($mcount-3);?></span></div></div>
                                            <?php } elseif( stristr($mediac[$j]['type'],'video') ) {
                                                echo '<div  data-unid="'.$rev.'" data-openId="'.$random.'" data-medid="'.$k.'" class="cfPrRevMedia cfPrRevMediaoOModal" > <i class="fas fa-video" ></i><div data-openId="'.$random.'"  data-medid="'.$k.'" data-unid="'.$rev.'" class="cfPrRevMediaoOModal cfPrRevMediaover"><span data-unid="'.$rev.'" data-medid="'.$k.'" data-openId="'.$random.'" class="cfPrRevMediaover">'.($mcount-3).'</span></div>';
                                            } elseif( stristr($mediac[$j]['type'],'audio') ){
                                                echo '<div  data-unid="'.$rev.'" data-openId="'.$random.'" data-medid="'.$k.'" class="cfPrRevMedia cfPrRevMediaoOModal" ><i class="fas fa-file-audio"></i><div data-openId="'.$random.'"  data-medid="'.$k.'" data-unid="'.$rev.'" class="cfPrRevMediaoOModal cfPrRevMediaover"><span data-unid="'.$rev.'"  data-medid="'.$k.'" data-openId="'.$random.'" class="cfPrRevMediaover">'.($mcount-3).'</span></div>';
                                            }
                                            break;
                                        }
                                        ?>
                                        <input type="hidden" id="cfprorev-media-container-<?=$rev; ?>" value='<?=$media;?>'>
                                    </div>
                                <?php } ?>
                                <?php  if( $showld ){ ?>
                                    <?php  if( is_member_loggedin($funnel_id) ){ ?>
                                        <?php 
                                        $total_ld = $setting_ob->showTotalLikes( $id, $rid );
                                        $like = $setting_ob->checkLikeStatusF( $midd, $id, $rid, 1 );
                                        $dlike = $setting_ob->checkLikeStatusF( $midd, $id, $rid, -1 );
                                        ?>
                                        <div class="cfpro-rev-ld-c">
                                            <a class="cfpro-rev-l-la  cfpro-rev-lkdk cfpro-rev-like"  data-status="true"  data-pid="<?= cf_enc($id); ?>" data-rid="<?= cf_enc($rid); ?>">
                                                <span class="cfpro-rev-l-ls cfpro-rev-like">
                                                    <i class="fas fa-thumbs-up cfpro-rev-like cfpro-rev-l-li  <?= ( $like || $like == 1 ) ? 'text-primary':''; ?>"></i>
                                                </span>
                                                <span class="cfpro-rev-ld-tltiptext">Like</span> 
                                            </a>
                                            <a class="pl-2 cfpro-rev-l-c text-sm">
                                                <span class="cfpro-rev-l-cs"><?= ($total_ld['likes']==0)?'':$total_ld['likes']; ?></span>
                                            </a> 
                                            <a class="pl-2 cfpro-rev-d-da cfpro-rev-lkdk cfpro-rev-dlike" data-status="true"  data-rid="<?= cf_enc($rid); ?>" data-pid="<?= cf_enc($id); ?>">
                                                <span class="cfpro-rev-d-ds cfpro-rev-dlike">
                                                    <i class="fas cfpro-rev-d-di cfpro-rev-dlike fa-thumbs-up <?= ( $dlike || $dlike == -1) ? 'text-primary' : ''; ?>"></i>
                                                </span> 
                                                <span class="cfpro-rev-ld-tltiptext">Dislike</span>
                                            </a>  
                                            <a class="pl-2 cfpro-rev-d-c text-sm">
                                                <span class="cfpro-rev-d-cs"><?= ($total_ld['dislikes']==0)?'':$total_ld['dislikes']; ?></span>
                                            </a>
                                        </div>
                                    <?php }else{  
                                        $total_ld = $setting_ob->showTotalLikes( $id, $rid );
                                        $newrevurl1 .= "&pid=".cf_enc($id)."&rid=".cf_enc($rid)."";
                                    ?>
                                        <div class="cfpro-rev-ld-c">
                                            <a class="cfpro-rev-l-la  cfpro-rev-lkdk cfpro-rev-like" data-url="<?=get_login_action_url( $funnel_id,$newrevurl1."&like=true",true );?>"  data-status="false"  data-pid="<?= cf_enc($id); ?>" data-rid="<?= cf_enc($rid); ?>">
                                                <span class="cfpro-rev-l-ls cfpro-rev-like">
                                                    <i class="fas fa-thumbs-up cfpro-rev-like cfpro-rev-l-li "></i>
                                                </span>
                                                <span class="cfpro-rev-ld-tltiptext">Like</span> 
                                            </a>
                                            <a class="pl-2 cfpro-rev-l-c text-sm">
                                                <span class="cfpro-rev-l-cs"><?= ( $total_ld['likes'] == 0 ) ? '' : $total_ld['likes']; ?></span>
                                            </a> 
                                            <a class="pl-2 cfpro-rev-d-da cfpro-rev-lkdk cfpro-rev-dlike"  data-url="<?=get_login_action_url( $funnel_id,$newrevurl1."&dislike=true",true );?>"  data-status="false"  data-rid="<?= cf_enc($rid); ?>" data-pid="<?= cf_enc($id); ?>">
                                                <span class="cfpro-rev-d-ds cfpro-rev-dlike">
                                                    <i class="fas cfpro-rev-d-di cfpro-rev-dlike fa-thumbs-up"></i>
                                                </span> 
                                                <span class="cfpro-rev-ld-tltiptext">Dislike</span>
                                            </a>  
                                            <a class="pl-2 cfpro-rev-d-c text-sm">
                                                <span class="cfpro-rev-d-cs"><?= ( $total_ld['dislikes'] == 0 ) ? '' : $total_ld['dislikes']; ?></span>
                                            </a>
                                        </div>
                                    <?php } ?>
                                <?php } ?>
                            </div>
                            <?php if(is_member_loggedin( $funnel_id ) && $email== $memail ){  ?>
                            <div class="modal show cfpro-rev-rating-container cfpro-review-rating fade" id="cfpro-rev-modal-edit-<?=$rid  ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <form action="" class="cfpro-review-form" enctype="multipart/form-data">
                                            <input type="hidden" name="action" value="cfproreviews_addreview">
                                            <input type="hidden"  name="name" value="<?= $name ?>" >
                                            <input type="hidden"  name="email" value="<?= $email ?>">
                                            <div class="modal-header" style="background-color:#<?= $rhbackcolor?>;color:#<?= $rhcolor?>">
                                                <h5 class="modal-title">Edit or Delete Review</h5>
                                                <button type="button" data-openId="<?=$rid  ?>" class="close cfpro-rev-modal-review-close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true" data-openId="<?=$rid  ?>" title="close" class="close cfpro-rev-modal-review-close">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body  mb-0 pb-0">
                                                <input type="hidden" name="pid" value="<?= cf_enc($id); ?>">
                                                <input type="hidden" name="rid" value="<?= cf_enc($rid); ?>">
                                                <input type="hidden" name="do_action" value="update">
                                                
                                                <?php if(isset($rshowstar) && $rshowstar): ?>
                                                <div class="mb-3 cfpro-rev-rat">
                                                    <div class="cfpro-rev-rating-c">
                                                        <input type="radio" id="cfpro-revstar5-<?=$rid?>" <?= ($rating==5)?'checked':"";?> name="rate" value="5" />
                                                        <label for="cfpro-revstar5-<?=$rid?>" title="5 stars"></label>
                                                        <input type="radio" id="cfpro-revstar4-<?=$rid?>" <?= ($rating==4)?'checked':"";?> name="rate" value="4" />
                                                        <label for="cfpro-revstar4-<?=$rid?>" title="4 stars"></label>
                                                        <input type="radio" id="cfpro-revstar3-<?=$rid?>" <?= ($rating==3)?'checked':"";?> name="rate" value="3" />
                                                        <label for="cfpro-revstar3-<?=$rid?>" title="3 stars"></label>
                                                        <input type="radio" id="cfpro-revstar2-<?=$rid?>" <?= ($rating==2)?'checked':"";?> name="rate" value="2" />
                                                        <label for="cfpro-revstar2-<?=$rid?>" title="2 stars"></label>
                                                        <input type="radio" id="cfpro-revstar1-<?=$rid?>" <?= ($rating==1)?'checked':"";?> name="rate" value="1" />
                                                        <label for="cfpro-revstar1-<?=$rid?>" title="1 star"></label>
                                                    </div>
                                                </div>
                                                <?php endif; ?>
                                                <?php if(isset($rshowsummary) && $rshowsummary ): ?>
                                                <div class="from-group ">
                                                    <?php if(isset($rsub_color)): ?>
                                                    <label style="color:#<?= $rlabelc?>"> <?=$rlabeltext; ?></label>
                                                    <?php endif; ?>
                                                    <textarea name="summary"   rows="2" class="form-control cfpro-rev-summary-box" maxlength="<?=$rsum_length; ?>" placeholder="<?=$rsum_place; ?>"><?=$review; ?></textarea>
                                                </div>
                                                <?php endif; ?>
                                                <?php if( isset( $rallowfu ) && $rallowfu ): ?>
                                                    <div class="mb-3  py-2">
                                                        <?php if(isset( $rallowfu ) ):?>
                                                        <label style="#color:<?= $rlabelc?>"> <?=$rupload_text; ?></label>
                                                        <?php endif;?>
                                                        <div class="form-control cfpro-rev-multiple-file-parent">
                                                            <input type="hidden" value="<?=$rmaxfile;?>" class="cfpro-rev-maxfile" >
                                                            <input type="hidden" value="<?=$rfsize;?>" class="cfpro-rev-filesize" >
                                                            <?php if(isset( $rfext ) ):?>
                                                            <input type="file"   accept="<?= $rfextesnions?>" multiple name="media[]" class="cfpro-rev-multiple-file">
                                                            <?php else:?>
                                                            <input type="file"   multiple name="media[]" class="cfpro-rev-multiple-file">
                                                            <?php endif;?>
                                                            <input type="hidden" class="cfpro-rev-multiple-next" value="<?=$newx;?>">
                                                        </div>
                                                        <div class="cfpro-rev-multiple-file-con py-2">
                                                        </div>
                                                        <div id="cfpro-rev-media-progress">
                                                        <div id="cfpro-rev-media-bar"></div>
                                                        </div>
                                                    </div>
                                                <?php endif;?>
                                            </div>
                                            <div style="background-color:<?= $rfooterc?>;"  class="modal-footer">
                                                <button type="button" data-id="<?= cf_enc($rid);?>" class="btn btn-danger cfpre-rev-delete-rbtn"><?=$rdeletebtn_text?> </button>
                                                <button type="submit" style="background-color:#<?= $rsub_backcolor?>;color:#<?= $rsub_color?>;border-color:#<?= $rsub_backcolor?>" class="btn btn-primary cfpre-rev-submit-rbtn"><?=$rsubbtn_text?> </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <?php } ?>                                 
                        </div>
                        <hr />
                        <?php
                    }
                }else{
                    ?>
                    <div class="text-center">
                        No Reviews available
                    </div>
                    <?php
                }
                ?>
            </div>
            <?php  if( $showp && $totalreviewer > 10 ){ ?>
            <?php echo $this->getPagination( $pages,$last_page,$first_page,$pnext,$pprev ) ?>
            <?php  } ?>
        <?php  } ?>
    </div>
    <!-- Modal for unauthenticate user -->
    <div class="modal show cfpro-rev-rating-container cfpro-review-rating fade" id="mycfpro-rev-modal-login-<?=$random ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form action="" class="cfpro-review-form" enctype="multipart/form-data">
                    <input type="hidden" name="action" value="cfproreviews_addreview">
                    <div class="modal-header" style="background-color:#<?= $rhbackcolor?>;color:#<?= $rhcolor?>">
                        <h5 class="modal-title"><?=$rheadertext?></h5>
                        <button type="button" data-openId="<?=$random ?>" class="close cfpro-rev-modal-login-close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true" data-openId="<?=$random ?>" title="close" class="close cfpro-rev-modal-login-close">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body  mb-0 pb-0">
                        <!-- Check if the anybody can add review option enabled -->
                        <?php 
                        if( $rallowall=="all" || $rallowall=="allver" ){
                                ?>
                        <div class="mb-3 mb-0">
                            <label for="">Name</label>
                            <input type="text"  name="name" class="form-control">
                        </div>
                        <div class="mb-3 mb-0">
                            <label for="">Email</label>
                            <input type="email" name="email" class="form-control">
                        </div>
                        <?php 
                            } 
                        ?>
                        <!-- Check if the anybody can add review option with verification enabled -->
                        <?php if( $rallowall=="logver" || $rallowall=="allver" ){ ?>
                            <input type="hidden" name="review_url" value="">
                        <?php } ?>
                        <input type="hidden" name="pid" value="<?= cf_enc($id); ?>">
                        <input type="hidden" name="do_action" value="add">
                        
                        <?php if(isset($rshowstar) && $rshowstar): ?>
                        <div class="mb-3 cfpro-rev-rat">
                            <div class="cfpro-rev-rating-c">
                                <input type="radio" id="cfpro-revstar5" name="rate" value="5" />
                                <label for="cfpro-revstar5" title="5 stars"></label>
                                <input type="radio" id="cfpro-revstar4" name="rate" value="4" />
                                <label for="cfpro-revstar4" title="4 stars"></label>
                                <input type="radio" id="cfpro-revstar3" name="rate" value="3" />
                                <label for="cfpro-revstar3" title="3 stars"></label>
                                <input type="radio" id="cfpro-revstar2" name="rate" value="2" />
                                <label for="cfpro-revstar2" title="2 stars"></label>
                                <input type="radio" id="cfpro-revstar1" name="rate" value="1" />
                                <label for="cfpro-revstar1" title="1 star"></label>
                            </div>
                        </div>
                        <?php endif; ?>
                        <?php if(isset($rshowsummary) && $rshowsummary ): ?>
                        <div class="from-group ">
                            <?php if(isset($rsub_color)): ?>
                            <label style="color:#<?= $rlabelc?>"> <?=$rlabeltext; ?></label>
                            <?php endif; ?>
                            <textarea name="summary"   rows="2" class="form-control cfpro-rev-summary-box" maxlength="<?=$rsum_length; ?>" placeholder="<?=$rsum_place; ?>"></textarea>
                        </div>
                        <?php endif; ?>
                        <?php if( isset( $rallowfu ) && $rallowfu ): ?>
                            <div class="mb-3  py-2">
                                <?php if(isset( $rallowfu ) ):?>
                                <label style="#color:<?= $rlabelc?>"> <?=$rupload_text; ?></label>
                                <?php endif;?>
                                <div class="form-control cfpro-rev-multiple-file-parent">
                                    <input type="hidden" value="<?=$rmaxfile;?>" class="cfpro-rev-maxfile" >
                                    <input type="hidden" value="<?=$rfsize;?>" class="cfpro-rev-filesize" >
                                    <?php if(isset( $rfext ) ):?>
                                    <input type="file"   accept="<?= $rfextesnions?>" multiple name="media[]" class="cfpro-rev-multiple-file">
                                    <?php else:?>
                                    <input type="file"   multiple name="media[]" class="cfpro-rev-multiple-file">
                                    <?php endif;?>
                                    <input type="hidden" class="cfpro-rev-multiple-next" value="<?=$newx;?>">
                                </div>
                                <div class="cfpro-rev-multiple-file-con py-2">
                                </div>
                                <div id="cfpro-rev-media-progress">
                                <div id="cfpro-rev-media-bar"></div>
                                </div>
                            </div>
                        <?php endif;?>
                    </div>
                    <div style="background-color:<?= $rfooterc?>;"  class="modal-footer">
                        <button type="submit" style="background-color:#<?= $rsub_backcolor?>;color:#<?= $rsub_color?>;border-color:#<?= $rsub_backcolor?>" class="btn btn-primary cfpre-rev-submit-rbtn"><?=$rsubbtn_text?> </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
    <div class="modal fade cfpro-rev-media-container cfpro-rev-rating-container"  id="cfpro-rev-media-container-<?=$random ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg"  role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" data-openId="<?=$random ?>" class="close cfpro-rev-modal-login-close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true" data-openId="<?=$random ?>" title="close" class="close cfpro-rev-modal-login-close">&times;</span>
                    </button>
                </div>
                <div class="modal-body p-0">
                    <div class="d-flex">
                        <div class="container cfpro-rev-imgmodal-c" >
                            <div class="position-relative cfpro-rev-image-box">
                                
                            </div>
                            <div class="position-relative mt-1  d-flex justify-content-center">
                                <a class="cfpro-rev-iprev1 cfpro-rev-nexprebtn" onclick="cfProRevplusSlides(-1,true)">❮</a>
                                <div class="caption-cfpro-rev-imgmodal d-none">
                                    <div class="d-flex caption-cfpro-rev-img-btn d-none">
                                    </div>
                                </div>
                                <a class="cfpro-rev-inext1 cfpro-rev-nexprebtn" onclick="cfProRevplusSlides(1,true)">❯</a>
                            </div>
                        </div>
                    </div>    
                </div>
            </div>
        </div>
    </div>
    <div class="cfpro-rev-snackbar-userside ">Review Added</div>
    <input type="hidden" id="cfpro-rev-modal-ajax" value="<?= get_option('install_url').'/index.php?page=ajax' ?>">
    <!-- Modal for unauthenticate user end -->

