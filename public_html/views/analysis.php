<?php
$mysqli = $info['mysqli'];
$dbpref = $info['dbpref'];
//print_r($_GET);
$table = $dbpref . "quick_funnels";
$page_table = $dbpref . "quick_pagefunnel";
$tablename = $dbpref . "site_visit_record";
?>
<div class="container-fluid">
  <div class="row">
    <div class="col-sm-12">
      <form action="" method="get">
        <input type="hidden" name="page" value="analysis">
        <div class="row mb-2">
          <div class="col-sm-8 text-left">
            <div class="mb-3">
              <label for="exampleFormControlSelect1" class="select-funnel-label"><?php w('Select funnel which you want to analyze'); ?></label>
            </div>
          </div>
          <div class="col-sm-4 text-right">            
            <select class="btn btn-primary " id="exampleFormControlSelect1" name="analysis_funnel" onchange="if(this.value>0){this.form.submit();}">
              <option value=0>-<?php w('Select Funnel'); ?>-</option>
              <?php
              $datas = $mysqli->query("select `id`,`name` from `" . $table . "` order by `id` desc");
              if ($datas->num_rows > 0) {
                while ($r = $datas->fetch_object()) {
                  $selected = "";
                  if (isset($_GET['analysis_funnel']) && ($r->id == $_GET['analysis_funnel'])) {
                    $selected = "selected";
                  }                 

                  echo "<option value='" . $r->id . "' " . $selected . ">" . $r->name . "</option>";
                }
              }
              ?>
            </select>
          </div>
        </div>
      </form>
      <hr>
    </div>
  </div>
  <?php if (isset($_GET['analysis_funnel'])) { ?>
    <div class="row" style="margin-top:5px;">
      <div class="col-md-2 mb-2">
        <?php echo createSearchBoxBydate(); ?>
      </div>
    </div>
  <?php } ?>
  <br>
  <?php
  if (isset($_GET['analysis_funnel'])) {
    $selectOption = $mysqli->real_escape_string($_GET['analysis_funnel']);

    //echo $selectOption."<br>" ;

    $date_search = dateBetween("visitedon", $tablename);

    //print_r($date_search);

    $qry = $mysqli->query("select `name`,`type`,`baseurl` from `" . $table . "` where `id`='" . $selectOption . "'");
    $id = 0;
    $type = "";
    $funnel_title = "Funnel NOt Found";
    $funnel_baseurl = "";
    if ($r = $qry->fetch_object()) {
      $id = $selectOption;
      $type = $r->type;
      $funnel_title = $r->name;
      $funnel_baseurl = $r->baseurl;
    }
    //print_r($r->id);

    $in_funnel = "select `id` from `" . $page_table . "` where `funnelid`='" . $id . "'";

    $qry = $mysqli->query("select count(`id`) as `viewcount`,sum(`convert_count`) as `sumconvertcount` from `" . $tablename . "` where `visit_pageid` in (" . $in_funnel . ")" . $date_search[1]);

    $sumconvertcount = 0;
    $viewcount = 0;
    if ($r = $qry->fetch_object()) {
      $sumconvertcount = $r->sumconvertcount;
      $viewcount = $r->viewcount;
    }
  ?>

    <div class="p-0" id="hidecard1">
      <ul class="nav nav-tabs md-tabs nav-justified theme-nav rounded-top  d-flex flex-column flex-sm-row tab-border" role="tablist">


        <li class="nav-item  tab-border">
          <a class="nav-link active tab-border" data-bs-toggle="tab" href="#home" role="tab">
            <?php w('Funnel Analysis'); ?></a>
        </li>

        <li class="nav-item tab-border">
          <a class="nav-link tab-border" data-bs-toggle="tab" href="#table" role="tab">
            <?php w('Page Analysis'); ?></a>
        </li>



      </ul>

      <div class="card-body pb-2 p-0" id="hidecard2">
        <div class="tab-content">
          <div id="home" class="tab-pane fade in active show ">
            <div class="container-fluid p-0">
              <div class="card funnel_post shadow mt-3">
                <div class="card-body">
                  <div class="row align-items-center">
                    <div class="col-lg-3 col-md-6 border-right">
                      <div class="row row align-items-center no-gutters">
                        <div class="col-auto">
                          <i class="fas fa-filter"></i>
                        </div>
                        <div class="col-auto  mt-3">
                          <h3 class="fs-12 mb-0"><?php w('Funnel Name') ?></h3>
                          <p class="fs-18"><?php echo $funnel_title; ?> </p>
                        </div>
                      </div>
                    </div>
                    <div class="col-lg-3 col-md-6 border-right">
                      <div class="row row align-items-center no-gutters">
                        <div class="">
                          <i class="fas fa-list"></i>
                        </div>
                        <div class="col-auto  mt-3">
                          <h3 class="fs-12 mb-0"><?php w('Type'); ?>:</h3>
                          <p class="fs-18"> <?php w($type); ?> </p>
                        </div>
                      </div>
                    </div>
                    <div class="col-lg-3 col-md-6 border-right">
                      <div class="row row align-items-center no-gutters">
                        <div class="col-auto">
                          <i class="fas fa-eye"></i>
                        </div>
                        <div class="col-auto  mt-3">
                          <h3 class="fs-12 mb-0"><?php w('Total Visits') ?>:</h3>
                          <p class="fs-18"><?php w(number_format($viewcount)); ?> </p>
                        </div>
                      </div>
                    </div>
                    <div class="col-lg-3 col-md-6">
                      <div class="row row align-items-center no-gutters">
                        <div class="col-auto">
                          <i class="fas fa-chart-bar"></i>
                        </div>
                        <div class="col-auto mt-3">
                          <h3 class="fs-12 mb-0"><?php w('Total Conversions'); ?>:</h3>
                          <p class="fs-18" data-temp="2019-10-14 17:14:25"> <?php w(number_format($sumconvertcount)); ?></p>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

            </div>
            <!--start-->
            <div class="row">

              <div class="col-lg-6 card-group">
                <div class="col-md-12 pt-4 pt-2 card shadow">
                  <div class="table-responsive">
                    <table class="table analytics_table">
                      <thead>
                        <th><?php w('Browser'); ?></th><!-- <th>Visits</th><th>Conversion</th> -->
                        <th class="text-right"><?php w('Unique visits'); ?></th>
                        <th class="text-right"><?php w('Unique Conversions'); ?></th>
                      </thead>
                      <?php
                      $qry = $mysqli->query("select count(`id`) as `countid`,sum(`convert_count`) as `total_convert`,`browser`  from  `" . $tablename . "` where `visit_pageid` in (" . $in_funnel . ")" . $date_search[1] . "  group by `browser`");
                      if ($qry->num_rows > 0) {
                        while ($res = $qry->fetch_assoc()) {
                          $browser = strtolower($res['browser']);
                          $icon = str_replace(" ", "-", $browser);
                          if (preg_match("/(netscape|maxthon|konqueror|unknown)+/", $icon)) {
                            $icon = "fas fa-question-alt";
                          } elseif (preg_match("/(mobile)+/", $icon)) {
                            $icon = "fas fa-mobile-alt";
                          } else {
                            $icon = "fab fa-" . $icon;
                          }
                      ?>
                          <tbody>
                            <td> <i class="mr-3 <?php echo $icon; ?>"></i> <?php echo t($res['browser']); ?></td>
                            <td class="text-right"><?php echo t(number_format($res['countid'])); ?></td>
                            <td class="text-right"><?php echo t(number_format($res['total_convert'])); ?></td>
                          <?php
                        }
                      } else {
                          ?>
                          <tr>
                            <td class="total-data" colspan=10><?php w('No Records Present'); ?></td>
                          </tr>
                        <?php
                      }
                        ?>
                          </tbody>
                    </table>
                  </div>
                </div>
              </div>
              <div class="col-lg-6 card-group">
                <div class="col-md-12 pt-4 pt-2 card shadow table-responsive">
                  <table class="table analytics_table">
                    <thead>
                      <th><?php w('Operating system') ?></th><!-- <th>Visits</th><th>Conversion</th> -->
                      <th class="text-right"><?php w('Unique visits') ?></th>
                      <th class="text-right"><?php w('Unique Conversions'); ?></th>
                    </thead>
                    <?php
                    $sql = $mysqli->query("select count(`id`) as `countid`,sum(`convert_count`) as `total_convert`,`os`  from  `" . $tablename . "` where `visit_pageid` in (" . $in_funnel . ")" . $date_search[1] . "  group by `os`");
                    if ($sql->num_rows > 0) {
                      while ($row = $sql->fetch_assoc()) {
                        $os = strtolower($row['os']);
                        $icon1 = "fab fa-" . $os;
                        if (preg_match("/(windows)+/", $os)) {
                          $icon1 = "fab fa-windows";
                        } elseif (preg_match("/(mac|iphone)+/", $os)) {
                          $icon1 = "fab fa-apple";
                        } elseif (preg_match("/(ipod|ipad|mobile)+/", $os)) {
                          $icon1 = "fab fa-mobile";
                        } elseif (preg_match("/(unknown)+/", $os)) {
                          $icon1 = "fas fa-question-alt";
                        }

                    ?>
                        <tbody>
                          <td><i class="mr-3 <?php echo $icon1; ?>"></i> <?php echo t($row['os']); ?></td>
                          <td class="text-right"><?php echo t(number_format($row['countid'])); ?></td>
                          <td class="text-right"><?php echo t(number_format($row['total_convert'])); ?></td>
                        <?php
                      }
                    } else {
                        ?>
                        <tr>
                          <td class="total-data" colspan=10><?php w('No Records Present') ?></td>
                        </tr>
                      <?php
                    }
                      ?>
                        </tbody>

                  </table>
                </div>
              </div>

              <div class="col-lg-6 card-group">
                <div class="col-md-12 pt-4 pt-2 card shadow">
                  <table class="table analytics_table">
                    <thead>
                      <th><?php w('Device'); ?></th><!-- <th>Visits</th><th>Conversion</th> -->
                      <th class="text-right"><?php w('Unique visits'); ?></th>
                      <th class="text-right"><?php w('Unique Conversions'); ?></th>
                    </thead>
                    <?php
                    $sql = $mysqli->query("select count(`id`) as `countid`,sum(`convert_count`) as `total_convert`,`device`  from  `" . $tablename . "` where `visit_pageid` in (" . $in_funnel . ")" . $date_search[1] . "  group by `device`");
                    if ($sql->num_rows > 0) {
                      while ($ress = $sql->fetch_assoc()) {
                    ?>
                        <tbody>
                          <td><?php
                              echo "<i class='mb-3 fas fa-" . strtolower($ress['device']) . "'></i>&nbsp;&nbsp;&nbsp;&nbsp;" . t($ress['device']);
                              ?></td>
                          <td class="text-right"><?php echo t(number_format($ress['countid'])); ?></td>
                          <td class="text-right"><?php echo t(number_format($ress['total_convert'])); ?></td>
                        <?php
                      }
                    } else {
                        ?>
                        <tr>
                          <td class="total-data" colspan=10><?php w('No Records Present'); ?></td>
                        </tr>
                      <?php
                    }
                      ?>
                        </tbody>
                  </table>
                </div>
              </div>
              <div class="col-lg-6 card-group">
                <div class="col-md-12 pt-4 pt-2 card shadow">
                  <table class="table analytics_table">
                    <thead>
                      <th><?php w('Location'); ?></th><!-- <th>Visits</th><th>Conversion</th> -->
                      <th class="text-right"><?php w('Unique visits'); ?></th>
                      <th class="text-right"><?php w('Unique Conversions'); ?></th>
                    </thead>
                    <?php
                    $sql = $mysqli->query("select count(`id`) as `countid`,sum(`convert_count`) as `total_convert`,`location`  from  `" . $tablename . "` where `visit_pageid` in (" . $in_funnel . ")" . $date_search[1] . "  group by `location`");
                    if ($sql->num_rows > 0) {
                      while ($ress = $sql->fetch_assoc()) {
                    ?>
                        <tbody>
                          <td><?php echo t($ress['location']); ?></td>
                          <td class="text-right"><?php echo t(number_format($ress['countid'])); ?></td>
                          <td class="text-right"><?php echo t(number_format($ress['total_convert'])); ?></td>
                        <?php
                      }
                    } else {
                        ?>
                        <tr>
                          <td class="total-data" colspan=10><?php w('No Records Present'); ?></td>
                        </tr>
                      <?php
                    }
                      ?>
                        </tbody>
                  </table>
                </div>
              </div>
            </div><!--end-->
          </div>
          <!--Page-->
          <div id="table" class="tab-pane fade">
            <?php
            //    echo $selectOption;
            $pages = '';
            //$date_search_1_temp=str_replace($tablename,'b',$date_search[1]);

            $total_countqry_visit = "select count(`id`) from `" . $tablename . "` where `visit_pageid` in (select `id` from `" . $page_table . "` where `filename`=`a`.filename and `funnelid`='" . $id . "')" . $date_search[1];

            $total_countqry_convert = "select sum(`convert_count`) from `" . $tablename . "` where `visit_pageid` in (select `id` from `" . $page_table . "` where `filename`=`a`.filename and `funnelid`='" . $id . "')" . $date_search[1];

            $pagecheckquery = $mysqli->query("select `id`,`filename`, `category`,(" . $total_countqry_visit . ") as `countid`,(" . $total_countqry_convert . ") as `total_convert` from `" . $page_table . "` as `a`  where `funnelid`='" . $id . "' and type='a'");

            while ($res = $pagecheckquery->fetch_assoc()) {
              $pages = $res['filename'];
              $in_page = "select `id` from `" . $page_table . "` where `funnelid`='" . $id . "' && `filename`='" . $pages . "'";
            ?>

              <div class="container-fluid p-0" onmousemove="displayDetail(<?php echo $res['id']; ?>,'block')" onmouseout="displayDetail(<?php echo $res['id']; ?>,'none')">
                <div class="card funnel_post shadow mt-3" style="position:relative;">
                  <div class="card-body">
                    <div class="row align-items-center">
                      <div class="col-lg-3 col-md-6 border-right">
                        <div class="row row align-items-center no-gutters">
                          <div class="col-auto">
                            <i class="fas fa-file"></i>
                          </div>
                          <div class="col-auto  mt-3">
                            <h3 class="fs-12 mb-0"><?php w('Page'); ?></h3>
                            <p class="fs-18"><a href="<?php echo $funnel_baseurl . "/" . $pages . "/"; ?>" target="_BLANK"><?php echo $pages; ?></a> </p>
                          </div>
                        </div>
                      </div>
                      <div class="col-lg-3 col-md-6 border-right">
                        <div class="row row align-items-center no-gutters">
                          <div class="">
                            <i class="fas fa-list"></i>
                          </div>
                          <div class="col-auto  mt-3">
                            <h3 class="fs-12 mb-0"><?php w('Category'); ?>:</h3>
                            <p class="fs-18"> <?php echo t($res['category']); ?> </p>
                          </div>
                        </div>
                      </div>
                      <div class="col-lg-3 col-md-6 border-right">
                        <div class="row row align-items-center no-gutters">
                          <div class="col-auto">
                            <i class="fas fa-eye"></i>
                          </div>
                          <div class="col-auto  mt-3">
                            <h3 class="fs-12 mb-0"><?php w('Total Visits'); ?>:</h3>
                            <p class="fs-18"><?php echo t(number_format($res['countid'])); ?> </p>
                          </div>
                        </div>
                      </div>
                      <div class="col-lg-3 col-md-6">
                        <div class="row row align-items-center no-gutters">
                          <div class="col-auto">
                            <i class="fas fa-chart-bar"></i>
                          </div>
                          <div class="col-auto mt-3">
                            <h3 class="fs-12 mb-0"><?php w('Total Conversions'); ?>:</h3>
                            <p class="fs-18" data-temp="2019-10-14 17:14:25"> <?php echo t(number_format($res['total_convert'])); ?></p>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="cover-div-<?php echo $res['id']; ?>" style="background-color:#00001a;opacity:0.5;width:100%;height:100%;top:0px;position:absolute;border-radius:16px;display:none;">
                  </div>
                  <button class="btn btn-primary cover-btn-<?php echo $res['id']; ?>" style="top:50%;left:50%;transform:translate(-50%,-50%);position:absolute;font-size:15px;min-width:250px !important;display:none;"> <?php w('View Detail'); ?></button>
                </div>

              </div>
              <!--start-->
              <div class="row pagestatall" id="detail-page-<?php echo $res['id']; ?>">

                <div class="col-lg-6 card-group">
                  <div class="col-md-12 pt-4 pt-2 card shadow">
                    <div class="table-responsive">
                      <table class="table analytics_table">
                        <thead>
                          <th><?php w('Browser'); ?></th><!-- <th>Visits</th><th>Conversion</th> -->
                          <th class="text-right"><?php w('Unique visits'); ?></th>
                          <th class="text-right"><?php w('Unique Conversions'); ?></th>
                        </thead>
                        <?php
                        $qry = $mysqli->query("select count(`id`) as `countid`,sum(`convert_count`) as `total_convert`,`browser`  from  `" . $tablename . "` where `visit_pageid` in (" . $in_page . ")" . $date_search[1] . "  group by `browser`");
                        if ($qry->num_rows > 0) {
                          while ($res = $qry->fetch_assoc()) {
                            $browser = strtolower($res['browser']);
                            $icon = str_replace(" ", "-", $browser);
                            if (preg_match("/(netscape|maxthon|konqueror|unknown)+/", $icon)) {
                              $icon = "fas fa-question-alt";
                            } elseif (preg_match("/(mobile)+/", $icon)) {
                              $icon = "fas fa-mobile-alt";
                            } else {
                              $icon = "fab fa-" . $icon;
                            }

                        ?>
                            <tbody>
                              <td> <i class="mr-3 <?php echo $icon; ?>"></i> <?php echo t($res['browser']); ?></td>
                              <td class="text-right"><?php echo t(number_format($res['countid'])); ?></td>
                              <td class="text-right"><?php echo t(number_format($res['total_convert'])); ?></td>
                            <?php
                          }
                        } else {
                            ?>
                            <tr>
                              <td class="total-data" colspan=10><?php w('No Records Present'); ?></td>
                            </tr>
                          <?php
                        }
                          ?>
                            </tbody>
                      </table>
                    </div>
                  </div>
                </div>
                <div class="col-lg-6 card-group">
                  <div class="col-md-12 pt-4 pt-2 card shadow table-responsive">
                    <table class="table analytics_table">
                      <thead>
                        <th><?php w('Operating system'); ?></th><!-- <th>Visits</th><th>Conversion</th> -->
                        <th class="text-right"><?php w('Unique visits'); ?></th>
                        <th class="text-right"><?php w('Unique Conversions'); ?></th>
                      </thead>
                      <?php
                      $sql = $mysqli->query("select count(`id`) as `countid`,sum(`convert_count`) as `total_convert`,`os`  from  `" . $tablename . "` where `visit_pageid` in (" . $in_page . ")" . $date_search[1] . "  group by `os`");
                      if ($sql->num_rows > 0) {
                        while ($row = $sql->fetch_assoc()) {
                          $os = strtolower($row['os']);
                          $icon1 = "fab fa-" . $os;
                          if (preg_match("/(windows)+/", $os)) {
                            $icon1 = "fab fa-windows";
                          } elseif (preg_match("/(mac|iphone)+/", $os)) {
                            $icon1 = "fab fa-apple";
                          } elseif (preg_match("/(ipod|ipad|mobile)+/", $os)) {
                            $icon1 = "fab fa-mobile";
                          } elseif (preg_match("/(unknown)+/", $os)) {
                            $icon1 = "fas fa-question-alt";
                          }
                      ?>
                          <tbody>
                            <td><i class="mr-3 <?php echo $icon1; ?>"></i> <?php echo t($row['os']); ?></td>
                            <td class="text-right"><?php echo t(number_format($row['countid'])); ?></td>
                            <td class="text-right"><?php echo t(number_format($row['total_convert'])); ?></td>
                          <?php
                        }
                      } else {
                          ?>
                          <tr>
                            <td class="total-data" colspan=10><?php w('No Records Present'); ?></td>
                          </tr>
                        <?php
                      }
                        ?>
                          </tbody>

                    </table>
                  </div>
                </div>

                <div class="col-lg-6 card-group">
                  <div class="col-md-12 pt-4 pt-2 card shadow">
                    <table class="table analytics_table">
                      <thead>
                        <th><?php w('Device'); ?></th><!-- <th>Visits</th><th>Conversion</th> -->
                        <th class="text-right"><?php w('Unique visits'); ?></th>
                        <th class="text-right"><?php w('Unique Conversions'); ?></th>
                      </thead>
                      <?php
                      $sql = $mysqli->query("select count(`id`) as `countid`,sum(`convert_count`) as `total_convert`,`device`  from  `" . $tablename . "` where `visit_pageid` in (" . $in_page . ")" . $date_search[1] . "  group by `device`");
                      if ($sql->num_rows > 0) {
                        while ($ress = $sql->fetch_assoc()) {
                      ?>
                          <tbody>
                            <td><?php
                                echo "<i class='mb-3 fas fa-" . strtolower($ress['device']) . "'></i>&nbsp;&nbsp;&nbsp;&nbsp;" . t($ress['device']);
                                ?></td>
                            <td class="text-right"><?php echo t(number_format($ress['countid'])); ?></td>
                            <td class="text-right"><?php echo t(number_format($ress['total_convert'])); ?></td>
                          <?php
                        }
                      } else {
                          ?>
                          <tr>
                            <td class="total-data" colspan=10><?php w('No Records Present'); ?></td>
                          </tr>
                        <?php
                      }
                        ?>
                          </tbody>
                    </table>
                  </div>
                </div>
                <div class="col-lg-6 card-group">
                  <div class="col-md-12 pt-4 pt-2 card shadow">
                    <table class="table analytics_table">
                      <thead>
                        <th><?php w('Location'); ?></th><!-- <th>Visits</th><th>Conversion</th> -->
                        <th class="text-right"><?php w('Unique visits'); ?></th>
                        <th class="text-right"><?php w('Unique Conversion'); ?></th>
                      </thead>
                      <?php
                      $sql = $mysqli->query("select count(`id`) as `countid`,sum(`convert_count`) as `total_convert`,`location`  from  `" . $tablename . "` where `visit_pageid` in (" . $in_page . ")" . $date_search[1] . "  group by `location`");
                      if ($sql->num_rows > 0) {
                        while ($ress = $sql->fetch_assoc()) {
                      ?>
                          <tbody>
                            <td><?php echo t($ress['location']); ?></td>
                            <td class="text-right"><?php echo t(number_format($ress['countid'])); ?></td>
                            <td class="text-right"><?php echo t(number_format($ress['total_convert'])); ?></td>
                          <?php
                        }
                      } else {
                          ?>
                          <tr>
                            <td class="total-data" colspan=10><?php w('No Records Present'); ?></td>
                          </tr>
                        <?php
                      }
                        ?>
                          </tbody>
                    </table>
                  </div>
                </div>
              </div><!--end-->


            <?php } ?>


          <?php

        } ?>
          </div>
        </div>
      </div>
    </div>

