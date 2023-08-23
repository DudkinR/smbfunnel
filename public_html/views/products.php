<?php
$productss = $data_arr['sell']->getProductIdTitle();
$productarr = array();
if (isset($_POST['delproduct'])) {
	$data_arr['sell']->deleteProduct($_POST['delproduct']);
}
if (is_object($productss)) {
	while ($rr = $productss->fetch_object()) {
		array_push($productarr, array('id' => $rr->id, 'productid' => $rr->productid, 'title' => $rr->title));
	}
}
if (isset($_GET['pagecount'])) {
	$hashcount = ($_GET['pagecount'] * get_option('qfnl_max_records_per_page')) - get_option('qfnl_max_records_per_page');
	$products = $data_arr['sell']->getProductForView($_GET['pagecount']);
} else {
	$hashcount = 0;
	$products = $data_arr['sell']->getProductForView();
}
?>
<div class="container-fluid" id="qfnlproducts">
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
					<?php echo arranger(array('`a`.id' => 'date', '`sales_count`' => 'sales')); ?>
				</div>
				<div class="col-md-4">
					<div class="mb-3">
						<div class="input-group input-group-sm">
							<div class="input-group-prepend ">
								<span class="input-group-text"><i class="fas fa-search"></i></span>
							</div>
							<input type="text" class="form-control form-control-sm" placeholder="<?php w('Enter product name or id'); ?>" v-on:keyup="searchProduct($event)">
						</div>
					</div>
				</div>
			</div>
			<div class="row productcontainer">
				<div class="col-sm-12">
					<div class="table-responsive">
						<?php
					//	print_r($products);
						?>
						<table class="table table-striped" id="tableforsearch">
							<thead>
								<tr>
									<th>#</th>
									<th><?php w('Product'); ?></th>
									<th><?php w('Product&nbsp;Id'); ?></th>
									<th><?php w('Price'); ?></th>
									<th><?php w('Currency'); ?></th>
									<th><?php w('Total&nbsp;Sales'); ?></th>
									<th><?php w('Added&nbsp;On'); ?></th>
									<th><?php w('Action'); ?></th>
								</tr>
							</thead>
							<tbody id="keywordsearchresult">
								<!-- keyword search -->
								<?php
								
								if (($products !== 0) && is_object($products['data'])) {
									while ($r = $products['data']->fetch_object()) {
										$count_product_used = $data_arr['sell']->getNubmerOfTimesTheProductUsed($r->id);
										$title = $r->title;
										$detailjsn = (array)$r;
										$detailjsn = base64_encode(json_encode($detailjsn));
										if (filter_var($r->url, FILTER_VALIDATE_URL)) {
											$title = "<a href='" . $r->url . "' target='_BLANK'>" . $title . "</a>";
										}
										++$hashcount;

										$action = "<table class='actionedittable'><tr><td><button class='btn unstyled-button edtbtn" . $r->id . "' data-bs-toggle='tooltip' title='" . t('Edit Product Detail') . "' v-on:click='openEditor(\"" . $detailjsn . "\",0)'><i class='fas fa-edit text-primary'></i></button></td><td><a href='index.php?page=sales&product_id=" . $r->id . "'><button class='btn unstyled-button' data-bs-toggle='tooltip' title='" . t('Sales') . "'><i class='fas fa-funnel-dollar text-success'></i></button></a></td><td><form action='' method='post' onsubmit=\"return confirmDeletion(this,event," . $count_product_used . ",'product')\"><input type='hidden' name='delproduct' value='" . $r->id . "'><button type='submit' class='btn unstyled-button' data-bs-toggle='tooltip' title='" . t('Delete Product') . "'><i class='fas fa-trash text-danger'></i></button></form></td></tr></table>";


										//$totalsales=$data_arr['sell']->countProductSales($r->id);
										if ($_SESSION['user_plan_type' . $site_token_for_dashboard] == 2 && $hashcount > 5) {
											break;
										}

										echo "<tr><td>" . t($hashcount) . "</td><td>" . $title . "</td><td>" . $r->productid . "</td><td>" . t(number_format($r->price)) . "</td><td>" . $r->currency . "</td><td><a href='index.php?page=sales&product_id=" . $r->id . "'>" . t(number_format($r->sales_count)) . "</a></td><td>" . date('d-M-Y', $r->createdon) . "</td><td>" . $action . "</td></tr>";
									}
								}
								?>
								<tr>
									<td colspan=10 class="total-data"><?php w('Total Products'); ?>: <?php
																										if ($products !== 0) {
																											echo ($_SESSION['user_plan_type' . $site_token_for_dashboard] == 2 && $products['total'] > 5) ? 5 : t(number_format($products['total']));
																										} else {
																											echo 0;
																										}
																										?></td>
								</tr>
								<!-- /keyword search -->
							</tbody>
						</table>
					</div>
					<div class="col-sm-12 row nopadding">
						<div class="col-sm-6 me-auto mt-2">
							<?php
							if ($products !== 0) {
								$pagecount = (isset($_GET['pagecount'])) ? $_GET['pagecount'] : 0;
								if ($_SESSION['user_plan_type' . $site_token_for_dashboard] == 2) {
									echo createPager(1, "index.php?page=products&pagecount", $pagecount);
								} else {
									echo createPager($products['total'], "index.php?page=products&pagecount", $pagecount);
								}
							}
							?>
						</div>
						<div class="col-sm-6 mt-2 text-right">
							<?php if ($_SESSION['user_plan_type' . $site_token_for_dashboard] == 2 && ($products !== 0 && $products['total'] >= 5)) { ?>
								<button class="btn theme-button" data-bs-toggle="modal" data-bs-target="#upgradeFromFreeModal"><i class="fas fa-pencil-alt"></i> Create New</button>
							<?php } else { ?>
								<button class="btn theme-button" v-on:click="openEditor('',1)"><i class="fas fa-pencil-alt"></i> Create New</button>
							<?php } ?>
						</div>
					</div>
					<textarea id="qfblallproducts" style="display:none;"><?php echo json_encode($productarr); ?></textarea>
				</div>
			</div>
		</div>
	</div>
</div>
<script>
	<?php
	if (isset($_GET['product_id'])) {
		echo "setTimeout(function(){document.getElementsByClassName('edtbtn" . $_GET['product_id'] . "')[0].dispatchEvent(new Event('click'));},500);";
	}
	?>
</script>
<style>
	.tmpltbdydiv .input-group {
		margin-top: 2px;
		margin-bottom: 2px;
	}
</style>