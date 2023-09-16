<?php
global $mysqli;
$total_setup=0;
$setup_ob=$this->load('setup');

$data = $setup_ob->getSocialData();
$a_d=[];
$accounts=[];
if(count($data)>0){
  $a_d = json_decode( $data['accounts_data'] );
  $accounts = json_decode( $data['accounts'], true );
}


if( !empty( $data ) ){
  $cfseo_enable_og = ( isset( $a_d->enable_og ) && !empty( $a_d->enable_og ) ) ?  $a_d->enable_og:false;
}

else{
  $cfseo_enable_og=true;
}
$cfseo_facebook_app_id = (!empty( $a_d->facebook_app_id ) ) ? $a_d->facebook_app_id:"";

$cfseo_article_type = ( isset( $a_d->article_type ) && !empty( $a_d->article_type ) )?$a_d->article_type:"";

$cfseo_locale = ( isset( $a_d->locale ) && !empty( $a_d->locale ) )?$a_d->locale:"";

$cfseo_facebook_image_url = ( isset( $a_d->facebook_image_url ) && !empty( $a_d->facebook_image_url ) )?$a_d->facebook_image_url:"";

$cfseo_twitter_default_card = ( isset( $a_d->twitter_default_card ) && !empty( $a_d->twitter_default_card ) )?$a_d->twitter_default_card:"";

$cfseo_reading_time = ( isset( $a_d->reading_time ) && !empty( $a_d->reading_time ) )?$a_d->reading_time:"";

$cfseo_pinterest_v = ( isset( $a_d->pinterest_verification ) && !empty( $a_d->pinterest_verification ) )?$a_d->pinterest_verification:"";

