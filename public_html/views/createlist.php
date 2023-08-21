<?php
$mysqli = $info['mysqli'];
$pref = $info['dbpref'];
if (isset($_GET['listid'])) {
	$listid = $_GET['listid'];
	$select = "select * from `" . $pref . "quick_list_records` where id=" . $_GET['listid'];
	$selres = $mysqli->query($select);
	$fetcharr = $selres->fetch_assoc();
	// print_r($fetcharr);
	$listid = $fetcharr['id'];
	$listtitle = $fetcharr['title'];
} else {
	$listid = "";
	$listtitle = "";
}
if (isset($_POST['listdeleterec'])) {
	$id = $_POST['listdeleterec'];
	$delete = "delete from `" . $pref . "quick_email_lists` where id=" . $id;
	$mysqli->query($delete);
}
$all_total_leads = $data_arr['total_leads'];
$all_total_lists = $data_arr['total_lists'];
?>

<script type="text/javascript">
	function updateNameForCsv(str) {
		document.getElementById("click-list-name").value = str;
	}
</script>

<div class="container" id="createlist">
	<br>
	<?php
	if (!isset($_GET['listid'])) {
	?>
		<div class="row">
			<div class="col-sm-4"></div>
			<div class="col-sm-4 col-sm-offset-4">

				<div class="mb-3">
					<input type="hidden" name="" value="1">
					<label><?php w("Choose a list name") ?> :</label>
					<input type="text" v-model="quick_list_name" class="form-control" required v-bind:placeholder="t('Enter List Name')">
				</div>
				<span style="color:#FF1493;font-size:14px;font-weight: bold;" v-html="err"></span>
				<?php if ($_SESSION['user_plan_type' . $site_token_for_dashboard] == 2 && $all_total_lists >= 1) { ?>
					<button type="button" data-bs-toggle="modal" data-bs-target="#upgradeFromFreeModal" class="btn theme-button btn-block">{{t("Create New List")}}</button>
				<?php } else { ?>
					<button type="button" class="btn theme-button btn-block" v-on:click="createlists">{{t("Create New List")}}</button>
				<?php } ?>
			</div>
			<div class="col-sm-4"></div>
		</div>

	<?php } else if (is_numeric($_GET['listid'])) {

		$hashcount = 0;
		$total_leads = 0;
		$_GET['listid'] = $mysqli->real_escape_string($_GET['listid']);

		$totalqry = $mysqli->query("select count(`id`) as `countid` from `" . $pref . "quick_email_lists` where `listid`='" . $_GET['listid'] . "'");

		$total_qryob = $totalqry->fetch_object();
		$total_leads = $total_qryob->countid;

		$startfrom = 0;
		if (isset($_GET['page_count'])) {
			$startfrom = ($_GET['page_count'] * 10) - 10;
		}
		$hashcount = $startfrom;

		$query = "SELECT * FROM `" . $pref . "quick_email_lists` where listid=" . $_GET['listid'] . " order by id DESC LIMIT " . $startfrom . ",10";

		$count = 0;
		$lastid = 0;


		$result = $mysqli->query($query);

	?>
		<div class="mb-3" id="editorform">
			<div class="col-md-12">
				<div class="row">
					<div class="col-md-6">
						<input type="hidden" id="click-listid" value="<?php echo $listid;  ?>">
						<label>{{t("List Name")}}</label>
						<input type="text" name="click-list-name" id="click-list-name" class="form-control" value="<?php echo $listtitle; ?>" required="" oninput="updateNameForCsv(this.value)">
						<label>{{t("Email")}}</label>
						<div class="inpgrplist">
							<input type="text" class="form-control" id="click_subs_email" v-bind:placeholder="t('Enter Email')">
						</div>
					</div>

					<div class="col-md-6">

						<label>{{t("Subscriber Name")}}</label>
						<div class="inpgrplist">
							<input type="text" class="form-control" id="click_subs_name" v-bind:placeholder="t('Enter Name')">
						</div>
						<label>{{t("Import From csv")}}</label>
						<form action="" method="post" enctype="multipart/form-data">
							<input type="hidden" name="wpqmlr-list-name-csv" id="wpqmlr-list-id-csv" value="">
							<?php if ($_SESSION['user_plan_type' . $site_token_for_dashboard] == 2 && $all_total_leads >= 50) { ?>
								<div class="input-group" data-bs-toggle="modal" data-bs-target="#upgradeFromFreeModal" style="cursor:pointer">
									<div class="input-group-prepend">
										<span class="input-group-text">{{t("Choose File")}}</span>
									</div>
									<p class="form-control" id="uploadedcsv">{{t("No File Chosen")}}</p>
									<input type="file" name="wpqmlr-exportcsv" id="wpqmlr-exportcsv" class="form-control" accept=".csv" required="" style="display:none;">
								</div>
							<?php } else { ?>
								<div class="input-group" onclick="wqmlrUploadCsv('open')" style="cursor:pointer">
									<div class="input-group-prepend">
										<span class="input-group-text">{{t("Choose File")}}</span>
									</div>
									<p class="form-control" id="uploadedcsv">{{t("No File Chosen")}}</p>
									<input type="file" name="wpqmlr-exportcsv" id="wpqmlr-exportcsv" class="form-control" accept=".csv" required="" style="display:none;" onchange="wqmlrUploadCsv('set')">
								</div>
							<?php } ?>
						</form>
						<div class="inpgrplistbtn float-right">
							<!--pagecount,hashcount and update subscriber hiddeen input-->
							<input type="hidden" id="updaterecord" value="0">
							<input type="hidden" id="clickpagecount" value="<?php if (isset($_GET['pagecount'])) {
																				echo $_GET['pagecount'];
																			} else {
																				echo 0;
																			} ?>">

							<br>
							<span class="text-danger" v-html="err"></span>
							<?php if ($_SESSION['user_plan_type' . $site_token_for_dashboard] == 2 && $all_total_leads >= 50) { ?>
								<button type="button" id="savebuttn" data-bs-toggle="modal" data-bs-target="#upgradeFromFreeModal" class="btn theme-button btn-block">&nbsp;&nbsp;&nbsp;<i class="fas fa-plus"></i> {{t("Add")}}&nbsp;&nbsp;&nbsp;</button>
							<?php } else { ?>
								<button type="button" id="savebuttn" class="btn theme-button btn-block" v-on:click="savesubs">&nbsp;&nbsp;&nbsp;<i class="fas fa-plus"></i> {{t("Add")}}&nbsp;&nbsp;&nbsp;</button>
							<?php } ?>

						</div>
					</div>

					<div class="col-md-12 bg-white shadow rounded pt-3 mt-4">
						<div class="col-md-12 row nopadding">
							<div class="col-5 mt-3 me-auto">
								<h3 class="theme-text mx-auto">{{t("All Subscribers")}}</h3>
							</div>
							<input type="hidden" id="searchlistid" value="<?php echo $_GET['listid']; ?>">
							<div class="col-5 mt-3 ">
								<div class="input-group input-group-sm mb-3 nopadding  float-right">
									<div class="input-group-prepend ">
										<span class="input-group-text"><i class="fas fa-search"></i></span>
									</div>
									<input type="text" class="form-control form-control-sm" id="searchpeople" v-bind:placeholder="t('Search With Name Or Email Id')" v-on:keyup="searchemaillist">
								</div>
							</div>
						</div>


						<div class="table-responsive small-table-data pt-3">
							<table class="table table-striped">
								<thead>
									<tr>
										<th>#</th>
										<th>{{t("Name")}}</th>
										<th>{{t("Email")}}</th>
										<th>{{t("IP")}}</th>
										<th>{{t("Custom Data")}}</th>
										<th>{{t("Added On")}}</th>
										<th>{{t("Action")}}</th>
									</tr>
								</thead>
								<tbody id="loadinglistqml">
									<?php
									while ($row = $result->fetch_assoc()) {
										++$hashcount;

										$disabled = "";
										if (strlen($row['exf']) < 1) {
											$disabled = "disabled";
										}

										$action = "<button style='margin-right:10px;' class='btn unstyled-button' data-bs-toggle='tooltip' title='" . t('Edit') . "' onclick='editRecord(" . $row['id'] . ")'><i class='text-primary fas fa-edit'></i></button><form action='' method='post' style='display:inline;'><button class='btn unstyled-button' name='listdeleterec' value=" . $row['id'] . " data-bs-toggle='tooltip' title='" . t('Delete Record') . "'><i class='text-danger fas fa-trash'></i></span></button></form>";

										echo "<tr><td>" . t($hashcount) . "</td><td id='listusername" . $row['id'] . "'>" . $row['name'] . "</td><td id='listuseremail" . $row['id'] . "'>" . $row['email'] . "</td><td>" . $row['ipaddr'] . "</td><td class='text-center'><button class='btn unstyled' " . $disabled . "  onclick=viewExfData(" . $row['id'] . ")><i class='text-secondary text-center fas fa-eye'  data-bs-toggle='tooltip' title='View Record'></i></button></td><td>" . date('d-M-Y h:ia', $row['date']) . "</td><td class='text-right'>" . $action . "</td></tr>";
									}
									?>
								</tbody>
							</table>
						</div>
						<div class="col-md-12 row border-top nopadding">
							<div class="col-6 pl-0 mt-2">
								<?php
								$paging_url = "index.php?page=createlist&listid=" . $_GET['listid'] . "&page_count";
								$pagecount = 0;
								if (isset($_GET['page_count'])) {
									$pagecount = $_GET['page_count'];
								}
								echo createPager($total_leads, $paging_url, $pagecount, 0);
								?>
							</div>
							<div class="col-6 text-right mt-2">
								<span class="total-data"><?php w("Total Leads"); ?>: <?php echo t($total_leads); ?> </span>
							</div>
						<?php  } ?>
						</div>
					</div>
				</div>


			</div>
		</div>
