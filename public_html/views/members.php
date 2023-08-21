<?php
$optins_ob = $data_arr['optinob'];
$funnel = $data_arr['funnel'];
if (isset($_POST['deletemember'])) {
	$optins_ob->deleteMemberByFunnel(base64_decode($_POST['deletemember']), $_GET['funnelid']);
}
?>
<div class="container-fluid">
	<div class="card pb-2  br-rounded" id="hidecard1">
		<div class="card-body pb-2" id="hidecard2">
			<div class="row">
				<div class="col-md-2 mb-2">
					<?php echo createSearchBoxBydate(); ?>
				</div>
				<div class="col-md-3">
					<?php echo showRecordCountSelection(); ?>
				</div>
				<div class="col-md-3">
					<?php echo arranger(array('id' => 'Date')); ?>
				</div>
				<div class="mb-3 col-md-4">
					<div class="input-group input-group-sm mb-3   float-right">
						<div class="input-group-prepend ">
							<span class="input-group-text"><i class="fas fa-search"></i></span>
						</div>
						<input type="text" class="form-control form-control-sm" placeholder="<?php w('Enter name, email, IP or extra fields'); ?>" onkeyup="searchMember(this.value)">
					</div>
				</div>
			</div>

			<div class="row membercontainer">
				<div class="col-sm-12">
					<?php
					$countpage = 0;
					if (isset($_GET['page_count'])) {
						$countpage = $_GET['page_count'];
					}

					if (isset($_GET['funnelid'])) {
						$optin_datas = $optins_ob->visualOptisForFunnels($_GET['funnelid'], $countpage);
					} else {
						$optin_datas = $optins_ob->visualOptisForFunnels('all', $countpage);
					}

					//echo $optin_datas['extracols'];
					?>

					<div class="table-responsive">
						<table class="table table-striped">
							<?php
							//optins for specific funnel
							if (isset($_GET['funnelid'])) {
							?>
								<thead>
									<th>#</th>
									<th><?php w('Name'); ?></th>
									<th><?php w('Email') ?></th>

									<th><?php w('Last&nbsp;IP'); ?></th>
									<th><?php w('Last&nbsp;Loggedin'); ?></th>
									<th><?php w('Action'); ?></th>
								</thead>
								<tbody id="srchmember">
									<!--srch-->
									<?php
									$hashcount = 0;

									if (isset($_GET['page_count'])) {
										$hashcount = ($_GET['page_count'] * get_option('qfnl_max_records_per_page')) - get_option('qfnl_max_records_per_page');
									}
									$count = 0;
									$lastid = 0;

									$taken_emails = array();

									if ($optin_datas['leads']) {
										$lastid = $optin_datas['leads']->num_rows;
										while ($r = $optin_datas['leads']->fetch_object()) {
											$last_sign =  $r->date_lastsignin == 'N/A' ? 'N/A' : date('d-M-Y', (int)$r->date_lastsignin);

											++$hashcount;
											++$count;
											$action = "<table class='actionedittable'><tr><td><button class='btn unstyled-button' data-bs-toggle='tooltip' title='" . t('View Data') . "' onclick=viewMemberData(" . $r->id . ",'" . base64_encode($r->name) . "','" . $r->email . "','" . base64_encode($r->exf) . "','" . base64_encode(date('d-M-Y', $r->date_created)) . "','" . base64_encode($r->ip_created) . "','" . base64_encode($last_sign) . "')><i class='fas fa-eye text-primary'></i></button><button class='btn unstyled-button' data-bs-toggle='tooltip' title='" . t('Edit Data') . "' onclick=editMemberData(" . $r->id . ",'" . base64_encode($r->name) . "','" . $r->email . "','" . base64_encode($r->exf) . "','" . base64_encode(date('d-M-Y', $r->date_created)) . "','" . base64_encode($r->ip_created) . "')><i class='fas fa-edit text-primary'></i></button></td><td><form action='' method='post' onsubmit=\"return confirmDeletion(this,event,'0','members')\"><input type='hidden' name='deletemember' value='" . base64_encode($r->email) . "'><button type='submit' class='btn unstyled-button' data-bs-toggle='tooltip' title='" . t('Cancel Membership/Delete Record') . "'><i class='fas fa-trash text-danger'></i></button></form></td></tr></table>";

											$pagefunnel_data = $funnel->getPageFunnel($r->pageid, '', '', 'id');

											if (!$pagefunnel_data) {
												continue;
											}

											echo "<tr><td>" . t($hashcount) . "</td><td>" . $r->name . "</td><td>" . $r->email . "</td>";


											$last_signindadate = $r->date_lastsignin;
											if (is_numeric($last_signindadate)) {
												$last_signindadate = date('d-M-Y', $r->date_lastsignin);
											} else {
												$last_signindadate = "N/A";
											}

											echo "<td>" . (($r->ip_lastsignin != 'N/A') ? $r->ip_lastsignin : $r->ip_created) . "</td><td>" . $last_signindadate . "</td><td>" . $action . "</td></tr>";
											if($_SESSION['user_plan_type' . $site_token_for_dashboard]==2 && $hashcount >= 1) {
												break;
											}
										}
									}
									?>
									<tr>
										<td colspan=10 class="total-data"><?php w('Total Members') ?> <?php echo ($_SESSION['user_plan_type' . $site_token_for_dashboard] == 2 && $optin_datas['total'] >= 1) ? 1 : $optin_datas['total']; ?></td>
									</tr>
									<!--/srch-->
								</tbody>
							<?php
							}
							//ends here for specific
							?>
						</table>
					</div>

					<div class="col-md-12 row nopadding">
						<div class="col-sm-6 mt-2">
							<?php
							$paging_url = $_SERVER['REQUEST_URI'] . "&page_count";
							$pagecount = 0;
							if (isset($_GET['page_count'])) {
								$pagecount = $_GET['page_count'];
							}
							echo createPager($optin_datas['total'], $paging_url, $pagecount, $lastid);
							?>
						</div>
						<div class="col-sm-6 mt-2 text-right">
							<form action="index.php?page=export_csv" method="post">
								<?php if($_SESSION['user_plan_type' . $site_token_for_dashboard]==2) { ?>
								<button type="button" class="btn theme-button" data-bs-toggle="modal" data-bs-target="#upgradeFromFreeModal" value="<?php if (isset($_GET['funnelid'])) { echo $_GET['funnelid']; } ?>">
									<i class="fas fa-file-download"></i> <?php w('Export To CSV'); ?>
								</button>
								<?php } else { ?>
								<button type="submit" class="btn theme-button" name="membersto_csv" value="<?php if (isset($_GET['funnelid'])) { echo $_GET['funnelid']; } ?>">
									<i class="fas fa-file-download"></i> <?php w('Export To CSV'); ?>
								</button>
								<?php } ?>
							</form>
						</div>
					</div>

				</div>
			</div>
		</div>
	</div>
