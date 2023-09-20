$(document).ready(function () {
  $("#loading-image")
  .bind("ajaxStart", function () {
    $(this).show();
  })
  .bind("ajaxStop", function () {
    $(this).hide();
  });
  $(document).on("click", ".send_mail", function () {
    var url = $("#cfshipping_ajax").val();
    var emailer_id = $("#emailer_id").val();
    var sender = $("#sender").val();
    var subject = $("#email_subject").val();
    tinymce.triggerSave();
    var content = $("#email_content").val();
    $('#loading-image').show();
    $(".send_mail").attr("disabled", true);
    $.ajax({
      url: url,
      type: "POST",
      data: {
        action: "send_email",
        emailer_id: emailer_id,
        sender: sender,
        subject: subject,
        content: content,
      },
      success: function (data) {
        if (data == "200") {   
          history.back();
        } else {
          alert("Something Wrong, Please Try Again");
          history.back();
        }
      },
      error: function (error) {
        console.log(error);
      },
      complete: function(){
        $('#loading-image').hide();
        $(":send_mail").removeAttr("disabled");
      }
    });
  });

});

function edit_option(val) {
  console.log(val);
}
function delete_option(val) {
  let id = val;
  var reload_url = $("#get_option_url").val();
  var url = $("#cfshipping_ajax").val();

  Swal.fire({
    title: t("Are you sure?"),
    text: t("You won't be able to revert this!"),
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#d33",
    cancelButtonColor: "#3085d6",
    confirmButtonText: t("Yes, delete it!"),
  }).then((result) => {
    if (result.isConfirmed) {
      $.ajax({
        url: url,
        type: "POST",
        data: {
          action: "shipping_option_delete",
          id: id,
        },
        success: function (data) {
          if (data == "200") {
            location.reload();
          } else {
            alert("Something Wrong, Please Try Again");
            location.reload();
          }
        },
        error: function (error) {
          console.log(error);
        },
      });
    }
  });
}
