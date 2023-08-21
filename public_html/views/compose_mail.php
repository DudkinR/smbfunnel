<?php
$list_ob=$data_arr['list_ob'];
$smtp_ob=$data_arr['smtp_ob'];
$edit_compose=false;
if(isset($_GET['seqid']))
{
    $get_seq=$data_arr['compose_ob']->getCompose($_GET['seqid']);
    if($get_seq)
    {
        $edit_compose=true;
        $email_content=explode("@clickbrk@",$get_seq->sentdata);
        //echo $get_seq->stimezone;
        $history_config=json_decode($get_seq->stimezone);
        //print_r($history_config);
        $used_smtps=explode(",",trim($get_seq->smtpid,','));

        $used_lists=explode("@",trim($get_seq->listid,"@"));
    }
}
?>
<?php register_tiny_editor("#email_content_composemail"); ?>
<div id="compose_mail" class="container-fluid">
<div class="col-md-12 nopadding"><div class="card pb-2  br-rounded">
<div class="card-body pb-2">
    <div class="row">
        <div class="col-md-6">
            <div class="mb-3">
                <label>{{t("Composition Title")}}</label>
                <input type="text" class="form-control" v-bind:placeholder="t('Enter a name for identification')" v-bind:value="state.identifier" v-on:keyup="doInput($event,'identifier')">
            </div>
            <div class="mb-3">
                <label>{{t("Select SMTPs")}}</label>
                    <div id="select_smtp_qfnl" class="mt-2 p-2 clpcompose">
                        <?php
                            $smtps=$smtp_ob->getSMTP(false,1);
                            $got_smtp=false;
                        ?>
                        <?php if(get_option('default_smtp')=='php'||(  is_numeric(get_option('default_smtp')) &&  get_option('default_smtp') !='0')){ $got_smtp=true;  ?>
                        <div class='input-group mb-2'>
                                    <div class='input-group-prepend'>
                                        <span class='input-group-text'>
                                            <input type='checkbox' value='<?php echo get_option('default_smtp'); ?>'  v-model="state.selected_smtps" v-on:change='doInput($event,"selected_smtps","value")'>
                                        </span>
                                    </div>
                                    <p class="form-control">{{t("Default Mailer")}}</p>
                        </div>
                        <?php } $got_smtp=true; ?>
                        <div class='input-group mb-2'>
                                    <div class='input-group-prepend'>
                                        <span class='input-group-text'>
                                            <input type='checkbox' value='php' v-model="state.selected_smtps" v-on:change='doInput($event,"selected_smtps","value")'>
                                        </span>
                                    </div>
                                    <p class="form-control">{{t("PHP Mailer")}}</p>
                        </div>
                        <?php
                            if(is_object($smtps))
                            {
                                while($r=$smtps->fetch_object())
                                {
                                    $got_smtp=true;
                                    echo "<div class='input-group mb-2'>
                                    <div class='input-group-prepend'>
                                        <span class='input-group-text'>
                                            <input type='checkbox' value='".$r->id."' v-model='state.selected_smtps' v-on:change='doInput(\$event,\"selected_smtps\",\"value\")'>
                                        </span>
                                    </div>
                                    <p class='form-control'>".$r->title."</p>
                                    </div>";
                                }
                            }

                            if(!$got_smtp)
                            {
                                echo t("No SMTP found");
                            }
                        ?>
                    </div>
            </div>
            <div class="mb-3">
                <label>{{t("Select List(s)")}}</label>
                <div id="select_lists_qfnl" class="mt-2 p-2 clpcompose">
                    <div class="input-group mb-2">
                        <div class="input-group-prepend"><span class="input-group-text">
                            <input type="checkbox" value="all_selection" v-on:click="selectAllLists($event)">
                        </span></div>
                        <p class="form-control">{{t("Select All")}}</p>
                    </div>
                    <?php
                        $lists=$list_ob->getList(false,1);
                        if(is_object($lists))
                        {
                        while($r=$lists->fetch_object())
                        {
                            echo "<div class='input-group mb-2'>
                                <div class='input-group-append'>
                                    <div class='input-group-text'>
                                        <input type='checkbox' value='".$r->id."' v-model='state.selected_lists' v-on:change='doInput(\$event,\"selected_lists\",\"value\")'>
                                    </div>
                                </div>
                                    <p class='form-control'>".$r->title."</p>
                            </div>";
                        }
                        }
                        else
                        {
                            echo "<center><h5 style='opacity:0.5'>".t("No Lists Found")."</h5></center>";
                        }
                    ?>
                </div>
            </div>
            <div class="mb-3">
                <label>{{t("Enter Custom Emails")}} ({{t("One on each line")}})</label>
                <textarea class="form-control" v-bind:placeholder="t('Enter custom emails')" v-bind:value="state.custom_emails" v-on:keyup="doInput($event,'custom_emails')"></textarea>
            </div>
            <div class="mb-3">
                <p class="btn btn-block btn-light" data-bs-toggle="collapse" data-bs-target="#mail_rate_qfnl">{{t("Manage Mail Sending Rate")}}</p>
                <div id="mail_rate_qfnl" class="collapse mt-2 p-2 clpcompose">
                    <div class="mb-3">
                    <label>{{t("Group Size")}}</label>
                    <input type="number" class="form-control" v-bind:placeholder="t('Enter group size')" v-bind:value="state.group_size" v-on:keyup="doInput($event,'group_size')">
                    </div>
                    <div class="mb-3">
                    <label>{{t("Delay Time (sec)")}}</label>
                    <input type="number" class="form-control" v-bind:placeholder="t('Enter Delay Time')" v-bind:value="state.delay_between" v-on:keyup="doInput($event,'delay_between')">
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="mb-3">
                <label>{{t('Enter Email Subject')}}</label>
                <input type="text" class="form-control" v-bind:placeholder="t('Enter Email Subject')" v-bind:value="state.email_subject" v-on:keyup="doInput($event,'email_subject')">
            </div>
            <div class="mb-3">
                <label>{{t('Enter Email Content')}}</label>
                <textarea id="email_content_composemail"><?php if(isset($email_content)){ echo htmlentities($email_content[1]);} ?></textarea>
            </div>
            <div class="mb-3">
                <label>{{t('Enter Unsubscription message')}}</label>
                <textarea class="form-control" v-bind:value="state.email_uns" v-on:keyup="doInput($event,'email_uns')"></textarea>
            </div>
            <p class='text-center' v-if="err.trim().length>0" v-html="err"></p>
            <div class="col-sm-12" v-if="state.sent_stat.init && state.sent_stat.started">
                <div class="row">
                    <div class="col-sm-4">{{t("Total")}}:&nbsp;{{t(state.sent_stat.total)}}</div>
                    <div class="col-sm-4">{{t("Sent")}}:&nbsp;{{t(state.sent_stat.sent)}}</div>
                    <div class="col-sm-4">{{t("Pending")}}:&nbsp;{{t(state.sent_stat.total-state.sent_stat.sent)}}</div>
                </div>
            </div>
            <?php if($_SESSION['user_plan_type'.$site_token_for_dashboard]==2) { ?>
                <button class="btn btn-block theme-button" data-bs-toggle="modal" data-bs-target="#upgradeFromFreeModal" v-html="mailSendButton"></button>
            <?php } else { ?>
                <button class="btn btn-block theme-button" v-on:click="composeMail($event)" v-html="mailSendButton"></button>
            <?php } ?>
        </div>
    </div>
