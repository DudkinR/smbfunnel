<?php
global $mysqli;
global $dbpref;
$total_setup = 0;

$setup_ob = $this->load('form_control');

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

$all_products = $setup_ob->getProdcuts(); 

$install_url =get_option("install_url");
$page_count=1;

if(isset($_GET['page_count']) && is_numeric($_GET['page_count']))
{
    $page_count=(int)$_GET['page_count'];
}

/*
  ******* All Question filter get parameter *************
*/ 

$product_id= (int) (isset( $_GET['cfprorev_product_id'] )) ? $mysqli->real_escape_string(  $_GET['cfprorev_product_id'] ):false;



// ************Filter form start *************
ob_start();
echo '<div class="col-lg-2"> ' . createSearchBoxBydate() . ' </div>
        <div class="col-lg-2 my-3 my-lg-0  ">
          <div class="position-relative cfpro-rev">
            <button class="w-100  btn  dropdown-toggle btn-sm btn-block"  id="cfpro-rev-filter-btn">' . t('Filter Product') . '</button>
            <div class="mx-auto collapse " id="cfpro-rev-open-filter">
              <div class="form-group">
                <select  id="cfpro-rev-selec-product" class="form-control form-control-sm">
                  <option value="all">' . t('All Product') . '</option>';
                  if( count( $all_products ) > 0 )
                  {
                    foreach ( $all_products as $all_product) {
                      if ($product_id == $all_product['id']) {
                        echo '<option value=' . $all_product['id'] . ' selected>' . $all_product['title'] . '</option>';
                      } else {
                        echo '<option value=' . $all_product['id'] . ' >' . $all_product['title'] . '</option>';
                      }
                    }
                  }
                echo '</select>
              </div>
            </div>
          </div>
        </div>
        <div class="col-lg-3"> ' . showRecordCountSelection() . ' </div>
        <div class="col-lg-2">' . arranger(array('id' => 'date')) . ' </div>
        <div class="col-lg-3 ">
          <div class="form-group">
            <div class="input-group input-group-sm">
              <div class="input-group-prepend ">
                <span class="input-group-text"><i class="fas fa-search"></i></span>
              </div>
              <input type="text" class="form-control form-control-sm" placeholder="' . t('Search with product name, ' . $students . ' name') . '" onkeyup="searchPaymentMethods(this.value)">
            </div>
          </div>
</div>';
$table_manager = ob_get_clean();
$max_leads_limit = (int)get_option('qfnl_max_records_per_page');
$total_setups = $setup_ob->getSetupsCount( $product_id);
$setups = $setup_ob->getAllSetups($total_setups, $max_leads_limit, $page_count, $product_id);

?>
<input type="hidden" id="get_option_url" value="<?= get_option('install_url'); ?>">

<link rel="stylesheet" href="<?php echo plugins_url('/assets/css/style.css', __FILE__) ?>">
<style>
  .cfpro-rev #cfpro-rev-open-filter {
    width: 300px;
    z-index: 99;
    background-color: #323a40;
    padding: 14px;
    position: absolute;
  }

  .cfpro-rev #cfpro-rev-filter-btn {
    background: #1f57ca;
    background: linear-gradient(25deg, rgba(31, 87, 202, 1) 0, #1f57ca 100%) !important;
    color: #fff;
    border-radius: 5px !important;
    font-size: 14px;
    box-shadow: 0 6px 0 0 rgba(0, 0, 0, .01), 0 15px 32px 0 rgba(0, 0, 0, .06)
  }

  /* Admin side rating */
  .cfpro-rev-my-rating input.cfpro-rev-star {
    display: none;
  }
</style>


<div class="card card-body">
  <p class="p-3 mt-1 rounded bg-info text-white">
  Use this shortcode <span class="text-white"> <strong style="cursor:pointer;" onclick="copyText(`[cfquestion]`)" data-toggle="tooltip" title="<?php w('Copy to clipboard'); ?>">[cfquestion]</strong> </span> to show <b>Question And Answer</b> in the product page.
  </p>
</div>

<!-- Delete Modal  -->
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="deleteModalLabel">Delete Reocords</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form action="" method="POST" id="delete_form"></form>
      <div class="modal-body">
        <p>you want to delete records?</p>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary" id="delete_recordes">Yes</button>
      </div>
      <div class="modal-footer">

      </div>
    </div>
  </div>
</div>
<!-- end Delete Modal  -->

