<?php
global $mysqli;
global $app_variant;
$total_reviews=0;
$reviews_ob=$this->load('setup');

// Check app
$app_variant = isset($app_variant)?$app_variant:"shopfunnels";
if( $app_variant == "shopfunnels" ){
    $students="customer";
}
elseif( $app_variant == "cloudfunnels" ){
    $students="member";
}
elseif( $app_variant == "coursefunnels" ){
    $students="student";
}

$all_products = $reviews_ob->getProdcuts(); 
$install_url =get_option("install_url");
$page_count=1;

if(isset($_GET['page_count']) && is_numeric($_GET['page_count']))
{
    $page_count=(int)$_GET['page_count'];
}
/*
  ******* All reviews filter get parameter *************
*/ 
$product_id= (int) (isset( $_GET['cfprorev_product_id'] )    && is_numeric( $_GET['cfprorev_product_id'] ) ) ?$mysqli->real_escape_string(  $_GET['cfprorev_product_id'] ):false;
$rating= (int) (isset( $_GET['cfprorev_rating'] )    && is_numeric( $_GET['cfprorev_rating'] ) ) ?$mysqli->real_escape_string(  $_GET['cfprorev_rating'] ):false;
$min_rat = (int) (isset( $_GET['cfprorev_min_rating'] )    && is_numeric( $_GET['cfprorev_min_rating'] ) ) ?$mysqli->real_escape_string(  $_GET['cfprorev_min_rating'] ):0;
$max_rat = (int) (isset( $_GET['cfprorev_max_rating'] )    && is_numeric( $_GET['cfprorev_max_rating'] ) ) ?$mysqli->real_escape_string(  $_GET['cfprorev_max_rating'] ):0;
$summary= (int) (isset( $_GET['cfprorev_summary'] ) ) ?$mysqli->real_escape_string(  $_GET['cfprorev_summary'] ):false;
$filter= (int) (isset( $_GET['cfprorev_filter_review'] )) ?$mysqli->real_escape_string(  $_GET['cfprorev_filter_review'] ):false;

// ************Filter Get Parameter end *************

