<?php
$sales_ob=$data_arr['sales_ob'];
//print_r($sales_ob);
?>
<div class="container-fluid">
<div class="card pb-2  br-rounded" id="hidecard1">
    <div class="card-body pb-2" id="hidecard2">

<?php if(!isset($_GET['sell_id'])){ ?>
<div class="row">
<div class="col-lg-2 col-md-12 mb-3">
<?php echo createSearchBoxBydate(); ?>
</div>
<div class="col-lg-4 col-md-12">
<?php echo showRecordCountSelection(); ?>
</div>
<div class="mb-3 col-lg-4 col-md-12">
<div class="input-group input-group-sm mb-3 float-right">
    <div class="input-group-prepend">
      <span class="input-group-text"><i class="fas fa-search"></i></span>
    </div>
    <input type="text" class="form-control form-control-sm" placeholder="Search With Name, Email, Payment Id" onkeyup="searchMember(this.value)">
  </div>
</div>
<div class="mb-3 col-lg-2 col-md-12 dropdown"><button class="btn dropdown-toggle btn-sm btn-block btn-primary" data-bs-toggle="dropdown"><i class="fas fa-eye" style="color: white !important;"></i>&nbsp;Sales Category&nbsp;&nbsp;</button>
<div class="dropdown-menu">
	<?php
		$has_product_id=(isset($_GET['product_id']))? '&product_id='.$_GET['product_id']:'';
	?>
	<a class="dropdown-item<?php if(!isset($_GET['payment_type'])){echo ' text-primary';} ?>" href="index.php?page=sales<?php echo $has_product_id; ?>">All</a>

	<a class="dropdown-item<?php if(isset($_GET['payment_type']) && $_GET['payment_type']=='all_shipped'){echo ' text-primary';} ?>" href="index.php?page=sales&payment_type=all_shipped<?php echo $has_product_id; ?>">Delivered</a>

	<a class="dropdown-item<?php if(isset($_GET['payment_type']) && $_GET['payment_type']=='all_pending'){echo ' text-primary';} ?>" href="index.php?page=sales&payment_type=all_pending<?php echo $has_product_id; ?>">Pending to Deliver</a>

	<a class="dropdown-item<?php if(isset($_GET['payment_type']) && $_GET['payment_type']=='cod'){echo ' text-primary';} ?>" href="index.php?page=sales&payment_type=cod<?php echo $has_product_id; ?>">COD Only</a>

	<a class="dropdown-item<?php if(isset($_GET['payment_type']) && $_GET['payment_type']=='cod_shipped'){echo ' text-primary';} ?>" href="index.php?page=sales&payment_type=cod_shipped<?php echo $has_product_id; ?>">COD(Shipped) Only</a>

	<a class="dropdown-item<?php if(isset($_GET['payment_type']) && $_GET['payment_type']=='cod_pending'){echo ' text-primary';} ?>" href="index.php?page=sales&payment_type=cod_pending<?php echo $has_product_id; ?>">COD(Pending) Only</a>

	<a class="dropdown-item<?php if(isset($_GET['payment_type']) && $_GET['payment_type']=='non_cod'){echo ' text-primary';} ?>" href="index.php?page=sales&payment_type=non_cod<?php echo $has_product_id; ?>">Through payment methods only</a>

	<a class="dropdown-item<?php if(isset($_GET['payment_type']) && $_GET['payment_type']=='non_cod_shipped'){echo ' text-primary';} ?>" href="index.php?page=sales&payment_type=non_cod_shipped<?php echo $has_product_id; ?>">Through payment methods(Shipped) only</a>

	<a class="dropdown-item<?php if(isset($_GET['payment_type']) && $_GET['payment_type']=='non_cod_pending'){echo ' text-primary';} ?>" href="index.php?page=sales&payment_type=non_cod_pending<?php echo $has_product_id; ?>">Through payment methods(Pending) only</a>

</div>
</div>
</div>
<?php } ?>
<div class="row membercontainer">
<div class="col-sm-12">
<?php
$countpage=0;
$search="";
if(isset($_POST['searchsales']))
{
	$search=$_POST['searchsales'];
}
if(isset($_GET['page_count'])){$countpage=$_GET['page_count'];}

$p_payment_type="all";
if(isset($_GET['payment_type']))
{
	$p_payment_type=$_GET['payment_type'];
}
if(isset($_GET['product_id']))
{
	$sales=$sales_ob->visualOptisForSales($_GET['product_id'], $countpage, $search,10, $p_payment_type);
	$selected_product_ob=$sales_ob->getProduct($_GET['product_id']);
	if($selected_product_ob)
	{
		echo "<script>document.getElementById('commoncontainerid').innerHTML='Product (<a href=\"index.php?page=products&product_id=".$_GET['product_id']."\">".addslashes($selected_product_ob->title)."</a>) Sales';</script>";
	}
}
else
{
	$sales=$sales_ob->visualOptisForSales('all', $countpage, $search, 10, $p_payment_type);
}
//echo $optin_datas['extracols'];
?>
<div id="crdcontainer">
<div id="container_singledata_table">
<div class="table-responsive">
<table class="table table-striped">

