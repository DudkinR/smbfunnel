$(document).ready(function () {
  $("form").submit(function (e) {    
    
    url = $("#sf_data_install_url").val() + "/index.php?page=ajax"; 
    var delivery_cost = $("input[name=delivery_type]:checked").attr("cost");
    var delivery_type = $("input[name=delivery_type]:checked").attr("delivery");
   
    $.ajax({
      url: url,
      type: "POST",
      data: {
        action: "selected_method",
        delivery_cost: delivery_cost,
        delivery_type: delivery_type
      
      },
      success: function (data) {
        // data = JSON.parse(data);
        // console.log(data);
        // e.preventDefault(e);
      },
      error: function (error) {
        console.log(error);
      },
    });
  });

});
