<?php
global $mysqli;
global $dbpref;
$table = $dbpref . 'all_products';
if (isset($_GET['orderid'])) {
    $id = $_GET['orderid'];
    $forms_ob = $this->load('form_controller');
    $data = $forms_ob->order_data($id);

    $pro_array = unserialize($data[0]->products);
    $shippingdata = unserialize($data[0]->shippingdata);

?>

    <div class="row membercontainer" style="width: -webkit-fill-available;">
        <div class="col-sm-12">
            <div id="crdcontainer">
                <div id="container_singledata_table">
                    <div><span id="gobacktable" class="gobacktable" style="float: right; color: rgb(31, 87, 202); font-size: 16px; margin-bottom: 10px; cursor: pointer;"><i class="fas fa-arrow-alt-circle-left"></i> Go&nbsp;back</span><br></div>
                    <div class="card custdetail pnl" style="width:100%;">
                        <div class="card-header">
                            <h4 style="margin:0px;font-size:15px;">Order Id: &nbsp;<?php echo $data[0]->order_id ?></h4>
                        </div>

                        <div class="card-body">
                            <?php foreach ($pro_array as $product) {  ?>
                                <p>Product: <a href="index.php?page=products&amp;store_id=<?php echo $data[0]->funnelid; ?>&amp;product_id=<?php echo $product['product'] ?>" target="_BLANK">
                                        <?php $sql = mysqli_fetch_assoc($mysqli->query("SELECT `productid`,`title`,`is_variant`,`parent_product` FROM `" . $table . "` WHERE `id` = " . $mysqli -> real_escape_string($product["product"]) . ""));
                                        if ($sql['is_variant'] == '1') {
                                            $sql2 = mysqli_fetch_assoc($mysqli->query("SELECT `productid`,`title`,`parent_product` FROM `" . $table . "` WHERE `id` = " . $mysqli -> real_escape_string($sql['parent_product']) . ""));
                                            echo '(' . $sql2['productid'] . ')&nbsp;&nbsp;' . $sql2['title'] . '&nbsp;X&nbsp;' . $product["quantity"] . '<br>';
                                        } else {
                                            echo '(' . $sql['productid'] . ')&nbsp;&nbsp;' . $sql['title'] . '&nbsp;X&nbsp;' . $product["quantity"] . '<br>';
                                        }
                                        ?></a></p>
                            <?php } ?>
                            <p>Customer Name: <?php echo $data[0]->user_name; ?></p>
                            <p>Customer Email: <?php echo $data[0]->contact ?></p>
                            <p>Total Amount: $<?php echo $data[0]->amount; ?></p>
                            <p>Purchased On: <?php echo date("d-m-Y, h:i a",strtotime($data[0]->created_at)); ?></p>
                            <p>Shipment Method: <?php echo $data[0]->shipment_method; ?></p>

                        </div>

                    </div>
                    <style>
                        div.custdetail p {
                            font-size: 14px;
                        }
                    </style><!-- Shipping Detail -->
                    <div class="card pnl ">
                        <!-- <div class="card-header">
                            <h4 style="margin:0px;font-size:15px;">Order Detail</h4>
                        </div> -->
                        <div class="card-body row">
                            <div class="col-sm-12">
                                <button data-bs-toggle="collapse" class="btn btn-info btn-block" style="border:0px;"><span style="float:left;"><strong>Order Details</strong></span></button>

                                <br>
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr></tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>Email</td>
                                                <td><?php echo $shippingdata['email']; ?></td>
                                            </tr>
                                            <tr>
                                                <td>Firstname</td>
                                                <td><?php echo $shippingdata['firstname']; ?></td>
                                            </tr>
                                            <tr>
                                                <td>Lastname</td>
                                                <td><?php echo $shippingdata['lastname']; ?></td>
                                            </tr>
                                            <tr>
                                                <td>Shipping_address</td>
                                                <td><?php echo $shippingdata['shipping_address']; ?></td>
                                            </tr>
                                            <tr>
                                                <td>Shipping_address2</td>
                                                <td><?php echo $shippingdata['shipping_address2']; ?></td>
                                            </tr>
                                            <tr>
                                                <td>Shipping_city</td>
                                                <td><?php echo $shippingdata['shipping_city']; ?></td>
                                            </tr>
                                            <tr>
                                                <td>Shipping_country</td>
                                                <td><?php echo $shippingdata['shipping_country']; ?></td>
                                            </tr>
                                            <tr>
                                                <td>Shipping_state</td>
                                                <td><?php echo $shippingdata['shipping_state']; ?></td>
                                            </tr>
                                            <tr>
                                                <td>Shipping_pincode</td>
                                                <td><?php echo $shippingdata['shipping_pincode']; ?></td>
                                            </tr>                                     
                                            <tr>
                                                <td>Name</td>
                                                <td><?php echo $shippingdata['firstname'] . ' ' . $shippingdata['lastname'] ; ?></td>
                                            </tr>
                                        </tbody>
                                        <thead>
                                            <tr>
                                                <th colspan="2">Payment Method</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td colspan="2"><?php echo $data[0]->payment_method; ?></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                    
                        </div>

                    </div><span id="gobacktable" data-direction="reverse" style="float: right; color: rgb(31, 87, 202); font-size: 16px; margin-bottom: 10px; cursor: pointer;"><i class="fas fa-arrow-alt-circle-left"></i> Go&nbsp;back</span><br>
                </div>

            </div>

        </div>
    </div>
<?php
}

?>
<script>
    $('span#gobacktable').on('click', function(e) {
        e.preventDefault();
        window.history.back();
    });
</script>