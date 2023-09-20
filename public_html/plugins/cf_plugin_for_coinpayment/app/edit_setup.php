<div class="container-fluid" id="cfpay_payment_methods">
    <div class="row page-titles mb-4">
        <div class="col-md-5 align-self-center">
            <h4 class="text-themecolor" id="commoncontainerid">Coin Payment</h4>
        </div>
        <div class="col-md-7 align-self-center text-end">
            <div class="d-flex justify-content-end align-items-center">Create, edit and manage your payment methods</div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12 mt-4">
            <div class="align-items-center mb-4 shadow p-3 rounded alert alert-success w-100">
                Use this IPN URL &nbsp;<span class="text-success"><strong style="cursor:pointer;" data-bs-toggle="tooltip" data-placement="top" title="" v-on:click="cfCoinpaymentCopyText(`<?= get_option('install_url') . '/index.php?page=callback_api&action=coinpayment_ipn' ?>`)" data-original-title="Click to copy"><?= get_option('install_url') . '/index.php?page=callback_api&action=coinpayment_ipn' ?></strong></span> &nbsp; to add the IPN URL to your Coinpayment.
            </div>
        </div>
    </div>


    <div class="row">
        <div class="col-sm-12" v-if="open_setup">
            <div class="row justify-content-center align-items-center">
                <!-- script for popup -->
                <div class="col-sm-6">
                    <div class="card pnl visual-pnl">
                        <div class="card-header">
                            <div class="row">

                                <div class="col-md-12">
                                    <span v-if="selected_method_data.title !==undefined" v-html="selected_method_data.title"></span>
                                    <!-- <span class="closebutton" v-on:click="showSetup(false)"><i class="fas fa-times-circle"></i></span> -->
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div v-if="selected_method_data.setup_header !==undefined" v-html="selected_method_data.setup_header"></div>
                            <div class="mb-3" v-for="(val,index) in selected_method_data.fields" data-bs-toggle="tooltip" v-bind:title="val.title">
                                <label v-if="val.label !==undefined" v-html="val.label"></label>
                                <div v-if="val.type!==undefined && val.type=='select'">
                                    <select class="form-control" v-model="val.value">
                                        <option v-if="val.options !==undefined" v-for="option in val.options" v-bind:value="option.value">{{option.name}}</option>
                                    </select>
                                </div>
                                <div v-else-if="val.type!==undefined && val.type=='textarea'">
                                    <textarea class="form-control" v-model="val.value" v-bind:placeholder="val.placeholder"></textarea>
                                </div>
                                <div v-else-if="val.type!==undefined">
                                    <input v-bind:type="val.type" class="form-control" v-bind:placeholder="val.placeholder" v-model="val.value">
                                </div>
                                <div v-else>
                                    <input type="text" class="form-control" v-model="val.value">
                                </div>
                            </div>
                            <div class="mb-3">
                                <p class="text-center text-success" style="font-weight:800px; margin-bottom:10px !important;" v-if="success_msg.trim().length>0" v-html="success_msg"></p>
                                <p class="text-center text-danger" style="font-weight:800px; margin-bottom:10px !important;" v-if="err.trim().length>0" v-html="err"></p>
                                <button class="btn btn-primary btn-block" v-on:click="saveSetup()">Save Settings</button>
                            </div>
                            <div v-if="selected_method_data.setup_footer !==undefined" v-html="selected_method_data.setup_footer"></div>
                        </div>
                    </div>
                </div>
                <!-- ends here -->
            </div>
        </div>
    </div>
</div>
<input type="hidden" id="cfpay_base_url" value="<?php echo get_option('install_url'); ?>">
<input type="hidden" id="cfpay_plugin_base_url" value="<?php echo plugins_url('../', __FILE__); ?>">
<div id="cfCoinpayment_snackbar" class="shadow p-3 rounded alert alert-success">Copied Sucessfully</div>

<style>
    #cfCoinpayment_snackbar {
        visibility: hidden;
        min-width: 250px;
        margin-left: -125px;
        text-align: center;
        border-radius: 2px;
        padding: 16px;
        position: fixed;
        z-index: 1;
        left: 50%;
        bottom: 50px;
        font-size: 17px;
    }

    #cfCoinpayment_snackbar.show {
        visibility: visible;
        -webkit-animation: fadein 0.5s, fadeout 0.5s 2.5s;
        animation: fadein 0.5s, fadeout 0.5s 2.5s;
    }

    @-webkit-keyframes fadein {
        from {
            bottom: 0;
            opacity: 0;
        }

        to {
            bottom: 30px;
            opacity: 1;
        }
    }

    @keyframes fadein {
        from {
            bottom: 0;
            opacity: 0;
        }

        to {
            bottom: 30px;
            opacity: 1;
        }
    }

    @-webkit-keyframes fadeout {
        from {
            bottom: 30px;
            opacity: 1;
        }

        to {
            bottom: 0;
            opacity: 0;
        }
    }

    @keyframes fadeout {
        from {
            bottom: 30px;
            opacity: 1;
        }

        to {
            bottom: 0;
            opacity: 0;
        }
    }
</style>
<!-- data for saved method -->
<?php
if (isset($_GET['id'])) {
    $data = $this->getSetup($_GET['id']);
    if ($data) {
        echo "<input type='hidden' id='cfpay_saved_id' value='" . $data->id . "'>";
        echo "<input type='hidden' id='cfpay_saved_title' value='" . str_replace("'", "&singlequot;", $data->title) . "'>";
        echo "<input type='hidden' id='cfpay_saved_method' value='" . str_replace("'", "&singlequot;", $data->method) . "'>";
        echo "<input type='hidden' id='cfpay_saved_fields' value='" . str_replace("'", "&singlequot;", $data->credentials) . "'>";
        echo "<input type='hidden' id='cfpay_saved_tax' value='" . str_replace("'", "&singlequot;", $data->tax) . "'>";
    }
}
?>