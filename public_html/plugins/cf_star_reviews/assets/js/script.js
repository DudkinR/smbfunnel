// Use jQuery via $(...)
"use strict";
$(document).ready(function(){
  
  let cfpro_review_jaxUrl =  $("#cfpro_review_jaxUrl").val();
  /*
    ********** Filters ******************
  */
      //Filter open button
      $("#cfpro-rev-filter-btn").on("click", function(){
        $("#cfpro-rev-open-filter").toggle(100);
      });
      //Filter with producta
      $("#cfpro-rev-selec-product").change(function()
      {
        let _thisValue = $(this).val();
        _thisValue = _thisValue.trim();
        const url = new URL(window.location);
        if(_thisValue != "all")
        {
          url.searchParams.set('cfprorev_product_id',  _thisValue.trim() );
          
        }else{
          url.searchParams.delete('cfprorev_product_id' );

        }
        window.history.pushState({}, '', url);
        window.location = location.href;
      });
      //Min and max rating filter 
      $("#cfpro-rev-enter-rating").click(function()
      {
        let min = $("#cfpro-rev-min-rating").val().trim();
        let max = $("#cfpro-rev-max-rating").val().trim();
        const url = new URL(window.location);
        if(min != "")
        {
          url.searchParams.set('cfprorev_min_rating',  min );
          
        }else{
          url.searchParams.delete('cfprorev_min_rating' );
      
        }
        if(max != "")
        {
          url.searchParams.set('cfprorev_max_rating',  max );
          
        }else{
          url.searchParams.delete('cfprorev_max_rating' );
      
        }
        window.history.pushState({}, '', url);
        window.location = location.href;
      });
      // Read and unread filter
      $("#cfpro-rev-filter-reivews").change(function()
      {
        let _thisValue = $(this).val();
        _thisValue = _thisValue.trim();
        const url = new URL(window.location);
        if(_thisValue != "all")
        {
          url.searchParams.set('cfprorev_filter_review', _thisValue );
        }else{
          url.searchParams.delete('cfprorev_filter_review' );
        }
        window.history.pushState({}, '', url);
        window.location = location.href;
      });
      // Filter with rating
      $("#cfpro-rev-select-rating").change(function()
      {
        let _thisValue = $(this).val();
        _thisValue = _thisValue.trim();
      
        const url = new URL(window.location);
        if(_thisValue != "all" && _thisValue != "-1")
        {
          url.searchParams.set('cfprorev_rating',  _thisValue.trim() );
          
        }else{
          url.searchParams.delete('cfprorev_rating' );
      
        }
        window.history.pushState({}, '', url);
        window.location = location.href;
      });
      // Check all record
      $("#cfpro-rev-checkforall").click(function () {
        $('.cfpro-rev-bulk-check').not(this).prop('checked', this.checked);
      });
      $('.cfpro-rev-bulk-check').click(function(){  
        $('#cfpro-rev-checkforall').prop('checked', false );
      });
      //Filter with summary
      $("#cfpro-rev-filter-summary").change(function()
      {
        let _thisValue = $(this).val();
        _thisValue = _thisValue.trim();
        const url = new URL(window.location);
        if(_thisValue != "all")
        {
          url.searchParams.set('cfprorev_summary', _thisValue );
        }else{
          url.searchParams.delete('cfprorev_summary' );
        }
        window.history.pushState({}, '', url);
        window.location = location.href;
      });
      //clear all filter
      $("#cfpro-rev-clear").on("click", function(){
        const url = new URL(window.location);
        url.searchParams.delete('cfprorev_min_rating' );
        url.searchParams.delete('cfprorev_summary' );
        url.searchParams.delete('cfprorev_filter_review' );
        url.searchParams.delete('cfprorev_product_id' );
        url.searchParams.delete('cfprorev_max_rating' );
        url.searchParams.delete('cfprorev_rating' );
        url.searchParams.delete('fromdate' );
        url.searchParams.delete('arrange_records_order' );
        url.searchParams.delete('fromdays' );
        url.searchParams.delete('todate' );
        window.history.pushState({}, '', url);
        window.location = location.href;
      });

  /*
    ****************Filters end*************
  */ 

  // ****************Action for review*******************
  $("#cfpro-rev-bulk-review-btn").click(function(){
      let sbtn = $("#cfpro-rev-bulk-review-btn");
      let bulkAction,i,len,allBulkval="",checklen=0;
      let bulkval = $("#cfpro-rev-bulk-review-action").val();
      let checkbulk = $(".cfpro-rev-bulk-check");//get all chackbox value
      bulkval = bulkval.trim();
      let checkforall =  $("#cfpro-rev-checkforall"); // get only one above checkbox value
      if( bulkval =="del" || bulkval =="ap" || bulkval =="uap" || bulkval =="re" || bulkval=="unre")
      {
        if( $( checkforall ).is(":checked") )
        {
            bulkAction="all";
        }else{
          bulkAction="custom";
        }
        len = checkbulk.length;
          for( i=0; i < len;i++ )
          {
            if( $( checkbulk[i] ).is(":checked") )
            { 
              checklen++
              allBulkval +=$(checkbulk[i]).val()+",";
            }
          }
          if( checklen==0 )
          {
            $(".cfpro_rev_drip_model-del-body").text(t("Please Check one review"));
            $(".cfpro-rev-remove-review").hide();
            $("#cfpro_rev_drip_delete_assign").show();

          }else{
            if( bulkval == "del" )
            {
              $("#cfpro-rev-review-bulkaction").val(bulkAction);
              $("#cfpro-rev-review-bulkval").val(bulkval);
              $("#cfpro-rev-delete-review").val(allBulkval);
              if( checklen ==1 )
              {
                $(".cfpro_rev_drip_model-del-body").text(t("Do you really want to delete this review?"));
                $(".cfpro-rev-remove-review").show();
              }
              else if(checklen==0)
              {
                $(".cfpro_rev_drip_model-del-body").text(t("Please Check one review"));
                $(".cfpro-rev-remove-review").hide();
              }
              else{
                $(".cfpro_rev_drip_model-del-body").text(t("Do you really want to delete the review?"));
                $(".cfpro-rev-remove-review").show();
              }
              $("#cfpro_rev_drip_delete_assign").show();
            }
            else{
              var postData= "action=cfproreviews_reviews&bulk=all_data&bulkval="+bulkval+"&bulkac="+bulkAction+"&data[]="+allBulkval;
              $(sbtn).html(t('Applying')+'.. <span class=" spinner-border spinner-border-sm"></span>'); 
              $.post( $("#cfpro_rev_ajax").val() , postData, function( response ){
                let res = $.parseJSON(response);
                if(res.status==1)
                { $(sbtn).html(t('Apply')); 
                  window.location=location.href;
                }
              });
            }
          }
      }else{
        $(".cfpro-rev-bulk-show").css("opacity",1);
        setTimeout(() => {
          $(".cfpro-rev-bulk-show").css("opacity",0);
        }, 1500);
      }
  });

  // ****************Delete  review*******************
  $(document).on("click",".cfpro-rev-delete-rev",function(eve){
    eve.preventDefault();
    let id = $(this).data("id");
    $("#cfpro-rev-review-bulkval").val("one");
    $("#cfpro-rev-delete-review").val(id);
    $(".cfpro_rev_drip_model-del-body").text(t("Do you really want to delete this review?"));
    $(".cfpro-rev-remove-review").show();
    $("#cfpro_rev_drip_delete_assign").show();
  });

  // ****************Read review*******************
  $(document).on("click",".cfpro-rev-read-review",function(eve){
    eve.preventDefault();
    let targ=$(this);
    let id = $(targ).attr("data-id");
    let read = $(targ).attr("data-readed");
    let tarText = $(targ).text();
    let Text  = tarText.toLowerCase();
    var postData= "action=cfproreviews_reviews&bulk=read&id="+id+"&read="+read;  
    $.post( $("#cfpro_rev_ajax").val() , postData, function( response ){
      let par =  $(targ).parents(".cfpro-rev-hover-review");
      let child = $(par).find(".cfpro-rev-read-check");
        let res = $.parseJSON(response);
        if(res.status==1)
        {
          if(Text==t("mark as unread"))
          {
            $(child).css("opacity","0");
            $(targ).text(t("Mark as read"));
            $(targ).attr("data-readed",0);
          }else{
            $(child).css("opacity","1");
            $(targ).text(t("Mark as unread"));
            $(targ).attr("data-readed",1);
          }
        }
    });
  });

  /**************** Approved Reviews *********************/ 
  $(document).on("click",".cfprov-rev-appr-review",function(eve){
    eve.preventDefault();
    let targ=$(this);
    let id = $(targ).data("id");
    let appr = $(targ).data("appr");
    let tarText = $(targ).text();
    let Text  = tarText.toLowerCase();
    var postData= "action=cfproreviews_reviews&bulk=appr&id="+id+"&appr="+appr;
    $.post( $("#cfpro_rev_ajax").val() , postData, function( response ){
        let res = $.parseJSON(response);
        let par =  $(targ).parents(".cfpro-rev-hover-review");
        if( res.status == 1 )
        {
          if( Text == "unapproved" )
          {
            $(par).addClass("cfpro-rev-approved");
            $(targ).text(t("approved"))
          }
          else{
            $(par).removeClass("cfpro-rev-approved");
            $(targ).text(t("unapproved"));
          }
        }
    });
  });

  // ************* Delete Reviews **************/
   $(".cfpro-rev-remove-review").click(function(eve){ 
    eve.preventDefault();
    let btn = $(".cfpro-rev-remove-review");
    let id =  $("#cfpro-rev-delete-review").val();
    let bulkAction = $("#cfpro-rev-review-bulkaction").val();
    let bulkval = $("#cfpro-rev-review-bulkval").val();
    if( bulkAction=="all" || bulkAction=="custom")
    {
      var postData= "action=cfproreviews_reviews&bulk=all_data&bulkval="+bulkval+"&bulkac="+bulkAction+"&data[]="+id;

    }else{
      var postData= "action=cfproreviews_reviews&bulk=delete_reivew&id="+id;  
    }
    $(btn).html(t('Deleting')+'.. <span class=" spinner-border spinner-border-sm"></span></button>'); 
    $.post( $("#cfpro_rev_ajax").val() , postData, function( response ){
      let res = $.parseJSON(response);
      if(res.status==1)
      {
        if( bulkAction=="all" || bulkAction=="custom")
        {
          window.location=location.href;

        }else{
          $(btn).prop('disabled', false);
          $(btn).html(t("Delete"));
          $("#cfpro-rev-delete-review").val("");
          $("#cfpro_rev_drip_delete_assign").hide();//this is for also assignment
          $("#cfpro-rev-review-row-"+id).remove();
          $("#cfpro-rev-snackbar-admin").addClass("cfpro-rev-snackbar-show");
          setTimeout(function(){ $("#cfpro-rev-snackbar-admin").removeClass("cfpro-rev-snackbar-show") }, 3000);
        }
      }
    });
  });

  //*********** Close Model *********** */
  $(".cfpro-rev-close-drip-btn").click(function(){ 
    $("#cfpro-rev-review-bulkval").val("");
    $("#cfpro-rev-delete-review").val("");
    $(".cfpro-rev-remove-review").hide();
    $("#cfpro_rev_drip_delete_assign").hide();
  });

  // **************** Read More *********************
  $(document).on('click','.cfpro-rev-read-more-review', function(eve){
    let targ = $(this);
    let parEl = $(targ).parents(".cfpro-rev-comment-text");//this is for assignment// here comment class only for code saving
    let hideEl = $(parEl).find(".cfpro-rev-comment-dot");
    let showEl = $(parEl).find(".cfpro-rev-comment-second-text");
    let showText = $(showEl).html();
    let text = $(targ).text().toLowerCase();
    if(text==t("read more"))
    {
      $(hideEl).html(showText);
      $(targ).text(t("Read less"))
    }
    else if(text==t("read less"))
    {
      $(hideEl).html("...");
      $(targ).text(t("Read more"))
    }
  });

  // Settings tabs
  // Change tab class and display content
  $('.cfpro-rev_tabs-nav a').on('click', function(event){
    event.preventDefault();
    $('.cfpro-rev_tabs-nav li').removeClass('cfpro-rev_tab-active');
    $(this).parent().addClass('cfpro-rev_tab-active');
    $('.cfpro-rev_tabs-stage div.cfpro-rev_tabs_navvar').hide();
    $($(this).attr('href')).show();
  });

  //**********  Setting form  *************** */
  $(".cfpro-rev-setting-form").on("submit", function(event){
    event.preventDefault();    
    let response;
    let btn =$(this).find(".cfpro-rev-save-setting");
    $(btn).html(`<i class="fa fa-spinner fa-spin" ></i> ${t('Saving')}`);
    $.ajax({
        method:"post",
        url:cfpro_review_jaxUrl,
        data:$(this).serialize(),
        success:function(resp){  
            response=JSON.parse(resp);
            $(btn).html(`Save changes`);
            if(response.status == 1)
            {
                $("#cfpro-rev-snackbar-admin").text(response.message).addClass('cfpro-rev-snackbar-show');
                setTimeout(function(){ $("#cfpro-rev-snackbar-admin").text('').removeClass("cfpro-rev-snackbar-show"); }, 1000);
            }else{
                $("#cfpro-rev-snackbar-admin").text(response.message).addClass("cfpro-rev-snackbar-show text-danger");
                setTimeout(function(){ $("#cfpro-rev-snackbar-admin").text('').removeClass("cfpro-rev-snackbar-show text-danger");  }, 3000);
            }
        },
        error:function(err){
            console.log(err)
        }
    });
  });
  /***** Reset Settings ********/ 
  $(".cfpro-rev-reset-setting").on("click", function(){
      
    let btn=$(this);
    if( confirm(t('Are you sure? Your all changes will be undo.')) )
    {
      $(btn).html(`<i class="fa fa-spinner fa-spin" ></i> ${t('Resetting')}`);
      let postData = "action=cfproreviews_reset_setting";
      $.ajax({
        method:"post",
        url:cfpro_review_jaxUrl,
        data:postData,
        success:function(resp){ 
          window.location=location.href;
        },
        error:function(err){
            console.log(err)
        }
    });
    }
  });

  /********* show and hide retrict file extension box ***********/ 
  $("#cfpro-rev-rfext").on("change", function(){
    if( $( this ).is(":checked") )
    {
      $(".cfpro-rev-rfext-open").show(100);
    }else{
      $(".cfpro-rev-rfext-open").hide(100);
    }
  });
    /********* show and hide allow file option ***********/ 
  $("#cfpro-rev-allow-f").on("change", function(){
    if( $( this ).is(":checked") )
    {
      $(".cfpro-rev-allow-fcont").show(100);
    }else{
      $(".cfpro-rev-allow-fcont").hide(100);
    }
  });
});

let slideIndex = 1;

function plusSlides(n,id) {
  showSlides(slideIndex += n,id);
}


function showSlides(n,id) {
  let i;
  let slidesConts = document.getElementById(`cfpro-rev-image-model-${id}`);
  if(slidesConts != null )
  {
    let slides = slidesConts.getElementsByClassName("mySlides");
  
    let dots = document.getElementsByClassName("dot");
    if (n > slides.length) {slideIndex = 1}    
    if (n < 1) {slideIndex = slides.length}
    for (i = 0; i < slides.length; i++) {
      slides[i].style.display = "none";  
    }
    for (i = 0; i < dots.length; i++) {
      dots[i].className = dots[i].className.replace(" active", "");
    }
    slides[slideIndex-1].style.display = "block";  
  }
}