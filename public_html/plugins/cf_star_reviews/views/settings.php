<?php
global $mysqli;
global  $app_variant;
$settings=$this->load('setting');

// Check app
$app_variant = isset($app_variant)?$app_variant:"shopfunnels";
$datas = $settings->getSettings();
if( $datas )
{
    // Extract all variable
    foreach($datas as $index=> $data)
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
        
        'rfextesnions'=>'.png, .jpg, .jpeg, .gif',"rboxposition"=>'c',"rallowall"=>"all",'rupload_text'=>'Upload File','rheadertext'=>'Rate this product','rlabeltext'=>'Enter Summary','rsubbtn_text'=>'Submit Review','rdeletebtn_text'=>'Delete','rsum_place'=>'Share details of your own experience at this place','rsum_length'=>2000,
        
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
?>
<div class="container-fluid">   
    <div class="row page-titles mb-4">
        <div class="col-md-5 align-self-center">
            <h4 class="text-themecolor" id="commoncontainerid"><img src='<?=CFPRODUCT_REV_PLUGIN_URL_URL; ?>/assets/image/f9.png' alt='Review' /> <?php w('Review Settings'); ?></h4>
        </div>
        <div class="col-md-7 align-self-center text-end">
            <div class="d-flex justify-content-end align-items-center"><?php w('Settings'); ?></div>
        </div>
    </div>
    <div>
        <div class="cfpro-rev_tabs">
                <ul class="cfpro-rev_tabs-nav">
                    <li class="cfpro-rev_tab-active"><a href="#cfpro-rev_tab-1"><?php w('General Setting'); ?></a></li>
                    <li ><a href="#cfpro-rev_tab-2"><?php w('Review Setting'); ?></a></li>
                    <li ><a href="#cfpro-rev_tab-3"><?php w('Rating form Settings'); ?></a></li>
                </ul>
                <div class="cfpro-rev_tabs-stage">
                    <form action="" class="cfpro-rev-setting-form">
                        <div id="cfpro-rev_tab-1" class="cfpro-rev_tabs_navvar">
                            <h3 class="text-dark text-center"><?php w('General Setting'); ?> </h3> <hr class="bg-primary  my-sm-0">
                            <input type="hidden" value="saveproduct_review_ajax" name="action" >
                            <div class="row">
                                <div class="col-md-5 cfpro-rev-border px-0 ">
                                    <h5 class="text-dark mt-2 text-center"><?php w('General Setting'); ?> </h5> <hr class="bg-primary">
                                    <div class="mb-3 p-3">
                                        <div class="row mb-3">
                                            <div class="col-1">
                                                <label class="cf-pro-rev-like-containerr">
                                                    <input type="radio" value="all" <?=($rallowall=="all")? "checked":""  ?> name="revtext[rallowall]" >
                                                    <span class="cf-pro-radio"></span>
                                                </label>
                                            </div>
                                            <label class="form-label col "><?php w('Anybody Can Add Review'); ?> </label>
                                        </div>
                                        <div class="row mb-3">
                                            <div class="col-1">
                                                <label class="cf-pro-rev-like-containerr">
                                                    <input type="radio" value="allver" <?=($rallowall=="allver")? "checked":""  ?> name="revtext[rallowall]" >
                                                    <span class="cf-pro-radio"></span>
                                                </label>
                                            </div>
                                            <label class="form-label col "><?php w('Anybody Can Add Review With Email Verification'); ?> <i data-bs-toggle="tooltip" data-placement="top" title="<?php w('Go to setting, on general setting page select default SMTP'); ?>"  class="fas fa-question-circle text-primary"></i></label>
                                        </div>
                                        <div class="row mb-3">
                                            <div class="col-1">
                                                <label class="cf-pro-rev-like-containerr">
                                                    <input type="radio" value="log"  <?=($rallowall=="log")? "checked":""  ?> name="revtext[rallowall]" >
                                                    <span class="cf-pro-radio"></span>
                                                </label>
                                            </div>
                                            <label class="form-label col "><?php w('Only Logged In User Can Add Review'); ?>  </label>
                                        </div>
                                        <div class="row mb-3" >
                                            <div class="col-1">
                                                <label class="cf-pro-rev-like-containerr">
                                                    <input type="radio" value="logver"  <?=($rallowall=="logver")? "checked":""  ?> name="revtext[rallowall]" >
                                                    <span class="cf-pro-radio"></span>
                                                </label>
                                            </div>
                                            <label class="form-label col "><?php w('Only Logged In User Can Add Review With Email Verification'); ?> <i data-bs-toggle="tooltip" data-placement="top" title="<?php w('Go to setting, on general setting page select default SMTP'); ?>"  class="fas fa-question-circle text-primary"></i> </label>
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="mb-3 px-3">
                                        <div class="mb-3 row">
                                            <div class="col-1">
                                                <label class="cf-pro-rev-like-container">
                                                    <input type="checkbox" <?=($aapproved)? "checked":""  ?> name="rsetting[aapproved]" >
                                                    <span class="cf-pro-rev-like-checkmark"></span>
                                                </label>
                                            </div>
                                            <label class="form-label col "><?php w('Automatic Approved Review'); ?></label>
                                        </div>
                                        <div class="mb-3 row">
                                            <div class="col-1">
                                                <label class="cf-pro-rev-like-container">
                                                    <input type="checkbox" <?=($markasread)? "checked":""  ?> name="rsetting[markasread]" >
                                                    <span class="cf-pro-rev-like-checkmark"></span>
                                                </label>
                                            </div>
                                            <label class="form-label col "><?php w('Automatic Mark As Read'); ?></label>
                                        </div>
                                    </div>
                                </div> 
                                <div class="col-md-7 px-0">
                                    <h5 class="text-dark mt-2 text-center"><?php w('General Setting'); ?> </h5> <hr class="bg-primary">
                                    <div class="mb-3 px-3">
                                        <label for=""><?php w('Email Subject'); ?> </label>
                                        <input type="text" value="<?=$email_subject ?>" placeholder="Enter Email Subject'); ?>" name="email_subject" class="form-control" >
                                        <div class="text-start mt-1">
                                            <span href="javascript:void(0)" class="btn btn-info btn-sm" data-bs-toggle="collapse" data-target="#cfpro-rev-demo5"><?php w('Shortcodes'); ?></span>
                                            <div id="cfpro-rev-demo5" class="collapse" >
                                                <p class="" style="font-size: 13px !important; opacity: 0.8;">
                                                    <span class="text-info cfdisp_cursor" onclick="copyText(`{verification_url}`)" data-bs-toggle="tooltip" title="Copy to clipboard" >{verification_url}</span> <?php w('for the verification url'); ?><br>
                                                    <span class="text-info cfdisp_cursor" onclick="copyText(`{name}`)" data-bs-toggle="tooltip" title="Copy to clipboard" >{name}</span> <?php w('for name'); ?> <br>
                                                    <span class="text-info cfdisp_cursor" onclick="copyText(`{email}`)" data-bs-toggle="tooltip" title="Copy to clipboard" >{email}</span> <?php w('for email'); ?>
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mb-3 px-3">
                                        <label for=""><?php w('Email Content For Reviewer Email Verification'); ?></label>
                                        <textarea  name="email_content" id="cfprorev_gmail_content" ><?php echo str_replace("\\\\r\\\\n","",str_replace("\&quot;","",str_replace("\\r\\n","",htmlentities($email_content)))); ?></textarea>
                                        <div class="text-start py-2">
                                            <strong class="btn btn-info btn-sm" data-bs-toggle="collapse" data-target="#cfprore-demo3"><?php w('Shortcodes'); ?></strong> 
                                            <p class="" style="font-size: 13px !important; opacity: 0.8;">
                                                <div class="text-start py-3 mt-1">
                                                    <div id="cfprore-demo3" class="collapse" style="font-size: 13px !important;font-weight:500">
                                                        <span class="text-info cfdisp_cursor" onclick="copyText(`{verification_url}`)" data-bs-toggle="tooltip" title="Copy to clipboard" >{verification_url}</span> <?php w('for the verification url'); ?><br>
                                                        <span class="text-info cfdisp_cursor" onclick="copyText(`{name}`)" data-bs-toggle="tooltip" title="Copy to clipboard" >{name}</span> <?php w('for name'); ?> <br>
                                                        <span class="text-info cfdisp_cursor" onclick="copyText(`{email}`)" data-bs-toggle="tooltip" title="Copy to clipboard" >{email}</span> <?php w('for email'); ?>
                                                    </div>
                                                </div>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div id="cfpro-rev_tab-2" class="cfpro-rev_tabs_navvar">
                            
                            <h3 class="text-dark text-center"><?php w('Reviews Setting'); ?> </h3> <hr class="bg-primary  my-sm-0">
                            <input type="hidden" value="saveproduct_review_ajax" name="action" >
                            <div class="row">
                                <div class="col-md-6 cfpro-rev-border px-0 ">
                                    <h5 class="text-dark mt-2 text-center"><?php w('Manage Reviews'); ?> </h5> <hr class="bg-primary">
                                    <div class="mb-3 p-3 pb-0">
                                        <div class="mb-3 row">
                                            <div class="col-1">
                                                <label class="cf-pro-rev-like-container">
                                                    <input type="checkbox" <?=($showld)? "checked":""  ?> name="rsetting[showld]" >
                                                    <span class="cf-pro-rev-like-checkmark"></span>
                                                </label>
                                            </div>
                                            <label class="form-label col "><?php w('Show Like And Dislike'); ?></label>
                                        </div>
                                        <div class="mb-3 row">
                                            <div class="col-1">
                                                <label class="cf-pro-rev-like-container">
                                                    <input type="checkbox"  <?=($showp)? "checked":""  ?> name="rsetting[showp]" >
                                                    <span class="cf-pro-rev-like-checkmark"></span>
                                                </label>
                                            </div>
                                            <label class="form-label col "><?php w('Show Pagination'); ?></label>
                                        </div>
                                        <div class="mb-3 row">
                                            <div class="col-1">
                                                <label class="cf-pro-rev-like-container">
                                                    <input type="checkbox"  <?=($shows)? "checked":""  ?> name="rsetting[shows]" >
                                                    <span class="cf-pro-rev-like-checkmark"></span>
                                                </label>
                                            </div>
                                            <label class="form-label col "><?php w('Show Star'); ?></label>
                                        </div>
                                        <div class="mb-3 row">
                                            <div class="col-1">
                                                <label class="cf-pro-rev-like-container">
                                                    <input type="checkbox" <?=($showsummary)? "checked":""  ?> name="rsetting[showsummary]" >
                                                    <span class="cf-pro-rev-like-checkmark"></span>
                                                </label>
                                            </div>
                                            <label class="form-label col "><?php w('Show Summary'); ?></label>
                                        </div>
                                        <div class="mb-3 row">
                                            <div class="col-1">
                                                <label class="cf-pro-rev-like-container">
                                                    <input type="checkbox" <?=($showavg)? "checked":""  ?> name="rsetting[showavg]" >
                                                    <span class="cf-pro-rev-like-checkmark"></span>
                                                </label>
                                            </div>
                                            <label class="form-label col "><?php w('Show Average Setting Box'); ?></label>
                                        </div>
                                        <div class="mb-3 row">
                                            <div class="col-1">
                                                <label class="cf-pro-rev-like-container">
                                                    <input type="checkbox" <?=($showsumbox)? "checked":""  ?> name="rsetting[showsumbox]" >
                                                    <span class="cf-pro-rev-like-checkmark"></span>
                                                </label>
                                            </div>
                                            <label class="form-label col "><?php w('Show Summary Box'); ?></label>
                                        </div>
                                        <hr>
                                        <div class="mb-3 row">
                                            <label class="col-form-label col-sm-5 align-self-center"><?php w('Rating Box Position'); ?></label>
                                            <div class="col ">
                                                <select class="form-control" name="rtext[boxposition]" id="">
                                                    <option value="c" <?=( $boxposition == "c" ) ? "selected" : ""; ?> ><?php w('Center'); ?></option>
                                                    <option value="l" <?=( $boxposition == "l" ) ? "selected" : ""; ?> ><?php w('Left'); ?></option>
                                                    <option value="r" <?=( $boxposition == "r" ) ? "selected" : ""; ?> ><?php w('Right'); ?></option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="mb-3 row">
                                            <label class="col-form-label col-sm-5 align-self-center"><?php w('Heading Text'); ?></label>
                                            <div class="col text-end">
                                                <input name="rtext[headertext]" value="<?= $headertext; ?>" class="form-control form-control-sm" >
                                            </div>
                                        </div>
                                        <div class="mb-3 row">
                                            <label class="col-form-label col-sm-5 align-self-center"><?php w('Rating Text'); ?></label>
                                            <div class="col text-end">
                                                <input name="rtext[ratingtext]" value="<?=$ratingtext; ?>" class="form-control form-control-sm" >
                                            </div>
                                        </div>
                                        <div class="mb-3 row">
                                            <label class="col-form-label col-sm-5 align-self-center"><?php w('Read More Text'); ?></label>
                                            <div class="col text-end">
                                                <input name="rtext[readmore]" value="<?=$readmore ?>" class="form-control form-control-sm" >
                                            </div>
                                        </div>
                                        <div class="mb-3 row">
                                            <label class="col-form-label col-sm-5 align-self-center"><?php w('Read Less'); ?></label>
                                            <div class="col text-end">
                                                <input name="rtext[readless]" value="<?=$readless ?>" class="form-control form-control-sm" >
                                            </div>
                                        </div>
                                        <div class="mb-3 row">
                                            <label class="col-form-label col-sm-5 align-self-center"><?php w("How Many Letters Do You Want To Show Till 'Read More'?"); ?></label>
                                            <div class="col text-end">
                                                <input name="rtext[summaryletter]" value="<?=$summaryletter ?>" class="form-control form-control-sm" >
                                            </div>
                                        </div>
                                        <div class="mb-3 row">
                                            <label class="col-form-label col-sm-5 align-self-center"><?php w('Rate Product Text'); ?></label>
                                            <div class="col text-end">
                                                <input name="rtext[rateproducttext]" value="<?=$rateproducttext?>" class="form-control form-control-sm" >
                                            </div>
                                        </div>
                                        <div class="mb-3 row">
                                            <label class="col-form-label col-sm-5 align-self-center"><?php w('Reviews Text'); ?></label>
                                            <div class="col text-end">
                                                <input name="rtext[reviewstext]" value="<?= $reviewstext ?>" class="form-control form-control-sm" >
                                            </div>
                                        </div>
                                        <div class="mb-3 row">
                                            <label class="col-form-label col-sm-5 align-self-center"><?php w('Pagination Next Text'); ?></label>
                                            <div class="col text-end">
                                                <input name="rtext[pnext]" value="<?=$pnext; ?>" class="form-control form-control-sm" >
                                            </div>
                                        </div>
                                        <div class="mb-3 row">
                                            <label class="col-form-label col-sm-5 align-self-center"><?php w('Pagination Previous Text'); ?></label>
                                            <div class="col text-end">
                                                <input name="rtext[pprev]" value="<?=$pprev?>" class="form-control form-control-sm" >
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 px-0">
                                    <h5 class="text-dark mt-2 text-center"><?php w('Manage Reviews Styling'); ?></h5> <hr class="bg-primary">
                                    <div class="mb-3 p-3 pb-0">
                                        <div class="mb-3 row">
                                            <label class="col-form-label col-sm-5 align-self-center"><?php w('Rating Container Width'); ?></label>
                                            <div class="col input-group">
                                                <input  name="rstyle[rwidth]" value="<?= $rwidth; ?>" type="number" class="form-control" placeholder="<?php w('Enter width in percentage'); ?>">
                                                <div class="input-group-append">
                                                    <span class="input-group-text">%</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="mb-3 row">
                                            <label class="col-form-label col-sm-5 align-self-center"><?php w('Average Rating Progress Background Color'); ?></label>
                                            <div class="col text-end">
                                                <input name="rstyle[avgratingcolor]" value="<?= $avgratingcolor; ?>" class="jscolor form-control form-control-sm">
                                            </div>
                                        </div>
                                        <div class="mb-3 row">
                                            <label class="col-form-label col-sm-5 align-self-center"><?php w('Rating Star Color'); ?></label>
                                            <div class="col text-end">
                                                <input name="rstyle[starcolor]" value="<?= $starcolor; ?>" class="jscolor form-control form-control-sm">
                                            </div>
                                        </div>
                                        <div class="mb-3 row">
                                            <label class="col-form-label col-sm-5 align-self-center"><?php w('1 Rating Color'); ?></label>
                                            <div class="col text-end">
                                                <input name="rstyle[star1]" value="<?= $star1 ?>" class="jscolor form-control form-control-sm">
                                            </div>
                                        </div>
                                        <div class="mb-3 row">
                                            <label class="col-form-label col-sm-5 align-self-center"><?php w('2 Rating Color'); ?></label>
                                            <div class="col text-end">
                                                <input name="rstyle[star2]" value="<?= $star2 ?>" class="jscolor form-control form-control-sm">
                                            </div>
                                        </div>
                                        <div class="mb-3 row">
                                            <label class="col-form-label col-sm-5 align-self-center"><?php w('3 Rating Color'); ?></label>
                                            <div class="col text-end">
                                                <input name="rstyle[star3]" value="<?= $star3 ?>" class="jscolor form-control form-control-sm">
                                            </div>
                                        </div>
                                        <div class="mb-3 row">
                                            <label class="col-form-label col-sm-5 align-self-center"><?php w('4 Rating Color'); ?></label>
                                            <div class="col text-end">
                                                <input name="rstyle[star4]" value="<?= $star4?>" class="jscolor form-control form-control-sm">
                                            </div>
                                        </div>
                                        <div class="mb-3 row">
                                            <label class="col-form-label col-sm-5 align-self-center"><?php w('5 Rating Color'); ?></label>
                                            <div class="col text-end">
                                                <input name="rstyle[star5]" value="<?= $star5?>" class="jscolor form-control form-control-sm">
                                            </div>
                                        </div>
                                        <div class="mb-3 row">
                                            <label class="col-form-label col-sm-5 align-self-center"><?php w('Rate Product Color'); ?></label>
                                            <div class="col text-end">
                                                <input name="rstyle[rprocolor]" value="<?= $rprocolor ?>" class="jscolor form-control form-control-sm">
                                            </div>
                                        </div>
                                        <div class="mb-3 row">
                                            <label class="col-form-label col-sm-5 align-self-center"><?php w('Rate Product Background Color'); ?></label>
                                            <div class="col text-end">
                                                <input name="rstyle[rprobackcolor]" value="<?= $rprobackcolor ?>" class="jscolor form-control form-control-sm">
                                            </div>
                                        </div>
                                        <div class="mb-3 row">
                                            <label class="col-form-label col-sm-5 align-self-center"><?php w('Read More Color'); ?></label>
                                            <div class="col text-end">
                                                <input name="rstyle[readmorecolor]" value="<?=$readmorecolor ?>" class="jscolor form-control form-control-sm">
                                            </div>
                                        </div>
                                        <div class="mb-3 row">
                                            <label class="col-form-label col-sm-5 align-self-center"><?php w('Summary Color'); ?></label>
                                            <div class="col text-end">
                                                <input name="rstyle[summarycolor]" value="<?=$summarycolor ?>" class="jscolor form-control form-control-sm">
                                            </div>
                                        </div>
                                        <div class="mb-3 row">
                                            <label class="col-form-label col-sm-5 align-self-center"><?php w('Summary Font Size'); ?></label>
                                            <div class="col input-group">
                                                <input  name="rstyle[summaryfize]" value="<?= $summaryfize; ?>" type="number" class="form-control" placeholder="<?php w('Enter font size in pixel'); ?>">
                                                <div class="input-group-append">
                                                    <span class="input-group-text">px</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="mb-3 row">
                                            <label class="col-form-label col-sm-5 align-self-center"><?php w('Pagination Active Color'); ?></label>
                                            <div class="col text-end">
                                                <input name="rstyle[pageac]" value="<?= $pageac ?>" class="jscolor form-control form-control-sm">
                                            </div>
                                        </div>
                                        <div class="mb-3 row">
                                            <label class="col-form-label col-sm-5 align-self-center"><?php w('Pagination Hover Color'); ?></label>
                                            <div class="col text-end">
                                                <input name="rstyle[pagehoc]" value="<?= $pagehoc ?>" class="jscolor form-control form-control-sm">
                                            </div>
                                        </div>
                                        <div class="mb-3 row">
                                            <label class="col-form-label col-sm-5 align-self-center"><?php w('Pagination Button Color'); ?></label>
                                            <div class="col text-end">
                                                <input name="rstyle[pagecolor]" value="<?= $pagecolor ?>" class="jscolor form-control form-control-sm">
                                            </div>
                                        </div>
                                        <div class="mb-3 row">
                                        <label class="col-form-label col-sm-5"><?php w('Custom CSS'); ?></label>
                                        <div class="col text-end">
                                            <textarea name="rstyle[customcss]" id="" rows="4" class="form-control form-control-sm"><?=($customcss)? $customcss:''; ?></textarea>
                                            <div class="text-start">
                                                <p class="mt-0" style="font-size: 12px !important; opacity: 0.6;">
                                                    **<?php w('Use base selector name'); ?>
                                                    <strong>.this-form</strong> <br><?php w('Example'); ?> : <br> 
                                                    <strong>.this-form input[type=text] <br>
                                                    {border-radius: 5px;}</strong>
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div id="cfpro-rev_tab-3" class="cfpro-rev_tabs_navvar">    
                            <h3 class="text-dark text-center"><?php w('Rating Form Setting'); ?> </h3> <hr class="bg-primary  my-sm-0">
                            <input type="hidden" value="saveproduct_review_ajax" name="action" >
                            <div class="row">
                                <div class="col-md-6 cfpro-rev-border px-0 ">
                                    <h5 class="text-dark mt-2 text-center"><?php w('Rating Form Setting'); ?> </h5> <hr class="bg-primary">
                                    <div class="mb-3 p-3 pb-0">
                                        <div class="row">
                                            <div class="col-1">
                                                <label class="cf-pro-rev-like-container">
                                                    <input type="checkbox" id="cfpro-rev-allow-f" <?=($rallowfu)? "checked":""  ?> name="revsetting[rallowfu]" >
                                                    <span class="cf-pro-rev-like-checkmark"></span>
                                                </label>
                                            </div>
                                            <label class="form-label col "><?php w('Allow File Upload'); ?></label>
                                        </div>
                                        <div class="mb-3 cfpro-rev-allow-fcont row" style="display: <?=($rallowfu)? "block":"none"  ?>">
                                            <div class="col-md-6">
                                                <label class="col-form-label"><?php w('Enter Maximum File Size'); ?></label>
                                                <div class="input-group">
                                                    <input  name="revtext[rfsize]" value="<?= $rfsize; ?>" type="number" class="form-control" placeholder="<?php w('Enter maximum file size'); ?>">
                                                    <div class="input-group-append">
                                                        <span class="input-group-text">MB</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="col-form-label"><?php w('How Many Files Reviewer Can Upload'); ?></label>
                                                <div class="input-group">
                                                    <input  name="revtext[rmaxfile]" value="<?= $rmaxfile; ?>" type="number" class="form-control" placeholder="<?php w('How many files reviewer can upload'); ?>">
                                                    <div class="input-group-append">
                                                        <span class="input-group-text"><?php w('file(s)'); ?></span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row mt-2">
                                            <div class="col-1">
                                                <label class="cf-pro-rev-like-container">
                                                    <input type="checkbox" id="cfpro-rev-rfext"  <?=($rfext)? "checked":""  ?> name="revsetting[rfext]" >
                                                    <span class="cf-pro-rev-like-checkmark"></span>
                                                </label>
                                            </div>
                                            <label class="form-label col "><?php w('Restrict File Extension'); ?></label>
                                        </div>
                                        <div class="mb-3 cfpro-rev-rfext-open row" style="display: <?=($rfext)? "block":"none"  ?>">
                                            <div class="col-md-12">
                                                <label class=""><?php w('Enter File Extension (Use comma, Ex: .png, .jpg, .jpeg)'); ?></label>
                                                <input  name="revtext[rfextesnions]" value="<?= $rfextesnions; ?>" type="text" class="form-control" placeholder="<?php w('Enter file extension (use comma, Ex: .png, .jpg, .jpeg)'); ?>">
                                            </div>
                                        </div>
                                        <div class="mb-3 mt-2 row">
                                            <div class="col-1">
                                                <label class="cf-pro-rev-like-container">
                                                    <input type="checkbox"  <?=($rshowstar)? "checked":""  ?> name="revsetting[rshowstar]" >
                                                    <span class="cf-pro-rev-like-checkmark"></span>
                                                </label>
                                            </div>
                                            <label class="form-label col "><?php w('Show Rating Star'); ?></label>
                                        </div>
                                        <div class="mb-3 row">
                                            <div class="col-1">
                                                <label class="cf-pro-rev-like-container">
                                                    <input type="checkbox" <?=($rshowsummary)? "checked":""  ?> name="revsetting[rshowsummary]" >
                                                    <span class="cf-pro-rev-like-checkmark"></span>
                                                </label>
                                            </div>
                                            <label class="form-label col "><?php w('Show Summary Textarea'); ?></label>
                                        </div>
                                        <hr>
                                        <div class="mb-3 row">
                                            <label class="col-form-label col-sm-5 align-self-center"><?php w('Rating Form Modal Position'); ?></label>
                                            <div class="col ">
                                                <select class="form-control" name="revtext[rboxposition]" id="">
                                                    <option value="c" <?=( $rboxposition == "c" ) ? "selected" : ""; ?> ><?php w('Center'); ?></option>
                                                    <option value="l" <?=( $rboxposition == "l" ) ? "selected" : ""; ?> ><?php w('Left'); ?></option>
                                                    <option value="r" <?=( $rboxposition == "r" ) ? "selected" : ""; ?> ><?php w('Right'); ?></option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="mb-3 row">
                                            <label class="col-form-label col-sm-5 align-self-center"><?php w('Form Heading Text'); ?></label>
                                            <div class="col text-end">
                                                <input name="revtext[rheadertext]" value="<?= $rheadertext; ?>" class="form-control form-control-sm" >
                                            </div>
                                        </div>
                                        <div class="mb-3 row">
                                            <label class="col-form-label col-sm-5 align-self-center"><?php w('Summary Character Length'); ?></label>
                                            <div class="col text-end">
                                                <input name="revtext[rheadertext]" value="<?= $rheadertext; ?>" class="form-control form-control-sm" >
                                            </div>
                                        </div>
                                        <div class="mb-3 row">
                                            <label class="col-form-label col-sm-5 align-self-center"><?php w('Summary Label Text'); ?></label>
                                            <div class="col text-end">
                                                <input name="revtext[rlabeltext]" value="<?=$rlabeltext; ?>" class="form-control form-control-sm" >
                                            </div>
                                        </div>
                                        <div class="mb-3 row">
                                            <label class="col-form-label col-sm-5 align-self-center"><?php w('Summary Textarea Placeholder Text'); ?></label>
                                            <div class="col text-end">
                                                <input name="revtext[rsum_place]" value="<?=$rsum_place ?>" class="form-control form-control-sm" >
                                            </div>
                                        </div>
                                        <div class="mb-3 row">
                                            <label class="col-form-label col-sm-5 align-self-center"><?php w('Summary Character Length'); ?></label>
                                            <div class="col text-end">
                                                <input name="revtext[rsum_length]" value="<?=$rsum_length ?>" class="form-control form-control-sm" >
                                            </div>
                                        </div>
                                        <div class="mb-3 row">
                                            <label class="col-form-label col-sm-5 align-self-center"><?php w('Upload File Text'); ?></label>
                                            <div class="col text-end">
                                                <input name="revtext[rupload_text]" value="<?= $rupload_text ?>" class="form-control form-control-sm" >
                                            </div>
                                        </div>
                                        <div class="mb-3 row">
                                            <label class="col-form-label col-sm-5 align-self-center"><?php w('Submit Button Text'); ?></label>
                                            <div class="col text-end">
                                                <input name="revtext[rsubbtn_text]" placeholder="<?php w('Enter submit button text');?>" value="<?=$rsubbtn_text; ?>" class="form-control form-control-sm" >
                                            </div>
                                        </div>
                                        <div class="mb-3 row">
                                            <label class="col-form-label col-sm-5 align-self-center"><?php w('Delete Button Text'); ?></label>
                                            <div class="col text-end">
                                                <input name="revtext[rdeletebtn_text]" placeholder="<?php w('Enter delete button text');?>" value="<?=$rdeletebtn_text; ?>" class="form-control form-control-sm" >
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 px-0">
                                    <h5 class="text-dark mt-2 text-center"><?php w('Form Styling'); ?></h5> <hr class="bg-primary">
                                    <div class="mb-3 p-3 pb-0">
                                        <div class="mb-3 row">
                                            <label class="col-form-label col-sm-5 align-self-center"><?php w('Form Container Width'); ?></label>
                                            <div class="col input-group">
                                                <input  name="formstyle[rfrmwidth]" value="<?= $rfrmwidth; ?>" type="number" class="form-control" placeholder="<?php w('Enter width in percentage'); ?>">
                                                <div class="input-group-append">
                                                    <span class="input-group-text">%</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="mb-3 row">
                                            <label class="col-form-label col-sm-5 align-self-center"><?php w('Form Header Background Color'); ?></label>
                                            <div class="col text-end">
                                                <input name="formstyle[rhbackcolor]" value="<?= $rhbackcolor; ?>" class="jscolor form-control form-control-sm">
                                            </div>
                                        </div>
                                        <div class="mb-3 row">
                                            <label class="col-form-label col-sm-5 align-self-center"><?php w('From Header Color'); ?></label>
                                            <div class="col text-end">
                                                <input name="formstyle[rhcolor]" value="<?= $rhcolor; ?>" class="jscolor form-control form-control-sm">
                                            </div>
                                        </div>
                                        <div class="mb-3 row">
                                            <label class="col-form-label col-sm-5 align-self-center"><?php w('Rating Star Color'); ?></label>
                                            <div class="col text-end">
                                                <input name="formstyle[rstarcolor]" value="<?= $rstarcolor ?>" class="jscolor form-control form-control-sm">
                                            </div>
                                        </div>
                                        <div class="mb-3 row">
                                            <label class="col-form-label col-sm-5 align-self-center"><?php w('Rating Hover Color'); ?></label>
                                            <div class="col text-end">
                                                <input name="formstyle[rstarhover]" value="<?= $rstarhover ?>" class="jscolor form-control form-control-sm">
                                            </div>
                                        </div>
                                        <div class="mb-3 row">
                                            <label class="col-form-label col-sm-5 align-self-center"><?php w('Rating Star Default Color'); ?></label>
                                            <div class="col text-end">
                                                <input name="formstyle[rstardefault]" value="<?= $rstardefault ?>" class="jscolor form-control form-control-sm">
                                            </div>
                                        </div>
                                        <div class="mb-3 row">
                                            <label class="col-form-label col-sm-5 align-self-center"><?php w('Summary Label Color'); ?></label>
                                            <div class="col text-end">
                                                <input name="formstyle[rlabelc]" value="<?= $rlabelc ?>" class="jscolor form-control form-control-sm">
                                            </div>
                                        </div>
                                        <div class="mb-3 row">
                                            <label class="col-form-label col-sm-5 align-self-center"><?php w('Form Footer Background Color'); ?></label>
                                            <div class="col text-end">
                                                <input name="formstyle[rfooterc]" value="<?= $rfooterc?>" class="jscolor form-control form-control-sm">
                                            </div>
                                        </div>
                                        <div class="mb-3 row">
                                            <label class="col-form-label col-sm-5 align-self-center"><?php w('Submit Button Background Color'); ?></label>
                                            <div class="col text-end">
                                                <input name="formstyle[rsub_backcolor]" value="<?= $rsub_backcolor?>" class="jscolor form-control form-control-sm">
                                            </div>
                                        </div>
                                        <div class="mb-3 row">
                                            <label class="col-form-label col-sm-5 align-self-center"><?php w('Submit Button Color'); ?></label>
                                            <div class="col text-end">
                                                <input name="formstyle[rsub_color]" value="<?= $rsub_color ?>" class="jscolor form-control form-control-sm">
                                            </div>
                                        </div>
                                        <div class="mb-3 row">
                                            <label class="col-form-label col-sm-5"><?php w('Custom CSS'); ?></label>
                                            <div class="col text-end">
                                                <textarea name="formstyle[rcustomcss]" id="" rows="4" class="form-control form-control-sm"><?=($customcss)? $customcss:''; ?></textarea>
                                                <div class="text-start">
                                                    <p class="mt-0" style="font-size: 12px !important; opacity: 0.6;">
                                                        **<?php w('Use base selector name'); ?> 
                                                        <strong>.this-form</strong> <br><?php w('Example'); ?> : <br> 
                                                        <strong>.this-form input[type=text] <br>
                                                        {border-radius: 5px;}</strong>
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <hr class="bg-primary mt-0">
                        <button class="btn my-1 btn-primary btn-sm cfpro-rev-save-setting" type="submit"><?php w('Save'); ?></button> &nbsp;&nbsp;
                        <button class="btn my-1 btn-primary btn-sm   cfpro-rev-reset-setting" type="button"><?php w('Reset Setting'); ?></button>
                    </form>
                </div>
            </div>
    </div>
    <br><br><br><br><br>

</div>


<div id="cfpro-rev-snackbar-admin">added successfully.</div>
<input type="hidden" id="discountGiftbaseurl" value="<?=CFPRODUCT_REV_PLUGIN_URL;?>/">
<input type="hidden" id="cfpro_review_jaxUrl" value="<?=get_option('install_url')?>/index.php?page=ajax">
<input type="hidden" id="giftdiscountinstall_url" value="<?=get_option('install_url')?>/">
<?php register_tiny_editor(array("#cfprorev_gmail_content")) ?>