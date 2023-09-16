<?php
if(isset($_POST['cfpaydelsetup']))
{
    $this->deleteSetup($_POST['cfpaydelsetup']);
}
?>
<div class="container-fluid">
<div class="row page-titles mb-4">
                    <div class="col-md-5 align-self-center">
                        <h4 class="text-themecolor" id="commoncontainerid">WarriorPlus</h4>
                    </div>
                    <div class="col-md-7 align-self-center text-right">
                        <div class="d-flex justify-content-end align-items-center">Create, edit and manage your payment methods</div>
                    </div>
</div>


    <div class="row">
        <div class="col-sm-12">
            <div class="card pb-2  br-rounded">
                <div class="card-body pb-2">
                <div class="row">
					<div class="col-md-2  mb-2">
					<?php echo createSearchBoxBydate(); ?>
					</div>
					<div class="col-md-3">
					<?php echo showRecordCountSelection(); ?>
					</div>
					<div class="col-md-3">
					<?php echo arranger(array('id'=>'date')); ?>
					</div>
					<div class="col-md-4">
					<div class="form-group">
						<div class="input-group input-group-sm">
							<div class="input-group-prepend ">
								<span class="input-group-text"><i class="fas fa-search"></i></span>
							</div>
							 <input type="text" class="form-control form-control-sm" placeholder="<?php w('Search with title & method'); ?>" onkeyup="searchPaymentMethods(this.value)">
						</div>
					</div>
					</div>
                </div>

                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr><th>#</th><th>Title</th><th>Added On</th><th>Action</th></tr>
                            </thead>
                            <tbody id="keywordsearchresult">
                            <!-- keyword search -->   
                                <?php
                                $page_count=0;
                                if(isset($_GET['page_count']))
                                {
                                    $page_count=(int)$_GET['page_count'];
                                }
                                $total=$this->countSetups();
                                $all_data=$this->loadSetups($page_count);
                                $last_id=0;
                                $count=($page_count<1)? 1:$page_count;
                                $records_to_show=get_option('qfnl_max_records_per_page');
                                $records_to_show=(int) $records_to_show;
                                $count=($count*$records_to_show)-$records_to_show;

                                for($i=0; $i<count($all_data); $i++)
                                {
                                    $data=$all_data[$i];
                                    $count_hash =$count+($i+1);

                                    $action="<table class='actionedittable'><tr>
                                    <td><a href='index.php?page=cfpay_setting_".$this->method."&id=".$data->id."'><button class='btn unstyled-button' data-toggle='tooltip' title='Edit'><i class='text-primary fas fa-edit'></i></button></a></td>
                                    <td><form action='' method='POST'><button type='submit' class='btn unstyled-button' data-toggle='tooltip' title='Delete' name='cfpaydelsetup' value='".$data->id."'><i class='text-danger fas fa-trash'></i></button></form></td>
                                    </tr></table>";

                                    echo "<tr>
                                    <td>".$count_hash."</td>
                                    <td><a href='index.php?page=cfpay_setting_".$this->method."&id=".$data->id."'>".$data->title."</a></td>
                                    <td>".date('d-m-Y h:ia',strtotime($data->createdon))."</td>
                                    <td>".$action."</td>
                                    </tr>";

                                    $last_id=$data->id;
                                }
                                ?>
                            <tr><td colspan=10 class="text-center">Total Payment Methods: <?php echo $total; ?></td></tr>
                            <!-- /keyword search -->
                            </tbody>
                        </table>
                    </div>

                    <div class="row mt-4">
                        <div class="col">
                            <?php
                            $next_page_url="index.php?page=cfpay_setups_".$this->method."&page_count";
                            echo createPager($total,$next_page_url,$page_count);
                            ?>
                        </div>
                        <div class="col text-right">
                            <a href="index.php?page=cfpay_setting_<?php echo $this->method; ?>"><button class="btn theme-button"><i class="fas fa-pencil-alt"></i>&nbsp;Create New</button></a>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
<script>
function searchPaymentMethods(search)
{
    var ob=new OnPageSearch(search,"#keywordsearchresult");
    ob.url=window.location.href;
    ob.search();
}
</script>