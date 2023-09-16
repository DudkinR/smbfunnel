<?php
$data_ob = $this->load('form_controller');

if(isset($_POST['cf_google_recaptchasetup']))
{
    $data_ob->deleteSetup($_POST['cf_google_recaptchasetup']);
}

?>


<div class="container-fluid">
<div class="row page-titles mb-4">
                    <div class="col-md-5 align-self-center">
                        <h4 class="text-themecolor" id="commoncontainerid">Google Recaptcha</h4>
                    </div>

                    <div class="col-md-7 align-self-center text-right">
                        <div class="d-flex justify-content-end align-items-center">Create, edit and manage your Google Recaptcha</div>
                        <div class="text-success errorid hide"></div>

                    </div>
                    


</div>


    <div class="row">
        <div class="col-sm-12">
            <div class="card pb-2  br-rounded">
                <div class="card-body pb-2">
                <div class="row">
					<div class="col-md-2  mb-2">
					<?php echo createSearchBoxBydate(); ?>
					</div>
					<div class="col-md-3">
					<?php echo showRecordCountSelection(); ?>
					</div>
					<div class="col-md-3">
					<?php echo arranger(array('id'=>'date')); ?>
					</div>
					<div class="col-md-4">
					<div class="form-group">
						<div class="input-group input-group-sm">
							<div class="input-group-prepend ">
								<span class="input-group-text"><i class="fas fa-search"></i></span>
							</div>
							 <input type="text" class="form-control form-control-sm" placeholder="<?php w('Search with title'); ?>" onkeyup="searchMethods(this.value)">
						</div>
					</div>
					</div>
                </div>

                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr><th>#</th><th>Title</th><th>Version</th><th>Shortcode</th><th>Added On</th><th>Action</th></tr>
                            </thead>
                            <tbody id="keywordsearchresult">
                            <!-- keyword search -->   
                                <?php
                                $page_count=0;
                                if(isset($_GET['page_count']))
                                {
                                    $page_count=(int)$_GET['page_count'];
                                }
                                $total=$data_ob->countSetups();
                                $all_data=$data_ob->loadSetups($page_count);
                                $last_id=0;
                                $count=($page_count<1)? 1:$page_count;
                                $records_to_show=get_option('qfnl_max_records_per_page');
                                $records_to_show=(int) $records_to_show;
                                $count=($count*$records_to_show)-$records_to_show;

                                for($i=0; $i<count($all_data); $i++)
                                {
                                    $data=$all_data[$i];
                                    $count_hash =$count+($i+1);
                                    

                                    $action="<table class='actionedittable'><tr>
                                    <td><a href='index.php?page=cf_setting_".$this->method."&id=".$data->id."'><button class='btn unstyled-button' data-toggle='tooltip' title='Edit'><i class='text-primary fas fa-edit'></i></button></a></td>
                                    <td><form action='' method='POST'><button type='submit'  class='btn unstyled-button' data-toggle='tooltip' title='Delete' name='cf_google_recaptchasetup' value='".$data->id."'><i class='text-danger fas fa-trash'></i></button></form></td>
                                    </tr></table>";

                                    echo "<tr>
                                    <td>".$count_hash."</td>
                                    <td><a href='index.php?page=cf_setting_".$this->method."&id".$data->id."'>".$data->g_title."</a></td>
                                    <td>".$data->g_version." </td>
                                    <td><div class='cf_code' id='cf_code'><span class='text-success'><strong data-toggle='tooltip' data-placement='top' title='Click to copy' style='cursor:pointer;' onclick='cfgoogle_recaptchaText(`".$data->id."`,`".$data->g_version."`)'>Click to Copy</strong></span></div></td>
                                    <td>".date('d-m-Y h:ia',strtotime($data->createdon))."</td>
                                    <td>".$action."</td>
                                    </tr>";

                                    $last_id=$data->id;
                                }
                                ?>
                            <tr><td colspan=10 class="text-center">Total: <?php echo $total; ?></td></tr>
                            <!-- /keyword search -->
                            </tbody>
                        </table>
                    </div>

                    <div class="row mt-4">
                        <div class="col">
                            <?php
                            $next_page_url="index.php?page=cf_setups_".$this->method."&page_count";
                            echo createPager($total,$next_page_url,$page_count);
                            ?>
                        </div>
                        <div class="col text-right">
                            <a href="index.php?page=cf_setting_<?php echo $this->method; ?>"><button class="btn theme-button"><i class="fas fa-pencil-alt"></i>&nbsp;Create New</button></a>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
<script>
function searchMethods(search)
{
    var ob=new OnPageSearch(search,"#keywordsearchresult");
    ob.url=window.location.href;
    ob.search();
}

  function cfgoogle_recaptchaText(id,g_version) {
    
    var text ='[cf_google_recaptcha';
    if(g_version == 'v2'){
        text += '_v2 '+ 'id=' + id + ']';
    }else{
        text += '_v3 '+ 'id=' + id + ']';
    }
    // alert(text);

    navigator.clipboard.writeText(text);
     $('.cf_code').click(function() {
        $(".errorid").text("Copied").show().delay(5000).fadeOut();
    });
}

</script>


