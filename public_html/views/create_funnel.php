<?php
$funnel_id = 0;
global $current_app_version;
$vrs = str_replace(".", "", $current_app_version);
$new_v = false;
if ($vrs >= 512) {
	$new_v = true;
}
if (isset($_GET['funnel_id'])) {
	$funnel_id =	$_GET['funnel_id'];
}

$funnelsetting = funnelSettingsPopup($data_arr);
$funnel_url = str_replace("@@qfnl_install_dir@@", get_option('install_url'), $data_arr['flodername']);
?>
<input type="hidden" id="hostname" value="<?php
	if (get_option('qfnl_router_mode') == '1') {
		echo get_option('install_url');
	} else {
		$protocol = $data_arr['protocol'];
		echo $protocol . $_SERVER['HTTP_HOST'];
	}
	?>">
<input type="hidden" id="funnel_url" value="<?php echo $funnel_url; ?>">
<div class="container-fluid" id="funnel">
	<div class="row">

		<div class="col-sm-12">
			<div class="mb-3">

				<?php if (!$funnel_id) { ?>

					<div class="row mt-5" id='step_1' v-if="(current_step=='step_1')? true : false">
						<div class="col-sm-6 mw120 mx-auto">
							<div class="card visual-pnl shadow">
								<div class="card-header theme-text bg-white border-bottom-0">{{t('Create Project')}}</div>
								<div class="card-body">

									<span v-html="t(err)" class="text-danger small mt-1"></span>

									<div class="mb-3">
										<label>{{t('Funnel Name')}}</label>
										<input type="text" class="form-control" v-model="funnel_name" v-on:keyup="urlSuggester()" v-bind:placeholder="t('Add a Title')">
									</div>

									<div class="mb-3">
										<label>{{t('Funnel Type')}}</label>
										<select class="form-select" v-model="funnel_type">
											<option value='0'>{{t('Select Funnel Type')}}</option>
											<option value='webinar'>{{t('Webinar')}}</option>
											<option value='membership'>{{t('Membership')}}</option>
											<option value="sales">{{t('Sales')}}</option>
											<option value="blank">{{t('Custom')}}</option>
										</select>
									</div>
		
									<div class="mb-3">
										<label>{{t('Funnel URL')}}</label>
										<?php
										$trans_install_url = "";
										if ((get_option('qfnl_router_mode') == '1')) {
											$trans_install_url = get_option('install_url');
										} else {
											$trans_install_url = $_SERVER['HTTP_HOST'];
										}
										
										?>
										<div class="input-group">
											<div class="input-group-prepend" data-bs-toggle="tooltip" title="<?php w('Base URL'); ?>">
												<span class="input-group-text">{{funnel_host}}/</span>
											</div>
											<input type="text" class="form-control" data-bs-toggle="tooltip" v-bind:title="t('Path for the funnel')" v-bind:placeholder="t('Enter path')" v-model="funnel_url_slug" />
										</div>
									</div>
									<div class="mb-3">
										<label>{{t('CNAME')}}</label>
										<input type="checkbox"  v-model="cname" />
										<?php
										$trans_install_url = "";
										if ((get_option('qfnl_router_mode') == '1')) {
											$trans_install_url = get_option('install_url');
										} else {
											$trans_install_url = $_SERVER['HTTP_HOST'];
										}
										$trans_install_url_short = str_replace("https://", "", $trans_install_url);
										?>
										<div class="input-group">
											<div class="input-group-prepend" data-bs-toggle="tooltip" title="<?php w('Base URL'); ?>">
												<span class="input-group-text">https://</span>
											</div>
											<input type="text" class="form-control" data-bs-toggle="tooltip" v-bind:title="t('Path for the CNAME')" v-bind:placeholder="t('Enter path')" v-model="funnel_url_slug" />
											<div class="input-group-prepend" data-bs-toggle="tooltip" title="<?php w('Base URL'); ?>">
												<span class="input-group-text">.<?=$trans_install_url_short?></span>
											</div>
											
										</div>
									</div>
									<?php if ($_SESSION['user_plan_type' . $site_token_for_dashboard] == 2 && $data_arr['total_funnels'] >= 1) { ?>
										<button type="button" class="btn theme-button float-right mt-2" style="margin-top:8px;float:right" data-bs-toggle="modal" data-bs-target="#upgradeFromFreeModal">
											<i class="fas fa-check"></i> {{t('Create Funnel')}}
										</button>
									<?php } else { ?>
										<button type="button" class="btn theme-button float-right mt-2" style="margin-top:8px;float:right" name="createdir" v-on:click="createFunnel()">
											<i class="fas fa-check"></i> {{t('Create Funnel')}}
										</button>
									<?php } ?>

									<div class="funnel-index-available-notice" v-if="err.indexOf('another funnel') !==-1">
										<div class="container">
											<div class="row">
												<div class="col-sm-12 mb-4">
													<div class="text-white" v-html="err"></div>
												</div>
												<div class="col-8">
													<button class="btn btn-secondary" v-on:click="function(){ err= ''; funnel_modify_pre_index= true; createFunnel();}">Overwrite
														existing funnel</button>
												</div>
												<div class="col-4">
													<button class="btn theme-button" v-on:click="function(){ err= ''; }">Modify</button>
												</div>
											</div>
										</div>
									</div>

								</div>

							</div>
						</div>
					</div>
					<!-- Funnel Cloner-->
					<div class="row mb-2" v-if="current_step=='step_2'">
						<div class="col-sm-12 text-right" v-if="!funnel_cloner_opened || !funnel_rename_opened">
							<button class="btn btn-primary  theme-button" v-on:click="toggleFunnelRename()"><i class="fas fa-edit"></i>&nbsp;<?php w('Rename Funnels'); ?></button>

							<?php if (!$_SESSION['user_plan_type' . $site_token_for_dashboard] || $_SESSION['user_plan_type' . $site_token_for_dashboard] == 2) { ?>
								<button class="btn btn-primary theme-button" data-bs-toggle="modal" data-bs-target="#upgradeFromFreeModal"><i class="fas fa-copy"></i>&nbsp;<?php w('Copy&nbsp;Funnel'); ?>&nbsp;<?php echo "(" . t('Pro&nbsp;Only') . ")"; ?></button>
							<?php } else { ?>
								<button class="btn btn-primary theme-button" v-on:click="toggleFunnelCloner()"><i class="fas fa-copy"></i>&nbsp;<?php w('Copy&nbsp;Funnel'); ?>&nbsp;<?php if (!$_SESSION['user_plan_type' . $site_token_for_dashboard]) { echo "(" . t('Pro&nbsp;Only') . ")"; } ?></button>
							<?php } ?>
						</div>
						<div class="col-sm-12 d-flex justify-content-center" v-if="funnel_rename_opened">
							<funnel_rename v-bind:toggle="toggleFunnelRename" v-bind:funnel_name="funnel_name"></funnel_rename>
						</div>
						<div class="col-sm-12 d-flex justify-content-center" v-if="funnel_cloner_opened">
							<funnel_cloner v-bind:toggle="toggleFunnelCloner"></funnel_cloner>
						</div>
					</div>
					<div class="row labelscontainer" id="step_2" v-if="(current_step=='step_2')? true : false" v-bind:style="{display:(funnel_cloner_opened || funnel_rename_opened)? 'none':'block'}">
						<div id="templateinstalldiv_container" class="col-sm-12" v-bind:style="{display:(templatecontaineropened)? 'block':'none'}">

						</div>

						<div class="col-sm-12" v-bind:style="{display:(funnel_setting_view||templatecontaineropened)? 'none':'block'}">
							<!-- start funnel webinar -->
							<div v-if="selectedFunnelType('webinar') || selectedFunnelType('membership') || selectedFunnelType('sales') || selectedFunnelType('blank')">
								<div class="input-group mb-3">
									<div class="input-group-prepend"><span class="input-group-text" v-html="t('Project&nbsp;${1}&nbsp;Created At',[`<i>${funnel_name}</i>`])"></span></div>
									<p class="form-control">{{funnel_url}}</p>
									<div class="input-group-append" data-bs-toggle="tooltip" v-bind:title="t('Open URL')"><span class="input-group-text"><a v-bind:href="funnel_url" target="_BLANK"><i class="fas fa-eye"></i></a></span></div>
									<div class="input-group-append" data-bs-toggle='tooltip' v-bind:title="t('Copy Project URL')" style="cursor:pointer;" onclick="copyText('funnel.funnel_url',1)"><span class="input-group-text"><i class="fas fa-copy"></i></span></div>
								</div>

								<div class="row">
									<div class="col-md-3  mx-auto mnubtn-container-parent">

										<div id="lblbtncontainer" class="mnubtn-container">
											<h4 class="card-header card-header-copy p-3 mb-2">{{t('Select Page Type')}}</h4>
											<?php
											$btncount = 0;
											if ($data_arr['pages']) {
												foreach ($data_arr['pages'] as $all_pages) {
													$page_url = $funnel_url . "/" . $all_pages->filename;
													if ($btncount == 0) {
											?>
														<button lbl="<?= $all_pages->level; ?>" category="<?= $all_pages->category; ?>" class="btn activemenubtn btn-outline-secondary btn-block mnubtn" v-on:mousedown="activeMenuBtn($event)" v-on:contextmenu="showContext(true, $event)"><span class=""></span><?= $all_pages->title ?></button>
													<?php
													} else {
													?>
														<button lbl="<?= $all_pages->level; ?>" category="<?= $all_pages->category; ?>" class="btn btn-outline-secondary btn-block mnubtn" v-on:mousedown="activeMenuBtn($event)" v-on:contextmenu="showContext(true, $event)"><span class=""></span><?= $all_pages->title ?></button>
											<?php
													}
													$btncount++;
												}
											} ?>
										</div>
										<qfnl-addnew-lbl v-bind:user_plan_type="<?php echo json_encode($_SESSION['user_plan_type' . $site_token_for_dashboard]); ?>" v-bind:all_pages="<?php if($data_arr['pages']){echo json_encode(count($data_arr['pages']));} else {echo 0;} ?>"></qfnl-addnew-lbl>
									</div>
									<?php echo $funnelsetting; ?>
								</div>
							</div>
							<div class="newlbladder" v-if="close_new_model">
								<div class="row ">
									<div style="top:0px;left:0px;height:100%;width:100%;position:fixed;background-color:#1ab2ff;opacity:0.5"> </div>
									<div class="col-sm-4" style="top:30%;left:40%;position:fixed;z-index:9999;">
										<div class="card pnl ">
											<div class="card-header">
												<div class="row">
													<div class="col">{{t("Add New Page")}}</div>
													<div class="col text-right"><i class="fas fa-times " v-on:click="closeNewModel()" style="cursor:pointer;"></i></div>
												</div>
											</div>
											<div class="card-body">
												<div class="mb-3">
													<label for="">{{t('Page Name')}}</label>
													<input type="text" class="form-control urltoclone" v-bind:placeholder="t('Enter Page Name')" v-model="new_label" />
												</div>
												<div class="mb-3">
													<label for="">{{t('Page Category')}}</label>
													<select class="form-select form-control mt-0" id="category" v-model="new_category">
														<option value="all">{{t('Not Specific')}}</option>
														<option value="optin">{{t('Optin')}}</option>
														<option value="register">{{t('Register')}}</option>
														<option value="login">{{t('Login')}}</option>
														<option value="membership">{{t('Membership')}}</option>
														<option value="forgotpassword">{{t('Forgot Password')}}</option>
														<option value="sales">{{t('Sales')}}</option>
														<option value="orderform">{{t('Order Form')}}</option>
														<option value="oto">{{t('OTO')}}</option>
														<option value="checkout">{{t('Checkout')}}</option>
														<option value="confirm">{{t('Confirmation')}}</option>
														<option value="cancel">{{t('Cancelation Page')}}</option>
														<option value="thankyou">{{t('Thank You Page')}}</option>
														<option value="tandc">{{t('Terms and Conditions')}}</option>
														<option value="privacy_policy">{{t('Privacy Policy')}}</option>
													</select>
												</div>
												<button v-on:click="addNewLabel" class="btn theme-button btn-block mt-2 doclone">{{t("Add")}}</button>
											</div>
										</div>
									</div>
								</div>
								<!-- row where is the select with available subdomains -->
								<div class="row">
									<div class="col-sm-12">
										<div class="card pnl">
											<div class="card-header">
												<div class="row">
													<div class="col">{{t("Select Subdomain")}}</div>
													<div class="col text-right"><i class="fas fa-times " v-on:click="closeNewModel()" style="cursor:pointer;"></i></div>
												</div>
											</div>
											<div class="card-body">
												<div class="mb-3">
													<label for="">{{t('Subdomain')}}</label>
													<select class="form-select form-control mt-0" id="subdomain" v-model="new_CNAME">
														<option value="">{{t('Select Subdomain')}}</option>
														<?php
														$mysqli = $info['mysqli'];
														$dbpref = $info['dbpref'];
														$table = $dbpref . "subdomians";
												        $query = "SELECT * FROM $table";
														$funnel_subdomains = $mysqli->query($query);
														foreach ($funnel_subdomains as $subdomain) {
															echo "<option value='" . $subdomain->id . "'>" . $subdomain->name . "</option>";
														}
														?>
													</select>
												</div>
												<button v-on:click="addNewLabel" class="btn theme-button btn-block mt-2 doclone">{{t("Add")}}</button>
											</div>
										</div>
									</div>

								</div>
							</div>
							<!-- ends funnel -->
						</div>
					</div>
				<?php } ?>
			</div>
		</div>
	</div>
	<div class="screenshot_ifr">
		<iframe class="screenshot_ifr_a" src=""></iframe>
		<iframe class="screenshot_ifr_b" src=""></iframe>
	</div>
	<div>
		<page_context v-bind:show="show_context"></page_context>
		<div v-if="page_position_changing" class="fnl-transparent-cover"></div>
	</div>
</div>