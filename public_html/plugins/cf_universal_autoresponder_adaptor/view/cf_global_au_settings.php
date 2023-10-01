<?php
$forms_ob = $this -> load('form_controller');
global $mysqli;
global $dbpref;
$table=$dbpref.'quick_autoresponders';
$user_id=$_SESSION['user' . get_option('site_token')];
$access=$_SESSION['access' . get_option('site_token')];
if(isset($_GET['cfglobal_au_id']))
{
    if( is_numeric( $_GET['cfglobal_au_id'] ) ) $getFormId = $_GET['cfglobal_au_id'];
    else
    {
        header('location: '.get_option('install_url').'/index.php?page=no-permission');
        die();
    }

    $getFormId = $mysqli->real_escape_string($getFormId);
    if($access == 'admin')
    {
        $result = $mysqli->query( "SELECT * FROM `".$table."` WHERE `id`=$getFormId" );
    }
    else
    {
        $result = $mysqli->query( "SELECT * FROM `".$table."` WHERE `id`=$getFormId AND `user_id`=$user_id" );
    }

    if( mysqli_num_rows($result)>0 )
    {
        $data = $result->fetch_assoc( );
        $cfglobal_exf = json_decode($data['exf'], true);
        $autoresponder_details = json_decode($data['autoresponder_detail'], true);
        $cfglobal_title = $data['autoresponder'];
        $auth_details = $autoresponder_details['Authorization'];
        $body_data_format = (isset($autoresponder_details['body_data_format'])?$autoresponder_details['body_data_format']:1);
        unset($autoresponder_details['body_data_format']);
        unset($autoresponder_details['Authorization']);
        if($auth_details=="")
        {
            $cfglobal_authRequired = 0;
            $cfglobal_username = "";
            $cfglobal_password = "";
        }
        else
        {
            $cfglobal_authRequired = 1;
            $exploded_data = explode(' ', $auth_details)[1];
            $decoded_data = explode(':', base64_decode($exploded_data));
            $cfglobal_username = $decoded_data[0];
            $cfglobal_password = $decoded_data[1];
        }
        foreach($cfglobal_exf as $key=>$value)
        {
            if(isset($value['api_url']))
            {
                $cfglobal_api_url = $value['api_url'];
                unset($cfglobal_exf[$key]['api_url']);
            }
            if(isset($value['form_method']))
            {
                $cfglobal_form_method = $value['form_method'];
                unset($cfglobal_exf[$key]['form_method']);
                $cfglobal_exf = array_filter($cfglobal_exf);
            }
        }
    }

    else
    {
        $cfglobal_authRequired = 0;
        $cfglobal_username = "";
        $cfglobal_password = "";
        $getFormId = 0;
        $cfglobal_title = "";
        $cfglobal_api_url = "";
        $body_data_format = 1;
        $cfglobal_form_method = "POST";
    }
}

