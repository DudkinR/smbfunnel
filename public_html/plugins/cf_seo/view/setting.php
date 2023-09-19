<?php
global $mysqli;
global $dbpref;
global $app_variant;
$setup_ob=$this->load('setup');
$cfseo_install_url=get_option("install_url");
$pref="smbf_";
	  	$user_id = $_SESSION['user' . get_option('site_token')];
		$access = $_SESSION['access' . get_option('site_token')];
		if($access !== 'admin' ){
			$select_funnels=" SELECT * FROM `".$pref."quick_funnels` as `funnels` 
		INNER JOIN `".$pref."user_funnel` as `uf` 
		ON `funnels`.`id` = `uf`.`funnel_id` 
		WHERE `uf`.`user_id`= ".$user_id;
		}
		else{
		$select_funnels=" SELECT * FROM `".$pref."quick_funnels` WHERE 1";
		}
		//echo $select_funnels;
		$funnels_query=$mysqli->query($select_funnels);
    $funnel_names=[];
    while($funnel=$funnels_query->fetch_assoc()){
      $funnel_names[]=$funnel['name'];
    }
    function a_funnels($funnel_names,$funnels){
      $result=[];
      foreach ($funnels as $funnel) {
        if(in_array($funnel['name'],$funnel_names)){
          $result[]=$funnel;
        }
      }
      return $result;
    }

