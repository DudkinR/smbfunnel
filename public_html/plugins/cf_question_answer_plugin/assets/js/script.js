$(document).ready(function () {

  // alert("hello");
  var showChar = 150;
	var ellipsestext = "...";
	var moretext = "Read More";
	var lesstext = "Read Less";
	$('.more').each(function() {
		var content = $(this).html();

		if(content.length > showChar) {

			var c = content.substr(0, showChar);
			var h = content.substr(showChar-1, content.length - showChar);

			var html = c + '<span class="moreellipses">' + ellipsestext+ '&nbsp;</span><span class="morecontent"><span>' + h + '</span>&nbsp;&nbsp;<a href="" class="morelink">' + moretext + '</a></span>';

			$(this).html(html);
		}

	});

	$(".morelink").click(function(){
		if($(this).hasClass("less")) {
			$(this).removeClass("less");
			$(this).html(moretext);
		} else {
			$(this).addClass("less");
			$(this).html(lesstext);
		}
		$(this).parent().prev().toggle();
		$(this).prev().toggle();
		return false;
	});

  $(".reply_btn").click(function (e) {
    e.preventDefault();
    var form_id = $(this).attr("id");
    console.log(form_id);
    $.ajax({
      type: "POST",
      url: $("#cf_answer_ajax").val(),
      data: {
        checking_answer_btn: true,
        f_id: form_id,
        action: "cf_preview_answers",
      },
      success: function (response) {
         console.log(response);
        $.each(response, function (key, value) {
          $("#answer_id").val(value["id"]);
        
        });
        $("#answer_form_Modal").modal("show");
      },
    });
  });

  $("#cf_answer_form").on("submit", function (event) {
    event.preventDefault();
    $.ajax({
      url: $("#cf_answer_ajax").val(),
      method: "POST",
      data: "action=cf_insert_answer&" + $("#cf_answer_form").serialize(),
      beforeSend: function () {
        $("#update_answer").val("Updating");
      },
      success: function (data) {
        $("#cf_answer_form")[0].reset();
        $("#answer_form_Modal").modal("hide");
        location.reload();

      },
    });
  });


  // update form
  $(".edit_btn").click(function (e) {
    e.preventDefault();
    var form_id = $(this).attr("id");
    //console.log(form_id);
    $.ajax({
      type: "POST",
      url: $("#cf_answer_ajax").val(),
      data: {
        checking_answer_btn: true,
        f_id: form_id,
        action: "cf_edit_preview_answer",
      },
      success: function (response) {
         console.log(response);
        $.each(response, function (key, value) {
          $("#edit_id").val(value["id"]);
          $("#edit_answer").val(value["answer"]);
                 
        });
        $("#edit_form_Modal").modal("show");
      },
    });
  });


  //update form
  $("#cf_update_answer_form").on("submit", function (event) {
    event.preventDefault();
    $.ajax({
      url: $("#cf_answer_ajax").val(),
      method: "POST",
      data: "action=cf_edit__answer&" + $("#cf_update_answer_form").serialize(),
      beforeSend: function () {
        $("#update_answer").val("Updating");
      },
      success: function (data) {
        $("#cf_update_answer_form")[0].reset();
        $("#edit_form_Modal").modal("hide");
        location.reload();

      },
    });
  });


 
 
  $(".delete_btn").click(function (e) {
    e.preventDefault();
     var delete_id = $(this).attr("id");
    //console.log(delete_id);
    $("#deleteModal").modal("show");
    $("#delete_recordes").click(function (e) {
      $.ajax({
        type: "POST",
        url: $("#cf_answer_ajax").val(),
        data: {
          del_id: delete_id,
          action: "cf_delete_question",
        },
        success: function (data) {
          $("#delete-message").html(data);
          $("#deleteModal").modal("hide");
          location.reload();
        },
      });
    });
  });

  $("#cf_style_form").on("submit", function (e) {
    e.preventDefault();
   
    $.ajax({
      url: $("#cf_ajax_style").val(),
      method: "POST",
      data: "action=cf_insert_style_ajax&" + $("#cf_style_form").serialize(),
      beforeSend: function () {
        $("#cf_style_save").val("save");
      },
      success: function (data) {
        swal("save");
             
      
      },
    });
  });
  $("#cfpro-rev-filter-btn").on("click", function() {
    $("#cfpro-rev-open-filter").toggle(100);
  });
  //Filter with producta
  $("#cfpro-rev-selec-product").change(function() {
    let _thisValue = $(this).val();
    _thisValue = _thisValue.trim();
    const url = new URL(window.location);
    if (_thisValue != "all") {

      url.searchParams.set('cfprorev_product_id', _thisValue.trim());

    } else {

      url.searchParams.delete('cfprorev_product_id');
  
      
    }
    window.history.pushState({}, '', url);
    window.location = location.href;
  });

  
  
  // Check all record
  $("#cfpro-rev-checkforall").click(function() {
    $('.cfpro-rev-bulk-check').not(this).prop('checked', this.checked);
  });
  $('.cfpro-rev-bulk-check').click(function() {
    $('#cfpro-rev-checkforall').prop('checked', false);
  });

  //clear all filter
  $("#cfpro-rev-clear").on("click", function() {
    const url = new URL(window.location);
   ;
    url.searchParams.delete('cfprorev_product_id');
   
    url.searchParams.delete('fromdate');
    url.searchParams.delete('arrange_records_order');
    url.searchParams.delete('fromdays');
    url.searchParams.delete('todate');
    window.history.pushState({}, '', url);
    window.location = location.href;
  });


});
function cfmenuCopyText(textVal) {
  navigator.clipboard.writeText(textVal);
  setSwalAnimation(t("Copied successfully"), 'success');
}