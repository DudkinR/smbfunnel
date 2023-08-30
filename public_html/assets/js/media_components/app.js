(function () {
    //------------------------------------------------
    const media_uploader = `<div class="row d-flex justify-content-center">
          <div class="col-sm-12 text-right">
              <button class="btn btn-primary theme-button" v-on:click="changeView('all')"><i class="fas fa-th-large"></i>&nbsp;${t(
                "View Files"
              )}</button>
          </div>
          <div class="col-sm-12 uploader">
              <center>
              <i v-bind:class="['fas', 'stat',(!uploading)? 'fa-arrow-circle-up':'fa-spinner fa-spin']" v-on:click="doUpload('init')" v-if="upload_stat===null"></i>
              <i v-bind:class="['fas','upload-stat',(upload_stat)? 'fa-check-circle text-success':'fa-times-circle text-danger']" v-else></i>
              <h3 v-bind:class="(uploadDone<30)? 'text-info': (uploadDone<70)? 'text-primary': 'text-success'" style="'opacity': 0.8" v-if="this.uploading">{{uploadDone}}%</h3>
              <input type="file" vid="cf_file_inp" v-on:change="doUpload('change', $event)" style="display:none;"/></center>
              
              <center><span class="file-name" v-text="(storage.state.uploadable_file ===null)? '${t(
                "No File Selected"
              )}':storage.state.uploadable_file.name"></span></center>
              <center v-if="!uploading && (storage.state.uploadable_file !==null)"><button class="btn btn-primary theme-button" v-on:click="doUpload('upload')"><i class="fas fa-cloud-upload-alt"></i>&nbsp;${t(
                "Upload Now"
              )}</button></center>
              <p class="text-center text-success mt-1" v-if="msg.trim().length>0">{{msg}}</p>
              <p class="text-center text-danger mt-1" v-if="err.trim().length>0">{{err}}</p>
              <p></p>
          </div>
      </div>`;
  
    let all_media = `<div class="row">
          <div class="col-sm-12"><!-- Place for controllers --></div>
          <div class="col-sm-12">
          <div class="row">
              <div v-bind:class="'col-sm-'+((current_file===null)? '12':'8')">
                  <div class="row">
                  <div class="col-sm-12 mt-5 text-center" v-if="getCurrentFilesCount()===0">
                  <h1 class="text-center" style="opacity: 0.5">${t(
                    "No file available"
                  )}, <span class="text-primary" v-on:click="changeView('uploader')" style="cursor: pointer;">${t(
      "let's upload one"
    )}</span><span v-if="getObjectKeyCount(storage.state.files.all)"> or <span class="text-primary" style="cursor:pointer;" v-on:click="doChangeCategory('all')">${t(
      "view all"
    )}</span></span></h1>
                  </div>
                  <div class="col-sm-12" v-else>
                      <div class="row">
                          <div class="col-sm-10">
                          <div class="row">
                          <div class="col-sm-4">
                              <div class="input-group">
                                  <div class="input-group-prepend"><span class="input-group-text">${t(
                                    "Select Type"
                                  )}</span></div>
                                  <select class="form-control" v-on:change="doChangeCategory($event.target.value)">
                                      <option v-for="(name, type) in file_types_menu" v-bind:value="type" v-text="name" v-bind:selected="(type== storage.state.current_type)? true:false"></option>
                                  </select>
                              </div>
                          </div>
                          <div class="col-sm-4">
                          <div class="input-group">
                              <div class="input-group-prepend"><span class="input-group-text">${t(
                                "Arrange By"
                              )}</span></div>
                              <select class="form-control" v-model="get_order" v-on:change="doOrderChange()">
                                  <option value='asc'>${t(
                                    "Date ascending"
                                  )}</option>
                                  <option value='desc'>${t(
                                    "Date descending"
                                  )}</option>
                              </select>
                          </div>
                          </div>
                          <div class="col-sm-4">
                              <div class="input-group">
                                  <input type="text" class="form-control" placeholder="${t(
                                    "Enter file name search"
                                  )}" v-model="search_content">
                                  <div class="input-group-append" style="cursor: pointer;" v-on:click="doSearch($event)">
                                  <button class="input-group-text">${t(
                                    "Search"
                                  )}</button>
                                  </div>
                              </div>
                          </div>
                          </div>
                          </div>
                          <div class="col-sm-2 text-right">
                          <button class="btn btn-primary theme-button" v-on:click="changeView('uploader')"><i class="fas fa-cloud-upload-alt"></i>&nbsp;${t(
                            "Upload New"
                          )}</button>
                          </div>
                          <div class="col-sm-12 mt-2">
                          <div class="row">
                              <div class="col-sm-10 text-right text-danger" v-text="(page_load_err.length>0)? page_load_err:''"></div>
                              <div class="col-sm-2 text-right">
                              <button class="btn text-primary unstyled-bytton" v-on:click="doLoadMore($event)"><i class="fas fa-cloud-download-alt"></i>&nbsp;${t(
                                "Load More"
                              )}</button>
                              </div>
                          </div>
                          </div>
                      </div>
                  </div>
                      <div class="media-content ml-2 row" style="width:100%;">
                      <div v-bind:class="['col-lg-'+((current_file===null)? '2': '3'), 'col-sm-4', ' m-0 p-0 each-mini-file', ((storage.state.selected_file !==null && storage.state.selected_file==file)? 'active': '')]" v-for="(data,file) in storage.state.files[storage.state.current_type]" v-on:click="doSelectFile(file)" v-if="storage.state.search_files.length<1 || storage.state.search_files.indexOf(file)>=1">
                         <div class="m-2 each-mini-file-outer">
                              <div class="each-mini-file-inner">
                                  <div class="each-mini-file-in-inner">
                                      <img v-bind:src="storage.state.media_url+'/'+file" class="img-fluid" style="border-radius: 5px;" v-if="data.type=='image'"/>
                                      <i v-bind:class="['fas', getFileFSIcon(file, data.type)]" style="text-align:center; font-size: 80px;opacity: 0.8;" v-else></i>
                                  </div>
                              </div>
                          <p class="mt-0 mw-100 o-break">{{(data.title.trim().length<=25)? data.title.trim():(data.title.trim().substr(0, 25))+'...'}}</p>
                         </div>
                      </div>
                      <div class="col-sm-12" v-if="storage.state.search_files.length===1">
                          <h1 class="text-center" style="opacity: 0.5;">${t(
                            "Nothing Found"
                          )}</h1>
                      </div>
                      </div>
                  </div>
              </div>
              <div class="col-sm-4 h-100" v-if="current_file !==null">
                  <div class="card h-100">
                      <div class="card-body cf-course-chbtn h-100" style="height: 100%;">
                          <div class="row">
                              <div class="col-sm-9">
                              <h4>${t("File Detail")}</h4>
                              </div>
                              <div class="col-sm-3">
                              <h4 class="text-right" style="cursor: pointer;" v-on:click="doCloseFileSelection()"><i class="fas fa-times"></i></h4>
                              </div>
                          </div>
                          <hr/>
                          <div class="selected_file_preview">
                          <center>
                          <img v-bind:src="storage.state.media_url+'/'+current_file.file" class="img-fluid" v-if="current_file.type=='image'"/>
                          <video class="video-js" width="320" height="240" controls v-else-if="current_file.type=='video'">
                              <source v-bind:src="storage.state.media_url+'/'+current_file.file" v-bind:type="current_file.file_type">
                          </video>
                          <audio width="320" height="240" controls v-else-if="current_file.type=='audio'">
                              <source v-bind:src="storage.state.media_url+'/'+current_file.file" v-bind:type="current_file.file_type">
                          </audio>
                          <i v-bind:class="'fas '+getFileFSIcon(current_file.file, current_file.type) " style="font-size: 80px; opacity: 0.8;" v-else></i>
                          </center>
                          </div>
                          <center><span  style="font-weight: 600;">{{current_file.title}}</span>&nbsp;&nbsp;<button class="btn btn-danger mt-1" v-on:click="doDeleteFile()"><i class="fas fa-trash "></i></button></center>
                          <div class="mt-3 mb-4">
                          <label>Size: {{getSize(current_file.size)}}</label><br>
                          <label>${t(
                            "MIME Type"
                          )}: {{current_file.file_type}}</label><br>
                          <label>${t(
                            "Added On"
                          )}: {{current_file.added_on}}</label>
                          </div>
          
                          <div class="mb-3">
  
                              <div class="input-group">
                              <div class="input-group-prepend"><span class="input-group-text">${t(
                                "Title"
                              )}</span></div>
                              <input type="text" id="cf_media_file_title" class="form-control" v-bind:value="current_file.title">
                              </div>
  
                              <div class="input-group mt-2">
                              <div class="input-group-prepend"><span class="input-group-text">${t(
                                "URL"
                              )}</span></div>
                              <input type="text" class="form-control" v-bind:value="storage.state.media_url+'/'+current_file.file" data-toggle="tooltip" title="Click to copy." v-on:click="doCopyCurrentURL()">
                              </div>                  
         
                              <div class="mt-2">
                              <label>${t("Description")}</label>
                              <textarea 
                              id="cf_media_file_desc" class="form-control" v-text="current_file.description" placeholder="${t(
                                "Enter file detail."
                              )}"></textarea>
                              </div>
                              <div class="mb-3 mt-4">
                              <div class="row">
                              <div class="col-sm-4 cf-course-chbtn"><button class="btn btn-danger mt-1" v-on:click="doDeleteFile()"><i class="fas fa-trash"></i></button></div>
                              <div class="col-sm-8">
                              <button class="btn btn-primary theme-button btn-block" v-on:click="doSaveFileData()"><i class="fas fa-save"></i>&nbsp;&nbsp;${t(
                                "Save Detail"
                              )}</button>
                              </div>
                              </div>
                              </div>
                              <p class="text-danger text-right mt-0" v-if="err.trim().length>0" v-html="err"></p>
                              <p class="text-success text-right mt-0" v-if="msg.trim().length>0" v-html="msg"></p>
                          </div>
                      </div>
                  </div>
              </div>
          </div>
          </div>
      </div>`;
  
    let container = `<div class="row main_row">
      <!-- Init -->
      <div class="col-sm-12" v-if="storage.state.view=='init'">
      <center><br><br><br><h1 style="opacity: 0.5; font-size:200px"><i class="fas fa-spinner fa-spin"></i></h1></center>
      </div>
      <!-- Show uploader -->
      <div class="col-sm-12" v-else-if="storage.state.view=='uploader'">${media_uploader}</div>
      <!-- Show grid of files -->
      <div class="col-sm-12" v-else-if="storage.state.view">${all_media}</div>
      <!-- show single file -->
      <div class="col-sm-12" v-else></div>
  </div>`;
  
    let external_container = `
      <div class="container-fluid external-media-loader overlay">
  
      <div class="row d-flex justify-content-center">
      <div class="col-sm-10">
      <div class="card pnl">
      <div class="card-header">
      <div class="row">
      <div class="col-sm-10">Media</div>
      <div class="col-sm-2 text-right"> <i class="fas fa-times-circle" style="cursor:pointer" onclick="closeMedia()"></i></div>
      </div>
      </div>
      <div class="card-body external-content-body">
      ${container}
      </div>
      <div class="card-footer text-right" v-if="current_file !==null">
          <button class="btn theme-button" v-on:click="doExport()"><i class="fas fa-file-import"></i>&nbsp;&nbsp;${t(
            "Import"
          )}</button>
      </div>
      </div>
      </div>
      </div>
  
      </div>
      `;
  
    Vue.component("media_app", {
      template: `<div>
          <div style="display:none;"><button type="button" class="plupload_browse_file">${t(
            "File Browse"
          )}</button></div>
          <div v-if="use_mode=='external'" class="external-container-media">${external_container}</div>
          <div v-else>${container}</div>
          </div>`,
      mounted: function () {
        if (this.storage.state.view == "init") {
          this.loadCurrentCategoryAssets(true);
        }
        this.doAssignPluploader();
      },
      updated: function () {
        setTimeout(() => {
          $(function () {
            //$('[data-toggle="tooltip"]').tooltip()
          });
        }, 1000);
      },
      data: function () {
        return {
          storage: media_storage,
  
          request: new ajaxRequest(),
          uploading: false,
          upload_stat: null,
          err: "",
          msg: "",
          page_load_err: "",
          search_content: "",
          searching: false,
          get_order: "desc",
          uploader: false,
          uploadDone: 0,
        };
      },
      props: ["view", "use_mode"],
      methods: {
        t: function (txt, arr = []) {
          return t(txt, arr);
        },
        doAssignPluploader: function () {
          this.uploader = new plupload.Uploader({
            browse_button: document.querySelectorAll(".plupload_browse_file")[0], // this can be an id of a DOM element or the DOM element itself
            url: "req.php",
            multipart_params: {
              manage_media: 1,
              upload: 1,
            },
          });
          this.uploader.init();
  
          this.uploader.setOption("chunk_size", "500mb");
  
          this.uploader.bind("FilesAdded", (up, files) => {
            if (this.uploader.files.length > 1) {
              this.uploader.removeFile(this.uploader.files[0]);
              this.uploader.refresh(); // must refresh for flash runtime
            }
            this.doUpload("change", { target: { files } });
          });
  
          this.uploader.bind("UploadProgress", (up, file) => {
            this.uploadDone = file.percent;
            this.err = "";
            if (parseInt(this.uploadDone) === 100) {
              this.processPostUpload("1");
            }
          });
  
          this.uploader.bind("Error", (up, err) => {
            this.err = "Error #" + err.code + ": " + err.message;
            if (this.uploading) {
              let err = this.err;
              this.err = "";
              this.processPostUpload(this.err);
            }
          });
        },
        processPostUpload: function (res) {
          res = res.trim();
          this.uploading = false;
          if (res == "1") {
            this.msg = t("File uploaded successfully.");
            this.upload_stat = true;
            setTimeout(() => {
              this.storage.commit("update", {
                current_type: "all",
              });
              this.changeView("all", true);
            }, 1000);
          } else {
            this.err = res;
            this.upload_stat = false;
            console.log(res);
          }
          setTimeout(() => {
            this.upload_stat = null;
          }, 1000);
        },
        doUpload: function (step = "init", e = null) {
          this.err = "";
          this.msg = "";
          this.upload_stat = null;
          this.uploadDone = 0;
  
          if (step == "init") {
            document.querySelectorAll(".plupload_browse_file")[0].click();
            //document.querySelectorAll(`input[vid='cf_file_inp']`)[0].click();
          } else if (step == "change") {
            let file = e.target.files[0];
            this.storage.commit("update", { uploadable_file: file });
          } else if (step == "upload") {
            this.uploading = true;
            this.uploader.start();
          }
        },
        getObjectKeyCount: function (ob) {
          try {
            return Object.keys(ob).length;
          } catch (err) {
            console.log(err);
            return 0;
          }
        },
        getCurrentFilesCount: function () {
          try {
            return Object.keys(
              this.storage.state.files[this.storage.state.current_type]
            ).length;
          } catch (err) {
            return 0;
          }
        },
        changeView: function (view, re_render = false) {
          this.err = "";
          this.msg = "";
          this.storage.commit("update", {
            view,
            selected_file: null,
            uploadable_file: null,
          });
          if (re_render) {
            if (view !== "uploader" || view !== "init") {
              //alert("loading");
              this.loadCurrentCategoryAssets();
            }
          }
          this.doResetSearch();
        },
        doChangeCategory: function (categ) {
          this.storage.commit("update", {
            current_type: categ,
          });
          try {
            if (Object.keys(this.storage.state.files[categ]).length < 1) {
              this.loadCurrentCategoryAssets();
            }
            this.doResetSearch();
          } catch (err) {
            console.log(err);
          }
        },
        loadCurrentCategoryAssets: async function (
          init = false,
          page_only = false
        ) {
          return new Promise((resolve, reject) => {
            let current_view = this.storage.state.view;
            let type = this.storage.state.current_type;
            let page = page_only ? this.storage.state.selected_page : 1;
            //alert(page);
  
            let req_data = {
              manage_media: 1,
            };
            if (init) {
              req_data.init = 1;
            } else {
              req_data.page = page;
              req_data.get_assets = type;
              if (this.searching) {
                req_data.do_search = this.search_content.trim();
              }
            }
            req_data.select_order = this.get_order;
            this.storage.commit("update", { view: "init" });
            this.request.postRequestCb("req.php", req_data, (res) => {
              res = res.trim();
              try {
                res = JSON.parse(res);
                // console.log(res);
                let files = this.storage.state.files;
                if (init) {
                  res.types.forEach((type) => {
                    files[type] = type == "all" ? res.files : {};
                  });
                } else {
                  files["all"] = { ...res, ...files["all"] };
                  if (type != "all") {
                    files[type] = { ...res, ...files[type] };
                  }
                  if (this.searching) {
                    let arr = [""];
                    for (let i in res) {
                      arr.push(i);
                    }
                    this.storage.commit("update", { search_files: arr });
                  }
                }
                let data_to_update = {
                  view: page_only ? current_view : "all",
                };
                if (init) {
                  data_to_update.types = res.types;
                  data_to_update.files = files;
                  data_to_update.media_url = res.media_url;
                  data_to_update.max_files = parseInt(res.max_files_per_page);
                  /*data_to_update.current_type='all';*/
                }
                this.storage.commit("update", data_to_update);
              } catch (err) {
                console.log(err);
              }
              resolve(1);
            });
          });
        },
        getFileFSIcon(file, type) {
          let arr = file.trim().split(".");
          let ext = arr[arr.length - 1].toLowerCase();
  
          let exts = {
            csv: "fa-file-csv",
            doc: "fa-file-word",
            docx: "fa-file-word",
            pdf: "fa-file-pdf",
            ppt: "fa-file-powerpoint",
            pptx: "fa-file-powerpoint",
            html: "fa-code",
            txt: "fa-file-contract",
            zip: "fa-file-archive",
            tar: "fa-file-archive",
            rar: "file-archive",
          };
          let types = {
            video: "fa-video",
            audio: "fa-headphones",
          };
          if (exts[ext] !== undefined) {
            return exts[ext];
          } else if (types[type] !== undefined) {
            return types[type];
          }
          return "fa-file-alt";
        },
        doCopyCurrentURL: function () {
          copyText(this.storage.state.media_url+'/'+current_file.file);
        },      
        doSelectFile: function (file) {
          this.doCloseFileSelection();
          setTimeout(() => {
            this.storage.commit("update", {
              selected_file: file,
            });
          }, 400);
        },
        doCloseFileSelection: function () {
          this.err = "";
          this.msg = "";
          this.storage.commit("update", {
            selected_file: null,
          });
        },
        doDeleteFile: function () {
          let _this = this;
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
              let types = _this.storage.state.types;
              let file = _this.current_file.file;
              let files = _this.storage.state.files;
              types.forEach((type) => {
                if (files[type][file] !== undefined) delete files[type][file];
              });
              _this.storage.commit("update", { selected_file: null, files });
              _this.request.postRequestCb(
                "req.php",
                { manage_media: 1, del_asset: file },
                (res) => {
                  res = res.trim();
                }
              );
            } else {
              return false;
            }
          });
        },
        getSize(bytes) {
          var sizes = ["Bytes", "KB", "MB", "GB", "TB"];
          if (bytes == 0) return "0 Byte";
          var i = parseInt(Math.floor(Math.log(bytes) / Math.log(1024)));
          return Math.round(bytes / Math.pow(1024, i), 2) + " " + sizes[i];
        },
        doExport: function () {
          let is_html =
            cf_media_export_html !== undefined && cf_media_export_html
              ? true
              : false;
          let current_file = this.current_file;
  
          if (this.current_file !== null) {
            if (
              global_cf_media_export_callback !== undefined &&
              global_cf_media_export_callback
            ) {
              let file_url=this.storage.state.media_url+'/'+current_file.file;
              let content = file_url;
              if (is_html) {
                let type = current_file.type;
                if (type == "image") {
                  content = `<img src="${file_url}" alt="${current_file.title}"/>`;
                } else if (type == "audio") {
                  content = `<audio controls>
                                  <source src="${file_url}" type="audio/ogg">
                                  Your browser does not support the audio element.
                                  </audio>`;
                } else if (type == "video") {
                  content = `<video class="video-js" controls>
                                  <source src="${file_url}" type="video/mp4">
                                Your browser does not support the video tag.
                                </video>`;
                } else {
                  content = `<a href="${file_url}">${current_file.title}</a>`;
                }
              }
              global_cf_media_export_callback(content);
              closeMedia();
            }
          } else {
            return;
          }
        },
        doLoadMore: async function (e) {
          e.target.disabled = true;
          this.page_load_err = "";
  
          let next = this.storage.state.selected_page + 1;
          this.storage.commit("update", {
            selected_page: next,
          });
  
          let files_count = this.getObjectKeyCount(this.storage.state.files.all);
          await this.loadCurrentCategoryAssets(false, true);
          e.target.disabled = false;
          let current_files_count = this.getObjectKeyCount(
            this.storage.state.files.all
          );
          if (current_files_count == files_count) {
            this.page_load_err = t(`No more files available for this category`);
            if (next > 1) {
              this.storage.commit("update", {
                selected_page: next - 1,
              });
            }
            setTimeout(() => {
              this.page_load_err = "";
            }, 2000);
          }
          e.target.disabled = false;
        },
        doSearch: async function (e) {
          let txt = this.search_content.trim();
          if (txt.length < 1) {
            return;
          }
          e.target.disabled = true;
          this.storage.commit("update", { search_files: [] });
          this.searching = true;
          await this.loadCurrentCategoryAssets();
          this.searching = false;
        },
        doResetSearch: function (e = false) {
          this.searching = false;
          if (e !== false) {
            if (e.target.value.trim().length > 1) {
              return;
            }
          }
          this.search_content = "";
          this.storage.commit("update", { search_files: [] });
        },
        doOrderChange: function () {
          this.err = "";
          this.msg = "";
          this.loadCurrentCategoryAssets();
        },
        doSaveFileData(e) {
          this.err = "";
          this.msg = "";
          let title = document
            .querySelectorAll("#cf_media_file_title")[0]
            .value.trim();
       
          let description = document
            .querySelectorAll("#cf_media_file_desc")[0]
            .value.trim();
          if (title.length < 1) {
            this.err = "Please provide file title";
            return;
          }
          let selected_file = this.storage.state.selected_file;
          this.request.postRequestCb(
            "req.php",
            {
              manage_media: 1,
              save_file_data: 1,
              file: selected_file,
              title,            
              description,
            },
            (res) => {
              res = res.trim();
              try {
                if (res == "1") {
                  let files = this.storage.state.files;
                  let types = this.storage.state.types;
                  types.forEach((type) => {
                    if (files[type] !== undefined && files[type][selected_file]) {
                      files[type][selected_file].title = title;
                      files[type][selected_file].description = description;                    
                    }
                  });
  
                  this.msg = "Saved successfully";
                } else {
                  this.err = "Unable to save";
                  console.log(res);
                }
              } catch (err) {
                console.log(err);
              }
            }
          );
        },
      },
      computed: {
        current_file: function () {
          try {
            let file =
              this.storage.state.files[this.storage.state.current_type][
                this.storage.state.selected_file
              ];
            if (file !== undefined) {
              file.file = this.storage.state.selected_file;
              return file;
            } else {
              return null;
            }
          } catch (err) {
            console.log(err);
            return null;
          }
        },
        file_types_menu: function () {
          return {
            all: "All",
            image: "Images",
            audio: "Audios",
            video: "Videos",
            document: "Documents",
            other: "Others",
          };
        },
        show_load_more: function () {},
      },
      watch: {
        search_content: function (data) {
          data = data.trim();
          if (data.length < 1) {
            this.doResetSearch();
          }
        },
      },
    });
    //----------------------------------------
  })();
  