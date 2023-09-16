<?php


if(isset($_POST['delintegration']))
{
    $this-> delIntegration($_POST['delintegration']);
}

$page_count=1;
$page_count=1;
if(isset($_GET['pagecount']))
{
    $page_count=$_GET['pagecount'];
}
$hashcount=(($page_count*get_option('qfnl_max_records_per_page'))-get_option('qfnl_max_records_per_page'))+1;
$all_query=$this->getData("all",$page_count);
 $total_integrations=$this->getData('total');
?>
<div class="container-fluid">
<div class="row page-titles mb-4">
                    <div class="col-md-5 align-self-center">
                        <h4 class="text-themecolor" id="commoncontainerid">External Scripts</h4>
                    </div>
                    <div class="col-md-7 align-self-center text-end">
                        <div class="d-flex justify-content-end align-items-center">Create, edit and manage your Integrations</div>
                    </div>
</div>
<div class="card pb-2  br-rounded" id="hidecard1">
    <div class="card-body pb-2" id="hidecard2">
<div class="row">
<div class="col-sm-12" id="searchdivv">
        <div class="row">
					
					<div class="col-md-2 mb-2">
					<?php echo createSearchBoxBydate(); ?>
					</div>
					<div class="col-md-3">
					<?php echo showRecordCountSelection(); ?>
					</div>
					<div class="col-md-3">
					<?php echo arranger(array('added_on'=>'Date')); ?>
					</div>
					<div class="col-md-4">
					<div class="mb-3">
						<div class="input-group input-group-sm">
							<div class="input-group-prepend ">
								<span class="input-group-text"><i class="fas fa-search"></i></span>
							</div>
							 <input type="text" class="form-control form-control-sm" placeholder="<?php w("Enter Name, type or content"); ?>"  onkeyup="searchIntegrationMethods(this.value)">
						</div>
					</div>
					</div>
</div>
        </div>
