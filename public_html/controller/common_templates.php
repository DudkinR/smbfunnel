<?php
function funnelSettingsPopup($data_arr = array())
{
	$plugins_ob = false;
	if (isset($GLOBALS['plugin_loader'])) {
		$plugins_ob = $GLOBALS['plugin_loader'];
	}
	ob_start();
?>

<div class="col-md-9  mx-auto">
	<div class="container">
	<div class="row">
		<div class="col-md-12">
			<ul id="navbar-funnel" class="nav nav-tabs md-tabs nav-justified theme-nav shadow-1 rounded-top mb-4  d-flex flex-column flex-sm-row" role="tablist">
				<li class="nav-item waves-effect waves-light">
					<a class="nav-link active" data-bs-toggle="tab" v-on:click="closeSettingBtn('first-setting','second-setting')" href="#home1" role="tab">
					<i class="fas fa-table pr-2"></i><?php w("General"); ?></a>
				</li>
				<li class="nav-item waves-effect waves-light">
					<a class="nav-link" data-bs-toggle="tab" v-on:click="closeSettingBtn('second-setting','first-setting')" href="#menu2" role="tab">
					<i class="fas fa-table pr-2"></i><?php w("Page Setting"); ?></a>
				</li>
				<li class="nav-item waves-effect waves-light">
					<a class="nav-link" data-bs-toggle="tab" v-on:click="closeSettingBtn('second-setting','first-setting')"  href="#menu3" role="tab">
					<i class="fas fa-table pr-2"></i><?php w("Advance Setting"); ?></a>
				</li>
				<li class="nav-item waves-effect waves-light">
					<a class="nav-link" data-bs-toggle="tab"  v-on:click="closeSettingBtn('second-setting','first-setting')"  href="#menu4" role="tab">
					<i class="fas fa-table pr-2"></i><?php w("Edit SEO Data"); ?></a>
				</li>
			</ul>
			<div class="tab-content ">
				<div id="home1" class="tab-pane fade show active">
					<div class="row">
						<div class="col-md-6 "> <label for="url">{{t('Page Name')}}:</label>
							<div class="input-group">
								<input type="text" class="form-control" id="quick-url" v-bind:placeholder="t('Path')" v-model="page_foler_name">
								<div class="input-group-append" data-bs-toggle="tooltip" v-bind:title="t('Copy To Clipboard')" onclick=copyText('funnel.funnel_url+"/"+funnel.page_foler_name+"/"',1) style="cursor:pointer;"><span class="input-group-text"> <i class="fas fa-copy"></i></span></div>
								<a class="input-group-append" v-bind:href="funnel_url+'/'+page_foler_name+'/'" target="_BLANK"><span class="input-group-text"><i class="fas fa-eye"></i></span></a>
								<div class="input-group-append" data-bs-toggle="tooltip" v-bind:title="t('Copy Verified Membership Link')" v-if="(tempselected_category=='register')? true:false" style="cursor:pointer;" onclick=copyText('funnel.funnel_url+"/"+funnel.page_foler_name+"@-cf-verified-"+funnel.current_funnel+"-member-@"',1)><span class="input-group-text"><i class="fas fa-user-shield"></i></span></div>
							</div>
							<label data-bs-toggle="tooltip" title="<?php w('Turn it on if you are sure about changing the page slug'); ?>"><input type="checkbox" v-model="force_rename">&nbsp;{{t('Rename page slug too')}}</label><br>

						</div>
						<div class="col-md-6">
							<div class="mb-3">
								<label for="category" class="mt-2 mb-0">{{t('Page Category')}}:</label>
								<select class="form-select mt-0" id="category" v-model="tempselected_category">
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
						</div>
						<div class="col-md-12 text-right">
							<button type="button" class="btn theme-button btntosavepagesetting first-setting" v-on:click="updatecurrentfunnelsetting()" style="color:white;">{{t('Save Settings')}}</button>
							<p  v-bind:style="{color:(funnel_setting_err.indexOf('success')>=0)? 'green':'#ff0066'}">
							<strong>{{t(funnel_setting_err)}}</strong>
							</p>
						</div>
					</div>
					<br>
					<div class="row">
						<div class="col-md-6 mx-auto" id="template_a">
							<template-detail v-bind:fid="current_funnel" name="a"
								v-bind:lbl="template_selector_btn">
							</template-detail>
						</div>

						<div class="col-md-6  mx-auto " id="template_b" v-if="template_has_a_b_test">
							<template-detail v-bind:fid="current_funnel" name="b"
								v-bind:lbl="template_selector_btn">
							</template-detail>
						</div>
					</div>
				</div>
				<div id="menu2" class="tab-pane fade">
					<div class="container">
						<div class="row ">
							<div class="col-md-6">
								
								<!-- select list -->
								<div class="dropdown mb-2">
									<button class="btn   btn-info btn-block dropdown-toggle me-4" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">{{t('Select Lists')}}</button>

									<div class="dropdown-menu pre-scrollable w-100" aria-labelledby="dropdownMenuLink" id="listslist">
										<?php
										$lists = $data_arr['lists'];
										if (is_object($lists)) {
											if ($lists->num_rows) {
												$lists->data_seek(0);
											}
											while ($r = $lists->fetch_object()) {
												echo '<div class="form-check pr-5"><input type="checkbox" value="' . $r->id . '" id="labellist"> <label class="form-check-label" for="labellist">' . $r->title . '</label></div>';
											}
										} else {
											echo "<div class='bg-danger text-white m-3 p-3'>" . t('No Lists Created') . "</div>";
										}
										?>
									</div>
								</div>
								<!--end select list -->

								<div class="dropdown mb-2">
									<button class="btn btn-info btn-block dropdown-toggle me-4" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">{{t('Select Membership Access')}}</button>

									<div class="dropdown-menu pre-scrollable w-100" aria-labelledby="dropdownMenuLink">
										<?php
										$regpages = $data_arr['registrationpages'];
										if (count($regpages) > 0) {
											foreach ($regpages as $regindex => $regvalue) {
												echo '<div class="form-check pr-5"><input type="checkbox" value="' . $regindex . '" v-model="selected_membership_pages"> <label class="pl-3">' . $regvalue . '</label></div>';
											}
										} else {
											echo "<div class='bg-danger text-white m-3 p-3'>" . t("No Registration Page Created For Members") . "</div>";
										}
										?>
									</div>
								</div>
								<!-- End MEmbership access -->
								<!-- Select Autoresponders-->
								<div class="dropdown mb-2">
									<button class="btn btn-info btn-block dropdown-toggle me-4" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">{{t('Select Autoresponders')}}</button>
									<div class="dropdown-menu pre-scrollable w-100" aria-labelledby="dropdownMenuLink" id="autoreslist">
										<?php
										$autorespondercount = 0;
										if ($plugins_ob && count($plugins_ob->autores_callbacks) > 0) {
											foreach ($plugins_ob->autores_callbacks as $plugin_autores_index => $plugin_autores_val) {
												++$autorespondercount;
												echo '<div class="form-check"><input class="me-2" type="checkbox" id="check-responder" value="' . $plugin_autores_index . '"><label class="form-check-label justify-content-center" for="check-responder">' .  $plugin_autores_val['name'] . '</label></div>';
											}
										}
										if ($autorespondercount < 1) {
											echo "<div class='bg-danger text-white m-3 p-3'><strong>" . t('No autoresponders created,<br> If you don\'t have autoresponder app, you can download from the <a v-bind:href="marketPlaceURL" target="_BLANK">marketplace</a> and can start creating.') . "</strong></div>";
										}
										?>
									</div>
								</div>
								<!--End  Select Autoresponders-->
								<div class="dropdown mb-2">
									<button class="btn btn-info btn-block dropdown-toggle me-4" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">{{t('Select Integrations')}}</button>

									<div class="dropdown-menu pre-scrollable w-100" aria-labelledby="dropdownMenuLink">
										<?php
										$integrations_ob = $data_arr['integrations'];
										$integrations = $integrations_ob->getData('all');

										if (!defined('CF_EXTERNAL_SCRIPT_LOADED')) {
											echo "<div class='bg-danger text-white m-3 p-3'><strong>" . t('The External Script app is not installed here, please download it from the <a v-bind:href="integrationsURL" target="_BLANK">marketplace</a> to continue.') . "</strong></div>";
										} else if ($integrations->num_rows < 1) {
											echo "<div class='bg-danger text-white m-3 p-3'>" . t('No Integrations Created') . "</div>";
										} else {
											while ($r = $integrations->fetch_object()) {
												echo '<div class="form-check pr-5"><input type="checkbox" value=' . $r->id . ' v-model="page_settings.snippet_integrations"> <label>' . $r->title . '</label></div>';
											}
										}
										?>
									</div>
								</div>
								<!-- PaymentMtehod -->
								<div class="dropdown mb-2" v-if="(selected_template_category=='orderform')">
									<button class="btn btn-info dropdown-toggle btn-block me-4" data-bs-target="#paymentmethods" data-bs-toggle="collapse">{{t('Select Payment Methods')}}</button>
									<div id="paymentmethods" class="collapse">

										<?php
										$ipnpaymenturl = get_option('install_url');
										$ipnpaymenturl = "'" . $ipnpaymenturl . "/index.php?page=do_payment&execute=1&qfnl_is_ipn=" . get_option('ipn_token');
										$ipnpaymenturl .= "&qfunnel_id='+current_funnel+'&qfolder='+page_foler_name"
										?>

										<?php

										echo '<div class="input-group" style="margin-top:1px;margin-bottom:1px"  ><div class="input-group-prepend"><span class="input-group-text"><input type="radio" value="cod" id="payment_method_cod" v-model="selected_payment_method"></span></div><label class="form-control" for="payment_method_cod">Cash On Delivery</label><div class="input-group-append"><span class="input-group-text" onclick="copyText(\'cod\')" data-bs-toggle="tooltip" v-bind:title="t(\'Copy To Clipboard\')"><i class="fas fa-copy"></i></span></div></div>';

										$got_payment_methods = false;
										if ($plugins_ob) {
											$plugin_saved_payment_methods = $plugins_ob->payment_methods_callbacks;
											if (is_array($plugin_saved_payment_methods)) {
												foreach ($plugin_saved_payment_methods as $plugin_saved_payment_methods_index => $plugin_saved_payment_methods_val) {
													$got_payment_methods = true;
													if (strpos($plugin_saved_payment_methods_val['credentials']['method'], "_ipn") > 0) {
														echo '<div class="input-group" style="margin-top:1px;margin-bottom:1px" onclick="copyText(this.value)" data-bs-toggle="tooltip" v-bind:title="t(\'Copy To Clipboard\')"><div class="input-group-prepend"><span class="input-group-text"><input type="radio" value="' . $plugin_saved_payment_methods_index . '" name="" v-model="selected_payment_method"> ' . $plugin_saved_payment_methods_val['credentials']['title'] . ' (' . t('IPN') . ')</span></div><input type="text" class="form-control" v-bind:value="' . $ipnpaymenturl . '" onclick="copyText(this.value)" data-bs-toggle="tooltip" v-bind:title="t(\'Copy To Clipboard\')"></div>';
													} else {
														echo '<div class="input-group" style="margin-top:1px;margin-bottom:1px" ><div class="input-group-prepend"><span class="input-group-text"><input type="radio" value="' . $plugin_saved_payment_methods_index . '" id="payment_method_' . $plugin_saved_payment_methods_index . '" v-model="selected_payment_method"></span></div><label class="form-control" for="payment_method_' . $plugin_saved_payment_methods_index . '"> ' . $plugin_saved_payment_methods_val['credentials']['title'] . '</label><div class="input-group-append"><span class="input-group-text" onclick="copyText(\'' . $plugin_saved_payment_methods_index . '\')" data-bs-toggle="tooltip" v-bind:title="t(\'Copy To Clipboard\')"><i class="fas fa-copy"></i></span></div></div>';
													}
												}
											}
										}
										if (!$got_payment_methods) {
											echo "<div class='bg-danger text-white m-3 p-3'>" . t('No Payment Methods Created<br>, If you don\'t have payment app, you can download from the <a v-bind:href="marketPlaceURL" target="_BLANK">marketplace</a> and can start creating. ') . "</div>";
										}
										?>
									</div>
								</div>
								<!--End PaymentMtehod -->

								<!-- Select Product -->
								<div class="dropdown mb-2" v-if="(selected_template_category=='orderform')">
									<button class="btn btn-info dropdown-toggle btn-block me-4" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Select Product</button>
									<div class="dropdown-menu pre-scrollable w-100" aria-labelledby="dropdownMenuLink">
										<?php
										$products = $data_arr['products'];
										if (is_object($products)) {
											if ($products->num_rows) {
												$products->data_seek(0);
											}
											while ($r = $products->fetch_object()) {
												echo '<div class="form-check"><input type="radio" value="' . $r->id . '" name="" v-model="selected_product"><label>(#' . $r->productid . ') ' . $r->title . '</label></div>';
											}
										} else {
											echo "<div class='bg-danger text-white m-3 p-3'>" . t('No Products Created') . "</div>";
										}
										?>
									</div>
								</div>
								<!--End Select Product-->
								<div class="mb-3">
									<label class="mb-0 mt-2">{{t('SMTP For The Project')}}</label>
									<select v-model='selected_smtp' class='form-control form-control-sm mt-0 form-select'>
										<option value='0'>{{t('No Mailer')}}</option>
										<option value='phpmailer'>{{t('PHP Mailer')}}</option>
										<?php
										$smtps = $data_arr['smtps'];
										if (is_object($smtps)) {
											if ($smtps->num_rows) {
												$smtps->data_seek(0);
											}
											while ($r = $smtps->fetch_object()) {
												echo "<option value='" . $r->id . "'>" . $r->title . "</option>";
											}
										}
										?>
									</select>
								</div>
							</div>
							<div class="col-md-6">
								
								<div class="custom-control custom-switch mb-2" v-on:click="doInitAbTurnOn()">
									<input type="checkbox" class="custom-control-input " id="customSwitches1" v-model="template_has_a_b_test">
									<label class="custom-control-label" for="customSwitches1">{{t('Use A/B Testing')}}</label>
								</div>
								<div class="mb-3" v-if="template_has_a_b_test">
									<label>Select template type to show</label>
									<select class="form-select" v-model="page_active_type">
										<option value="NA">Random Template</option>
										<option value="a">Template A</option>
										<option value="b">Template B</option>
									</select>
								</div>
								<div class="custom-control custom-switch mb-2" id="gdprhandle">
									<input type="checkbox" class="custom-control-input " id="customSwitch4" v-model="page_settings.cookie_notice">
									<label class="custom-control-label" for="customSwitch4">{{t('Display GDPR Cookie Notice')}}</label>
								</div>
								<div class="custom-control custom-switch mb-2" data-bs-toggle="tooltip" v-bind:title="t('Its not recomended to use the cache mode for Membership pages and Payment Confirmation Pages but you can use with other category like sales pages, optin generation pages etc according your requirement.')">
									<input type="checkbox" class="custom-control-input " id="customSwitchescache" v-model="page_settings.page_cache">
									<label class="custom-control-label" for="customSwitchescache">{{t('Create Page Cache')}}</label>
								</div>
								<div class="custom-control custom-switch mb-2" data-bs-toggle="tooltip" v-bind:title="t('Cache will be turrened on automatically if you want to create AMP Version for the page.')">
									<input type="checkbox" class="custom-control-input " id="customSwitchamp" v-model="page_settings.active_amp" v-on:change="activeCacheForAMP">
									<label class="custom-control-label" for="customSwitchamp">{{t('Create Equivalent AMP Page')}}</label>
								</div>
								<div class="custom-control custom-switch mb-2" data-bs-toggle="tooltip" title="<?php if (!get_option('zapier_auth_id')) {
																												echo t("You can not modify the setting until you create a Zapier authentication token");
																											} ?>">
									<input type="checkbox" class="custom-control-input " id="customSwitchesZapier" v-model="page_settings.zapier_enable" <?php if (!get_option('zapier_auth_id') || !defined("CF_PLUGIN_FOR_ZAPIER_INIT")) {
																																								echo "disabled=true";
																																							} ?>>
									<label class="custom-control-label" for="customSwitchesZapier">{{t('Send Leads to Zapier')}} <?php if (!defined("CF_PLUGIN_FOR_ZAPIER_INIT")) {
																																		echo "<strong><a v-bind:href='getMarketPlaceURL(\"cf_plugin_zapier\")' target='_BLANK' class='text-danger'>(Required Zapier plugin to continue, please click here to get it.)</a></strong>";
																																	} ?></label>
								</div>
								<div class="custom-control custom-switch mb-2" data-bs-toggle="tooltip" title="<?php if (!defined('CF_PLUGIN_FOR_PABBLY_INIT')) {
																												echo t("You can not modify the setting until you enable Pabbly");
																											} ?>">
									<input type="checkbox" class="custom-control-input " id="customSwitchesPabbly" v-model="page_settings.pabbly_enable" <?php if (!defined("CF_PLUGIN_FOR_PABBLY_INIT")) {
																																								echo "disabled=true";
																																							} ?>>
									<label class="custom-control-label" for="customSwitchesPabbly">{{t('Send Leads to Pabbly')}} <?php if (!defined("CF_PLUGIN_FOR_PABBLY_INIT")) {
																																		echo "<strong><a v-bind:href='getMarketPlaceURL(\"cf_plugin_pabbly\")' target='_BLANK' class='text-danger'>(Required Pabbly plugin to continue, please click here to get it.)</a></strong>";
																																	} ?></label>
								</div>
								<div class="custom-control custom-switch" v-if="(selected_template_category=='register')">
									<input type="checkbox" class="custom-control-input " id="customSwitch3" v-model="verifyed_membership_page">
									<label class="custom-control-label" for="customSwitch3">{{t('Verified Registration Page')}}</label>
								</div>


								<div class="" id="redirectioncntrol">
									<div class="custom-control custom-switch">
										<input type="checkbox" class="custom-control-input" id="customSwitch2" v-model="page_settings.redirect_for_post">
										<label class="custom-control-label" for="customSwitch2">{{t('Redirect Instead Of Going To The Next Page')}}</label>

									</div>
									<label style="margin-top:5px;margin-bottom:2px;">{{t('Enter Redirecton URL')}}</label>
									<input type="url" class="form-control form-control-sm" v-bind:placeholder="t('Enter URL')" v-model="page_settings.redirect_for_post_url">
								</div>
							</div>
						</div>
					</div>
				</div>
				<div id="menu3" class="tab-pane fade">
					<div class="container">
						<div class="row">
							<div class="col-md-6 ">
								<div class="mb-3">
									<label for="header">{{t('Header Script')}}:</label>
									<textarea class="form-control" id="quick-header" rows="8" v-bind:placeholder="t('Enter Header Script')" v-model="page_header_scripts"></textarea>
								</div>
								<div class="mb-3">
									<label for="footer">{{t('Footer Script')}}:</label>
									<textarea class="form-control" id="quick-footer" rows="8" v-bind:placeholder="t('Enter Footer Script')" v-model="page_footer_scripts"></textarea>
								</div>
							</div>
							<div class="col-md-6 ">
								<div class="mb-3">
									<label for="footer">{{t('Valid Input Names For The Project')}}:</label>
									<textarea class="form-control" id="quick-footer" rows="8" v-bind:placeholder="t('Enter Input Names. Please Enter One On Each Line')" v-bind:value="common_inputs_for_current_funnel.split(',').join('\n')" v-on:change="addFunnelAndPageValidInput('common_inputs_for_current_funnel',$event)"></textarea>
								</div>
								<div class="mb-3">
									<label for="footer">{{t('Valid Input Names For This Page')}}:</label>
									<textarea class="form-control" id="quick-footer" rows="8" v-bind:placeholder="t('Enter Input Names. Please Enter One On Each Line')" v-bind:value="valid_inputs_pages.split(',').join('\n')" v-on:change="addFunnelAndPageValidInput('valid_inputs_pages',$event)"></textarea>
								</div>

							</div>
						</div>	
					</div>
				</div>
					<!-- end first tab -->
					<!-- start second tab -->
				<div id="menu4" class="tab-pane fade ">
					<!-- seo datas -->
					<div class="row mt-4">
						<div class="col-md-6">
							<div class="mb-3">
								<label for="title">{{t('Page Title')}}:</label>
								<input type="text" class="form-control" id="quick-title" v-bind:placeholder="t('Enter Title')" v-model="page_title">
							</div>

						</div>
						<div class="col-md-6">
							<div class="mb-3">
								<label>{{t('Title Icon')}}</label>
								<input type="text" class="form-control" id="quick-icon" v-bind:placeholder="t('Enter Icon URL')" v-model="page_meta.icon">
							</div>
						</div>


					</div>

					<div class="row">
						<div class="col-md-6">
							<div class="mb-3">
								<label>{{t('Page Description')}}</label>
								<textarea class="form-control" rows="4" v-bind:placeholder="t('Enter Description')" v-model="page_meta.description"></textarea>
							</div>
						</div>
						<div class="col-md-6">
							<div class="mb-3">
								<label>{{t('Keywords')}}</label>
								<textarea class="form-control" rows="4" v-bind:placeholder="t('Add Keywords. Please Enter One On Each Line')" v-bind:value="page_meta.keywords.split(',').join('\n')" v-on:change="addFunnelAndPageValidInput('page_meta.keywords',$event)"></textarea>
							</div>
						</div>
					</div>



					<div class="row">
						<div class="col-md-6">
							<div class="mb-3">
								<label>{{t('Robots Value')}}</label>
								<textarea class="form-control" rows="4" v-bind:placeholder="t('Enter Robots Data')" v-model="page_meta.robots"></textarea>
							</div>
						</div>
						<div class="col-md-6">
							<div class="mb-3">
								<label>{{t('Copyright')}}</label>
								<textarea class="form-control" rows="4" v-bind:placeholder="t('Enter Copyright Description')" v-model="page_meta.copyright"></textarea>
							</div>
						</div>
					</div>
					<div class="mb-3">
						<label for="title">{{t('DC.title')}}</label>
						<textarea class="form-control" v-bind:placeholder="t('Add DC.title')" v-model="page_meta.DC_title"></textarea>
					</div>
				</div>
			</div>
			<!-- <div class="container-fluid">
				<div class="form-inputs">
				
				</div>
			</div> -->
		</div>
		<div class="col-md-12 ms-3">
			<button type="button" class="btn theme-button btntosavepagesetting second-setting" style="visibility:hidden" v-on:click="updatecurrentfunnelsetting()" style="color:white;margin-top:10px;">{{t('Save Settings')}}</button>
			<p style="margin-left:10px;visibility:hidden" class="second-setting" v-bind:style="{color:(funnel_setting_err.indexOf('success')>=0)? 'green':'#ff0066'}">
			<strong>{{t(funnel_setting_err)}}</strong></p>

		</div>
	</div>
	</div>
</div>

	
<?php
	$contents = ob_get_contents();
	ob_end_clean();
	return $contents;
}
?>