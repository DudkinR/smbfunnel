<?php
global $mysqli;
$install_url = get_option("install_url");
if( isset( $_GET['store_id'] ) )
{
    $store_id = $_GET['store_id'];

}else{
    $install_url = $install_url."/index.php?page=cfdiscount_giftcards_products"; 
    header("Location: ".$install_url."");
}
$total_setup=0;
$setup_ob=$this->load('giftcard');
$total_setups=0;
$giftcard_id=false;

$page_count=1;

$product_id=false;

if( isset( $_GET['product_id'] ) && $_GET['page']=="cfdiscount_add_giftproduct" && is_numeric($_GET['product_id'] ) )
{
    $product_id = $_GET['product_id'];

}

$producds = $setup_ob->getGiftCardProduct( $product_id );
if( count( $producds ) > 0 )
{
    $title  = $producds['title'];
    $p_id  = $producds['productid'];
    $def_product_page  = $producds['def_product_page'];
    $desc   = $producds['description'];
    $medias = json_decode($producds['media'],true);
    $has_variant  = $producds['has_variant'];
    $is_active  = $producds['is_active'];
    $p_type  = $producds['p_type'];
    $currency  = $producds['currency'];
    $gcollection  = json_decode($producds['collections'],true);
    $tags  = $producds['tags'];
}else{

    $title  = '';
    $product_id  = '';
    $p_id  = false;
    $desc   = '';
    $medias = [];
    $has_variant  = false;
    $is_active  = 0;
    $currency  = '';
    $variantsdata  = [];
    $gcollection  = [];
    $tags  = '';
}
$fnls=$setup_ob->getAllFunnels($store_id);
$s_avatar= function($s_txt,$setup_ob){return $setup_ob->text_to_avatar($s_txt);};
$pages= get_funnel_pages($_GET['store_id']);
if(is_array($pages)){
    $pages= array_filter($pages, function($page){
        return ($page['category']==='product')? true: false;
    });

    $pages= array_map(function($page){
        $page['url']= str_replace('@@qfnl_install_url@@', get_option('install_url'), $page['url']);
        return $page;
    }, $pages);

}
?>
<div class="container-fluid">
    <div class="row page-titles mb-4">
        <div class="col-md-5 align-self-center">
            <a href="index.php?page=cfdiscount_giftcards_products">
            <div class="d-flex text-white align-items-center">
                <?php
                echo $s_avatar($fnls[0]['name'],$setup_ob)."<span class='text-dark d-inline-block px-2' style='font-weight:600'>".$fnls[0]['name']."</sapn> ";
                ?>
                </div>
            </a>
        </div>
        <div class="col-md-7 align-self-center text-end">
            <div class="d-flex justify-content-end align-items-center">
                <div><a href="index.php?page=cfdiscount_giftcards_products&store_id=<?=$store_id?>" class="btn btn-primary"><?php w('Go Back'); ?></a></div>
            </div>
        </div>
    </div>
    <div id="cfdisp-success-class" tabindex="-1">
    </div>
    <div id="cfdisp-error-class" tabindex="-1">
    </div>
    <form id="add-giftcard-form">
        <div class="row d-flex justify-content-around">
            <div class="col-md-7 setup-section-1">
                <div class="row">
                    <!---->
                    <div class="col-sm-12">
                        <div class="card pb-2  br-rounded">
                            <div class="card-body"><label class="f-20"><?php w('Basic detail'); ?></label>
                            <input type="hidden" name="funnelid" id="funnelid" value="<?=$store_id?>" />
                                <input type="hidden" id="savegiftcardsproduct"  value="<?=($product_id)?'update':'save';?>" name="savegiftcardsproduct"
                                    class="form-control">
                                <input type="hidden" id="giftcardprd_id" name="giftcardprd_id"
                                    value="<?= $product_id; ?>">
                                    <input type="hidden" value="savegiftproducts_ajax" name="action" >
                                <div class="mb-3">
                                    <label><?php w('Product Id'); ?></label> <input type="text" id="productid" 
                                        name="productid" value="<?= $p_id; ?>" class="form-control">
                                </div>
                                <!---->
                                <div class="mb-3"><label><?php w('Enter title'); ?></label> <input type="text"
                                        placeholder="Enter title" value="<?=$title ?>" name="title" class="form-control"
                                        id="title"></div>
                                <div class="mb-3">
                                    <label><?php w('Select default product page'); ?></label>
                                    <div class="input-group">
                                        <select class="form-control" id="product-review-page-select" name="def_product_page">
                                            <?php
                                                if( count( $pages ) > 0 )
                                                {
                                                    foreach($pages as $page)
                                                    {
                                                        if($page['id']==$def_product_page)
                                                        {
                                                            echo '<option value="'.$page['id'].'" selected data-url="'.$page['url'].'">'.$page['file_name'].'</option>';
                                                        }else{
                                                            echo '<option value="'.$page['id'].'" data-url="'.$page['url'].'">'.$page['file_name'].'</option>';
                                                        }
                                                    }
                                                }else{
                                                    echo 'option value="0">'.t('No page selected').'</option>';
                                                }
                                            ?>
                                        </select>
                                        <div class="input-group-append">
                                            <button type="button"
                                            class="btn input-group-text cp" 
                                            data-bs-toggle="tooltip" 
                                            title= "Preview"
                                            id="product-review-icon">
                                                <i class="fas fa-eye text-primary"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label><?php w('Description'); ?></label> <textarea id="giftproduct_description" aria-hidden="true"
                                        class="form-control" name="description"><?=$desc;?></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-12">
                        <div class="card pb-2  br-rounded">
                            <div class="card-body">
                                <div class="col-sm-12 product-media-selector">
                                    <div class="row">
                                        <div class="col-sm-5"><label class="f-20"><?php w('Media'); ?></label></div>
                                        <div class="col-sm-7 text-end">
                                            <div class="dropdown  mt-2">
                                                <button type="button" data-bs-toggle="dropdown"
                                                    class="btn btn-border-primary dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                    <?php w('Add media from URL'); ?>
                                                </button>
                                                <div class="dropdown-menu dropdown-menu-media p-4"  aria-labelledby="dropdownMenuButton">
                                                    <div class="mb-3"><label><?php w('Provide media type and URL'); ?></label>
                                                        <div class="input-group">
                                                            <div class="input-group-prepend">
                                                                <select  class="input-group-text" id="media_type">
                                                                    <option value="0"><?php w('Type'); ?></option>
                                                                    <option value="image"><?php w('Image'); ?></option>
                                                                    <option value="youtube"><?php w('Youtube'); ?></option>
                                                                    <option value="vimeo"><?php w('Vimeo'); ?></option>
                                                                </select>
                                                            </div> 
                                                            <input type="text" placeholder="<?php w('Enter URL'); ?>" id="media_url"  class="form-control">
                                                        </div>
                                                    </div>
                                                    <div class="mb-3">
                                                        <div class="row">
                                                            <div class="col-sm-8"> </div>
                                                            <div class="col-sm-4">
                                                                <button type="submit" id="add_media_url_f" class="btn btn-outline-secondary btn-block"><?php w('Add'); ?></button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="container-fluid" id="cfdisp-delete-produc-images">
                                                <div class="row">
                                                    <div class="col-sm-12 text-end">
                                                        <button type="button"
                                                            class="btn images-del-btn unstyled-btn text-danger">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                            <!---->
                                        </div>
                                    </div>
                                    <div class="row  <?= ( count($medias) >0)?'inline-overflow':'' ?> mt-2">
                                        <?php
                                            if( count($medias) >0 )
                                            {
                                                foreach($medias as $media)
                                                {   
                                                ?>
                                        <div class="col-sm-4 item-selection mt-2 me-2 mb-4 h-180">
                                            <div class="row w-200-pc">
                                                <div class="col-sm-12">
                                                    <div class="mb-3">
                                                        <input type="checkbox" data-bs-toggle="tooltip"
                                                            class="delete-image-box" title="Select" value="0">
                                                        <input type="hidden" name='media[]' data-bs-toggle="tooltip"
                                                            title="Select" value="<?=$media['media']; ?>">
                                                        <input type="hidden" name='type[]' data-bs-toggle="tooltip"
                                                            title="Select" value="<?=$media['type']; ?>">
                                                    </div>
                                                </div>
                                                <div class="col-sm-12 media-container">
                                                    <div class="media img">
                                                        <?php
                                                                        if( $media['type']=="image" )
                                                                        {
                                                                            echo "<img src='".$media['media']."' alt='".$media['media']."' class='img-fluid'>";
                                                                        }
                                                                        else if($media['type']=="video" )
                                                                        {
                                                                            echo "<video style='width: 100%;'><source src='".$media['media']."'></video>";
                                                                        }
                                                                        else if( $media['type']=="audio" )
                                                                        {
                                                                            echo "<audio style='width: 100%;'><source src='".$media['media']."'></audio>";
                                                                        }else{
                                                                            echo "<img src='".$media['media']."' alt='".$media['media']."' class='img-fluid'>";
                                                                        }
                                                                    ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <?php
                                                }
                                                ?>
                                        <div class="col-sm-4 item-selection  mt-2 me-2 mb-4 h-180 flex-center-column cp "
                                            id="add-media-box">
                                            <button type="button" class="uploader upload-gift-image">
                                                <i class="fas fa-angle-up"></i>
                                            </button>
                                            <button type="button"
                                                class="btn btn-outline-secondary upload-gift-image"><?php w('Add Media'); ?></button>
                                        </div>
                                        <?php
                                            }else{
                                                ?>
                                        <div class="col-sm-12 item-selection h-220 flex-center-column cp "
                                            id="add-media-box">
                                            <button type="button" class="uploader upload-gift-image">
                                                <i class="fas fa-angle-up"></i>
                                            </button>
                                            <h5 class="text-gray p5"><?php w('No items are available!'); ?></h5>
                                            <button type="button"
                                                class="btn btn-outline-secondary upload-gift-image"><?php w('Add Media'); ?></button>
                                        </div>
                                        <?php
                                            }
                                        ?>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-sm-12">
                        <div class="card pb-2  br-rounded">
                            <div class="card-body">
                                <div class="mb-3">
                                    <label><?php w('Currency'); ?></label>
                                    <input type="hidden" id="set-currency" value="<?=$currency?>">
                                    <input type="hidden" name="currency_symbol" id="currency_symbol" value="">
                                    <select class="form-control" id="currency" name="currency">
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
                                <br>
                                <label><?php w('Denominations'); ?></label> <br>
                                <?php
                                     if( $has_variant )
                                     {
                                         $variantdatas = $setup_ob->getGiftCardProductVariant( $product_id );
                                         ?>
                                <div class="mb-3">
                                    <br>
                                    <div id="varient_container" class="table-responsive better-scroll">
                                        <table class="table table-striped" style="min-width: 710px;">
                                            <thead class="sticky-table-header">
                                                <tr>
                                                    <th>#</th>
                                                    <th><?php w('Media'); ?></th>
                                                    <th><?php w('Denominations'); ?></th>
                                                    <th><?php w('Price'); ?></th>
                                                    <th><?php w('SKU'); ?></th>
                                                    <th><?php w('Action'); ?></th>
                                                </tr>
                                            </thead>
                                            <tbody id="varient_row">
                                                <?php
                                                        foreach($variantdatas as $variantdata)
                                                        {
                                                            ?>
                                                <tr class="del-variant-id" data-id="<?= $variantdata['id']; ?>">
                                                    <td>
                                                        <input type="checkbox" class="form-control"   value="<?= $variantdata['productid']; ?>">
                                                        <input type="hidden" class="form-control"  name="denominations[variant_id][]"  value="<?= $variantdata['productid']; ?>">
                                                    </td>
                                                    <td style="width:100px">
                                                        <div class="container-fluid media_view_media cp">
                                                            <div class="row">
                                                                <div class="col-sm-12 media-container">
                                                                    <div class="media img change-media-ve">
                                                                        <?php
                                                                                    if( count( json_decode($variantdata['media'],true) ) > 0 )
                                                                                    {
                                                                                        $vmedia =json_decode($variantdata['media'],true); 
        
                                                                                            if( $vmedia[0]['type']=="image" )
                                                                                            {
                                                                                                echo "<img src='".$media['media']."' alt='".$vmedia[0]['media']."' class='img-fluid'>";
                                                                                                echo '<input name="denominations[variant_mtype][]" type="hidden" value="image" />';

                                                                                            }
                                                                                            else if($vmedia[0]['type']=="video" )
                                                                                            {
                                                                                                echo "<video style='width: 100%;'><source src='".$vmedia[0]['media']."'></video>";
                                                                                                echo '<input name="denominations[variant_mtype][]" type="hidden" value="video" />';
                                                                                            }
                                                                                            else if( $vmedia[0]['type']=="audio" )
                                                                                            {
                                                                                                echo "<audio style='width: 100%;'><source src='".$vmedia[0]['media']."'></audio>";
                                                                                                echo '<input name="denominations[variant_mtype][]" type="hidden" value="audio" />';
                                                                                            }else{
                                                                                                echo "<img src='".$vmedia[0]['media']."' alt='".$vmedia[0]['media']."' class='img-fluid'>";
                                                                                                echo '<input name="denominations[variant_mtype][]" type="hidden" value="image" />';
                                                                                            }
                                                                                            echo '<input name="denominations[variant_media][]" type="hidden" value="'.$media['media'].'" />';
        
                                                                                    }else{
                                                                                        echo ' <img src="assets/img/no-media.png" class="img-responsive">';
                                                                                        echo '<input name="denominations[variant_media][]" type="hidden" value="" />';
                                                                                        echo '<input name="denominations[variant_mtype][]" type="hidden" value="" />';
                                                                                    }
                                                                                ?>

                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="mb-3">
                                                            <input name="denominations[title][]"
                                                                value="<?=$variantdata['title']?>" type="text"
                                                                class="form-control">
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="mb-3">
                                                            <input name="denominations[price][]"
                                                                value="<?=$variantdata['price']?>" type="number"
                                                                class="form-control">
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="mb-3">
                                                            <input name="denominations[sku][]"
                                                                value="<?=$variantdata['sku']?>" type="text"
                                                                class="form-control">
                                                        </div>
                                                    </td>
                                                    <td class="sticky-action-col">
                                                        <div class="mb-3">
                                                            <table class="actionedittable">
                                                                <tbody>
                                                                    <tr>
                                                                        <td><a target="_blank" href="index.php?page=products&amp;store_id=<?= $store_id; ?>&amp;variant=<?= $variantdata['productid']; ?>&amp;product_id=<?=$product_id; ?>"  data-bs-toggle="tooltip" title=""
                                                                                class="btn unstyled-button" data-original-title="Edit">
                                                                                <i  class="fas fa-edit text-primary"></i>
                                                                            </a>
                                                                        </td>
                                                                        <td>
                                                                            <button data-bs-toggle="tooltip" title="Delete"
                                                                                class="btn unstyled-button varient_delete">
                                                                                <i class="fas fa-trash text-danger"></i>
                                                                            </button>
                                                                        </td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <?php
                                                        }
                                                    ?>

                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <?php

                                     }else{
                                         ?>
                                <div id="denominationss">
                                    <div class="add-denominations">
                                        <?php
                                                    for( $j=1; $j<5;$j++ )
                                                    {
                                                        $varient_id = time()."_".$setup_ob->random_strings(7);
                                                        ?>
                                        <div class="mb-3 denominations-box">
                                            <div class="row">
                                                <div class="col-sm-10">
                                                    <input type="number" name="denominations[<?=$varient_id;?>]"
                                                        value="<?=$j?>0.00" min="0"
                                                        class="form-control denominations_inp">
                                                </div>
                                                <div class="col-sm-2">
                                                    <button data-bs-toggle="tooltip" title="Delete"
                                                        class="btn btn-outline-danger btn-delete-denominations">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                        <?php
                                                    }
                                                ?>
                                    </div>
                                    <hr>
                                    <button type="button" class="btn btn-primary btn-sm" id="add-denominations-m"><?php w('Add Denomination'); ?> </button>
                                </div>
                                <?php
                                     }
                                    ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4 setup-section-2">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="card pb-2  br-rounded">
                            <div class="card-body"><label class="f-20"><?php w('Status'); ?></label>
                                <div class="mb-3">
                                    <select name="is_active" class="form-control">
                                        <option value="0" <?= ( $is_active==0 ) ? 'selected':''; ?>><?php w('Inactive'); ?></option>
                                        <option value="1" <?= ( $is_active==1 ) ? 'selected':''; ?>><?php w('Active'); ?></option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-12">
                        <div class="card pb-2  br-rounded">
                            <div class="card-body">
                                <div><label class="f-20"><?php w('Others'); ?></label>
                                    <div class="mb-3"><label><?php w('Product type'); ?></label>
                                        <input value="Gift Card" readonly disabled type="text" class="form-control">
                                        <input value="gift_card" value="<?= $p_type; ?>" name="p_type" type="hidden"
                                            class="form-control">
                                    </div>
                                    <?php $collections = $setup_ob->getCollection($store_id); ?>
                                    <div class="mb-3 select-product" id="select-proudctr-div">
                                        <label class="fs16px fw6"><?php w('Collection');?></label>
                                        <select name="collections[]" multiple id="collection-select"
                                            class="form-control">
                                            <?php
                                                if ( count($collections) > 0 )
                                                {
                                                    foreach( $collections as $collection )
                                                    {
                                                        if(in_array( $collection['id'], $gcollection ) )
                                                        {
                                                            echo '<option selected value="'.$collection['id'].'">'.$collection['title'].'</option>';
                                                        }else{
                                                            echo '<option  value="'.$collection['id'].'">'.$collection['title'].'</option>';
                                                        }
                                                    }
                                                }else{
                                                    echo '<option value="-1"> '.t('Collection is not available').'</option>';
                                                }
                                            ?>
                                        </select>
                                    </div>
                                    <div class="mb-3"><label><?php w('Tags'); ?></label>
                                        <input type="text" name="tags" value="<?= $tags; ?>"
                                            placeholder="<?php w('E.g. Cotton, summer'); ?>" class="form-control">
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <button type="submit" id="save-gift-prd-btn"
                                        class="btn btn-primary theme-button btn-block">
                                        <i class="fas fa-check-circle"></i>
                                        &nbsp;<?php w('Save setup'); ?>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
