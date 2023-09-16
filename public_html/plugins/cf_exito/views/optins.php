<?php
$optins=array();
$has_form_id=false;
$total_leads=0;
$optin_ob=$this->load('optin_control');


if(isset($_POST['cfexito_del_optin']))
{
    $optin_ob->deleteLeads($_POST['cfexito_del_optin']);
}
if(isset($_GET['cfexito_form_id']))
{
    $has_form_id=true;
    $page_count=1;
    if(isset($_GET['page_count']) && is_numeric($_GET['page_count']))
    {
        $page_count=(int)$_GET['page_count'];
    }

    ob_start();
    echo '<div class="row">
                    <div class="col-md-2  mb-2">
                    '.createSearchBoxBydate().'
                    </div>
                    <div class="col-md-3">
                    '.showRecordCountSelection().'
                    </div>
                    <div class="col-md-3">'.arranger(array('id'=>'date')).'
                    </div>
                    <div class="col-md-4">
                    <div class="form-group">
                        <div class="input-group input-group-sm">
                            <div class="input-group-prepend ">
                                <span class="input-group-text"><i class="fas fa-search"></i></span>
                            </div>
                             <input type="text" class="form-control form-control-sm" placeholder="'.t('Enter title, type or credentilas').'" onkeyup="searchPaymentMethods(this.value)">
                        </div>
                    </div>
                    </div>
                </div>';
    $table_manager=ob_get_clean();

    $max_leads_limit=(int)get_option('qfnl_max_records_per_page');
    $optins=$optin_ob->getLeads($_GET['cfexito_form_id'], $max_leads_limit, $page_count);
    $total_leads=$optin_ob->getLeadsCount($_GET['cfexito_form_id']);
}
?>

<div class="container-fluid">
<div class="row page-titles mb-4">
                    <div class="col-md-5 align-self-center">
                        <h4 class="text-themecolor" id="commoncontainerid">Exito Optins</h4>
                    </div>
                    <div class="col-md-7 align-self-center text-right">
                        <div class="d-flex justify-content-end align-items-center">Manage optins</div>
                    </div>
</div>


<div class="row">
    <div class="col-sm-12">
    <?php if(count($optins)>0){ ?>
    <div class="card pb-2  br-rounded">
        <div class="card-body pb-2">
            <?php
            echo $table_manager;
            ?>
            <div class="table-responsive">
                <table class="table table-striped">
                <thead>
                    <tr>
                        <?php
                        for($i=0; $i<count($optins[0]); $i++)
                        {
                            echo "<th>".htmlentities($optins[0][$i])."</th>";
                        }
                        unset($optins[0]);
                        ?>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody id="keywordsearchresult">
                <!-- keyword search -->   
                    <?php
                        foreach($optins as $index=>$val)
                        {
                            echo "<tr>";
                            for($i=0; $i<count($val);$i++)
                            {
                                echo "<td>".htmlentities($val[$i])."</td>";
                            }
                            echo "<td><form action='' method='POST'><button class='btn unstyled-button' name='cfexito_del_optin' value='".$index."'><i class='fas fa-trash text-danger'></i></button></form></td>";
                            echo "</tr>";
                        }
                    ?>
                <tr><td class='text-center' colspan=10>Total Optins: <?php echo $total_leads; ?></td></tr>
                <!-- /keyword search -->
                </tbody>
                </table>
            </div>
            <div class="row mt-4">
                <div class="col">
                    <?php
                        $next_page_url="index.php?page=cfexito_all_optins&cfexito_form_id=".$_GET['cfexito_form_id']."&page_count";
                        $page_count=($page_count<2)? 0:$page_count;
                        echo createPager($total_leads,$next_page_url,$page_count);
                    ?>
                </div>
                <div class="col text-right">
                    <form action="" method="POST" target="_BLANK">
                        <button type="submit" class="btn theme-button" name="cfexito_export_csv" value="<?php echo $_GET['cfexito_form_id']; ?>"><i class="fas fa-file-download"></i>&nbsp;Export CSV</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <?php }
    elseif(isset($_GET['cfexito_form_id'])){
        echo "<h1 class='text-center' style='opacity:0.5;'>No Leads Found, <span class='text-primary' onclick='window.history.back()' style='cursor:pointer'>Go Back</span></h1>";
    }
    else{ ?>
                    <div class="row justify-content-center align-items-center">
                        <div class="col-sm-4">
                            <div class="card pnl">
                                <div class="card-header">Select Form</div>
                                <div class="card-body" style="max-height:300px; overflow: auto;">
                                    <?php
                                        $forms_ob=$this->load('forms_control');
                                        $forms=$forms_ob->getMiniForms();
                                        //print_r($forms);
                                        foreach($forms as $form_id=>$form_name)
                                        {
                                            echo "<a href='index.php?page=cfexito_all_optins&cfexito_form_id=".$form_id."'><button class='btn btn-light btn-block mb-2'>".$form_name."</button></a>";
                                        }
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>
            <?php } ?>

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