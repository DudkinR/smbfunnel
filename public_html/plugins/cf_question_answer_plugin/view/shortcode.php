<?php

if( $sql2->num_rows > 0 && $settings = $sql2->fetch_assoc(  ) )
{
    $que_font   = $settings['que_font'];
    $ans_font   = $settings['ans_font'];
    $que_tcolor = $settings['que_tcolor'];
    $ans_tcolor = $settings['ans_tcolor'];
    $que_bg     = $settings['que_bg']; 
    $ans_bg     = $settings['ans_bg'];

}else{
    $que_font   = 16;
    $ans_font   = 15;
    $que_tcolor = '#195391f5';
    $ans_tcolor = '#1a19188f';
    $que_bg     = '#FFFFFF'; 
    $ans_bg     = '#FFFFFF';
}

$random = bin2hex(random_bytes(5));
$cur_url = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
$cur_ur = parse_url($cur_url);
if( isset( $cur_ur['query'] ) )
{
    $newrevurl = $cur_url."&cfquestion_do_login=".$random;
}else{
    $newrevurl = $cur_url."?cfquestion_do_login=".$random;
}

$login_url = get_login_action_url( $funnel_id,$newrevurl,true );

if( is_member_loggedin($funnel_id) )
{
    
    $mdata  = get_current_member($funnel_id);
    $midd   = $mdata['id'];
    $memail  = $mdata['email'];
    if( empty( $mdata['name'] ) )
    {
        $exf = json_decode( $mdata['exf'] );
        if( isset( $exf->firstname ) )
        {
            $mname  = $exf->firstname;
            if( isset( $exf->lastname ) )
            {
                $mname   .= " ".$exf->lastname;
            }
        }
    }else{
        $mname   = $mdata['name'];
    }
    set_session('cfprrev_mdata',array('email'=>$memail,'name'=>$mname));
}else{
    $midd  = '';
    $memail = '';
    $mname  = '';
}

?>

<div class="cfquestion_main  p-0 p-sm-5" id="cfqandanswer">
  <div class="row cfquestion_main_heading pb-1">
    <div class=" col-sm-8">
      <h4>Customer Questions & Answers</h4>
    </div>
    <div class="col-md-4">
      <?php  if( is_member_loggedin( $funnel_id ) ){  ?>
      <button type="button" class="btn btn-secondary" data-toggle="modal" data-target="#add_data_Modal">
        Post Question
      </button>
      <?php } else{ ?>
        <a type="button" class="btn btn-secondary" href="<?=$login_url ?>">
        Post Question
        </a>
      <?php } ?>
    </div>
  </div>
  <div id="cfq_qandadiv_c">
    <?php
    if ( $postCount > 0 ) {
      while ($row = $sql->fetch_assoc()) {
        $newDate = date("d-m-Y", strtotime( $row["added_on"] ) );
        if ( $row['status'] == 1 ){ ?>
            <div id="cfquestion_qandadiv">
              <div class="cfquestion_question col-sm-8">
                <div class="d-flex">
                  <div class="cfquestion_question_q">
                    <span style="font-weight: bold;">Question:</span>
                  </div>
                  <div class="cfquestion_question_qd">
                    <p><span style="font-weight:500;color:<?= $que_tcolor; ?>;font-size:<?= $que_font; ?>px;background-color:<?= $que_bg; ?>;"><?= $row['question']; ?></span></p>
                  </div>
                </div>
              </div>
              <div class="cfquestion_answer col-sm-8">
                <div class="d-flex">
                  <div class="cfquestion_question_a">
                    <span style="font-weight: bold;">Answer:</span>
                  </div>
                  <div class="cfquestion_question_ans">
                    <p><span style="font-weight:500;color:<?= $ans_tcolor; ?>;font-size:<?= $ans_font; ?>px;background-color:<?= $ans_bg; ?>;"><?= $row['answer']; ?></span></p>
                  </div>
                </div>
              </div>
            </div>
            <?php
          }
        }
      }
    ?>
  </div>
  <?php if( $postCount >5 ){  ?>
  <div class="mt-2 ">
    <button type="button" id="cfq_loadBtn" >See more answered questions</button>
  </div>
  <div class="loadmore">
    <input type="hidden" id="postCount" value="0">
  </div>
  <?php } ?>
</div>

<!-- Modal -->
<div class="modal fade" id="add_data_Modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Post Question</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <div class="modal-body">
        <form id="cf_ques_form" method="POST">
          <input type="hidden" class="form-control" id="product_id" name="product_id" value="<?= $pid ?>" required />
          <input type="hidden" class="form-control" id="product_title" name="product_title" value="<?=$pro_title;?>" required />
          <input type="hidden"  name="name" value="<?=$mname; ?>" required />
          <input type="hidden"  name="email" value="<?=$memail; ?>" required />
          <div class="form-group">
            <label for="text">Write Question Here:</label>
            <textarea rows="4" cols="50" class="form-control" id="question" placeholder="Enter question" name="question" required></textarea>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-primary" id="cf_save_ques" name="submit">Post Question</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
<!-- modal end -->
<input type="hidden" id="cf_ques_ajax" value="<?php echo get_option('install_url') . "/index.php?page=ajax"; ?>">