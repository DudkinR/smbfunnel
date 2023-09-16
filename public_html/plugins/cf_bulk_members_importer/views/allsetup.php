<?php
global $mysqli;
$total_setup=0;
$setup_ob=$this->load('setup');
global $app_variant;
$app_variant = isset($app_variant)?$app_variant:"coursefunnels";
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
    $funne_type="Course Funnel";

}

$page_count=1;
$install_url= get_option('install_url');
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
$total_setups=$setup_ob->getAllFunnelsCounts( );
$setups=$setup_ob->getAllFunnels();

$s_avatar= function($s_txt,$setup_ob){return $setup_ob->text_to_avatar($s_txt);};

?>
<div class="container-fluid">
    <div class="row page-titles mb-4">
        <div class="col-md-5 align-self-center">
            <h4 class="text-themecolor" id="commoncontainerid"><?= ucfirst($funne_type );?>s</h4>
        </div>
        <div class="col-md-7 align-self-center text-right">
            <div class="d-flex justify-content-end align-items-center">Manage <?= ucfirst($funne_type);?>s </div>
        </div>
    </div>
    <div class="container-fluid cfs-student-selection">
        <div class="row">
            <div class="col-md-6 offset-md-3">
                <div class="card br-rounded">
                    <div class="card-header text-dark">
                        <div class="form-group">
                            <?php if($app_variant=="shopfunnels"){ ?>
                              <h3><i class="fas fa-store"></i>&nbsp;Select Store</h3>
                            <?php } else{ ?>
                              <h3><i class="fas fa-funnel-dollar"></i>&nbsp;Select Funnel</h3>
                            <?php } ?>
                        </div>
                    </div>
                    <div class="card-body cfs-student-container pb-4" style="height:500px;overflow:auto">
                        <div class="form-group cfs-student-list">
                            <ul>
                              <?php
                               if(count($setups)>0 )
                               {
                                foreach($setups as $data)
                                {
                                  $s_avatar_el= $s_avatar( $data['name'],$setup_ob);
                                  $s_shortened_text= $data['name'];

                                  $name = $data['name']
                                  ?>
                                    <li> 
                                      <a href="index.php?page=cfbulk_members_members&funnelid=<?=$data['id']?>">
                                            <div class="d-inline-block text-white d-flex align-items-center w-100">
                                            <?= $s_avatar_el ?>&nbsp; <span class="text-dark"><?= $s_shortened_text ?></span> 
                                            </div>
                                            <div class="total-customer pl-2 pt-2">&nbsp;&nbsp; Total <?= ucfirst($students );?>s:
                                                <span><?php echo  $setup_ob->getStudentsCount( $data['id'] ); ?></span></div>
                                        </a>
                                    </li>        
                                <?php
                                }
                               }else{
                                 ?>
                                    <li class="p-3">   
                                    <a  href="index.php?page=create_funnel"> 
                                      <span class="text-dark" > No <?= ucfirst($funne_type);?> available please.</span>
                                      Create a new <?= lcfirst($funne_type);?>
                                        </a>
                                    </li>      
                                 <?php
                               }
                              ?>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
<input type="hidden" id="cfproof_convert_ajax" value="<?php echo  $install_url."/index.php?page=ajax"; ?>">
<script>
function searchPaymentMethods(search) {
    var ob = new OnPageSearch(search, "#keywordsearchresult");
    ob.url = window.location.href;
    ob.search();
}
</script>