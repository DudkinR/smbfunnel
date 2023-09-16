<?php
global $mysqli;
$total_setup=0;
$setup_ob=$this->load('setup');

$page_count=1;
if(isset($_GET['page_count']) && is_numeric($_GET['page_count']))
{
  $page_count=(int)$_GET['page_count'];
}
ob_start();
  echo '<div class="col-md-2  mb-2">
    '.createSearchBoxBydate().'
    </div>
    <div class="col-md-3">
    '.showRecordCountSelection().'
    </div>
    <div class="col-md-3">'.arranger(array('id'=>'date')).'
    </div>
    <div class="col-md-4">
      <div class="mb-3">
        <div class="input-group input-group-sm">
          <div class="input-group-prepend ">
            <span class="input-group-text"><i class="fas fa-search"></i></span>
          </div>
          <input type="text" class="form-control form-control-sm" placeholder="'.t('Enter page name, page url').'" onkeyup="searchPaymentMethods(this.value)">
        </div>
      </div>
  </div>';
$table_manager=ob_get_clean();
$max_leads_limit=(int)get_option('qfnl_max_records_per_page');
$total_setups=$setup_ob->getSetupsCount( );
$setups=$setup_ob->getAllSetups($total_setups, $max_leads_limit, $page_count);

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
    <a href="#cfseo-link2" class="cfseo-tabbed-tab-links">Webmaster Tools</a>
    <a href="#cfseo-link3" class="cfseo-tabbed-tab-links cfseo-active">Dashboard</a>
  </div>