<?php
	register_tiny_editor(array('#giftproduct_description'));
	cf_media(true);
?>
<div id="cfdisp-sfsnackbar"><?php w(ucfirst($students)." added successfully"); ?>.</div>
<input type="hidden" id="discountGiftbaseurl" value="<?=CFGIFT_DISCOUNT_PLUGIN_URL;?>/">
<input type="hidden" id="giftdiscountajaxUrl" value="<?=get_option('install_url')?>/index.php?page=ajax">
<input type="hidden" id="giftdiscountinstall_url" value="<?=get_option('install_url')?>/">

<?php

if( isset( $_GET['page'] ) && ($_GET['page']=="cfdiscount_giftcards_products"  || $_GET['page']=="cfdiscount_add_giftproduct") )
{
    ?>
    <script>
      let sideBarPluginLinks= document.querySelectorAll(`.sidebar-submenu a`);
            sideBarPluginLinks.forEach(link=>{
                if(typeof(link.getAttribute('href'))==='string' &&  (  link.href.indexOf('cfdiscount_giftcards_products') !=-1 ||  link.href.indexOf('cfdiscount_add_giftproduct') !=-1 ) ){
                    let url= new URL(link.href);
                    url.searchParams.append('store_id', `<?= (int)$_GET['store_id']  ?>`);
                    link.href= url.href;
                }
            });
</script>
    
    <?php

}
?>