</div>




<script type="text/javascript">
	function editRecord(str) {
		var username = document.getElementById('listusername' + str).innerHTML;
		var email = document.getElementById('listuseremail' + str).innerHTML;
		document.getElementById('click_subs_name').value = username;
		document.getElementById('click_subs_email').value = email;
		document.getElementById("savebuttn").innerHTML = "Update";
		document.getElementById('updaterecord').value = str;
		window.scrollTo(0, 0);
	}
	var globdiv;

	function viewExfData(id) {
		var request = new ajaxRequest();
		var div = document.createElement("div");
		div.setAttribute('class', 'row');
		var container = "<div class='overlay'><div class='api-forms  col-sm-6'><div class='card pnl visual-pnl'><div class='card-header'>" + t('Detail') + " <i class='fas fa-times closethidiv' style='color:white;right:20px;top:20px;position:absolute;cursor:pointer;'></i></div><div class='card-body exfdataqfnl' style='max-height:400px;overflow-y:auto;'>" + t("Loading...") + "</div></div></div></div>";
		div.innerHTML = container;
		//alert(div);
		var maindiv = document.getElementById("createlist");
		try {
			maindiv.removeChild(globdiv);
		} catch (err) {
			console.log(err.message);
		}
		globdiv = div;
		maindiv.appendChild(div);
		document.getElementsByClassName("closethidiv")[0].onclick = function() {
			maindiv.removeChild(div);
		};
		request.postRequestCb('req.php', {
			"viewlistexfdata": id
		}, function(data) {
			document.getElementsByClassName("exfdataqfnl")[0].innerHTML = data.trim();
		});
	}