</div>
<script type="text/javascript">
  $(document).ready(function() {
    $('#dtHorizontalExample').DataTable({
      "scrollX": true
    });
  });
</script>



<style type="text/css">
  .analytics_table tbody+tbody {
    border-top: none;
  }

  .funnel_post {
    border-radius: 20px;
  }

  .funnel_post i {

    font-size: 28px;
    margin-right: 10px;
    color: #1fa2ff;
    display: block;
    background: #1f57ca;
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
  }

  .fs-12 {
    font-size: 14px !important;
  }

  .fs-18 {
    font-size: 18px !important;
  }

  .subtitle {
    color: #515455;
    font-family: poppins, san-serif;
    font-size: 20px;
    font-weight: 400 !important;
  }


  .dtHorizontalExampleWrapper {
    max-width: 600px;
    margin: 0 auto;
  }

  #dtHorizontalExample th,
  td {
    white-space: nowrap;
  }

  .cover-div,
  .cover-btn {
    opacity: 0;
  }

  .cover-div:hover,
  .cover-btn:hover {
    opacity: 1 !important;
  }

  @keyframes opacity-animation {
    from {
      opacity: 0
    }

    to {
      opacity: 1
    }
  }

  .show-opacity-animation {
    animation-name: opacity-animation;
    animation-duration: 1.5s;
  }
  .select-funnel-label{
    padding-left: 10px; 
    font-size: 20px!important;
  }
  .tab-border{
    border: 0px !important;
  }
</style>
<script>
  function displayDetail(id, doo = "block") {
    document.getElementsByClassName("cover-div-" + id)[0].style.display = doo;
    document.getElementsByClassName("cover-btn-" + id)[0].style.display = doo;
    document.getElementsByClassName("cover-btn-" + id)[0].onclick = function() {
      var doc_arr = document.querySelectorAll("#detail-page-" + id + " .col-lg-6");
      for (var i = 0; i < doc_arr.length; i++) {
        var current = (doc_arr[i].style.display == "none") ? "block" : "none";
        doc_arr[i].style.display = current;

        if (current == "block") {
          //opacity-animation
          doc_arr[i].classList.add('show-opacity-animation');
        } else {
          doc_arr[i].classList.remove('show-opacity-animation');
        }
      }
    };
  }
  try {
    var doc_arr = document.querySelectorAll(".pagestatall .col-lg-6");
    for (var i = 0; i < doc_arr.length; i++) {
      doc_arr[i].style.display = "none";
    }
  } catch (errr) {
    console.log(errr.message);
  }
</script>