if(isset($_GET['cfseo_id']))
{
  if( is_numeric( $_GET['cfseo_id'] ) )
  {
    $setup_id = $_GET['cfseo_id'];
  }

  $cfseo_id = $mysqli->real_escape_string($setup_id);
  $table = $dbpref."cfseo_setup"; 
  $returnOptions = $mysqli->query("SELECT * FROM `".$table."` WHERE `id`=".$setup_id );

  if($returnOptions->num_rows > 0)
  {
      $data = $returnOptions->fetch_assoc( );
    
      if( empty( $data ) ){
        $no_permission = $cfseo_install_url."/index.php?page=no_permission";
        header( "Location:".$no_permission."" );
      }
    
      $seo_data = base64_decode( $data['seo_data'] );
      $setup  =  json_decode( $seo_data );
      $cfseo_schema_file = base64_decode($data['schema_org']);
      $cfseo_seperator = ( !empty( $setup->seperator) ) ? $setup->seperator: "cfseo-pipe";
      $cfseo_page_id = ( !empty( $setup->page_id) ) ? $setup->page_id: "";
      $cfseo_title = ( !empty( $setup->title) ) ? stripcslashes($setup->title): "";
    
      
      $cfseo_page_name = $data['page_name'];
      
      $cfseo_descriptions = $setup->description; 
      $cfseo_keyword = $setup->keywords;
    
      $cfseo_icon = ( !empty( $setup->icon) ) ? stripcslashes($setup->icon): "";
      $cfseo_author = ( !empty( $setup->author) ) ? stripcslashes(stripcslashes($setup->author)): "";
      $cfseo_canonical = ( !empty( $setup->canonical ) ) ? stripcslashes($setup->canonical): "";
      $cfseo_robots_value = ( !empty( $setup->robots_value ) ) ? stripcslashes(stripcslashes($setup->robots_value)): "";
      
      $cfseo_custom_meta = $data['custom_meta'];
    
    
      
      $cfseo_enable_og = ( isset( $setup->enable_og) && ($setup->enable_og==1) ) ? true:false;
      $cfseo_facebook_app_id = ( !empty( $setup->facebook_api_id) ) ? $setup->facebook_api_id: "";
      $cfseo_locale = ( !empty( $setup->facebook_app_id) ) ? $setup->facebook_app_id: "";
      $cfseo_article_type = ( !empty( $setup->article_type) ) ? $setup->article_type: "";
      $cfseo_facebook_image_url = ( !empty( $setup->facebook_image_url) ) ? $setup->facebook_image_url: "";
      $cfseo_twitter_default_card = ( !empty( $setup->twitter_default_card ) ) ? $setup->twitter_default_card: "";
      $cfseo_reading_time = ( !empty( $setup->reading_time ) ) ? $setup->reading_time: "";
       
      $cfseo_schema= stripcslashes(trim(stripcslashes($cfseo_schema_file),"\r\n"));
      $cfseo_robots = $setup_ob->getRobotsFile();
  }
 
}
else
{
  $cfseo_seperator = "cfseo-pipe";
  $cfseo_page_id = "";
  $cfseo_page_name = "";
  $cfseo_title = "";
  $cfseo_descriptions = "";
  $cfseo_keyword = "";

  $cfseo_icon = "";
  $cfseo_author = "";
  $cfseo_canonical = "";
  $cfseo_custom_meta = "";
  $cfseo_robots_value="";

  
  $cfseo_schema="";
  $cfseo_sitemap=$cfseo_install_url."/sitemap.xml";
  $cfseo_robots[]='User-agent: *';
  $cfseo_robots[]='Allow: /';
  $cfseo_robots[]="Sitemap: ".$cfseo_sitemap."";
}
?>
<div class="container-fluid">
  <div class="row page-titles mb-4">
    <div class="col-md-5 align-self-center">
      <h4 class="text-themecolor" id="commoncontainerid">CF SEO setting</h4>
    </div>
    <div class="col-md-7 align-self-center text-end">
      <div class="d-flex justify-content-end align-items-center">Create, Edit, Manage Titles</div>
    </div>
  </div>

  <div class='cfseo-tabbed'>
    <div class="cfseo-tabbed-container">
      <a href="#cfseo-link1" class="cfseo-tabbed-tab-links">Robots File</a>
      <a href="#cfseo-link2" class="cfseo-tabbed-tab-links">Schema.org File</a>
      <a href="#cfseo-link3" class="cfseo-tabbed-tab-links">Optional Meta</a>
      <a href="#cfseo-link4" class="cfseo-tabbed-tab-links cfseo-active">General</a>
    </div>
  </div>

  <!-- Tab panes -->

  <div class="cfseo-tabcontent-container p-0">
    <form id="cfseo-add-setting" method="post" action="" class="p-0">
      <input type="hidden" id="cfseo_ajax" name="cfseo_ajax" value="<?php echo  get_option('install_url')."/index.php?page=ajax"; ?>" />
      <input type="hidden" id="cfseo_id" name="cfseo_id" value="<?php echo ((isset($cfseo_id))? $cfseo_id:0); ?>">
      <input type="hidden" id="cfseo_param" name="cfseo_param" value="<?php echo ((isset($cfseo_id))? 'update_cfseo':'save_cfseo'); ?>">
      <div id="cfseo-link1" class="cfseo-tabcontent p-2"><br>
        <div class="row">
          <div class="col-lg-12">
            <div class="mb-3">
              <label class="cfseo-webmaster-label" >Edit robots.txt File</label>
              <span class="cfseo-help" data-bs-toggle="collapse" data-target="#cfseo-robots-text-help"><i class="fa fa-question-circle" aria-hidden="true"></i></span>
              <div id="cfseo-robots-text-help" class="collapse cfseo-collapse">
                Robots.txt is a text file webmasters create to instruct web robots (typically search engine robots) how to crawl pages on their website. The robots.txt file is part of the the robots exclusion protocol (REP), a group of web standards that regulate how robots crawl the web, access and index content, and serve that content up to users.
                <div><a href="https://moz.com/learn/seo/robotstxt" target="_blank"> read more...  </a></div>
              </div>
              
              <textarea rows="15" name="cfseo_robots_file" class="form-control"><?php foreach ($cfseo_robots as $cfseo_robot) { echo str_ireplace(" ", " ", rtrim(stripcslashes($cfseo_robot)))."\r\n";} ?></textarea>
            </div>
          </div>
        </div>
      </div>
      <div id="cfseo-link2" class="cfseo-tabcontent p-2"><br>
        <div class="row">
          <div class="col-lg-12">
            <div class="mb-3">
              <label class="cfseo-webmaster-label" >Add schema.org File</label>
              <span class="cfseo-help" data-bs-toggle="collapse" data-target="#cfseo-schema-help"><i class="fa fa-question-circle" aria-hidden="true"></i></span>
              
              <div id="cfseo-schema-help" class="collapse cfseo-collapse">
                Schema.org provides a collection of shared vocabularies webmasters can use to mark up their pages in ways that can be understood by the major search engines: Google, Microsoft, Yandex and Yahoo! You can use the schema.org vocabulary along with the Microdata, RDFa, or JSON-LD formats to add information to your Web content.
                <div> <a href="https://schema.org/docs/faq.html" target="_blank"> read more...  </a> </div>
              </div>
              <div class="p-2" style="font-size: 0.8em;"><a href="https://technicalseo.com/tools/schema-markup-generator/" target="_blank">Click here  </a>to create schema file or <a href="https://www.rankranger.com/schema-markup-generator" target="_blank">Click here  </a></div> 
              <textarea rows="15" name="cfseo_schema_file" class="form-control" placeholder="Paste Here Schema File"><?= $cfseo_schema; ?></textarea>
            </div>
          </div>
        </div>
      </div>
      <div id="cfseo-link3" class="cfseo-tabcontent p-2"><br>
        <div class="row">
          <div class="col-lg-12">
            <div class="mb-3">
              <label class="cfseo-webmaster-label">Enter Author value</label>
              <input type="text" name="cfseo[author]" value="<?=stripcslashes($cfseo_author); ?>" placeholder="Enter Author Name" class="form-control" />
            </div>
          </div>
          <div class="col-lg-12">
            <div class="mb-3">
              <label class="cfseo-webmaster-label" >Enter Canonical URL </label>
              <span class="cfseo-help" data-bs-toggle="collapse" data-target="#cfseo-canonical-help"><i class="fa fa-question-circle" aria-hidden="true"></i></span>
              <div id="cfseo-canonical-help" class="collapse cfseo-collapse">
                A canonical tag (aka "rel canonical") is a way of telling search engines that a specific URL represents the master copy of a page. Using the canonical tag prevents problems caused by identical or "duplicate" content appearing on multiple URLs.
              </div>
                <input type="text" class="form-control" value="<?=stripcslashes($cfseo_canonical); ?>" name="cfseo[canonical]" placeholder="Enter Canonical URL" />
            </div>
          </div>
          <div class="col-lg-12">
            <div class="mb-3">
              <label class="cfseo-webmaster-label">Robots Value (Use Comma  Between Two Values)</label>
              <span class="cfseo-help" data-bs-toggle="collapse" data-target="#cfseo-robots-help"><i class="fa fa-question-circle" aria-hidden="true"></i></span>
              <div id="cfseo-robots-help" class="collapse cfseo-collapse">
                A robots meta tag is an HTML snippet that tells search engines how to crawl or index a certain page. It’s placed into the &lt;head&gt; section of a web page, and looks like this:
                <p style="color:blue" >&lt;meta name="robots" content="index, follow" /&gt;</p>
                <div><a href="https://developers.google.com/search/reference/robots_meta_tag" target="_blank"> read more...  </a></div>
              </div>
              <input type="text" name="cfseo[robots_value]" value="<?=stripcslashes($cfseo_robots_value); ?>" placeholder="Enter Robots Value, Exp. index, follow" class="form-control" />
            </div>
          </div>
          <div class="col-lg-12">
            <div class="mb-3">
              <label class="cfseo-webmaster-label" >Enter Custom Meta Tag</label>
                <textarea name="cfseo_custom_meta_tag" class="form-control" rows="5" placeholder='Exp. <meta name="googlebot" content="index, follow" />'><?php echo stripcslashes(rtrim(stripcslashes($cfseo_custom_meta),"\\r\\n")); ?> </textarea>
            </div>
          </div>
        </div>
      </div>
      <div id="cfseo-link4" class="cfseo-tabcontent cfseo-show p-2"><br>
        <div class="row">
          <div class="col-lg-12">
            <div class="mb-3">
              <label class="cfseo-webmaster-label" >Enter Page Name </label>
              <input type="text" class="form-control" value="<?= stripcslashes($cfseo_page_name); ?>" id="cfseo-main-page-name" required name="cfseo_page_name" placeholder="Enter Page Name" />
            </div>
          </div>
          <div class="col-lg-12">
            <div class="mb-3">
              <label class="cfseo-webmaster-label">Select Seperator</label>
              <span class="cfseo-help" data-bs-toggle="collapse" data-target="#cfseo-seperator-help"><i class="fa fa-question-circle" aria-hidden="true"></i></span>
              <div id="cfseo-seperator-help" class="collapse cfseo-collapse">
                Choose a symbol to use as your title separator. This will display, for instance, between your post title and site name. Symbols shown will appear in the exact size in search results.
              </div>
              <div  class="border border-primary p-2">
              
                <input type="radio" name="cfseo[seperator]" <?php if($cfseo_seperator=="cfseo-dash") { echo "checked"; } ?>  class="cfseo-seperator" id="cfseo-dash" value="cfseo-dash" />
                <label for="cfseo-dash"   class="cfseo-seperators <?php if($cfseo_seperator=="cfseo-dash") { echo 'cfseo-seperators-active'; } ?>">-</label>
                
                <input type="radio" name="cfseo[seperator]" <?php if($cfseo_seperator=="cfseo-ndash") { echo "checked"; } ?> class="cfseo-seperator" id="cfseo-ndash" value="cfseo-ndash" />
                <label for="cfseo-ndash"  class="cfseo-seperators <?php if($cfseo_seperator=="cfseo-ndash") { echo "cfseo-seperators-active"; } ?>" >_</label>

                <input type="radio" name="cfseo[seperator]" <?php if($cfseo_seperator=="cfseo-plus") { echo "checked"; } ?> class="cfseo-seperator" id="cfseo-plus" value="cfseo-plus" />
                <label for="cfseo-plus"  class="cfseo-seperators  <?php if($cfseo_seperator=="cfseo-plus") { echo 'cfseo-seperators-active'; } ?>" >+</label>

                <input type="radio" name="cfseo[seperator]" <?php if($cfseo_seperator=="cfseo-question") { echo "checked"; } ?> class="cfseo-seperator" value="cfseo-question" id="cfseo-question" />
                <label for="cfseo-question"  class=" cfseo-seperators <?php if( $cfseo_seperator=="cfseo-question" ) { echo 'cfseo-seperators-active'; } ?>">?</label>

                <input type="radio" name="cfseo[seperator]" <?php if($cfseo_seperator=="cfseo-pipe") { echo "checked"; } ?> class="cfseo-seperator" id="cfseo-pipe" value="cfseo-pipe" />
                <label for="cfseo-pipe" class=' cfseo-seperators <?php if( $cfseo_seperator=="cfseo-pipe" ) { echo "cfseo-seperators-active"; } ?>' >|</label>

                <input type="radio" name="cfseo[seperator]" <?php if($cfseo_seperator=="cfseo-colon") { echo "checked"; } ?> class="cfseo-seperator" value="cfseo-colon" id="cfseo-colon" />
                <label for="cfseo-colon" class='cfseo-seperators <?php if( $cfseo_seperator=="cfseo-colon" ) { echo "cfseo-seperators-active"; } ?>' >:</label>

                <input type="radio" name="cfseo[seperator]" <?php if($cfseo_seperator=="cfseo-astric") { echo "checked"; } ?> class="cfseo-seperator" id="cfseo-astric" value="cfseo-astric" />
                <label for="cfseo-astric" class='cfseo-seperators  <?php if( $cfseo_seperator=="cfseo-astric" ) { echo "cfseo-seperators-active"; } ?>' >*</label>

                <input type="radio" name="cfseo[seperator]"<?php if($cfseo_seperator=="cfseo-tilde") { echo "checked"; } ?> class="cfseo-seperator" id="cfseo-tilde" value="cfseo-tilde" />
                <label for="cfseo-tilde" class='cfseo-seperators <?php if( $cfseo_seperator=="cfseo-tilde" ) { echo "cfseo-seperators-active"; } ?>' >~</label>

                <input type="radio" name="cfseo[seperator]" <?php if($cfseo_seperator=="cfseo-lt") { echo "checked"; } ?> class="cfseo-seperator" id="cfseo-lt" value="cfseo-lt" />
                <label for="cfseo-lt" class='cfseo-seperators <?php if( $cfseo_seperator=="cfseo-lt" ) { echo "cfseo-seperators-active"; } ?>' ><</label>

                <input type="radio" name="cfseo[seperator]" <?php if($cfseo_seperator=="cfseo-gt") { echo "checked"; } ?> class="cfseo-seperator" id="cfseo-gt" value="cfseo-gt" />
                <label for="cfseo-gt" class='cfseo-seperators <?php if( $cfseo_seperator=="cfseo-gt" ) { echo "cfseo-seperators-active"; } ?>' >></label>
            
                <input type="radio" name="cfseo[seperator]" <?php if($cfseo_seperator=="cfseo-caret") { echo "checked"; } ?> class="cfseo-seperator" id="cfseo-caret" value="cfseo-caret" />
                <label for="cfseo-caret" class='cfseo-seperators <?php if( $cfseo_seperator=="cfseo-caret" ) { echo "cfseo-seperators-active"; } ?>' >^</label>

                <input type="radio" name="cfseo[seperator]" <?php if($cfseo_seperator=="cfseo-laquo") { echo "checked"; } ?> class="cfseo-seperator" id="cfseo-laquo" value="cfseo-laquo" />
                <label for="cfseo-laquo" class='cfseo-seperators <?php if( $cfseo_seperator=="cfseo-laquo" ) { echo "cfseo-seperators-active"; } ?>' >«</label>

                <input type="radio" name="cfseo[seperator]" <?php if($cfseo_seperator=="cfseo-raquo") { echo "checked"; } ?> class="cfseo-seperator" id="cfseo-raquo" value="cfseo-raquo" />
                <label for="cfseo-raquo" class='cfseo-seperators <?php if( $cfseo_seperator=="cfseo-raquo" ) { echo "cfseo-seperators-active"; } ?>' >»</label>
                
                <input type="radio" name="cfseo[seperator]" <?php if($cfseo_seperator=="cfseo-fdot") { echo "checked"; } ?> class="cfseo-seperator" id="cfseo-fdot" value="cfseo-fdot" />
                <label for="cfseo-fdot" class='cfseo-seperators <?php if( $cfseo_seperator=="cfseo-fdot" ) { echo "cfseo-seperators-active"; } ?>' >•</label>

                <input type="radio" name="cfseo[seperator]" <?php if($cfseo_seperator=="cfseo-dot") { echo "checked"; } ?> class="cfseo-seperator" id="cfseo-dot" value="cfseo-dot" />
                <label for="cfseo-dot" class='cfseo-seperators <?php if( $cfseo_seperator=="cfseo-dot" ) { echo "cfseo-seperators-active"; } ?>' >.</label>
              </div>

            </div>
          </div>
          <div class="col-lg-12 ">
            <div class="mb-3">
              <label class="cfseo-webmaster-label">Select Page</label>
              <select name="cfseo[page_id]"  class="cfseo-select-container form-control cfseo-select">
              <?php 
                $fnls = a_funnels($funnel_names,get_funnels());
                foreach ( $fnls as $f ) 
                {
                  $pages=get_funnel_pages($f['id']);
                  foreach ($pages as $page) {
                    if(!empty($cfseo_page_id) && ($cfseo_page_id==$page['id'])){
                      echo '<option value="'  .  $page['id']  . '" id="cfseo-selected" class="cfseo-selected" data-sel="selected"  rel="icon-temperature">' .  $page['url'] .  ' </option>';
                    }
                  }
                } 
                $fnls = a_funnels($funnel_names,get_funnels());
                foreach ( $fnls as $f ) 
                {
                  $pages=get_funnel_pages($f['id']);
                  foreach ($pages as $page) {
                    $pageId = json_decode(get_option("cfseo_page_ids"));

                    if(!empty($pageId)){
                        if( !in_array( $page['id'], $pageId ) ){
    
                          echo '<option value="'  .  $page['id']  . '" class="cfseo-selected"  data-sel="not-selected" rel="icon-temperature">' .  $page['url'] .  ' </option>';
                        }
                    }else{
                        echo '<option value="'  .  $page['id']  . '" class="cfseo-selected"  data-sel="not-selected" rel="icon-temperature">' .  $page['url'] .  ' </option>';
                    }
                  }
                }
                ?>
              </select>
            </div>
          </div>
          <div class="col-lg-12">
            <div class="mb-3">
              <label class="cfseo-webmaster-label" >Enter Page Title  (max 50-60) </label>
              <span class="cfseo-help" data-bs-toggle="collapse" data-target="#cfseo-title-help"><i class="fa fa-question-circle" aria-hidden="true"></i></span>
              <div id="cfseo-title-help" class="collapse cfseo-collapse">
                Meta title tags are a major factor in helping search engines understand what your page is about, and they are the first impression many people have of your page (max 50-60)
                <?php if( $app_variant == "shopfunnels" ): ?>
                <br /><span class="text-info cfdisp_cursor" onclick="copyText(`{product_title}`)" data-bs-toggle="tooltip" title="Copy to clipboard" >{product_title}</span> Product Title <br>
                <span class="text-info cfdisp_cursor" onclick="copyText(`{collection_title}`)" data-bs-toggle="tooltip" title="Copy to clipboard" >{collection_title}</span> Collection Title <br>
                <span class="text-info cfdisp_cursor" onclick="copyText(`{product_description}`)" data-bs-toggle="tooltip" title="Copy to clipboard" >{product_description}</span> Product Description <br>
                <span class="text-info cfdisp_cursor" onclick="copyText(`{collection_description}`)" data-bs-toggle="tooltip" title="Copy to clipboard" >{collection_description}</span> Collection Description <br>
                <?php endif; ?>
              </div>
                <input type="text" class="form-control" value="<?= stripcslashes($cfseo_title); ?>" id="cfseo-main-title" name="cfseo[title]" placeholder="Enter Title" />
            </div>
          </div>
          <div class="col-lg-12">
            <div class="mb-3">
              <label class="cfseo-webmaster-label">Title Icon URL</label>
              <span class="cfseo-help" data-bs-toggle="collapse" data-target="#cfseo-url-help"><i class="fa fa-question-circle" aria-hidden="true"></i></span>
              <div id="cfseo-url-help" class="collapse cfseo-collapse">
                A favicon (pronounced "fave-icon") is a small, iconic image that represents your website. Favicons are most often found in the address bar of your web browser.
              </div>
              <div class="input-group mb-3">
                <input type="text" name="cfseo[icon]" value="<?= stripcslashes($cfseo_icon); ?>" placeholder="Enter Icon URL" class="form-control" id="cfseo-icon-url">
                <div class="input-group-append">
                  <button class="btn btn-success" onclick="cfSeoGetIcon('#cfseo-icon-url', false)">Get URL</button>
                </div>
              </div>
            </div>
          </div>
          <div class="col-lg-12">
            <div class="mb-3">
              <label class="cfseo-webmaster-label">Enter Page Description (max 155-160)</label>
              <span class="cfseo-help" data-bs-toggle="collapse" data-target="#cfseo-description-help"><i class="fa fa-question-circle" aria-hidden="true"></i></span>
              <div id="cfseo-description-help" class="collapse cfseo-collapse">
                The meta description tag in HTML is the 160 character snippet used to summarize a web page's content. Search engines sometimes use these snippets in search results to let visitors know what a page is about before they click on it. 
              </div>
              <textarea name="cfseo[description]" rows="3" id="cfseo-main-description" placeholder="Enter Page Description" class="form-control"><?php echo stripcslashes(rtrim(stripcslashes($cfseo_descriptions),"\\r\\n")); ?></textarea>
            </div>
          </div>
          <div class="col-lg-12">
            <div class="mb-3">
              <label class="cfseo-webmaster-label">Enter keywords (Use Comma  Between Two Keywords)</span></label>
              <span class="cfseo-help" data-bs-toggle="collapse" data-target="#cfseo-keywords-help"><i class="fa fa-question-circle" aria-hidden="true"></i></span>
              <div id="cfseo-keywords-help" class="collapse cfseo-collapse">
              Keywords are ideas and topics that define what your content is about. In terms of SEO, they're the words and phrases that searchers enter into search engines, also called "search queries." If you boil everything on your page — all the images, video, copy, etc
              </div>
              <textarea name="cfseo[keywords]" rows="3" placeholder="Keyword, Keyword, Keyword, Keyword" class="form-control"><?php echo stripcslashes(rtrim(stripcslashes($cfseo_keyword),"\\r\\n")); ?></textarea>
            </div>
          </div>      
        </div>
      </div>
      <hr />
      <button type="submit" class="btn btn-primary cfseo_save_setting m-3 mt-1 " id="cfseo_save_setting">Save</button>
    </form>
  </div>
</div>

    <script>
        function cfSeoGetIcon(selector, html)
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