<thead><tr class="salerow"><th>#</th><th><?php w('Payment Id'); ?></th><th><?php w('Name'); ?></th><th><?php w('Email'); ?></th><?php ?><th><?php w('Date'); ?></th><th style="display:none;"><?php w('product'); ?></th><th><?php w('Action'); ?></th></tr></thead>

<tbody id="srchmember">
<!--srch-->
<?php
$hashcount=0;
if(isset($_GET['page_count']))
{
	$hashcount=($_GET['page_count']*10)-10;
}
print_r($sales['sales']);
while($r=$sales['sales']->fetch_object())
{
	//print_r($r);
//	echo "<br>";
	++$hashcount;

	$shippedclass="text-danger";
	$shippeng_title="Mark As Shipped";
	$is_shipped=0;
	if($r->shipped=="1")
	{
	++$is_shipped;
	$shippedclass="text-success";
	$shippeng_title="Mark As Not Shipped";
	}
	$date=date('d-M-Y',$r->addedon);

	$valid_btn_class=($r->valid==1)? "text-success":"text-danger";
	$valid_btn_title=($r->valid==1)? "Cancel Purchase":"Confirm Purchase";

	$this_sale_link=get_option('install_url');
	$this_sale_link.='/index.php?page=sales&sell_id=';
	$this_sale_link=$this_sale_link.cf_enc($r->id);
	
	$action="<table class='actionedittable'><tr><td onclick='viewPurchaseDetail(".$r->id.")' style='cursor:pointer;'><i class='fas fa-eye' style='margin-right:4px;'></i></td><td><button type='button' class='btn unstyled-button' shipped='".$is_shipped."' idvalue='".$r->id."' data-bs-toggle='tooltip' title='".t($shippeng_title)."' onclick='shippedNotShipped(this)'><i class='fas fa-truck ".$shippedclass."'></i></button></td><td><button type='submit' idvalue='".$r->id."' isvalid='".$r->valid."' class='btn unstyled-button'data-bs-toggle='tooltip' title='".t($valid_btn_title)."' onclick='shippedNotShipped(this,\"valid\")'><i class='fas fa-check-circle ".$valid_btn_class."'></i></button></td><td><button class='btn unstyled-button text-primary' data-bs-toggle='tooltip' title='Copy to clipboard' onclick='copyText(\"".$this_sale_link."\")'><i class='fas fa-link'></i></button></td></tr></table>";

	$product="";

	$productdata=$sales_ob->getProduct($r->productid);


	if($productdata)
	{
	$product="(#".$productdata->productid.") ".$productdata->title."";

		$product="<a href='index.php?page=products&product_id=".$r->productid."' target='_BLANK'>".$product."</a>";
	}

	if(isset($_GET['product_id']))
	{
	$parent_product="Yes";
	if((int)$r->parent>0)
	{
		$tempparent_product=$sales_ob->getProduct($r->parent);
		if($tempparent_product)
		{
			$tempparent_product="<a href='index.php?page=products&product_id=".$r->parent."'>(#".$tempparent_product->productid.") ".$tempparent_product->title."</a>";
		}
		else{$tempparent_product="Not Found";}
	}
	}
	else
	{
		$parent_product="N/A";
		$mysqli=$info['mysqli'];
		$dbpref=$info['dbpref'];
		$checkotherproducts_query=$mysqli->query("select `id`,`productid`,`title` from `".$dbpref."all_products` where id in(select `productid` from `".$dbpref."all_sales` where `parent` in('".$r->productid."') and `payment_id`='".$r->payment_id."')");
	
		if($checkotherproducts_query->num_rows>0)
		{
		$parent_product="";
		while($tempr=$checkotherproducts_query->fetch_object())
		{
			$parent_product .="<a href='index.php?page=products&product_id=".$tempr->id."'>(#".$tempr->productid.") ".$tempr->title."</a> ,";
		}
		$parent_product=rtrim($parent_product," ,");
		}
	}

	$valid_style=($r->valid==1)? "":"background-color:rgba(255, 0, 0, 0.2);";

	echo "<tr class='salerow salestrace".$r->id."' style='".$valid_style."'><td>".t($hashcount)."</td><td>".$r->payment_id."</td><td>".$r->purchase_name."</td><td>".$r->purchase_email."</td><td>".$date."</td><td style='display:none;'>".$product."</td><td>".$action."</td></tr>";
}
?>
<?php if(!isset($_GET['sell_id'])){ ?>
<tr><td colspan=10 class="total-data"><?php w('Total Sales'); ?>: <?php echo t(number_format($sales['total'])); ?></td></tr>
<?php } ?>
<!--/srch-->
</tbody>

