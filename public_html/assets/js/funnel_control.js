var request = new ajaxRequest();
var visualloader = new visualLoader();
var initialtemplate = "Test This template";
var tmpltcrdstyle = { height: "200px", width: "100%" };
Vue.component('is_ajax_loading', {
	template: `<div class="overlay"></div>`,
});
//Page control context
Vue.component('page_context', {
	template: `
		<div class="fnl_page_context" v-bind:style="{display: (show)? 'block':'none'}">
			<div v-on:click="visit()"><i class="fas fa-globe"></i>&nbsp;${t('Visit Page')}</div>
			<hr/>
			<div v-on:click="changeLevel(true)"><i class="fas fa-arrow-circle-up"></i>&nbsp;${t('Move page one level up')}</div>
			<hr/>
			<div v-on:click="changeLevel(false)"><i class="fas fa-arrow-circle-down"></i>&nbsp;${t('Move page one level down')}</div>
			<hr/>
			<div v-on:click="del()"><i class="fas fa-trash"></i>&nbsp;Delete</div>
			<div v-on:click="copyPage()"><i class="fas fa-copy"></i>&nbsp;${t('Duplicate Page')}</div>
		</div>
	`,
	props: ['show'],
	methods: {
		del: function () {
			funnel.deleteCurrent();
		},
		changeLevel: function (up = false) {
			funnel.changeLevel(up);
		},
		visit: function () {
			funnel.show_context = false;
			let url = `${funnel.funnel_url}/${funnel.page_foler_name}`;
			window.open(url, '_blank');
		},
		copyPage: function () {
			funnel.copyCurrent();
		},
	}
});
//Funnels rename
Vue.component('funnel_rename', {
	template: `
	<div class="col-sm-4">
			<div class="card clone-funnel-template-card shadow">
				<div class="card-header card-header-copy text-center">
				<div class="row"><div class="col">{{t('Rename Funnel')}}</div><div class="col text-right"> <i class="fas fa-times-circle" style="cursor:pointer" v-on:click="doToggle()"></i> </div></div></div>
				<div class="card-body">
					<div class="mb-3">
						<input type="text" class="form-control" v-bind:placeholder="t('Enter Funnel Name')" v-model="funnelname">
					</div>
					<div class="mb-3 text-center text-error" v-if="(err.length>0)" v-html="t(err)"></div>
					<div class="mb-3">
						<button class="btn btn-block theme-button" v-on:click="renameFunnels()">{{t('Rename Funnel')}}</button>
					</div>
					<div class="mb-3 text-success text-center" v-if="(success.length>0)" v-html="t(success)"></div>
				</div>
			</div>
		</div>
	`,
	props: ['toggle', 'funnel_name'],
	mounted: function () {
		this.funnelname = funnel.funnel_name;
	},
	data: function () {
		return {
			funnelname: "",
			success: '',
			err: '',
		};
	},
	methods: {
		t: function (txt, arr = []) {
			return t(txt, arr);
		},
		doToggle: function () {
			this.toggle();
		}, 
		renameFunnels: function () {

			const url = new URL(window.location.href);
			const funnel_id = url.searchParams.get("id");
			var name = this.funnelname.toLowerCase().split(" ");
			var name = name.join("-");
			name = funnel.removeSpecialChars(name);

			var data = { "funnelid": funnel_id, "funnelname": name, renamefunnels: 1 };
			request.postRequestCb('req.php', data, (data) => {
				if (data.trim() == 1) {
					this.success = "Funnel Renamed Successfully";
					this.err = "";
					setTimeout(() => {
						this.success = "";
						this.doToggle();
						window.location.reload();
					}, 2000);
				} else {
					this.err = data;
					this.success = "";
				}
			});
		}
	}
});
//Vue funnel cloning container
Vue.component('funnel_cloner', {
	template: `<div class="col-sm-4">
			<div class="card clone-funnel-template-card shadow">
				<div class="card-header card-header-copy text-center">
				<div class="row"><div class="col">{{t('Copy Funnel')}}</div><div class="col text-right"> <i class="fas fa-times-circle" style="cursor:pointer" v-on:click="doToggle()"></i> </div></div></div>
				<div class="card-body">
					<div class="alert alert-info">{{t('Please provide funnel token to start the installation process')}}</div>
					<div class="mb-3">
						<input type="text" class="form-control" v-bind:placeholder="t('Enter Funnel Code')" v-model="remote_id">
					</div>
					<div class="mb-3 text-center" v-if="(err.length>0)" v-html="t(err)"></div>
					<div class="mb-3">
						<button class="btn btn-block theme-button" v-on:click="createPage()">{{t('Start Cloning')}}</button>
					</div>
				</div>
			</div>
		</div>`,
	props: ['toggle'],
	data: function () {
		return {
			remote_id: "",
			err: "",
		};
	},
	methods: {
		/*w:function(txt,arr=[]){
		w(txt,arr);
		},*/
		t: function (txt, arr = []) {
			return t(txt, arr);
		},
		doToggle: function () {
			this.toggle();
		},
		createPage: async function () {
			this.err = "";

			this.remote_id = this.remote_id.trim();
			if (this.remote_id.length < 1) {
				return;
			}
			let loader = new visualLoader();
			loader.set(document.querySelectorAll(".clone-funnel-template-card")[0]);
			loader.load('Loading...');
			let pages = [];

			let get_pages = await (new Promise((resolve, reject) => {
				request.postRequestCb('req.php', {
					clone_funnel_get_map: this.remote_id,
					current_funnel: funnel.current_funnel
				}, function (data) {
					try {
						pages = JSON.parse(data);
						resolve(pages);
						return;
					} catch (err) { console.log(err); resolve(false); return; }
				});
			}));

			if (get_pages && Array.isArray(pages) && pages.length > 0) {
				try {
					let lbl_container = document.querySelectorAll("#lblbtncontainer")[0];
					let older_lbls = lbl_container.getElementsByTagName("button");
					for (let i = older_lbls.length; i >= 1; i--) {
						lbl_container.removeChild(older_lbls[i - 1]);
					}

					let start = 0;
					let counted_pages = [];
					for (let i = 0; i < pages.length; i++) {
						let page = pages[i];
						let done = await qfnlSiteDownloader(page.page_url, 'init_content', page.page_content);

						let btn;
						if (counted_pages.indexOf(page.filename) === -1) {
							counted_pages.push(page.filename);
							funnel.new_lbl_name = page.filename;
							addNewLabel();

							await new Promise((resolve, reject) => {
								setTimeout(function () { resolve(true); }, 1000);
							});

							//funnel.createIndicator();

							await new Promise((resolve, reject) => {
								setTimeout(function () { resolve(true); }, 200);
							});
							btn = lbl_container.getElementsByTagName("button")[i];
							btn.setAttribute('category', page.category);
						}
						else {
							btn = lbl_container.getElementsByTagName("button")[counted_pages.indexOf(page.filename)];
						}

						await new Promise((resolve, reject) => {
							setTimeout(function () { resolve(true); }, 200);
						});

						btn.dispatchEvent(new Event("mousedown"));

						await new Promise((resolve, reject) => {
							setTimeout(function () { resolve(true); }, 100);
						});

						let clone_req = { qfnl_clone_site: page.page_url, clone_step: "download" };
						clone_req = { ...clone_req, "funnel_id": funnel.current_funnel, "ab_type": page.type, "lavel": funnel.template_selector_btn, "category": funnel.selected_template_category, "page": funnel.page_foler_name };

						//console.log(clone_req);

						await new Promise((resolve, reject) => {
							request.postRequestCb('req.php', clone_req, (data) => {
								//alert(data);
								resolve(true);
							});
						});

						await new Promise((resolve, reject) => {
							setTimeout(function () { resolve(true); }, 200);
						});

						funnel.funnalSettingToggle();

						funnel.page_meta = JSON.parse(page.metadata);
						funnel.page_title = page.title;
						funnel.template_has_a_b_test = (parseInt(page.hasabtest)) ? true : false;
						if (page.active_type !== undefined) {
							funnel.page_active_type = page.active_type.trim();
						}

						let saved_settings = JSON.parse(page.settings);
						saved_settings.snippet_integrations = [];

						//console.log(page.settings);
						funnel.page_settings = saved_settings;

						funnel.page_header_scripts = page.pageheader;
						funnel.page_footer_scripts = page.pagefooter;

						funnel.valid_inputs_pages = page.inputs;

						await new Promise((resolve, reject) => {
							setTimeout(function () { resolve(true); }, 200);
						});

						await new Promise((resolve, reject) => {
							funnel.updatecurrentfunnelsetting((data) => { resolve(data); });
						});

						await new Promise((resolve, reject) => {
							setTimeout(function () { resolve(true); }, 400);
						});

						await funnel.takeCurrentScreenshot(funnel.current_funnel, funnel.page_foler_name, page.type, funnel.template_selector_btn, funnel.selected_template_category);

						await new Promise((resolve, reject) => {
							setTimeout(function () { resolve(true); }, 400);
						});

						funnel.funnalSettingToggle();

						// start +=i*100;
						// setTimeout((name)=>{
						// 	funnel.new_lbl_name=name;
						// 	addNewLabel();
						// 	funnel.createIndicator();
						// },start,data[i].filename);
					}
					loader.done();
					window.location = `index.php?page=create_funnel&id=${funnel.current_funnel}`;
				}
				catch (err) {
					console.log(err);
				}
			}
			else {
				loader.unable(this.t("Something wrong please try again."));
				setTimeout(function () { loader.unset(); }, 1000);
				this.err = `<p class='text-center text-danger'>${this.t('Something wrong please try again.')}</p>`;
			}
		}
	},
	watch: {}
});
//blank website label template
Vue.component("qfnl-addnew-lbl", {
	template: `<div class="addnewlblanddroplabel"><button class="btn intro-newpage theme-button btn-block mt-2" data-bs-toggle="modal" data-bs-target="#upgradeFromFreeModal" v-if="all_pages>=4 && user_plan_type==2"><i class="fas fa-plus"></i> {{t("New Page")}}</button><button class="btn intro-newpage theme-button btn-block mt-2" v-on:click="openLabelNameAdder()" v-else><i class="fas fa-plus"></i> {{t("New Page")}}</button></div>
	`,
	data: function () {
		return {
			openclosename: 0,
			lblname: "",
		};
	},
	props: {
		user_plan_type: {
			type: Number,
			default: 2
		},
		all_pages: {
			type: Number,
			default: 0
		}
	},
	methods: {
		t: function (txt, arr = []) {
			let str = t(txt, arr);
			return this.decodeHTMLEntities(str);
		},
		decodeHTMLEntities: function (str) {
			if (str && typeof str === 'string') {
				var txt = document.createElement("textarea");
				txt.innerHTML = str;
				str = txt.value;
			}
			return str;
		},
		openLabelNameAdder: function () {
			funnel.close_new_model = true;
		},

	},
});
//AB template start
Vue.component('template-detail', {
	template: "<div class='card template-card shadow' v-bind:style='{display:(haspage)? \"block\":\"none\"}'><div class='card-header card-header-copy text-center'>{{t('Template')}} {{(name=='a')? '':'B'}}</div><div v-bind:class='\" card-body abdivcontent\"+name+lbl' style='height:250px;background-color:#fff;' v-html='(hashtml)? hascontent:nocontent'></div><div class='card-footer justify-content-center footerabstat'><div class='row'><div class='col-5'><p>{{t('Views')}}: {{t(views.toLocaleString())}}</p><p>{{t('Conversion')}}: <span v-html='t(converts)'></span></p></div><div class='col-7 text-right'><span v-html='(hashtml)? hastemplate:notemplate'></span></div></div></div>{{chkLbl()}}</div>",
	props: ['name', 'fid', 'lbl', 'backgroundimg'],
	mounted: function () {
		this.initTemplate();
	},
	data: function () {
		return {
			haspage: true,
			cachelbl: 1,
			views: 0,
			converts: 0,
			reattempt: 0,
			hashtml: false,
			nocontent: "<div><center><h3 style='font-size:200px;opacity:0.2;'><i class='fas fa-image'></i></h3></center></div>",
			hascontent: "<div></div>",
			notemplate: "<div><button type='button' class='btn theme-button btn-xs' style='display:inline-block;margin-right:2px;min-width:158px;margin-bottom:2px;' onclick='openTemplateSelector(\"" + this.name + "\")'>" + t('Choose&nbsp;Template') + "&nbsp;<i class='far fa-hand-point-up' ></i></button><button type='button' class='btn theme-button btn-xs' style='display:inline-block;min-width:158px;' onclick='editTemplate(" + this.fid + ",\"" + this.name + "\")'>" + t('Create&nbsp;From&nbsp;Scratch') + "&nbsp;<i class='fas fa-pencil-alt'></i></button></div>",
			hastemplate: "<div><button type='button' class='btn theme-button btn-xs' style='display:inline-block;margin-right:2px;min-width:158px;margin-bottom:2px;' onclick='openTemplateSelector(\"" + this.name + "\")'>" + t('Change&nbsp;Template') + "&nbsp;<i class='far fa-hand-point-up' ></i></button><button type='button' style='display:inline-block;min-width:158px;' class='btn theme-button btn-xs' onclick='editTemplate(" + this.fid + ",\"" + this.name + "\")'>" + t("Edit&nbsp;Existing&nbsp;Page") + "&nbsp;<i class='far fa-edit' ></i></button></div>",
			cardbg: {
				height: '300px',
				backgroundColor: "#fff",
			},
		}
	},
	methods: {
		/*w:function(txt,arr=[]){
		w(txt,arr);
		},*/
		t: function (txt, arr = []) {
			return t(txt, arr);
		},
		test: function () { alert(1); },
		initTemplate: function () {
			var thisvue = this;
			try {
				setTimeout(function () {
					//alert(funnel.page_foler_name.trim().length);
					thisvue.haspage = (funnel.page_foler_name.trim().length > 0) ? true : false;
					var settingcircles = document.getElementsByClassName("setiingcircle");
					for (var i = 0; i < settingcircles.length; i++) {
						settingcircles[i].style.display = (thisvue.haspage) ? 'block' : 'none';
					}
				}, 20);
			} catch (err) { console.log(err.message); }
			try {
				setTimeout(function () {
					document.getElementsByClassName("abdivcontent" + thisvue.name + thisvue.lbl)[0].style.backgroundColor = "#fff";
					document.getElementsByClassName("abdivcontent" + thisvue.name + thisvue.lbl)[0].style.backgroundImage = "url()";
				}, 200);

				this.cachelbl = this.lbl;
				var loada = new visualLoader(); var loadb = new visualLoader();
				try {
					loada.set(document.getElementsByClassName("template-card")[0]);
					loadb.set(document.getElementsByClassName("template-card")[1]);
					loada.load(); loadb.load();
				} catch (err) { console.log(err.message); }

				var reqdata = { "currentfunnelabdetail": 1, "funnel_id": this.fid, "label": this.lbl, "type": this.name };
				var currentabtype = this.name;
				request.postRequestCb('req.php', reqdata, function (data) {
					try {
						var tempdata = data.trim();
						//alert(tempdata);
						if (tempdata.length > 2) {
							try {
								var jsn = JSON.parse(tempdata);
							}
							catch (errrr) {
								if (thisvue.reattempt == 0) {
									thisvue.initTemplate();
									++thisvue.reattempt;
									loada.unset(); loadb.unset();
								}
							}
							//console.log(jsn);
							//alert(jsn.templateimg);

							funnel.page_title = jsn.title;
							try {
								var tempmetadata = JSON.parse(jsn.metadata);
								funnel.page_meta = tempmetadata;
							}
							catch (err) { console.log(err.message); }
							funnel.page_foler_name = jsn.filename;
							funnel.page_header_scripts = jsn.pageheader;
							funnel.page_footer_scripts = jsn.pagefooter;
							funnel.valid_inputs_pages = jsn.valid_inputs;
							funnel.selected_smtp = jsn.primarysmtp;
							funnel.selected_product = jsn.product;
							funnel.selected_payment_method = jsn.paymentmethod;
							funnel.selected_membership_pages = jsn.membership.split(',');

							if (jsn.active_type !== undefined) {
								funnel.page_active_type = jsn.active_type.trim();
							}

							if (jsn.settings.length > 0) {
								try {
									funnel.page_settings = JSON.parse(jsn.settings.trim());
								} catch (erst) { console.log(erst); }
							}
							var hasab = jsn.hasabtest;

							if (thisvue.name !== 'b') {
								if (hasab == '1') {
									funnel.template_has_a_b_test = true;
								}
								else {
									funnel.template_has_a_b_test = false;
								}
							}
							funnel.init_ab_turn_on = false;

							if (jsn.verified_membership_page !== undefined) {
								if (jsn.verified_membership_page == 1) {
									funnel.verifyed_membership_page = true;
								}
								else {
									funnel.verifyed_membership_page = false;
								}
							}

							var autorespondersarr = jsn.selares.split('@');
							var selectedautoresponders = document.getElementById("autoreslist").querySelectorAll("input[type=checkbox]");

							for (var i = 0; i < selectedautoresponders.length; i++) {
								if (autorespondersarr.indexOf(selectedautoresponders[i].value) < 0) {
									selectedautoresponders[i].checked = false;
								}
								else {
									selectedautoresponders[i].checked = true;
								}
							}


							var listsarr = jsn.lists.split('@');
							var selectedlists = document.getElementById("listslist").querySelectorAll("input[type=checkbox]");

							for (var i = 0; i < selectedlists.length; i++) {
								if (listsarr.indexOf(selectedlists[i].value) < 0) {
									selectedlists[i].checked = false;
								}
								else {
									selectedlists[i].checked = true;
								}
							}

							setTimeout(function () {
								thisvue.views = jsn.viewcount;
								thisvue.converts = "<a href='index.php?page=optins&funnelid=page@" + funnel.current_funnel + "@" + jsn.id + "'>" + jsn.convertcount.toLocaleString() + "</a>";
								thisvue.hashtml = true;
								//alert(JSON.stringify(jsn));
								try {
									//alert(jsn.templateimg);
									let fURL = "";
									try {
										if (jsn.templateimg.indexOf('data:image/png;base64,') === 0) {
											fURL = `${funnel.funnel_url}/${funnel.page_foler_name}`;
										}
										else {
											fURL = `${funnel.funnel_url}/${jsn.templateimg}`;
										}
									} catch (err) { console.log(err); }
									//document.getElementsByClassName("abdivcontent"+jsn.type+jsn.level)[0].style.backgroundImage=`url("${jsn.templateimg}")`;
									setPreview(fURL, "abdivcontent" + jsn.type + jsn.level, jsn.type);
									document.getElementsByClassName("abdivcontent" + jsn.type + jsn.level)[0].style.backgroundRepeat = "no-repeat";
									document.getElementsByClassName("abdivcontent" + jsn.type + jsn.level)[0].style.backgroundSize = "contain";
									document.getElementsByClassName("abdivcontent" + jsn.type + jsn.level)[0].style.backgroundPosition = "center";
									document.getElementsByClassName("abdivcontent" + jsn.type + jsn.level)[0].style.height = "250px";
								} catch (errrdf) { console.log(errrdf); }
							}, 2000);

						}
						else {
							thisvue.hashtml = false;
							//funnel.initSetup();

							if (currentabtype != 'b') {
								//autoresponders
								var selectedautoresponders = document.getElementById("autoreslist").querySelectorAll("input[type=checkbox]");
								for (var i = 0; i < selectedautoresponders.length; i++) {
									selectedautoresponders[i].checked = false;
								}
								//selected lists
								var selectedlistssss = document.getElementById("listslist").querySelectorAll("input[type=checkbox]");
								for (var i = 0; i < selectedlistssss.length; i++) {
									selectedlistssss[i].checked = false;
								}
							}

						}

						loada.unset(); loadb.unset();
					} catch (err) { console.log(err.message); }

				});
			}
			catch (err) { console.log(err.message); }
		},
		chkLbl: function () {
			if (this.cachelbl != this.lbl) {
				if (funnel.manual_selection && (this.cachelbl > 0 && this.lbl > 0)) {
					funnel.initSetup();
					funnel.manual_selection = false;
					//alert(this.cachelbl+"--"+this.lbl);
				}
				this.initTemplate();
			}
		},
	},
});
//AB template Ends


