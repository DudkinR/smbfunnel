
<?php
if( has_session('cfredeem_discount_successfully') )
{
    $cfreedem = get_session('cfredeem_discount_successfully');
    $status = $cfreedem['status'];
    $error = $cfreedem['data']['error'];
    if($status==0)
    {
        $message = $cfreedem['data']['message'];
    }else{
        $price = $cfreedem['data']['for_restore'];
        $currency = $cfreedem['data']['currency'];
        $message = $cfreedem['data']['message'];
        $percentage = $cfreedem['data']['percentage'];
        $gift_code = $cfreedem['data']['gift_code'];
    }

}else{
    $price = 0;
    $currency = '';
    $status=0;
    $percentage=0;
    $message='';
    $error='';
}
$discounts=$this->load('discount');
$data = $discounts->getSettings();
if( $data )
{
    $gift = json_decode($data['discount'],true);


    $glabeltext =$gift['label_text'];
    $gbuttontext =$gift['button_text'];
    $gbuttoncolor =$gift['button_color'];
    $gbuttonbcolor =$gift['button_bcolor'];
    $gresult_bcolor =$gift['result_bcolor'];
    $gresult_color =$gift['result_color'];
    $gcustomcss =$gift['customCSS'];
    $gerror_color =$gift['error_color'];
}else{
    $glabeltext ="Enter Discount Code";
    $gbuttontext = "Apply";
    $gbuttonbcolor ="023059";
    $gbuttoncolor ="ffffff";
    $gerror_color ="ff0000";
    $gresult_bcolor ='ceecf2';
    $gresult_color ='777777';
    $gcustomcss = "";
}
?>

<div class="cfgift-card-container" tabindex="-1">
    <?php if( $status == 0  || isset($_GET['cfdisc_revert_discount']) || !isset($_GET['cfdisc_redeem_discount']) ||  isset($_GET['oto_removed']) ) {?>
    <div class="row justify-content-between align-items-center cfgift-input-box">
        <div class="col-sm-8 pr-0">
            <div class="mb-3 " id="cfgift-giftcard-wrapper">
                <label for="input"><?=$glabeltext;?></label>
                <input type="text" class="form-control"  autocomplete="off" name="discount_code" id="cfdis-discount-input">
            </div>
        </div>
        <div class="col-sm-4 p-0 pl-2 mt-2 mt-sm-0 text-end text-sm-start">
            <button class="btn btn-secondary cfgift-not-allowed btn-sm" type="button" id="cfdis-sub-btn-discount" disabled ><?=$gbuttontext;?> </button>
        </div>
    </div>
    <?php  } ?>
    <div class="w-100 cfgift-result-box">
        <?php if( $status == 1 && isset( $_GET['cfdisc_redeem_discount'] ) &&  !isset( $_GET['cfdisc_revert_discount'] ) &&  !isset($_GET['oto_removed']) ) {
         $cfgiftcode = substr( $gift_code , -4 );
            ?>
            <div class="cfgift-redeemed-giftcode my-1" style="background-color:#<?= $gresult_bcolor; ?>;color:#<?= $gresult_color; ?>;">
                <div class="cfdic-first-redeem">
                    &nbsp;&nbsp;&nbsp;<?php printf("%'*8s",$cfgiftcode); ?>
                </div>
                <div class="d-flex m-1 justify-content-between align-items-center font-weight-bold cfgift-gift-redeemed">
                    <div>
                        <?= $percentage.'% worth of '. $price.' '.$currency.' has been applied to this product'; ?>: &nbsp;&nbsp;
                    </div>
                    <div >
                        &nbsp;&nbsp;&nbsp;&nbsp;<span class="cfdisp-remove-discount px-2">&times;</span>
                    </div>
                </div>
            </div>
        <?php }  else if( $status==0 && ( isset( $_GET['cfdisc_revert_discount'] ) || isset( $_GET['cfdisc_redeem_discount'] ) ) ) { ?>
            <div class="cfgift-gift-error <?= ($error=="err")?'d-block':''?>  cfgift-error" style="color:#<?=$gerror_color?>;"> 
            <?= ($error=="err")?$message:''?>
            </div>
        <?php } else if( isset( $_GET['cfdisc_redeem_discount'] )  ) { ?>
            <div class="cfgift-gift-error d-block cfgift-error <?= ($error=="err")?'d-block':''?>" style="color:#<?=$gerror_color?>">
            <?= ($error=="err")?$message:''?>
            </div>
        <?php } else { ?>
            <div class="cfgift-gift-error cfgift-error" style="color:#<?=$gerror_color?>">
                <?= ($error=="err")?$message:''?>
            </div>
        <?php } ?>
    </div>