</div>
</div>
</div>
</div>
<?php if($edit_compose){ ?>
<script>
    document.addEventListener('DOMContentLoaded',function(){
    
        compose_mail_app.updateStorage('identifier',`<?php echo str_replace("`","\\`",$get_seq->title); ?>`);
        compose_mail_app.updateStorage('custom_emails',`<?php echo str_replace("`","\\`",$history_config->extra_mails); ?>`);
        compose_mail_app.updateStorage('group_size',<?php echo (int)$history_config->group; ?>);
        compose_mail_app.updateStorage('delay_between',<?php echo (((int)$history_config->delay)/1000); ?>);
        compose_mail_app.updateStorage('email_subject',`<?php echo str_replace("`","\\`",$email_content[0]); ?>`);
        compose_mail_app.updateStorage('email_uns',`<?php echo str_replace("`","\\`",$email_content[2]); ?>`);

        <?php
                foreach($used_smtps as $smtp)
                {
                    if($smtp=='php')
                    {
                        $smtp="'php'";
                    }
                    else
                    {
                     $smtp=(int)$smtp;   
                    }
                    echo "compose_mail_app.updateStorage('selected_smtps',".$smtp.");";
                }
                foreach($used_lists as $list)
                {
                    $list=(int)$list;
                    echo "compose_mail_app.updateStorage('selected_lists',".$list.");";
                }
        ?>

        <?php

        ?>

    });
</script>
<?php } ?>

<style>
    .clpcompose
    {
        background-color:#f8f9fa;
        border-radius:5px;
        max-height:180px;
        overflow:auto;
    }
    p[data-bs-toggle=collapse]
    {
        background-color:#d2d9df !important;
    }
</style>