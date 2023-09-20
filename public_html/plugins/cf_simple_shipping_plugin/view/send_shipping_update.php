<?php
global $mysqli;
$total_setup=0;
$setup_ob=$this->load('form_controller');
global $app_variant;
$app_variant = isset($app_variant)?$app_variant:"shopfunnels";
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
$total_setups=$setup_ob->getAllFunnelsCounts( );
$setups=$setup_ob->getAllFunnels();

$s_avatar= function($s_txt,$setup_ob){return $setup_ob->text_to_avatar($s_txt);};

?>
<style>
    
@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@200;300;400;500;600;700&display=swap');
.cfs_drag-area{

}
.cfs_drag-area .cfs_before_draging
{
  display: flex;
  align-items: center;
  flex-direction: column;
  border: 2px dashed #1e1d1d;
  border-radius: 5px;
  padding: 26px 2px;
}
#cfs_custom_header{
  display: none;
}
#cf-studnet-errr{
  display: none;
}

.cfs_drag-area .cfs_dragged_filed
{
  border-top: 1px solid #cecece;
  border-bottom: 1px solid #cecece;
  display: none;

}
.cfs_drag-area .cfs_dragged_filed .cfs_replace_button
{
  border: 1px solid;
  width: 100px;
} 
.cfs_drag-area .cfs_dragged_filed 
{
  font-size: 12px;
} 

.cfs_drag-area.cfs_active{
  border: 2px solid #fff;
}
.cfs_drag-area .cfs_icon{
  font-size: 36px;
  color: #505054;
}
.cfs_drag-area span{
  font-size: 12px;
  font-weight: 500;
  /* color: #fff; */
  margin: 2px 0 4px 0;
}
.cfs_drag-area header{
  font-size: 13px;
    color: #837e7e;
}
.cfs_drag-area button{
  padding: 4px 9px;
  font-size: 13px;
  font-weight: 500;
  border: none;
  outline: none;
  color: #5256ad;
  border-radius: 5px;
  cursor: pointer;
}
.cfs_drag-area img{
  height: 100%;
  width: 100%;
  object-fit: cover;
  border-radius: 5px;
}
.cfs_progress {
  background: #4b7e1c;
  display: block;
  height: 20px;
  text-align: center;
  transition: width .3s;
  width: 0;
}

.cfs_progress.cfs_hide {
  opacity: 0;
  transition: opacity 1.3s;
}
.cfs-student-container .cfs-student-list ul {
  list-style: none;
  padding: 0;
  margin: 0;
}
.cfs-student-container hr {
  background-color: blue;
  height: 5px;
  width: 200px;
  margin: 0 !important;
  padding: 0 !important;
}
.cfs-student-container h3 {
  margin: 0 !important;
  padding: 0 !important;
}
.cfs-student-container .cfs-student-list ul li{
  padding: 5px 2px;
  color: #ffffffcf;
  border: 1px solid #ccc;
  cursor: pointer;
  border-radius: 3px;
  font-size: 18px;
  font-weight: 600;
  margin: 8px 0;
  transition: all 0.4s linear;
  position: relative;
  background-color: #f7f7f7;
}

.cfs_before_draging button{
 transition: 0.4s;
}
.cfs_before_draging button:hover{
  transform: scale(1.2);
}
.cfs-student-container .cfs-student-list ul li a {
  padding: 0 6px;
  display: inline-block;
  position: relative;
  transform: left .3s linear;
  width: 100%;
}
.cfs-student-container .cfs-student-list ul li  .total-customer{
  font-size: 14px;
  color: #1783e1 !important;
}
.cfs-student-container .cfs-student-list ul li:hover{box-shadow: 0px 3px 15px -2px #ccc;background-color: #e5e3e324;}
[cfs-studnet-store-avatar] {
  padding: 20px;
  font-size: 12px;
  height: 10px;
  width: 10px;
  display: flex;
  justify-content: center;
  align-items: center;
  border-radius: 50%;
  font-weight: 600;
}
</style>
<?php if(isset($_GET['funnelid'])){
  require_once('shipping_update.php');       
}else{ ?>
<div class="container-fluid">
    <div class="row page-titles mb-4">
        <div class="col-md-5 align-self-center">
            <h4 class="text-themecolor" id="commoncontainerid"><?= ucfirst($funne_type );?>s</h4>
        </div>
        <div class="col-md-7 align-self-center text-end">
            <div class="d-flex justify-content-end align-items-center">Manage <?= ucfirst($funne_type);?>s </div>
        </div>
    </div>
    <div class="container-fluid cfs-student-selection">
        <div class="row">
            <div class="col-md-6 offset-md-3">
                <div class="card br-rounded">
                    <div class="card-header text-dark">
                        <div class="mb-3">
                            <?php if($app_variant=="shopfunnels"){ ?>
                              <h3><i class="fas fa-store"></i>&nbsp;Select Store</h3>
                            <?php } else{ ?>
                              <h3><i class="fas fa-funnel-dollar"></i>&nbsp;Select Funnel</h3>
                            <?php } ?>
                        </div>
                    </div>
                    <div class="card-body cfs-student-container pb-4" style="height:500px;overflow:auto">
                        <div class="mb-3 cfs-student-list">
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
                                      <a href="index.php?page=send_shipping_update&funnelid=<?=$data['id']?>">
                                            <div class="d-inline-block text-white d-flex align-items-center w-100">
                                            <?= $s_avatar_el ?>&nbsp; <span class="text-dark"><?= $s_shortened_text ?></span> 
                                            </div>                                        
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
<?php } ?>
<input type="hidden" id="cfproof_convert_ajax" value="<?php echo  $install_url."/index.php?page=ajax"; ?>">