</table>
</div>

<?php if(!isset($_GET['sell_id'])){ ?>
<div class="col-sm-12 row nopadding">
<div class="col-sm-6 me-auto mt-2">
<?php
$pagecount=(isset($_GET['page_count']))? $_GET['page_count']:0;

echo createPager($sales['total'],$_SERVER['REQUEST_URI']."&page_count",$pagecount);
 ?>
</div>
<div class="col-sm-6 mt-2  text-right">
<form action="index.php?page=export_csv" method="post">
	<?php if($_SESSION['user_plan_type' . $site_token_for_dashboard]==2) { ?>
	<button type="button" class="btn theme-button"  data-bs-toggle="modal" data-bs-target="#upgradeFromFreeModal" value="<?php if(isset($_GET['product_id'])){ echo $_GET['product_id']; }else{echo 0;} ?>">
	<?php } else { ?>
	<button type="submit" class="btn theme-button"  name="salesto_csv" value="<?php if(isset($_GET['product_id'])){ echo $_GET['product_id']; }else{echo 0;} ?>">
	<?php } ?>
		<i class="fas fa-file-download"></i> <?php w('Export To CSV'); ?>
	</button>
</form>
</div>
</div>
<?php } ?>


</div>

</div>

</div>
</div>
</div>
</div></div>
<script>
function searchMember(search)
{
	var request=new ajaxRequest();
	request.postRequestCb("index.php?page=sales&productid=<?php if(isset($_GET['productid'])){echo $_GET['productid'];}else{echo "all";} ?>",{"searchsales":search},function(data){
		var first=data.indexOf("<!--srch-->");
		var last=data.indexOf("<!--/srch-->");
		document.getElementById("srchmember").innerHTML=data.substr(first,last-first);
		console.log(data.substr(first,last-first));
	})
}

