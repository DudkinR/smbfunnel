"use strict";
$(document).ready(function(){

    $("#cf-adds-checkpas").on("change", function(){
        if($(this).is(":checked")){
          $(".cf-adds-password").collapse('show');
        }else{
          $(".cf-adds-password").collapse('hide');
        }
    });
    $(document).on('click','.cfadds-delete-inp', function(){
        $(this).parents(".input-group").remove();
    });
    $("#cfadds-add-new").on("click", function(){
      let ht=`<div class="input-group mb-3">
      <input type="text"  name="input_name[]" class="form-control" placeholder="Enter Input Name">
      <input type="text"  name="input_value[]" class="form-control" placeholder="Enter Input value">
      <div class="input-group-prepend">
        <button type="button" class="btn btn-danger cfadds-delete-inp" >Delete</button>
      </div>
    </div>`;
      $("#cfadds-inputs").append(ht);
    });
    // popup settings
  $("#cfaddStudentManually").on("submit", function(eve){
    eve.preventDefault();
    let btn=eve.target;
    btn.disabled=true;
    $("#addstudent_btn").html('Saving.. <span class=" spinner-border spinner-border-sm"></span></button>');
    var postData= "action=cfaddstudentm_admin_ajax&"+$("#cfaddStudentManually").serialize();      
    $.post( $("#cfaddstudent_ajax").val() , postData, function( response ){
      btn.disabled=false;
      var response  = $.parseJSON(response);
      if(response.status==1){
        $("#cf-studnet-errr").removeClass('alert-danger').addClass('alert-success').show();
        $(".cfstudent-err").text(response.message);
        $("#addstudent_btn").html("Save");    
          setTimeout(function(){
             window.location.href=`index.php?page=cfbulk_members_add&funnel_id=${response.funnel_id}&cfbulk_members_id=${response.student_id}`;
          }, 500);  
      }else if(response.status==0){
        $("#addstudent_btn").html("Save");
        $("#cf-studnet-errr").removeClass('alert-success').addClass('alert-danger').show();
        $(".cfstudent-err").text(response.message);
      }
    });
  });

    $("#keywordsearchresult").on("click" ,".cfadd_studnet_delete",function(eve){
      var conf = confirm( "Are you sure!" );
      if(conf){
        var deleteId=$(this).attr("data-id");;
        var postData= "action=cfdeletestudentm_ajax&id="+deleteId;
        
        $.post( $("#cfaddstudent_ajax").val() , postData, function( response ){
            var response = JSON.parse(response);
            if(response.status==1){
              setTimeout(function(){
                location.reload()
               }, 100);
            
            }else{
                alert("Error: User Not Deleted successfully! ") 
            }
        });
      }
    });
    $("#keywordsearchresult").on("change",".cfadd_student_cancel" ,function( eve ){

        var cancelId=$(this).attr("data-id");
        var update=$(this).val();
        var postData= `action=cfcancelstudentm_ajax&id=${cancelId}&update=${update}`;
        
        $.post( $("#cfaddstudent_ajax").val() , postData, function( response ){
            var response = JSON.parse(response);
            if(response.status==1){
              setTimeout(function(){
                location.reload()
               }, 100);
            
            }else{
                alert("Error:Something went wrong! Please try again ") 
            }
        });
    });
    let cfstudent_importer = $("#cf-software-type").val();
    $(".select_courses").select2();
    $(".select_courses").select2({
      placeholder: "Select Course",
      allowClear: true,
    });
    if( cfstudent_importer=='coursefunnels' )
    {
     
      var cfstudent_importer_url = $("#cfaddstudent_ajax").val();
      let cfstudent_importer_id = $("#cfstudent_id").val();
      try{
        $.ajax({
          url: cfstudent_importer_url,
          type: "POST",
          data: {
            action: "cfaddstudent_courses",
            id: cfstudent_importer_id,
          },
          success: function (data) {
           try {
            data = JSON.parse(data);
            var selectedValues = [];
            let i = 0;
            data.forEach((element) => {
              selectedValues[i] = element["product"];
              i++;
            });
            $(".select_courses").val(selectedValues).change();
           } catch (error) {
            console.log(error);
           }
          },
          error: function (error) {
            console.log(error);
          },
        });
       
      }
      catch(err)
      {
        console.log(err);
      }
    }
    $("#select_courses_import").select2();
    $("#select_courses_import").select2({
      placeholder: "Select Course",
      allowClear: true,
    });
});