else
{
    $body_data_format = 1;
    $cfglobal_username = "";
    $cfglobal_password = "";
    $cfglobal_authRequired = 0;
    $getFormId = 0;
    $cfglobal_title = "";
    $cfglobal_api_url = "";
    $cfglobal_form_method = "POST";
}
?>
<div class="container-fluid bg-white px-lg-4">

    <!--
        ================================
        CF Universal Autoresponder Adaptor Settings
                  Heading
        ================================
    -->
    <div class="row">
        <div class="col-md-12 mt-4">
            <div class="d-sm-flex align-items-center justify-content-between mb-4 shadow p-3 rounded">
                <h6 class="mb-0 text-success">CF Universal Autoresponder Adaptor Settings</h6>
                <div class="text-info">Create, edit, manage forms</div>
            </div>
        </div>
    </div>

    <!--
        ================================
                Settings Section
        ================================
    -->

    <div class="container-fluid">
        <div class="row">
            <div class="mt-4" style="width: 100%;">
                <div class="modal fade" id="cfGlobalReuestStatus" tabindex="-1" role="dialog">
                    <div class="modal-dialog modal-lg" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Request Status</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <div id="showMessageAfterCurl"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <button type="button" class="p-3 shadow rounded btn btn-primary h5" data-bs-toggle="modal" data-target="#exportHTML">Import HTML Form Code</button>
            <div class="modal fade" id="exportHTML" tabindex="-1" role="dialog">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Paste your HTML Code</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <textarea class="form-control" rows="15" id="cfglobal_textarea" placeholder="Paste your HTML code here..."></textarea>
                            <button class="btn btn-primary mt-3" onclick="setDataToInputs()">Import</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <form method="post" id="global_au_settings_form">
        <div class="row">
            <div class="col-md-6 col-lg-6 mt-4">
                <h5 class="p-3 shadow global-top-border rounded">Form Settings</h5>
                <div class="mb-4 shadow p-3 rounded global-bottom-border">
                    <input type="hidden" id="cfglobalau_ajax" name="cfglobalau_cfglobalau_ajax" value="<?php echo  get_option('install_url')."/index.php?page=ajax"; ?>" />
                    <input type="hidden" name="cfglobalau_update_insert" id="cfglobalau_update_insert" value="<?php echo ((isset($getFormId) && $getFormId != 0)? 'UPDATE':'CREATE'); ?>" />
                    <input type="hidden" name="cfglobalau_form_id" value="<?=cf_enc($getFormId,"encrypt"); ?>">
                    <input type="hidden" name="cfglobal_ajax_insertUrl" value="<?php echo get_option('install_url')."/index.php?page=cf_global_au_settings&cfglobal_au_id="; ?>" />

                    <div class="mb-3">
                        <h6 class="mb-2">Enter title</h6>
                        <input type="text" id="cfglobal_input_title" placeholder="Enter title" name="cfglobal_title" class="form-control mb-4" value="<?= $cfglobal_title ?>" required />
                    </div>

                    <div class="mb-3">
                        <h6 class="mb-2">API URL</h6>
                        <input type="url" id="cfglobal_api_url" name="cfglobal_api_url" placeholder="Enter API URL" value="<?= $cfglobal_api_url ?>" class="form-control mb-4" />
                    </div>
                    
                    <div class="mb-3">
                        <h6 class="mb-2">Form Method</h6>
                        <div class="cfglobal_dropdown">
                            <input type="text" name="cfglobal_form_methods" class="cfglobal_textBox" id="cfglobal_form_methods" value="<?= $cfglobal_form_method ?>" readonly>
                            <div class="option">
                                <div onclick="cfglobal_show_dropdown('POST')">POST</div>
                                <div onclick="cfglobal_show_dropdown('GET')">GET</div>
                            </div>
                        </div>
                    </div>

                    <h6 class="mt-4">Body Data Format</h6>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="body_data_format" id="body_data_format1" onclick="setValueToInputOnHeader('application/json')" value="1" <?= ($body_data_format)?'checked':'' ?> />
                        <label class="form-check-label" for="body_data_format1">application/x-www-form-urlencoded</label>
                    </div>
                    <div class="form-check mb-5">
                        <input class="form-check-input" <?= ($body_data_format)?'':'checked' ?> type="radio" onclick="setValueToInputOnHeader('application/x-www-form-urlencoded')" name="body_data_format" id="body_data_format2" value="0" />
                        <label class="form-check-label" for="body_data_format2">application/json</label>
                    </div>

                    <h6 class="mt-4">Custom Body Data</h6>
                    <div class="mb-3 text-center">
                        <div class="container-fluid">
                            <div id="cfglobal_input_container" style="max-width:100%"></div>
                        </div>
                        <button type="button" class="btn btn-primary cfglobal_createinp_btn mt-2"><i class="fa fa-plus" type="button"></i></button>
                    </div>                    
                </div>
            </div>
            
            <div class="col-md-6 col-lg-6 mt-4">
                <h5 class="p-3 shadow global-top-border rounded">Form Header Settings</h5>
                <div class="mb-4 shadow p-3 rounded global-bottom-border">
                    <div class="mb-3 text-center">
                        <div class="container-fluid">
                            <div id="cfglobal_input_header_container" style="max-width:100%"></div>
                        </div>
                        <button type="button" class="btn btn-primary cfglobal_createheader_btn mt-2"><i class="fa fa-plus" type="button"></i></button>
                    </div>
                </div>

                <div class="shadow global-top-border rounded p-3 mb-2">
                    <input class="form-check-input ms-1" type="checkbox" <?=($cfglobal_authRequired)?"checked":""?> id="authRequired" name="authRequired">
                    <h5 class="ms-4">Basic Authentication</h5>
                </div>
                <div class="mb-4 shadow p-3 rounded global-bottom-border" id="showAuthInputs" style="display: <?=($cfglobal_authRequired)?'block':'none'?>">
                    <div class="mb-3">
                        <input type="text" placeholder="Enter Username" name="cfglobal_username" id="cfglobal_username" value="<?=($cfglobal_username)?$cfglobal_username:''?>" class="form-control mb-2" <?=($cfglobal_authRequired)?"required":""?> />
                    </div>
                    <div class="mb-3">
                        <input type="text" placeholder="Enter Password" name="cfglobal_password" id="cfglobal_password" value="<?=($cfglobal_password)?$cfglobal_password:''?>" <?=($cfglobal_authRequired)?"required":""?> class="form-control mb-2" />
                    </div>
                </div>

                <div class="mb-3 text-end">
                    <button type="submit" id="global_au_settings_form_test" name="global_au_settings_form_test" class="btn btn-primary mt-5">TEST</button>
                    <button type="submit" id="global_au_settings_form_submit" name="global_au_settings_form_submit" class="btn btn-primary mt-5">Save Settings</button>
                </div>
            </div>
        </div>
    </form>
</div>



<script type="text/javascript">
    let cfglobal_inp_ob = new CFGlobalMangeInputs();
    let cfglobal_header_inp_ob = new CFGlobalMangeHeaderInputs();

    <?php
    if(isset($cfglobal_exf) || isset($autoresponder_details))
    {
        if(isset($cfglobal_exf))
        {
            foreach($cfglobal_exf as $key=>$value)
            {
                echo "
                  cfglobal_inp_ob.createINP( '".$value['name']."','".$value['title']."', '".$value['custom']."' );
                ";
            }
        }
        if(isset($autoresponder_details))
        {
            foreach($autoresponder_details as $key=>$value)
            {
                echo "
                    cfglobal_header_inp_ob.createINP( '".$key."','".$value."' );
                ";
            }
        }
    }
    else
    {
        echo "
            cfglobal_header_inp_ob.createINP( 'Content-Type','application/x-www-form-urlencoded' );
        ";
    }
    ?>
    document.querySelectorAll(".cfglobal_createinp_btn")[0].onclick = function(eve) {
        eve.preventDefault();
        cfglobal_inp_ob.createINP();
    };
    document.querySelectorAll(".cfglobal_createheader_btn")[0].onclick = function(eve) {
        eve.preventDefault();
        cfglobal_header_inp_ob.createINP();
    };
</script>