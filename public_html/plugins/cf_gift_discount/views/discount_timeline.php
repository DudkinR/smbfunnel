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
$discount_id=false;

if(isset($_GET['page_count']) && is_numeric($_GET['page_count']))
{
  $page_count=(int)$_GET['page_count'];
}

if( isset( $_GET['discount_id'] ) && is_numeric($_GET['discount_id'] ) )
{
    $discount_id = $_GET['discount_id'];
}

$discounts = $setup_ob->getGiftCards( 1, $discount_id,'percentage');
?>
<div class="container-fluid">
    <br>
    <?php
        if( $discount_id )
        {
            $gift_status     = $discounts[0]['status'];
            $gift_code     = $discounts[0]['gift_code'];
            $exp_date   = date("Y-m-d",strtotime($discounts[0]['expiration_date']));
            $exp_type   = $discounts[0]['expiration_type'];
            $percentage   = $discounts[0]['percentage'];
            $notes =  $discounts[0]['notes'];
            $apply_type = $discounts[0]['apply_type'];
            $notes =  $discounts[0]['notes'];
            $member_id   = $discounts[0]['member_id'];
            $redeem_no = $discounts[0]['redeem_no'];
            $gproducts  = json_decode($discounts[0]['products'],true);
        }else{
            header("location:index.php?page=cfdiscount_discount");
        }
    ?>
    <div class="alert alert-success">
        <div class="py-2 font-weight-bold"><span class="text-dark"><?php w('Discount Code'); ?>:</span> <strong  onclick="copyText(`<?=$gift_code?>`)" data-bs-toggle="tooltip" title="<?php w('Copy to clipboard'); ?>" class="cfdisp_cursor "><?=$gift_code?></strong></div>    
    </div>
    <br>
    <div id="cfdisp-collectionformdiv" class="px-4 cfdisp-issue-gift-card br-rounded">
        <div id="cfdisp-success-class" tabindex="-1">
        </div>
        <div id="cfdisp-error-class" tabindex="-1">
        </div>
        <div class="row d-flex justify-content-between">
            <div class="col-sm-7 setup-section-1">
                <div class="card br-rounded">
                    <div class="card-header">
                        <h5><?php w('Discount Code Details'); ?></h5>
                    </div>
                    <div class="card-body">
                        <p class="fw6"><?php w('Discount Code'); ?></p>
                        <p><?=$gift_code?><br>
                        <?php w('Worth'); ?>: <span class="text-info"><?=$percentage?>%</span>
                        </p>
                    </div>
                    <div class="card-footer">
                        <div class="row ">
                            <div class="col-md-4">
                                <span><?php w('Expiration date'); ?></span><br>
                                <span class="text-primary"><?php
                                if( $exp_type == "no_expiration" )
                                {
                                    echo "Never";
                                }
                                else
                                {
                                   echo  date('d M, Y',strtotime($exp_date));
                                }
                                ?></span> - &nbsp; <span data-bs-toggle="modal" data-target="#updategiftcarddate" class="cfdisp_cursor  text-success"><?php w('Change It'); ?></span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card br-rounded">
                    <div class="card-header">
                        <h5><?php w('Timeline'); ?></h5>
                    </div>
                    <div class="card-body">
                        <ul>
                            <?php
                                $timeline = $setup_ob->getIssueTimeline($discount_id);
                                if(  is_object($timeline))
                                {
                                    $row = $timeline->num_rows;
                                    $i=1;
                                    while( $timelines =$timeline->fetch_assoc() )
                                    {
                                        echo '<li>
                                                <div class="mb-3">
                                                    <div class="fw5 mb-1 text-primary">'.date('d M, Y',strtotime($timelines['created_at'] ) ).'</div>
                                                    <div class=" d-flex justify-content-between">
                                                        <div>'.$timelines['comment'].'</div>
                                                        <span>'.date('H:i A',strtotime($timelines['created_at'] ) ).'</span>
                                                    </div>
                                                </div>
                                                '.( $row != $i ?'<i class="fas fa-arrow-up"></i>':'').'
                                            </li>';
                                        $i++;
                                    }
                                }
                            ?>
                        </ul>    
                        <br>
                    </div>
                </div>
            </div>
            <div class="col-sm-4 setup-section-2">
            <?php
                if( !empty($member_id) && $member_id!=-1 )
                {
                    $memb = get_member( $member_id );
                    $name=$memb['name'];
                    $email=$memb['email'];
                    $image = $setup_ob->getGravatarImage( $email );
                    ?>
                    <div class="card br-rounded">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <span class="fw6"><?=ucfirst($students)?></span>
                                <form action="" id="resend_gift-code" method="post">
                                <input type="hidden" value="savegiftcards_send_email" name="action" >
                                    <input type="hidden" name="mid" value="<?=$member_id?>">
                                    <input type="hidden" name="resentcode" value="resend">
                                    <input type="hidden" name="name" value="<?=$name?>">
                                    <input type="hidden" name="email" value="<?=$email?>">
                                    <input type="hidden" name="type" value="discount">
                                    <input type="hidden" name="giftcard_id" value="<?=$discount_id?>">
                                    <button type="submit" class="cfdisp-resend-code" name="sendemail"><?php w('Resend'); ?></button>
                                </form>
                            </div>
                            <br><br>
                            <div class="mb-3">
                                <div class="d-flex">
                                    <div class="img pt-1">
                                        <img src="<?=$image;?>" class="img-fluid" alt="">
                                    </div>
                                    <div class="details pl-2">
                                        <span id="issur_name" class="d-inline-block py-1 text-primary"><?=$name?></span><br>
                                        <span id="issur_email"><?=$email;?></span><br>
                                    </div>
                                </div>
                            </div>
                            <br><hr>
                            <form  class="updatediscountform">       
                            <label for="status" ><?php w('Status'); ?></label>
                            <input type="hidden" value="savegiftcards_ajax" name="action" >
                            <div class="mb-3 mt-1">
                                <select name="status" id="status" class="form-control">
                                    <option value="0" <?php echo ( $gift_status == 0 ) ? 'selected':''; ?> ><?php w('Inactive'); ?></option>
                                    <option value="1" <?php echo ( $gift_status == 1 ) ? 'selected':''; ?>><?php w('Active'); ?></option>
                                </select>
                            </div>
                            <div class="mb-3 ">
                                <label for="cfdis_redeemno" ><?php w('How many times can we redeem this discount code?'); ?>.</label>
                                <input type="number" name="redeem_no"   value="<?= $redeem_no ?>" id="cfdis_redeemno" class="form-control" />
                                <div class="cfdis-sf-error cfdis_redeem_no_ad"></div>
                            </div>
                            <div class="">
                                <label for="title"><?php w('Apply on'); ?></label>
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
                            <div class="cfdis_set_apply_product" style="display:<?php echo ( $apply_type == "custom" ) ? 'block':'none'; ?>">
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
                                                echo '<option value="-1"> '.t('Products are not available!').'</option>';
                                            }
                                        ?>
                                    </select>
                                </div>
                                <div class="cfdis-error cfdis_applypro_err"></div>
                            </div>
                            <input type="hidden"   value="update" name="savegiftcards" class="form-control">
                            <input type="hidden"   value="discount" name="type" class="form-control">
                            <input type="hidden" name="giftcard_id" value="<?= $discount_id ?>">
                            <div class="mb-3">
                                <label for=""><?php w('Notes'); ?></label>
                                <textarea class="form-control" name="notes"><?php echo str_replace("\\\\r\\\\n","",str_replace("\&quot;","",str_replace("\\r\\n","",htmlentities($notes)))); ?></textarea>
                            </div>
                            <br>
                            <div class="w-100">
                                <div class="mb-3">
                                    <button type="submit" class="save-changes  btn btn-primary theme-button btn-block"><?php w('Save changes'); ?></button>
                                </div>
                            </div>
                        </form>
                        </div>
                    </div>
                    <?php
                }else{
                    ?>
                <div class="card">
                    <div class="card-body">
                        <form  class="updatediscountform">       
                            <label for="status" ><?php w('Status'); ?></label>
                            <input type="hidden" value="savegiftcards_ajax" name="action" >
                            <div class="mb-3 mt-1">
                                <select name="status" id="status" class="form-control">
                                    <option value="0" <?php echo ( $gift_status == 0 ) ? 'selected':''; ?> ><?php w('Inactive'); ?></option>
                                    <option value="1" <?php echo ( $gift_status == 1 ) ? 'selected':''; ?>><?php w('Active'); ?></option>
                                </select>
                            </div>
                            <div class="mb-3 ">
                                <label for="cfdis_redeemno" ><?php w('How many time can we redeem this discount code'); ?>.</label>
                                <input type="number" name="redeem_no"   value="<?= $redeem_no ?>" id="cfdis_redeemno" class="form-control" />
                                <div class="cfdis-sf-error cfdis_redeem_no_ad"></div>
                            </div>
                            <div class="">
                                <label for="title"><?php w('Apply on')?></label>
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
                            <div class="cfdis_set_apply_product" style="display:<?php echo ( $apply_type == "custom" ) ? 'block':'none'; ?>">
                            <?php $cfproducts = get_products(); ?>
                                <div class="mb-3 cfdis_select-product" id="cfdis_select-proudctr-div">
                                    <label ><?php w('Products')?></label>
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
                                                echo '<option value="-1"> '.t('Products are not available!').'</option>';
                                            }
                                        ?>
                                    </select>
                                </div>
                                <div class="cfdis-error cfdis_applypro_err"></div>
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
                                    echo '<option value="-1" data-not="not"> '.t(lcfirst($students).'s are not available').'</option>';
                                }
                                ?>
                                </select>
                            </div>
                            <input type="hidden"   value="update" name="savegiftcards" class="form-control">
                            <input type="hidden"   value="discount" name="type" class="form-control">
                            <input type="hidden" name="giftcard_id" value="<?= $discount_id ?>">
                            <div class="mb-3">
                                <label for=""><?php w('Notes'); ?></label>
                                <textarea  class="form-control" name="notes"><?php echo str_replace("\\\\r\\\\n","",str_replace("\&quot;","",str_replace("\\r\\n","",htmlentities($notes)))); ?></textarea>
                            </div>
                            <br>
                            <div class="w-100">
                                <div class="mb-3">
                                    <button type="submit" class="save-changes  btn btn-primary theme-button btn-block"><?php w('Save changes'); ?></button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <?php
                } ?>
            </div>
        </div>
    </div>
    <br><br><br><br><br>
