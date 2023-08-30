Vue.component("display_update_popup", {
  mounted: function () {
    //alert(this.plugin);
    this.createPluginChanger(this.plugin);
  },
  data: function () {
    return {
      plugin_name: "No name",
      plugin_detail: "No detail available",
      plugin_version: "0.0",
    };
  },
  props: ["plugin"],
  template: `<div class="cf_plugin_detail_view">
        <div class="card pnl">
            <div class="card-header">
            <div class="row">
            <div class="col-11">
            <h4>{{plugin_name}}<span class="cf-plugin-version-detail">(v-{{plugin_version}})</span></h4>
            </div>
            <div class="col-1"><i class="fas fa-times-circle" v-on:click="closePopup()" style="cursor:pointer;"></i></div>
            </div>
            </div>
            <div class="card-body" v-html="plugin_detail"></div>
            <div class="card-footer">
            <button class="btn btn-primary theme-button" v-on:click="downloadUpdate(plugin)"><i class="fas fa-cloud-download-alt"></i>&nbsp;Download The Update(s)</button>
            </div>
        </div>
    </div>`,
  methods: {
    downloadUpdate: function (plugin) {
      plugin_control.doUpdateInFrontend(plugin);
    },
    closePopup: function () {
      plugin_control.show_update_detail_popup = false;
    },
    createPluginChanger: function (id) {
      if (id && plugin_control.plugins[id] !== undefined) {
        let plugin = plugin_control.plugins[id];
        this.plugin_name = plugin.name;
        this.plugin_version = plugin.u_available_version;
        this.plugin_detail =
          plugin.u_version_detail !== undefined
            ? plugin.u_version_detail
            : "No detail available";
      }
    },
  },
  watch: {
    plugin: function (id) {
      this.createPluginChanger(id);
    },
  },
});
Vue.component("container_plugin_installer", {
  data: function () {
    return {};
  },
  template: `<div class="cf_plugin_detail_view">
    <div class="card pnl">
        <div class="card-header">
        <div class="row">
        <div class="col-11">
        <h4>Add New Plugin</h4>
        </div>
        <div class="col-1"><i class="fas fa-times-circle" v-on:click="closePopup()" style="cursor:pointer;"></i></div>
        </div>
        </div>
        <div class="card-body">
        <center><button class="btn button-warning theme-button" v-on:click="doAction($event,'')" style="min-width:220px;"><i class="fas fa-cloud-download-alt"></i>&nbsp;Install From Market Place</button></center>
        <center><h5 style="font-size:15px;" class="mt-2">OR</h5></center>
        <center><button class="btn button-warning theme-button" v-on:click="doAction($event,'upload')" style="min-width:220px;"><i class="fas fa-arrow-circle-up"></i>&nbsp;Upload a Plugin</button></center>
        </div>
        </div>
    </div>`,
  methods: {
    closePopup: function () {
      plugin_control.show_plugin_installer_popup = false;
    },
    doAction: function (e, doo = "upload") {
      //doo should be upload or marketplace
      //ins_remote_plugin
      if (doo == "upload") {
        plugin_control.uploadPlugin(e);
      } else {
        let current_url = window.location.href;
        try {
          let url = new URL(current_url);
          url.searchParams.delete("ins_remote_plugin");
          current_url = url.href;
        } catch (err) {
          console.log(err);
        }

        window.open(`${plugin_control.getMarketPlaceURL()}`, "_BLANK");
        plugin_control.show_plugin_installer_popup = false;
      }
    },
  },
});
Vue.component("loading_container", {
  template: `<div class="status_handler">
    <div class="plugin_loader" v-if="loading">
        <div class="load"><img src="assets/img/visual_cog.gif"><span class="load-text">Loading...</span></div>
    </div>
    <div class="msg" v-if="msg.trim().length>0||err.trim().length>0">
        <div class="alert alert-success alert-dismissible fade show success_msg" v-if="msg.trim().length>0">
        <button type="button" class="close" data-bs-dismiss="alert">&times;</button>
        {{msg}}</div>
        <div class="alert alert-danger alert-dismissible fade show err_msg" v-else>
        <button type="button" class="close" data-bs-dismiss="alert">&times;</button>
        {{err}}</div>
    </div>
    </div>`,
  mounted: function () {},
  props: ["loading", "err", "msg"],
  data: function () {
    return {
      is_loading: false,
      show_err: false,
      show_msg: false,
    };
  },
  methods: {},
  watch: {},
});
var plugin_control = new Vue({
  el: "#app_plugins",
  mounted: function () {
    this.loadPlugins();
  },
  data: {
    plugins: [],
    request: new ajaxRequest(),
    err: "",
    msg: "",
    ajax_loading: true,
    update_processing: false,
    show_update_detail_popup: false,
    show_plugin_installer_popup: false,
    cf_version: "0.0",
    marketplace_url: "https://cloudfunnels.in",
  },
  methods: {
    /*w:function(txt,arr=[]){
		w(txt,arr);
	    },*/
    t: function (txt, arr = []) {
      return t(txt, arr);
    },
    console: function (data) {
      console.log(data);
    },
    getMarketPlaceURL: function () {
      let current_url = window.location.href;
      try {
        let url = new URL(current_url);
        url.searchParams.delete("ins_remote_plugin");
        current_url = url.href;
      } catch (err) {
        console.log(err);
      }

      return `http://marketplace.cloudfunnels.in/?product=cloudfunnels&plugin_ref_installation=${btoa(
        current_url
      )}`;
    },
    loadPlugins: function () {
      this.err = "";
      this.ajax_loading = true;
      this.request.postRequestCb(
        "req.php",
        { manage_plugins: 1, load: "all" },
        (res) => {
          this.ajax_loading = false;
          try {
            res = JSON.parse(res);
            console.log(res);
            this.plugins = res;
            this.loadUpdateChecker();
          } catch (err) {
            console.log(err);
            this.err = err.message;
          }
        }
      );
    },
    loadUpdateChecker: function (plugin_id = false) {
      //update status should be
      let plugins = this.plugins;
      if (plugin_id && plugins[plugin_id] !== undefined) {
        plugins = {};
        plugins[plugin_id] = this.plugins[plugin_id];
      }
      for (let i in plugins) {
        let status = "none";
        if (plugins[i].version !== undefined) {
          status = "checking";
        }
        this.plugins[i].u_stat = status;
        this.plugins[i].u_stat_class =
          status == "checking"
            ? "fa-spinner fa-spin text-primary"
            : "fa-sync-alt text-info";

        if (plugin_id) {
          this.plugins = { ...this.plugins };
        }

        this.doManualCheckForUpdate(i, false);
      }
    },
    descriptionCreate: function (des, idd) {
      des = des.split(" ");
      idd = idd.toString();
      let reg = /(\s|")+/g;
      idd = idd.replace(reg, "_");
      if (des.length > 8) {
        let des_len = des.length;
        let show = des.slice(0, 8);
        let pending = des.slice(8, des_len);
        //console.log(des);
        //console.log(show);
        //console.log(pending);

        let r_more = `${show.join(
          " "
        )}<span style="display:none" hdndescription="${idd}"> ${pending.join(
          " "
        )}</span><span activehiddendescription="${idd}">...<span class="plugin-readmore">Read More</span></span>`;
        setTimeout(
          (idd) => {
            try {
              document.querySelectorAll(
                `span[activehiddendescription="${idd}"]`
              )[0].onclick = function (e) {
                try {
                  document.querySelectorAll(
                    `span[activehiddendescription="${idd}"]`
                  )[0].style.display = "none";
                  document.querySelectorAll(
                    `span[hdndescription="${idd}"]`
                  )[0].style.display = "block";
                } catch (err) {
                  console.log(err);
                }
              };
            } catch (err) {
              console.log(err);
            }
          },
          500,
          idd
        );

        return r_more;
      } else {
        return des.join(" ");
      }
    },
    doActiveorDeactive: function (plugin_id, e) {
      this.err = "";
      this.msg = "";

      let doo = this.plugins[plugin_id].active ? "deactivate" : "activate";
      e.target.checked = false;

      let data = { manage_plugins: 1, process_activation: doo, plugin_id };
      this.ajax_loading = true;
      this.request.postRequestCb("req.php", data, (res) => {
        this.ajax_loading = false;
        res = res.trim();
        //console.log(res);
        if (res == "1") {
          e.target.checked = true;
          this.plugins[plugin_id].active = this.plugins[plugin_id].active
            ? false
            : true;
          this.msg = this.t(`The plugin ${doo}d successfuly.`);
          setTimeout(() => {
            window.location = "";
          }, 500);
        } else {
          this.err =
            this.t("The plugin generated unwanted output") + ": " + this.t(res);
        }
      });
    },
    uploadPlugin: function (e) {
      this.err = "";
      this.msg = "";
      let frm = document.createElement("form");
      frm.enctype = "multipart/form-data";
      let fileinput = document.createElement("input");
      fileinput.accept = ".zip";
      fileinput.type = "file";
      frm.appendChild(fileinput);
      fileinput.click();
      let thisvue = this;

      fileinput.onchange = function (e) {
        let file = e.target.files[0];
        let form_data = new FormData(frm);
        form_data.append("manage_plugins", 1);
        form_data.append("upload_plugin", 1);
        form_data.append("plugin_file", file);
        thisvue.ajax_loading = true;
        //e.target.disabled=true;
        let srvr = new XMLHttpRequest();
        srvr.onreadystatechange = function () {
          if (this.readyState == 4) {
            thisvue.show_plugin_installer_popup = false;
            thisvue.ajax_loading = false;
            //e.target.disabled=false;
            if (this.status == 200) {
              let stat = this.responseText.trim();
              if (stat == "1") {
                thisvue.msg = thisvue.t("Plugin uploaded successfully");
                thisvue.loadPlugins();
              } else {
                thisvue.err = thisvue.t(stat);
              }
            } else {
              thisvue.err = thisvue.t(this.statusText);
              console.log(this.statusText);
            }
          }
        };
        srvr.open("POST", "req.php", true);
        srvr.send(form_data);
      };
    },
    doInstallRemotePlugin: function (plugin_url) {
      this.err = "";
      this.msg = "";
      this.ajax_loading = true;
      this.request.postRequestCb(
        "req.php",
        { manage_plugins: 1, upload_remote_plugin: plugin_url },
        (res) => {
          this.ajax_loading = false;
          res = res.trim();
          if (res === "1") {
            this.msg = this.t("Plugin installed successfully");
            setTimeout(function () {
              window.location = "index.php?page=plugins";
            }, 500);
            //this.loadPlugins();
          } else {
            this.err = res;
          }
        }
      );
    },
    doDelete: function (plugin_id) {
      this.msg = "";
      this.err = ""; 
      
      Swal.fire({
        title: t("Are you sure?"),
        text: t("You won't be able to revert this!"),
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#d33",
        cancelButtonColor: "#3085d6",
        confirmButtonText: t("Yes, delete it!"),
      }).then((result) => {
        if (result.isConfirmed) {		
          this.ajax_loading = true;
          this.request.postRequestCb(
            "req.php",
            { manage_plugins: 1, del_plugin: plugin_id },
            (res) => {
              this.ajax_loading = false;
              res = res.trim();
              if (res == "1") {
                this.msg = this.t("Plugin removed successfully.");
                setTimeout(() => {
                  window.location = "";
                }, 500);
              } else {
                this.err = this.t(res);
              }
              this.loadPlugins();
            }
          );
        } else {			
          return false;
        }		
      });		

    },
    doDelayToUpdate: function () {
      return new Promise((resolve, reject) => {
        if (this.update_processing) {
          var intrv = setInterval(() => {
            if (this.update_processing == false) {
              clearInterval(intrv);
              resolve(1);
            }
          }, 50);
        } else {
          resolve(1);
        }
      });
    },
    doManualCheckForUpdate: function (plugin_id, detailed = false) {
      this.request.postRequestCb(
        "req.php",
        {
          manage_plugins: 1,
          plugin_update_check: plugin_id,
          get_in_detail: detailed ? 1 : 0,
        },
        async (res) => {
          try {
            await this.doDelayToUpdate();
            this.update_processing = true;
            res = res.trim();
            let stat = JSON.parse(res);
            console.log(stat);
            console.log(this.plugins[plugin_id].u_stat);
            this.plugins[plugin_id].u_stat = "none";
            this.plugins[plugin_id].u_stat_class = "fa-sync-alt text-info";
            if (stat.update) {
              this.plugins[plugin_id].u_stat = "available";
              this.plugins[plugin_id].u_available_version = stat.version;
              if (stat.data.trim().length > 0) {
                this.plugins[plugin_id].u_version_detail = stat.data;
              }
              this.plugins[plugin_id].u_stat_class =
                "fa-cloud-download-alt text-warning";
              this.plugins[plugin_id].u_file = stat.file;
              this.plugins[plugin_id].u_required_cf_version =
                stat.required_cf_version;
            }
            this.plugins = { ...this.plugins };
            this.update_processing = false;
          } catch (err) {
            console.log(err);
            this.update_processing = false;
            this.err = err.message;
            console.log(res);
          }
        }
      );
    },
    doUpdate: async function (file, plugin_id) {
      this.msg = "";
      this.err = "";
      if (
        this.plugins[plugin_id].u_required_cf_version !== undefined &&
        this.cf_version < this.plugins[plugin_id].u_required_cf_version
      ) {
        this.err = `This update needs CloudFunnels version ${this.plugins[plugin_id].u_required_cf_version}, currently you are using ${this.cf_version}`;
        return;
      }
      this.ajax_loading = true;
      await this.doDelayToUpdate();
      this.request.postRequestCb(
        "req.php",
        { manage_plugins: 1, update_plugin: file, plugin_id },
        (res) => {
          this.show_update_detail_popup = false;
          this.ajax_loading = false;
          res = res.trim();
          if (res == "1") {
            this.plugins[plugin_id].u_stat = "updated";
            this.plugins[plugin_id].version =
              this.plugins[plugin_id].u_available_version;
            this.plugins[plugin_id].u_stat_class =
              "fa-check-circle text-success";
            this.msg = this.t("Plugin updated successfully");
            this.plugins = { ...this.plugins };
          } else {
            this.err = res;
          }
        }
      );
    },
    doUpdateInFrontend: function (plugin) {
      let plugins = this.plugins;
      console.log(plugins[plugin]);
      if (
        plugins[plugin] !== undefined &&
        plugins[plugin].u_file !== undefined &&
        plugins[plugin].u_stat == "available"
      ) {
        this.doUpdate(plugins[plugin].u_file, plugin);
      }
      if (
        plugins[plugin] !== undefined &&
        plugins[plugin].u_stat !== undefined &&
        plugins[plugin].u_stat == "none"
      ) {
        this.loadUpdateChecker(plugin);
      } else {
        return false;
      }
    },
    showUpdateDetailPopup: function (plugin) {
      this.show_update_detail_popup = plugin;
    },
    createUpdatePopup: function (plugin) {
      let plugins = this.plugins;
      if (
        plugins[plugin] !== undefined &&
        plugins[plugin].u_stat !== undefined
      ) {
        //none updated available
        let stat = plugins[plugin].u_stat;
        if (stat == "none") {
          this.loadUpdateChecker(plugin);
        } else if (stat == "available") {
          this.showUpdateDetailPopup(plugin);
        }
      }
    },
  },
  watch: {
    deep: true,
  },
});