</div>


  <!-- Tab panes -->
  <div class="cfseo-tabcontent-container p-0">
  <div id="cfseo-link2" class="cfseo-tabcontent"><br>
      <?php 
          $webmasters = $setup_ob->getWebmaster();
       ?>
      <form action="" method="post" id="cfseo-add-webmaster-tools">
        <input type="hidden" id="cfseo_webmaster_param" name="cfseo_webmaster_param" value="<?php echo ( ( !empty($webmasters) && count( $webmasters ) >0 ) ? 'update_cfseo_webmaster':'save_cfseo_webmaster'); ?>">

        <div class="p-2 ps-3">
          <label class="">Webmaster verification tools</label>
            <span class="cfseo-help" data-bs-toggle="collapse" data-target="#demo" style="cursor:pointer;"><i class="fa fa-question-circle" aria-hidden="true"></i></span>
            <div id="demo" class="collapse cfseo-collapse text-primary" style="color:rgba(0,0,0,0.6)">
            Verify with different webmaster tools. This feature will add a verification meta tag on your home page. Follow the links to the different Webmaster Tools and look for instructions for the meta tag verification method to get the verification code. If your site is already verified, you can just forget about these.
            </div>
          </div>
          <hr />
          <div class="p-2 ps-3">
           See your xml sitemap <a href="<?php get_option("install_url")?>sitemap.xml" target="_blank" class="cfseo-xml-sitemap">click here</a>
         </div>
        <div class="p-2 ps-3 w-75">
          <div class="mb-3 pt-4">
            <div class="row">
              <div class="col-xl-3 pt-1">
                <label class="cfseo-webmaster-label">Google verification meta tag</label>
              </div>
              <div class="col-xl-9">
                <input type="text" value="<?php echo  ( !empty( $webmasters['google'] ) )?$webmasters['google']:''; ?>" name="cfseo_google_verification" class="form-control">
                <p class="cfseo-webmaster-p" style="font-size: 0.7em !important;">Get your Google verification code in <a target="_blank" href="https://www.google.com/webmasters/verification/verification?hl=en&amp;tid=alternate&amp;siteUrl=<?php echo urlencode(get_option('install_url')); ?>" rel="noopener noreferrer">Google Search Console</a>.</p>
              </div>
            </div>
          </div>
          <div class="mb-3">
            <div class="row">
              <div class="col-lg-3 pt-1">
                <label class="cfseo-webmaster-label">Bing verification meta tag</label>
              </div>
              <div class="col-lg-9">
                <input type="text" value="<?php echo  ( !empty( $webmasters['bing'] ) )?$webmasters['bing']:''; ?>"  name="cfseo_bing_verification" class="form-control">
                <p class=" cfseo-webmaster-p "  style="font-size: 0.7em !important;"> Get your Bing verification code in <a target="_blank" href="https://www.bing.com/toolbox/webmaster/#/Dashboard/" rel="noopener noreferrer">Bing Webmaster Tools</a>.</p>
              </div>
            </div>
          </div>
          <div class="mb-3">
            <div class="row">
              <div class="col-lg-3 pt-1">
                <label class="cfseo-webmaster-label">Yandex verification meta tag</label>
              </div>
              <div class="col-lg-9">
                <input type="text" value="<?php echo  ( !empty( $webmasters['yandex'] ) )?$webmasters['yandex']:''; ?>" name="cfseo_yandex_verification" class="form-control">
                <p class=" cfseo-webmaster-p"  style="font-size: 0.7em !important;">Get your Yandex verification code in <a target="_blank" href="https://webmaster.yandex.com/sites/add/" rel="noopener noreferrer">Yandex Webmaster Tools</a>.</p>
              </div>
            </div>
          </div>
          <div class="mb-3">
            <div class="row">
              <div class="col-lg-3 pt-1">
                <label class="cfseo-webmaster-label">Baidu verification meta tag</label>
              </div>
              <div class="col-lg-9">
                <input type="text" value="<?php echo ( !empty( $webmasters['baidu'] ) )?$webmasters['baidu']:''; ?>" name="cfseo_baidu_verification" class="form-control">
                <p class="cfseo-webmaster-p"  style="font-size: 0.7em !important;">Get your Baidu verification code in <a target="_blank" href="https://ziyuan.baidu.com/site/siteadd" rel="noopener noreferrer">Baidu Webmaster Tools</a>.</p>
              </div>
            </div>
          </div>
        </div>
        <hr />
        <button type="submit" class="btn btn-primary cfseo_save m-3 mt-1 " id="cfseo_save_setting">Save</button>
      </form>
    </div>
    <div id="cfseo-link3" class="cfseo-tabcontent cfseo-show"><br>
      <div class="card pb-2  br-rounded">
      <div class="card-body pb-2">
        <div class="row">
          <?=$table_manager;  ?>
          <div class="col-sm-12 nopadding">
            <div class="table-responsive">
              <table class="table table-striped">
                <thead>
                  <tr>
                    <th>#</th>
                    <th>Page Name</th>
                    <th>Page url</th>
                    <th>Added On</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody id="keywordsearchresult">
                  <!-- keyword search -->
                  <?php 
                  $count=1;
                  if(isset($_GET['page_count']))
                  {
                    $page_count=(int)$_GET['page_count'];
                    $count=($page_count*$max_leads_limit)-$max_leads_limit;
                    ++$count;
                  }
               
                  if( count($setups) > 0 )
                  {
                    $install_url=get_option("install_url");
                    foreach( $setups as $data ){
                  ?>  <tr>
                        <td><?=$count; ?></td>

                        <td><?= $data['page_name']; ?></td>
                        <td><a href="<?=$data['page_url']; ?>" target="_blank"><?=$data['page_url']; ?></a></td>
                        <td><?= date('d-M-y h:ia', strtotime($data['added_on'])); ?></td>
                        <td>
                          <a data-bs-toggle="tooltip" title="Edit setup"  href="index.php?page=cfseo_setting&cfseo_id=<?=$data['id'] ?>">
                            <i class="fas fa-edit text-primary"></i>
                              &nbsp;
                          </a>
                          </button>
                          <form id="cfseo-delete-setting" method="post"  action="" class="p-0 d-inline inline-form">
                            <input type="hidden" name="cfseo_param" value="delete_cfseo" />
                            <input type="hidden" name="cfseo_id" value="<?=$data['id'] ?>" />
                            <button data-bs-toggle="tooltip" title="Delete setup"  class="cfseo_delete btn unstyled-button" type="submit"><i class="fas fa-trash text-danger"></i> </button>
                        </td>
                      </tr>
                    <?php
                    $count++;
                  }
                }
                    ?>
                      <tr>
                        <td colspan="6" class="total-data" ><center> Total setups: <?=$total_setups; ?></center></td>
                      </tr>
                <!-- /keyword search -->
                </tbody>
              </table>
            </div>
          </div>
        </div>
        <div class="col-md-12 row nopadding">
          <div class="col-sm-6 mt-2">
            <?php
              $next_page_url="index.php?page=cfseo_dashboard&page_count";
              $page_count=( $page_count < 2 )? 0: $page_count;
              echo createPager( $total_setups,  $next_page_url, $page_count );
            ?> 
          </div>
          <div class="col-sm-6 text-end mt-2"> 
            <a href="index.php?page=cfseo_setting" class="btn theme-button"><i class="fas fa-pencil-alt"></i> Create New</a>
          </div>  
        </div>
      </div>
  </div>       
    </div>
  </div>
</div>

<input type="hidden" id="cfseo_ajax" value="<?php echo  get_option('install_url')."/index.php?page=ajax"; ?>">
<script>
function searchPaymentMethods(search)
{
    var ob=new OnPageSearch(search,"#keywordsearchresult");
    ob.url=window.location.href;
    ob.search();
}
</script>
