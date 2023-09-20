<?php
global $mysqli;
global $dbpref;

$table = $dbpref . "scarcity_jeet";

$returnOptions = $mysqli->query("SELECT * FROM `" . $table . "`");
$data = $returnOptions->fetch_assoc();
$date = date("Y/m/w/d/h/M/s/l");
?>

<div name="shortCode" id="cf_scarcity_jeet_shortCode" class="row shortCode animated <?= $data['effect'] ?>" data-effect="<?= $data['effect'] ?>">

  <div name="CatchLine" id="cf_scarcity_jeet_CatchLine" class="cf_scarcity_jeet_box col-sm-3" data-effect="none">
    <div class="CatchLine">
      <h2 class="CatchLine_h3"> <?= $data['CatchLine']; ?></h2>
    </div>
  </div>
  <div name="end_date" id="cf_scarcity_jeet_end_date" class="cf_scarcity_jeet_box col-sm-4" data-effect="none">
    <div class="end_date">
      <p style="color:#<?= $data['timer_text_color']; ?>"></p>
      <div id="cf_scarcity_jeet_clockdiv">
        <div>
          <span class="days"></span>
          <div class="cf_scarcity_jeet_smalltext">Days</div>
        </div>
        <div>
          <span class="hours"></span>
          <div class="cf_scarcity_jeet_smalltext">Hours</div>
        </div>
        <div>
          <span class="minutes"></span>
          <div class="cf_scarcity_jeet_smalltext">Minutes</div>
        </div>
        <div>
          <span class="seconds"></span>
          <div class="cf_scarcity_jeet_smalltext">Seconds</div>
        </div>
      </div>

      <script class="cf_scarcity_jeet_box">
        function getTimeRemaining(endtime, timezone) {
          var now = new Date();
          const total = Date.parse(endtime) - Date.parse(new Date(now.getTime() + timezone * 60000));
          const seconds = Math.floor((total / 1000) % 60);
          const minutes = Math.floor((total / 1000 / 60) % 60);
          const hours = Math.floor((total / (1000 * 60 * 60)) % 24);
          const days = Math.floor(total / (1000 * 60 * 60 * 24));

          return {
            total,
            days,
            hours,
            minutes,
            seconds
          };
        }

        function initializeClock(id, endtime, timezone) {
          const clock = document.getElementById(id);
          const daysSpan = clock.querySelector('.days');
          const hoursSpan = clock.querySelector('.hours');
          const minutesSpan = clock.querySelector('.minutes');
          const secondsSpan = clock.querySelector('.seconds');

          function updateClock() {

            const t = getTimeRemaining(endtime, timezone);

            daysSpan.innerHTML = t.days;
            hoursSpan.innerHTML = ('0' + t.hours).slice(-2);
            minutesSpan.innerHTML = ('0' + t.minutes).slice(-2);
            secondsSpan.innerHTML = ('0' + t.seconds).slice(-2);

            if (t.total <= 0) {
              clearInterval(timeinterval);
            }
          }

          updateClock();
          const timeinterval = setInterval(updateClock, 1000);

        }

        const deadline = new Date(Date.parse("<?= $data['end_date'] ?>"));
        var timezone = <?= $data['timezone'] ?>;
        initializeClock('cf_scarcity_jeet_clockdiv', deadline, timezone);

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
  <div name="action_button_text" id="action_button_text" class="cf_scarcity_jeet_box col-sm-2" data-effect="none">
    <?php if ($data['show_action_button'] == 'true') {  ?>
      <div class="action_button_text">
        <button id="cf_scarcity_jeet_button" onclick="hreffunction()"> <?= $data['action_button_text']; ?> </button>
      </div>
    <?php } ?>
  </div>
  <?php if ($data['product_box_show'] == 'true') {  ?>
    <div id="cf_scarcity_jeet_image" class="cf_scarcity_jeet_box col-sm-3">
      <img id="cf_scarcity_jeet_product_box_image" height="95px" onclick="hrefimage()" src="<?= $data['product_box_image']; ?>" alt="" srcset="">
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

  #cf_scarcity_jeet_product_box_image {
    box-sizing: border-box;
  }

  #cf_scarcity_jeet_shortCode::after {
    content: "";
    clear: both;
    display: table;
  }

  #cf_scarcity_jeet_shortCode.shortCode {
    width: 100%;
    margin-left: 0px;
    margin-right: 0px;
    height: 95px;
    position: fixed;
    left: 0;
    bottom: 0;
    background-image: linear-gradient(<?= $data['Bar_Gradient1']  ?>, <?= $data['Bar_Gradient1_1']  ?>);
    width: 100%;
  }



  #cf_scarcity_jeet_form {
    text-align: center;
    font-size: 25px;
    box-sizing: border-box;
  }


  #cf_scarcity_jeet_product_box_image {
    vertical-align: middle;
    border-style: none;
    height: 100px;
  }


  #cf_scarcity_jeet_button {
    width: 200px;
    height: 50px;
    border-radius: 10px;
    font-size: 20px;
    color: <?= $data['action_button_text_color']; ?>;
    background-color: <?= $data['action_Background_color']; ?>
  }

  #cf_scarcity_jeet_demo {
    color: <?= $data['timer_text_color']; ?>
  }

  #cf_scarcity_jeet_clockdiv {
    font-family: sans-serif;
    display: inline-block;
    font-weight: 100;
    text-align: center;
    font-size: 30px;

  }

  #cf_scarcity_jeet_clockdiv>div {
    padding: 5px;
    border-radius: 5px;
    background: white;
    display: inline-block;
    line-height: normal;
  }

  .cf_scarcity_jeet_smalltext {
    font-size: 16px;
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

  @media screen and (max-width: 570px) {
    #cf_scarcity_jeet_shortCode.shortCode {
      height: 100%;
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
      width: 150px;
      height: 35px;
      border-radius: 10px;
      font-size: 18px;
      color: <?= $data['action_button_text_color']; ?>;
      background-color: <?= $data['action_Background_color']; ?>
    }
  }
  @media screen and (max-width: 880px) and (min-width: 570px) {
    #cf_scarcity_jeet_shortCode.shortCode {
      height: 200px;
    }
  }
</style>