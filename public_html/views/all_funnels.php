<?php
$mysqli = $info['mysqli'];
$dbpref = $info['dbpref'];

if (isset($_POST['regeneratefunnelcode'])) {
	$_POST['regeneratefunnelcode'] = $mysqli->real_escape_string($_POST['regeneratefunnelcode']);
	$regeneratecode = substr(str_shuffle('123456789089455515445515155'), 0, 8);
	$mysqli->query("update `" . $dbpref . "quick_funnels` set `token`='" . $regeneratecode . "' where `id`=" . $_POST['regeneratefunnelcode'] . "");
}
?>
<div class="container-fluid">
	<div class="card pb-2  br-rounded">
		<div class="card-body pb-2">
			<div class="row">
				<div class="col-md-2 mb-2">
					<?php echo createSearchBoxBydate(); ?>
				</div>
				<div class="col-md-3">
					<?php echo showRecordCountSelection(); ?>
				</div>
				<div class="col-md-3">
					<?php echo arranger(array('`b`.id' => 'date', 'ab_viewcount' => 'views', 'ab_convertcount' => 'converted visitors', 'ab_bouncecount' => 'bounce')); ?>
				</div>
				<div class="col-md-4">
					<div class="mb-3">
						<div class="input-group input-group-sm">
							<div class="input-group-prepend ">
								<span class="input-group-text"><i class="fas fa-search"></i></span>
							</div>
							<input type="text" class="form-control form-control-sm" placeholder="<?php w('Enter funnel or page name or category'); ?>" onkeyup="searchFunnel(this.value)">
						</div>
					</div>
				</div>
			</div>

			<div class="col-sm-12 nopadding">
				<div class="table-responsive">
					<table class="table table-striped">
						<thead>
							<th>#</th>
							<th><?php w("Project"); ?></th>
							<th><?php w("Type"); ?></th>
							<th><?php w("Pages"); ?></th>
							<th><?php w("Visits") ?></th>
							<th><?php w("Conversion"); ?></th>
							<th><?php w("Bounces"); ?></th>
							<th><?php w("Created&nbsp;On") ?></th>
							<th><?php w("Action"); ?></th>
						</thead>
						<tbody id="keywordsearchresult">
							<!-- keyword search -->
							<?php
							$hashcount = 0;
							$funnel = $data_arr['funnel'];
							
							if (isset($_POST['delfunnel'])) {
								$funnel->deleteFunnel($_POST['delfunnel']);
							}

							if (isset($_GET['hash_count'])) {
								$hashcount = $_GET['hash_count'];
							}
							if (isset($_GET['page_count'])) {
								$datas = $funnel->getAllFunnelForView($_GET['page_count']);
							} else {
								$datas = $funnel->getAllFunnelForView();
							}

							if ($datas['rows']) {

								$hashcount = 0;

								if (isset($_GET['page_count'])) {
									$hashcount = ($_GET['page_count'] * get_option('qfnl_max_records_per_page')) - get_option('qfnl_max_records_per_page');
								}
								$count = 0;
								$lastid = 0;
								

								if ($datas['rows']) {
									$lastid = $datas['rows']->num_rows;
									while ($r = $datas['rows']->fetch_object()) {
										$count_used_in = 0;
										$count_used_type = "";
										if ($r->funnel_type == "membership") {
											$count_used_in = $funnel->countIntegratedInOthers($r->id);
											$count_used_type = "membership funnel";
										}
										++$hashcount;
										++$count;

										$optinspages = '<a data-bs-toggle="popover" title="' . t('Select Page') . '" data-bs-html="true" data-bs-content="';
										$pagecheckquery = $mysqli->query("select distinct(`filename`) from `" . $dbpref . "quick_pagefunnel` where `funnelid`='" . $r->funnel_id . "'");

										if ($pagecheckquery->num_rows > 0) {
											while ($rfilename = $pagecheckquery->fetch_object())
												$optinspages .= "<a class='dropdown-item' href='index.php?page=optins&funnelid=page@" . $r->funnel_id . "@" . $rfilename->filename . "'>" . $rfilename->filename . "</a>";
										}

										$optinspages .= '"><button class="btn unstyled-button" data-bs-toggle="tooltip" title="' . t('View Optins') . '" ><i class="fas fa-users text-success"></i></button></a>';

										$share_token_code = $funnel->createFunnelToken($r->funnel_id, $r->funnel_token);
										if ($_SESSION['user_plan_type' . $site_token_for_dashboard] == 2) {
											$share_button = '<button class="unstyled-button" data-bs-toggle="popover" data-bs-trigger="click" title="' . t('Take Action') . '" data-bs-toggle="modal" data-bs-target="#upgradeFromFreeModal"';
										} else {
											$share_button = '<button class="unstyled-button" data-bs-toggle="popover" data-bs-trigger="click" title="' . t('Take Action') . '" data-bs-html="true" data-bs-content="';
											$share_button .= "<button class='dropdown-item btn-block btn-primary' onclick=copyText(`" . $share_token_code . "`)><i class='fas fa-copy'></i>&nbsp;" . t('Copy&nbsp;Code') . "</button> <form action='' method='post'><input type='hidden' value='" . $r->funnel_id . "' name='regeneratefunnelcode'><button class='dropdown-item btn-block btn-info' type='submit'><i class='fas fa-redo'></i>&nbsp;" . t('Re-Generate&nbspCode') . "</button></form>";
										}

										$share_button .= '"><i class="fas fa-code-branch text-primary"></i></button>';

										//$sumdata=$funnel->totalSumCountsFunelPages($r->funnel_id);
										$action = "<table class='actionedittable'><tr><td><a href='index.php?page=create_funnel&id=" . $r->funnel_id . "'><button class='btn unstyled-button' data-bs-toggle='tooltip' title='" . t('Edit Funnel') . "'><i class='fas fa-edit text-primary'></i></button></a></td><td>" . $optinspages . "</td><td><form action='' method='post' onsubmit=\"return confirmDeletion(this,event," . $count_used_in . ",'" . $count_used_type . "')\"><input type='hidden' name='delfunnel' value='" . $r->funnel_id . "'><button type='submit' class='btn unstyled-button' data-bs-toggle='tooltip' title='" . t('Delete Funnel') . "'><i class='fas fa-trash text-danger'></i></button></form></td><td>" . $share_button . "</td></tr></table>";

										$bounces = $r->ab_viewcount - $r->ab_convertcount;

										$converted_percentage = 0;
										$bounces_percentage = 0;
										if ($bounces < 0) {
											$bounces = 0;
										}
										if ($r->ab_viewcount > 0) {
											$converted_percentage = number_format((($r->ab_convertcount / $r->ab_viewcount) * 100), 2);

											$bounces_percentage = number_format((($bounces / $r->ab_viewcount) * 100), 2);
										}

										$display_funnel_type = $r->funnel_type;
										if ($display_funnel_type == "blank") {
											$display_funnel_type = "Custom";
										}
										$display_funnel_type = ucfirst($display_funnel_type);
										if ($_SESSION['user_plan_type' . $site_token_for_dashboard] == 2 && $hashcount >= 2) {
											break;
										}
										echo "<tr><td>" . t($hashcount) . "</td><td><a href='" . $r->funnel_baseurl . "' target='_BLANK'>" . $r->funnel_name . " &nbsp;<i class='fas fa-external-link-alt'></i></a></td><td>" . $display_funnel_type . "</td><td>" . t(number_format(!empty($r->sumpages) ? $r->sumpages : 0)) . "</td><td>" . t(number_format(!empty($r->ab_viewcount) ? $r->ab_viewcount : 0)) . "</td><td>" . t(number_format(!empty($r->ab_convertcount) ? $r->ab_convertcount : 0)) . " <span class='percentage'>" . t($converted_percentage) . "%</span></td><td>" . t(number_format(!empty($bounces) ? $bounces : 0)) . " <span class='percentage'>" . t($bounces_percentage) . "%</span></td><td>" . date('d-M-Y', $r->funnelcreatedon) . "</td><td>" . $action . "</td></tr>";
									}
								}
							}
							?>
							<tr>
								<td colspan=10 class="total-data">
									<center><?php w("Total Funnels"); ?>: <?php echo ($_SESSION['user_plan_type' . $site_token_for_dashboard]==2 && $datas['total_rows'] != 0) ? 1 : w(number_format($datas['total_rows'])); ?></center>
								</td>
							</tr>
							<!-- /keyword search -->
						</tbody>
					</table>

				</div>
				<div class="col-md-12 row nopadding">
					<div class="col-sm-6 mr-auto mt-2">
						<?php
						$paging_url = "index.php?page=all_funnels&page_count";
						$pagecount = 0;
						if (isset($_GET['page_count'])) {
							$pagecount = $_GET['page_count'];
						}
						echo createPager($datas['total_rows'], $paging_url, $pagecount);
						?>
					</div>
					<div class="col-sm-6 mt-2 text-right">
						<?php if ($_SESSION['user_plan_type' . $site_token_for_dashboard] == 2 && $datas['total_rows'] >= 1) { ?>
							<a data-bs-toggle="modal" data-bs-target="#upgradeFromFreeModal"><button class="btn theme-button"><i class="fas fa-pencil-alt"></i> <?php w("Create New"); ?></button></a>
						<?php } else { ?>
							<a href="index.php?page=create_funnel"><button class="btn theme-button"><i class="fas fa-pencil-alt"></i> <?php w("Create New"); ?></button></a>
						<?php } ?>
					</div>
				</div>

			</div>


		</div>
	</div>
</div>

<script>
	$(document).ready(function() {
		$('[data-bs-toggle="popover"]').popover();
	});

	function searchFunnel(search) {
		var ob = new OnPageSearch(search, "#keywordsearchresult");
		ob.url = "<?php echo getProtocol(); ?>";
		ob.url += "<?php echo $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']; ?>";
		ob.search();
	}
	authPurchaseData();

	var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'))
	var popoverList = popoverTriggerList.map(function(popoverTriggerEl) {
		return new bootstrap.Popover(popoverTriggerEl, {
			html: true,
			sanitize: false,
			content: function() {
				return this.getAttribute('data-bs-content');
			}
		});
	});
</script>