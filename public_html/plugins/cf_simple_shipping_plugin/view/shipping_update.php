<?php
global $mysqli;
global $dbpref;
$table = $dbpref . 'ship_orders';
$table2 = $dbpref . 'all_products';
$store_id = false;
$abandoned = (isset($_GET['abandoned'])) ? true : false;

$store_id = $_GET['funnelid'];

$sales_ob = $this->load('form_controller');
$shipping_options = $sales_ob->shipping_options($store_id);

$total = 0;

$get_record_query = $mysqli->query("SELECT `id` FROM `" . $table . "` WHERE `funnelid` = " . $mysqli -> real_escape_string($store_id) . " ");
if (mysqli_num_rows($get_record_query) > 0) {
    $total = mysqli_num_rows($get_record_query);
}
$fnls = $sales_ob->getAllFunnels($store_id);
$s_avatar = function ($fname, $sales_ob) {
    return $sales_ob->text_to_avatar($fname);
};
// echo '<pre>';
// print_r($data);
// exit;	
?>
<style>
    .modal .modal-dialog {
        max-width: 700px;
    }

    .margin-class {
        margin-bottom: 3rem;
    }
</style>

<div class="container-fluid sf-store-selection">
    <div class="row">
        <div class="col-md-12">
            <div class="card br-rounded">
                <div class="card-body p-0 pt-2 px-2 sf-store-container">
                    <div class=" d-flex justify-content-between">
                        <div class="sf-store-list">
                            <h4 class="fw-7 text-white d-flex align-items-center">
                                <?php
                                echo $s_avatar($fnls[0]['name'], $setup_ob) . "<span class='text-dark d-inline-block px-2' style='font-weight:600'>" . $fnls[0]['name'] . "</sapn> ";
                                ?>
                            </h4>
                        </div>
                        <div><a href="index.php?page=send_shipping_update" class="btn btn-primary">Change Store</a></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="container-fluid">
    <div class="card pb-2  br-rounded" id="hidecard1">
        <div class="sforiginal p-2">
            <?php if (!isset($_GET['sell_id'])) { ?>
                <div class="row">
                    <!-- <div class="col-lg-2 col-md-12 mb-3">
                        <?php echo createSearchBoxBydate(); ?>
                    </div> -->
                    <div class="col-lg-4 col-md-12">
                        <?php echo showRecordCountSelection(); ?>
                    </div>
                    <div class="mb-3 col-lg-4 col-md-12">
                        <div class="input-group input-group-sm mb-3 float-end">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fas fa-search"></i></span>
                            </div>
                            <input type="text" class="form-control form-control-sm" placeholder="<?php w('Search With Name, Order Id, Email') ?>" onkeyup="searchOrder(this.value)">
                        </div>
                    </div>

                </div>
            <?php } ?>
        </div>
        <div class="card-body p-0" id="hidecard2">
            <div class="row membercontainer">
                <div class="col-sm-12">
                    <?php
                    ?>
                    <div id="crdcontainer">
                        <div id="container_singledata_table">
                            <div class="table-responsive sforiginal">
                                <table class="table table-striped">
                                    <thead>
                                        <tr class="salerow">
                                            <th>#</th>
                                            <th><?php w('Order&nbsp;Id'); ?></th>
                                            <th><?php w('Name'); ?></th>
                                            <th><?php w('Email'); ?></th>
                                            <th><?php w('Date'); ?></th>
                                            <th><?php w('Products'); ?></th>
                                            <th><?php w('Option'); ?></th>
                                        </tr>
                                    </thead>
                                    <tbody id="keywordsearchresult2">
                                        <!-- keyword search -->
                                        <?php
                                        $page_count = 0;
                                        if (isset($_GET['page_count'])) {
                                            $page_count = (int)$_GET['page_count'];
                                        }

                                        $all_data = $sales_ob->sales_data($store_id, $page_count);
                                        $last_id = 0;
                                        $count = ($page_count < 1) ? 1 : $page_count;
                                        $records_to_show = get_option('qfnl_max_records_per_page');
                                        $records_to_show = (int) $records_to_show;
                                        $count = ($count * $records_to_show) - $records_to_show;

                                        ++$count;
                                        foreach ($all_data as $all_data2) { ?>

                                            <tr>
                                                <td> <?php echo $count; ?></td>
                                                <td><?php echo $all_data2->order_id; ?></td>
                                                <td> <?php echo ucwords($all_data2->user_name); ?></td>
                                                <td> <?php echo ucwords($all_data2->contact); ?></td>
                                                <td> <?php echo date("d-m-Y",strtotime($all_data2->created_at)); ?></td>
                                                
                                                <td>
                                                    <a class="btn unstyled-button text-primary" data-bs-toggle="tooltip" title="" href="index.php?page=send_shipping_update&orderid=<?= $all_data2->id ?>" data-original-title="View Order Details"><i class="fas fa-eye"></i></a>
                                                    <?php
                                                    $products = unserialize($all_data2->products);
                                                    // echo '<pre>';
                                                    // print_r($products);
                                                    foreach ($products as $product) {

                                                        $sql = mysqli_fetch_assoc($mysqli->query("SELECT `productid`,`title`,`is_variant`,`parent_product` FROM `" . $table2 . "` WHERE `id` = " . $mysqli -> real_escape_string($product["product"]) . ""));
                                                        if (isset($sql['is_variant'])) {
                                                            if ($sql['is_variant'] == '1') {
                                                                $sql2 = mysqli_fetch_assoc($mysqli->query("SELECT `productid`,`title`,`parent_product` FROM `" . $table2 . "` WHERE `id` = " . $mysqli -> real_escape_string($sql['parent_product']) . ""));
                                                                echo $sql2['title'] . '&nbsp;(' . $product["quantity"] . ')<br>';
                                                            } else {
                                                                echo $sql['title'] . '&nbsp;(' . $product["quantity"] . ')<br>';
                                                            }
                                                        } else {
                                                            echo 'Product Removed.<br>';
                                                        }
                                                    }
                                                    ?>
                                                </td>

                                                <td>
                                                    <button class="btn btn-primary save_update" tracking_number_val="<?php echo $all_data2->tracking_number; ?>" carrier_url_val="<?php echo $all_data2->carrier_url; ?>" carrier_service_val="<?php echo $all_data2->carrier_service; ?>" data-id="<?php echo $all_data2->id; ?>" shipment_method_val="<?php echo $all_data2->shipment_method; ?>" data-bs-toggle="modal" data-target="#send_update">Send Update</button>
                                                </td>
                                            </tr>

                                        <?php
                                            $count++;
                                        } ?>
                                        <tr>
                                            <td colspan="12" class="total-data">
                                                <center> Total : <?= $total; ?></center>
                                            </td>
                                        </tr>
                                        <!-- /keyword search -->
                                    </tbody>
                                </table>
                            </div>
                            <div class="row mt-4">
                                <div class="col-sm-6 mt-2">
                                    <?php
                                    $next_page_url = "index.php?page=send_shipping_update&funnelid=$store_id&page_count";
                                    $page_count = ($page_count < 2) ? 0 : $page_count;
                                    echo createPager($total, $next_page_url, $page_count);
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="send_update" tabindex="-1" aria-labelledby="exampleModalLabel" style="display: none;" aria-hidden="true">
    <div class="container modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content border-0 p-4">
            <div class="modal-header border-0 py-4">
                <h4>Update Shipping Information:</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" id="shipping_zone_modle_close">
                    <span class="text-dark display-5" aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <form>
                    <div class="form-row">
                        <h6>Shipment Tracking Information</h6>
                        <div class="mb-3 col-xl-12">
                            <input type="text" id="tracking_number" placeholder="Enter Tracking Number" class="form-control">
                        </div>
                        <div class="mb-3 col-xl-12">
                            <input type="text" id="carrier_url" placeholder="Enter Carrier URL" class="form-control">
                        </div>
                        <div class="mb-3 col-xl-12 margin-class">
                            <input type="text" id="carrier_name" placeholder="Enter Carrier Name" class="form-control">
                        </div><br>
           
                        <button type="button" onclick="saveFullForm()" class="btn btn-outline-primary px-4 py-2">Save</button>
                        <button type="button" class="btn btn-secondary px-5 py-2 ms-3" data-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
    function searchOrder(search) {        
        var ob = new OnPageSearch(search, "#keywordsearchresult2");
        ob.url = window.location.href;
        ob.search();
    }

    var eventId = '';
    $(document).on("click", ".save_update", function() {
        eventId = $(this).data('id');
        if($(this).attr('tracking_number_val') != 'null'){
            $('#tracking_number').val($(this).attr('tracking_number_val'));
        }
        if($(this).attr('carrier_url_val') != 'null'){
            $('#carrier_url').val($(this).attr('carrier_url_val'));
        }
        if($(this).attr('carrier_service_val') != 'null'){
            $('#carrier_name').val($(this).attr('carrier_service_val'));
        }
 
    });

    function saveFullForm() {
        var tracking_number = $('#tracking_number').val();
        var carrier_url = $('#carrier_url').val();
        var carrier_name = $('#carrier_name').val();
        
        $.ajax({
            url: '<?php echo get_option("install_url") . "/index.php?page=ajax" ?>',
            type: "POST",
            data: {
                action: 'save_tracking_info',
                id: eventId,
                tracking_number: tracking_number,
                carrier_url: carrier_url,
                carrier_service: carrier_name
            },
            success: function(data) {
                if (data == '200') {
                    window.location.href = "index.php?page=send_shipping_update&email=" + eventId;
                } else {
                    alert('Something wrong');
                }
            },
            error: function(error) {
                console.log(error);
            }
        });

    }
</script>