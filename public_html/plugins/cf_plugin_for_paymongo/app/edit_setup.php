<?php
if(isset($_GET['id']))
{
    $data=$this->getSetup($_GET['id']);
    if($data)
    {
        $paymongo_id=$data->id;
        $title = $data->title;
        $method = $data->method;
        $credentials = json_decode( $data->credentials );
        $tax = $data->tax;
        $public_key=$credentials->public_key;
        $secret_key=$credentials->secret_key;
        $type=$credentials->type;
    }
}else{
    $paymongo_id="";
    $title = "";
    $method = "";
    $tax = "";
    $public_key="";
    $secret_key="";
    $type=0;
}

?>
<div class="container-fluid" id="cfpay_payment_methods">
<div class="row page-titles mb-4">
    <div class="col-md-5 align-self-center">
        <h4 class="text-themecolor" id="commoncontainerid">Paymongo</h4>
    </div>
    <div class="col-md-7 align-self-center text-end">
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
                                    <span>paymongo</span>
                                </div>
                            </div>
                        </div>

                        <form action="" method="post" id="paymongo_add_setting">
                            <div class="card-body">
                                <input type="hidden" id="paymongo_base_url" value="<?php echo get_option('install_url'); ?>">
                                <input type="hidden" name="pespal_id" value="<?=$paymongo_id;  ?>"> 
                                <div data-bs-toggle="tooltip" title="" class="mb-3">
                                    <label>Enter Title</label> 
                                    <div>
                                        <input placeholder="Enter Title" name="paymongo_title" type="text" value="<?=$title ?>" class="form-control">
                                    </div>
                                </div>
                                <div data-bs-toggle="tooltip" title="" class="mb-3">
                                    <label>Enter Public Key</label> 
                                    <div>
                                        <input placeholder="Enter your public key " type="text" class="form-control" name="paymongo_public_key" value="<?=$public_key ?>">
                                    </div>
                                </div>
                                <div data-bs-toggle="tooltip" title="" class="mb-3">
                                    <label>Enter Secret key</label> 
                                    <div>
                                        <input placeholder="Enter your  secret key" type="text" class="form-control" name="paymongo_secret_key" value="<?=$secret_key ?>">
                                    </div>
                                </div>
                                <div data-bs-toggle="tooltip" title="Sandbox is to just test the payment method it will not create real payment." class="mb-3">
                                    <label>Select Type</label> 
                                    <div>
                                        <select class="form-control" name="paymongo_type">
                                            <option value="0" <?php if($type==0){ echo "selected"; } ?> >Sandbox</option>
                                            <option value="1" <?php if($type==1){ echo "selected"; } ?> >Live</option>
                                        </select>
                                    </div>
                                </div>
                                <div data-bs-toggle="tooltip" title="" class="mb-3">
                                    <label>Enter Tax Amount (will be applied as a percentage)</label> 
                                    <div>
                                        <input placeholder="Enter tax amount" type="number" class="form-control" name="paymongo_tax" value="<?php if($tax!=""){ echo $tax; }else{echo 0; } ?>">
                                    </div>
                                </div>
                                <div class="paymongo_error text-center">
                                    
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