</div>
<input type="hidden" id="cfgift-ajax" value="<?= get_option('install_url');?>/index.php?page=ajax">
<style>
    /* Gift card apply button css start from here */
    .cfgift-card-container{
        border-top: 2px dashed #ccc;
        padding-top: 15px;
    }
    .cfgift-card-container #cfdis-sub-btn-discount * {
        color: <?= "#".$gbuttoncolor; ?> !important;
    }
    .cfgift-card-container .cfgift-input-box,
    .cfgift-card-container .cfgift-result-box {
        max-width: 400px;
    }
    .cfgift-not-allowed{
        cursor:not-allowed;
    }
    .cfgift-card-container .cfdiscount-netpayble{
        border-top: 2px dashed #ccc;
        margin-top: 25px;
        padding:10px 0;
    }
    .cfgift-card-container .cfgift-redeemed-giftcode
    {
        background-color: #ceecf2;
        padding: 8px 10px;
        border-radius: 11px;
    }
    .cfgift-card-container .cfgift-redeemed-giftcode > div.cfdic-first-redeem
    {
        font-weight: 600;
        font-size: 19px;
        padding: 3px 0;
        border-bottom: 2px dashed <?="#".$gresult_color; ?>;

    }
    .cfgift-gift-error{
        color: red;
        display: none;
    }
    .cfgift-card-container .cfgift-redeemed-giftcode i.fa
    {
        font-size: 29px;
        color: #888383;
    }

    .cfgift-card-container .cfgift-gift-error
    {
        color: red;
        font-family: none;
        font-weight: 600;
    }
    #cfdis-discount-input{
        border: none;
        margin: 0 !important;
        outline: none;
        padding-bottom: 4px !important;
        height: 20px;

    }
    #cfgift-giftcard-wrapper{
        border: 1px solid #ccc;
        padding: 0 0 7px 0;
        border-radius: 5px;
        margin: 0px;
        transition: 0.4s;
        background-color: white !important;
    }
    #cfgift-giftcard-wrapper label{
        padding: 0;
        margin: 0;
        font-size: 12px;
        font-family:serif;
        font-weight: 600;
        margin-left: 5px
            
    }
    #cfgift-giftcard-wrapper button{
        padding: 11px 15px;
        margin:auto;

    }
    .cfgift-btn-active{
    background-color:<?= "#".$gbuttonbcolor; ?> !important;
    color:<?= "#".$gbuttoncolor; ?> !important;
            
    }

    #cfdis-sub-btn-discount{
        padding: 12px 14px;
        margin: auto;
        width: 100px;
    }
    #cfdis-discount-input.form-control:focus{
    outline: none;
    box-shadow:none !important;
    }
    #cfgift-giftcard-wrapper:focus-within{
        border: 1px solid #1878b9 !important;
    outline: 1px solid #1878b9 !important;
    box-shadow: 0 0 0 1px #1878b9;
    -webkit-box-shadow: 0 0 0 1px #1878b9;
}
    .cfdisp-remove-discount{
        cursor: pointer;
        font-size: 23px;
    }
    .cfdisp-remove-discount:hover{
        transform: scale(1.2);
    }

/* Gift card apply button css end here */
</style>

<style>
    <?php
    echo str_ireplace(".this-form",".cfgift-card-",str_ireplace("\\r\\n","",$gcustomcss));
    ?>
</style>