</script>



<script type="text/javascript">
	function wqmlrUploadCsv(str) {
		let usertype = <?= $_SESSION['user_plan_type' . $site_token_for_dashboard]; ?>;

		if (str == 'open') {
			document.getElementById("wpqmlr-exportcsv").click();
		} else {
			var reader = new FileReader()
			var fileob = document.getElementById("wpqmlr-exportcsv");
			var filename = fileob.value;
			filename = filename.substring(filename.lastIndexOf("\\") + 1, filename.length);

			var fileobb = fileob.files[0];

			reader.onload = function() {
				var result = this.result;
				var arr = result.split(/\r?\n|\r/);
				var head = arr[0].trim();
				head = head.split(',');

				var selectname = "<select class='form-select'><option value='NO'>" + t("Select Name Field") + "</option>";

				var selectemail = "<select class='form-select'><option value='NO'>" + t("Select Email Field") + "</option>";

				for (var i = 0; i < head.length; i++) {
					selectname += "<option value='" + i + "'>" + head[i] + "</option>";
					selectemail += "<option value='" + i + "'>" + head[i] + "</option>";
				}
				selectname += "</select>";

				selectemail += "</select>";

				var select = document.createElement("div");
				select.innerHTML = "<div class='overlay'><div class='popup-modal'><div class='card pnl visual-pnl'><div class='card-header'>" + t("Select Required Fields") + "<span class='fas fa-times' style='right:10px;top:8px;position:absolute;font-size:20px;cursor:pointer' id='clsecsvreader'></span></div><div class='card-body'><label>" + t("Select Name Field") + "</label>" + selectname + "<label style='margin-top:5px;'>" + t("Select Email Field") + "</label>" + selectemail + " <center><p></p></center><input type='button' class='mt-3 btn theme-button btn-block'  id='wpqmlrcsvfilesave' value='" + t("Save It") + "'></div></div></div></div>";

				document.getElementById("editorform").appendChild(select);

				document.getElementById("clsecsvreader").onclick = function() {
					document.getElementById("editorform").removeChild(select);
				}
				//save file
				document.getElementById("wpqmlrcsvfilesave").onclick = async function() {
					var namecols = select.getElementsByTagName("select");

					var jsnarr = [];

					if (namecols[1].value != "NO") {
						var username = 1;

						if (namecols[0].value == "NO") {
							username = 0;
						}

						var count = 0;
						for (var i = 1; i < arr.length; i++) {
							var exfdata = {};
							var temparr = arr[i].split(',');
							//alert(temparr[namecols[1].value]);
							if (temparr[namecols[1].value] !== undefined) {
								jsnarr[count] = [];
								for (j = 0; j < temparr.length; j++) {
									if (j == namecols[0].value || j == namecols[1].value) {
										continue;
									}
									exfdata[head[j]] = trimDoubleQuote(temparr[j]);
								}
								exfdata.byadmin = 1;
								var exfdatajsn = JSON.stringify(exfdata);

								if (username == 1) {
									jsnarr[count].push(trimDoubleQuote(temparr[namecols[0].value]));
								} else {
									jsnarr[count].push("Imported From CSV");
								}
								jsnarr[count].push(trimDoubleQuote(temparr[namecols[1].value]));

								jsnarr[count].push(exfdatajsn);

								var listid = <?php if (isset($_GET['listid'])) {
													echo $_GET['listid'];
												} else {
													echo 0;
												} ?>;
								++count;
							}
						}

						if (count > 0) {
							var errdiv = select.getElementsByTagName("p")[0];

							errdiv.style.color = "green";
							errdiv.innerHTML = "<strong>" + t("Uploading, Please Wait") + "</strong>";
							var totalupload = jsnarr.length;
							var totaldone = 0;
							if (usertype == 2) {
								for (var i = 0; i < totalupload; i++) {
									try {
										try {
											await listUploadDelay(0.2);
										} catch (errrr) {
											console.log(errrr);
										}
										request.postRequestCb('req.php', {
											'ajxsavetolist': JSON.stringify(jsnarr[i]),
											'listid': listid
										}, function(data) {
											++totaldone;
											if (totalupload == totaldone) {
												errdiv.innerHTML = "<strong>" + t("Inserted Successfully") + "</strong>";
												location.reload();
											}
										});
									} catch (err) {
										++totaldone;
										if (totalupload == totaldone) {
											errdiv.innerHTML = "<strong>" + t("Inserted Successfully") + "</strong>";
											location.reload();
										}
									}
								}
							} else {
								for (var i = 0; i < totalupload; i++) {
									try {
										try {
											await listUploadDelay(0.2);
										} catch (errrr) {
											console.log(errrr);
										}
										request.postRequestCb('req.php', {
											'ajxsavetolist': JSON.stringify(jsnarr[i]),
											'listid': listid
										}, function(data) {
											++totaldone;
											if (totalupload == totaldone) {
												errdiv.innerHTML = "<strong>" + t("Inserted Successfully") + "</strong>";
												location.reload();
											}
										});
									} catch (err) {
										++totaldone;
										if (totalupload == totaldone) {
											errdiv.innerHTML = "<strong>" + t("Inserted Successfully") + "</strong>";
											location.reload();
										}
									}
								}
							}
						} else {
							alert(t("please create a header row for CSV file and try again"));
						}

					} else {
						alert(t("Please select Email field"));
					}
				}
			};

			// reader.readAsBinaryString(fileobb);
			reader.readAsText(fileobb);
			document.getElementById("uploadedcsv").innerHTML = filename;
		}
	}

	function listUploadDelay(sec) {
		try {
			sec = sec * 1000;
			return new Promise(function(resolve, reject) {
				setTimeout(function() {
					resolve(1)
				}, sec);
			});
		} catch (err) {
			console.log(err);
		}
	}

	function trimDoubleQuote(str) {
		var err = 0;
		try {
			str = str.trim();
			var chk = 0;
			if (str.indexOf('"') == 0 && str.lastIndexOf('"') == str.length - 1 && str.length > 2) {
				return str.substr(1, str.length - 2);
			} else if (str.lastIndexOf(';') == str.length - 1 && str.length > 2) {
				return str.substr(0, str.length - 1);
			} else {
				return str;
			}
		} catch (e) {
			++err;
			console.log(e.message);
		}
		if (err > 0) {
			return str;
		}
	}
	<?php
	if (isset($listtitle) && strlen($listtitle) > 0) {
		echo 'modifytitle("' . $listtitle . '","Lists");';
	}
	?>
</script>