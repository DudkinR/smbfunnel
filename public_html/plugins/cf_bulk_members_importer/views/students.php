<?php
global $mysqli;
$total_setups=0;
$setup_ob=$this->load('setup');
global $app_variant;
$app_variant = isset($app_variant)?$app_variant:"coursefunnels";

$install_url = get_option("install_url");
if( $app_variant == "shopfunnels" ){
    $students="Customer";
    $funne_type="Store";

}
elseif( $app_variant == "cloudfunnels" ){
    $students="Member";
    $funne_type="Funnel";

}
elseif( $app_variant == "coursefunnels" ){
    $students="Student";
    $funne_type="Funnel";

}
$page_count=1;
$funnel_id=false;
$install_url= get_option('install_url');
if(isset($_GET['page_count']) && is_numeric($_GET['page_count']))
{
  $page_count=(int)$_GET['page_count'];
}
if(isset($_GET['funnelid']) && is_numeric($_GET['funnelid']))
{
  $funnel_id=$mysqli->real_escape_string($_GET['funnelid']);
}
else{
  $install_url=$install_url."/index.php?page=cfbulk_members_funnels";
  header("Location:".$install_url."");
}
$pages = get_funnel_pages($funnel_id);
$page_id=1;
if( count($pages)>0 )
{
  foreach($pages as $page)
  {
    if( $page['category'] == "register" || $page['category'] == "order" )
    {
      $page_id = $page['id'];
    }else{
      $page_id = $page['id'];
    }
  }
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
          <input type="text" class="form-control form-control-sm" placeholder="'.t('Search with name, email, last IP').'" onkeyup="searchPaymentMethods(this.value)">
        </div>
      </div>
  </div>';
$table_manager=ob_get_clean();
$max_leads_limit=(int)get_option('qfnl_max_records_per_page');
$setups=$setup_ob->gedtAllStudents($total_setups, $max_leads_limit, $page_count,$funnel_id);
$fnls = $setup_ob->getAllFunnels( $funnel_id );
$total_setups = $setup_ob->getStudentsCount($funnel_id);
$s_avatar= function( $fname,$setup_ob){ return $setup_ob->text_to_avatar($fname);};

$courses = get_products();
?>
<div class="container-fluid">
  <div class="row page-titles mb-4">
      <div class="col-md-5 align-self-center">
        <h4 class="text-themecolor" id="commoncontainerid">Add <?= ucfirst($students);?> Manually</h4>
      </div>
      <div class="col-md-7 align-self-center text-right">
        <div class="d-flex justify-content-end align-items-center">Manage <?= ucfirst($students);?></div>
      </div>
  </div>
  <div class="card pb-2  br-rounded">
      <div class="card-body pb-2">
        <div class="mb-4">
        <div class="row">
          <div class="col-md-6">
            <div class="d-flex text-white align-items-center">
            <?php
              echo $s_avatar($fnls[0]['name'],$setup_ob)."<span class='text-dark d-inline-block px-2' style='font-weight:600'>".$fnls[0]['name']."</sapn> ";
            ?>
            </div>
          </div>
          <div class="col-md-6">
            <div class="from-group float-right mb-3">
              <a href="index.php?page=cfbulk_members_add&funnel_id=<?=$funnel_id ?>"><button class="btn btn-sm theme-button p-1" style="font-size: .875rem"><i class="fas fa-plus"></i> Add New  <?= ucfirst($students);?> </button></a>
            &nbsp;<button class="btn btn-primary btn-sm" data-toggle="modal" data-target=".bd-example-modal-lg"> <i class="fas fa-file-upload"></i> Import <?= ucfirst($students);?>s</button>
            </div>
          </div>
        </div>
        </div>
        <div class="row">
          <?=$table_manager;  ?>
          <div class="col-sm-12 nopadding">
            <div class="table-responsive ">
              <table class="table table-striped">
                <thead>
                  <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Last IP</th>
                    <th>Last Loggedin</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody id="keywordsearchresult" class="text-left">
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

                  ?>  <tr>
                        <td><?php echo $count; ?></td>
                        <td><?php echo $data['name']; ?></td>
                        
                        <td> <?=$data['email'];?> </td>
                        <td><?=$data['ip_lastsignin']; ?></td>
                        
                        <td><?php
                          if(empty($data['date_lastsignin']) ||  $data['date_lastsignin']=="N/A")
                          {
                            echo "N/A";
                          }else{
                            echo date('d-M-y h:ia', $data['date_lastsignin']); 
                          }
                            ?>
                            </td>
                        <td>
                          <a data-toggle="tooltip" title="Edit <?= $students ?>"  href="index.php?page=cfbulk_members_add&cfbulk_members_id=<?=$data['id'] ?>&funnel_id=<?=$funnel_id ?>">
                            <i class="fas fa-edit text-primary"></i>
                              &nbsp;
                          </a>
                          <button data-toggle="tooltip" title="Delete <?= $students ?>"  class="cfadd_studnet_delete btn unstyled-button" data-id="<?=$data['id'] ?>">
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
                        <td colspan="7" class="total-data" ><center> Total <?= ucfirst($students);?>s: <?=$total_setups; ?></center></td>
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
              $next_page_url="index.php?page=cfbulk_members_members&funnelid=$funnel_id&page_count";
              $page_count=($page_count<2)? 0:$page_count;
              echo createPager($total_setups,$next_page_url,$page_count);
            ?> 
          </div>
        </div>
        <div class="col-md-12">
          <div class="alert alert-success" role="alert">
            <h4 class="alert-heading">Note!</h4>
            <p>The password combination of the imported users will be, <strong>First four characters of the email id and first character in the uppercase</strong> and then <strong>$1234</strong></p>
            <hr>
            <p class="mb-0">
              For Example:
                <br>
                If this is the email id  <strong> johndoe@gmail.com </strong> of the user.
                then password will be <strong><span class="text-primary">J</span>ohn$1234</strong>
            </p>
          </div>
        </div>
      </div>
  </div>  
</div>

<div class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form action=""  method="post" enctype="multipart/form-data" id="cfs_import_member">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">Import <?= ucfirst($students);?> by CSV</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="funnel_id" value="<?=$funnel_id; ?>">
          <input type="hidden" name="page_id" value="<?=$page_id; ?>" >
          <input type="hidden" name="action" value="cfstudent_bulk" >
            <div class="form-group">
              <div class="cfs_drag-area">
                  <div class="cfs_before_draging">
                    <div class="cfs_icon"><i class="fas fa-cloud-upload-alt"></i></div>
                    <header>Drop File to upload</header>
                    <span>OR</span>
                    <button type="button">Browse File</button>
                  </div>
                  <div class="cfs_dragged_filed py-2">
                    <div class="form-group d-flex align-items-center justify-content-between">
                      <div class="cfs_filename">
                      </div>
                      <div>
                        <button type="button" class="cfs_replace_button">Replce File</button>
                      </div>
                    </div>
                  </div>
                  <input type="file" name="files" hidden>
                </div>
                <div id="cfs_custom_header" class="mt-2">
                  <div class="form-group">
                    <label for="">Select Name Field</label>
                    <select name="namef" class="form-control" id="cfs_custom_name">
                    </select>
                  </div>
                  <div class="form-group">
                    <label for="">Select Email Field</label>
                    <select name="emailf"  class="form-control" id="cfs_custom_email">
                    </select>
                  </div>
                  <?php if( $app_variant == 'coursefunnels' ) { ?>
                  <div class="form-group">
                    <label for="">Select Courses</label>
                    <select id="select_courses_import" class="form-control" name="select_courses[]" multiple="multiple">
                      <?php
                      foreach ($courses as $value) {
                        echo "<option value='" . $value['id'] . "'>" . $value['title'] . "</option>";
                      }
                      ?>
                    </select>
                  </div>
                  <?php } ?>
                  <div class="form-group mt-2">
                  <div class="cfs_progress"></div>
                  </div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-primary btn-sm" id="cfs_importer_button">Import</button>
        </div>
      </form>
    </div>
  </div>
</div>
<input type="hidden" id="cfaddstudent_ajax"  value="<?php echo  get_option('install_url')."/index.php?page=ajax"; ?>" />
<script>
function searchPaymentMethods(search)
{
    var ob=new OnPageSearch(search,"#keywordsearchresult");
    ob.url=window.location.href;
    ob.search();
}
</script>