function viewPurchaseDetail(id){
	
	var designdiv1=document.getElementById("hidecard1");
     var designdiv2=document.getElementById("hidecard2");
	designdiv1.classList.remove("card","pb-2","br-rounded");
    designdiv2.classList.remove("card-body","pb-2");
var div=document.getElementById("container_singledata_table");
var table_data=div.getElementsByClassName("salestrace"+id)[0].getElementsByTagName("td");
var cache_html=div.innerHTML;
div.innerHTML="";
var goback_button='<span class="gobacktable" style="float: right; color: rgb(31, 87, 202); font-size: 16px; margin-bottom: 10px; cursor: pointer;"><i class="fas fa-arrow-alt-circle-left"></i> '+t('Go&nbsp;back')+'</span><br>';

var request=new ajaxRequest();

 var container="<center><h4>"+t('Loading...')+"</h4></center>"+goback_button;
 div.innerHTML=container;

 div.getElementsByClassName("gobacktable")[0].onclick=function(){div.innerHTML=cache_html;
	designdiv1.classList.add("card","pb-2","br-rounded");
    designdiv2.classList.add("card-body","pb-2");
};
 
 
request.postRequestCb('req.php',{"viewpurchasedetail":id},function(data){
	var arr=data.trim();
	if(arr.length>2)
	{
		arr=arr.split('@sbreak@');				
		arr[0] = arr[0].replace(/&quot;/g, "'");
        arr[0] = arr[0].replace(/(?:\r\n|\r|\n)/g, '');		
		var shipping=JSON.parse(arr[0]);		
		var cod_data=JSON.parse(arr[4]);
		try
		{
			var cod_template="";
			try
			{
				if(cod_data.signed_by !==undefined)
				{
					cod_template= "<div class='card pnl'><div class='card-header'><h4 style='margin:0px;font-size:15px;'>"+t('Delivery Detail')+"</h4></div><div class='card-body'><div class='table-responsive'><table class='table table-striped'><tbody>";

					cod_template +="<tr><td>delivered by</td><td>"+cod_data.signed_by+"</td></tr>";
					cod_template +="<tr><td>Date</td><td>"+cod_data.updated_on+"</td></tr>";
					cod_template +="<tr><td>IP</td><td>"+cod_data.last_ip+"</td></tr>";

					cod_template += "</tbody></table></div></div></div>";
				}
			}catch(err){ console.log(err);}			
			var shippingtable="<div class='card custdetail pnl' style='width:100%;'><div class='card-header'><h4 style='margin:0px;font-size:15px;'>"+t('Payment ID')+": "+table_data[1].innerText+"</h4></div><div class='card-body'><p>"+t('Product')+": "+table_data[5].innerHTML+"</p><p>"+t('Customer Name')+": "+table_data[2].innerText+"</p><p>"+t('Customer Email')+": "+table_data[3].innerText+"</p><p>"+t('Purchased On')+": "+table_data[4].innerText+"</p></div></div><style>div.custdetail p{font-size:14px;}</style><!-- Shipping Detail --><div class='card pnl'><div class='card-header'><h4 style='margin:0px;font-size:15px;'>"+t('Shipping Detail')+"</h4></div><div class='card-body'>@replacesteppayment@<br><div class='table-responsive'><table class='table table-striped'><thead><tr></tr></thead><tbody>";

			for(var i in shipping)
			{
				shippingtable +="<tr><td>"+i+"</td><td>"+shipping[i]+"</td></tr>";
			}
		}catch(err){console.log(err.message);}

		var steppayment=arr[3];
		//console.log(steppayment);
        shippingtable=shippingtable.replace("@replacesteppayment@",steppayment);

		shippingtable +="</tbody><thead ><th colspan=2>"+t('Payment Method')+"</th></thead><tbody>";
		try{shippingtable +="<tr><td colspan=2>"+arr[1]+"</td></tr>";}catch(err){console.log(err.message);}
		shippingtable +="</tbody></table></div></div></div>";

	}
	div.innerHTML="<div>"+goback_button+"</div>"+shippingtable+cod_template+goback_button;
	div.getElementsByClassName("gobacktable")[0].onclick=function(){div.innerHTML=cache_html;designdiv1.classList.add("card","pb-2","br-rounded");
    designdiv2.classList.add("card-body","pb-2");};
	div.getElementsByClassName("gobacktable")[1].onclick=function(){div.innerHTML=cache_html;designdiv1.classList.add("card","pb-2","br-rounded");
    designdiv2.classList.add("card-body","pb-2");};
});
}
function shippedNotShipped(current,doo="shipping")
{
	if(doo=="valid")
	{
		if(current.getAttribute('isvalid')=="1" && !confirmDeletion())
		{
			return;
		}
	}

	var doc=current;
	doc.disabled=true;
	var id=doc.getAttribute('idvalue');

	if(doo=="shipping")
	{
	var shipped=doc.getAttribute('shipped');
	}
	else
	{
	var is_valid=doc.getAttribute("isvalid");
	}

	var request=new ajaxRequest();
	//alert("product_shipping_status="+id);
	try {
		document.body.removeChild(document.getElementById(doc.getAttribute("aria-describedby")));
	} catch (error) {
		console.log(error.message);
	}

	if(doo=="shipping")
	{
	var req_str={"product_shipping_status":id};
	}
	else
	{
	var req_str={"product_valid_status":id};
	}

	request.postRequestCb('req.php',req_str,function(data){
		doc.disabled=false;
		try{
			if(doo=="shipping")
			{
		let shifted_doc=doc.querySelectorAll("i.fa-truck")[0];
		if(shipped=="1")
		{
			doc.setAttribute('data-original-title',t("Mark As Shipped"));
			doc.classList.add('btn-warning');
			doc.setAttribute('shipped','0');
			doc.classList.remove('btn-success');
			shifted_doc.classList.add('text-danger');
			shifted_doc.classList.remove('text-success');
		}
		else
		{
			doc.setAttribute('data-original-title',t("Mark As Not Shipped"));
			doc.classList.add('btn-success');
			doc.setAttribute('shipped','1');
			doc.classList.remove('btn-warning');
			shifted_doc.classList.add('text-success');
			shifted_doc.classList.remove('text-danger');
		}
			}
			else
			{
				if(is_valid=="1")
				{
					doc.setAttribute('data-original-title',t("Cancel Purchase"));
					doc.classList.add('btn-danger');
					doc.setAttribute('isvalid','0');
					doc.classList.remove('btn-success');
					document.getElementsByClassName("salestrace"+id)[0].style.backgroundColor="rgba(255, 0, 0, 0.2)";
				}
				else
				{
					doc.setAttribute('data-original-title',t("Confirm Purchase"));
					doc.classList.add('btn-success');
					doc.setAttribute('isvalid','1');
					doc.classList.remove('btn-danger');
					delete document.getElementsByClassName("salestrace"+id)[0].removeAttribute('style');
				}
			}
		}catch(err){}
	});

}
authPurchaseData();
</script>
<style>
.salerow td,.salerow th
{
	padding:0px;
	margin:0px;
	padding-top:10px;
	padding-bottom:10px;
	padding-left:4px;
	padding-right:4px;
	vertical-align:middle;
	max-width:230px;
}
</style>
