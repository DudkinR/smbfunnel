
<?php
if( has_session('cfredeem_giftcard_successfully') )
{

    $cfreedem = get_session('cfredeem_giftcard_successfully');
    $status = $cfreedem['status'];
    $error = $cfreedem['data']['error'];
    if($status==0)
    {
        $message = $cfreedem['data']['message'];
    }else{
        $price = $cfreedem['data']['for_restore'];
        $currency = $cfreedem['data']['currency'];
        $message = $cfreedem['data']['message'];
        $gift_code = $cfreedem['data']['gift_code'];
    }
}else{
    $price = 0;
    $currency = '';
    $status=0;
    $message='';
    $error='';
}
$discounts=$this->load('discount');
$data = $discounts->getSettings();
if( $data )
{
    $gift = json_decode($data['giftcard'],true);


    $glabeltext =$gift['label_text'];
    $gbuttontext =$gift['button_text'];
    $gbuttoncolor =$gift['button_color'];
    $gbuttonbcolor =$gift['button_bcolor'];
    $gbuttonbcolor =$gift['button_bcolor'];
    $gresult_bcolor =$gift['result_bcolor'];
    $gresult_color =$gift['result_color'];
    $gcustomcss =$gift['customCSS'];
    $gerror_color =$gift['error_color'];
}else{
    $glabeltext ="Enter Gift Code";
    $gbuttontext = "Apply";
    $gbuttonbcolor ="023059";
    $gbuttoncolor ="ffffff";
    $gerror_color ="ff0000";
    $gresult_bcolor ='ceecf2';
    $gresult_color ='777777';
    $gcustomcss = "";
}


?>

<div class="cfgift-card-container">
    <?php if( $status == 0 || isset($_GET['cfdisc_redeem_revert']) || !isset($_GET['cfdisc_redeem_giftcard'])  ||  isset($_GET['oto_removed'])  ) {?>
    <div class="row justify-content-between w-100 align-items-center cfgift-input-box">
        <div class="col-sm-8 pr-0">
            <div class="mb-3 " id="cfgift-giftcard-wrapper">
                <label for="input"><?=$glabeltext;?></label>
                <input type="text" class="form-control"  autocomplete="off" name="giftcode" id="cfgift-giftcard-input">
            </div>
        </div>
        <div class="col-sm-4 p-0 pl-2 mt-2 mt-sm-0 text-end text-sm-start">
            <button class="btn btn-secondary cfgift-not-allowed btn-sm" type="button" id="cfgift-sub-btn-for-gift" disabled > <?=$gbuttontext;?> </button>
        </div>
    </div>
    <?php  } ?>
    <div class="w-100 cfgift-result-box">
    <?php if( $status==1 && isset( $_GET['cfdisc_redeem_giftcard'] )  && !isset($_GET['cfdisc_redeem_revert']) &&  !isset($_GET['oto_removed'])  ) {
        $cfgiftcode = substr($gift_code,-4);
        ?>
        <div class="d-flex flex-wrap cfgift-redeemed-giftcode my-1 justify-content-center" >
            <div class="d-flex m-1 justify-content-between align-items-center font-weight-bold cfgift-gift-redeemed" style="background-color:#<?= $gresult_bcolor; ?>;color:#<?= $gresult_color; ?>;">
                <div>
                    -<?= $price.' '.$currency; ?>: &nbsp;&nbsp;
                </div>
                <div class="c1">
                    <i class="fa fa-gift"></i>
                </div>
                <div>
                    &nbsp;&nbsp;&nbsp;<?php printf("%'*8s",$cfgiftcode); ?>
                </div>
                <div >
                    &nbsp;&nbsp;&nbsp;&nbsp;<span class="cfgift-remove-gift-card px-2">&times;</span>
                </div>
            </div>
        </div>
    <?php } else if( $status==0 && (isset($_GET['cfdisc_redeem_revert']) || isset( $_GET['cfdisc_redeem_giftcard'] ) ) ) { ?>
        <div class="cfgift-gift-error  <?= ($error=="err")?'d-block':''?> cfgift-error" style="color:#<?=$gerror_color?>">
        <?= ($error=="err")?$message:''?>
        </div>
    <?php } else if( isset( $_GET['cfdisc_redeem_giftcard'] )  ) { ?>
        <div class="cfgift-gift-error <?= ($error=="err")?'d-block':''?> cfgift-error" style="color:#<?=$gerror_color?>">
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
    .cfgift-card-container .cfgift-input-box,
    .cfgift-card-container .cfgift-result-box {
        max-width: 400px;
    }
    .cfgift-card-container #cfgift-sub-btn-for-gift * {
        color: <?= "#".$gbuttoncolor; ?> !important;
    }
    .cfgift-card-container .cfdiscount-netpayble{
        border-top: 2px dashed #ccc;
        margin-top: 25px;
        padding:10px 0;
    }
    .cfgift-not-allowed{
        cursor:not-allowed;
    }
    .cfgift-card-container .cfgift-redeemed-giftcode .d-flex
    {
        background-color: #ceecf2;
        padding: 8px 10px;
        border-radius: 11px;
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
    .cfgift-card-container .cfgift-redeemed-giftcode .cfgift-remove-gift-card
    {
        font-size: 24px;
        cursor: pointer;
    }
    .cfgift-remove-gift-card:focus
    {
        color:red;
    transform: scale(2);
    }

    .cfgift-card-container .cfgiftgift-error
    {
        color: red;
        font-family: none;
        font-weight: 600;
    }
    #cfgift-giftcard-input{
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

    #cfgift-sub-btn-for-gift{
        padding: 12px 14px;
        margin: auto;
        width: 100px;
    }

    #cfgift-sub-btn-for-gift:hover{
        padding: 12px 14px;
        margin: auto;
        width: 100px;
    }

    #cfgift-giftcard-input.form-control:focus{
    outline: none;
    box-shadow:none !important;
    }
    #cfgift-giftcard-wrapper:focus-within{
        border: 1px solid #1878b9 !important;
    outline: 1px solid #1878b9 !important;
    box-shadow: 0 0 0 1px #1878b9;
    -webkit-box-shadow: 0 0 0 1px #1878b9;
    }
    .cfgift-remove-gift-card{
        cursor: pointer;
        font-size: 23px;
    }
    .cfgift-remove-gift-card:hover{
        transform: scale(1.2);
    }
/* Gift card apply button css end here */
</style>
<style>
    <?php
    echo str_ireplace(".this-form",".cfgift-card-",str_ireplace("\\r\\n","",$gcustomcss));
    ?>
</style>
