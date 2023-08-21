<?php
function addToCOD($sell_ob,$credentials,$items,$description="")
{	
//==================================
    $err="";
    $credentials=json_decode($credentials);
    $sheepingcharge=0;
    $tax=0;
    $currency="USD";

    $all_price_detail=$sell_ob->getProductsPriceDependingOnMethod($credentials,$items);
    //'itemarr','sheepingcharge','tax','totalprice','currency','total', 'allproductdetail'
    if(is_array($all_price_detail))
    {
        foreach($all_price_detail as $all_price_detail_index=>$all_price_detail_val)
        {
            ${$all_price_detail_index}=$all_price_detail_val;
        }
    }
    
    $order_data_array=$_SESSION['order_form_data'.get_option('site_token')];

    $email=false;
    $cod_step=0;

    $name="";
    if(isset($order_data_array['data']['name']))
    {
        $name=$order_data_array['data']['name'];
    }
    else if(isset($order_data_array['data']['firstname']))
    {
        $name=$order_data_array['data']['firstname'];
        if(isset($order_data_array['data']['lastname']))
        {
            $name .=" ".$order_data_array['data']['lastname'];
        }
    }    
    
    $check_cod = get_option("disable_otp_for_cod");
    if( $check_cod )
    {
        $_SESSION['total_paid'.get_option('site_token')]=$total;
        $_SESSION['payment_currency'.get_option('site_token')]=$currency;
        if(isset($order_data_array['data']['email']) && filter_var($order_data_array['data']['email'], FILTER_VALIDATE_EMAIL))
            {
                $email=$order_data_array['data']['email'];
            }
            $_GET['execute']=1;
            $arr= array(
                'payer_name'=> $name,
                'payer_email'=> $email,
                'payment_id'=>  'cf_cod_'.bin2hex(random_bytes(2)).'_'.time(),
                'total_paid'=> $total,
                'payment_currency'=> $currency,
            );

            return json_encode($arr);

    }else{

        if(isset($_POST['process_otp']) && isset($_POST['codCustEmail']))
        {
            if(filter_var($_POST['codCustEmail'], FILTER_VALIDATE_EMAIL))
            {
                $email=$_POST['codCustEmail'];
                $_SESSION['cod_otp_email_'.get_option('site_token')]= $email;
                $_SESSION['order_form_data'.get_option('site_token')]['data']['email']= $email;
                $sequence_ob=$sell_ob->load->loadSequence();

                $otp= substr(str_shuffle('1234567890XCVBNMASDFGHJQWERTYUIOP@#$%^&*()+xcvbnm,ertyuiwertyui'), 0, 8);

                $_SESSION['cod_otp_token_'.get_option('site_token')]= $otp;

                $email_title= str_replace('{otp}', $otp, get_option('cod_otp_email_title'));

                $email_content= str_replace('{otp}',$otp, str_replace("\\\\r\\\\n","",str_replace("\&quot;","",str_replace("\\r\\n","",get_option('cod_otp_email_content')))));

                if($sequence_ob->sendMail($order_data_array['smtp'],'', $email,$email_title, $email_content, ""))
                {
                    $cod_step=1;
                }
                else
                {
                    $err="Unable to send mail please contact admin.";
                }
            }
            else
            {
                $err="Invalid Email Provided";
            }
        }
        else if(isset($_POST['reset_otp']))
        {
            if(isset($_SESSION['cod_otp_email_'.get_option('site_token')]))
            {unset($_SESSION['cod_otp_email_'.get_option('site_token')]);}

            if(isset($_SESSION['cod_otp_token_'.get_option('site_token')]))
            {unset($_SESSION['cod_otp_token_'.get_option('site_token')]);}
        }
        else if(isset($_POST['verify_otp']) && isset($_SESSION['cod_otp_token_'.get_option('site_token')]))
        {
            if($_SESSION['cod_otp_token_'.get_option('site_token')]===$_POST['codCustOTP'])
            {
                $_GET['execute']=1;
                $email=$_SESSION['cod_otp_email_'.get_option('site_token')];

                unset($_SESSION['cod_otp_email_'.get_option('site_token')]);

                unset($_SESSION['cod_otp_token_'.get_option('site_token')]);

                $arr= array(
                    'payer_name'=> $name,
                    'payer_email'=> $email,
                    'payment_id'=>  'cf_cod_'.$_POST['codCustOTP'].'_'.time(),
                    'total_paid'=> $total,
                    'payment_currency'=> $currency,
                );
                return json_encode($arr);
            }
            else
            {
                $cod_step=1;
                $err="OTP did not match, try again.";
            }
        }
        else
        {
            if(isset($order_data_array['data']['email']) && filter_var($order_data_array['data']['email'], FILTER_VALIDATE_EMAIL))
            {
                $email=$order_data_array['data']['email'];
                $_SESSION['cod_otp_email_'.get_option('site_token')]= $email;
            }
        }


        $_SESSION['total_paid'.get_option('site_token')]=$total;
        $_SESSION['payment_currency'.get_option('site_token')]=$currency;
        lbl:
        if(isset($_GET['execute']))
        {

        }
        else
        { ?>
            <!DOCTYPE html>
                    <html lang="en">

                    <head>
                        <meta charset="UTF-8">
                        <meta http-equiv="X-UA-Compatible" content="IE=edge">
                        <meta name="viewport" content="width=device-width, initial-scale=1.0">
                        <title><?= get_option("cod_store_name"); ?></title>
                        <meta name="description" content="<?= get_option("cod_store_message"); ?>" />
                         <link rel="stylesheet" href="assets/bootstrap/css/bootstrap.min.css">
                         <link rel="stylesheet" href="assets/fontawesome/css/all.min.css">	
                        <script src="assets/js/jquery-3.4.1.min.js"></script>
                        <script src="assets/bootstrap/js/bootstrap.bundle.min.js"></script>
                        <link rel="stylesheet" href="assets/css/style.css">	
                    </head>

                    <body>

                        <section id="new_cod_style">
                            <div class='container my-5 p-0'>
                                <div class="inner row d-flex justify-content-center">
                                    <div class="col-md-6 col-12 box1 py-5 order-2 order-sm-1">
                                        <div class="card-content">
                                            <div class="card-header">
                                                <div class="heading heaeder-1 mb-3"> <a href="#" onclick="history.back()"><i class="fa fa-arrow-left text-dark mr-2"
                                                            aria-hidden="true"></i>
                                                    </a>Cash On Delivery Payment </div>

                                            </div>
                                            <div class="card-body">
                                                <?php if(get_option("cod_store_message")): ?>
                                                    <p><small> <?= get_option("cod_store_message"); ?> </small></p>
                                                <?php else: ?>
                                                    <p><small> You need to verify your email address for purchasing the listed products </small></p>

                                                <?php endif; ?>
                                                
                                            </div>

                                            <div class="left_aside">
                                                <div class="shop_room">
                                                    <h2 class="mb-2">Products details</h2>
                                                    <?php
                                                        foreach($items as $item)
                                                        {
                                                            ?>
                                                                <div class="row">
                                                                    <div class="col-lg-8 col-md-7 col-7">
                                                                        <h4 class="text-muted"><?= $item['title']; ?></h4>
                                                                    </div>
                                                                    <div class="col-lg-4 col-md-5 col-5 text-right">
                                                                        <h4><?= number_format((float)$item['price'], 2, '.', ''); ?> <?= $item['currency']; ?></h4>
                                                                    </div>
                                                                </div>
                                                            <?php
                                                        }
                                                    ?>
                                                    <div class="row">
                                                        <div class="col-lg-8 col-md-7 col-7">
                                                            <h4 class="text-muted">Tax </h4>
                                                        </div>
                                                        <div class="col-lg-4 col-md-5 col-5 text-right">
                                                            <h4 style="color: #388e3c;"><?= number_format((float)$tax, 2, '.', '');?> <?= $currency; ?></h4>
                                                        </div>
                                                    </div>

                                                    <div class="row">
                                                        <div class="col-lg-8 col-md-7 col-7">
                                                            <h4 class="text-muted">Shipping Charges</h4>
                                                        </div>
                                                        <div class="col-lg-4 col-md-5 col-5 text-right">
                                                            <h4><?= number_format((float)$sheepingcharge, 2, '.', '');?> <?= $currency; ?></h4>
                                                        </div>
                                                    </div>

                                                    <div class="cstm_rw">
                                                        <div class="row">
                                                            <div class="col-lg-8 col-md-7 col-7">
                                                                <h5>Total</h5>
                                                            </div>
                                                            <div class="col-lg-4 col-md-5 col-5 text-right">
                                                                <h5><?= number_format((float)$total, 2, '.', ''); ?> <?= $currency; ?></h5>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-12 box2 py-5  order-1 order-sm-2">
                                        <div class="card-content">
                                             <div class="card-header heaeder-2">
                                                <div class="heading  mb-3"> 
                                                    <a href="#" onclick="history.back()" ><i class="fa fa-arrow-left text-dark mr-2" aria-hidden="true"></i></a>Cash On Delivery Payment 
                                                </div>
                                            </div>
                                            <div class="card-header box2-head text-center">
                                                <?php if(get_option("cod_store_message")): ?>
                                                    <a href="#"><img src="<?= get_option("cod_store_image"); ?>" width="300" alt="img" class="img-fluid"></a>
                                                <?php endif; ?>
                                                <?php if(get_option("cod_store_message")): ?>
                                                    <div class="heading2"><?= get_option("cod_store_name"); ?></div>
                                                <?php else: ?>
                                                    <div class="heading2">Cash On Delivery</div>
                                                <?php endif; ?>

                                            </div>
                                            <div class="card-body col-10 offset-1">
                                                <form action="" method="POST" id="paymentForm">
                                                    <?php if( $cod_step ===0){ ?>
                                                    <div class="mb-3"> 
                                                        <label style="font-size:15px !important"><small><strong class="text-muted">Email</strong></small></label> 
                                                        <input type="email" name="codCustEmail" class="form-control" placeholder="Your Email Id" value="<?php if($email){echo $email;} ?>" required>
                                                    </div>
                                                    <?php }else if($cod_step ===1){ ?>
                                                    <div class="alert alert-info">We have sent the OTP successfully, please verify.</div>
                                                    <div class="mb-3">
                                                        <input type="text" id="idpotp" class="form-control" placeholder="Enter the OTP" name="codCustOTP">
                                                    </div>
                                                    <?php } if(strlen(trim($err))>0){echo "<p class='text-center text-danger'>".$err."</p>";}else{echo "<br>";} ?>
                                                    <?php if($cod_step ===0){ ?>
                                                        <div class="mb-3">
                                                            <input type="submit" name="process_otp"  class="btn col-12" value="Send OTP">
                                                        </div>
                                                    <?php }else if($cod_step ===1){ ?>
                                                        <div class="row">
                                                            <div class="col-sm-6"> 
                                                                <input type="submit" class="btn btn-primary btn-block" name="verify_otp" 
                                                                onclick="return (function(){if((document.querySelectorAll('#idpotp')[0].value).trim().length<1){return false}else{return true;}})()" value="Verify">
                                                            </div>
                                                            <div class="col-sm-6">
                                                                <button type="submit" class="btn btn-danger btn-block" name="reset_otp">Try Again</button>
                                                            </div>
                                                        </div> 
                                                    <?php }?>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </section>

                    </body>
                    </html>
            <?php 
        }
    }   
} ?>