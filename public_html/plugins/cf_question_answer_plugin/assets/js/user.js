$(document).ready(function () {
  $(document).on('click', '#cfq_loadBtn', function (e) {
    e.preventDefault();
    let product_id = $('#product_id').val();
    let row = parseInt($('#postCount').val());
    var limit  =  0;
    var count = 5;
    limit = row + count;
    $("#postCount").val(limit);
    $.ajax({
      type: 'POST',
      url:   $("#cf_ques_ajax").val(),  
      data: "action=cf_question_loadmore&row="+limit+"&product_id="+product_id,
      success: function (data) {
        if(data)
        {
          $('#cfq_qandadiv_c').append(data);
        }else{
          $("#cfq_loadBtn").addClass("text-info").text("No more questions available")
        }
      }
    });
  });

  $("#cf_ques_form").on("submit", function (e) {
    e.preventDefault();
    $("#add_data_Modal").modal("hide");
    $.ajax({
      url: $("#cf_ques_ajax").val(),
      method: "POST",
      data: "action=cf_questions_ajax&" + $("#cf_ques_form").serialize(),
      beforeSend: function () {
        $("#cf_save_ques").val();
      },
      success: function (data) {
        $("#cf_ques_form")[0].reset();
        $("#add_data_Modal").modal("hide");
        swal({
          title: "Thank you!",
          text: "You will receive the answer from the admin! You can check here answer",
          icon: "success",
          button: "Okay!",
        });
      },
    });
  });
});