// ************Filter form start *************
ob_start();
echo '<div class="col-lg-2"> '.createSearchBoxBydate().' </div>
      <div class="col-lg-2 my-3 my-lg-0  ">
        <div class="position-relative cfpro-rev">
          <button class="w-100  btn  dropdown-toggle btn-sm btn-block"  id="cfpro-rev-filter-btn">'.t('Filter reviews').'</button>
          <div class="mx-auto collapse " id="cfpro-rev-open-filter">
            <div class="mb-3">
              <select  id="cfpro-rev-selec-product" class="form-control form-control-sm"">
                <option value="all">'.t('All Product').'</option>';
                foreach($all_products as $all_product)
                {
                  if( $product_id == $all_product['id'] )
                  {
                    echo '<option value='.$all_product['id'].' selected>'.$all_product['title'].'</option>';
                  }else{
                    echo '<option value='.$all_product['id'].' >'.$all_product['title'].'</option>';
                  }
                }
              echo '</select>
            </div>
            <div  class="mb-3">
              <select id="cfpro-rev-filter-summary" class="form-control form-control-sm">
                <option value="all" '. ($summary==false?"selected":"") .' >'.t('Filter with summary').'</option>
                <option value="20" '.(($summary==20)?"selected":"").'>'.t('Summary above 20 character').'</option>
                <option value="50" '.(($summary==50)?"selected":"").'>'.t('Summary above 50 character').'</option>
                <option value="100" '.(($summary==100)?"selected":"").'>'.t('Summary above 100 character').'</option>
                <option value="200" '.(($summary==200)?"selected":"").'>'.t('Summary above 200 character').'</option>
                <option value="500" '.(($summary==500)?"selected":"").'>'.t('Summary above 500 character').'</option>
              </select>
            </div> 
            <div class="mb-3">
              <select id="cfpro-rev-select-rating" class="form-control form-control-sm">
                <option value="all" '.(($rating==false)?"selected":"").'>'.t('All').'</option>
                <option value="5" '.(($rating==5)?"selected":"").'>5 '.t('Rating').'</option>
                <option value="4.5" '.(($rating==4.5)?"selected":"").'>4.5 '.t('Rating').'</option>
                <option value="4" '.(($rating==4)?"selected":"").'>4 '.t('Rating').'</option>
                <option value="3.5" '.(($rating==3.5)?"selected":"").'>3.5 '.t('Rating').'</option>
                <option value="3" '.(($rating==3)?"selected":"").'>3 '.t('Rating').'</option>
                <option value="2.5" '.(($rating==2.5)?"selected":"").'>2.5 '.t('Rating').'</option>
                <option value="2" '.(($rating==2)?"selected":"").'>2 '.t('Rating').'</option>
                <option value="1.5" '.(($rating==1.5)?"selected":"").'>1.5 '.t('Rating').'</option>
                <option value="1" '.(($rating==1)?"selected":"").'>1 '.t('Rating').'</option>
                <option value="0.5" '.(($rating==0.5)?"selected":"").'>0.5 '.t('Rating').'</option>
              </select>
            </div>
            <div  class="mb-3">
            <select id="cfpro-rev-filter-reivews" class="form-control form-control-sm">
              <option value="all" '. ($filter=="all"?"selected":"") .' >'.t('All reviews').'</option>
              <option value="r" '. ($filter=="r"?"selected":"") .'>'.t('Read reviews').'</option>
              <option value="ur" '. ($filter=="ur"?"selected":"") .'>'.t('Unread reviews').'</option>
              <option value="ap" '. ($filter=="ap"?"selected":"") .'>'.t('Approved reviews').'</option>
              <option value="uap" '. ($filter=="uap"?"selected":"") .'>'.t('Unapproved reviews').'</option>
            </select>
            </div> 
            <div class="mb-3">
            <label class="text-white">'.t('Rating between').'</label>
              <div class="input-group mb-3">
                <input type="number" class="form-control" value="'.$min_rat.'" min="0" max="5" id="cfpro-rev-min-rating" style="border-bottom:1px solid;" name="min-rating" placeholder="'.t('min').'" />
                <input type="number" class="form-control" value="'.$max_rat.'" min="0" max="5" id="cfpro-rev-max-rating" style="border-bottom:none" name="max-rating" placeholder="'.t('max').'" />
                <div class="input-group-append">
                  <button class="btn btn-primary" id="cfpro-rev-enter-rating" type="button">'.t('Search').'</span>
                </div>
              </div>
            </div>
            <div class="mb-3">
            <button class="btn btn-primary" id="cfpro-rev-clear" type="button">'.t('Clear').'</span>
            </div>
          </div>
        </div>
      </div>
      <div class="col-lg-3"> '.showRecordCountSelection().' </div>
      <div class="col-lg-2">'.arranger(array('id'=>'date')).' </div>
      <div class="col-lg-3 ">
        <div class="mb-3">
          <div class="input-group input-group-sm">
            <div class="input-group-prepend ">
              <span class="input-group-text"><i class="fas fa-search"></i></span>
            </div>
            <input type="text" class="form-control form-control-sm" placeholder="'.t('Search with product name, '.$students.' name, summary').'" onkeyup="searchPaymentMethods(this.value)">
          </div>
        </div>
      </div>';
$table_manager=ob_get_clean();
// ************Filter form End *************

// Fetch record *******************
$max_leads_limit=(int)get_option('qfnl_max_records_per_page');
$total_reviews=$reviews_ob->getReviewsCount( $product_id );
$reviews=$reviews_ob->getAllReviews( $total_reviews, $max_leads_limit, $page_count, $product_id,$rating,$min_rat,$max_rat,$summary,$filter );
// Fetch record End *******************    

