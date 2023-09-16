<?php
$data_ob = $this->load('form_controller');

if(isset($_GET['id']))
{
    $data=$data_ob->getSetup($_GET['id']);
    if($data)
    {
        $cf_google_recaptcha_id=$data->id;
        $title = $data->g_title;
        $version=$data->g_version;
        $credentials = json_decode( $data->credentials );
        $site_key=$credentials->site_key;
        $secret_key=$credentials->secret_key;
       
    

             
    }
}
else{
    $cf_google_recaptcha_id="";
    $title = "";
    $version="";
    $site_key="";
    $secret_key="";
     
}

?>



<div class="container-fluid" id="cfpay_payment_methods">
<div class="row page-titles mb-4">
    <div class="col-md-5 align-self-center">
        <h4 class="text-themecolor" id="commoncontainerid">Google Recaptcha</h4>
    </div>
    <div class="col-md-7 align-self-center text-right">
        <div class="d-flex justify-content-end align-items-center">Create, edit and manage your payment methods</div>
    </div>
</div>
    <div class="row">
        <div class="col-sm-12" >
            <div class="row justify-content-center align-items-center">
                <!-- script for popup -->
                <div class="col-sm-6" > 
                    <div class="card pnl visual-pnl" >
                        <div class="card-header">
                            <div class="row">
                                <div class="col-md-12">
                                    <span>Google reCaptcha</span>
                                </div>
                            </div>
                        </div>

                        <form action="" method="post" id="cf_google_add_form">
                            <div class="card-body">
                                <input type="hidden" id="cf_google_base_url" value="<?php echo get_option('install_url'); ?>">
                                <input type="hidden" name="pespal_id" value="<?=$cf_google_recaptcha_id;  ?>"> 

                                
                                <div data-toggle="tooltip" title="" class="mb-3">
                                    <label>Enter Title</label> 
                                    <div>
                                        <input placeholder="Enter Title" name="google_recaptcha_title" type="text" value="<?=$title ?>" class="form-control">
                                    </div>
                                </div>

                                <div data-toggle="tooltip" title="" class="mb-3">
                                    <label>Enter Site Key</label> 
                                    <div>
                                        <input placeholder="Enter your Site key " type="text" class="form-control" name="google_recaptcha_site_key" value="<?=$site_key ?>">
                                    </div>
                                </div>
                                <div data-toggle="tooltip" title="" class="mb-3">
                                    <label>Enter Secret key</label> 
                                    <div>
                                        <input placeholder="Enter your secret key" type="text" class="form-control" name="google_recaptcha_secret_key" value="<?=$secret_key ?>">
                                    </div>
                                </div>
                                <div data-toggle="tooltip" title="" class="mb-3">
                                <label for="version">Choose Version:</label>
                                  
                                    <select name="google_recaptcha_version" id="google_recaptcha_version" class="form-control">
                                        <option value="v2" <?php if($version == 'v2'){ echo 'selected'; }else{ ' '; }  ?> >V2</option>
                                        <option value="v3" <?php if($version == 'v3'){ echo 'selected'; }else{ ' '; }  ?> >V3</option>
                                    </select>
                                </div>
                               
                                <div class="google_recaptcha_error text-center">
                                    
                                </div> 
                                <div class="mb-3">
                                    <!----> <!----> 
                                    <button class="btn btn-primary btn-block">Save Settings</button>
                                </div> 
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>