<?php
global $mysqli;
global $dbpref;
$table = $dbpref . 'shipping_options';
$store_id = false;
$abandoned = (isset($_GET['abandoned'])) ? true : false;
$all_data =[];

$store_id = $_GET['funnelid'];

$data_ob = $this->load('form_controller');

$total = 0;
$prosucts = get_products();
if(isset($prosucts[0]['currency_symbol']))
  $currency_symbol = $prosucts[0]['currency_symbol'];
else
  $currency_symbol = '$';

$get_record_query = $mysqli->query("SELECT `id` FROM `" . $table . "` WHERE `funnelid` = " . $mysqli->real_escape_string($store_id) . " ");
if (mysqli_num_rows($get_record_query) > 0) {
  $total = mysqli_num_rows($get_record_query);
}
$fnls = $data_ob->getAllFunnels($store_id);
$s_avatar = function ($fname, $data_ob) {
  return $data_ob->text_to_avatar($fname);
};
// echo '<pre>';
// print_r($data);
// exit;	
?>
<input type="hidden" id="get_option_url" value="<?= get_option('install_url'); ?>" />
<input type="hidden" id="cfshipping_ajax" value="<?= get_option('install_url') . "/index.php?page=ajax"; ?>" />
<input type="hidden" id="funnelid" value="<?= $store_id; ?>" />
<div class="container-fluid sf-store-selection">
  <div class="row">
    <div class="col-md-12">
      <div class="card br-rounded">
        <div class="card-body p-0 pt-2 px-2 sf-store-container">
          <div class=" d-flex justify-content-between">
            <div class="sf-store-list">
              <h4 class="fw-7 text-white d-flex align-items-center">
                <?php
                echo $s_avatar($fnls[0]['name'], $setup_ob) . "<span class='text-dark d-inline-block px-2' style='font-weight:600'>" . $fnls[0]['name'] . "</sapn> ";
                ?>
              </h4>
            </div>

            <div><button class="btn theme-button new_save_option" data-bs-toggle="modal" data-target="#exampleModal"><i class="fas fa-plus"></i> Add Shipping Option</button>
              <a href="index.php?page=all_shipping" class="btn btn-primary">Change Store</a>

            </div>
          </div>
        </div>
      </div>

    </div>
  </div>
</div>