</div>                       
    <div id="qfnlintegrations" class="baserow">
        <div class="col-sm-12" v-if="(show=='setup')? true:false">
                 <div class="row">
                 <div class="col-xl-4 col-lg-6 col-md-12">
                    <div class="card pnl cursor-pointer">
                        <div class="card-body"><img src="<?php echo plugins_url('.',__FILE__); ?>./assets/img/tawk.png" onmouseover="this.src='<?php echo plugins_url('.',__FILE__); ?>./assets/img/hover-logo.png'" onmouseout="this.src='<?php echo plugins_url('.',__FILE__); ?>./assets/img/tawk.png'" v-on:click="popupOpen('tawkdotto','header')" alt="Integration" class="card-img-top img-fluid">
                            <div class="card-block border-top">
                                <h4 class="card-title">{{t("Tawk.to")}}</h4>
                                <p class="card-text">{{t("Click the image for the integration")}}</p>
                            </div>
                        </div>
                    </div>
                </div>
                     <div class="col-xl-4 col-lg-6 col-md-12">
                    <div class="card pnl cursor-pointer">
                        <div class="card-body"><img src="<?php echo plugins_url('.',__FILE__); ?>./assets/img/fbmessenger.png" onmouseover="this.src='<?php echo plugins_url('.',__FILE__); ?>./assets/img/hover-logo.png'" onmouseout="this.src='<?php echo plugins_url('.',__FILE__); ?>./assets/img/fbmessenger.png'" v-on:click="popupOpen('messenger','footer',false)" alt="Integration" class="card-img-top img-fluid">
                            <div class="card-block border-top">
                                <h4 class="card-title">{{t("Facebook Messenger")}}</h4>
                                <p class="card-text">{{t("Click the image for the integration")}}</p>
                            </div>
                        </div>
                    </div>
                </div>     
                <div class="col-xl-4 col-lg-6 col-md-12">
                    <div class="card pnl cursor-pointer">
                        <div class="card-body"><img src="<?php echo plugins_url('.',__FILE__); ?>./assets/img/skype.png" onmouseover="this.src='<?php echo plugins_url('.',__FILE__); ?>./assets/img/hover-logo.png'" onmouseout="this.src='<?php echo plugins_url('.',__FILE__); ?>./assets/img/skype.png'" v-on:click="popupOpen('skype','footer',false)" alt="Integration" class="card-img-top img-fluid">
                            <div class="card-block border-top">
                                <h4 class="card-title">{{t("Skype")}}</h4>
                                <p class="card-text">{{t("Click the image for the integration")}}</p>
                            </div>
                        </div>
                    </div>
                </div>
                     <!-- analytics-->
                <div class="col-xl-4 col-lg-6 col-md-12">
                    <div class="card pnl cursor-pointer">
                        <div class="card-body"><img src="<?php echo plugins_url('.',__FILE__); ?>./assets/img/googleanalytics.png" onmouseover="this.src='<?php echo plugins_url('.',__FILE__); ?>./assets/img/hover-logo.png'" onmouseout="this.src='<?php echo plugins_url('.',__FILE__); ?>./assets/img/googleanalytics.png'" v-on:click="popupOpen('ganalytic','header')" alt="Integration" class="card-img-top img-fluid">
                            <div class="card-block border-top">
                                <h4 class="card-title">{{t("Google Analytics")}}</h4>
                                <p class="card-text">{{t("Click the image for the integration")}}</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-4 col-lg-6 col-md-12">
                    <div class="card pnl cursor-pointer">
                        <div class="card-body"><img src="<?php echo plugins_url('.',__FILE__); ?>./assets/img/fbpixel.png" onmouseover="this.src='<?php echo plugins_url('.',__FILE__); ?>./assets/img/hover-logo.png'" onmouseout="this.src='<?php echo plugins_url('.',__FILE__); ?>./assets/img/fbpixel.png'" v-on:click="popupOpen('fpixel','header')" alt="Integration" class="card-img-top img-fluid">
                            <div class="card-block border-top">
                                <h4 class="card-title">{{t("Facebook Pixel")}}</h4>
                                <p class="card-text">{{t("Click the image for the integration")}}</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-4 col-lg-6 col-md-12">
                    <div class="card pnl cursor-pointer">
                        <div class="card-body" v-on:click="popupOpen('custom')">

                            <img src="<?php echo plugins_url('.',__FILE__); ?>./assets/img/addnew.png" alt="Integration" class="card-img-top img-fluid" onmouseover="this.src='<?php echo plugins_url('.',__FILE__); ?>./assets/img/hover-logo.png'" onmouseout="this.src='<?php echo plugins_url('.',__FILE__); ?>./assets/img/addnew.png'">
                            <!--
                                <div class="mt-4 ml-5 mb-3 create-integration"><button class="btn btn-secondary btn-circle " v-on:click="popupOpen('custom')"><i class="fas fa-plus "></i></button><span class="ml-3 text-muted" v-on:click="popupOpen('custom')">Create Custom Integration</span></div>

                            <div class="mt-5 ml-5  mb-2 create-integration"><button class="btn btn-secondary  btn-circle " v-on:click="showDiv('table')"><i class="fas fa-eye"></i></button><span class="ml-3 text-muted" v-on:click="showDiv('table')">View Current Integrations</span></div>
                            -->

                            <div class="card-block border-top">
                                <h4 class="card-title">{{t("Create Custom Integration")}}</h4>
                                <p class="card-text">{{t("Click the image for new integration")}}</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-12"><button class="btn theme-button" style="float:end;margin-bottom:10px;" v-on:click="showDiv('table')">{{t("View Older")}}</button></div>
                </div>
        <!-- popup -->        
        <div class='overlay intsettingpopup' v-bind:style="{display:(open)? 'block':'none'}"><div class='card pnl api-forms'><div class='card-header card-header-copy' style='position:relative'>{{t("Integration Setting")}} ({{(integration_types[type] !==undefined)? t(integration_types[type]):''}}) <i class="fas fa-times-circle float-end cursor-pointer" v-on:click="popupClose()"></i></div><div class='card-body' style="background-color:white"><div class='mb-3'>
        <label>{{t("Title")}}</label>
        <input type='text' class="form-control" v-model="title" v-bind:placeholder="t('Add Title')" style="margin-bottom:5px;">
        <label v-if="code">{{t("Add Script")}} <span v-if="(type=='skype'||type=='messenger')? true:false">(<a v-on:click="toggleCode()" class="text-primary" style="cursor:pointer;">{{t("Add Id")}}</a>)</span></label>
        <textarea class="form-control" style="min-height:100px;" v-model="data" v-if="code" v-bind:placeholder="t('Add Script')"></textarea>

        <label v-if="!code">{{t('Add Id')}} <span v-if="(type=='skype'||type=='messenger')? true:false">(<a v-on:click="toggleCode()" class="text-primary" style="cursor:pointer;">Add Script</a>)</span></label>
        <input v-if="!code" type="text" class="form-control" v-bind:placeholder="t('Enter Your Id')" v-model="data">

        <span v-if="(type=='custom')? true:false">
        <label style="margin-top:5px;">{{t('Set Position')}}</label>
        <select v-model="position" class="form-control">
        <option value="header">{{t('Header')}}</option>
        <option value="footer" selected>{{t('Footer')}}</option>
        </select>
        </span>


        <center>
            
        <p class="text-center text-success" style="font-weight:800px" v-if="success_msg.trim().length>0" v-html="success_msg"></p>
                     <p class="text-center text-danger" style="font-weight:800px;" v-if="err.trim().length>0" v-html="err"></p>
            </center>
        <button class="btn theme-button btn-block" style="margin-top:5px;" v-on:click="saveSettings($event)">{{t('Save Settings')}}</button>
        </div></div>
        </div></div>

            </div>
        <input type="hidden" id="inididintegration" value="<?php if(isset($_GET['int_id'])){echo $_GET['int_id'];}else{echo 0;} ?>">
       <div class="col-sm-12 nopadding" v-if="(show=='table')? true:false">
        <div class="table-responsive">
            <table class="table table-striped" id="keywordsearchtable">
                <thead><tr><th>#</th><th>{{t('Title')}}</th><th>{{t('Type')}}</th><th>{{t('Created On')}}</th><th>{{t('Action')}}</th></tr></thead>
                <tbody id="keywordsearchresult">
                <!-- keyword search --> 
            
                <?php
                while($r=$all_query->fetch_object())
                {
                    $count_presence_infunnel=$this->countOccurranceInPagetable($r->id);
                    $type=$r->type;
                    if($type=="tawkdotto")
                    {
                        $type="tawk.to";
                    }
                    elseif($type=="ganalytic")
                    {
                        $type="Google Analytic";
                    }
                    elseif($type=="fpixel")
                    {
                        $type="Facebook Pixel";
                    }
                    else
                    {
                        $type=ucfirst($type);
                    }
                    $action="<table class='actionedittable'><tr><td><button class='btn unstyled-button' style='' data-bs-toggle='tooltip' title='".t("Edit Integration")."' v-on:click=\"showDiv('setup',".$r->id.")\"><i class='fas fa-edit text-primary'></i></button></td><td><form action='' method='post' onsubmit='return confirmDeletion(".$count_presence_infunnel.",\"Integration\")'><button type='submit' class='btn unstyled-button' value='".$r->id."' name='delintegration' data-bs-toggle='tooltip' title='".t("Delete Integration")."'><i class='fas fa-trash text-danger'></i></button></form></td></tr></table>";

                    echo "<tr><td>".t($hashcount)."</td><td>".$r->title."</td><td>".t($type)."</td><td>".date('d-M-Y h:ia',$r->added_on)."</td><td>".$action."</td></tr>";
                    ++$hashcount;
                }
                echo "<tr><td colspan=10 class='total-data'>".t("Total Integrations").": ".t($total_integrations)."</td></tr>";
                ?>
                <!-- /keyword search --> 
                  </tbody>
            </table>
            </div>
            <div class="col-sm-12 row boder-top nopadding">
            <div class="col-sm-6 ms-auto mt-2">
                <?php
                $nextpageurl=$_SERVER['REQUEST_URI']."&pagecount";
                if($total_integrations>0)
                {
                echo "<tr><td colspan=10>".createPager($total_integrations,$nextpageurl,$page_count)."</td></tr>";
                }
                ?> 
            </div>
            <div class="col-sm-6 text-end mt-2">
            <button class="btn theme-button" v-on:click="showDiv('setup')"><i class="fas fa-pencil-alt"></i> {{t('Create New')}}</button>
            </div>
            </div>
       </div>

        </div>
    </div></div></div>
    <input type="hidden" id="cfint_base_url" value="<?php echo get_option('install_url'); ?>">
<input type="hidden" id="cfint_plugin_base_url" value="<?php echo plugins_url('../',__FILE__); ?>">

<style>

    .intsettingpopup
    {
       
        position:fixed;
        z-index:9999;
    }
   

</style>
<script>
function searchIntegrationMethods(search)
{
    var ob=new OnPageSearch(search,"#keywordsearchresult");
    ob.url=window.location.href;
    ob.search();
}
</script>