?>
<div class="container-fluid">
  <div class="row page-titles mb-4">
      <div class="col-md-5 align-self-center">
        <h4 class="text-themecolor" id="commoncontainerid"><img src='<?=CFPRODUCT_REV_PLUGIN_URL_URL; ?>/assets/image/f9.png' alt='Review' /> <?php w('Manage Reviews'); ?></h4>
      </div>
      <?php if($product_id): ?>
        <div class="col-md-7  text-end">
        <button type="button" class="btn btn-primary" data-bs-toggle="collapse" data-target="#collapseExample" ><?php w('Shortcodes'); ?></button>
        </div>
      <?php elseif($app_variant=="shopfunnels"):?>
        <div class="col-md-7  text-end">
        <button type="button" class="btn btn-primary" data-bs-toggle="collapse" data-target="#collapseExample" ><?php w('Shortcodes'); ?></button>
        </div>
      <?php endif; ?>
  </div>
  <div class="collapse" id="collapseExample">
    <div class="card card-body">
        <?php if($product_id ): ?>
          <h5><?php w('Use these shortcodes to show reviews on any page'); ?></h5>
          <p class="p-3 mt-1 rounded bg-info text-white">
            <span class="text-white">  <strong style="cursor:pointer;" onclick="copyText(`[cfproduct_reviews  id=<?= $product_id; ?> show='all']`)" data-bs-toggle="tooltip" title="<?php w('Copy to clipboard'); ?>">[cfproduct_reviews  id=<?php echo $product_id; ?> show='all']</strong>  </span>&nbsp; <?= t('show all reviews'); ?>.
          </p>
          <p class="p-3 mt-1 rounded bg-info text-white">
            <span class="text-white">  <strong style="cursor:pointer;" onclick="copyText(`[cfproduct_reviews  id=<?= $product_id; ?> show='approved']`)" data-bs-toggle="tooltip" title="<?php w('Copy to clipboard'); ?>">[cfproduct_reviews  id=<?php echo $product_id; ?> show='approved']</strong>  </span>&nbsp; <?= t('show only approved reviews'); ?>.
          </p>
          <p class="p-3 mt-1 rounded bg-info text-white">
            <span class="text-white">  <strong style="cursor:pointer;" onclick="copyText(`[cfproduct_reviews  id=<?= $product_id; ?>  read='yes']`)" data-bs-toggle="tooltip" title="<?php w('Copy to clipboard'); ?>">[cfproduct_reviews  id=<?php echo $product_id; ?> read='yes']</strong>  </span>&nbsp; <?= t('show only mark as read reviews'); ?>.
          </p>
          <p class="p-3 mt-1 rounded bg-info text-white">
            <span class="text-white">  <strong style="cursor:pointer;" onclick="copyText(`[cfproduct_reviews  id=<?= $product_id; ?> show='approved' read='yes']`)" data-bs-toggle="tooltip" title="<?php w('Copy to clipboard'); ?>">[cfproduct_reviews  id=<?php echo $product_id; ?> show='approved' read='yes']</strong>  </span>&nbsp; <?= t('show only mark as read  and approved reviews'); ?>.
          </p>
        <?php elseif($app_variant=="shopfunnels"): ?>
        <h5><?php w('Use these shortcodes to show reviews on any page'); ?></h5>
        <p class="p-3 mt-1 rounded bg-info text-white">
          <span class="text-white">  <strong style="cursor:pointer;" onclick="copyText(`[cfproduct_reviews  show='all']`)" data-bs-toggle="tooltip" title="<?php w('Copy to clipboard'); ?>">[cfproduct_reviews  show='all']</strong>  </span>&nbsp; <?= t('show all reviews'); ?>.
        </p>
        <p class="p-3 mt-1 rounded bg-info text-white">
          <span class="text-white">  <strong style="cursor:pointer;" onclick="copyText(`[cfproduct_reviews  show='approved']`)" data-bs-toggle="tooltip" title="<?php w('Copy to clipboard'); ?>">[cfproduct_reviews  show='approved']</strong>  </span>&nbsp; <?= t('show only approved reviews'); ?>.
        </p>
        <p class="p-3 mt-1 rounded bg-info text-white">
          <span class="text-white">  <strong style="cursor:pointer;" onclick="copyText(`[cfproduct_reviews  read='yes']`)" data-bs-toggle="tooltip" title="<?php w('Copy to clipboard'); ?>">[cfproduct_reviews  read='yes']</strong>  </span>&nbsp; <?= t('show only mark as read reviews'); ?>.
        </p>
        <p class="p-3 mt-1 rounded bg-info text-white">
          <span class="text-white">  <strong style="cursor:pointer;" onclick="copyText(`[cfproduct_reviews  show='approved' read='yes']`)" data-bs-toggle="tooltip" title="<?php w('Copy to clipboard'); ?>">[cfproduct_reviews  show='approved' read='yes']</strong>  </span>&nbsp; <?= t('show only mark as read  and approved reviews'); ?>.
        </p>
        <?php endif; ?>
    </div>
  </div>
  <div class="row">
      <!-- Filter Table Start -->
      <?=$table_manager;  ?>
      <!-- Filter Table End -->
      <div class="col-sm-12">
        <div class="pt-3 pb-1">
          <div class="row">
            <div class="col-md-4 px-1 pl-3 ">
              <div class="row">
                  <div class="col-md-8 m-0 p-0 pl-3">
                    <select name="" id="cfpro-rev-bulk-review-action" class="form-control form-control-sm ms-4">
                      <option value="-1"><?= t('Action'); ?></option>
                      <option value="del"><?= t('Delete'); ?></option>
                      <option value="re"><?= t('Mark as read'); ?></option>
                      <option value="unre"><?= t('Mark as unread'); ?></option>
                      <option value="uap"><?= t('Mark as unapproved'); ?></option>
                      <option value="ap"><?= t('Mark as approved'); ?></option>
                    </select>
                  </div>
                  <div class="col-md-4 m-0 p-0 pl-1"> 
                    <button class="btn btn-outline-secondary d-inline btn-sm ms-4" id="cfpro-rev-bulk-review-btn"><?= t('Apply'); ?></button>
                  </div>
                  <div class="col-md-12">
                  <span class="pb-1 ms-4 text-danger cfpro-rev-bulk-show">
                    <?= t('Please choose one option.'); ?>
                  </span>
                  </div>
              </div>
              
            </div>
          </div>
        </div>
      </div>
  </div>
  <div class="card pb-2  br-rounded">
      <div class="card-body pb-2">
        <div class="row">
          <div class="col-sm-12 nopadding">
            <div class="table-responsive">
              <!-- Reviews Record Table -->
              <table class="table table-striped">
                <thead>
                  <tr>
                    <th width="3%">#</th>
                    <th  width="20%">
                      <span class="cfpro-rev-custom-check"> <input type="checkbox"  id='cfpro-rev-checkforall' > <label for="cfpro-rev-checkforall"></label> </span>
                      <?= ucfirst(t($students))." " ?><?=t('Name'); ?>
                    </th>
                    <th><?= t('Product Name'); ?></th>
                    <th><?= t('Rating'); ?></th>
                    <th><?= t('Summary'); ?></th>
                    <th><?= t('Media'); ?></th>
                    <th><?= t('Added on'); ?></th>
                  </tr>
                </thead>

                <tbody id="reviewkeywordsearchresult">
                  <!-- keyword search -->
                  <?php 
                  $count=1;
                  if(isset($_GET['page_count']))
                  {
                    $page_count=(int)$_GET['page_count'];
                    $count=($page_count*$max_leads_limit)-$max_leads_limit;
                    ++$count;
                  }
                  if( count( $reviews ) > 0 )
                  {
                    $counting=0;
                    foreach( $reviews as $data ){

                      $counting++;
                      $revid  = $data['id'];
                      $review = nl2br($data['summary']);
                      $previd = cf_enc( $revid, "encrypt" );
                      $rating =  $data['rating'];
                      if( $data['approved'] == 1){
                        $apr_cls="";
                      }
                      else{
                        $apr_cls="cfpro-rev-approved";
                      }
                    
                    ?>    
                      <tr class='cfpro-rev-hover-review  <?=$apr_cls; ?>  cfpro-rev-parent-reviews' id='cfpro-rev-review-row-<?= $revid ?>'  >
                        <td width="3%"><?= $counting; ?></td>
                        <td  width="12%">
                        <span class="cfpro-rev-custom-check">
                          <input type="checkbox" class="cfpro-rev-bulk-check" value="<?=$revid ?>" id='cfpro-rev-bulk-check-<?=$previd ?>'>
                          <label class="cfpro-rev-bulk-check-for" for="cfpro-rev-bulk-check-<?=$previd ?>"><?php echo $data['name']; ?> <span class="text-info  cfpro-rev-read-check text-sm" style="opacity:<?= ( $data['readed']=='1')?'1':'0'; ?>"><i class='fas fa-check'></i></span></label>
                        </span>
                        </td>
                        <td width="12%"><?= $data['product_title'] ?></td>
                        <td  width="12%">
                          <span class="cfpro-rev-my-rating"></span>
                          <div class="cfpro-rev-my-rating">
                          <?php 
                            $not_check_rating = 5-$rating;
                              if( $not_check_rating > 0)
                                {
                                  for($i=1; $i<=$not_check_rating; $i++)
                                  {
                                    echo '<input class="cfpro-rev-star cfpro-rev-star-not cfpro-rev-star-'.$i.'" id="star-'.$revid.$i.'"  type="radio"  readonly/>
                                    <label class="cfpro-rev-star cfpro-rev-star-not cfpro-rev-star-'.$i.'" for="star-'.$revid.$i.'"></label>';
                                  }
                                }
                              for( $i = $not_check_rating+1; $i<=5; $i++)
                              {
                                echo '<input class="cfpro-rev-star cfpro-rev-star-'.$i.'" id="star-'.$revid.$i.'" checked type="radio"  readonly/>
                                <label class="cfpro-rev-star cfpro-rev-star-'.$i.'" for="star-'.$revid.$i.'"></label>';
                              }
                            ?>
                          </div>
                        </td>
                        <td width="33%">
                          <div class="cfpro-rev-comment-text">
                          <?php 
                            $str = $reviews_ob->readMore( $review,80 );
                            if( $str['status']  ) { ?>
                              <span class="cfpro-rev-comment-first-text"><?= $str['readmore']; ?><span class="cfpro-rev-comment-dot">...</span></span><a href="javascript:void(0)" class="cfpro-rev-read-more-review"><?= t('Read More')?></a><span class="cfpro-rev-comment-second-text"><?= $str['string']; ?></span>
                              <?php
                            }else{
                              echo $str['string'];
                            }
                            ?>
                          </div>
                          <div class="cfpro-rev-actions p-1">
                            <a href="javascript:void(0)" data-id="<?= $revid ?>"  data-appr="<?= $data['approved']; ?>" class="text-primary cfprov-rev-appr-review text-sm"><?= t(( $data['approved']=="1")?"Unapproved":"Approved"); ?></a> |
                            <a href="javascript:void(0)" data-id="<?= $revid ?>"   class="text-success  cfpro-rev-delete-rev text-sm"><?= t('Delete'); ?></a> |
                            <a href="javascript:void(0)" data-id="<?= $revid ?>"  data-readed="<?= $data['readed']; ?>" class="text-info  cfpro-rev-read-review text-sm"><?= t(( $data['readed']=="1")?"Mark as unread":"Mark as read"); ?></a>
                          </div>
                        </td>
                        <td width="5%" class='text-end'>
                          <?php
                            $medias= json_decode( $data['media'] );
                            if( count( $medias ) > 0 )
                            { ?>
                              <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-target="#cfpro-rev-image-model-<?=$counting?>"> <?= t('View Media'); ?></button>
                              <div class="modal fade slideshowcont" id="cfpro-rev-image-model-<?=$counting?>" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
                                <div class="modal-dialog " >
                                  <div class="modal-content">
                                    <div class="modal-header">
                                      <h5 class="modal-title" id="exampleModalLabel"><?php w('Review File(s)'); ?></h5>
                                      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span> </button>
                                    </div>
                                    <div class="modal-body cfpro-reviews-media">
                                      <div class="slideshow-container">
                                        <?php
                                        $i=1;

                                        foreach( $medias as $media )
                                        {
                                          $img    = ['jpg', 'jpeg' , 'png', 'gif', 'svg'];
                                          $aud    = ['mp3', 'wma', 'aac', 'wav', 'flac','ogv'];
                                          $vid    = ['flv', 'mp4', 'm3u8', 'ts', '3gp', 'mov', 'avi', 'wmv'];
                                          $ht='';
                                          if(in_array(strtolower($media->ext),$img) )
                                          {
                                            ?>
                                            <div class="mySlides mySlides-img cfade" style="display:<?=($i==1?'block':'none'); ?>" >
                                              <div class="numbertext"><?=$i;?> / <?= count( $medias ); ?></div>
                                              <img src="<?=$media->name?>" class="img-fluid">
                                            </div>
                                            <?php
                                          }
                                          else if( in_array(strtolower($media->ext),$aud) )
                                          {
                                            ?>
                                            <div class="mySlides mySlides-audio cfade" style="display:<?=($i==1?'block':'none'); ?>">
                                              <div class="numbertext"><?=$i;?> / <?= count( $medias ); ?></div>
                                              <audio  width="100%" height="auto" controls >
                                                <source src="<?=$media->name?>" type="audio/<?=$media->ext?>">
                                                Your browser does not support the video tag.
                                              </audio>
                                            </div>
                                          
                                            <?php
                                            
                                          }
                                          else if( in_array(strtolower($media->ext),$vid) )
                                          {
                                            ?>
                                            <div class="mySlides mySlides-video cfade" style="display:<?=($i==1?'block':'none'); ?>">
                                              <div class="numbertext"><?=$i;?> / <?= count( $medias ); ?></div>
                                                <video  width="100%" height="auto" controls >
                                                  <source src="<?=$media->name?>" type="video/<?=$media->ext?>">
                                                  Your browser does not support the video tag.
                                                </video>
                                            </div>
                                            <?php
                                          }
                                          $i++;
                                        }
                                        ?>
                                        
                                      </div>
                                      <br>
                                      <div style="text-align:center" class="position-relative">
                                        <a class="cfpro-rev-prev" onclick="plusSlides(-1,<?=$counting?>)">❮</a>
                                        <a class="cfpro-rev-next" onclick="plusSlides(1,<?=$counting?>)">❯</a>
                                      </div>
                                    </div>
                                  </div>
                                </div>
                              </div>
                          <?php
                          }
                          else{
                            echo '<button class="btn  btn-sm btn-info">&nbsp;&nbsp;'.t('No Media').'&nbsp;&nbsp;</button>';
                          }  
                          ?>      
                        </td>
                        <td width="15%" class='text-end'><?php echo date( "d-M-Y h:ia", strtotime($data['added_on']));  ?></td>
                      </tr>
                    <?php 
                    }
                  }
                  ?>
                  <!-- /keyword search -->
                </tbody>
                <tfoot>
                    <tr>
                      <td colspan="7" class="total-data" ><center> <?= t('Total Reviews'); ?>: <?=$total_reviews; ?></center></td>
                    </tr>
                </tfoot>
              </table>
               <!-- Reviews Record Table End -->
            </div>
          </div>
        </div>
        <div class="col-md-12 row nopadding">
          <div class="col-sm-6 mt-2">
            <?php
              $next_page_url="index.php?page=cfproduct_review_all&page_count";
              $page_count=($page_count<2)? 0:$page_count;
              echo createPager($total_reviews,$next_page_url,$page_count);
            ?> 
          </div>
        </div>
      </div>
  </div>  
