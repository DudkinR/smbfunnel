$(document).ready(function () {
  var applyto = $("#cf_scarcity_jeet_apply_to").val();
  if (applyto == "specificpage") {
    $("#specificpage_div").show();
    $("#shortcodeOnly_div").hide();
    $("#specificfunnel_div").hide();
  }
  if (applyto == "shortcodeOnly") {
    $("#specificpage_div").hide();
    $("#shortcodeOnly_div").show();
    $("#specificfunnel_div").hide();
  }
  if (applyto == "funnels") {
    $("#specificfunnel_div").show();
    $("#specificpage_div").hide();
    $("#shortcodeOnly_div").hide();
  }
  $("#cf_scarcity_jeet_apply_to").on("change", function (e) {
    var applyto = $("#cf_scarcity_jeet_apply_to").val();
    if (applyto == "specificpage") {
      $("#specificpage_div").show();
      $("#shortcodeOnly_div").hide();
      $("#specificfunnel_div").hide();
    }
    if (applyto == "shortcodeOnly") {
      $("#specificpage_div").hide();
      $("#shortcodeOnly_div").show();
      $("#specificfunnel_div").hide();
    }
    if (applyto == "funnels") {
      $("#specificfunnel_div").show();
      $("#specificpage_div").hide();
      $("#shortcodeOnly_div").hide();
    }
  });  
});
function myFunction() {
  var checkBox = document.getElementById("cf_scarcity_jeet_show_action_button");
  var text = document.getElementById("text");
  if (checkBox.checked == true) {
    text.style.display = "block";
  } else {
    text.style.display = "none";
  }
}
function showFunction() {
  var checkBox = document.getElementById("cf_scarcity_jeet_ProductBoxShow");
  var text = document.getElementById("sImage");
  if (checkBox.checked == true) {
    text.style.display = "block";
  } else {
    text.style.display = "none";
  }
}
function cfscarcity_Geturl(selector, html) {
  try {
    //here calling open media
    openMedia(function (content) {
      try {
        document.querySelectorAll(selector)[0].value = content;
      } catch (err) {
        console.log(err);
      }
    }, html);
  } catch (err) {
    console.log(err);
  }
}
