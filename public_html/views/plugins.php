<?php
$ob=$data_arr['plugins_ob'];
?>
<div class="container-fluid" id="app_plugins">
    <?php
      if($_SESSION['access' . get_option('site_token')]==='admin') {
        ?>
<loading_container v-bind:loading="ajax_loading" v-bind:err="err" v-bind:msg="msg"></loading_container>
<display_update_popup v-if="show_update_detail_popup" v-bind:plugin="show_update_detail_popup"></display_update_popup>
<container_plugin_installer v-if="show_plugin_installer_popup"></container_plugin_installer>
<div class="card pb-2  br-rounded">
    <div class="card-body pb-2">
           <div class="row">
            <?php if($_SESSION['user_plan_type' . $site_token_for_dashboard]==2) { ?>
                <div class="col-sm-12 text-right">
                    <button class="btn theme-button mb-2" data-bs-toggle="modal" data-bs-target="#upgradeFromFreeModal"><i class="fas fa-plug"></i>&nbsp;{{t('Add New Plugin')}}</button>
                </div>
                <div class="col-sm-12 mt-5 mb-4" v-if="plugins.length<1">
                    <center><h4 style="opacity:0.8;">No plugins installed yet, <a data-bs-toggle="modal" data-bs-target="#upgradeFromFreeModal">visit the marketplace</a> to get one.</h4></center>
                </div>
            <?php } else { ?>
            <div class="col-sm-12 text-right">
            <button class="btn theme-button mb-2" v-on:click="function(){show_plugin_installer_popup=true;}"><i class="fas fa-plug"></i>&nbsp;{{t('Add New Plugin')}}</button>
            </div>
           <div class="col-sm-12 mt-5 mb-4" v-if="plugins.length<1">
               <center><h4 style="opacity:0.8;">No plugins installed yet, <a v-bind:href="getMarketPlaceURL()" target="_BLANK">visit the marketplace</a> to get one.</h4></center>
           </div>
           <?php } ?>
           <div class="col-sm-4" v-for="(plugin,id) in plugins">
               <div class="card cf_plugin_div_each">
                   <div class="card-body pd-0">
                   <div class="col-sm-12">
                    <div class="row">
                        <div class="col-sm-3 text-center">
                            <div class="plugin-logo text-center">
                            <i class="fas fa-image" v-if="(plugin.logo===undefined||plugin.logo.trim().length<1)"></i>
                            <img v-bind:src="plugin.logo" style="max-height:100px;max-width:100px;" class="img-fluid" v-else>
                            </div>
                        </div>
                        <div class="col-sm-9">
                            <h4 class="plugin-title">
                            <span v-if="plugin.plugin_url===undefined">{{(plugin.name!==undefined && plugin.name.trim().length>0)? plugin.name.trim():"No Name"}}</span>
                            <span v-else><a v-bind:href="plugin.plugin_url" target="_BLANK">{{(plugin.name!==undefined && plugin.name.trim().length>0)? plugin.name.trim():"No Name"}}</a></span>
                            <span class="cf-plugin-version-detail" v-html="(plugin.version !==undefined)? ` (v-${plugin.version.trim()})`:''"></span></h4>
                            <h6 v-if="plugin.author !==undefined" class="cf_plugin_author"><i>By&nbsp;<span v-if="plugin.author_url!==undefined"><a v-bind:href="plugin.author_url" target="_BLANK">{{plugin.author}}</a></span><span v-else>{{plugin.author}}</span></i></h6>
                            <h6 class="plugin-description" v-if="plugin.description !==undefined" v-html="descriptionCreate(plugin.description.trim(),plugin.temp_id)"></h6>
                        </div>
                    </div>
                   </div>

                    <div class="row mt-2">
                        <div class="col-4">
                                <div class="custom-control custom-switch ms-2">
                                    <input type="checkbox" class="custom-control-input " v-bind:id="'sw'+plugin.temp_id" v-bind:checked="(plugins[id].active)? true:false" v-on:click="doActiveorDeactive(id,$event)">
                                    <label class="custom-control-label" v-bind:for="'sw'+plugin.temp_id" v-html="(plugins[id].active)? 'Active':'Disabled'"></label>
                                </div>
                        </div>
                        <div class="col-6 cf-plugin-update-checker" v-bind:style="{'pointer-events': (plugins[id].u_stat=='checking')? 'none':'','opacity': (plugins[id].u_stat=='checking')? '0.4':'1'}">
                        <span v-if="plugin.version !==undefined">
                        <i v-bind:class="['fas', plugins[id].u_stat_class]" style="cursor:pointer;" v-on:click="doUpdateInFrontend(id)"></i>&nbsp;<span v-on:click="createUpdatePopup(id)">{{(plugins[id].u_stat=='checking')? 'Checking For Updated':((plugins[id].u_stat=='available')? `New version v-${plugins[id].u_available_version} available`:((plugins[id].u_stat=='updated')? 'Updated successfully':'Check for update'))}}</span>
                        </span>
                        </div>
                        <div class="col-2 text-right"><i class="fas fa-trash text-danger" style="cursor:pointer" v-on:click="doDelete(id)"></i></div>
                    </div>
                   </div>
               </div>              
           </div>
           <div></div>

           </div>
    </div>
</div>
<?php }
else{
    ?>
    <h1>You don`t have access to enable and disable plugins!</h1>
    
    <?php
}
?>
</div>