<?php

$id=0;
if( isset( $_GET['cfstripe_id'] ) )
{
    $id=$_GET['cfstripe_id'];
    $data= $this->loadSetting($id);
    if( $data )
    {
        $setting=$data->setting;
        $return_url = $data->return_url;  
        $email_days = $data->email_days;
        if( !empty($data->email_subject) )
        {
            $email_subject = $data->email_subject;
        }else{
            $email_subject = "Your membership of the {product_name} is going to be expired in {expire_date} day(s)";
        }
        if( !empty($data->email_content) )
        {
            $email_body = $data->email_content;
        }else{
            $email_body = '<p>Hi {name},</p><p>Your membership of the {product_name} is going to be expired in {expire_date} day(s).</p><p>Renew your <a title="Billing URL" href="{billing_url}" target="_blank" rel="noopener">billing URL</a> to enjoy uninterrupted service before it expires.</p><p>Please <a title="click here" href="{billing_url}" target="_blank" rel="noopener">click here</a>&nbsp; for the renewal</p><p>Cheers!</p>';
        }
    }else{
        $setting=false;
        $email_days=10;
        $return_url="";
        $email_subject = "Your membership of the {product_name} is going to be expired in {expire_date} day(s)";
        $email_body = '<p>Hi {name},</p><p>Your membership of the {product_name} is going to be expired in {expire_date} day(s).</p><p>Renew your <a title="Billing URL" href="{billing_url}" target="_blank" rel="noopener">billing URL</a> to enjoy uninterrupted service before it expires.</p><p>Please <a title="click here" href="{billing_url}" target="_blank" rel="noopener">click here</a>&nbsp; for the renewal</p><p>Cheers!</p>';
    }
    ?>
    <div class="container-fluid" >   
    <div class="row page-titles mb-4">
        <div class="col-md-5 align-self-center">
            <h4 class="text-themecolor" id="commoncontainerid">Stripe Billing Button Setting</h4>
        </div>
        <div class="col-md-7 align-self-center text-end">
            <div class="d-flex justify-content-end align-items-center">Settings</div>
        </div>
    </div>
    <div id="cfstripe-setting-from"  class="px-4">
        <h4 class="text-dark">Copy Cronjob Command</h4>
        <div class="mb-3">
            <?php
            
                $cron_c = 'wget -O - "'.get_option("install_url").'/index.php?page=callback_api&action=cf_stripe_load_reminder" >/dev/null 2>&1';
                // echo $cron_c;
            ?>
            <input type="text" value='<?= $cron_c ?>' class="form-control" placeholder="Cronjob Command" />
        </div>
        <hr />
        <br />
        <ul class="nav nav-tabs" id="myTab" role="tablist">
            <li class="nav-item waves-effect bg-primary waves-light">
                <a class="nav-link active bg-primary text-white" id="home-tab" data-bs-toggle="tab" href="#cfstripe_demo1" role="tab" aria-controls="home" aria-selected="false">Button Setting</a>
            </li>
            <li class="nav-item  bg-primary waves-effect waves-light">
                <a class="nav-link bg-primary text-white" id="profile-tab" data-bs-toggle="tab" href="#cfstripe_demo2" role="tab" aria-controls="profile" aria-selected="false">Email Content Setting</a>
            </li>
        </ul>
        <hr class="bg-primary">
        <form action="" class="cfstripe_setting-form">
            <div class="tab-content" id="myTabContent">
                <div class="tab-pane fade active show" id="cfstripe_demo1" role="tabpanel" aria-labelledby="home-tab">    
                    <div class="d-flex">
                        <h5 class="text-dark">Manage Billing Button   </h5> &nbsp;&nbsp;&nbsp;&nbsp;
                        <div class="linkCopy">
                            <strong  onclick="copyText(`[cfstripe_btn id=<?php echo $id; ?>]`)" data-bs-toggle="tooltip" title="Copy to clipboard" style="cursor:pointer;">
                                [cfstripe_btn id=<?php echo $id; ?>]
                            </strong>
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <input type="hidden" id="cfstripe_stripe_id" value="<?=$id?>">
                        <div class="col-md-6" style="max-height: 550px;overflow:auto">

                            <h3>Card</h3>
                            <div class="mb-3 row">
                                <label class="col-form-label col-sm-5 align-self-center">Card Width</label>
                                <div class="text-end col">
                                    <select  v-model="block.card.width" class="form-control">
                                        <option value="col-md-2">col-md-2</option>
                                        <option value="col-md-3">col-md-3</option>
                                        <option value="col-md-4">col-md-4</option>
                                        <option value="col-md-6">col-md-6</option>
                                    </select>
                                </div>
                            </div>
                            <div class="mb-3 row">
                                <label class="col-form-label col-sm-5 align-self-center">Card Background Color</label>
                                <div class="col text-end">
                                    <input type="color"  v-model="block.card.backgroundColor" class="form-control form-control-sm" >
                                </div>
                            </div>
                            <hr />
                            <h3>Card Header</h3>
                            <div class="mb-3 row">
                                <label class="col-form-label col-sm-5 align-self-center">Header Background Color</label>
                                <div class="col text-end">
                                    <input v-model="block.cardHeader.backgroundColor" type="color"  class=" form-control form-control-sm">
                                </div>
                            </div>
                            <div class="mb-3 row">
                                <label class="col-form-label col-sm-5 align-self-center">Header Color</label>
                                <div class="col text-end">
                                    <input v-model="block.cardHeading.color" type="color"  class=" form-control form-control-sm" placeholder="Enter Header Color">
                                </div>
                            </div>
                            <div class="mb-3 row">
                                <label class="col-form-label col-sm-5 align-self-center">Header Font Size</label>
                                <div class="input-group col">
                                    <input type="number" v-model="block.cardHeading.fontSize" class="form-control" placeholder="Enter Font Size" >
                                    <div class="input-group-append">
                                        <span class="input-group-text" >px</span>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3 row">
                                <label class="col-form-label col-sm-5 align-self-center">Header Font Weight</label>
                                <div class="text-end col">
                                    <select v-model="block.cardHeading.fontWeight" class="form-control">
                                        <option v-for="item in fontWeight" v-bind:value="item" v-html="item"></option>
                                    </select>
                                </div>
                            </div>
                            <div class="mb-3 row">
                                <label class="col-form-label col-sm-5 align-self-center">Header Text Alignment</label>
                                <div class="col text-end">
                                    <select v-model="block.cardHeader.textAlign" class="form-control" >
                                        <option value="center">cetner</option>
                                        <option value="left">left</option>
                                        <option value="right">right</option>
                                    </select>
                                </div>
                            </div>
                            <div class="mb-3 row">
                                <label class="col-form-label col-sm-5 align-self-center">Header Padding</label>
                                <div class="col">
                                    <div class="row">
                                        <div class="col-md-6 mb-2">
                                            <label for="">Top</label>
                                            <div class="input-group ">
                                                <input type="number"  v-model="block.headerPadding.top" class="form-control" placeholder="Top" >
                                                <div class="input-group-append">
                                                    <span class="input-group-text" >px</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-2" >
                                            <label for="">Left</label>
                                            <div class="input-group ">
                                                <input type="number"  v-model="block.headerPadding.left" class="form-control" placeholder="Left" >
                                                <div class="input-group-append">
                                                    <span class="input-group-text">px</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-2">
                                            <label for="">Right</label>
                                            <div class="input-group ">
                                                <input type="number"  v-model="block.headerPadding.right" class="form-control" placeholder="Right" >
                                                <div class="input-group-append">
                                                    <span class="input-group-text">px</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-2">
                                            <label for="">Bottom</label>
                                            <div class="input-group ">
                                                <input type="number"  v-model="block.headerPadding.bottom" class="form-control" placeholder="Bottom" >
                                                <div class="input-group-append">
                                                    <span class="input-group-text">px</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <hr />

                            <h3>Status</h3>
                            <div class="mb-3 row">
                                <label class="col-form-label col-sm-5 align-self-center">Text</label>
                                <div class="col text-end">
                                    <input v-model="block.pStatus.text" placeholder="Enter Text" type="text"  class=" form-control form-control-sm">
                                </div>
                            </div>
                            <div class="mb-3 row">
                                <label class="col-form-label col-sm-5 align-self-center">Text Color</label>
                                <div class="col text-end">
                                    <input v-model="block.pStatus.color" type="color" placeholder="Enter Text Color"  class=" form-control form-control-sm">
                                </div>
                            </div>
                            <div class="mb-3 row">
                                <label class="col-form-label col-sm-5 align-self-center"> Status Color</label>
                                <div class="col text-end">
                                    <input v-model="block.pStatus.pStatuscolor" type="color"  class=" form-control form-control-sm" placeholder="Enter Status Color">
                                </div>
                            </div>
                            <div class="mb-3 row">
                                <label class="col-form-label col-sm-5 align-self-center">Font Size</label>
                                <div class="input-group col">
                                    <input type="number" v-model="block.pStatus.fontSize" class="form-control" placeholder="Enter Font Size" >
                                    <div class="input-group-append">
                                        <span class="input-group-text" >px</span>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3 row">
                                <label class="col-form-label col-sm-5 align-self-center">Font Weight</label>
                                <div class="text-end col">
                                    <select v-model="block.pStatus.fontWeight" class="form-control">
                                        <option v-for="item in fontWeight" v-bind:value="item" v-html="item"></option>
                                    </select>
                                </div>
                            </div>
                            <hr />
                            <h3>Expire</h3>
                            <div class="mb-3 row">
                                <label class="col-form-label col-sm-5 align-self-center">Text</label>
                                <div class="col text-end">
                                    <input v-model="block.pExpire.text" placeholder="Enter Text" type="text"  class=" form-control form-control-sm">
                                </div>
                            </div>
                            <div class="mb-3 row">
                                <label class="col-form-label col-sm-5 align-self-center">Text Color</label>
                                <div class="col text-end">
                                    <input v-model="block.pExpire.color" type="color"  class=" form-control form-control-sm">
                                </div>
                            </div>
                            <div class="mb-3 row">
                                <label class="col-form-label col-sm-5 align-self-center">Day Text color</label>
                                <div class="col text-end">
                                    <input v-model="block.pExpire.pExpirecolor" type="color"  class=" form-control form-control-sm" placeholder="Enter Day Text Color">
                                </div>
                            </div>
                            <div class="mb-3 row">
                                <label class="col-form-label col-sm-5 align-self-center">Font Size</label>
                                <div class="input-group col">
                                    <input type="number" v-model="block.pExpire.fontSize" class="form-control" placeholder="Enter Font Size" >
                                    <div class="input-group-append">
                                        <span class="input-group-text" >px</span>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3 row">
                                <label class="col-form-label col-sm-5 align-self-center">Font Weight</label>
                                <div class="text-end col">
                                    <select v-model="block.pExpire.fontWeight" class="form-control">
                                        <option v-for="item in fontWeight" v-bind:value="item" v-html="item"></option>
                                    </select>
                                </div>
                            </div>
                            <hr />
                            <h3>Button</h3>
                            <div class="mb-3 row">
                                <label class="col-form-label col-sm-5 align-self-center">Button Text</label>
                                <div class="col text-end">
                                    <input v-model="form_data.bText" class="form-control form-control-sm" >
                                </div>
                            </div>
                            <div class="mb-3 row">
                                <label class="col-form-label col-sm-5 align-self-center">Button Background Color</label>
                                <div class="col text-end">
                                    <input v-model="form_data.bBColor" type="color"  class=" form-control form-control-sm">
                                </div>
                            </div>
                            <div class="mb-3 row">
                                <label class="col-form-label col-sm-5 align-self-center">Button Color</label>
                                <div class="col text-end">
                                    <input v-model="form_data.bColor" type="color"  class=" form-control form-control-sm">
                                </div>
                            </div>
                            <div class="mb-3 row">
                                <label class="col-form-label col-sm-5 align-self-center">Button Border Color</label>
                                <div class="col text-end">
                                    <input v-model="form_data.bBorderColor" type="color"  class=" form-control form-control-sm">
                                </div>
                            </div>
                            <div class="mb-3 row">
                                <label class="col-form-label col-sm-5 align-self-center">Font Size</label>
                                <div class="input-group col">
                                    <input type="number" v-model="form_data.bSize" class="form-control" placeholder="Font Size">
                                    <div class="input-group-append">
                                        <span class="input-group-text" >px</span>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3 row">
                                <label class="col-form-label col-sm-5 align-self-center">Border Width</label>
                                <div class="input-group col">
                                    <input type="number" v-model="form_data.bBorderWidth" class="form-control" placeholder="Border Width" >
                                    <div class="input-group-append">
                                        <span class="input-group-text" >px</span>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3 row">
                                <label class="col-form-label col-sm-5 align-self-center">Border Style</label>
                                <div class="col text-end">
                                    <select v-model="form_data.bBorderStyle" class="form-control" >
                                        <option value="dotted ">dotted </option>
                                        <option value="dashed">dashed</option>
                                        <option value="solid">solid</option>
                                        <option value="double">double</option>
                                        <option value="groove">groove</option>
                                        <option value="ridge">ridge</option>
                                        <option value="none">none</option>
                                    </select>
                                </div>
                            </div>
                            <div class="mb-3 row">
                                <label class="col-form-label col-sm-5 align-self-center">Font Size</label>
                                <div class="col text-end">
                                    <select v-model="form_data.bFWeight" class="form-control" >
                                        <option value="400">400</option>
                                        <option value="500">500</option>
                                        <option value="600">600</option>
                                        <option value="700">700</option>
                                        <option value="800">800</option>
                                        <option value="900">900</option>
                                        <option value="1000">1000</option>
                                    </select>
                                </div>
                            </div>
                            <div class="mb-3 row">
                                <label class="col-form-label col-sm-5 align-self-center">Button Padding</label>
                                <div class="col">
                                    <div class="row">
                                        <div class="col-md-6 mb-2">
                                            <label for="">Top</label>
                                            <div class="input-group ">
                                                <input type="number" v-model="form_data.bPadding.pTop" class="form-control" placeholder="Top" >
                                                <div class="input-group-append">
                                                    <span class="input-group-text">px</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-2" >
                                            <label for="">Left</label>
                                            <div class="input-group ">
                                                <input type="number" v-model="form_data.bPadding.pLeft" class="form-control" placeholder="Left" >
                                                <div class="input-group-append">
                                                    <span class="input-group-text">px</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-2">
                                            <label for="">Right</label>
                                            <div class="input-group ">
                                                <input type="number" v-model="form_data.bPadding.pRight" class="form-control" placeholder="Right" >
                                                <div class="input-group-append">
                                                    <span class="input-group-text" >px</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-2">
                                            <label for="">Bottom</label>
                                            <div class="input-group ">
                                                <input type="number"  v-model="form_data.bPadding.pBottom" class="form-control" placeholder="Bottom" >
                                                <div class="input-group-append">
                                                    <span class="input-group-text" >px</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3 row">
                                <label class="col-form-label col-sm-5 align-self-center">Button Marging</label>
                                <div class="col">
                                    <div class="row">
                                        <div class="col-md-6 mb-2">
                                            <label for="">Top</label>
                                            <div class="input-group ">
                                                <input type="number" v-model="form_data.bMargin.mTop" class="form-control" placeholder="Top" >
                                                <div class="input-group-append">
                                                    <span class="input-group-text" >px</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-2" >
                                            <label for="">Left</label>
                                            <div class="input-group ">
                                                <input type="number" v-model="form_data.bMargin.mLeft" class="form-control" placeholder="Left" >
                                                <div class="input-group-append">
                                                    <span class="input-group-text">px</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-2">
                                            <label for="">Right</label>
                                            <div class="input-group ">
                                                <input type="number" v-model="form_data.bMargin.mRight" class="form-control" placeholder="Right" >
                                                <div class="input-group-append">
                                                    <span class="input-group-text">px</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-2">
                                            <label for="">Bottom</label>
                                            <div class="input-group ">
                                                <input type="number" v-model="form_data.bMargin.mBottom" class="form-control" placeholder="Bottom" >
                                                <div class="input-group-append">
                                                    <span class="input-group-text">px</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="position-relative h-100 sf-manage-biling-btn">
                                <div class="row d-flex justify-content-center">
                                    <div class="col-md-8">
                                        <br><br>
                                        <div class="card"  v-bind:style="{ maxWitdth: block.card.maxWidth, backgroundColor: block.card.backgroundColor}">
                                            <div class="card-header"  v-bind:style="{ textAlign: block.cardHeader.textAlign, backgroundColor: block.cardHeader.backgroundColor, padding: block.cardHeader.padding}">
                                                <div class=" py-1" v-bind:style="{ color: block.cardHeading.color, fontSize: block.cardHeading.fontSize+'px',fontWeight: block.cardHeading.fontWeight}">Product Three</div>
                                            </div>
                                            <div class="card-body" >
                                                <div class="py-1"   v-bind:style="{ color: block.pStatus.color, fontSize: block.pStatus.fontSize+'px',fontWeight: block.pStatus.fontWeight}">{{block.pStatus.text}}: <span  v-bind:style="{ color: block.pStatus.pStatuscolor}" class="text-success"> <i class="fas fa-check-circle "></i> Active</span> </div>
                                                <div class="py-1"   v-bind:style="{ color: block.pExpire.color, fontSize: block.pExpire.fontSize+'px',fontWeight: block.pExpire.fontWeight}">{{block.pExpire.text}}: <span  v-bind:style="{ color: block.pExpire.pExpirecolor}"> 30</span> day(s)  </div>
                                                
                                                <div class="pt-3">
                                                    <input type="hidden" name="cfstripe_get_session" value="{sales_id}">
                                                    <button type="submit" class="btn btn-primary" v-bind:style="dostyleobj">{{form_data.bText}} &nbsp;<i class="fas fa-file-invoice-dollar"></i></button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="tab-pane fade" id="cfstripe_demo2" role="tabpanel" aria-labelledby="profile-tab">
                    <h5 class="text-dark">Email Content</h5> 
                    <br>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="">Enter Return URL</label>
                                <input type="url" required v-model="return_url" class="form-control" placeholder="Enter Retrun URL">
                            </div>
                            <div class="mb-3 row">
                                <div class="col-md-4">
                                    <label for="">Send Email Before</label>
                                    <div class="input-group">
                                        <input type="number" required v-model="email_days" placeholder="Enter Day"  class="form-control">
                                        <div class="input-group-append">
                                            <span class="input-group-text">day(s)</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="">Email Subject</label>
                                <input type="text" v-model="fileSubejct" class="form-control" placeholder="Enter Subject">
                            </div>
                            <div class="mb-3">
                                <label for="">Email Body</label>
                                <textarea v-model="fileContent" id="cfstripe_gmail_content" ></textarea>
                            </div>
                            <div class="text-start mt-1">
                                <span href="javascript:void(0)" class="btn btn-info btn-sm" data-bs-toggle="collapse" data-target="#cfpro-rev-demo5">Shortcodes</span>
                                <div id="cfpro-rev-demo5" class="collapse" >
                                    <p class="" style="font-size: 13px !important; opacity: 0.8;">
                                        <span class="text-info cfdisp_cursor" onclick="copyText(`{product_name}`)" data-bs-toggle="tooltip" title="Copy to clipboard" >{product_name}</span>: Course Name<br>
                                        <span class="text-info cfdisp_cursor" onclick="copyText(`{expire_date}`)" data-bs-toggle="tooltip" title="Copy to clipboard" >{expire_date}</span>: Expire Date<br>
                                        <span class="text-info cfdisp_cursor" onclick="copyText(`{name}`)" data-bs-toggle="tooltip" title="Copy to clipboard" >{name}</span>:  Membserhip Name <br>
                                        <span class="text-info cfdisp_cursor" onclick="copyText(`{email}`)" data-bs-toggle="tooltip" title="Copy to clipboard" >{email}</span>:  Membserhip Email <br>
                                        <span class="text-info cfdisp_cursor" onclick="copyText(`{billing_url}`)" data-bs-toggle="tooltip" title="Copy to clipboard" >{billing_url}</span>: Subscription URL
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>   
                </div>
            </div>
            <div class="text-center mt-2">
            <button class="btn btn-primary" type="button" ref="stripe_setting_btn" v-on:click="saveSetup()">Save</button>
            </div>
        </form>
        <br><br><br>
    </div>
</div>
<br><br><br><br>
<div id="cfstripe-snackbar-admin">Setting added successfully</div>
<input type="hidden" id="cfstripe_ajaxUrl" value="<?=get_option('install_url')?>/index.php?page=ajax">
<textarea  id="cfstripe_setting_data"  style="display: none;" ><?php echo $setting;?></textarea>
<textarea  id="cfstripe_email_subject"  style="display: none;" ><?=json_encode($email_subject); ?></textarea>
<textarea  id="cfstripe_email_content"  style="display: none;" ><?=json_encode($email_body); ?></textarea>
<textarea  id="cfstripe_return_url"  style="display: none;" ><?=json_encode($return_url); ?></textarea>
<textarea  id="cfstripe_return_email_day"  style="display: none;" ><?=$email_days ?></textarea>
<?php register_tiny_editor(array("#cfstripe_gmail_content")) ?>
    
    <?php
}else{

}
?>
