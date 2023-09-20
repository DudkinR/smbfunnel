<?php
global $mysqli;
global $dbpref;
$table = $dbpref . "scarcity_jeet";

$returnOptions = $mysqli->query("SELECT * FROM `" . $table . "`");
$data = $returnOptions->fetch_assoc();

$date = date("Y/m/w/d/h/M/s/l");
?>
<div name="shortCode" id="cf_scarcity_jeet_shortCode" class="shortCode row animated <?= $data['effect'] ?>" data-effect="<?= $data['effect'] ?>">
  <div name="CatchLine" id="cf_scarcity_jeet_CatchLine" class="cf_scarcity_jeet_box col-sm-3" data-effect="none">
    <div class="CatchLine">
      <h3 class="CatchLine_h3" style="margin-top: 10px;"> <?= $data['CatchLine']; ?></h3>
    </div>
  </div>
  <div name="end_date" id="cf_scarcity_jeet_end_date" class="cf_scarcity_jeet_box col-sm-4" data-effect="none">

    <div class="end_date">
      <h3 id="cf_scarcity_jeet_demo" style="margin-top: 10px;"></h3>
      <script class="cf_scarcity_jeet_box">
        var countDownDate = new Date("<?= $data['end_date'] ?>").getTime();
        var timezone = <?= $data['timezone'] ?>;

        var x = setInterval(function() {
          var now = new Date();
          let next = new Date(now.getTime() + timezone * 60000);
          var distance = countDownDate - next;
          var days = Math.floor(distance / (1000 * 60 * 60 * 24));
          var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
          var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
          var seconds = Math.floor((distance % (1000 * 60)) / 1000);

          document.getElementById("cf_scarcity_jeet_demo").innerHTML = "<b>" + days + "</b>" + "<small> : D </small>" + ' &nbsp ' + "<b>" + hours + "</b>" + "<small> : H </small>" + ' &nbsp ' + "<b>" + minutes + "</b>" + "<small> : M </small>" + ' &nbsp ' + "<b>" + seconds + "</b>" + "<small> : S </small>";

          if (distance < 0) {
            clearInterval(x);
            document.getElementById("cf_scarcity_jeet_demo").innerHTML = "EXPIRED";
          }
        }, 1000);

        function hreffunction() {
          window.open("<?php echo $data['button_link'] ?>", "_blank");
        }

        function hrefimage() {
          window.open("<?php echo $data['product_link'] ?>", "_blank");
        }
        window.onload = function() {
          setInterval(function() {
            $("#cf_scarcity_jeet_shortCode").show();
          }, <?= $data['effect_delay'] ?>);
        };
        window.onload = function() {
          setInterval(function() {
            $("#cf_scarcity_jeet_shortCode").show();
          }, <?= $data['effect_transition'] ?>);
        };
      </script>

    </div>

  </div>
  <div name="action_button_text" id="action_button_text" class="cf_scarcity_jeet_box col-sm-3" data-effect="none">
    <?php if ($data['show_action_button'] == 'true') {  ?>
      <div class="action_button_text">
        <button id="cf_scarcity_jeet_button" onclick="hreffunction()"> <?= $data['action_button_text']; ?> </button>
      </div>
    <?php } ?>
  </div>
  <?php if ($data['product_box_show'] == 'true') {  ?>
    <div id="cf_scarcity_jeet_image" class="cf_scarcity_jeet_box col-sm-2">
      <img id="cf_scarcity_jeet_product_box_image" height="80px" onclick="hrefimage()" src="<?= $data['product_box_image']; ?>" alt="" srcset="">
    </div>
  <?php } ?>

</div>

<style>
  #action_button_text,
  #cf_scarcity_jeet_CatchLine,
  #cf_scarcity_jeet_end_date,
  #cf_scarcity_jeet_image {
    display: flex;
    align-items: center;
    justify-content: center;
  }
  #cf_scarcity_jeet_shortCode::after {
    content: "";
    clear: both;
    display: table;
  }

  #cf_scarcity_jeet_shortCode.shortCode {
    height: 80px;
    width: 98%;
    margin: 0 1% 0 1%;
    border: 1px solid;
    position: fixed;
    left: 0;
    bottom: 0;
    background-image: linear-gradient(<?= $data['Bar_Gradient1']  ?>, <?= $data['Bar_Gradient1_1']  ?>);
  }

  .col-1 {
    width: 8.33%;
  }

  #cf_scarcity_jeet_form {
    text-align: center;
    font-size: inherit;
    box-sizing: border-box;
  }

  #cf_scarcity_jeet_button {
    border-radius: 10px;
    padding: 10px 20px 10px 20px;
    font-size: inherit;
    color: <?= $data['action_button_text_color']; ?>;
    background-color: <?= $data['action_Background_color']; ?>
  }

  #cf_scarcity_jeet_demo {
    color: <?= $data['timer_text_color']; ?>
  }

  .CatchLine_h3 {
    color: <?= $data['catch_line_color']; ?>;
    font-family: <?= $data['Catch_line_font']; ?>;
    font-weight: <?= $data['Catch_line_style']; ?>
  }
  .CatchLine_h3 {
    color: <?= $data['catch_line_color']; ?>;
    font-family: <?= $data['Catch_line_font']; ?>;
    font-weight: <?= $data['Catch_line_style']; ?>;
  }
  
  @media screen and (max-width: 678px) {
    #cf_scarcity_jeet_shortCode.shortCode {
      position: unset;
    }
  }

  @media screen and (max-width: 935px) and (min-width: 572px) {
    #cf_scarcity_jeet_form {
      font-size: 18px;
    }

    #cf_scarcity_jeet_product_box_image {
      height: 75px;
    }

    #cf_scarcity_jeet_button {
      width: 140px;
      height: 45px;            
      color: <?= $data['action_button_text_color']; ?>;
      background-color: <?= $data['action_Background_color']; ?>
    }
  }
  @media screen and (max-width: 880px) and (min-width: 570px) {
    #cf_scarcity_jeet_shortCode.shortCode {
      height: 200px;
    }
  }
  @media screen and (max-width: 570px) {
    #cf_scarcity_jeet_shortCode.shortCode {
      height: 100%;
    }
  }
</style>