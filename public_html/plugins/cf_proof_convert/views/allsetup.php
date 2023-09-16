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
          <input type="text" class="form-control form-control-sm" placeholder="'.t('Enter title').'" onkeyup="searchPaymentMethods(this.value)">
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
        <h4 class="text-themecolor" id="commoncontainerid">Proof Convert settings</h4>
      </div>
      <div class="col-md-7 align-self-center text-end">
        <div class="d-flex justify-content-end align-items-center">Manage setups</div>
      </div>
  </div>
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
                    <th>Title</th>
                    <th>Impression</th>
                    <th>Addded On</th>
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
                    foreach( $setups as $data ){
                      $total_impressions=$setup_ob->getTotalImpressions( $data['id'] );
                  ?>  <tr>
                        <td><?php echo $count; ?></td>
                        <td><?php echo $data['title']; ?></td>
                        <td><?php echo  $setup_ob->getTotalImpressions( $data['id'] ) ?></td>
                        <td><?php echo date('d-M-y h:ia', strtotime($data['created_at'])); ?></td>
                        <td>
                          <a data-bs-toggle="tooltip" title="Edit setup"  href="index.php?page=cfproof_convert_setup_setting&cfproof_convert_setup_id=<?=$data['id'] ?>">
                            <i class="fas fa-edit text-primary"></i>
                              &nbsp;
                          </a>
                          <button data-bs-toggle="tooltip" title="Delete setup"  class="cfproof_convert_setup_delete btn unstyled-button" data-id="<?=$data['id'] ?>">
                            <i class="fas fa-trash text-danger"></i>
                          </button>
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
              $next_page_url="index.php?page=cfproof_convert_all_setup&page_count";
              $page_count=($page_count<2)? 0:$page_count;
              echo createPager($total_setups,$next_page_url,$page_count);
            ?> 
          </div>
          <div class="col-sm-6 text-end mt-2"> 
            <a href="index.php?page=cfproof_convert_setup_setting"><button class="btn theme-button"><i class="fas fa-pencil-alt"></i> Create New</button></a>
          </div>  
        </div>
      </div>
  </div>  
</div>
<input type="hidden" id="cfproof_convert_ajax" value="<?php echo  get_option('install_url')."/index.php?page=ajax"; ?>">
<script>
function searchPaymentMethods(search)
{
    var ob=new OnPageSearch(search,"#keywordsearchresult");
    ob.url=window.location.href;
    ob.search();
}
</script>