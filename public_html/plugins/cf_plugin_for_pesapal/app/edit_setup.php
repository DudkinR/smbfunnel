<?php
if(isset($_GET['id']))
{
    $data=$this->getSetup($_GET['id']);
    if($data)
    {
        $pesapal_id=$data->id;
        $title = $data->title;
        $method = $data->method;
        $credentials = json_decode( $data->credentials );
        $tax = $data->tax;
        $consumer_key=$credentials->consumer_key;
        $secret_key=$credentials->secret_key;
        $type=$credentials->type;
    }
}else{
    $pesapal_id="";
    $title = "";
    $method = "";
    $tax = "";
    $consumer_key="";
    $secret_key="";
    $type=0;
}

?>
<div class="container-fluid" id="cfpay_payment_methods">
<div class="row page-titles mb-4">
    <div class="col-md-5 align-self-center">
        <h4 class="text-themecolor" id="commoncontainerid">Pesapal</h4>
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
                                    <span>Pesapal</span>
                                </div>
                            </div>
                        </div>

                        <form action="" method="post" id="pesapal_add_setting">
                            <div class="card-body">
                            <div class='alert alert-danger'>To handle IPN return requests, please set the ipn listener url in your <a href="https://www.pesapal.com/merchantdashboard" target="_blank">PesaPal</a> account setting</div>
                                <input type="hidden" id="pesapal_base_url" value="<?php echo get_option('install_url'); ?>">
                                <input type="hidden" name="pespal_id" value="<?=$pesapal_id;  ?>"> 
                                <div data-toggle="tooltip" title="please copy and paste ipn url in pesapal site" class="form-group">
                                    <label>IPN Listener URL</label> 
                                    <div>
                                        <input placeholder="" type="text" class="form-control" value="<?php echo str_replace("https://","http://",get_option('install_url')); ?>?page=do_payment_execute">
                                    </div>
                                </div>
                                <div data-toggle="tooltip" title="" class="form-group">
                                    <label>Enter Title</label> 
                                    <div>
                                        <input placeholder="Enter Title" name="pesapal_title" type="text" value="<?=$title ?>" class="form-control">
                                    </div>
                                </div>
                                <div data-toggle="tooltip" title="" class="form-group">
                                    <label>Enter Consumer Key</label> 
                                    <div>
                                        <input placeholder="Enter your consumer key " type="text" class="form-control" name="pesapal_consumer_key" value="<?=$consumer_key ?>">
                                    </div>
                                </div>
                                <div data-toggle="tooltip" title="" class="form-group">
                                    <label>Enter Consumer Secret key</label> 
                                    <div>
                                        <input placeholder="Enter your consumer secret key" type="text" class="form-control" name="pesapal_secret_key" value="<?=$secret_key ?>">
                                    </div>
                                </div>
                                <div data-toggle="tooltip" title="Sandbox is to just test the payment method it will not create real payment." class="form-group">
                                    <label>Select Type</label> 
                                    <div>
                                        <select class="form-control" name="pesapal_type">
                                            <option value="0" <?php if($type==0){ echo "selected"; } ?> >Sandbox</option>
                                            <option value="1" <?php if($type==1){ echo "selected"; } ?> >Live</option>
                                        </select>
                                    </div>
                                </div>
                                <div data-toggle="tooltip" title="" class="form-group">
                                    <label>Enter Tax Amount (will be applied as a percentage)</label> 
                                    <div>
                                        <input placeholder="Enter tax amount" type="number" class="form-control" name="pesapal_tax" value="<?php if($tax!=""){ echo $tax; }else{echo 0; } ?>">
                                    </div>
                                </div>
                                <div class="pesapal_error text-center">
                                    
                                </div> 
                                <div class="form-group">
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