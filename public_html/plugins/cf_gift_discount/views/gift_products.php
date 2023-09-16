<?php
global $mysqli;

$setup_ob=$this->load('giftcard');

if(!isset($_GET['store_id']))
{

$setups=$setup_ob->getAllFunnels();
$s_avatar= function($s_txt,$setup_ob){return $setup_ob->text_to_avatar($s_txt);};

?>
<div class="container-fluid">
    <div class="row page-titles mb-4">
        <div class="col-md-5 align-self-center">
            <h4 class="text-themecolor" id="commoncontainerid">  <img src='<?=CFGIFT_DISCOUNT_PLUGIN_URL_URL?>/assets/img/f7.png' alt='Gift' />  <?php w('Gift Card Products') ?></h4>
        </div>
        <div class="col-md-7 align-self-center text-end">
            <div class="d-flex justify-content-end align-items-center"><?php w('Create, edit and manage Gift Card Product') ?></div>
        </div>
    </div>
    <div class="container-fluid cfgift-selection">
        <div class="row">
            <div class="col-md-6 offset-md-3">
                <div class="card br-rounded">
                    <div class="card-header text-dark">
                        <div class="mb-3">
                            <h3><i class="fas fa-store"></i>&nbsp;<?php w('Select Store') ?></h3>
                        </div>
                    </div>
                    <div class="card-body cfgift-selection-container pb-4" style="height:500px;overflow:auto">
                        <div class="mb-3 cfgift-list">
                            <ul>
                              <?php
                               if(count($setups)>0 )
                               {
                                foreach($setups as $data)
                                {
                                  if( !empty( $data['name'] ) )
                                  {
                                    $s_avatar_el= $s_avatar( $data['name'],$setup_ob);
                                    $s_shortened_text= $data['name'];
  
                                    $name = $data['name']
                                    ?>
                                      <li> 
                                        <a href="index.php?page=cfdiscount_giftcards_products&store_id=<?=$data['id']?>">
                                              <div class="d-inline-block text-white d-flex align-items-center w-100">
                                              <?= $s_avatar_el ?>&nbsp; <span class="text-dark"><?= $s_shortened_text ?></span> 
                                              </div>
                                          </a>
                                      </li>        
                                  <?php
                                  }

                                }
                               }else{
                                 ?>
                                    <li class="p-3">   
                                    <a  href="index.php?page=create_funnel"> 
                                      <span class="text-dark" > <?php w('No '.ucfirst($funne_type).' available please');?>.</span>
                                      <?php w('Create a new '.lcfirst($funne_type)); ?>
                                        </a>
                                    </li>      
                                 <?php
                               }
                              ?>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
<?php
}
else{
$store_id = $_GET['store_id'];
$total_setup=0;
$giftcard_id=false;
$fnls=$setup_ob->getAllFunnels($store_id);
$s_avatar= function($s_txt,$setup_ob){return $setup_ob->text_to_avatar($s_txt);};

$page_count=1;
if(isset($_GET['page_count']) && is_numeric($_GET['page_count']))
{
  $page_count=(int)$_GET['page_count'];
}

if( isset( $_GET['giftcard_id'] ) && is_numeric($_GET['giftcard_id'] ) )
{
    $giftcard_id = $_GET['giftcard_id'];
}

if(isset($_POST['delgiftcardprds']))
{
    $setup_ob->deleteGiftProducts($_POST['delgiftcardprds']);
}

$total_setups= $setup_ob->getGiftProdctCount( );
$products = $setup_ob->getAllGiftProducts( $total_setups, $page_count,$store_id, $giftcard_id);
?>

<div class="container-fluid">
    <div class="row page-titles mb-4">
        <div class="col-md-5 align-self-center">
            <a href="index.php?page=cfdiscount_giftcards_products">
            <div class="d-flex text-white align-items-center">
                <?php
                echo $s_avatar($fnls[0]['name'],$setup_ob)."<span class='text-dark d-inline-block px-2' style='font-weight:600'>".$fnls[0]['name']."</sapn> ";
                ?>
                </div>
            </a>
        </div>
        <div class="col-md-7 align-self-center text-end">
            <div class="d-flex justify-content-end align-items-center">
                <div><a href="index.php?page=cfdiscount_giftcards_products" class="btn btn-primary"><?php w('Change Store'); ?></a></div>
            </div>
        </div>
    </div>
    <div class="card pb-2  br-rounded" style="display:<?php echo ( isset($_GET['giftcard_id'] ) && !empty( $_GET['giftcard_id'] ) ) ? 'none':'block'; ?>" id="collectionhidecard1">
        <div class="sforiginal p-2">
            <div class="row">
                <div class="col-lg-2 col-md-12 ">
                    <?php echo createSearchBoxBydate(); ?>
                </div>
                <div class="col-lg-3 col-md-12">
                    <?php echo showRecordCountSelection(); ?>
                </div>
                <div class="col-md-3">
                <?php echo arranger(array('id'=>'date')); ?>
                </div>
                <div class=" col-lg-4 col-md-12">
                    <div class="input-group input-group-sm mb-3 float-end">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-search"></i></span>
                        </div>
                        <input type="text" class="form-control form-control-sm" placeholder="<?php w('Search with name, id') ?>" onkeyup="searchGiftCards(this.value)">
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body p-0 sforiginal" >   
            <div class="row collectioncontainer">
                <div class="col-sm-12">
                    <div class="table-responsive">
                    <table class="table " id="tableforsearch">
                        <thead>
                            <tr><th>#</th><th><?php w('Product'); ?></th><th><?php w('Product&nbsp;Id'); ?></th><th><?php w('Price'); ?></th><th><?php w('Currency'); ?></th><th><?php w('Total&nbsp;Sales'); ?></th><th><?php w('Added&nbsp;On'); ?></th><th><?php w('Action'); ?></th></tr>
                        </thead>
                        <tbody id="keywordsearchresult">
                            <!-- keyword search -->
                            <?php
                                if(($products !==0) && is_object($products))
                                {
                                    $hashcount=1;
                                    while($r=$products->fetch_object())
                                        {
                                            $count_product_used=0;
                                            $title=$r->title;
                                            $detailjsn=(array)$r;
                                            $detailjsn=base64_encode(json_encode($detailjsn));
                                            $sales = $setup_ob->getSales($r->id);

                                            if(filter_var($r->url,FILTER_VALIDATE_URL))
                                            {
                                                $title="<a href='".$r->url."' target='_BLANK'>".$title."</a>";
                                            }
                                            ++$hashcount;

                                            $action="<table class='actionedittable'><tr>
                                            <td><a class='btn unstyled-button' data-toggle='tooltip' title='".t('Edit Gift Product Detail')."' href='index.php?page=cfdiscount_add_giftproduct&store_id=$store_id&product_id=$r->id'><i class='fas fa-edit text-primary'></i></a></td><td><a class='btn unstyled-button' data-toggle='tooltip' title='".t('View Gift Card Product')."' target='href' href='index.php?page=view_product&product=$r->productid'><i class='fas fa-eye text-info'></i></a></td><td><a href='index.php?page=sales&product_id=".$r->id."'><button class='btn unstyled-button' data-toggle='tooltip' title='".t('Sales')."'><i class='fas fa-funnel-dollar text-success'></i></button></a></td><td><form action='' method='post' onsubmit=\"return confirmDeletion(".$count_product_used.",'product')\"><input type='hidden' name='delgiftcardprds' value='".$r->id."'><button type='submit' class='btn unstyled-button' data-toggle='tooltip' title='".t('Delete Product')."'><i class='fas fa-trash text-danger'></i></button></form></td></tr></table>";

                                            echo "<tr><td>".t($hashcount)."</td><td>".$title."</td><td>".$r->productid."</td><td>".t(number_format($r->price))."</td><td>".$r->currency."</td><td><a href='index.php?page=sales&store_id=$store_id&product_id=$r->id'>".t(number_format($sales))."</a></td><td>".date('d-M-Y h:ia',$r->createdon)."</td><td>".$action."</td></tr>";
                                        }
                                }
                            ?>
                            <tr>
                                <td colspan=10 class="total-data"><?php w('Total Products'); ?>: <?php
                                        echo t(number_format($total_setups));
                                ?></td>
                            </tr>
                            <!-- /keyword search -->
                        </tbody>
                    </table>
                    </div>
                    <div class="col-sm-12 row nopadding">
                        <div class="col-sm-6 me-auto mt-2">
                        <?php
                            $next_page_url="index.php?page=cfdiscount_giftcards_products&store_id=$store_id&page_count";
                            $page_count=( $page_count < 2 )? 0: $page_count;
                            echo createPager( $total_setups,  $next_page_url, $page_count );
                            ?> 
                        </div>
                        <div class="col-sm-6 mt-2 text-end">
                        <a href="index.php?page=cfdiscount_add_giftproduct&store_id=<?=$store_id?>" class="btn theme-button" id="open-giftcard-form"><i class="fas fa-pencil-alt"></i> <?php w('Create New') ?></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
}
if( isset( $_GET['page'] ) && ($_GET['page']=="cfdiscount_giftcards_products"  || $_GET['page']=="cfdiscount_add_giftproduct") )
{
    ?>
    <script>
      let sideBarPluginLinks= document.querySelectorAll(`.sidebar-submenu a`);
            sideBarPluginLinks.forEach(link=>{
                if(typeof(link.getAttribute('href'))==='string' &&  (  link.href.indexOf('cfdiscount_giftcards_products') !=-1 ||  link.href.indexOf('cfdiscount_add_giftproduct') !=-1 ) ){
                    let url= new URL(link.href);
                    url.searchParams.append('store_id', `<?= (int)$_GET['store_id']  ?>`);
                    link.href= url.href;
                }
            });
</script>
    
    <?php

}
?>



