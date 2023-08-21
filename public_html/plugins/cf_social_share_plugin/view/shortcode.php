<?php
global $mysqli;
global $dbpref;
global $app_variant;
$table = $dbpref . 'cf_social_setting';

$sql1 = $mysqli->query("SELECT * FROM `" . $table . "` ");

if ($sql1->num_rows > 0 && $settings = $sql1->fetch_assoc()) {
  $icon_shape = $settings['icon_shape'];
  $display = $settings['display'];
  $icon_color = $settings['icon_color'];
  $icon_color = $settings['icon_color'];
  $object = json_decode($icon_color);
  $facbook = $object->icon_facebook;
  $twitter = $object->icon_twitter;
  $instagram = $object->icon_instagram;
  $youtube = $object->icon_youtube;
  $google = $object->icon_google;
  $pinterest = $object->icon_pinterest;
  $linkedin = $object->icon_linkedin;
  $whatsapp = $object->icon_whatsapp;
  $skype = $object->icon_skype;
  $tumblr = $object->icon_tumblr;
  $yahoo = $object->icon_yahoo;
  $reddit = $object->icon_reddit;
  $digg = $object->icon_digg;
  $blogger = $object->icon_blogger;
  $buffer = $object->icon_buffer;
  $vkontakte = $object->icon_vkontakte;
  $xing = $object->icon_xing;
  $telegram = $object->icon_telegram;
} else {
  $icon_shape = '0';
  $display = 'block';
  $facbook = "#3B5998";
  $twitter = "#55ACEE";
  $instagram = " #fa55d4";
  $youtube = "#bb0000;";
  $google = "#ed1710";
  $pinterest = "#cb2027";
  $linkedin = "#00aff0";
  $whatsapp = "#13f848";
  $skype = "#00aff0";
  $tumblr = "#2c4762";
  $yahoo = "#430297";
  $reddit = "#ff5700";
  $digg = "#2217FF";
  $blogger = "#FF930D";
  $buffer = "#FFFBFA";
  $vkontakte = "#1f38b5";
  $xing = "#23825A";
  $telegram = "#559ede";
}

$table2 = $dbpref . 'cf_social_share';
$sql2 = $mysqli->query("SELECT * FROM `" . $table2 . "` LIMIT 5");
$num_rows = mysqli_num_rows($sql2);
$num_rows2 = mysqli_num_rows($sql2);

?>