<div id="cfqa-link3" class="cf-qa-tabcontent cfqa-show"><br>
  <div class="card pb-2  br-rounded">
    <div class="card-body pb-2">
      <div class="row">
        <?= $table_manager;  ?>
        <div class="col-sm-12 nopadding">
          <div class="table-responsive">
            <table class="table" id="ques_table">
              <thead>
                <tr>
                  <th style="width:5%">#</th>
                  <th style="width:10%">Name</th>
                  <th style="width:15%">Product</th>
                  <th style="width:25%">Question</th>
                  <th style="width:25%">Answer</th>
                  <th style="width:20%" colspan="2">Action</th>
                </tr>

              </thead>
              <tbody>
              <tbody id="keywordsearchresult">
                <!-- keyword search -->
                <?php
                $count = 1;
                if ( isset( $_GET['page_count'] ) ) {
                  $page_count = (int)$_GET['page_count'];
                  $count = ($page_count * $max_leads_limit) - $max_leads_limit;
                  ++$count;
                }
                if ( count( $setups ) > 0 ) {
                  $install_url = get_option("install_url");

                  foreach ($setups as $data) {
                ?>
                    <tr id="tr_<?= $data['id']; ?>">
                      <td><?= $count; ?></td>
                      <td><?= $data['name']; ?></td>
                      <td><?= $data['product_title']; ?></td>
                      <td>
                        <div class="comment more">
                          <?= $data['question']; ?>
                        </div>
                      </td>
                      <td>
                        <?php
                        if( $data['status'] == 1 )
                        {
                          
                          echo '<div class="comment more">'.$data['answer'].'</div>';
                        }       
                        else{
                          echo '<a class="btn reply_btn text-primary" id=" '.$data["id"].' " name="edit_answer" data-toggle="tooltip" title="Reply">reply</a>';
                        }
                        ?>

                        <div class="modal fade" id="answer_form_Modal" tabindex="-1" role="dialog" aria-labelledby="studentModal" aria-hidden="true">
                          <div class="modal-dialog" role="document">
                          <div class="modal-content">
                            <div class="modal-header">
                              <h5 class="modal-title" id="exampleModalLabel">Your Answer</h5>
                              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                              </button>
                            </div>
                            <form id="cf_answer_form" method="POST">
                              <input type="hidden" name="answer_id" id="answer_id">
                              <div class="modal-body">
                              
                                <textarea rows="4" cols="50" name="answer" id="answer" placeholder="Enter Answer" class="form-control" ></textarea>
                              </div>

                              <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-primary" id="update_answer" name="update_answer">Post Answer</button>

                              </div>
                            </form>
                          </div>
                        </div>
                        </div>
                      </td>
                      <td>
                        <a class="btn edit_btn" id="<?php echo $data["id"]; ?>" name="edit_answer" data-toggle="tooltip" title="<?php w('Edit'); ?>"><i class="fas fa-edit text-primary"></i></a>&nbsp;
                        <a class="btn delete_btn" id="<?php echo $data["id"]; ?>" name="delete_answer" data-toggle="tooltip" title="<?php w('Delete'); ?>"><i class="fas fa-trash text-danger"></i></a>
                      </td>
                    </tr>
                <?php
                    $count++;
                  }
                }
                ?>
                <tr>
                  <td colspan="6" class="total-data">
                    <center> Total Questions: <?= $total_setups; ?></center>
                  </td>
                </tr>
                <!-- /keyword search -->
              </tbody>
            </table>

          </div>
        </div>
        <div class="col-md-12 row nopadding">
          <div class="col-sm-6 mt-2">
            <?php
            $next_page_url = get_option('install_url') . "/index.php?page=cf_question_all&page_count";
            $page_count = ($page_count < 2) ? 0 : $page_count;
            echo createPager($total_setups,  $next_page_url, $page_count);
            ?>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<div class="modal fade" id="edit_form_Modal" tabindex="-1" role="dialog" aria-labelledby="studentModal" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Your Answer</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
   
      <form id="cf_update_answer_form" method="POST">
        <input type="hidden" name="edit_id" id="edit_id">
        <div class="modal-body">
         
          <textarea rows="4" cols="50" name="edit_answer" id="edit_answer" class="form-control" placeholder="" ><?= $data['answer']?></textarea>
        </div>
       

        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-primary" id="update_answer" name="update_answer">Edit Answer</button>

        </div>
      </form>
    </div>
  </div>
  <input type="hidden" id="cf_answer_ajax" value="<?php echo get_option('install_url') . "/index.php?page=ajax"; ?>">
  <script>
    function searchPaymentMethods(search) {
      var ob = new OnPageSearch(search, "#keywordsearchresult");
      ob.url = window.location.href;
      ob.search();
    }
  </script>
 <script src="<?php echo plugins_url('../assets/js/sweet_alert.js', __FILE__) ?>"></script>

 