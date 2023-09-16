<?php
global $mysqli;
global $app_variant;
$app_variant = isset($app_variant)?$app_variant:"coursefunnels";
$total_setup=0;
$setup_ob=$this->load('giftcard');
$total_setups=0;
$discount_id=false;

if( $app_variant == "shopfunnels" ){
    $students="Customer";

}
elseif( $app_variant == "cloudfunnels" ){
    $students="Member";

}
elseif( $app_variant == "coursefunnels" ){
    $students="Student";

}

$page_count=1;
if(isset($_GET['page_count']) && is_numeric($_GET['page_count']))
{
  $page_count=(int)$_GET['page_count'];
}

if( isset( $_GET['discount_id'] ) && is_numeric($_GET['discount_id'] ) )
{
    $discount_id = $_GET['discount_id'];
}

if(isset($_POST['delgiftcards']))
{
    $setup_ob->deleteGiftCard($_POST['delgiftcards']);
}

$total_setups= $setup_ob->getGiftCardsCount( 'percentage' );
$discounts = $setup_ob->getGiftCards( $page_count, $discount_id,'percentage');

?>
<div class="container-fluid">
    <div class="row page-titles mb-4">
        <div class="col-md-5 align-self-center">
            <h4 class="text-themecolor" id="commoncontainerid"> <img src='<?=CFGIFT_DISCOUNT_PLUGIN_URL_URL?>/assets/img/f7.png' alt='Gift' /> <?php w('Discounts'); ?></h4>
        </div>
        <div class="col-md-7 align-self-center text-end">
            <div class="d-flex justify-content-end align-items-center"><?php w('Create, edit and manage Discounts'); ?></div>
        </div>
    </div>
    <h5><?php w('Use the shortcode'); ?>   <strong class="text-info cfdisp_cursor" onclick="copyText(`[discount_box]`)" data-bs-toggle="tooltip" title="<?php w('Copy to clipboard'); ?>" >[discount_box]</strong> <?php w('to show the Discount box on the checkout page'); ?></h5>
    <br>
    <div class="card pb-2  br-rounded" style="display:<?php echo ( isset($_GET['discount_id'] ) && !empty( $_GET['discount_id'] ) ) ? 'none':'block'; ?>" id="collectionhidecard1">
        <div class="card-body " id="hidecard2">
            <div class="row">
                <div class="col-lg-2 col-md-12 ">
                    <?php echo createSearchBoxBydate(); ?>
                </div>
                <div class="col-lg-3 col-md-12">
                    <?php echo showRecordCountSelection(); ?>
                </div>
                <div class="col-md-3">
                <?php echo arranger(array('id'=>'date')); ?>
                </div>
                <div class=" col-lg-4 col-md-12">
                    <div class="input-group input-group-sm mb-3 float-end">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-search"></i></span>
                        </div>
                        <input type="text" class="form-control form-control-sm" placeholder="<?php w('Search With discount code'); ?>" onkeyup="searchGiftCards(this.value)">
                    </div>
                </div>
            </div>
            <div class="row collectioncontainer">
                <div class="col-sm-12">
                    <div class="table-responsive">
                        <table class="table table-striped" id="tableforsearch">
                            <thead>
                                <tr><th>#</th><th><?php w('Code'); ?></th><th><?php w('Status'); ?></th><th><?php w('Percentage'); ?></th><th><?php w('Date issued'); ?></th><th><?php w('Action'); ?></th></tr>
                            </thead>
                            <tbody id="keywordsearchresult">
                                <!-- keyword search -->
                                <?php
                                    if(count($discounts) > 0 )
                                    {
                                        $c=0;
                                        foreach($discounts as $discount)
                                        {
                                            $symbol = $setup_ob->get_currency_symbol($discount['currency']);
                                            $c++;
                                            ?>
                                            <tr>
                                                <td><?= $c ?></td>
                                                <td><strong class="text-info cfdisp_cursor" onclick="copyText(`<?= $discount['gift_code'] ?>`)" data-bs-toggle="tooltip" title="<?=t('Copy to clipboard'); ?>" ><?= $discount['gift_code'] ?></strong></td>

                                                <td ><span  class="badge badge-primary fs12px" ><?= ($discount['status'] ==1) ? 'Active' :'Deactive'; ?></td></span>
                                                <td><?= !empty($discount['percentage'])?$discount['percentage']:"00.00"; ?>% </td>
                                                <td><?= date('M d, Y',strtotime($discount['created_at'])); ?></td>
                                                <td>
                                                    <table class='actionedittable'>
                                                        <tr>
                                                            <td>
                                                                <a class='btn unstyled-button' data-bs-toggle='tooltip' title="<?=t('See uses timeline of Discount'); ?>" href="index.php?page=cfdiscount_discount_timeline&discount_id=<?=$discount['id']; ?>">
                                                                <i class="fas fa-chart-line text-info"></i>
                                                                </a>
                                                            </td>
                                                            <td>
                                                                <form action='' method='post' onsubmit="return confirm(t('Are you sure'))">
                                                                    <input type='hidden' name='delgiftcards' value='<?= $discount['id']; ?>'>
                                                                    <button type='submit' class='btn unstyled-button' data-bs-toggle='tooltip' title="<?=t('Delete Discount'); ?>">
                                                                        <i class='fas fa-trash text-danger'></i>
                                                                    </button>
                                                                </form>
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </td>
                                            </tr>
                                            <?php
                                        }
                                    }
                                ?>
                                <tr>
                                </tr>mb-3
                                <tr>
                                    <td colspan=10 class="total-data"><?php w('Total Discounts'); ?>: <?=$total_setups;  ?></td>
								</tr>
                                <!-- /keyword search -->
                            </tbody>
                            
                        </table>
                    </div>
                    <div class="col-sm-12 row nopadding">
                        <div class="col-sm-6 me-auto mt-2">
                        <?php
                            $next_page_url="index.php?page=cfdiscount_discount&page_count";
                            $page_count=( $page_count < 2 )? 0: $page_count;
                            echo createPager( $total_setups,  $next_page_url, $page_count );
                            ?> 
                        </div>
                        <div class="col-sm-6 mt-2 text-end">
                        <a href="index.php?page=cfdiscount_giftcard_settings" class="btn  btn-success"><?php w('Setting'); ?></a> &nbsp;&nbsp;
                        <button class="btn theme-button" id="open-giftcard-form"><i class="fas fa-pencil-alt"></i> <?php w('Create New'); ?></button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php
        if( $discount_id )
        {
            $gift_code  = $discounts[0]['gift_code'];
            $inivalue   = number_format($discounts[0]['initial_value'],2);
            $gift_status= $discounts[0]['status'];
            $discount_type = $discounts[0]['discount_type'];
            $redeem_no = $discounts[0]['redeem_no'];
            $cfpercentage = $discounts[0]['percentage'];
            $apply_type = $discounts[0]['apply_type'];
            $exp_date   = date("Y-m-d",strtotime($discounts[0]['expiration_date']));
            $exp_type   = $discounts[0]['expiration_type'];
            $gproducts  = json_decode($discounts[0]['products'],true);
            $member_id   = $discounts[0]['member_id'];
            $updated_at =  $discounts[0]['updated_at'];
        }else{
        
            $gift_code = bin2hex(random_bytes(10));
            $inivalue   = "10.00";
            $gift_status     = 1;
            $apply_type     = "all";
            $discount_type     = "giftcard";
            $exp_date   = date("Y-m-d",time());
            $exp_type   = "no_expiration";
            $member_id   = 0;
            $cfpercentage   = 5.00;
            $redeem_no   = 1;
            $gproducts   = [];
            $updated_at = "";
        }
    ?>
    <div id="cfdisp-collectionformdiv" style="display:<?php echo ( $discount_id ) ? 'block':'none'; ?>" class="px-4">
        <div id="cfdisp-success-class" tabindex="-1" >
        </div>
        <div id="cfdisp-error-class"  tabindex="-1">
        </div>
        <form id="cfdis_discountform">
            <div class="row d-flex justify-content-between">
                <div class="col-lg-7">
                    <div class="card br-rounded">
                        <div class="card-body">
                            <label for="title" id="giftcarddetails" tabindex="-1" class="fs18px fw6" ><?php w('Discount details'); ?></label>
                            <br>
                            <div class="mb-3">
                                <label for="title" ><?php w('Discount code'); ?></label>
                                <?php 
                                    if( isset( $discount_id) && !empty($discount_id) )
                                    {?>
                                        <input type="text" value="<?=$gift_code ?>" disabled id="cardcodde" name="gift_code" class="form-control">
                                    <?php
                                    }else{
                                        ?>
                                        <input type="text" value="<?=$gift_code ?>" id="cardcodde" name="gift_code" class="form-control">
                                        <?php
                                    }
                                ?>
                                <input type="hidden" value="savediscountcode_ajax" name="action" >
                                <input type="hidden"  id="cfsavediscounts" value="<?php echo ( $discount_id ) ? 'update':'save'; ?>" name="savediscounts" class="form-control">
                                <input type="hidden" id="discount_id" name="discount_id" value="<?= $discount_id ?>">
                                <div class="cfdis-sf-error gift-card-err"></div>
                            </div>
                        </div>
                    </div>
                    <div class="card br-rounded">
                        <div class="card-body p-0">
                            <div class=" px-3 pt-3">
                                <label for="title" class="fs18px fw6"><?php w('Apply on'); ?></label>
                                <br>
                                <div class="mb-3">
                                    <div class="mb-3">
                                        <input type="radio" value="all" <?php echo ( $apply_type == "all" ) ? 'checked':''; ?> name="apply_type" class="cfdisc_apply_on_product" id="cfdis_no_all_product"> <label  for="cfdis_no_all_product"> <?php w('All Product'); ?></label>
                                        <br>
                                        <input type="radio" value="custom" <?php echo ( $apply_type == "custom" ) ? 'checked':''; ?> name="apply_type" class="cfdisc_apply_on_product" id="cfdis_set_choose_product"> <label for="cfdis_set_choose_product"> <?php w('No, I want to select Product(s)'); ?></label>
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <div class="px-3 cfdis_set_apply_product" style="display:<?php echo ( $apply_type == "custom" ) ? 'block':'none'; ?>">
                            <?php $cfproducts = get_products(); ?>
                                <div class="mb-3 cfdis_select-product" id="cfdis_select-proudctr-div">
                                    <label ><?php w('Products'); ?></label>
                                    <select name="products[]" multiple id="cfdis_product-select"
                                        class="form-control">
                                        <?php
                                            if ( count($cfproducts) > 0 )
                                            {
                                                foreach( $cfproducts as $cfproduct )
                                                {
                                                    
                                                    if(in_array( $cfproduct['id'], $gproducts ) )
                                                    {
                                                        echo '<option selected value="'.$cfproduct['id'].'">'.$cfproduct['title'].'dd</option>';
                                                    }else{
                                                        echo '<option  value="'.$cfproduct['id'].'">'.$cfproduct['title'].'</option>';
                                                    }
                                                }
                                            }else{
                                                echo '<option value="-1">'.t('Products are not available!').'</option>';
                                            }
                                        ?>
                                    </select>
                                </div>
                                <div class="cfdis-error cfdis_applypro_err"></div>
                            </div>
                        </div>
                    </div>
                    <div class="card br-rounded">
                        <div class="card-body p-0">
                            <div class="p-3">
                                <label for="title" class="fs18px fw6"><?php w('Expiration date'); ?></label>
                                <br>
                                <div class="mb-3">
                                    <div class="mb-3">
                                        <input type="radio" value="no_expiration" <?php echo ( $exp_type == "no_expiration" ) ? 'checked':''; ?> name="expiration_type" class="cfdisp-expiration_type" id="cfdisp-no_expiration_date"> <label for="cfdisp-no_expiration_date"> <?php w('No expiration date'); ?></label>
                                        <br>
                                        <input type="radio" value="set_expiration" <?php echo ( $exp_type == "set_expiration" ) ? 'checked':''; ?> name="expiration_type" class="cfdisp-expiration_type" id="cfdisp-set_expiration_date"> <label  for="cfdisp-set_expiration_date"> <?php w('Set expiration date'); ?></label>
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <div class="p-3 cfdisp-set_expiration_date" style="display:<?php echo ( $exp_type == "set_expiration" ) ? 'block':'none'; ?>">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="" for=""><?php w('Select Date'); ?></label>
                                            <input type="date" min="1997-01-01" max="2040-12-31" value="<?=$exp_date ?>" class="form-control"  name="expiration_date" id="cfdisp-expiration_date" >
                                        </div>
                                    </div>
                                </div>
                                <div class="cfdis-sf-error expiration_date_err"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="card br-rounded">
                        <div class="card-body">
                            <label for="status"><?php w('Status'); ?></label>
                            <div class="mb-3 mt-1">
                                <select name="status" id="status" class="form-control">
                                    <option value="0" <?php echo ( $gift_status == 0 ) ? 'selected':''; ?> ><?php w('Inactive'); ?></option>
                                    <option value="1" <?php echo ( $gift_status == 1 ) ? 'selected':''; ?>><?php w('Active'); ?></option>
                                </select>
                            </div>
                            <div class="mb-3 ">
                                <input type="hidden" name="discount_type" value="percentage">
                                <label for="status" ><?php w('Add Percentage'); ?></label>
                                <div class="input-group" style="width:100px;">
                                    <input type="text" name="percentage" max="100"  value="<?= $cfpercentage ?>" id="cfdis_percentage" class="form-control" />
                                    <div class="input-group-append">
                                        <span class="input-group-text">%</span>        
                                    </div>
                                </div>
                                <div class="cfdis-sf-error cfdis_percentage_ad"></div>
                            </div>
                            <div class="mb-3 ">
                                <label for="cfdis_redeemno" ><?php w('How many times can we redeem this discount code'); ?>.</label>
                                <input type="number" name="redeem_no"   value="<?= $redeem_no ?>" id="cfdis_redeemno" class="form-control" />
                                <div class="cfdis-sf-error cfdis_redeem_no_ad"></div>
                            </div>
                            <?php
                            $members = get_members();
                            
                            ?>
                            <div class="mb-3">
                                <label for="title"  ><?php w('Select a '.lcfirst($students)); ?></label>
                                <br>
                                <select name="member_id" class="custom-select" id="member_id">
                                <option value="-1"><?php w('Select '.ucfirst($students)); ?></option>
                                <?php
                                if ( count($members) > 0 )
                                {
                                    
                                    foreach( $members as $member )
                                    {
                                        if($member['id']== $member_id  )
                                        {
                                            echo '<option selected value="'.$member['id'].'">'.ucwords($member['name']).' ( '.$member['email'].' )</option>';
                                        }else{
                                            echo '<option  value="'.$member['id'].'">'.ucwords($member['name']).' ( '.$member['email'].' ) </option>';
                                        }
                                    }
                                }else{
                                    echo '<option value="-1" data-not="not"> '.t(lcfirst($students).'s are not available').'.</option>';
                                }
                                ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for=""><?php w('Notes')?></label>
                                <textarea name="notes" class="form-control"></textarea>
                            </div>
                            <div class="w-100">
                                <div class="mb-3">
                                    <button type="submit" class="btn btn-primary theme-button btn-block" id="save-gift-prd-btn"><i class="fas fa-check-circle"></i>
                                    &nbsp;<?php w('Save setup')?>
                                </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
    <br><br><br><br><br>
</div>

<div id="cfdisp-sfsnackbar"><?php w('Customer added successfully')?>.</div>
<input type="hidden" id="discountGiftbaseurl" value="<?=CFGIFT_DISCOUNT_PLUGIN_URL;?>/">
<input type="hidden" id="giftdiscountajaxUrl" value="<?=get_option('install_url')?>/index.php?page=ajax">
<input type="hidden" id="giftdiscountinstall_url" value="<?=get_option('install_url')?>/">