if(!empty($cfseo_pinterest_v))
{
  $cfseo_pinterest_v = stripslashes($cfseo_pinterest_v);
  $cfseo_pinterest_v = rtrim($cfseo_pinterest_v,"/>");
    // Create a new DOMDocument 
  $dom = new DOMDocument(); 
      
    // Load the XML 
  $dom->loadXML("<?xml version=\"1.0\"?> 
  <body> 
    ".$cfseo_pinterest_v."></meta>
  </body>"); 
      
    // Get the strong element 
  $element = $dom->getElementsByTagName('meta'); 
      
    // Get the attribute 
  $cfseo_pinterest_v = $element[0]->getAttribute('content'); 

}else{
  $cfseo_pinterest_v = $cfseo_pinterest_v;
}

?>
<div class="container-fluid">
  <div class="row page-titles mb-4">
    <div class="col-md-5 align-self-center">
      <h4 class="text-themecolor" id="commoncontainerid">CF SEO setting</h4>
    </div>
    <div class="col-md-7 align-self-center text-end">
      <div class="d-flex justify-content-end align-items-center">Create, edit, manage titles</div>
    </div>
  </div>

  <div class='cfseo-tabbed'>
    <div class="cfseo-tabbed-container">
      <a href="#cfseo-link2" class="cfseo-tabbed-tab-links">Enable Social Account</a>
      <a href="#cfseo-link1" class="cfseo-tabbed-tab-links cfseo-active">Account</a>
    </div>
  </div>


  <!-- Tab panes -->
  <div class="cfseo-tabcontent-container p-0" >
  <form method="post" action="" id="cfseo-add-social-accounts">
      <input type="hidden" id="cfseo_ajax" name="cfseo_ajax" value="<?php echo  get_option('install_url')."/index.php?page=ajax"; ?>" />
      <input type="hidden" id="cfseo_social_param" name="cfseo_social_param" value="<?php echo ( ( !empty($accounts) && count($accounts)>0 ) ? 'update_cfseo_social':'save_cfseo_social'); ?>">
      <div id="cfseo-link2" class="cfseo-tabcontent"><br>
        <div class="p-2 ps-3">
          <label>Add Open Graph Data</label>
          <span class="cfseo-help" data-bs-toggle="collapse" data-target="#cfseo_facebook_help" style="cursor:pointer;"><i class="fa fa-question-circle" aria-hidden="true"></i></span>
          <div id="cfseo_facebook_help" class="collapse cfseo-collapse">
            Enable this feature if you want Facebook and other social media to display a preview with images and a text excerpt when a link to your site is shared.
          </div>
          <div class="cfseo-toggle">
            <input id="cfseo-all-social-enable" type="checkbox" <?php if($cfseo_enable_og==true){ echo "checked"; } ?>  name="cfseo[enable_og]" value="1">
            <label for="cfseo-all-social-enable">
              <div class="cfseo-toggle-switch" data-checked="Enabled" data-unchecked="Disabled"></div>
            </label>
          </div>
        </div>
        <hr />
        <div class="p-2 ps-3 w-sm-100 w-75">
          <h6>Facebook </h6>
          <div class="mb-3 pt-4">
            <div class="row">
              <div class="col-lg-3 pt-1">
                <label class="cfseo-webmaster-label">Facebook App Id</label>
              </div>
              <div class="col-lg-9">
                <input type="text" name="cfseo[facebook_app_id]" placeholder="Facebook App Id" value="<?=$cfseo_facebook_app_id; ?>" class="form-control">
              </div>
            </div>
          </div>
          <div class="mb-3 pt-4">
            <div class="row">
              <div class="col-lg-3 pt-1">
                <label class="cfseo-webmaster-label">Article type</label>
              </div>
              <div class="col-lg-9">
                <select class="form-control" name="cfseo[article_type]"> 
                  <option value="none" <?php if($cfseo_article_type=="none"){ echo "selected"; } ?> >None</option>  
                  <option value="article" <?php if($cfseo_article_type=="article"){ echo "selected"; } ?> >Article</option>  
                  <option value="news article" <?php if($cfseo_article_type=="news article"){ echo "selected"; } ?> >News Article</option>  
                  <option value="blog post" <?php if($cfseo_article_type=="blog post"){ echo "selected"; } ?> >Blog Post</option>    
                </select>
              </div>
            </div>
          </div>
          <div class="mb-3 pt-4">
            <div class="row">
              <div class="col-lg-3 pt-1">
                <label class="cfseo-webmaster-label">Local Lanugage <span style="color:red; "> </span> </label>
              </div>
              <div class="col-lg-9">
                <select class="form-control" name="cfseo[locale]"> 
                  <option value="en_US" <?php if($cfseo_locale=="en_US"){ echo "selected"; } ?> >English, USA</option>  
                  <option value="en_Uk" <?php if($cfseo_locale=="en_US"){ echo "selected"; } ?> >English, UK</option>  
                  <option value="hi_IN" <?php if($cfseo_locale=="hi_IN"){ echo "selected"; } ?> >Hindi, India</option>  
                  <option value="nl_NL" <?php if($cfseo_locale=="nl_NL"){ echo "selected"; } ?> >Dutch, Netherlands</option>  
                  <option value="fr_FR" <?php if($cfseo_locale=="fr_FR"){ echo "selected"; } ?> >French, France</option>  
                  <option value="de_DE" <?php if($cfseo_locale=="de_DE"){ echo "selected"; } ?> >Deutsch, Germany</option>  
                  <option value="el_GR" <?php if($cfseo_locale=="el_GR"){ echo "selected"; } ?> >Greek, Greece</option>  
                  <option value="it_IT" <?php if($cfseo_locale=="it_IT"){ echo "selected"; } ?> >Italian, Italy</option>  
                  <option value="ja_JP" <?php if($cfseo_locale=="ja_JP"){ echo "selected"; } ?> >Japanese, Japan</option>  
                  <option value="id_ID" <?php if($cfseo_locale=="id_ID"){ echo "selected"; } ?> >Indonesian, Indonesia</option>  
                  <option value="pt_PT" <?php if($cfseo_locale=="pt_PT"){ echo "selected"; } ?> >Portuguese, Portugal</option>  
                  <option value="pl_PL" <?php if($cfseo_locale=="pl_PL"){ echo "selected"; } ?> >Polish, Poland</option>  
                  <option value="es_ES" <?php if($cfseo_locale=="es_ES"){ echo "selected"; } ?> >Spanish, Spain</option>  
                  <option value="ro_RO" <?php if($cfseo_locale=="ro_RO"){ echo "selected"; } ?> >Romanian, Romania</option>  
                  <option value="ko_KR" <?php if($cfseo_locale=="ko_KR"){ echo "selected"; } ?> >Korean, Korea</option>  
                  <option value="da_DK" <?php if($cfseo_locale=="da_DK"){ echo "selected"; } ?> >Danish, Denmark</option>  
                  <option value="ar_SA" <?php if($cfseo_locale=="ar_SA"){ echo "selected"; } ?> >Arabic</option>  
                </select>
              </div>
            </div>
          </div>
          <div class="mb-3">
            <div class="row">
              <div class="col-lg-3 pt-1">
                <label class="cfseo-webmaster-label">Image URL</label>
              </div>
              <div class="col-lg-9">
                <div class="input-group mb-3">
                <input type="text" name="cfseo[facebook_image_url]" id="cfseo-facebook-image-r" value="<?=$cfseo_facebook_image_url; ?>" class="form-control">
                  <div class="input-group-append">
                    <button class="btn btn-success" onclick="cfSeoOpenMedia('#cfseo-facebook-image-r', false)">Upload</button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <hr />
        <div class="p-2 ps-3 w-sm-100 w-75">
          <h6>Twitter</h6>
          <div class="mb-3 pt-4">
            <div class="row">
              <div class="col-lg-3 pt-1">
                <label class="cfseo-webmaster-label">The Default Card Type To Use</label>
              </div>
              <div class="col-lg-9">
                <select class="form-control" name="cfseo[twitter_default_card]"> 
                  <option value="s" <?php if($cfseo_twitter_default_card=="s"){ echo "selected"; } ?> >Summery</option>  
                  <option value="s_img" <?php if($cfseo_twitter_default_card=="s_img"){ echo "selected"; } ?> >Summery with large image</option>  
                </select>
              </div>
            </div>
          </div>
          <div class="mb-3 pt-4">
            <div class="row">
              <div class="col-lg-3 pt-1">
                <label class="cfseo-webmaster-label">Est. Reading Time</label>
              </div>
              <div class="col-lg-9">
                <input type="text" name="cfseo[reading_time]" placeholder="Est. Reading Time Exp. 6 minutes" value="<?=$cfseo_reading_time; ?>" class="form-control">
              </div>
            </div>
          </div>
        </div>
        <hr />
        <div class="p-2 ps-3 w-sm-100 w-75">
          <h6>Pinterest Setting</h6>
          <p style="font-size:0.8em;">
            If you have already confirmed your website with Pinterest, you can skip the step below.

            To <a target="_blank" href="https://www.pinterest.com/settings/claim">confirm your site with Pinterest</a>, add the meta tag here:
          </p>
          <div class="mb-3 pt-4">
            <div class="row">
              <div class="col-lg-3 pt-1">
                <label class="cfseo-webmaster-label">Pinterest Confirmation</label>
              </div>
              <div class="col-lg-9">
                <input type="text" name="cfseo[pinterest_verification]" value="<?=$cfseo_pinterest_v; ?>"  class="form-control" placeholder="Paste Here Meta Tag">
              </div>
            </div>
          </div>
        </div>
      </div>
      <div id="cfseo-link1" class="cfseo-tabcontent cfseo-show"><br>
        <div class="p-2 ps-3">
          <label class="">Add Your Social Media Profile</label>
            <span class="cfseo-help" data-bs-toggle="collapse" data-target="#cfseo_social_help" style="cursor:pointer;"><i class="fa fa-question-circle" aria-hidden="true"></i></span>
            <div id="cfseo_social_help" class="collapse cfseo-collapse text-primary" style="color:rgba(0,0,0,0.6)">
              To let search engines know which social profiles are associated to this site, enter your social profiles data below. If a Wikipedia page for you or your organization exists, add it too.
            </div>
        </div>
        <hr />
        <div class="p-2 ps-3 w-75">
          <div class="mb-3 pt-4">
            <div class="row">
              <div class="col-lg-3 pt-1">
                <label class="cfseo-webmaster-label">Facebook Page Url</label>
              </div>
              <div class="col-lg-9">
                <input type="text" name="cfseo_accounts[facebook]" value="<?php echo  ( !empty( $accounts['facebook'] ) )?$accounts['facebook']:''; ?>" class="form-control">
              </div>
            </div>
          </div>
          <div class="mb-3">
            <div class="row">
              <div class="col-lg-3 pt-1">
                <label class="cfseo-webmaster-label">Twitter Username</label>
              </div>
              <div class="col-lg-9">
                <input type="text" name="cfseo_accounts[twitter]" value="<?php echo  ( !empty( $accounts['twitter'] ) )?$accounts['twitter']:''; ?>" class="form-control">
              </div>
            </div>
          </div>
          <div class="mb-3">
            <div class="row">
              <div class="col-lg-3 pt-1">
                <label class="cfseo-webmaster-label">Instagram URL</label>
              </div>
              <div class="col-lg-9">
                <input type="text" name="cfseo_accounts[instagram]" value="<?php echo  ( !empty( $accounts['instagram'] ) )?$accounts['instagram']:''; ?>" class="form-control">
              </div>
            </div>
          </div>
          <div class="mb-3">
            <div class="row">
              <div class="col-lg-3 pt-1">
                <label class="cfseo-webmaster-label">LinkedIn URL</label>
              </div>
              <div class="col-lg-9">
                <input type="text" value="<?php echo  ( !empty( $accounts['linkedin'] ) )?$accounts['linkedin']:''; ?>" name="cfseo_accounts[linkedin]" class="form-control">
              </div>
            </div>
          </div>
          <div class="mb-3">
            <div class="row">
              <div class="col-lg-3 pt-1">
                <label class="cfseo-webmaster-label">MySpace URL</label>
              </div>
              <div class="col-lg-9">
                <input type="text" value="<?php echo  ( !empty( $accounts['myspace'] ) )?$accounts['myspace']:''; ?>" name="cfseo_accounts[myspace]" class="form-control">
              </div>
            </div>
          </div>
          <div class="mb-3">
            <div class="row">
              <div class="col-lg-3 pt-1">
                <label class="cfseo-webmaster-label">Pinterest URL</label>
              </div>
              <div class="col-lg-9">
                <input type="text" name="cfseo_accounts[pinterest]" value="<?php echo  ( !empty( $accounts['pinterest'] ) )?$accounts['pinterest']:''; ?>" class="form-control">
              </div>
            </div>
          </div>
          <div class="mb-3">
            <div class="row">
              <div class="col-lg-3 pt-1">
                <label class="cfseo-webmaster-label">YouTube URL</label>
              </div>
              <div class="col-lg-9">
                <input type="text" value="<?php echo  ( !empty( $accounts['youtube'] ) )?$accounts['youtube']:''; ?>" name="cfseo_accounts[youtube]" class="form-control">
              </div>
            </div>
          </div>
          <div class="mb-3">
            <div class="row">
              <div class="col-lg-3 pt-1">
                <label class="cfseo-webmaster-label">Wikipedia URL</label>
              </div>
              <div class="col-lg-9">
                <input type="text" value="<?php echo  ( !empty( $accounts['wikipedia'] ) )?$accounts['wikipedia']:''; ?>" name="cfseo_accounts[wikipedia]" class="form-control">
              </div>
            </div>
          </div>
        </div>
        <hr />
      </div>
      <button type="submit" class="btn btn-primary cfproof_convert_save_setting m-3 mt-1 " id="cfseo_save_setting">Save</button>
    </form>
  </div>
</div>

    <script>
        function cfSeoOpenMedia(selector, html)
        {
            try
            {
                //here calling open media
                openMedia(function(content){
                    try
                    {
                        document.querySelectorAll(selector)[0].value= content;
                    }catch(err){console.log(err);}
                }, html);
            }catch(err){console.log(err)}
        }
    </script>
<?php
//here we are imporing  cf_media
cf_media();
?>