<div class="container-fluid">
<h5>Use the shortcode   <strong class="text-info cfdisp_cursor" onclick="copyText(`[delivery_type]`)" data-bs-toggle="tooltip" title="" data-original-title="Copy to clipboard">[delivery_type]</strong> to show the Shipping Options on the checkout page</h5><br>
  <div class="card pb-2  br-rounded" id="hidecard1">
    <div class="sforiginal p-2">

      <div class="row">
        <!-- <div class="col-lg-2 col-md-12 mb-3">
                        <?php echo createSearchBoxBydate(); ?>
                    </div> -->
        <div class="col-lg-4 col-md-12">
          <?php echo showRecordCountSelection(); ?>
        </div>
        <div class="mb-3 col-lg-4 col-md-12">
          <div class="input-group input-group-sm mb-3 float-end">
            <div class="input-group-prepend">
              <span class="input-group-text"><i class="fas fa-search"></i></span>
            </div>
            <input type="text" class="form-control form-control-sm" placeholder="<?php w('Search With Name, Cost') ?>" onkeyup="searchOrder(this.value)">
          </div>
        </div>

      </div>

    </div>
    <div class="card-body p-0" id="hidecard2">
      <div class="row membercontainer">
        <div class="col-sm-12">
          <?php
          ?>
          <div id="crdcontainer">
            <div id="container_singledata_table">
              <div class="table-responsive sforiginal">
                <table class="table table-striped" id="option-table">
                  <thead>
                    <tr class="salerow">
                      <th>#</th>
                      <th><?php w('Name'); ?></th>
                      <th><?php w('Cost'); ?></th>
                      <th><?php w('Action'); ?></th>
                    </tr>
                  </thead>
                  <tbody id="keywordsearchresult">
                    <!-- keyword search -->
                    <?php
                    $page_count = 0;
                    if (isset($_GET['page_count'])) {
                      $page_count = (int)$_GET['page_count'];
                    }

                    $all_data = $data_ob->option_data($store_id, $page_count);
                    $last_id = 0;
                    $count = ($page_count < 1) ? 1 : $page_count;
                    $records_to_show = get_option('qfnl_max_records_per_page');
                    $records_to_show = (int) $records_to_show;
                    $count = ($count * $records_to_show) - $records_to_show;

                    ++$count;
                    foreach ($all_data as $all_data2) { ?>
                      <tr>
                        <td> <?php echo $count; ?></td>
                        <td><?php echo ucwords($all_data2->name); ?></td>
                        <td> <?php echo $currency_symbol.$all_data2->cost; ?></td>

                        <td>
                          <table class="actionedittable">
                            <tbody>
                              <tr>
                                <td><button option_name_val="<?php echo ucwords($all_data2->name); ?>" cost_val="<?php echo $all_data2->cost; ?>" idvalue="495" isvalid="1" class="btn unstyled-button edit_option" data-id="<?php echo $all_data2->id; ?>" data-bs-toggle="modal" data-target="#exampleModal" data-original-title="Edit Option"><i class="fa fa-pencil-alt"></i></button></td>
                                <td><button type="button" class="btn unstyled-button" shipped="0" idvalue="495" data-bs-toggle="tooltip" title="" onclick="delete_option(<?php echo $all_data2->id; ?>)" data-original-title="Delete Option"><i class="fa fa-trash text-danger"></i></button></td>

                              </tr>
                            </tbody>
                          </table>
                        </td>
                      </tr>
                    <?php
                      $count++;
                    } ?>
                    <tr>
                      <td colspan="12" class="total-data">
                        <center> Total : <?= $total; ?></center>
                      </td>
                    </tr>
                    <!-- /keyword search -->
                  </tbody>
                </table>
              </div>
              <div class="row mt-4">
                <div class="col-sm-6 mt-2">
                  <?php
                  $next_page_url = "index.php?page=all_shipping&funnelid=$store_id&page_count";
                  $page_count = ($page_count < 2) ? 0 : $page_count;
                  echo createPager($total, $next_page_url, $page_count);
                  ?>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" style="display: none;" aria-hidden="true">
  <div class="container modal-dialog modal-dialog-centered modal-xl">
    <div class="modal-content border-0 p-4">
      <div class="modal-header border-0 py-4">
        <h4>Shipping Option:</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close" id="shipping_zone_modle_close">
          <span class="text-dark display-5" aria-hidden="true">×</span>
        </button>
      </div>
      <div class="modal-body">
        <form>
          <div class="form-row">
            <label>Name</label>
            <div class="mb-3 col-xl-12">
              <input type="text" id="name" placeholder="Shipping Method" class="form-control">
            </div>
            <label>Cost</label>
            <div class="mb-3 col-xl-12" style="padding-bottom: 40px;">
              <input type="text" id="cost" placeholder="Shipping Cost" class="form-control">
            </div>
            <br>
            <input id="option_id" type="hidden">
            <button type="button" onclick="save_option()" class="btn btn-outline-primary px-4 py-2">Save Shipping
              Option</button>
            <button type="button" id="modle_close" class="btn btn-secondary px-5 py-2 ms-3" data-dismiss="modal">Close</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
<script>
  var eventId = '';
  $(document).on("click", ".new_save_option", function() {
    $('#option_id').val('');
    $("#name").val('');
    $("#cost").val('');


  });
  $(document).on("click", ".edit_option", function() {
    eventId = $(this).data('id');
    $('#option_id').val(eventId);
    $('#name').val($(this).attr('option_name_val'));
    $('#cost').val($(this).attr('cost_val'));
    

  });

  function save_option() {
    let error = 0;
    var option_id = $('#option_id').val();
    var name = $("#name").val();
    var cost = $("#cost").val();        
    var funnelid = $("#funnelid").val();
    var url = $("#cfshipping_ajax").val();

    if (name == "") {
      alert("Please enter shipping method name");
      error = 1;
    }
    if (cost != "") {
      if (!(/^\d*$/.test(cost))) {
        alert("Please enter valid shipping method cost");
        error = 1;
      }
    } else {
      alert("Please enter shipping method cost");
      error = 1;
    }
    if (error == 0) {
      $.ajax({
        url: url,
        type: "POST",
        data: {
          action: 'save_shipping_options',
          option_id: option_id,
          name: name,
          cost: cost,          
          funnelid: funnelid
        },
        success: function(data) {
          if (data == '200') {
            alert("Save successfully");
            $("#modle_close").click();
            location.reload();
          } else if (data == '201') {
            alert("Method Name is already exist. Please enter unique name.")
            location.reload();
          } else {
            alert('Something wrong');
            location.reload();
          }
        },
        error: function(error) {
          console.log(error);
        }
      });
    }
  }
</script>