<style>
  .share_btn {
    margin-left: 5px;
    margin-bottom: 12px;
    width: 60px;
    height: 60px;
    border-radius: <?php echo $icon_shape ?>%
  }

  .social_icons .fab {
    padding: 18px;
    font-size: 25px;
    margin: 5px;
    width: 60px;
    border-radius: <?php echo $icon_shape ?>%
  }

  .social_icons {
    left: 0;
    position: fixed;
    top: 30%;
  }

  .social_icons ul {
    list-style-type: none;
    margin: 0;
    padding: 0;
    display: <?php echo $display ?>;


  }




  .social_icons .fa-facebook {
    background: <?php echo $facbook ?> !important;
    color: white !important;

  }

  .social_icons .fa-twitter {
    background: <?php echo $twitter ?> !important;
    color: white !important;
  }

  .social_icons .fa-google {
    background: <?php echo $google ?> !important;
    color: white !important;
  }

  .social_icons .fa-whatsapp {
    background: <?php echo $whatsapp ?> !important;
    color: white !important;
  }

  .social_icons .fa-linkedin {
    background: <?php echo $linkedin ?> !important;
    color: white !important;
  }

  .social_icons .fa-youtube {
    background: <?php echo $youtube ?> !important;
    color: white !important;
  }

  .social_icons .fa-instagram {
    background: <?php echo $instagram ?> !important;
    color: white !important;
  }

  .social_icons .fa-pinterest {
    background: <?php echo $pinterest ?> !important;
    color: white !important;
  }

  .social_icons .fa-skype {
    background: <?php echo $skype ?> !important;
    color: white !important;
  }

  .social_icons .fa-tumblr {
    background: <?php echo $tumblr ?> !important;
    color: white !important;
  }

  .social_icons .fa-yahoo {
    background: <?php echo $yahoo ?> !important;
    color: white !important;
  }

  .social_icons .fa-reddit {
    background: <?php echo $reddit ?> !important;
    color: white !important;
  }

  .social_icons .fa-digg {
    background: <?php echo $digg ?> !important;
    color: white !important;
  }

  .social_icons .fa-blogger {
    background: <?php echo $blogger ?> !important;
    color: white !important;
  }

  .social_icons .fa-buffer {
    background: #000000 !important;
    color: <?php echo $buffer ?> !important;
  }

  .social_icons .fa-vk {
    background: <?php echo $vkontakte ?> !important;
    color: white !important
  }

  .social_icons .fa-xing {
    background: <?php echo $xing ?> !important;
    color: white !important;
  }

  .social_icons .fa-telegram {
    background: <?php echo $telegram ?> !important;
    color: white !important;
  }

  /* ###################################### */

  .modal_icons .fa-facebook {
    background: <?php echo $facbook ?> !important;
    color: white !important;
  }

  .modal_icons .fa-twitter {
    background: <?php echo $twitter ?> !important;
    color: white !important;
  }

  .modal_icons .fa-google {
    background: <?php echo $google ?> !important;
    color: white !important;
  }

  .modal_icons .fa-whatsapp {
    background: <?php echo $whatsapp ?> !important;
    color: white !important;
  }

  .modal_icons .fa-linkedin {
    background: <?php echo $linkedin ?> !important;
    color: white !important
  }

  .modal_icons .fa-youtube {
    background: <?php echo $youtube ?> !important;
    color: white !important;
  }

  .modal_icons .fa-instagram {
    background: <?php echo $instagram ?> !important;
    color: white !important;
  }

  .modal_icons .fa-pinterest {
    background: <?php echo $pinterest ?> !important;
    color: white !important;
  }

  .modal_icons .fa-skype {
    background: <?php echo $skype ?> !important;
    color: white !important;
  }

  .modal_icons .fa-tumblr {
    background: <?php echo $tumblr ?> !important;
    color: white !important;
  }

  .modal_icons .fa-yahoo {
    background: <?php echo $yahoo ?> !important;
    color: white !important;
  }

  .modal_icons .fa-reddit {
    background: <?php echo $reddit ?> !important;
    color: white !important;
  }

  .modal_icons .fa-digg {
    background: <?php echo $digg ?> !important;
    color: white !important;
  }

  .modal_icons .fa-blogger {
    background: <?php echo $blogger ?> !important;
    color: white !important;
  }

  .modal_icons .fa-buffer {
    background: #000000 !important;
    color: <?php echo $buffer ?> !important;
  }

  .modal_icons .fa-vk {
    background: <?php echo $vkontakte ?> !important;
    color: white !important;
  }

  .modal_icons .fa-xing {
    background: <?php echo $xing ?> !important;
    color: white !important;
  }

  .modal_icons .fa-telegram {
    background: <?php echo $telegram ?> !important;
    color: white !important;
  }

  .submit_btn {
    margin-right: 970px;
  }

  .social_icons a {
    text-decoration: none;
  }
</style>
<style>


<?php
// echo $display."hello";

if($display == 'inline-block'){
  ?>
  .social_icons {
    position: relative !important;
  }
  <?php

}

?>
</style>


<div class="social_icons">
  <?php
  if ($num_rows > 0) {

    while ($row = mysqli_fetch_array($sql2)) {

      $str = $row['network_name'];
      $object = json_decode($str);
      $icon = $object->icon;
      $url = $object->url;
      $actual_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

  ?>
      <ul>
        <li><a href="<?= $url . urlencode($actual_link); ?>" class="<?= $icon; ?>" target="popup" onclick="window.open('<?= $url . urlencode($actual_link); ?>','name','width=600,height=400')"></a>
        </li>
      </ul>

  <?php  }
  } ?>
  <button class="share_btn btn fa fa-share-alt" data-bs-toggle="modal" data-target=".bd-example-modal-lg"></button>
</div>




<div class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
      
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="modal_icons" id="modal_icons">

          <?php
          $table3 = $dbpref . 'cf_social_share';
          $sql3 = $mysqli->query("SELECT * FROM `" . $table3 . "`");
          $num_rows3 = mysqli_num_rows($sql3);
          if ($num_rows3 > 0) {

            while ($row3 = mysqli_fetch_array($sql3)) {

              $str3 = $row3['network_name'];

              $object3 = json_decode($str3);
              $icon3 = $object3->icon;
              $url3 = $object3->url;
              $actual_link3 = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

          ?>
              <a href="<?= $url3 . urlencode($actual_link3); ?>" class="<?= $icon3; ?>" target="popup" onclick="window.open('<?= $url3 . urlencode($actual_link3); ?>','name','width=600,height=400')"></a>
          <?php
            }
          }
          ?>
        </div>
      </div>
    </div>
  </div>
</div>