//previewCreator
function setPreview(url, cls, type) {
	try {
		let ifr = document.querySelectorAll(`iframe.screenshot_ifr_${type}`)[0];
		ifr.src = url;
		let doc;
		try {
			doc = document.getElementsByClassName(cls)[0];
			doc.style.backgroundImage = `url("assets/img/visual_cog.gif")`;
		} catch (err) { console.log(err); }
		ifr.onload = function () {
			html2canvas(ifr.contentWindow.document.body, { width: 1200 }).then(canvas => {
				try {
					let url = canvas.toDataURL('image/png');
					doc.style.backgroundImage = `url("${url}")`;
					doc.style.backgroundSize = 'cover';
				} catch (err) { console.log(err); }
			});
		};
	} catch (err) {
		console.log(err);
	}
}

var funnel = new Vue({
	el: "#funnel",
	mounted: function () {
		this.initHideContext();
	},
	data: {
		is_ajax_loading: false,
		funnel_name: "",
		funnel_host: document.getElementById("hostname").value,
		funnel_url: document.getElementById("hostname").value,
		funnel_url_full: document.getElementById("funnel_url").value,
		funnel_url_slug: "",
		err: "",
		funnel_type: 0,
		manual_selection: false,
		step: [true, false],
		current_funnel: 0,
		current_step: 'step_1',
		funnel_modify_pre_index: 0,
		template_detail_a: initialtemplate,
		template_detail_b: initialtemplate,
		template_has_a_b_test: false,
		tmpltcrdstyle: tmpltcrdstyle,
		template_selector_btn: 0,
		pagecount_start_from: 1,
		movable_element: 0,
		page_title: "",
		page_foler_name: "",
		page_header_scripts: "",
		page_footer_scripts: "",
		funnel_setting_err: "",
		funnel_setting_view: false,
		common_inputs_for_current_funnel: "firstname,lastname,name,email,phone,password,reenterpassword,remember_user,payment_method",
		valid_inputs_pages: "firstname,lastname,name,email,phone,password,reenterpassword,remember_user,payment_method",
		new_lbl_name: "",
		selected_template_category: "all",
		tempselected_category: "all",
		templatecontaineropened: 0,
		template_under_download: false,
		verifyed_membership_page: false,
		selected_smtp: "phpmailer",
		selected_product: 0,
		selected_payment_method: 0,
		new_label: 'Page',
		new_category: 'all',
		close_new_model: false,
		selected_membership_pages: [],
		page_meta: { description: '', icon: '', keywords: '', robots: '', copyright: '', DC_title: '' },
		page_settings: { cookie_notice: 0, redirect_for_post: 0, redirect_for_post_url: '', snippet_integrations: [], page_cache: 0, zapier_enable: false, pabbly_enable: false, active_amp: false },
		funnel_cloner_opened: false,
		funnel_rename_opened: false,
		init_ab_turn_on: false,
		initiated_template_blocks: false,
		force_rename: false,
		show_context: false,
		page_position_changing: false,
		pglvl_active: 0,
		page_active_type: 'NA',
		cname: 1, // Добавляем переменную cname в данные
	
	},

	methods: {
		t: function (txt, arr = []) {
			return t(txt, arr);
		},
		doInitAbTurnOn: function () {
			this.init_ab_turn_on = true;
		},
		toggleFunnelCloner: function () {
			this.funnel_cloner_opened = !this.funnel_cloner_opened;
		},
		toggleFunnelRename: function () {
			this.funnel_rename_opened = !this.funnel_rename_opened;
		},
		addFunnelAndPageValidInput: function (var_name, e) {
			let value = e.target.value;
			let reg = /[\n]+/g;
			let inp_arr = value.replace(reg, ",").split(",");
			let final_arr = [];
			inp_arr.map((data) => {
				data = data.trim();
				if (data.length > 0) {
					final_arr.push(data);
				}
			});

			eval(`this.${var_name}=final_arr.join(',')`);
		},
		initSetup: function () {
			this.template_has_a_b_test = false;
			this.page_title = "";
			this.page_header_scripts = "";
			this.page_footer_scripts = "";
			this.valid_inputs_pages = "firstname,lastname,name,email,password,reenterpassword,remember_user,payment_method";
			//this.selected_template_category="all";
			//this.tempselected_category="all";
			this.verifyed_membership_page = false;
			this.selected_smtp = "phpmailer";
			this.selected_product = 0;
			this.selected_payment_method = 0;
			this.selected_membership_pages = [];
			this.page_meta = { description: '', icon: '', keywords: '', robots: '', copyright: '', DC_title: '' };
			this.page_settings = { cookie_notice: 0, redirect_for_post: 0, redirect_for_post_url: '', snippet_integrations: [], page_cache: 0, zapier_enable: false, pabbly_enable: false, active_amp: false };
			this.force_rename = false;
			this.show_context = false;
			this.page_active_type = 'NA';
		},
		activeCacheForAMP: function () {

			if (this.page_settings.active_amp && !this.page_settings.page_cache) {
				this.page_settings.page_cache = true;
			}
		},
		headerAdder: function () {
			var project = this.funnel_type;
			project = project.charAt(0).toUpperCase() + project.slice(1).toLowerCase();
			var header_doc = document.getElementById("uniquefunnelheader");
			if (project == "Blank") {
				project = "Custom"
			}
			if (this.funnel_setting_view) {
				header_doc.innerHTML = this.t("Edit Page") + " <span style='font-size:15px'>" + project + " / <a href='" + this.funnel_url + "'>" + this.funnel_name + "</a> / <a href='" + this.funnel_url + "/" + this.page_foler_name + "'>" + this.page_foler_name + "</a></span>";
			}
			else {
				header_doc.innerHTML = this.t("Edit Funnel") + " <span style='font-size:15px'>" + project + " / <a href='" + this.funnel_url + "'>" + this.funnel_name + "</a></span>";
			}
			modifytitle(this.funnel_name, "Edit Funnel");
		},
		funnalSettingToggle: function () {
			this.funnel_setting_view = (this.funnel_setting_view) ? false : true;
			if (this.funnel_setting_err.indexOf("successfully") > -1) {
				this.funnel_setting_err = "";
			}
			this.headerAdder();
		},
		updateMainInputsFromPage: function () {
			//type for main 1 for page 2
			try {
				var main = this.common_inputs_for_current_funnel.split(',');
				var page = this.valid_inputs_pages.split(',');

				for (i = 0; i < page.length; i++) {
					if (main.indexOf(page[i]) < 0) {
						if (page[i].trim().length < 1) { continue; }
						main.push(page[i]);
					}
				}
				this.common_inputs_for_current_funnel = main.join(',');
			}
			catch (err) { console.log(err.message); }
		},
		urlSuggester: function () {
			var name = this.funnel_name.toLowerCase().split(" ");
			var name = name.join("-");
			name = this.removeSpecialChars(name);
			this.funnel_url_slug = name;
		},
		createFunnel: function () {
			this.err = "";
			visualloader.set(document.getElementsByClassName("visual-pnl")[0]);
			visualloader.load(this.t("Creating URL"));
			visualloader.addStyle("current", ["width:100% !important;"]);
			if (this.funnel_url.length < 4 || this.funnel_name < 1 || this.funnel_type == '0') {
				this.err = `<div style='overflow-wrap:break-word;max-height:60px;max-width:100%;overflow-y:auto;margin-bottom:5px;'><center>${this.t('Fill all required fields properly')}</center></div>`;

				visualloader.unable(this.t("Unable To Create"));
				var t = setTimeout(function () {
					visualloader.unset();
				}, 1000);
			}
			else {
				var thisvue = this;
				var fields = { "createfunnel": 1, 
				"funnel_url": this.funnel_url.trim(),
				"funnel_name": this.funnel_name.trim(),
				"funnel_type": this.funnel_type,
				"modify_index": this.funnel_modify_pre_index,
            	"cname": this.cname // Добавляем CNAME в запрос
			};

				request.postRequestCb('req.php', fields, function (data) {
					data = data.trim();
					if (!isNaN(data)) {
						visualloader.done("Done");
						window.history.pushState(
							null,
							null,
							`?page=create_funnel&id=${data}`
						);
						window.location = location.href;
					}
					else {
						visualloader.unable("Unable To Create");
						var t = setTimeout(function () {
							visualloader.unset();
						}, 1000);
						thisvue.err = data;
						thisvue.err = "<div style='overflow-wrap:break-word;max-height:60px;max-width:100%;overflow-y:auto;margin-bottom:5px;'><center>" + thisvue.t(thisvue.err) + "</center></div>";
					}
				});
			}

		},

		removeSpecialChars: function (str) {
			const specialChars = /[`!@#$%^&*()+=\[\]{};':"\\|,.<>\/?~]/gim;
			return str.replace(specialChars, "");
		},

		selectedFunnelType: function (type) {
			if (this.current_funnel != 0) { if (this.funnel_type == type) { return true } else { return false }; }
		},

		activeMenuBtn: function (e) {
			this.force_rename = false;
			try {
				if (!this.manual_selection) { this.manual_selection = true; }

				var current = e.target;

				try {
					if ((!parseInt(current.getAttribute('ready'))) || (!this.initiated_template_blocks)) {
						this.initiated_template_blocks = true;
						this.page_foler_name = current.innerText.trim().toLowerCase();
						this.page_foler_name = this.page_foler_name.replace(/[\s]+/g, "-");
					}
				} catch (err) { console.log(err); }
				this.funnel_setting_err = "";

				var doc = document.getElementsByClassName("mnubtn");
				for (var j = 0; j < doc.length; j++) {
					doc[j].classList.remove('activemenubtn');
				}
				current.classList.add('activemenubtn');

				this.template_selector_btn = current.getAttribute('lbl');

				try {
					this.selected_template_category = current.getAttribute("category");
				} catch (errrr) { }
				if (this.selected_template_category === null) {
					this.selected_template_category = "all";
				}

				this.detectCurrentCategory();
			}
			catch (err) {
				console.log(err.message);
			}
		},
		addNewLabel: function () {
			this.tempselected_category = this.new_category;
			this.new_lbl_name = this.new_label;
			addNewLabel();
			this.new_category = "all";
			this.new_label = "Page";
			this.close_new_model = false;
			let Btns = document.querySelectorAll(`.mnubtn`);
			let firstBtn = Btns[Btns.length - 1];
			// fire the mousedown event on latest 
			if (firstBtn) {
				firstBtn.dispatchEvent(new Event('mousedown'));
				firstBtn.dispatchEvent(new Event("click"));
				firstBtn.dispatchEvent(new Event('mouseup'));

			}
		},
		closeNewModel: function () {
			this.close_new_model = false;
		},
		initHideContext: function () {
			try {

				document.body.addEventListener('mousedown', (e) => {

					if (this.show_context) {
						let doc = document.querySelectorAll(".fnl_page_context")[0];
						if (e.target.closest(".fnl_page_context") == null) {
							if (this.pglvl_active > 0) {
								--this.show_context;
							}
							else { this.show_context = false; }
						}
					}
				});
			} catch (err) { console.log(err); }
		},
		showContext: function (show = true, e = false) {
			try {
				e.preventDefault();
				if (show) {
					setTimeout(() => {
						this.show_context = true;
						let scrollHeight = Math.round(document.documentElement.scrollTop || document.body.scrollTop);
						let doc = document.querySelectorAll(".fnl_page_context")[0];
						doc.style.top = `${(e.pageY - scrollHeight)}px`;
						doc.style.left = `${e.pageX}px`;
					}, 10);
				}
				else {
					this.show_context = false;
				}
			} catch (err) { console.log(err); }
		},
		deleteCurrent: function (e = false) {
			if (this.page_position_changing) { return false; }
			var val = confirm(this.t("Do you want to delete this page?"));
			if (val) {
				this.page_position_changing = true;
				this.show_context = false;
				var doc = document.getElementById("lblbtncontainer");
				doc.removeChild(document.querySelectorAll(`button[lbl="${this.template_selector_btn}"]`)[0]);
				let current_tempselected_button = this.template_selector_btn;
				this.template_selector_btn = 0;
				this.page_foler_name = "";
				var data = { "dellbl": 1, "funnelid": this.current_funnel, "label": current_tempselected_button };
				request.postRequestCb('req.php', data, (data) => {
					this.processChangeLbl();
				});
			}
		},
		copyCurrent: function (e = false) {

			if (this.page_position_changing) {
				return false;
			}
			var val = confirm(this.t("Do you want to copy this page?"));
			if (val) {

				this.page_position_changing = true;
				this.show_context = false;
				let current_tempselected_button = this.template_selector_btn;

				var data = {
					copylbl: 1,
					funnelid: this.current_funnel,
					label: current_tempselected_button,
				};

				request.postRequestCb("req.php", data, (r) => {
					try {
						let res = JSON.parse(r);
						this.lblname = res.page_name;
						funnel.new_lbl_name = this.lblname;
						funnel.tempselected_category = res.category;
						addNewLabel();
						funnel.createIndicator();
						this.initTemplate();

					} catch (error) {
						console.log(error);
					}
				});
			}
		},
		changeLevel: function (up = false) {
			if (this.page_position_changing) { return false; }
			thisvue = this;
			var container = document.getElementById("lblbtncontainer");
			let lbl = parseInt(this.template_selector_btn);

			if (this.show_context && !isNaN(lbl)) {
				this.pglvl_active = 2;
				let selected = container.querySelectorAll(`button[lbl="${lbl}"]`)[0];
				let target = lbl + ((up) ? -1 : 1);
				if (target < 1) { return };
				target = container.querySelectorAll(`button[lbl="${target}"]`);
				if (target[0] === undefined) { return; }
				target = target[0];

				this.page_position_changing = true;

				if (up) {
					container.insertBefore(selected, target);
				}
				else {
					container.insertBefore(target, selected);
				}

				setTimeout(() => {
					this.processChangeLbl(() => {
						thisvue.template_selector_btn = selected.getAttribute('lbl');
					});
				}, 200);

			}
		},
		processChangeLbl: function (cb = false) {
			let thisvue = this;
			var container = document.getElementById("lblbtncontainer");
			var btns = container.getElementsByTagName("button");

			var arr = {};

			for (var i = 0; i < btns.length; i++) {
				var current = btns[i].getAttribute('lbl');
				arr[current] = i + 1;
				btns[i].setAttribute('lbl', i + 1);
			}
			var chngedlbls = JSON.stringify(arr);
			thisvue.page_position_changing = true;

			var lblhtml = document.getElementById("lblbtncontainer").innerHTML;
			lblhtml = lblhtml.substr(lblhtml.indexOf("/h4") + 4, lblhtml.length).trim();
			request.postRequestCb('req.php', { "chnglbl": 1, "funnel_id": thisvue.current_funnel, 'lbls': chngedlbls, "lblhtml": lblhtml }, function (data) {
				if (parseInt(data) === 1) { thisvue.page_position_changing = false; }
				thisvue.createIndicator();
				if (typeof (cb) === 'function') { cb(); }
			});
		},
		abload: function (template = 1) {

		},

		changePosition: function (elem) {
			if (elem.button === 0) {
				this.movable_element = elem.target;
				//alert(elem.target.getAttribute("ready"));
				var thisvue = this;
				var pos = elem.pageY;
				//console.log(pos);
				//alert(1);
				var container = document.getElementById("lblbtncontainer");

				var startmoving = 0;
				container.addEventListener('mousemove', function (e) {
					if (e.target.getAttribute("ready") == "1") {
						if (thisvue.movable_element !== 0) {
							if (startmoving > 5) {
								//console.log(e.pageY);
								var incor_dec = e.pageY - pos;
								pos += incor_dec;
								thisvue.movable_element.style.top = (pos - 320).toString() + "px";
								if (incor_dec === 0) {
									thisvue.movable_element.style.position = "static";
								}
								else { thisvue.movable_element.style.position = "absolute"; }

							}
							++startmoving;
						}
					}
				});

				document.addEventListener('mouseup', function (e) {
					startmoving = 0;
					if (thisvue.movable_element !== 0) {
						thisvue.movable_element.style.top = "0px";
						thisvue.movable_element.style.position = "static";
						thisvue.movable_element = 0;
					}
				});
			}
		},
		catchElement: function (evnt, force = 0) {
			thisvue = this;
			var container = document.getElementById("lblbtncontainer");
			if (this.movable_element != 0 || force == 1) {
				if (force == 0) {
					if (evnt.target.getAttribute("ready") == "1") {
						if (this.movable_element.getAttribute('lbl') > evnt.target.getAttribute('lbl')) {
							container.insertBefore(this.movable_element, evnt.target);
						}
						else {
							container.insertBefore(evnt.target, this.movable_element);
						}
					}
					this.movable_element.style.top = "0px";
					this.movable_element.style.position = "static";
				}


				var btns = container.getElementsByTagName("button");

				var arr = {};

				for (var i = 0; i < btns.length; i++) {
					var current = btns[i].getAttribute('lbl');
					arr[current] = i + 1;
					btns[i].setAttribute('lbl', i + 1);
				}

				var chngedlbls = JSON.stringify(arr);

				if (force == 0) {
					var temp = thisvue.movable_element.getAttribute('lbl');
				}
				else {
					chngedlbls = "html";
				}
				this.movable_element = 0;
				var lblhtml = document.getElementById("lblbtncontainer").innerHTML;
				lblhtml = lblhtml.substr(lblhtml.indexOf("/h4") + 4, lblhtml.length).trim();
				request.postRequestCb('req.php', { "chnglbl": 1, "funnel_id": thisvue.current_funnel, 'lbls': chngedlbls, "lblhtml": lblhtml }, function (data) {
					if (force == 0) { thisvue.template_selector_btn = temp; }
					thisvue.createIndicator();
				});
			}
		},
		updatecurrentfunnelsetting: function (cb = null, editor = false) {
			this.updateMainInputsFromPage();
			this.detectCurrentCategory(1);
			var thisvue = this;
			this.funnel_setting_err = "";
			var hasab = 0;
			if (this.template_has_a_b_test) {
				hasab = 1;
			}

			var selectedautoresponders = document.getElementById("autoreslist").querySelectorAll("input[type=checkbox]");

			var autoresponders = "";
			for (var i = 0; i < selectedautoresponders.length; i++) {
				if (selectedautoresponders[i].checked) {
					autoresponders += selectedautoresponders[i].value + '@';
				}
			}


			var selectedlists = document.getElementById("listslist").querySelectorAll("input[type=checkbox]");

			var lists = "";
			for (var i = 0; i < selectedlists.length; i++) {
				if (selectedlists[i].checked) {
					lists += selectedlists[i].value + '@';
				}
			}

			let page_name = this.page_foler_name.toLowerCase();
			page_name = page_name.replace(/\s/g, "-");

			var jsn = JSON.stringify({ page_title: this.page_title, metadata: JSON.stringify(this.page_meta), page_folder: this.page_foler_name.toLowerCase(), header_scripts: this.page_header_scripts, footer_scripts: this.page_footer_scripts, has_ab: hasab, valid_inputs: this.common_inputs_for_current_funnel, valid_inputs_page: this.valid_inputs_pages, autoresponders: autoresponders, vrified_membership: this.verifyed_membership_page, page_category: this.selected_template_category, smtps: this.selected_smtp, lists: lists, product: this.selected_product, payment_method: this.selected_payment_method, membership_pages: this.selected_membership_pages.join(','), page_settings: JSON.stringify(this.page_settings), force_rename: (this.force_rename) ? 1 : 0, active_type: this.page_active_type });

			var update = { "update_funnel_setting": 1, "funnel_id": this.current_funnel, "label": this.template_selector_btn, "data": jsn };
			let prenEl = document.querySelector(`button[lbl="${this.template_selector_btn}"]`);

			request.postRequestCb('req.php', update, function (data) {
				//console.log(data);
				var res = data.trim();
				if (res == 1) {
					prenEl.innerHTML = thisvue.converCapatilize(page_name.replace(/-/g, " "));
					thisvue.force_rename = false;
					if (editor === false) {
						thisvue.funnel_setting_err =
							"Saved successfully (Please consider closing existing page editors if opened, reopen it if required!)";
						setTimeout(function () {
							thisvue.funnel_setting_err =
								"";
						}, 5000);
					}
					thisvue.catchElement('', 1);
					if (thisvue.page_settings.page_cache) {
						//request.getRequest(thisvue.funnel_url+"/"+thisvue.page_foler_name+"/");
					}
				}
				else {
					thisvue.funnel_setting_err = res;
				}
				if (cb !== null) { cb(data); }
			});
		},
		closeSettingBtn: function (frst, second) {
			let secondS = document.querySelectorAll(`.${second}`);
			let firstS = document.querySelectorAll(`.${frst}`);
			for (let i = 0; i < secondS.length; i++) {
				secondS[i].style.visibility = "hidden";
			}
			for (let i = 0; i < firstS.length; i++) {
				firstS[i].style.visibility = "visible";
			}
		},
		converCapatilize: function (str) {

			//split the above string into an array of strings 
			//whenever a blank space is encountered
			const arr = str.split(" ");
			//loop through each element of the array and capitalize the first letter.

			for (var i = 0; i < arr.length; i++) {
				arr[i] = arr[i].charAt(0).toUpperCase() + arr[i].slice(1);

			}

			//Join all the elements of the array back into a string 
			//using a blankspace as a separator 
			const str2 = arr.join(" ");
			return str2;
		},
		indicatorRequest: function (data, btn) {
			this.page_position_changing = true;
			request.postRequestCb('req.php', data, (data) => {
				this.page_position_changing = false;
				if (data.trim() == '0') {
					btn.setAttribute('ready', 0);
					btn.style.setProperty('opacity', '0.6', 'important');
				}
				else {
					btn.setAttribute('ready', 1);
					btn.style.setProperty('opacity', '1', 'important');
				}
			});
		},
		createIndicator: function (current = 0) {
			var mnubtns = document.getElementById("lblbtncontainer").getElementsByTagName("button");
			if (current === 0) {
				for (var i = 0; i < mnubtns.length; i++) {
					var lvl = mnubtns[i].getAttribute('lbl');
					var postdata = { "currentfunnelabdetail": 1, "funnel_id": this.current_funnel, "type": "a", "label": lvl };
					this.indicatorRequest(postdata, mnubtns[i]);
					//console.log(mnubtns[i].getAttribute('lbl'));

				}
			}
		},
		openTemplateSelector: function (abtype) {
			var thisvue = this;
			var template_container = document.getElementById("templateinstalldiv_container");
			if (this.templatecontaineropened == 0) {
				var bdy = document.getElementsByClassName("labelscontainer")[0];
				var div = document.createElement("div");
				div.setAttribute('id', 'templateinstalldiv');

				var head = "<div class='card' style='background-color:white !important;border:0px;box-shadow: 0 .5rem 1rem rgba(0,0,0,.15)!important;border:0px !important;'><div class='card-header' style='background-color:white !important;border: 0!important;font-size: 23px!important;color: #1d56bb;'>" + this.t('Choose Template') + " (" + this.t('Category') + ": " + this.t(this.tempselected_category.charAt(0).toUpperCase() + this.tempselected_category.slice(1)) + ")<span id='deltemplateselectiondiv' style='right:10px;top:8px;position:absolute;font-size:20px;cursor:pointer;color:rgb(31, 87, 202);'><i class='fas fa-arrow-alt-circle-left'></i>&nbsp;" + this.t('Go&nbsp;Back') + "</span></div><div class='card-body'><div class='row'><div class='col text-right'><button class='btn btn-primary theme-button mb-1' onclick='zipTemplateUpload(event)' data-bs-toggle='tooltip' title='" + this.t("Upload your template as ZIP format. Make sure the main content file is named as \"index.html\"") + "'><i class='fas fa-download'></i>&nbsp;" + this.t('Import&nbsp;Template') + "</button>&nbsp;<button class='clonesite btn btn-prmary theme-button' style='margin-bottom:6px;'><i class='fas fa-copy'></i> " + this.t('Clone URL') + "</button></div></div><div class='input-group'><div class='input-group-prepend'><div class='input-group-text'>" + this.t('Search Templates') + "</div></div><input type='text' placeholder='" + this.t('Enter Your Search Attribute For Templates') + "' class='form-control srchtemplate'></div><div class='tmpltbdydiv' style='width:100%;margin-top:20px;'>";
				var footer = "</div></div></div>";

				div.innerHTML = head + "<center><center><h2 style='offset:4'>" + this.t('Loading...') + "</h2></center></center>" + footer;

				//div.classList.add("row");
				this.selected_template_category = (this.tempselected_category == null) ? 'all' : this.tempselected_category;
				var reqdata = { "current_funnel": this.current_funnel, "folder_name": this.page_foler_name, "abtype": abtype, "type": this.tempselected_category, "load_templates": 1 };

				var close_div = function () {
					try {
						template_container.removeChild(div);
					} catch (errrr) { }
					thisvue.templatecontaineropened = 0;
					thisvue.template_under_download = false;
				};

				doEscapePopup(close_div);

				request.postRequestCb('req.php', reqdata, function (data) {
					try {
						div.getElementsByClassName("tmpltbdydiv")[0].innerHTML = data;
						document.getElementById("deltemplateselectiondiv").addEventListener('click', function () {
							close_div();
						});
					} catch (err) { }
				}
				);
				template_container.appendChild(div);
				try {
					setTimeout(function () {
						try {
							document.getElementsByClassName("clonesite")[0].onclick = function () {
								var clone_doc = document.createElement("div");
								clone_doc.classList.add('row');
								var clonedoc_content = `<div style="top:0px;left:0px;height:100%;width:100%;position:fixed;background-color:#1ab2ff;opacity:0.5"> </div><div class="col-sm-4" style="top:30%;left:40%;transform:translate:(-50%,-50%);position:fixed;"><div class="card pnl clonecard" style="z-index:9999;"><div class="card-header"><div class="row"><div class="col">${thisvue.t('Clone URL')}</div><div class="col text-right"><i class="fas fa-times closeclonediv" style="cursor:pointer;"></i></div></div></div>
				<div class="card-body">
				<label>${thisvue.t('Add The Page URL You Want To Clone')}</label>
				<div class="alert alert-info">${thisvue.t('Download HTML & CSS content of any static page')}</div>
				<div class="mb-3">
				<input type="text" class="form-control urltoclone" placeholder="${thisvue.t('Add The Page URL You Want To Clone')}">
				<div class="text-right text-primary" style="cursor: pointer;"><span class="do_clone_htmlcontent">${thisvue.t('Already Have HTML content?')}</span></div>
				<textarea class="form-control clonehtmlcontent" style="display:none" placeholder='${thisvue.t('Enter Content')}'></textarea>
				<p class="clone_err text-center mt-2"></p>
				<button class="btn theme-button btn-block mt-2 doclone">${thisvue.t('Clone The URL')}</button>
				</div>
				</div>
				</div></div>`;
								clone_doc.innerHTML = clonedoc_content;
								if (div.getElementsByClassName("clonecard").length < 1) {
									div.appendChild(clone_doc);
									setTimeout(
										function () {
											document.querySelectorAll(".do_clone_htmlcontent")[0].onclick = function () {
												let doc = document.querySelectorAll(".clonehtmlcontent")[0];
												doc.style.display = (doc.style.display === 'block') ? 'none' : 'block';
											};

											var closeclonediv = document.querySelectorAll(".closeclonediv")[0];
											closeclonediv.addEventListener("click", function () { div.removeChild(clone_doc); });
											var clonebtn = document.querySelectorAll("button.doclone")[0]
											clonebtn.addEventListener("click", async function () {
												var clonable_url = document.getElementsByClassName("urltoclone")[0].value.trim();
												var clonable_content = document.getElementsByClassName("clonehtmlcontent")[0].value;
												var cloneerr_div = document.getElementsByClassName("clone_err")[0];
												if (clonable_url.length > 2) {
													try {
														var clone_loader = new visualLoader();
														clone_loader.set(document.querySelectorAll("div.clonecard")[0]);
														clone_loader.load(thisvue.t("Please Wait, The Process May Take Few Minutes"));
													} catch (err) { console.log(err.messaes); }

													//alert(clonable_content);
													if (clonable_content.trim().length > 0) {
														clonable_content = clonable_content.trim();
														await qfnlSiteDownloader(clonable_url, 'init_content', clonable_content);
													}
													else { await qfnlSiteDownloader(clonable_url); }
													//alert("await done");

													let clone_req = { qfnl_clone_site: clonable_url, clone_step: "download" };
													clone_req = { ...clone_req, "funnel_id": thisvue.current_funnel, "ab_type": abtype, "lavel": thisvue.template_selector_btn, "category": thisvue.selected_template_category, "page": thisvue.page_foler_name };

													cloneerr_div.innerHTML = thisvue.t("Please Wait, This Process Will Take Few Minutes");
													cloneerr_div.style.color = "green";
													clonebtn.disabled = true;

													var tempstorebtn = thisvue.template_selector_btn;
													thisvue.template_selector_btn = 0;

													request.postRequestCb("req.php", clone_req, async function (data) {
														await thisvue.takeCurrentScreenshot(thisvue.current_funnel, thisvue.page_foler_name, abtype, tempstorebtn, thisvue.selected_template_category);
														thisvue.template_selector_btn = tempstorebtn;
														clonebtn.disabled = false;
														data = data.trim();
														//alert(data);
														if (data == '1') {
															try { clone_loader.done("Done"); } catch (err) { console.log(err.message); }
															setTimeout(
																function () {
																	closeclonediv.click();
																	document.getElementById("deltemplateselectiondiv").click();
																	//alert(tempstorebtn);
																	thisvue.updatecurrentfunnelsetting();
																	thisvue.createIndicator();
																}, 1500);
														}
														else {
															try { clone_loader.unable(thisvue.t("Unable To Download")); } catch (err) { console.log(err.message); }
															cloneerr_div.style.color = "#cc0066";
															cloneerr_div.innerHTML = data;

															thisvue.updatecurrentfunnelsetting();
															thisvue.createIndicator();
														}
														setTimeout(function () {
															try { clone_loader.unset(); } catch (err) { console.log(err.message); }
														}, 1500);

													});
												}
											});
										}, 200);
								}
							};
						}
						catch (err) { console.log(err.message); }

						document.getElementsByClassName("srchtemplate")[0].onkeyup = function (srchelem) {
							var searchdata = srchelem.target.value.trim();
							if (searchdata.length > 3) {
								div.getElementsByClassName("tmpltbdydiv")[0].innerHTML = "<center><h2 >Searching...</h2></center>";
								var reqdata_search = { "current_funnel": this.current_funnel, "folder_name": this.page_foler_name, "abtype": abtype, "type": this.selected_template_category, "load_templates": 1, "search_template": searchdata };
								request.postRequestCb('req.php', reqdata_search, function (data) {
									div.getElementsByClassName("tmpltbdydiv")[0].innerHTML = data;
									if (data.trim().length < 5) {
										div.getElementsByClassName("tmpltbdydiv")[0].innerHTML = "<center><h2 style='offset:2;'>" + thisvue.t('No Template Available') + "</h2></center>";
									}
								});
							}
							else {
								request.postRequestCb('req.php', reqdata, function (data) {
									div.getElementsByClassName("tmpltbdydiv")[0].innerHTML = data;
								});
							}
						}
					}, 1000);
				} catch (errrrrrr) { }


				++this.templatecontaineropened;
				thisvue = this;
				setTimeout(
					function () {
						if (thisvue.templatecontaineropened > 0) {
							document.getElementById("deltemplateselectiondiv").addEventListener('click', function () {
								try {
									document.getElementsByClassName("labelscontainer")[0].removeChild(document.getElementById("templateinstalldiv"));
								} catch (errrrr) { }
								thisvue.templatecontaineropened = 0;
							});
						}
					}, 1000);
			}
		},
		qfnlDownloadSpecificTemplate: function (thisdiv, templateid, abtype) {
			//console.log(arguments);
			var thisvue = this;
			var alltemp = document.querySelectorAll(".tmpltbdydiv")[0].querySelectorAll("div.card");
			for (var i = 0; i < alltemp.length; i++) {
				alltemp[i].getElementsByClassName("card-body")[0].style.border = "0px";
				alltemp[i].getElementsByClassName("card-body")[0].getElementsByClassName("masktemplateimg")[0].style.filter = "blur(0px)";
				alltemp[i].getElementsByClassName("card-body")[0].getElementsByClassName("rounddownload")[0].style.display = "none";
				//alltemp[i].getElementsByClassName("card-body")[0].getElementsByClassName("downloadhovermask")[0].style.display="none";
			}
			var downloader = thisdiv.getElementsByClassName("rounddownload")[0];
			//thisdiv.getElementsByClassName("downloadhovermask")[0].style.display="block";
			downloader.style.display = "block";
			thisdiv.getElementsByClassName("masktemplateimg")[0].style.filter = "blur(10px)";
			//thisdiv.style.borderRadius="4px";
			if (isNaN(templateid)) {
				return;
			}
			downloader.onclick = function (e) {
				if (thisvue.template_under_download) { return; }
				thisvue.template_under_download = true;
				var btn = downloader;
				btn.disabled = true;
				btn.innerHTML = "<h1><i class='fas fa-circle-notch fa-spin'></i></h1>";
				var reqdata = { "template_id": templateid, "funnel_id": thisvue.current_funnel, "ab_type": abtype, "lavel": thisvue.template_selector_btn, "category": thisvue.selected_template_category, "page": thisvue.page_foler_name, "save_template": 1 };

				request.setCookie('templateedited_' + thisvue.current_funnel + '_' + thisvue.template_selector_btn, 0, -1);
				var tempstorebtn = thisvue.template_selector_btn;
				thisvue.template_selector_btn = 0;
				//refreshDuringEdit();

				request.postRequestCb('req.php', reqdata, async function (data) {
					await thisvue.takeCurrentScreenshot(thisvue.current_funnel, thisvue.page_foler_name, abtype, tempstorebtn, thisvue.selected_template_category);
					thisvue.template_selector_btn = tempstorebtn;
					//console.log(data);
					btn.disabled = false;
					document.getElementById("deltemplateselectiondiv").click();
					thisvue.updatecurrentfunnelsetting();
					thisvue.createIndicator();
				});
			};
		},
		takeCurrentScreenshot: function (funnel, page, abtype, lavel, category) {
			return new Promise(function (resolve, reject) {
				resolve(1);
			});
		},
		detectCurrentCategory: function (update = 0) {
			try {
				var category = document.querySelectorAll("button[lbl='" + this.template_selector_btn + "']")[0].getAttribute("category");
				if (update == 0) {
					if (category === null) { category = "all"; }
					this.tempselected_category = category;
				}
				else {
					document.querySelectorAll("button[lbl='" + this.template_selector_btn + "']")[0].setAttribute("category", this.tempselected_category);
					this.selected_template_category = this.tempselected_category;
				}
			} catch (err) { }
		},
		getMarketPlaceURL: function (plugin = false) {
			let marketplace_url = `http://marketplace.cloudfunnels.in`;
			let current_url = window.location.href;
			if (plugin) {
				marketplace_url = `${marketplace_url}/plugin/view/${plugin}`;
			}
			try {
				let url = new URL(current_url);
				try {
					url.searchParams.forEach(s => {
						url.searchParams.delete(s);
					});
				} catch (err) {
					console.log(err);
				}
				url.searchParams.append('page', 'plugins');
				current_url = url.href;
			}
			catch (err) { console.log(err); }

			return `${marketplace_url}/?plugin_ref_installation=${btoa(current_url)}`;
		}
	},
	computed: {
		integrationsURL: function () {
			return this.getMarketPlaceURL('cf_plugin_for_externalscripts');
		},
		marketPlaceURL: function () {
			return this.getMarketPlaceURL();
		}
	},
	watch: {
		page_foler_name: function (new_val, old_val) {
			if (new_val != old_val) {
				//console.log(`Watch ${this.page_foler_name}`);		
			}
		},
		funnel_url_slug: function (_new) {
			let url = `${this.funnel_host}/${_new}`;
			url = url.trim().split("");
			if (url[url.length - 1] === "/") {
				url.pop();
			}
			url = url.join("");
			this.funnel_url = url;
		},
	},
});

var Newlabelcomponent = Vue.extend({
	template: '<button v-bind:category="category"  v-bind:lbl="nextlabel" class="btn btn-outline-secondary btn-block mnubtn" v-on:mousedown="activeMenuBtn($event)" v-on:contextmenu="showContext(true, $event)"><span class=""></span> {{labelname}}</button>',
	mounted: function () {
		this.inIt();
	},
	data: function () {
		return {
			nextlabel: 1,
			labelname: "page 1",
			category: 'all',
			page_url: '',
		};
	},
	methods: {
		changePosition: function (e) {
			funnel.changePosition(e);
		},
		activeMenuBtn: function (e) {
			funnel.activeMenuBtn(e);
		},
		catchElement: function (e) {
			funnel.catchElement(e);
		},
		showContext: function (stat, e) {
			funnel.showContext(stat, e);
		},
		inIt: function () {
			var len = 1;
			try {
				var allbtns = document.getElementById("lblbtncontainer").getElementsByTagName("button");
				if (allbtns.length > 0) {
					len = allbtns.length + 1;
				}
			}
			catch (err) {
				console.log(err);
			}
			this.nextlabel = len;

			if (funnel.new_lbl_name.length < 1) {
				funnel.new_lbl_name = 'page ' + len;
			}
			this.labelname = funnel.new_lbl_name;
			let lblname = this.labelname;
			lblname = lblname.replace(/\s/gi, "-").toLowerCase();
			this.page_url = `${funnel.funnel_url_full}/${lblname}`;
			this.category = funnel.tempselected_category;
			funnel.new_lbl_name = "";
		},
	}
});

function addNewLabel() {

	var component = new Newlabelcomponent().$mount();
	document.getElementById("lblbtncontainer").appendChild(component.$el);
	setTimeout(function () {
		funnel.createIndicator();
	}, 500);
}
function changeTemplate() {

}
var tempholderforeditor = 0;
var editcheckcookie = "templateedited";

var cache_lists = [];
var cache_autores = [];

function processCachingListsAndAutoresponders(set = true) {
	try {
		let lists = document.querySelectorAll("#listslist")[0].querySelectorAll("input[type=checkbox]");
		let autores = document.querySelectorAll("#autoreslist")[0].querySelectorAll("input[type=checkbox]");

		lists.forEach(list => {
			if (set && list.checked) {
				cache_lists.push(list.value);
			}
			else if (!set && cache_lists.indexOf(list.value) >= 0) {
				list.checked = true;
			}
		});

		if (!set) { cache_lists = []; }

		autores.forEach(auto => {
			if (set && auto.checked) {
				cache_autores.push(auto.value);
			}
			else if (!set && cache_autores.indexOf(auto.value) >= 0) {
				auto.checked = true;
			}
		});

		if (!set) { cache_autores = []; }

		//console.log(cache_lists);

	} catch (err) { console.log(err); }
}

function editTemplate(idd, name) {
	var lbl = funnel.template_selector_btn;
	var go = 0;
	var prevlbl = lbl - 1;
	try {
		go = document.querySelectorAll('button[lbl="' + prevlbl + '"]')[0].getAttribute('ready');
	} catch (e) { console.log(e.message); }

	if (lbl == 1) {
		go = 1;
	}
	//alert(lbl+" "+go);
	//without any reason
	go = 1;
	if (go > 0) {
		processCachingListsAndAutoresponders(true);
		var category = funnel.selected_template_category;
		if (category === null) {
			category = "all";
		}
		var editor = window.open("index.php?page=page_builder&fid=" + idd + "&abtype=" + name + "&lbl=" + funnel.template_selector_btn + "&category=" + category + "&folder=" + funnel.page_foler_name, "..::CloudFunnels Editor::..", "toolbar=yes,top=0,left=0,height=4000,width=4000");

		editor.moveTo(0, 0);
		editor.resizeTo(screen.width, screen.height);

		tempholderforeditor = lbl;

		editcheckcookie += "_" + idd + "_" + tempholderforeditor;

		editor.onbeforeunload = function () { request.setCookie(editcheckcookie, lbl, 30); };
		document.body.addEventListener('mousemove', refreshOnMouseMove);

		request.setCookie(editcheckcookie, 0, -1);
		funnel.template_selector_btn = 0;
		refreshDuringEdit();
	}
	else {
		alert(t("Seems you forgot to carete previous label"));
	}
}
function refreshOnMouseMove() {
	request.setCookie(editcheckcookie, tempholderforeditor, 30);
}
function refreshDuringEdit() {

	if (request.getCookie(editcheckcookie) < 1) {
		setTimeout(refreshDuringEdit, 1000);
	}
	else {
		funnel.template_selector_btn = request.getCookie(editcheckcookie);
		tempholderforeditor = 0;
		editcheckcookie = "templateedited";
		//alert(request.getCookie(editcheckcookie));
		processCachingListsAndAutoresponders(false);
		funnel.updatecurrentfunnelsetting(null, true);
		funnel.createIndicator();
	}

}

var settingdivopen = 1;
function settingDivOpenClose() {
	jQuery(document).ready(function ($) {
		$(".settingdiv").hide();
		$(".openclosetoggle").click(function () {
			if (settingdivopen == 1) {
				$(".settingdiv").show(500);
				settingdivopen = 0;
			}
			else {
				$(".settingdiv").hide(500);
				settingdivopen = 1;
			}
		});
	});
}
function openTemplateSelector(type) {
	funnel.openTemplateSelector(type);
}
function qfnlDownloadSpecificTemplate(div, id, abtype) {
	funnel.qfnlDownloadSpecificTemplate(div, id, abtype);
}

const qfnlimgloadersleep = function (delay = 5000) {
	return new Promise((resolve, reject) => {
		setTimeout(() => { resolve(1); }, delay);
	});
};
const qfnlSiteDownloader = function (url, doo = 'init', remote_content = '') {
	return new Promise((resolve, reject) => {
		request.postRequestCb("req.php", { qfnl_arrenge_cloner: doo, qfnl_clone_target_url: url, remote_content }, (data) => {
			data = data.trim();
			//console.log(data);
			if (data !== '0' && !isNaN(data)) {
				let count = parseInt(data);
				let count_req = 0;
				let image_downloader = async () => {
					//await qfnlimgloadersleep(10000);
					request.postRequestPromise("req.php", { qfnl_arrenge_cloner: "download_images", qfnl_clone_target_url: url }).then(async (res) => {
						if (count == 0) {
							//await qfnlimgloadersleep(10000);
							resolve(1);
						}
						else {
							++count_req;
							if (count_req == 10) {
								//await qfnlimgloadersleep(50000);
								count_req = 0;
							}
							else {
								//await qfnlimgloadersleep(20000);
							}
							--count;
							image_downloader();
						}
					});
				};
				image_downloader();
			}
			else { resolve(data); }
		});
	});
}

const zipTemplateUpload = function (e) {
	let doc = document.createElement("input");
	doc.type = "file";
	doc.accept = ".zip";
	doc.click();

	doc.onchange = function () {
		let file = doc.files[0];
		e.target.disabled = true;
		e.target.innerHTML = "<i class='fas fa-spinner fa-spin'></i>&nbsp;" + t('Loading') + "...";

		request.postRequestCb("req.php", {
			upload_zipped_template: 1,
			template_zip: file
		}, async function (res) {
			e.target.innerHTML = "";
			e.target.innerHTML = "<i class='fas fa-download'></i>&nbsp;" + t('Import&nbsp;Template') + "";
			try {
				//console.log(res);
				res = JSON.parse(res);
				if (res.status) {
					let url = res.data;
					document.querySelectorAll("button.clonesite")[0].click();
					await new Promise(function (resolve, reject) { setTimeout(function () { resolve(1); }, 300) });
					document.querySelectorAll("input.urltoclone")[0].value = url;
					await new Promise(function (resolve, reject) { setTimeout(function () { resolve(1); }, 100) });
					document.querySelectorAll("button.doclone")[0].click();
				}
				else {
					alert(res.data);
					e.target.disabled = false;
				}
			}
			catch (err) {
				alert(err.message);
				console.log(err);
				e.target.disabled = false;
			}
		});
	};
	return;
}

request.registerAjaxLoading(function (stat) {
	funnel.is_ajax_loading = stat;
});