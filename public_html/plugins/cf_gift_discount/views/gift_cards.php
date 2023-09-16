<?php
global $mysqli;
$total_setup=0;
global  $app_variant;
$app_variant = isset($app_variant)?$app_variant:"coursefunnels";
$setup_ob=$this->load('giftcard');
$total_setups=0;
$giftcard_id=false;

$page_count=1;
if(isset($_GET['page_count']) && is_numeric($_GET['page_count']))
{
  $page_count=(int)$_GET['page_count'];
}

if( isset( $_GET['giftcard_id'] ) && is_numeric($_GET['giftcard_id'] ) )
{
    $giftcard_id = $_GET['giftcard_id'];
}

if(isset($_POST['delgiftcards']))
{
    $setup_ob->deleteGiftCard($_POST['delgiftcards']);
}

if( $app_variant == "shopfunnels" ){
    $students="Customer";

}
elseif( $app_variant == "cloudfunnels" ){
    $students="Member";

}
elseif( $app_variant == "coursefunnels" ){
    $students="Student";

}



$total_setups= $setup_ob->getGiftCardsCount( 'giftcard');
$giftcards = $setup_ob->getGiftCards( $page_count, $giftcard_id,'giftcard' );

?>
<div class="container-fluid">   
    <div class="row page-titles mb-4">
        <div class="col-md-5 align-self-center">
            <h4 class="text-themecolor" id="commoncontainerid">  <img src='<?=CFGIFT_DISCOUNT_PLUGIN_URL_URL?>/assets/img/f7.png' alt='History' />  <?php w('Gift Cards'); ?></h4>
        </div>
        <div class="col-md-7 align-self-center text-end">
            <div class="d-flex justify-content-end align-items-center"><?php w('Create, edit and manage Gift Cards'); ?></div>
        </div>
    </div>
    <h5><?php w('Use the shortcode'); ?>   <strong class="text-info cfdisp_cursor" onclick="copyText(`[giftcard_box]`)" data-bs-toggle="tooltip" title="<?php w('Copy to clipboard'); ?>" >[giftcard_box]</strong> <?php w('to show the Giftcard box on the checkout page'); ?></h5>
    <br>
    <div class="card pb-2  br-rounded" style="display:<?php echo ( isset($_GET['giftcard_id'] ) && !empty( $_GET['giftcard_id'] ) ) ? 'none':'block'; ?>" id="collectionhidecard1">
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
                        <input type="text" class="form-control form-control-sm" placeholder="<?php w('Search With gift code, the initial value'); ?>" onkeyup="searchGiftCards(this.value)">
                    </div>
                </div>
            </div>
            <div class="row collectioncontainer">
                <div class="col-sm-12">
                    <div class="table-responsive">
                        <table class="table table-striped" id="tableforsearch">
                            <thead>
                                <tr><th>#</th><th><?php w('Code'); ?></th><th><?php w('Status'); ?></th><th><?php w($students.'s'); ?></th><th><?php w('Date issued'); ?></th><th><?php w('Remaining/Value'); ?></th><th><?php w('Action'); ?></th></tr>
                            </thead>
                            <tbody id="keywordsearchresult">
                                <!-- keyword search -->
                                <?php
                                    if(count($giftcards) > 0 )
                                    {
                                        $c=0;
                                        foreach($giftcards as $giftcard)
                                        {
                                            $symbol = $setup_ob->get_currency_symbol($giftcard['currency']);
                                            $c++;
                                            ?>
                                            <tr>
                                                <td><?= $c ?></td>
                                                <td><strong class="text-info cfdisp_cursor" onclick="copyText(`<?= $giftcard['gift_code'] ?>`)" data-bs-toggle="tooltip" title="<?=t('Copy to clipboard'); ?>" ><?= $giftcard['gift_code'] ?></strong></td>
                                                <td ><span  class="badge badge-primary fs12px" ><?= ($giftcard['status'] ==1) ? 'Active' :'Deactive'; ?></td></span>
                                                <td>
                                                <?php
                                                    $mdata =  get_member($giftcard['member_id'] );
                                                    if($mdata)
                                                    {
                                                        echo ucwords($mdata['name']);
                                                    }else{
                                                        echo t('No '.$students);
                                                    }
                                                ?>
                                                </td>
                                                <td><?= date('M d, Y',strtotime($giftcard['created_at'])); ?></td>
                                                <td><?=number_format($giftcard['remaining_value'],2); ?>/<?= number_format($giftcard['initial_value'],2).' '.$giftcard['currency']; ?></td>
                                                <td>
                                                    <table class='actionedittable'>
                                                        <tr>
                                                            <td>
                                                                <a class='btn unstyled-button' data-bs-toggle='tooltip' title="<?=t('See uses timeline of Gift card'); ?>" href="index.php?page=cfdiscount_giftcard_timeline&giftcard_id=<?=$giftcard['id']; ?>">
                                                                <i class="fas fa-chart-line text-info"></i>
                                                                </a>
                                                            </td>
                                                            <td>
                                                                <form action='' method='post' onsubmit="return confirm(t('Are you sure'))">
                                                                    <input type='hidden' name='delgiftcards' value='<?= $giftcard['id']; ?>'>
                                                                    <button type='submit' class='btn unstyled-button' data-bs-toggle='tooltip' title="<?=t('Delete Gift Card'); ?>">
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
                                </tr>
                                <tr>
                                    <td colspan=10 class="total-data"><?php w('Total Gift Cards'); ?>: <?=$total_setups;  ?></td>
								</tr>
                                <!-- /keyword search -->
                            </tbody>
                            
                        </table>
                    </div>
                    <div class="col-sm-12 row nopadding">
                        <div class="col-sm-6 mr-auto mt-2">
                        <?php
                            $next_page_url="index.php?page=cfdiscount_giftcards&page_count";
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
        if( $giftcard_id )
        {
            $gift_code  = $giftcards[0]['gift_code'];
            $inivalue   = sprintf("%1\$.2f",$giftcards[0]['initial_value']);
            $gift_status= $giftcards[0]['status'];
            $discount_type = $giftcards[0]['discount_type'];
            $currency = $giftcards[0]['currency'];
            $apply_type = $giftcards[0]['apply_type'];
            $exp_date   = date("Y-m-d",strtotime($giftcards[0]['expiration_date']));
            $exp_type   = $giftcards[0]['expiration_type'];
            $gproducts  = json_decode($giftcards[0]['products'],true);
            $member_id   = $giftcards[0]['member_id'];
            $updated_at =  $giftcards[0]['updated_at'];
        }else{
        
            $gift_code = bin2hex(random_bytes(10));
            $inivalue   = "10.00";
            $gift_status     = 1;
            $apply_type     = "all";
            $currency     = "USD";
            $discount_type     = "giftcard";
            $exp_date   = date("Y-m-d",time());
            $exp_type   = "no_expiration";
            $member_id   = 0;
            $gproducts   = [];
            $updated_at = "";
        }
        
    ?>
    <div id="cfdisp-collectionformdiv" style="display:<?php echo ( $giftcard_id ) ? 'block':'none'; ?>" class="px-4">
        <div id="cfdisp-success-class" tabindex="-1" >
        </div>
        <div id="cfdisp-error-class"  tabindex="-1">
        </div>
        <form id="cfdis_Giftform">
            <div class="row d-flex justify-content-between">
                <div class="col-lg-7">
                    <div class="card br-rounded">
                        <div class="card-body">
                            <label for="title" id="giftcarddetails" tabindex="-1" class="fs18px fw6" ><?php w('Gift card details'); ?></label>
                            <br>
                            <div class="mb-3">
                                <label for="title" ><?php w('Gift card code'); ?></label>
                                <input type="text" value="<?=$gift_code ?>" id="cardcodde" name="gift_code" class="form-control">
                                <input type="hidden" value="savegiftcards_ajax" name="action" >
                                <input type="hidden"  id="savegiftcards" value="<?php echo ( $giftcard_id ) ? 'update':'save'; ?>" name="savegiftcards" class="form-control">
                                <input type="hidden" id="giftcard_id" name="giftcard_id" value="<?= $giftcard_id ?>">
                                <div class="sf-error gift-card-err"></div>
                            </div>
                            <div class="mb-3">
                                <label for="title"><?php w('Initial value'); ?></label>
                                <input type="text" placeholder="10.00" name="initial_value"  value="<?= $inivalue; ?>"  value="10.00" id="initial_value" class="form-control initial_value" />
                                <div class="sf-error initial-value-err"></div>
                            </div>
                            <div class="mb-3">
                                <label for="title"><?php w('Currency'); ?></label>
                                <input type="hidden"  id="set-currency" value="<?=$currency?>">
                                <br>
                                <select class="custom-select form-control" id="currency" name="currency">
                                    <option value="USD">US Dollar</option>
                                    <option value="CAD">Canadian Dollar</option>
                                    <option value="EUR">Euro</option>
                                    <option value="AED">United Arab Emirates Dirham</option>
                                    <option value="AFN">Afghan Afghani</option>
                                    <option value="ALL">Albanian Lek</option>
                                    <option value="AMD">Armenian Dram</option>
                                    <option value="ARS">Argentine Peso</option>
                                    <option value="AUD">Australian Dollar</option>
                                    <option value="AZN">Azerbaijani Manat</option>
                                    <option value="BAM">Bosnia-Herzegovina Convertible Mark</option>
                                    <option value="BDT">Bangladeshi Taka</option>
                                    <option value="BGN">Bulgarian Lev</option>
                                    <option value="BHD">Bahraini Dinar</option>
                                    <option value="BIF">Burundian Franc</option>
                                    <option value="BND">Brunei Dollar</option>
                                    <option value="BOB">Bolivian Boliviano</option>
                                    <option value="BRL">Brazilian Real</option>
                                    <option value="BWP">Botswanan Pula</option>
                                    <option value="BYN">Belarusian Ruble</option>
                                    <option value="BZD">Belize Dollar</option>
                                    <option value="CDF">Congolese Franc</option>
                                    <option value="CHF">Swiss Franc</option>
                                    <option value="CLP">Chilean Peso</option>
                                    <option value="CNY">Chinese Yuan</option>
                                    <option value="COP">Colombian Peso</option>
                                    <option value="CRC">Costa Rican Colón</option>
                                    <option value="CVE">Cape Verdean Escudo</option>
                                    <option value="CZK">Czech Republic Koruna</option>
                                    <option value="DJF">Djiboutian Franc</option>
                                    <option value="DKK">Danish Krone</option>
                                    <option value="DOP">Dominican Peso</option>
                                    <option value="DZD">Algerian Dinar</option>
                                    <option value="EEK">Estonian Kroon</option>
                                    <option value="EGP">Egyptian Pound</option>
                                    <option value="ERN">Eritrean Nakfa</option>
                                    <option value="ETB">Ethiopian Birr</option>
                                    <option value="GBP">British Pound Sterling</option>
                                    <option value="GEL">Georgian Lari</option>
                                    <option value="GHS">Ghanaian Cedi</option>
                                    <option value="GNF">Guinean Franc</option>
                                    <option value="GTQ">Guatemalan Quetzal</option>
                                    <option value="HKD">Hong Kong Dollar</option>
                                    <option value="HNL">Honduran Lempira</option>
                                    <option value="HRK">Croatian Kuna</option>
                                    <option value="HUF">Hungarian Forint</option>
                                    <option value="IDR">Indonesian Rupiah</option>
                                    <option value="ILS">Israeli New Sheqel</option>
                                    <option value="INR">Indian Rupee</option>
                                    <option value="IQD">Iraqi Dinar</option>
                                    <option value="IRR">Iranian Rial</option>
                                    <option value="ISK">Icelandic Króna</option>
                                    <option value="JMD">Jamaican Dollar</option>
                                    <option value="JOD">Jordanian Dinar</option>
                                    <option value="JPY">Japanese Yen</option>
                                    <option value="KES">Kenyan Shilling</option>
                                    <option value="KHR">Cambodian Riel</option>
                                    <option value="KMF">Comorian Franc</option>
                                    <option value="KRW">South Korean Won</option>
                                    <option value="KWD">Kuwaiti Dinar</option>
                                    <option value="KZT">Kazakhstani Tenge</option>
                                    <option value="LBP">Lebanese Pound</option>
                                    <option value="LKR">Sri Lankan Rupee</option>
                                    <option value="LTL">Lithuanian Litas</option>
                                    <option value="LVL">Latvian Lats</option>
                                    <option value="LYD">Libyan Dinar</option>
                                    <option value="MAD">Moroccan Dirham</option>
                                    <option value="MDL">Moldovan Leu</option>
                                    <option value="MGA">Malagasy Ariary</option>
                                    <option value="MKD">Macedonian Denar</option>
                                    <option value="MMK">Myanma Kyat</option>
                                    <option value="MOP">Macanese Pataca</option>
                                    <option value="MUR">Mauritian Rupee</option>
                                    <option value="MXN">Mexican Peso</option>
                                    <option value="MYR">Malaysian Ringgit</option>
                                    <option value="MZN">Mozambican Metical</option>
                                    <option value="NAD">Namibian Dollar</option>
                                    <option value="NGN">Nigerian Naira</option>
                                    <option value="NIO">Nicaraguan Córdoba</option>
                                    <option value="NOK">Norwegian Krone</option>
                                    <option value="NPR">Nepalese Rupee</option>
                                    <option value="NZD">New Zealand Dollar</option>
                                    <option value="OMR">Omani Rial</option>
                                    <option value="PAB">Panamanian Balboa</option>
                                    <option value="PEN">Peruvian Nuevo Sol</option>
                                    <option value="PHP">Philippine Peso</option>
                                    <option value="PKR">Pakistani Rupee</option>
                                    <option value="PLN">Polish Zloty</option>
                                    <option value="PYG">Paraguayan Guarani</option>
                                    <option value="QAR">Qatari Rial</option>
                                    <option value="RON">Romanian Leu</option>
                                    <option value="RSD">Serbian Dinar</option>
                                    <option value="RUB">Russian Ruble</option>
                                    <option value="RWF">Rwandan Franc</option>
                                    <option value="SAR">Saudi Riyal</option>
                                    <option value="SDG">Sudanese Pound</option>
                                    <option value="SEK">Swedish Krona</option>
                                    <option value="SGD">Singapore Dollar</option>
                                    <option value="SOS">Somali Shilling</option>
                                    <option value="SYP">Syrian Pound</option>
                                    <option value="THB">Thai Baht</option>
                                    <option value="TND">Tunisian Dinar</option>
                                    <option value="TOP">Tongan Paʻanga</option>
                                    <option value="TRY">Turkish Lira</option>
                                    <option value="TTD">Trinidad and Tobago Dollar</option>
                                    <option value="TWD">New Taiwan Dollar</option>
                                    <option value="TZS">Tanzanian Shilling</option>
                                    <option value="UAH">Ukrainian Hryvnia</option>
                                    <option value="UGX">Ugandan Shilling</option>
                                    <option value="UYU">Uruguayan Peso</option>
                                    <option value="UZS">Uzbekistan Som</option>
                                    <option value="VEF">Venezuelan Bolívar</option>
                                    <option value="VND">Vietnamese Dong</option>
                                    <option value="XAF">CFA Franc BEAC</option>
                                    <option value="XOF">CFA Franc BCEAO</option>
                                    <option value="YER">Yemeni Rial</option>
                                    <option value="ZAR">South African Rand</option>
                                    <option value="ZMK">Zambian Kwacha</option>
                                    <option value="ZWL">Zimbabwean Dollar</option>
                                </select>
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
                            <?php $cfproducts = $setup_ob->getallStoreProducts(); 
                           
                            ?>
                                <div class="mb-3 cfdis_select-product" id="cfdis_select-proudctr-div">
                                    <label ><?php w('Products'); ?></label>
                                    <select name="products[]" multiple id="cfdis_product-select"
                                        class="form-control">
                                        <?php
                                            if ( count($cfproducts) > 0 )
                                            {
                                                foreach( $cfproducts as $cfproduct )
                                                {
                                                    if(!empty($cfproduct->parent_product))
                                                    {
                                                        $ptitle = '<span>'.$cfproduct->title.'</span>( <strong class="text-primary">'.$cfproduct->v.'</strong> )';
                                                    }else{
                                                        $ptitle = '<span>'.$cfproduct->title.'</span>';
                                                    }
                                                    if(in_array( $cfproduct->id, $gproducts ) )
                                                    {
                                                        echo '<option selected value="'.$cfproduct->id.'">'.$ptitle.'</option>';
                                                    }else{
                                                        echo '<option  value="'.$cfproduct->id.'">'.$ptitle.'</option>';
                                                    }
                                                }
                                            }else{
                                                echo '<option value="-1"> '.t('Products are not available!').'</option>';
                                            }
                                        ?>
                                    </select>
                                </div>
                                <div class="sf-error cfdis_applypro_err"></div>
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
                                        <input type="radio" value="no_expiration" <?php echo ( $exp_type == "no_expiration" ) ? 'checked':''; ?> name="expiration_type" class="cfdisp-expiration_type" id="cfdisp-no_expiration_date"> <label  for="cfdisp-no_expiration_date"> <?php w('No expiration date'); ?></label>
                                        <br>
                                        <input type="radio" value="set_expiration" <?php echo ( $exp_type == "set_expiration" ) ? 'checked':''; ?> name="expiration_type" class="cfdisp-expiration_type" id="cfdisp-set_expiration_date"> <label  for="cfdisp-set_expiration_date"> <?php w('Set expiration date'); ?></label>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <div class="fs13px fw4"><span class="fw6"><?php w('Note'); ?>:</span>  <?php w('Countries have different laws for gift card expiry dates. Check the laws for your country before changing this date'); ?>.</div>
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
                                <div class="sf-error expiration_date_err"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="card br-rounded">
                        <div class="card-body">
                            <label for="status" ><?php w('Status'); ?></label>
                            <div class="mb-3 mt-1">
                                <select name="status" id="status" class="form-control">
                                    <option value="0" <?php echo ( $gift_status == 0 ) ? 'selected':''; ?> ><?php w('Inactive'); ?></option>
                                    <option value="1" <?php echo ( $gift_status == 1 ) ? 'selected':''; ?>><?php w('Active'); ?></option>
                                </select>
                            </div>
                            <input type="hidden" name="discount_type" value="giftcard">
                            <?php
                            $members = $this->getMembers();
                            
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
                                    echo '<option value="-1" data-not="not"> '.t(lcfirst($students).'s are not available ').'.</option>';
                                }
                                ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for=""><?php w('Notes'); ?></label>
                                <textarea name="notes" class="form-control"></textarea>
                            </div>
                            <div class="w-100">
                                <div class="mb-3">
                                    <button type="submit" class="btn btn-primary theme-button btn-block" id="save-gift-prd-btn"><i class="fas fa-check-circle"></i>
                                    &nbsp;<?php w('Save setup'); ?>
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


<div id="cfdisp-sfsnackbar"><?php w(ucfirst($students).' added successfully') ?>.</div>
<input type="hidden" id="discountGiftbaseurl" value="<?=CFGIFT_DISCOUNT_PLUGIN_URL;?>/">
<input type="hidden" id="giftdiscountajaxUrl" value="<?=get_option('install_url')?>/index.php?page=ajax">
<input type="hidden" id="giftdiscountinstall_url" value="<?=get_option('install_url')?>/">