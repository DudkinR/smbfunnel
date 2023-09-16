function cfquizoDoLoadUserSideScript(fid, delay_time, exit_popup, force_show=false)
{
    let popupFormModal = document.getElementById(`cfquizo-modal_${fid}`);
    let close_btn = document.getElementById(`cfquizo-modal-close-${fid}`);

    if(force_show)
    {
      delay_time=0;
      exit_popup=false;
      let bdy=popupFormModal.querySelectorAll('.cfquizo-modal-body')[0];
      bdy.scrollTop = bdy.scrollHeight;
    }
  
    close_btn.onclick = function() {
      popupFormModal.style.display = "none";
    }
  
  /*  Uncomment if you wnt click outside window should close form
  window.onclick = function(event) {
      if (event.target == popupFormModal) {
        popupFormModal.style.display = "none";
      }
    };  */

    let exit_popup_displayed=false;
    if(!exit_popup)
    {
        window.addEventListener("load",function(){
            setTimeout(function(){
                popupFormModal.style.display = "block";
            },(delay_time*1000));
        });
    }
    else if(!exit_popup_displayed)
    {
        document.onmouseleave=function(){
            if(!exit_popup_displayed)
          {
            exit_popup_displayed=true;
            popupFormModal.style.display= "block";
          }
        };
    }
}

$(".usersidechecking").on("submit" ,function(eve){
     $("#show_questions").show();
        var postData="action=myPopupUserSideQuizAjax";
        console.log($("#cfquiz_ajax").val());
       $.post( $("#cfquiz_ajax").val() , postData, function( response ){
          console.log("response is"+response);
            alert("testing hit the userside"+response);
        }); 
  });