</div>
<input type="hidden" id="cfpro_rev_ajax" value="<?php echo  get_option('install_url')."/index.php?page=ajax"; ?>">
<!-- Modal -->
<!-- The cfpro_rev_drip_model -->
<div id="cfpro_rev_drip_delete_assign" class="cfpro_rev_drip_model" style="border-radius:5px">
  <!-- cfpro_rev_drip_model content -->
  <div class="cfpro_rev_model_w_anim">
    <div class="alert alert-info pb-1 mb-0  ">
      <span class="cfpro-rev-close-drip cfpro-rev-close-drip-btn">&times;</span>
      <h5 id="cfpro-rev-delete-he "><?= t('Please Confirm'); ?></h5>
    </div>
    <div class="cfpro_rev_drip_model-body py-3 cfpro_rev_drip_model-del-body">
     <?= t('Do you really want to delete reviews?'); ?>
    </div>
    <input type="hidden" id="cfpro-rev-delete-review" value="">
    <input type="hidden" id="cfpro-rev-review-bulkaction" value="">
    <input type="hidden" id="cfpro-rev-review-bulkval" value="">
    <div class="modal-footer">
          <button type="button" class="btn btn-primary btn-sm cfpro-rev-close-drip-btn"><?= t('Cancel'); ?></button>
          <button type="button" class="btn btn-danger btn-sm cfpro-rev-remove-review"><?= t('Delete'); ?></button>
    </div>
  </div>
</div>
<!--Model end -->

<div id="cfpro-rev-snackbar-admin"><?= t('Review Deleted'); ?>.</div>
<script>
function searchPaymentMethods(search)
{
    var ob=new OnPageSearch(search,"#reviewkeywordsearchresult");
    ob.url=window.location.href;
    ob.search();
}
</script>