<?php
global $mysqli;

$total_forms=0;
$forms_ob=$this->load('forms_control');
$optin_ob=$this->load('optin_control');

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
            <div class="form-group">
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
    $total_forms=$forms_ob->getFormsCount( );
    $forms=$forms_ob->getAllForms($total_forms, $max_leads_limit, $page_count);
   
?>
<div class="container-fluid">
  <div class="row page-titles mb-4">
      <div class="col-md-5 align-self-center">
        <h4 class="text-themecolor" id="commoncontainerid">Exito Settings</h4>
      </div>
      <div class="col-md-7 align-self-center text-right">
        <div class="d-flex justify-content-end align-items-center">Manage forms</div>
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
                    <th>Form&nbsp;Name</th>
                    <th>Shortcode</th>
                    <th>Subscribers</th>
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
               
                  if( count($forms) > 0 )
                  {
                      foreach( $forms as $data ){
                        $total_users=$forms_ob->getUsersCount( $data['id'] );
                  ?>    <tr>
                          <td><?php echo $count; ?></td>
                          <td>
                            <a href="index.php?page=cfexito_all_optins&cfexito_form_id=<?=$data['id'] ?>" >
                              <?php echo $data['form_name']; ?>
                            </a>
                          </td>
                          <td><strong class="text-info" onclick="copyText(`[cfexito_shortcode id=<?=$data['id']; ?>]`)" data-toggle="tooltip" title="Copy to clipboard" style="cursor:pointer;">[cfexito_shortcode id=<?=$data['id']; ?>]</strong></td>
                          <td>
                          <a href="index.php?page=cfexito_all_optins&cfexito_form_id=<?=$data['id'] ?>"><?php echo $optin_ob->getLeadsCount($data['id']); ?></a>
                          </td>
                          <td><?php echo date('d-M-y h:ia', strtotime($data['created_at'])); ?></td>
                          <td>
                            <a href="index.php?page=cfexito_popup_forms&cfexito_form_id=<?=$data['id'] ?>">
                              <i class="fas fa-edit text-primary"></i>
                              &nbsp;
                            </a>
                            <a href="index.php?page=cfexito_all_optins&cfexito_form_id=<?=$data['id'] ?>" >
                              <i class="fas fa-eye text-info"></i>
                            </a>&nbsp;
                            <button  class="cfexitoFormdelete btn unstyled-button" data-id="<?=$data['id'] ?>">
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
                          <td colspan="6" class="total-data" ><center> Total Forms: <?=$total_forms; ?></center></td>
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
              $next_page_url="index.php?page=cfexito_all_forms&page_count";
              $page_count=($page_count<2)? 0:$page_count;
              echo createPager($total_forms,$next_page_url,$page_count);
            ?> 
          </div>
          <div class="col-sm-6 text-right mt-2"> 
            <a href="index.php?page=cfexito_popup_forms"><button class="btn theme-button"><i class="fas fa-pencil-alt"></i> Create New</button></a>
          </div>  
        </div>
      </div>
  </div>  
</div>
<input type="hidden" id="cfexito_ajax" value="<?php echo  get_option('install_url')."/index.php?page=ajax"; ?>">
<script>
function searchPaymentMethods(search)
{
    var ob=new OnPageSearch(search,"#keywordsearchresult");
    ob.url=window.location.href;
    ob.search();
}
</script>