</div>
<!-- Modal -->
<div class="modal" id="updategiftcarddate" tabindex="-1" role="dialog" aria-labelledby="updategiftcarddateTitle"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <form action="" class="updatediscountform" method="POST">
            <input type="hidden" value="savegiftcards_ajax" name="action" >
            <input type="hidden"   value="discount" name="type" class="form-control">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle"><?php w('Update Expire Date'); ?></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                <input type="hidden"  id="savegiftcards" value="<?php echo ( $discount_id ) ? 'update':'save'; ?>" name="savegiftcards" class="form-control">
                    <input type="hidden" id="giftcard_id" name="giftcard_id" value="<?= $discount_id ?>">
                    <label for="title" class="fs18px fw6"><?php w('Expiration date'); ?></label>
                    <br>
                    <div class="mb-3">
                        <div class="mb-3">
                            <input type="radio" value="no_expiration" <?php echo ( $exp_type == "no_expiration" ) ? 'checked':''; ?> name="expiration_type" class="cfdisp-expiration_type" id="no_expiration_date"> <label class="fs17px fw6" for="no_expiration_date"> <?php w('No expiration date'); ?></label>
                            <br>
                            <input type="radio" value="set_expiration" <?php echo ( $exp_type == "set_expiration" ) ? 'checked':''; ?> name="expiration_type" class="cfdisp-expiration_type" id="cfdisp-set_expiration_date"> <label class="fs17px fw6" for="cfdisp-set_expiration_date"> <?php w('Set expiration date'); ?></label>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="fs13px fw4"><span class="fw6"><?php w('Note'); ?>:</span>  <?php w('Countries have different laws for gift card expiry dates. Check the laws for your country before changing this date'); ?>.</div>
                    </div>
                    <hr>
                    <div class="p-3 cfdisp-set_expiration_date" style="display:<?php echo ( $exp_type == "set_expiration" ) ? 'block':'none'; ?>">
                        <div class="row">
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label class="" for=""><?php w('Select Date'); ?></label>
                                    <?php if($exp_type=="set_expiration"):?>
                                    <input type="date" min="2021-01-01" max="2040-12-31" value="<?=$exp_date ?>" class="form-control"  name="expiration_date" id="cfdisp-expiration_date" >
                                    <?php else:?>
                                    <input type="date" min="2021-01-01" max="2040-12-31" value="<?=date('Y-m-d',time()) ?>" class="form-control"  name="expiration_date" id="cfdisp-expiration_date" >
                                    <?php endif;?>

                                </div>
                            </div>
                        </div>
                        <div class="sf-error expiration_date_err"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" id="btn-close-model2"
                        data-dismiss="modal"><?php w('Close'); ?></button>
                    <button type="submit"  class="save-changes btn btn-success"><?php w('Save changes'); ?></button>
                </div>
            </div>
        </form>
    </div>
</div>

<div id="cfdisp-sfsnackbar"><?php w(ucfirst($students)." added successfully"); ?>.</div>
<input type="hidden" id="discountGiftbaseurl" value="<?=CFGIFT_DISCOUNT_PLUGIN_URL;?>/">
<input type="hidden" id="giftdiscountajaxUrl" value="<?=get_option('install_url')?>/index.php?page=ajax">
<input type="hidden" id="giftdiscountinstall_url" value="<?=get_option('install_url')?>/">