</div>
<script>
	function searchMember(search) {
		var request = new ajaxRequest();
		request.postRequestCb("index.php?page=members&funnelid=<?php echo $_GET['funnelid']; ?>", {
			"searchmember": search
		}, function(data) {
			var str = "<!--srch-->";
			var first = data.indexOf(str) + str.length;
			var last = data.indexOf("<!--/srch-->");
			document.getElementById("srchmember").innerHTML = data.substr(first, last - first);
		})
	}

	function editMemberData(id, name, email, exf, addedon, regip) {
		var bdy = document.getElementsByClassName("membercontainer")[0];
		var div = document.createElement("div");
		var style = "top:50%;left:55%;transform:translate(-50%,-50%);position:fixed;z-index:99999999;width:100%;max-width:800px;";
		var head = "<div class='col-sm-8 col-offset-4' style='" + style + "'><div class='card pnl visual-pnl'><div class='card-header' style='font-size:16px !important;position:relative;'>" + t('Edit User') + "<strong><i id='deltemplateselectiondiv' class='fa fa-times-circle' style='font-size:20px;color:white;right:10px;top:8px;position:absolute;cursor:pointer;'></i></strong></div><div class='card-body tmpltbdydiv' style='max-height:800px;overflow-y:auto;'>"
		var footer = "</div><div class='card-footer'><button id='updateuserdetail' class='btn theme-button'><strong>" + t('Save') + "</strong></button> <strong><span style='margin-left:10px;color:' id='usrdatasaveerr'></span></strong> </div></div></div>";
		var body = "<div class='card-body upusrbdy' style='max-height:180px;overflow-y:auto;'><div class='mb-3'>";
		body += "<div class='input-group'><div class='input-group-prepend'><span class='input-group-text'>" + t('Name') + "</span></div><input type='text' id='name' placeholder='" + t('Enter Name') + "' value='" + atob(name) + "' class='form-control'></div>";

		body += "<div class='input-group'><div class='input-group-prepend'><span class='input-group-text'>" + t('Email') + "</span></div><input type='text' id='email' placeholder='" + t('Enter Email') + "' value='" + email + "' class='form-control'></div>";

		body += "<div class='input-group'><div class='input-group-prepend'><span class='input-group-text'>" + t('Password') + "</span></div><input type='password' id='password' placeholder='" + t('Enter Password') + "' class='form-control'></div>";

		body += "<div class='input-group'><div class='input-group-prepend'><span class='input-group-text'>" + t('Re-Enter Password') + "</span></div><input type='password' id='reenterpassword' placeholder='" + t('Enter Password') + "' class='form-control'></div>";

		body += "<div class='input-group'><div class='input-group-prepend'><span class='input-group-text'>" + t('Ragistration Date') + "</span></div><p class='form-control'>" + atob(addedon) + "</p></div>";

		body += "<div class='input-group'><div class='input-group-prepend'><span class='input-group-text'>" + t('Registration IP') + "</span></div><p class='form-control'>" + atob(regip) + "</p></div>";

		var extras = "<div class='alert alert-warning'>" + t('No Extrafield Available') + "</div>";
		var exf = atob(exf);
		try {
			exf = exf.trim();
		} catch (err) {}
		if (exf.length > 2) {
			exf = exf.replace(/&quot;/g, '"');
			exf = exf.replace(/(?:\r\n|\r|\n)/g, '');
			exf = JSON.parse(exf);
			extras = "";
			for (i in exf) {
				extras += "<div class='input-group'><div class='input-group-prepend'><span class='input-group-text'>" + i + "</span></div><input type='text' id='" + i + "' value='" + exf[i] + "' class='form-control'></div>";
			}
		}

		body += "<button data-bs-toggle='collapse' data-bs-target='.exfdatadiv' class='btn btn-outline-info  btn-block' style='margin-top:5px;'>" + t('Edit Extra Fields') + "</button><div class='collapse exfdatadiv'>" + extras + "</div>";

		body += "</div></div><style>.upusrbdy .input-group{margin-top:2px;margin-bottom:2px;}</style>";
		div.innerHTML = head + body + footer;

		bdy.appendChild(div);
		doEscapePopup(function() {
			bdy.removeChild(div);
		});
		document.getElementById("deltemplateselectiondiv").onclick = function() {
			bdy.removeChild(div);
		}
		document.getElementById("updateuserdetail").onclick = function(e) {
			var request = new ajaxRequest();
			var userdatas = document.querySelectorAll('.upusrbdy')[0].getElementsByTagName("input");
			var senddata = {};
			senddata_ob = {
				"updatememberdata": 1,
				"funnelid": <?php echo $_GET['funnelid']; ?>,
				"userid": id
			};
			for (var i = 0; i < userdatas.length; i++) {
				try {
					var field = userdatas[i].getAttribute("id");
					var val = userdatas[i].value;
					senddata_ob[field] = val;
				} catch (err) {
					console.log(err);
				}
			}

			e.target.disabled = true;
			var errdiv = document.getElementById("usrdatasaveerr");
			errdiv.innerHTML = "";
			request.postRequestCb('req.php', senddata_ob, function(data) {
				e.target.disabled = false;
				if (data.trim() != '1') {
					errdiv.innerHTML = "<span style='color:#800033;'>" + t(data.trim()) + "</span>";
				} else {
					errdiv.innerHTML = "<font style='color:green;'>" + t('Saved Successfully') + "</font>"
				}
			});
		}
	}

	function viewMemberData(id, name, email, exf, addedon, regip, date_lastsignin) {
		var bdy = document.getElementsByClassName("membercontainer")[0];
		var div = document.createElement("div");
		var style = "top:50%;left:55%;transform:translate(-50%,-50%);position:fixed;z-index:99999999;width:100%;max-width:1050px;";
		var head = "<div class='col-sm-8 col-offset-4 view_data' style='" + style + "'><div class='card pnl visual-pnl'><div class='card-header' style='font-size:16px !important;position:relative;'>" + t('Member Details') + "<strong><i id='deltemplateselectiondiv' class='fa fa-times-circle' style='font-size:20px;color:white;right:10px;top:8px;position:absolute;cursor:pointer;'></i></strong></div><div class='card-body tmpltbdydiv' style='max-height:800px;overflow-y:auto;'>"

		var body = "<div class='card-body upusrbdy' style='max-height:540px;overflow-y:auto;'><div class='mb-3'>";

		body += "<div class='row'><div class='col-4'>Name :</div><div class='col-6'><lable>" + atob(name) + "</lable></div></div>";
		body += "<hr><div class='row'><div class='col-4'>Email :</div><div class='col-6'><lable>" + email + "</lable></div></div>";

		var exf = atob(exf);
		try {
			exf = exf.trim();
		} catch (err) {}
		if (exf.length > 2) {
			exf = exf.replace(/&quot;/g, '"');
			exf = exf.replace(/(?:\r\n|\r|\n)/g, '');
			exf = JSON.parse(exf);
			if (exf.firstname != undefined) {
				body += "<hr><div class='row'><div class='col-4'>Firstname :</div><div class='col-6'><lable>" + exf.firstname + "</lable></div></div>";
			}
			if (exf.lastname != undefined) {
				body += "<hr><div class='row'><div class='col-4'>Lastname :</div><div class='col-6'><lable>" + exf.lastname + "</lable></div></div>";
			}
			if (exf.phone != undefined) {
				body += "<hr><div class='row'><div class='col-4'>Phone :</div><div class='col-6'><lable>" + exf.phone + "</lable></div></div>";
			}
			if (exf.remember_user != undefined) {
				body += "<hr><div class='row'><div class='col-4'>Remember_user :</div><div class='col-6'><lable>" + exf.remember_user + "</lable></div></div>";
			}
		}
		body += "<hr><div class='row'><div class='col-4'>Last IP	:</div><div class='col-6'><lable>" + atob(regip) + "</lable></div></div>";
		body += "<hr><div class='row'><div class='col-4'>Last Loggedin :</div><div class='col-6'><lable>" + atob(date_lastsignin) + "</lable></div></div>";
		body += "<hr><div class='row'><div class='col-4'>Ragistration Date :</div><div class='col-6'><lable>" + atob(addedon) + "</lable></div></div>";



		body += "</div></div><style>.upusrbdy .input-group{margin-top:2px;margin-bottom:2px;}</style>";
		div.innerHTML = head + body;

		bdy.appendChild(div);		
		
		doEscapePopup(function() {
			bdy.removeChild(div);
		});
		document.getElementById("deltemplateselectiondiv").onclick = function() {
			bdy.removeChild(div);						
		}
	}
</script>